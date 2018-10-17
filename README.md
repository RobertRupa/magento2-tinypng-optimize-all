This CLI command uses Tinypng Magento 2 module with valid API key to compress jpeg,jpg,png images in pub media(excluding cache, tmp and .thumbs) directory.

Install.

1. Instal and configure Tintypng Magento 2 module(https://marketplace.magento.com/tinify-magento2.html)
2. Copy, clone files to app/code or use composer require robertrupa/tinypng-optimize-all
3. run: php bin/magento setup:upgrade
4. run: php bin/magento konatsu:tinypng:optimize-all
