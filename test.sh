# Install gumbo

GUMBO_VERSION="0.10.1"

rm -f gumbo.zip
rm -rf gumbo-src

wget https://github.com/google/gumbo-parser/archive/v$GUMBO_VERSION.zip -O gumbo.zip
unzip gumbo.zip
mv gumbo-parser-$GUMBO_VERSION gumbo-src
cd gumbo-src

./autogen.sh
./configure
make
sudo make install

cd ../

# Install gumbo-php

phpize
./configure
make
sudo make install

phpenv config-add travis/gumbo.ini
