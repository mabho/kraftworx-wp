#!/usr/bin/env bash

# Shared Cloudways sync configuration helpers.
# Source this file from host commands. It does not execute a sync.

if [ -n "${DDEV_APPROOT:-}" ]; then
  KW_PROJECT_ROOT="$DDEV_APPROOT"
else
  KW_PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
fi

KW_CONFIG_FILE="${KW_PROJECT_ROOT}/.ddev/sync/config.json"

kw_require_config() {
  if [ ! -f "$KW_CONFIG_FILE" ]; then
    printf 'Config file not found: %s\n' "$KW_CONFIG_FILE" >&2
    return 1
  fi

  if ! command -v jq >/dev/null 2>&1; then
    printf 'jq is required to read %s\n' "$KW_CONFIG_FILE" >&2
    return 1
  fi
}

kw_env_get() {
  local env="$1"
  local expression="$2"

  jq -r --arg env "$env" "$expression" "$KW_CONFIG_FILE"
}

kw_environment_exists() {
  local env="$1"

  jq -e --arg env "$env" '.environments[$env] != null' "$KW_CONFIG_FILE" >/dev/null 2>&1
}

kw_environment_enabled() {
  local env="$1"

  jq -e --arg env "$env" '.environments[$env].enabled == true' "$KW_CONFIG_FILE" >/dev/null 2>&1
}

kw_expand_path() {
  local value="$1"

  value="${value//\{server_id\}/$KW_SERVER_ID}"
  value="${value//\{folder\}/$KW_FOLDER_NAME}"
  value="${value//\{app_root\}/$KW_REMOTE_APP_ROOT}"
  value="${value//\{public_path\}/$KW_REMOTE_PUBLIC_PATH}"

  printf '%s' "$value"
}

kw_absolute_path() {
  case "$1" in
    /*) printf '%s' "$1" ;;
    ./*) printf '%s/%s' "$KW_PROJECT_ROOT" "${1#./}" ;;
    *) printf '%s/%s' "$KW_PROJECT_ROOT" "$1" ;;
  esac
}

kw_load_environment() {
  local env="$1"

  kw_require_config || return 1

  if ! kw_environment_exists "$env"; then
    printf 'Unknown environment: %s\n' "$env" >&2
    return 1
  fi

  if ! kw_environment_enabled "$env"; then
    printf "Environment '%s' is not configured yet.\n" "$env" >&2
    return 1
  fi

  KW_ENVIRONMENT="$env"
  KW_FOLDER_NAME="$(kw_env_get "$env" '.environments[$env].folder // empty')"
  KW_ENVIRONMENT_URL="$(kw_env_get "$env" '.environments[$env].url // empty')"

  if [ -z "$KW_FOLDER_NAME" ]; then
    printf "The Cloudways folder for '%s' is empty.\n" "$env" >&2
    return 1
  fi

  KW_SERVER_ID="$(kw_env_get "$env" '.environments[$env].server_id // .server.id')"
  KW_SSH_ALIAS="$(kw_env_get "$env" '.environments[$env].ssh_alias // .ssh.default_alias')"
  KW_REMOTE_HOST="${KRAFTWORX_SYNC_HOST:-$KW_SSH_ALIAS}"
  KW_SSH_CONNECT_TIMEOUT="$(jq -r '.ssh.connect_timeout // 15' "$KW_CONFIG_FILE")"

  KW_REMOTE_APP_ROOT="$(kw_env_get "$env" '.remote.app_root')"
  KW_REMOTE_APP_ROOT="$(kw_expand_path "$KW_REMOTE_APP_ROOT")"

  KW_REMOTE_PUBLIC_PATH="$(kw_env_get "$env" '.remote.public_path')"
  KW_REMOTE_PUBLIC_PATH="$(kw_expand_path "$KW_REMOTE_PUBLIC_PATH")"

  KW_REMOTE_DATABASE_PATH="$(kw_env_get "$env" '.remote.database_path')"
  KW_REMOTE_DATABASE_PATH="$(kw_expand_path "$KW_REMOTE_DATABASE_PATH")"

  KW_REMOTE_UPLOADS_PATH="$(kw_env_get "$env" '.remote.uploads_path')"
  KW_REMOTE_UPLOADS_PATH="$(kw_expand_path "$KW_REMOTE_UPLOADS_PATH")"

  KW_REMOTE_WP_PATH="$(kw_env_get "$env" '.remote.wordpress_path')"
  KW_REMOTE_WP_PATH="$(kw_expand_path "$KW_REMOTE_WP_PATH")"

  KW_LOCAL_DATABASE_PATH="$(kw_env_get "$env" '.local.database_path')"
  KW_LOCAL_DATABASE_PATH="$(kw_absolute_path "$KW_LOCAL_DATABASE_PATH")"

  KW_LOCAL_UPLOADS_PATH="$(kw_env_get "$env" '.local.uploads_path')"
  KW_LOCAL_UPLOADS_PATH="$(kw_absolute_path "$KW_LOCAL_UPLOADS_PATH")"

  KW_SSH_OPTS=(
    -T
    -o StrictHostKeyChecking=accept-new
    -o BatchMode=yes
    -o "ConnectTimeout=${KW_SSH_CONNECT_TIMEOUT}"
  )

  KW_RSYNC_SSH="ssh -o StrictHostKeyChecking=accept-new -o BatchMode=yes -o ConnectTimeout=${KW_SSH_CONNECT_TIMEOUT}"
}
