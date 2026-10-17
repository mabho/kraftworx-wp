# Agent Instructions

## Filesystem boundary

The project root is the boundary for routine file access:

```
/Users/marcoshollanda/Projects/__boxes/kraftworx
```

- **Within the project root:** proceed normally (read, list, glob, grep, edit, run commands).
- **Outside the project root:** always ask for permission first, even when the
  request seems harmless. This includes parent directories, sibling projects,
  and home-level or system paths such as:

  - `/Users/marcoshollanda/Projects/__boxes/` and other sibling projects
  - `/Users/marcoshollanda/Projects`, `/Users/marcoshollanda`
  - `~/.composer`, `~/.ddev`, `~/.config`, `~/.ssh`
  - `/opt`, `/etc`, `/usr`, and any other system location

Ask before using Read, Glob, Grep, List, or shell commands that inspect those
paths. When in doubt, ask.

Credentials and environment files intentionally live outside this repository
(e.g. `~/.composer/auth.json`, `~/.ddev/homeadditions`); do not read or modify
them without explicit permission.
