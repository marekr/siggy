php artisan down
git fetch --all
git checkout --force origin/master

git submodule sync
git submodule update --init --recursive

call composer install --no-dev
php artisan migrate
php artisan assets:compile

php artisan optimize
php artisan route:cache
php artisan api:cache
php artisan config:cache
php artisan cache:clear
php artisan up