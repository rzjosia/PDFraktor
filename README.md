# PDFraktor

PDFraktor est une application web en symfony 4 qui permet de découper un fichier pdf en plusieurs fichiers.

Pour cela il faut un motif pour séparer les pages. On utilise ici un QRcode avec les mot clé **_"intercalary"_**

## Prérequis :
- symfony
- composer
- php 7.2 >
- zbar (apt install zbar-tools)

Modifiez dans le fichier **_.env_** la partie :

## Configurations:
```dotenv
DATABASE_URL=mysql://user:password@127.0.0.1:3306/dbanme
```

Une fois tous les composants installé, vous pourvez lancer la commande suivante:

Migration de la base de données:
```shell script
symfony console make:migrations
```

```shell script
symfony console migrations:migrate
```

```shell script
symfony console doctrine:migrations:migrate
```

## Lancer le serveur: 
```shell script
symfony serve
```

