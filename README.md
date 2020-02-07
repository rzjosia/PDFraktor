# PDFraktor

PDFraktor est une application web en symfony 4 qui permet de découper un fichier pdf en plusieurs fichiers.

Pour cela il faut un motif pour séparer les pages. On utilise ici un QRcode avec les mot clé **_"intercalary"_**

## Prérequis :
- symfony
- composer
- php 7.2 >
- zbar (apt install zbar-tools)
- node

## Configurations:

Modifiez dans le fichier **_.env_** la partie :

```dotenv
DATABASE_URL=mysql://user:password@127.0.0.1:3306/dbanme
```

Installation:

```shell script
composer install
yarn
symfony console make:migrations
symfony console doctrine:migrations:migrate
```

ou lancer tout avec

```shell script
make
```

## Lancer le serveur: 
```shell script
symfony serve
```

