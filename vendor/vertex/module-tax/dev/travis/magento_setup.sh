#!/usr/bin/env bash

# Copyright © Vertex.  All rights reserved https://www.vertexinc.com/
# Author: Mediotype https://www.mediotype.com/

set -e
set -v
trap '>&2 echo Error: Command \`$BASH_COMMAND\` on line $LINENO failed with exit code $?' ERR

mkdir artifacts
cd VertexTax
export VERTEX_VERSION=`composer config version`
composer archive --format=zip --dir=../artifacts
cd ../artifacts
export ARTIFACTS_DIR=`pwd`
cd ..
git clone -b $MAGENTO_BRANCH --single-branch --depth=5 https://github.com/magento/magento2.git
cd magento2
echo "Magento Version: `composer config version`, Branch: ${MAGENTO_BRANCH}"
export TRAVIS_BUILD_DIR=`pwd`

if [[ $MAGENTO_BRANCH != '2.0' ]]; then ./dev/travis/before_install.sh;
else
  sudo rm /etc/apt/sources.list.d/mongodb-3.4.list
  sudo apt-get update -qq
  sudo apt-get install -y -qq postfix
  if [ "$CASHER_DIR" ]; then
       if [ -x $HOME/.cache/bin/composer ]; then
         $HOME/.cache/bin/composer self-update; echo '';
       else
         mkdir -p $HOME/.cache/bin;
         curl --connect-timeout 30 -sS https://getcomposer.org/installer | php -- --install-dir $HOME/.cache/bin/ --filename composer;
       fi
     fi
  export PATH="$HOME/.cache/bin:$PATH"
fi

composer config repositories.artifacts "{\"type\":\"artifact\",\"url\":\"${ARTIFACTS_DIR}\"}"
composer install --no-interaction --prefer-dist -v
composer require vertex/module-tax:$VERTEX_VERSION

if [[ $MAGENTO_BRANCH != '2.0' ]]; then ./dev/travis/before_script.sh;
else
  sudo service postfix stop
  smtp-sink -d "%d.%H.%M.%S" localhost:2500 1000 &
  echo -e '#!/usr/bin/env bash\nexit 0' | sudo tee /usr/sbin/sendmail
  echo 'sendmail_path = "/usr/sbin/sendmail -t -i "' \
    | sudo tee "/home/travis/.phpenv/versions/`php -i \
    | grep "PHP Version" \
    | head -n 1 \
    | grep -o -P '\d+\.\d+\.\d+.*'`/etc/conf.d/sendmail.ini"
  # Disable xDebug
  echo '' > ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/xdebug.ini
  # Install MySQL 5.6, create DB for integration tests
  if [ $TEST_SUITE = 'integration_part_1' ] || [ $TEST_SUITE = 'integration_part_2' ] || [ $TEST_SUITE = 'integration_integrity' ]; then
      mysql -u root -e 'SET @@global.sql_mode = NO_ENGINE_SUBSTITUTION; CREATE DATABASE magento_integration_tests;';
      mv dev/tests/integration/etc/install-config-mysql.travis.php.dist dev/tests/integration/etc/install-config-mysql.php;
  fi
  # Change memory_limit for travis
  echo 'memory_limit = -1' >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini
  phpenv rehash;
  composer install --no-interaction --prefer-dist
fi

if [[ $TEST_SUITE == "compile" ]]; then
    echo "Installing Magento"
    mysql -uroot -e 'CREATE DATABASE magento2;'
    php bin/magento setup:install -q \
        --admin-user="admin" \
        --admin-password="123123q" \
        --admin-email="admin@example.com" \
        --admin-firstname="John" \
        --admin-lastname="Doe";
fi

cd ..
rm -rf VertexTax
