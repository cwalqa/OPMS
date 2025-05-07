#!/bin/bash

# Laravel Permission Fix Script

echo "Setting file permissions to 644..."
find . -type f -exec chmod 644 {} \;

echo "Setting directory permissions to 755..."
find . -type d -exec chmod 755 {} \;

echo "Granting write access to storage and bootstrap/cache..."
chmod -R ug+rwx storage bootstrap/cache

# Change ownership to web server user (customize 'www-data' if needed)
echo "Changing ownership to www-data..."
chown -R www-data:www-data .

echo "Permissions have been set successfully."
