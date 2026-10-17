# Tech Debt Log

This file records known technical debt, temporary workarounds, and the
conditions under which each item should be resolved. Add a new entry for every
deliberate shortcut or pinned dependency.

## Conventions

- IDs are sequential: `TD-001`, `TD-002`, ...
- Status values: `Open`, `Mitigated`, `Resolved`, `Won't fix`.
- Every entry should state a concrete resolution criterion so it does not rot.
- When an item is resolved, keep the entry and mark it `Resolved` with the date.

## Index

| ID | Area | Status | Summary |
| --- | --- | --- | --- |
| TD-001 | Tooling / Composer | Open | Composer pinned to 2.8.12 to avoid forced HTTP/3 |

---

## TD-001: Composer pinned to 2.8.12 to avoid forced HTTP/3

**Status:** Open (workaround in place)
**Added:** 2026-10-17
**Area:** Tooling / Composer / DDEV
**Affects:** `ddev composer` for this project

### Symptom

Installing a private package (`wpengine/advanced-custom-fields-pro`) failed
during download with:

```
curl error 95 while downloading https://storage.googleapis.com/...:
HTTP/3 stream 0 reset by server
```

The dependency resolution and authentication succeeded; only the dist download
failed.

### Root cause

Composer 2.9.0 and later unconditionally request HTTP/3 when the PHP `curl`
extension reports HTTP/3 support:

```php
if (!$willUseProxy && defined('CURL_VERSION_HTTP3') && defined('CURL_HTTP_VERSION_3')
    && (CURL_VERSION_HTTP3 & $features) !== 0) {
    curl_setopt($curlHandle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_3);
}
```

On this network, QUIC/UDP is reset by the server, so the download aborts.
Composer 2.10.3 (the version shipped in the DDEV web image) still has this
behavior.

The upstream fix stops requesting HTTP/3 and was merged for the 2.11 milestone
(PR #13096). It was not released at the time this entry was created.

### Current mitigation

Composer is pinned to `2.8.12`, the latest stable release before the HTTP/3
behavior was introduced in 2.9.0.

- File: `.ddev/web-build/Dockerfile.composer`
- Pin: `ARG COMPOSER_VERSION=2.8.12`
- The phar is downloaded from `getcomposer.org` and verified with its
  `sha256sum` before replacing `/usr/local/bin/composer`.
- DDEV rebuilds the web image on restart, so `ddev composer` uses 2.8.12 for
  this project only.

Verify with:

```bash
ddev composer --version   # expect 2.8.12
```

### Resolution criterion

Resolve this item when a **stable** Composer `2.11.x` (or later) is available
and no longer forces HTTP/3.

### Resolution steps

1. Confirm the stable release exists and contains the fix (PR #13096):

   ```bash
   curl -s https://getcomposer.org/versions  # look for 2.11.x in stable
   ```

2. Either remove the pin so the DDEV-provided Composer is used again:

   ```bash
   rm .ddev/web-build/Dockerfile.composer
   ddev restart
   ```

   or bump the pin:

   ```dockerfile
   ARG COMPOSER_VERSION=2.11.x
   ```

3. Verify and re-run an install that exercises a download:

   ```bash
   ddev composer --version
   ddev composer install
   ```

4. Mark this entry `Resolved` with the date and the Composer version used.

### References

- Composer PR #13096: <https://github.com/composer/composer/pull/13096>
- Composer issue #12987: <https://github.com/composer/composer/issues/12987>
- DDEV custom web image builds: <https://docs.ddev.com/en/stable/users/extend/customizing-images/>

---

## Template

```markdown
## TD-XXX: <short title>

**Status:** Open | Mitigated | Resolved | Won't fix
**Added:** YYYY-MM-DD
**Area:** <tooling / security / architecture / dependency>
**Affects:** <what is impacted>

### Symptom

<what goes wrong>

### Root cause

<why it happens>

### Current mitigation

<the workaround, with file paths and versions>

### Resolution criterion

<the concrete signal that the debt can be removed>

### Resolution steps

<commands / changes to make>

### References

<links and related issues>
```
