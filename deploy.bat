php artisan down
git fetch --all
git checkout --force origin/master

git submodule sync
git submodule update --init --recursive

call composer install
php artisan assets:compile
php artisan up