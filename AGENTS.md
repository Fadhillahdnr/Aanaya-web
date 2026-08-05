# Laravel Project Engineering Guide

## Purpose

This file defines the permanent engineering rules for AI coding agents working in this repository.

Act as a senior full-stack Laravel engineer. Before writing code, understand the existing architecture, business flow, supported runtime, conventions, and operational constraints. Prefer the smallest correct change that is easy to read, test, operate, and maintain.

These instructions apply to every task in this repository unless a more specific `AGENTS.md` exists in a subdirectory.

---

## 1. Project Baseline

The expected baseline for this project is:

- PHP 8.2
- Laravel 12
- Composer
- Blade and/or a JavaScript frontend built with Vite
- PostgreSQL melalui Supabase sebagai database production
- Docker and Docker Compose for local development where provided
- Laravel Cloud sebagai deployment environment production
- Cloudinary sebagai penyimpanan media utama
- Redis untuk cache, session, dan queue pada environment Docker

### Aanaya project profile

- **Application:** website band/music sekaligus content platform dan merchandise store.
- **Backend:** Laravel 12, Eloquent, Blade, Form Requests where established, jobs, scheduler, and PHPUnit 11.
- **Authentication:** Laravel Breeze/session auth, Google OAuth through Socialite, email verification, and password reset through Laravel mail/Resend.
- **Frontend:** Blade, modular plain CSS, vanilla JavaScript, Alpine.js where useful, and Vite 7. Tailwind packages exist but Tailwind is not the default styling architecture; follow the existing page CSS before introducing utilities.
- **Production database:** PostgreSQL hosted by Supabase. Automated tests use SQLite in-memory, so database-sensitive behavior such as row locking, constraints, indexes, and PostgreSQL SQL must receive an additional PostgreSQL review.
- **Media:** signed direct browser uploads to Cloudinary, persisted through the `media` registry, then claimed by domain models. Cleanup is asynchronous through queue jobs.
- **Commerce:** session cart, product variants, transactional stock reduction, orders, user tracking, and WhatsApp handoff. Database price and stock are authoritative at checkout.
- **Local containers:** Nginx 1.28, PHP 8.2 FPM, Redis 7, queue worker, and scheduler. The `app` service contains PHP but not Node.
- **Production:** Laravel Cloud terminates the application runtime; Supabase, Cloudinary, Resend, Google OAuth, and a Laravel Cloud KV/Redis-compatible resource are external integrations.
- **Nginx boundary:** the repository Nginx configuration belongs to Docker. Do not assume Laravel Cloud uses that Nginx configuration.
- **Local host:** PHP may be newer than production. PHP 8.2 compatibility remains mandatory even when host verification runs on PHP 8.4.

Treat `composer.json`, `composer.lock`, `package.json`, lockfiles, Docker files, CI configuration, and existing source code as the source of truth.

Do not:

- upgrade PHP, Laravel, Composer packages, Node packages, or database versions unless explicitly requested;
- replace an existing architectural pattern merely because another pattern is more fashionable;
- introduce a new dependency when the current stack can solve the problem clearly;
- assume local machine versions are the same as the versions used by Docker or CI.

Preserve backward compatibility unless the task explicitly requires a breaking change.

---

## 2. Required Working Method

Before changing code:

1. Read `docs/README.md`, then read the smallest relevant documents from its lookup table. The local `docs` directory is the project knowledge index even though it is intentionally ignored by Git and Docker.
2. Confirm documentation claims against current source code. Source code and lockfiles remain authoritative when documentation is stale.
3. Read the task and identify its expected user-visible or system-visible behavior.
4. Inspect the relevant routes, controllers, requests, policies, services/actions, models, resources, migrations, jobs, events, views/components, JavaScript, tests, and configuration.
5. Find a similar implementation already accepted in the repository.
6. Trace the complete data flow and identify:
   - input;
   - validation;
   - authentication;
   - authorization;
   - business rules;
   - persistence;
   - side effects;
   - response;
   - frontend state;
   - failure behavior.
7. Identify compatibility, security, data-integrity, performance, and UX risks.
8. Make a short implementation plan.
9. Implement the smallest cohesive change.
10. Add or update relevant tests.
11. Update the relevant `docs` files in the same change when architecture, routes, schema, business rules, integrations, or operational behavior changes.
12. Run the smallest relevant verification first, followed by broader checks.
13. Review the final diff before reporting completion.

Do not begin with a broad refactor. Do not edit unrelated files.

When requirements are incomplete, infer from existing project behavior and state assumptions in the final report. Never invent API contracts, table structures, environment variables, or business rules when they can be discovered from the repository.

---

## 3. Code Quality and Naming

Write code that is easy for another developer to understand without unnecessary comments.

### Naming

Use names based on business meaning and behavior.

Prefer:

```php
$validatedOrderData
$authenticatedCustomer
$uploadedProductImage
$orderTotalInCents
$isPaymentConfirmed
$maximumUploadSizeKilobytes
```

Avoid vague names when a domain-specific name is practical:

```php
$data
$info
$obj
$arr
$res
$temp
$value2
$resultData
$finalData
```

Short names are acceptable only for very small, conventional scopes, such as `$id`, `$key`, `$item`, or `$query` when their meaning is obvious.

Boolean names should read naturally:

```php
$isActive
$hasPermission
$canCancelOrder
$shouldSendNotification
```

Method names should describe an action or query:

```php
calculateOrderTotal()
findAvailableProducts()
authorizeProductUpdate()
```

### Readability

- Prefer early returns over excessive nesting.
- Keep methods focused on one meaningful responsibility.
- Avoid hidden side effects.
- Replace repeated business rules with one well-named implementation.
- Use existing enums, value objects, constants, scopes, helpers, services, or actions when appropriate.
- Do not create abstractions for a single trivial operation.
- Remove dead imports and code introduced by the current change.
- Do not leave commented-out code.
- Comments must explain intent, constraints, or non-obvious tradeoffs—not restate the code.

Follow PSR-12 and the repository's formatter.

---

## 4. Laravel Architecture

Use Laravel conventions without forcing unnecessary layers.

### Controllers

Controllers should coordinate HTTP concerns:

- receive validated input;
- authorize the action;
- call business logic;
- return a response.

Do not place large business workflows, complex calculations, repeated queries, or infrastructure details directly in controllers.

### Validation

- Validate every external input.
- Use a Form Request for complex, reusable, or authorization-aware validation.
- Use `$request->validated()` or `$request->safe()` instead of `$request->all()`.
- Define appropriate type, size, length, format, enum, existence, uniqueness, and ownership constraints.
- Treat frontend validation as UX only; enforce validation again on the server.
- Ensure update uniqueness rules ignore only the correct current record.
- Give users useful validation messages when default messages are unclear.

### Business Logic

Place reusable or complex business logic in an existing service, action, domain class, or model method according to repository conventions.

Do not introduce a service/repository layer solely to wrap one simple Eloquent call.

### Authorization

Authentication does not imply authorization.

- Enforce authorization on the server.
- Prefer Policies for model/resource actions.
- Prefer Gates for actions not naturally tied to a model.
- Do not rely only on hidden buttons, disabled inputs, route names, or client-provided roles.
- Prevent users from reading or mutating records they do not own or cannot administer.
- Validate tenant, organization, unit, or ownership boundaries where applicable.

### API Responses

- Preserve existing response contracts.
- Use correct HTTP status codes.
- Prefer API Resources when the project already uses them.
- Do not expose internal exceptions, stack traces, hidden model attributes, credentials, or unnecessary database fields.
- Keep pagination shape consistent.
- Document or test intentional contract changes.

---

## 5. Eloquent and Database Rules

### Queries

- Avoid N+1 queries by using intentional eager loading.
- Select only needed columns when payload size or table width matters.
- Use pagination, cursors, chunking, or streaming for potentially large datasets.
- Never add an unbounded `Model::all()` to a growing production path without a documented reason.
- Perform filtering, sorting, and aggregation in the database when appropriate.
- Do not execute the same query repeatedly inside loops.
- Review new query paths for appropriate indexes.
- Use `exists()` when only existence is needed.
- Use `value()` when only one scalar is needed.
- Preserve stable ordering for pagination.

### Mass Assignment

Do not pass untrusted input directly:

```php
Model::create($request->all());
```

Use validated, explicitly mapped data and maintain intentional `$fillable` or `$guarded` configuration.

### Transactions and Concurrency

Use a database transaction when multiple writes must succeed or fail together.

Consider:

- partial writes;
- duplicate submissions;
- retries;
- idempotency;
- race conditions;
- lost updates;
- row locking when justified;
- unique constraints as the final protection against duplicates;
- dispatching jobs or events only after successful commit when required.

Do not add locks without understanding contention and transaction boundaries.

### Migrations

- Never edit an old migration that may already have run outside a disposable local environment.
- Create a new migration for schema evolution.
- Provide a valid `down()` method when rollback is supported and safe.
- Add foreign keys, unique constraints, indexes, nullability, and defaults intentionally.
- Consider existing rows before adding non-null columns.
- Treat column drops, type changes, renames, large table rewrites, and data backfills as deployment-sensitive.
- Keep schema migration and large data migration concerns separate when practical.
- Do not run destructive commands such as `migrate:fresh`, `migrate:reset`, `db:wipe`, or database drops without explicit approval and confirmation that the target is disposable.

---

## 6. Security and Privacy

Treat all request data, route parameters, uploaded files, headers, cookies, third-party responses, queue payloads, and imported data as untrusted.

Check for:

- SQL or NoSQL injection;
- mass assignment;
- insecure direct object references;
- authorization bypass;
- XSS;
- CSRF where applicable;
- path traversal;
- command injection;
- unsafe deserialization;
- open redirects;
- SSRF in user-controlled URLs;
- sensitive logging;
- weak file upload controls;
- exposed debug information;
- unsafe CORS;
- missing rate/resource controls on abuse-prone endpoints.

Never commit or print:

- `.env` contents;
- passwords;
- tokens;
- private keys;
- cloud credentials;
- database dumps with real user data;
- production secrets;
- personally identifiable data not needed for the task.

Use `env()` only in configuration files. Application code should read values through `config()`.

Keep `APP_DEBUG=false` in production configuration.

---

## 7. File Uploads and Storage

For uploads:

- authorize who may upload and who may access the result;
- validate file presence, size, MIME type, and expected image/file properties;
- never trust the client filename or extension;
- generate safe storage names;
- prevent path traversal;
- use Laravel filesystem disks instead of hard-coded local paths;
- avoid loading large files entirely into memory when streaming is available;
- clean up files when a multi-step operation fails;
- define replacement and deletion behavior;
- avoid public exposure unless required;
- use signed or authorized access for private files;
- do not log file contents, tokens, or sensitive paths.

When replacing a file, do not delete the previous valid file until the new state can be committed safely, unless repository design explicitly dictates otherwise.

For Aanaya direct media uploads:

- preserve the sign → direct Cloudinary upload → completion verification → media claim flow;
- never proxy large media through the Laravel request unless explicitly redesigning the architecture;
- verify uploader ownership, purpose, MIME type, size, Cloudinary response signature, resource type, and media state;
- preserve image/audio/video-specific dimension and duration rules;
- queue external deletion after database commit and keep jobs retry-safe;
- update `docs/08-direct-media-upload.md` when this contract changes.

---

## 8. Queues, Events, Notifications, and Scheduler

Jobs should be safe under retries.

Review:

- idempotency;
- duplicate execution;
- retry count and backoff;
- timeout;
- failed job behavior;
- serialized payload size;
- model state becoming stale;
- jobs dispatched before database commit;
- sensitive values in job payloads;
- external API timeouts and error handling.

For scheduled tasks:

- prevent overlap when overlapping is unsafe;
- use one-server execution where required;
- make repeated execution safe;
- log enough context for diagnosis without leaking secrets.

Events should not hide critical business behavior that must succeed synchronously unless asynchronous behavior is intentional and tested.

---

## 9. Cache and Configuration

- Use stable, namespaced cache keys.
- Include tenant/user context when data is scoped.
- Define invalidation behavior.
- Avoid caching authorization decisions longer than their source data allows.
- Do not cache sensitive responses in shared keys.
- Do not call `env()` outside `config/`.
- Avoid hard-coded local hosts, ports, paths, or credentials.
- Document new required environment variables in `.env.example` without real secrets.
- Ensure configuration works with Laravel configuration caching.

Treat `php artisan optimize` as a deployment concern, not a default development command.

---

## 10. Frontend and UI/UX

Whether the project uses Blade, Livewire, Alpine, Vue, React, or plain JavaScript, provide a complete experience.

For every asynchronous or data-driven UI, consider:

- initial state;
- loading state;
- empty state;
- success state;
- validation state;
- error state;
- retry behavior;
- disabled state;
- duplicate submission prevention.

UI requirements:

- mobile-first and responsive;
- clear visual hierarchy;
- readable spacing and typography;
- consistent terminology;
- useful button labels;
- keyboard operability;
- visible focus;
- semantic HTML;
- form labels and associated errors;
- accessible names for controls;
- sensible dialog focus behavior;
- no unnecessary layout shift;
- no large dependency for a small interaction;
- no duplicated request or avoidable re-render.

Preserve user input after recoverable errors.

Do not sacrifice accessibility or clarity for visual decoration.

---

## 11. Performance and Application Weight

Optimize based on credible bottlenecks, not speculation.

Prefer:

- efficient database queries;
- bounded result sets;
- eager loading with intent;
- background processing for slow non-interactive work;
- caching with correct invalidation;
- lazy loading where it reduces real frontend cost;
- asset compression and appropriate image dimensions;
- server-side pagination;
- minimal dependencies;
- reuse of existing utilities.

Avoid:

- N+1 queries;
- large payloads;
- base64 file transport without a clear need;
- synchronous external API calls without timeouts;
- repeated serialization;
- repeated network requests;
- unnecessary global state;
- loading complete datasets for one screen;
- adding packages for functionality that can be implemented clearly with the existing stack.

Measure or explain performance claims whenever practical.

---

## 12. Error Handling and Observability

- Catch exceptions only when the code can recover, translate, add useful context, or perform required cleanup.
- Do not silently swallow errors.
- Preserve the original exception as the previous exception when wrapping.
- Return safe, consistent user-facing messages.
- Log stable identifiers and operational context, not secrets or entire sensitive payloads.
- Use an appropriate log level.
- Avoid noisy logs in hot paths.
- Ensure important background failures can be diagnosed.

Do not expose stack traces or internal exception messages to end users in production.

---

## 13. Testing Strategy

Prefer tests that protect behavior, not implementation details.

- Add Feature tests for routes, validation, authorization, database behavior, and end-to-end application flows.
- Add Unit tests for isolated calculations or domain logic with meaningful boundaries.
- Add regression tests for bugs.
- Test success and realistic failure paths.
- Test authorization denial, not only authenticated success.
- Test important boundaries and malformed input.
- Use factories and existing test helpers.
- Avoid real external network calls.
- Use fakes/mocks at system boundaries when appropriate.
- Keep tests deterministic.
- Do not weaken or delete a test only to make the suite pass.

Use the configured testing database. Never point automated tests at production data.

Aanaya test boundaries:

- PHPUnit uses SQLite in-memory by default and must never inherit the Supabase `DB_URL`;
- Cloudinary, Resend, Google, Supabase, and Redis tests must fake or inspect boundaries unless an explicit integration test environment was requested;
- tests must pass on a clean clone without optional local `.codex` MCP configuration;
- use PostgreSQL-aware review or a disposable PostgreSQL environment for locking, concurrency, JSON, index, and constraint behavior that SQLite cannot prove.

---

## 14. Dependency and Build Rules

Before adding a dependency:

1. Check whether the project already has equivalent functionality.
2. Check maintenance, compatibility, security, and bundle/runtime impact.
3. Explain why it is necessary.
4. Update the correct manifest and lockfile together.
5. Verify the dependency using the project's runtime and CI path.

Use the package manager selected by the existing lockfile. Do not mix npm, Yarn, pnpm, or Bun.

Do not manually edit lockfiles.

---

## 15. Docker and Environment

When Docker files are present:

- respect existing service names, networks, volumes, ports, users, and health checks;
- avoid embedding secrets in images;
- minimize production image size without harming reliability;
- keep build-time and runtime environment concerns separate;
- do not mount or copy unnecessary files;
- consider file ownership and writable Laravel directories;
- use `docker compose config` to validate Compose changes;
- prefer running project commands in the same environment used by the team.

Do not rebuild every service when only one service is relevant unless required.

Aanaya runtime rules:

- PHP production compatibility is 8.2; prefer the running Docker `app` service for PHP verification when it is available;
- run frontend builds on the host when the lockfile package manager exists, otherwise use the Dockerfile `frontend` stage—the PHP-FPM `app` service does not contain Node;
- validate `docker-compose.yml`, Nginx FastCGI/static routing, PHP-FPM configuration, Redis extension availability, queue, and scheduler when infrastructure changes;
- do not run migrations automatically during container startup;
- Laravel Cloud deployment settings are separate from Docker Nginx/PHP-FPM settings.

---

## 16. Git Safety

Before changing code, inspect repository state.

Do not:

- discard user changes;
- overwrite unrelated work;
- use `git reset --hard`;
- use `git clean -fd`;
- force-push;
- amend commits;
- rebase;
- merge;
- switch branches;
- create a commit;
- push;

unless explicitly requested.

Keep changes focused. Review staged, unstaged, untracked, and unpushed content before declaring work ready.

Never claim that tests, lint, analysis, or builds passed unless the commands were actually run successfully.

---

## 17. Verification

Discover commands from:

- `composer.json`;
- `package.json`;
- `README.md`;
- `Makefile`;
- CI configuration;
- Docker configuration;
- existing scripts.

Use the repository's own scripts before inventing commands.

Typical checks, only when available and relevant:

```bash
composer validate --strict
composer audit
./vendor/bin/pint --test
./vendor/bin/phpstan analyse
php artisan test
npm run build
docker compose config --quiet
```

Repository quality commands:

```bash
bash .agents/skills/laravel-fullstack-engineer/scripts/laravel-quality-check.sh quick
bash .agents/skills/laravel-fullstack-engineer/scripts/laravel-quality-check.sh full
```

`quick` checks changed and untracked PHP files plus focused stack contracts. `full` checks the complete Pint scope, full tests, frontend production build, Blade compilation, PostgreSQL/Cloudinary/Redis configuration, and Docker Compose. Neither mode may run migrations or connect to production services merely to verify configuration.

Run focused tests before the full suite when possible.

Do not install missing tools or mutate a database just to complete verification. Report unavailable checks honestly.

After verification, inspect `git status` again and report files generated or modified by tools.

---

## 18. Definition of Done

A task is complete only when:

- requested behavior is implemented;
- code follows existing architecture and conventions;
- names clearly communicate intent;
- validation and authorization are server-side;
- relevant edge cases are handled;
- data integrity and concurrency are considered;
- UI includes necessary loading/error/empty/success states;
- no unnecessary dependency was added;
- relevant tests were added or updated;
- applicable checks were executed;
- the final diff contains no accidental files;
- assumptions, verification results, and remaining risks are reported honestly.

Final reports should include:

1. what changed;
2. why it changed;
3. important design decisions;
4. files changed;
5. commands run and their results;
6. assumptions;
7. remaining risks or follow-up work.
