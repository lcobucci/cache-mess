#!/bin/sh

set -euo pipefail

check_redis_connection()
{
  local host="${1}"
  local port="${2}"

  until nc -z -v -w30 ${host} ${port}; do
    sleep 1
  done
}

run_phpbench()
{
  vendor/bin/phpbench run --iterations=5 --revs=100 --report=aggregate --progress=dots
}

check_redis_connection ${REDIS_HOST} ${REDIS_PORT}
run_phpbench
