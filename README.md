This CLI command uses Tinypng Magento 2 module with valid API key to compress jpeg,jpg,png images in pub media(excluding cache, tmp and .thumbs) directory.

Install.

instal and configure Tintypng Magento 2 module(https://marketplace.magento.com/tinify-magento2.html)
Copy or clone files to app/code
run: php bin/magento setup:upgrade
run: php bin/magento konatsu:tinypng:optimize-all