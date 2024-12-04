# Usage 

sudo systemctl enable laravel-reverb.service
sudo systemctl enable laravel-queue.service
sudo systemctl enable laravel-schedule.service

sudo systemctl start laravel-reverb.service
sudo systemctl start laravel-queue.service
sudo systemctl start laravel-schedule.service

sudo systemctl stop laravel-reverb.service
sudo systemctl stop laravel-queue.service
sudo systemctl stop laravel-schedule.service

sudo systemctl status laravel-reverb.service
sudo systemctl status laravel-queue.service
sudo systemctl status laravel-schedule.service


# Debugging

sudo journalctl -u laravel-reverb.service
sudo journalctl -u laravel-queue.service
sudo journalctl -u laravel-schedule.service
