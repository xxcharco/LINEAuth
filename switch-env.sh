#!/bin/bash

if [ "$1" = "ngrok" ]; then
    echo "Switching to NGROK environment..."
    cp .env.ngrok .env
    php artisan config:clear
    php artisan cache:clear
    echo "Now using NGROK environment"
elif [ "$1" = "prod" ]; then
    echo "Switching to production environment..."
    cp .env.production .env
    php artisan config:clear
    php artisan cache:clear
    echo "Now using production environment"
else
    echo "Please specify environment: ngrok or prod"
    echo "Usage: ./switch-env.sh [ngrok|prod]"
fi