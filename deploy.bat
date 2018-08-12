php artisan down
git fetch --all
git checkout --force origin/master

call composer install --no-dev
php artisan migrate

php artisan config:cache

call npm install
php artisan assets:compile

php artisan route:cache
php artisan api:cache
php artisan cache:clear
php artisan up