# Usage

// enable service

sudo systemctl enable laravel-reverb.service 

sudo systemctl enable laravel-queue.service  

sudo systemctl enable laravel-schedule.service  

// start service

sudo systemctl start laravel-reverb.service  

sudo systemctl start laravel-queue.service  

sudo systemctl start laravel-schedule.service  

// stop service

sudo systemctl stop laravel-reverb.service  

sudo systemctl stop laravel-queue.service  

sudo systemctl stop laravel-schedule.service  

// check service health

sudo systemctl status laravel-reverb.service  

sudo systemctl status laravel-queue.service  

sudo systemctl status laravel-schedule.service  

# Debugging

// debugging the logs

sudo journalctl -u laravel-reverb.service  

sudo journalctl -u laravel-queue.service  

sudo journalctl -u laravel-schedule.service  
