git fetch --all
git checkout --force origin/master

git submodule sync
git submodule update --init --recursive

cd public/js
js.thirdparty.bat
js.build.bat