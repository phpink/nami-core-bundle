php -r "readfile('https://getcomposer.org/installer');" | php;
php composer.phar install;
php app/console doctrine:database:create;
php app/console doctrine:schema:update --force;
php app/console doctrine:fixtures:load --no-interaction;
php app/console assets:install;
php app/console assetic:dump;