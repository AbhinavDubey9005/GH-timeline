#!/bin/bash
# Minimal change: demonstration edit
# This script should set up a CRON job to run cron.php every 5 minutes.
# You need to implement the CRON setup logic here.

CRON_JOB="*/5 * * * * php $(pwd)/cron.php >/dev/null 2>&1"
(crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -
echo "CRON job added: $CRON_JOB"
