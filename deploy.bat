git fetch --all
git checkout --force origin/master

git submodule sync
git submodule update --init --recursive

cd public/js
call js.thirdparty.bat
call js.build.bat
cd ../../