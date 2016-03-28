php -r "readfile('https://getcomposer.org/installer');" | php;
php composer.phar install;
php app/console doctrine:database:create;
php app/console doctrine:schema:update --force;
php app/console doctrine:fixtures:load --no-interaction;
php app/console assets:install;
php app/console assetic:dump;
mkdir app/var; mkdir app/var/jwt;
openssl genrsa -out app/var/jwt/private.pem -aes256 4096;
openssl rsa -pubout -in app/var/jwt/private.pem -out app/var/jwt/public.pem
