#!/bin/bash
GLOBAL_OPTIONS="--exclude=*~ --exclude-dir=password_compat --color --exclude=find_bad_code.sh"

grep $GLOBAL_OPTIONS -ri ' == ' *
grep $GLOBAL_OPTIONS -ri ' != ' *
grep $GLOBAL_OPTIONS -ri 'print' *
grep $GLOBAL_OPTIONS -ri 'var_dump' *

