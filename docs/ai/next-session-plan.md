# Next Session Plan

**Updated**: 2026-05-15 20:25 (America/Indiana)
**Branch**: `release-v1.0.0`

---

## Start Here

Slice 19 is complete. All 9 gates done.

**Slice 20 is next** — human code review & cleanup feedback. The BoxClientFactory namespace move is part of Slice 20 scope (or may be broken out as its own slice if it grows).

---

## Slice 20 Scope

### 1. Typed Constants
Audit `src/` for untyped class constants. Add PHP 8.3+ type declarations wherever the constant has a concrete scalar type. Example:
```php
public const string ENDPOINT = 'https://api.box.com/2.0/files';
```
Pre-flight: `grep -rn "const " src/ --include="*.php" | grep -v "const string\|const int\|const bool\|const float\|const array"` to find candidates.

### 2. Type Coverage Audit
Review `src/` for untyped or `mixed` properties, parameters, and return types. Tighten where the actual type is known. Documented exceptions (e.g., intentional `mixed` for legacy hydration) are acceptable — just confirm they're intentional.

### 3. Property Hooks on DTOs / Value Objects
Audit classes in `src/Dto/`, `src/Resource/`, `src/Connection/Token/` for simple get/set pairs that qualify for PHP 8.4 property hooks. Apply when:
- Class is data-only (no interface method contracts declaring getters/setters)
- Property is public API (`$obj->prop` access is natural)
- Hook logic is lightweight (normalize/coerce/guard — no service calls, no side-effects)
- No fluent setter chain needed

Skip: any class implementing an interface with getter/setter method signatures.

### 4. BoxClientFactory Namespace Move
Move `Box\Service\BoxClientFactory` → `Box\Factory\BoxClientFactory`. Rename `createClient()` → `createOAuth2Client()`. Update `BoxClientFactoryInterface`. Update all callers, tests, migration guide.

**Pre-flight:**
```
grep -rn "BoxClientFactory\|createClient\b" src/ tests/ bin/ --include="*.php"
```

---

## After Slice 20

- Step 17: v1 Release Readiness (docs, changelog, `composer.json` version bump, security scan)
- Step 18: Documentation Cleanup and Organization
- Package/repo rename (user-driven — user will ping when ready)

---

## Resolved Questions (do not re-open)

All Slice 19 resolved questions carry forward — see `current-handoff-summary.md`.

---

## Acceptance Criteria for This Slice

- `BoxClientFactory` lives in `Box\Factory`, `createOAuth2Client()` is the public API
- `BoxClientFactoryInterface` updated in tandem
- Migration guide updated with the breaking change
- `composer review` green
