# GestionTachesSymfony
Projet PHP utilisant Symphony et Twig

# Mise en place

1. (CMD) git clone
2. (CODE) changer le DATABASE_URL du .env

php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate

4. (CMD) composer install
6. (CMD) symfony serve
7. (URL) 127.0.0.1 ( ou 127.0.0.1/login )

si il y a un problème allez sur {nom de domaine}/logout.

exemple :127.0.0.1/logout 





