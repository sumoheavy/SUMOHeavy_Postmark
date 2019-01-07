#!/usr/bin/env bash

repo=~/magento

echo 'Setting up composer'
cd "$repo"/build
cp "$repo/.circleci/composer.json" .
composer --global --no-interaction config repositories.this path "$repo"
# Firegento has to come last
composer --global --no-interaction config repositories.firegento composer https://packages.firegento.com
composer --no-interaction --prefer-source update

echo 'Removing base url from database'
mysql --user ubuntu --password ubuntu -e 'DELETE FROM core_config_data WHERE path LIKE "web/%secure/base_url"\g' circle_test

echo 'Configuring Magento for testing'
cp "$repo"/.circleci/local.xml.phpunit app/etc/
cp "$repo"/.circleci/phpunit.xml.dist .

echo 'Copying module files'
cp -R "$repo"/app/code/community/SUMOHeavy app/code/community/
cp "$repo"/app/etc/modules/SUMOHeavy_Postmark.xml app/etc/modules/
cp -R "$repo"/lib/SUMOHeavy lib/
