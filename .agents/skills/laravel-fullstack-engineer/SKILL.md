---
name: laravel-fullstack-engineer
description: Analyze, implement, debug, optimize, and review full-stack Laravel features safely. Use for Laravel routes, controllers, Form Requests, policies, services/actions, Eloquent, migrations, APIs, Blade/Livewire/Vue/React UI, Vite assets, queues, tests, Docker, and pre-push verification.
---

# Laravel Full-Stack Engineer

Use this skill for implementation, debugging, refactoring, performance work, security review, or pre-push verification in a Laravel repository.

Read the repository's applicable `AGENTS.md` files before doing any work.
For Aanaya, read `docs/README.md` and the smallest relevant linked documents before scanning implementation files broadly. Confirm documentation against source code.

## Goal

Deliver the smallest complete Laravel change that:

- satisfies the requested behavior;
- follows the repository's conventions;
- remains compatible with the existing PHP, Laravel, Node, database, and Docker versions;
- is secure and authorized server-side;
- preserves data integrity;
- is efficient enough for the expected workload;
- provides clear, responsive, accessible UI behavior;
- is readable by another developer;
- includes meaningful verification.

## Modes

Infer the mode from the request.

### Analyze mode

Use when the user asks for explanation, investigation, root cause, architecture review, or recommendations.

Do not edit files unless explicitly requested.

### Implement mode

Use when the user asks to add, change, fix, or optimize behavior.

Implement only after understanding the relevant flow.

### Review mode

Use when the user asks for code review, readiness review, or a check before push.

Default to review-only. Do not modify code, commit, or push unless separately requested.

### Fix-review mode

Use only when the user explicitly asks to fix review findings.

Apply minimal fixes, add regression coverage, rerun checks, and report the new verdict.

---

## Workflow

### 1. Establish repository state

Inspect:

```bash
git status --short
git branch --show-current
git diff --check
git diff --stat
git diff
git diff --cached --check
git diff --cached --stat
git diff --cached
git ls-files --others --exclude-standard
```

When reviewing content intended for push, inspect the upstream range when available:

```bash
git rev-parse --abbrev-ref --symbolic-full-name '@{upstream}'
git log --oneline '@{upstream}..HEAD'
git diff --stat '@{upstream}...HEAD'
git diff '@{upstream}...HEAD'
```

Do not fetch, pull, switch branches, reset, clean, merge, rebase, commit, or push without explicit permission.

### 2. Identify the runtime and architecture

Read the relevant files:

- `composer.json` and `composer.lock`;
- `package.json` and its lockfile;
- `artisan`;
- `bootstrap/app.php`;
- routes;
- Dockerfile and Compose configuration;
- CI configuration;
- README;
- relevant application and test files.

Determine:

- Laravel and PHP versions;
- frontend stack;
- database;
- authentication method;
- testing framework;
- formatter/static analyzer;
- Docker service names;
- existing architectural pattern.

Do not assume a package or tool exists merely because it is common in Laravel projects.

### 3. Trace the complete feature

For the requested behavior, inspect the complete path:

```text
Route
→ Middleware
→ Authentication
→ Authorization
→ Form Request / Validation
→ Controller
→ Service / Action / Domain Logic
→ Model / Query
→ Transaction / Side Effects
→ API Resource / Response
→ Blade / Livewire / JavaScript UI
→ Tests
```

Read full functions and their callers, not only changed diff lines.

Find a similar accepted implementation in the repository and prefer consistency over introducing a new pattern.

### 4. Plan the smallest complete change

Before editing, define:

- files that need changes;
- API or database contract impact;
- authorization rules;
- validation rules;
- transaction and concurrency needs;
- expected UI states;
- tests needed;
- verification commands.

Avoid unrelated cleanup and speculative refactoring.

### 5. Implement safely

Apply the applicable rules:

#### HTTP and API

- Keep controllers focused on HTTP orchestration.
- Use Form Requests for complex validation.
- Use validated data rather than `$request->all()`.
- Authorize server-side with Policies, Gates, middleware, or existing project mechanisms.
- Preserve response contracts and status codes.
- Do not expose internal exception details.

#### Database

- Avoid N+1 queries.
- Bound potentially large results.
- Use indexes and constraints intentionally.
- Use transactions for atomic multi-write workflows.
- Consider duplicate requests, retries, idempotency, and race conditions.
- Do not modify historical migrations that may have run.
- Never run destructive database commands without explicit approval.

#### Files

- Validate size and real content type.
- Generate safe names.
- Use configured filesystem disks.
- Prevent path traversal.
- Define replacement and cleanup behavior.
- Protect private files with authorization or signed access.

#### Queue and external services

- Add timeouts.
- Handle failures explicitly.
- Make retries safe.
- Keep payloads small and free of secrets.
- Dispatch after commit when required.

#### Frontend

Handle:

- loading;
- empty;
- validation;
- error;
- success;
- retry;
- disabled;
- duplicate-submission prevention;
- responsive layout;
- keyboard access;
- focus;
- semantic markup;
- clear copy.

Do not add a heavy dependency for a simple interaction.

#### Naming

Use names that communicate business intent. Avoid generic variables such as `data`, `obj`, `arr`, `res`, `temp`, `value2`, or `finalData` when a clearer name is practical.

### 6. Add tests

Prefer Feature tests for:

- routes;
- validation;
- authentication;
- authorization;
- database state;
- JSON response contracts;
- uploads;
- queues and notifications;
- regressions.

Use Unit tests for isolated domain logic.

Test both successful and realistic failure paths. Do not remove meaningful assertions merely to make a test pass.

### 7. Run the bundled Laravel quality tool

The skill includes:

```text
scripts/laravel-quality-check.sh
```

Run from the repository root:

```bash
bash .agents/skills/laravel-fullstack-engineer/scripts/laravel-quality-check.sh quick
```

Before declaring a task ready or before push:

```bash
bash .agents/skills/laravel-fullstack-engineer/scripts/laravel-quality-check.sh full
```

For a Dockerized project, use:

```bash
LARAVEL_RUNNER=docker \
LARAVEL_DOCKER_SERVICE=app \
bash .agents/skills/laravel-fullstack-engineer/scripts/laravel-quality-check.sh full
```

Replace `app` only after reading the actual Compose service name.

In Aanaya, `auto` prioritizes the running PHP 8.2 Docker `app` service and falls back to host PHP when the container is unavailable. Frontend verification still runs with the host lockfile package manager when available because the PHP-FPM service does not contain Node; otherwise it builds the Dockerfile `frontend` stage.

The tool modes intentionally differ:

- `quick`: syntax and Pint for changed, staged, upstream, and untracked PHP files; focused Laravel stack contracts; Blade compilation; frontend build; Compose validation.
- `full`: full-repository Pint, the complete Laravel test suite, and the same stack/build checks.

The script is a safe verifier. It does not install dependencies, migrate databases, modify source code, commit, or push.

When a repository-specific command is more authoritative than the bundled tool, run the repository command as well.

### 8. Review the final diff

Check:

- only intended files changed;
- no credentials or `.env` files;
- no debug statements;
- no accidental generated files;
- no unrelated lockfile churn;
- no route or response regression;
- no unhandled UI state;
- no missing authorization;
- no N+1 query;
- no unsafe migration;
- no test weakened without reason.

Run `git status --short` after all verification.

---

## Review Severity

Use:

### Critical

Credential exposure, exploitable security issue, destructive data loss, severe authorization bypass, or broad application failure.

### High

Likely runtime failure, incorrect core behavior, serious regression, unsafe migration, major data-integrity issue, unresolved conflict, or required checks failing because of the change.

### Medium

Real defect, limited regression, maintainability issue, credible performance issue, incomplete UX state, or important missing test that should be fixed soon.

### Low

Small clarity, consistency, resilience, or cleanup issue with low expected impact.

Do not report personal style preferences as defects.

---

## Readiness Verdict

For pre-push reviews, use exactly one:

- `NOT READY TO PUSH`
- `READY WITH WARNINGS`
- `READY TO PUSH`

`NOT READY TO PUSH` applies when Critical or High findings remain, conflicts exist, secrets may be committed, required checks fail because of the change, or a migration/configuration change has credible production risk.

`READY WITH WARNINGS` applies when no blocking findings remain but verification is incomplete or Medium/Low risks remain.

`READY TO PUSH` requires reviewed push scope, no blocking findings, successful relevant verification, and no accidental sensitive or unrelated files.

---

## Required Final Report

For implementation:

```markdown
## Implemented
- Behavior completed

## Files Changed
- `path/to/file`: purpose

## Design Notes
- Important decisions and tradeoffs

## Verification
- `exact command`: passed / failed / not run

## Assumptions and Remaining Risk
- Explicit limitations
```

For review:

```markdown
# Laravel Review

## Verdict
READY TO PUSH | READY WITH WARNINGS | NOT READY TO PUSH

## Scope Reviewed
- Branch, base, commits, staged, unstaged, and untracked scope

## Blocking Findings
- Severity, `file:line`, scenario, impact, smallest safe correction
- Write `None` when empty

## Non-Blocking Findings
- Actionable Medium and Low findings
- Write `None` when empty

## Verification
- Exact command and actual result

## Push Contents
- Logical summary and suspicious/unrelated files

## Residual Risk
- Untested paths, unavailable services, or assumptions

## Recommended Next Action
- One concrete next step
```

Never claim a command passed if it was not run successfully.
