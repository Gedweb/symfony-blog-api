#!/bin/bash

until mysql -uroot -proot mysql -e 'select 1' 1>/dev/null 2>&1; do
        sleep 1
done
