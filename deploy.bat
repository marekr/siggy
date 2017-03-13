git fetch --all
git checkout --force origin/master

git submodule sync
git submodule update --init --recursive

php artisan down
php artisan assets:compile
php artisan up