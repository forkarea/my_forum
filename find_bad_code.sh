#!/bin/bash
GLOBAL_OPTIONS="--exclude=*~ --exclude-dir=password_compat --color --exclude=find_bad_code.sh --exclude=php-cs-fixer"

grep $GLOBAL_OPTIONS -ri ' == ' *
grep $GLOBAL_OPTIONS -ri ' != ' *
grep $GLOBAL_OPTIONS -ri 'print' *
grep $GLOBAL_OPTIONS -ri 'var_dump' *

#http://danuxx.blogspot.com/2013/03/unauthorized-access-bypassing-php-strcmp.html
grep $GLOBAL_OPTIONS -ri 'strcmp' *

#can introduce some info to attackers and be used for reflected XSS if it is not escaped
grep $GLOBAL_OPTIONS -ri 'getMessage' * | grep --invert-match trigger_warning

