# P8 Améliorez une application existante de ToDo & Co

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/f7e595f098b243b69506e1984109f0a2)](https://app.codacy.com/gh/Hadidi09/p8_Am-liorez-une-application-existante-de-ToDo-Co/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

1. Clonez le projet

   `git clone https://github.com/Hadidi09/p8_Am-liorez-une-application-existante-de-ToDo-Co.git`

2. Exécutez la commande composer install
3. > Renseignez les identifiants de votre base de données MYSQL dans le fichier .env.local comme ceci : Créez un fichier **.env.local** à la racine de votre projet. Dans ce fichier .env.local, ajoutez la ligne suivante pour configurer la connexion à votre base de données MySQL :

   `DATABASE_URL="mysql://USER:PASSWORD@HOST:PORT/DB_NAME?serverVersion=5.1.36&charset=utf8mb4"`

4. Remplacez les éléments suivants par vos informations:

   > USER : Nom d'utilisateur de votre base de données

   > PASSWORD : Mot de passe de votre base de données

   > HOST : Adresse de votre serveur MySQL (127.0.0.1 pour localhost)

   > PORT : Port de votre serveur MySQL (3306)

   > DB_NAME : Nom de votre base de données 6.

   Dans le fichier **.env** existant à la racine du projet, assurez vous que la variable d'environnement suivante est présente:

   `DATABASE_URL=${DATABASE_URL}`

5. Exécuter la commande:

   `php bin/console doctrine:database:create`

6. Pour créez les tables
   Exécuter la commande:

   `php bin/console make:migration`

   puis la commande :

   `php bin/console doctrine:migrations:migrate`

7. Pour créer des données rapidement, utilisez les fixtures:

   `php bin/console doctrine:fixtures:load`

8. Lancez votre projet avec la commande
   `symfony serve`

## Lancer des tests

1. `Créez un fichier **.env.test.local** à la racine de votre projet.  Dans ce fichier .env.test.local, ajoutez la même ligne que pour **.env.local**  pour configurer la connexion à votre base de données MySQL de test :`

   `DATABASE_URL="mysql://USER:PASSWORD@HOST:PORT/DB_NAME?serverVersion=5.1.36&charset=utf8mb4"`

2. Exécuter la commande:

   `php bin/console doctrine:database:create --env=test`

3. Pour créez les tables
   Exécuter la commande:

   `php bin/console make:migration --env=test`

   Puis la commande :

   `php bin/console doctrine:migrations:migrate --env=test`

4. Pour créer des données rapidement, utilisez les fixtures:

   `php bin/console doctrine:fixtures:load --env=test`

5. Exécuter la commande pour le resultat des tests :

   `php bin/phpunit`

   et pour voir le taux de couverture du code

   `php bin/phpunit --coverage-text`
