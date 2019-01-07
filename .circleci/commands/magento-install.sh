#!/usr/bin/env bash

mgver="1.9.3.4"

if [[ -f ~/magento/source/app/etc/local.xml ]]; then
    echo "Magento $mgver appears already to be installed."
    exit 0
fi

echo "Downloading Magento $mgver"
mkdir -p ~/magento/source/
cd ~/magento/source/
curl -LSs "https://github.com/OpenMage/magento-mirror/archive/$mgver.tar.gz" | tar --strip-components=1 -xzf-
echo "Installing Magento $mgver"
php install.php \
    --admin_email admin@example.com \
    --admin_firstname ad \
    --admin_lastname min \
    --admin_password testing123 \
    --admin_username admin \
    --db_host 127.0.0.1 \
    --db_name circle_test \
    --db_pass ubuntu \
    --db_user ubuntu \
    --default_currency USD \
    --license_agreement_accepted yes \
    --locale en_US \
    --secure_base_url 'http://example.com/' \
    --skip_url_validation yes \
    --timezone America/New_York \
    --url 'http://example.com/' \
    --use_rewrites yes \
    --use_secure no \
    --use_secure_admin no

