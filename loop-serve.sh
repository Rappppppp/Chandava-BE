while true; do
    php artisan serve --port=5175
    echo "Server crashed with exit code $?. Restarting..."
    sleep 1
done
