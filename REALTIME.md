## For Ubuntu/Debian-based distributions:

$ sudo apt update
$ sudo apt install supervisor

For Amazon Linux 2 (using yum):

$ sudo yum install supervisor


## Checking Status 

$ sudo systemctl status supervisor

## Start Server  if not running 

$ sudo systemctl start supervisor

## Create a conf file 

$ sudo nano /etc/supervisor/conf.d/laravel-programs.conf


## Add this script ( Replace the real path in path/to/your/project/)

[program:reverb]
command=php /path/to/your/project/artisan reverb:start --port=8081
directory=/path/to/your/project
autostart=true
autorestart=true
stderr_logfile=/var/log/supervisor/reverb.err.log
stdout_logfile=/var/log/supervisor/reverb.out.log
user=www-data
environment=APP_ENV="production",APP_DEBUG="false"

[program:queue-worker]
command=php /path/to/your/project/artisan queue:work
directory=/path/to/your/project
autostart=true
autorestart=true
stderr_logfile=/var/log/supervisor/queue-worker.err.log
stdout_logfile=/var/log/supervisor/queue-worker.out.log
user=www-data
environment=APP_ENV="production",APP_DEBUG="false"

[program:schedule-run]
command=php /path/to/your/project/artisan schedule:run
directory=/path/to/your/project
autostart=true
autorestart=true
stderr_logfile=/var/log/supervisor/schedule-run.err.log
stdout_logfile=/var/log/supervisor/schedule-run.out.log
user=www-data
environment=APP_ENV="production",APP_DEBUG="false"


## Update Config

$ sudo supervisorctl reread
$ sudo supervisorctl update

## Start Realtime

$ sudo supervisorctl start all


## Checking Log

$ sudo tail -f /var/log/supervisord.log


## Note 

If you reboot the server, you have to restart the supervsior 

$ sudo supervisorctl start all
