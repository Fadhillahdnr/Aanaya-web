#!/usr/bin/env bash
#
# Laravel Quality Check
#
# Safe, read-only-oriented quality verification for Laravel repositories.
# It does NOT install dependencies, run migrations, modify source files,
# create commits, or push.
#
# Usage:
#   bash path/to/laravel-quality-check.sh quick
#   bash path/to/laravel-quality-check.sh full
#
# Optional environment variables:
#   LARAVEL_RUNNER=auto|host|docker   Default: auto
#   LARAVEL_DOCKER_SERVICE=app        Default: app
#   LARAVEL_COMPOSE_FILE=compose.yaml Optional
#   LARAVEL_SKIP_FRONTEND=1           Skip frontend checks
#   LARAVEL_SKIP_AUDIT=1              Skip Composer audit
#   LARAVEL_SKIP_STACK=1              Skip Aanaya stack contract checks
#   LARAVEL_TEST_ARGS="--filter=..."  Extra args for php artisan test
#
# Exit codes:
#   0 = all executed required checks passed
#   1 = one or more checks failed
#   2 = invalid usage or not a Laravel repository

set -u
set -o pipefail

MODE="${1:-quick}"
RUNNER="${LARAVEL_RUNNER:-auto}"
DOCKER_SERVICE="${LARAVEL_DOCKER_SERVICE:-app}"
COMPOSE_FILE="${LARAVEL_COMPOSE_FILE:-}"
SKIP_FRONTEND="${LARAVEL_SKIP_FRONTEND:-0}"
SKIP_AUDIT="${LARAVEL_SKIP_AUDIT:-0}"
SKIP_STACK="${LARAVEL_SKIP_STACK:-0}"
TEST_ARGS="${LARAVEL_TEST_ARGS:-}"

PASS_COUNT=0
FAIL_COUNT=0
SKIP_COUNT=0
WARN_COUNT=0

if [[ "$MODE" != "quick" && "$MODE" != "full" ]]; then
  echo "Usage: $0 [quick|full]"
  exit 2
fi

find_project_root() {
  local current="$PWD"

  while [[ "$current" != "/" ]]; do
    if [[ -f "$current/artisan" && -f "$current/composer.json" ]]; then
      printf '%s\n' "$current"
      return 0
    fi
    current="$(dirname "$current")"
  done

  return 1
}

PROJECT_ROOT="$(find_project_root)" || {
  echo "ERROR: Laravel project root not found. Run this tool inside a repository containing artisan and composer.json."
  exit 2
}

cd "$PROJECT_ROOT" || exit 2

header() {
  printf '\n============================================================\n'
  printf '%s\n' "$1"
  printf '============================================================\n'
}

pass() {
  PASS_COUNT=$((PASS_COUNT + 1))
  printf '[PASS] %s\n' "$1"
}

fail() {
  FAIL_COUNT=$((FAIL_COUNT + 1))
  printf '[FAIL] %s\n' "$1"
}

skip() {
  SKIP_COUNT=$((SKIP_COUNT + 1))
  printf '[SKIP] %s\n' "$1"
}

warn() {
  WARN_COUNT=$((WARN_COUNT + 1))
  printf '[WARN] %s\n' "$1"
}

run_check() {
  local label="$1"
  shift

  printf '\n[RUN ] %s\n' "$label"
  printf '       Command:'
  printf ' %q' "$@"
  printf '\n'

  if "$@"; then
    pass "$label"
    return 0
  else
    local status=$?
    fail "$label (exit $status)"
    return "$status"
  fi
}

compose_exec() {
  if [[ -n "$COMPOSE_FILE" ]]; then
    docker compose -f "$COMPOSE_FILE" "$@"
  else
    docker compose "$@"
  fi
}

docker_available() {
  command -v docker >/dev/null 2>&1 || return 1
  docker compose version >/dev/null 2>&1 || return 1
  docker info >/dev/null 2>&1 || return 1
  compose_exec config --services 2>/dev/null | grep -Fxq "$DOCKER_SERVICE" || return 1
  compose_exec ps --status running --services 2>/dev/null | grep -Fxq "$DOCKER_SERVICE"
}

select_runner() {
  case "$RUNNER" in
    host)
      printf 'host\n'
      ;;
    docker)
      if docker_available; then
        printf 'docker\n'
      else
        echo "ERROR: Docker runner requested, but Docker Compose or service '$DOCKER_SERVICE' is unavailable." >&2
        return 1
      fi
      ;;
    auto)
      if docker_available; then
        printf 'docker\n'
      elif command -v php >/dev/null 2>&1 && command -v composer >/dev/null 2>&1; then
        printf 'host\n'
      else
        echo "ERROR: Neither host PHP/Composer nor a usable Docker service '$DOCKER_SERVICE' is available." >&2
        return 1
      fi
      ;;
    *)
      echo "ERROR: LARAVEL_RUNNER must be auto, host, or docker." >&2
      return 1
      ;;
  esac
}

ACTIVE_RUNNER="$(select_runner)" || exit 2

project_exec() {
  if [[ "$ACTIVE_RUNNER" == "host" ]]; then
    "$@"
    return
  fi

  compose_exec exec -T "$DOCKER_SERVICE" "$@"
}

project_test() {
  if [[ "$ACTIVE_RUNNER" == "host" ]]; then
    APP_ENV=testing \
      DB_CONNECTION=sqlite \
      DB_DATABASE=:memory: \
      DB_URL= \
      CACHE_STORE=array \
      SESSION_DRIVER=array \
      QUEUE_CONNECTION=sync \
      "$@"
    return
  fi

  compose_exec exec -T \
    -e APP_ENV=testing \
    -e DB_CONNECTION=sqlite \
    -e DB_DATABASE=:memory: \
    -e DB_URL= \
    -e CACHE_STORE=array \
    -e SESSION_DRIVER=array \
    -e QUEUE_CONNECTION=sync \
    "$DOCKER_SERVICE" "$@"
}

project_has_executable() {
  local executable="$1"

  if [[ "$ACTIVE_RUNNER" == "host" ]]; then
    [[ -x "$executable" ]]
  else
    project_exec test -x "$executable" >/dev/null 2>&1
  fi
}

project_file_exists() {
  local file="$1"

  if [[ "$ACTIVE_RUNNER" == "host" ]]; then
    [[ -f "$file" ]]
  else
    project_exec test -f "$file" >/dev/null 2>&1
  fi
}

json_has_script() {
  local script_name="$1"

  if ! command -v python3 >/dev/null 2>&1; then
    return 1
  fi

  python3 - "$script_name" <<'PY'
import json
import pathlib
import sys

path = pathlib.Path("package.json")
if not path.exists():
    raise SystemExit(1)

try:
    data = json.loads(path.read_text(encoding="utf-8"))
except Exception:
    raise SystemExit(1)

script = sys.argv[1]
raise SystemExit(0 if script in data.get("scripts", {}) else 1)
PY
}

changed_php_files() {
  {
    git diff --name-only --diff-filter=ACMR
    git diff --cached --name-only --diff-filter=ACMR
    git ls-files --others --exclude-standard -- '*.php'

    if git rev-parse --verify '@{upstream}' >/dev/null 2>&1; then
      git diff --name-only --diff-filter=ACMR '@{upstream}...HEAD'
    fi
  } 2>/dev/null | awk '/\.php$/ && !seen[$0]++'
}

stack_configuration_check() {
  project_exec php -r '
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$checks = [
    config("database.connections.pgsql.driver") === "pgsql",
    config("cache.stores.redis.driver") === "redis",
    config("queue.connections.redis.driver") === "redis",
    config("database.redis.client") === "phpredis",
    class_exists(Cloudinary\Cloudinary::class),
    $app->bound(Cloudinary\Cloudinary::class),
];
exit(in_array(false, $checks, true) ? 1 : 0);
'
}

docker_runtime_declarations_check() {
  [[ -f Dockerfile && -f docker-compose.yml ]] || return 1
  grep -Fq 'pdo_pgsql' Dockerfile \
    && grep -Fq 'docker-php-ext-enable redis' Dockerfile \
    && grep -Fq 'redis:7-alpine' docker-compose.yml \
    && grep -Fq ' AS frontend' Dockerfile
}

blade_compile_check() {
  if [[ "$ACTIVE_RUNNER" == "docker" ]]; then
    project_exec sh -lc '
      compiled_dir="$(mktemp -d /tmp/aanaya-blade.XXXXXX)" || exit 1
      VIEW_COMPILED_PATH="$compiled_dir" php artisan view:cache --no-interaction
      status=$?
      rm -rf "$compiled_dir"
      exit "$status"
    '
    return
  fi

  local compiled_dir
  compiled_dir="$(mktemp -d "${TMPDIR:-/tmp}/aanaya-blade.XXXXXX")" || return 1
  VIEW_COMPILED_PATH="$compiled_dir" php artisan view:cache --no-interaction
  local status=$?
  rm -rf "$compiled_dir"
  return "$status"
}

scan_sensitive_filenames() {
  local suspicious=0
  local files

  files="$({
    git diff --name-only
    git diff --cached --name-only
    git ls-files --others --exclude-standard
    if git rev-parse --verify '@{upstream}' >/dev/null 2>&1; then
      git diff --name-only '@{upstream}...HEAD'
    fi
  } 2>/dev/null | sort -u)"

  while IFS= read -r file; do
    [[ -z "$file" ]] && continue

    case "$file" in
      .env|.env.*|*.pem|*.key|*.p12|*.pfx|*.jks|id_rsa|id_ed25519|*.sql|*.sqlite|*.sqlite3)
        # Permit documented examples and explicit test fixtures.
        case "$file" in
          .env.example|.env.testing.example|tests/Fixtures/*|database/schema/*)
            ;;
          *)
            printf 'Suspicious file: %s\n' "$file"
            suspicious=1
            ;;
        esac
        ;;
    esac
  done <<< "$files"

  return "$suspicious"
}

scan_added_secret_patterns() {
  local diff_content
  local matches

  diff_content="$({
    git diff --unified=0
    git diff --cached --unified=0
    if git rev-parse --verify '@{upstream}' >/dev/null 2>&1; then
      git diff --unified=0 '@{upstream}...HEAD'
    fi
  } 2>/dev/null)"

  # Show only file and line metadata plus redacted pattern category.
  matches="$(printf '%s\n' "$diff_content" | grep -E '^\+[^+]' | grep -Ei \
    '(BEGIN (RSA |EC |OPENSSH )?PRIVATE KEY|AWS_SECRET_ACCESS_KEY|GOOGLE_APPLICATION_CREDENTIALS|PRIVATE_KEY[[:space:]]*=|PASSWORD[[:space:]]*=[^[:space:]]+|API_KEY[[:space:]]*=[^[:space:]]+|SECRET[[:space:]]*=[^[:space:]]+|TOKEN[[:space:]]*=[^[:space:]]+)' || true)"

  if [[ -n "$matches" ]]; then
    printf 'Potential secret-like values were found in added lines. Values are intentionally not printed.\n'
    return 1
  fi

  return 0
}

header "Laravel Quality Check"
printf 'Project : %s\n' "$PROJECT_ROOT"
printf 'Mode    : %s\n' "$MODE"
printf 'Runner  : %s\n' "$ACTIVE_RUNNER"
if [[ "$ACTIVE_RUNNER" == "docker" ]]; then
  printf 'Service : %s\n' "$DOCKER_SERVICE"
fi

header "Git Safety"

if command -v git >/dev/null 2>&1 && git rev-parse --is-inside-work-tree >/dev/null 2>&1; then
  run_check "Git whitespace validation" git diff --check || true

  if [[ -n "$(git diff --name-only --diff-filter=U 2>/dev/null)" ]]; then
    git diff --name-only --diff-filter=U
    fail "Unresolved merge conflicts detected"
  else
    pass "No unresolved merge conflicts detected"
  fi

  printf '\nRepository status:\n'
  git status --short || true

  if scan_sensitive_filenames; then
    pass "No suspicious sensitive filenames in the current push/worktree scope"
  else
    fail "Suspicious sensitive filenames detected"
  fi

  if scan_added_secret_patterns; then
    pass "No obvious secret-like patterns in added diff lines"
  else
    fail "Potential secret-like patterns detected in added diff lines"
  fi
else
  warn "Git repository not detected; Git safety checks skipped"
fi

header "Runtime"

run_check "PHP version" project_exec php --version || true
run_check "Composer version" project_exec composer --version || true
if [[ "$MODE" == "full" ]]; then
  run_check "Composer manifest validation (strict)" project_exec composer validate --strict --no-interaction || true
else
  run_check "Composer manifest validation" project_exec composer validate --no-interaction || true
fi

if [[ "$SKIP_AUDIT" == "1" ]]; then
  skip "Composer security audit disabled by LARAVEL_SKIP_AUDIT=1"
elif [[ -f composer.lock ]]; then
  run_check "Composer security audit" project_exec composer audit --no-interaction || true
else
  skip "Composer security audit: composer.lock not found"
fi

if project_file_exists artisan; then
  run_check "Laravel application boot check" project_exec php artisan about --no-interaction || true
else
  fail "artisan file not found in execution environment"
fi

header "Aanaya Stack Contracts"

if [[ "$SKIP_STACK" == "1" ]]; then
  skip "Aanaya stack checks disabled by LARAVEL_SKIP_STACK=1"
else
  run_check "PostgreSQL, Cloudinary, and Redis Laravel configuration" stack_configuration_check || true

  if [[ "$ACTIVE_RUNNER" == "docker" ]]; then
    run_check "Docker PHP Redis extension" project_exec php --ri redis || true
    run_check "Docker PHP PostgreSQL extensions" project_exec php -r 'exit(extension_loaded("pdo_pgsql") && extension_loaded("pgsql") ? 0 : 1);' || true
  else
    run_check "Docker declarations for PostgreSQL and Redis" docker_runtime_declarations_check || true
  fi

  run_check "Cloudinary direct-upload and MCP contract tests" project_test php artisan test tests/Feature/DirectMediaUploadTest.php tests/Feature/CloudinaryMcpServerTest.php || true
  run_check "Blade template compilation" blade_compile_check || true
fi

header "Changed PHP Syntax"

PHP_FILE_FOUND=0

while IFS= read -r php_file; do
  [[ -z "$php_file" ]] && continue
  PHP_FILE_FOUND=1

  if [[ ! -f "$php_file" ]]; then
    skip "PHP syntax: $php_file no longer exists"
    continue
  fi

  run_check "PHP syntax: $php_file" project_exec php -l "$php_file" || true
done < <(changed_php_files)

if [[ "$PHP_FILE_FOUND" -eq 0 ]]; then
  skip "No changed PHP files detected"
fi

header "Code Quality"

if project_has_executable ./vendor/bin/pint; then
  if [[ "$MODE" == "full" ]]; then
    run_check "Laravel Pint full-repository formatting check" project_exec ./vendor/bin/pint --test || true
  else
    CHANGED_PHP_FILES=()
    while IFS= read -r php_file; do
      [[ -n "$php_file" && -f "$php_file" ]] && CHANGED_PHP_FILES+=("$php_file")
    done < <(changed_php_files)

    if [[ "${#CHANGED_PHP_FILES[@]}" -gt 0 ]]; then
      run_check "Laravel Pint changed-file formatting check" project_exec ./vendor/bin/pint --test "${CHANGED_PHP_FILES[@]}" || true
    else
      skip "Laravel Pint changed-file check: no changed PHP files"
    fi
  fi
else
  skip "Laravel Pint not installed"
fi

if project_has_executable ./vendor/bin/phpstan; then
  run_check "PHPStan / Larastan analysis" project_exec ./vendor/bin/phpstan analyse --no-interaction || true
else
  skip "PHPStan / Larastan not installed"
fi

if project_has_executable ./vendor/bin/psalm; then
  run_check "Psalm analysis" project_exec ./vendor/bin/psalm --no-progress || true
else
  skip "Psalm not installed"
fi

header "Tests"

if [[ "$MODE" == "full" ]]; then
  if [[ -n "$TEST_ARGS" ]]; then
    # Intentional word splitting for command arguments supplied by the developer.
    # shellcheck disable=SC2206
    EXTRA_TEST_ARGS=($TEST_ARGS)
    run_check "Laravel test suite" project_test php artisan test "${EXTRA_TEST_ARGS[@]}" || true
  else
    run_check "Laravel test suite" project_test php artisan test || true
  fi
else
  skip "Full Laravel test suite is only run in full mode"
fi

header "Frontend"

if [[ "$SKIP_FRONTEND" == "1" ]]; then
  skip "Frontend checks disabled by LARAVEL_SKIP_FRONTEND=1"
elif [[ ! -f package.json ]]; then
  skip "package.json not found"
elif ! json_has_script build; then
  skip "No build script found in package.json"
else
  PACKAGE_MANAGER=""

  if [[ -f pnpm-lock.yaml ]]; then
    PACKAGE_MANAGER="pnpm"
  elif [[ -f yarn.lock ]]; then
    PACKAGE_MANAGER="yarn"
  elif [[ -f bun.lockb || -f bun.lock ]]; then
    PACKAGE_MANAGER="bun"
  elif [[ -f package-lock.json ]]; then
    PACKAGE_MANAGER="npm"
  fi

  if [[ -z "$PACKAGE_MANAGER" ]]; then
    warn "Could not determine frontend package manager from lockfile; frontend build skipped"
  elif command -v "$PACKAGE_MANAGER" >/dev/null 2>&1; then
    run_check "Frontend production build ($PACKAGE_MANAGER)" "$PACKAGE_MANAGER" run build || true
  elif command -v docker >/dev/null 2>&1 && docker info >/dev/null 2>&1 && [[ -f Dockerfile ]]; then
    run_check "Frontend Docker stage build" docker build --target frontend --file Dockerfile . || true
  else
    skip "$PACKAGE_MANAGER is unavailable on the host and Docker frontend stage is unavailable"
  fi
fi

header "Docker Compose"

if command -v docker >/dev/null 2>&1 && docker compose version >/dev/null 2>&1; then
  if [[ -f compose.yaml || -f compose.yml || -f docker-compose.yaml || -f docker-compose.yml || -n "$COMPOSE_FILE" ]]; then
    run_check "Docker Compose configuration" compose_exec config --quiet || true
  else
    skip "Docker Compose file not found"
  fi
else
  skip "Docker Compose is unavailable"
fi

header "Final Repository Status"

if command -v git >/dev/null 2>&1 && git rev-parse --is-inside-work-tree >/dev/null 2>&1; then
  git status --short || true
else
  skip "Git repository status unavailable"
fi

header "Summary"

printf 'Passed : %d\n' "$PASS_COUNT"
printf 'Failed : %d\n' "$FAIL_COUNT"
printf 'Warnings: %d\n' "$WARN_COUNT"
printf 'Skipped : %d\n' "$SKIP_COUNT"

if [[ "$FAIL_COUNT" -gt 0 ]]; then
  printf '\nRESULT: FAILED\n'
  exit 1
fi

if [[ "$WARN_COUNT" -gt 0 || "$SKIP_COUNT" -gt 0 ]]; then
  printf '\nRESULT: PASSED WITH WARNINGS / SKIPPED CHECKS\n'
else
  printf '\nRESULT: PASSED\n'
fi

exit 0
