# Test PHP(Json to Csv)

# Description
========================
 Former 2 deux CSV distincts, à partir du fichier sample_data_test.json localisé à la racine du projet :
    teams.csv
    team_members.csv

# Requirements
==============
  * PHP ^7.4
  * Symfony 5.4
  * composer

# Installation
==============
extracter le fichier zip symfony_json_to_csv

# Usage
=======
``` cd symfony_json_to_csv ```

### Avec Windows 
Demarer le serveur Symfony par la commande suivante
``` php -S 127.0.0.1:8000 -t public ```

après lancer le projet à l'url :
 ``` http://127.0.0.1:8000/teams ```

### Avec Linux:
Configurer vhost (DocumentRoot,Directory) pour pointer sur le dossier public du projet
```
  DocumentRoot /var/www/public
 <Directory /var/www/public>
```

# Test
=======
Vous aurez deux bouttons:
- [ Teams ] =>  Recuperer teams.csv
- [ Team Members ] => Recuperer team_members.csv 

Description de l'application
----------------------------

**Contenu du projet :**

## Class Service TeamService:  
elle permet de : 
- [ ] Recuperer le contenu json du fichier sample_data_test
- [ ] Transformer les données Json en tableau associatif.
- [ ] Construire un tableau associatif team personnalisé
- [ ] Construire à partir du tableau team deux chaines format CSV  pour team et team_members respectivement.
- [ ] utiliser le fichier ```mapping.csv```  dans le traitement du fichier team_members.csv

## Class Controller TeamsController:
elle contient : 
- [ ] action index : elle renvoie une vue twig avec deux bouttons
- [ ] action getCSV : elle recupere les Données CSV et renvoie une reponse (donlowad du fichier )

## Class Service RequestEvent:
- [ ] Definir un alias pour le service TeamService dans ```service.yaml ```
```
  teamRequestListener:
    alias: App\Service\TeamsService
    public: true
```
- [ ] Elle ecoute un evenement(``onKernelRequest ``  de ``RequestEvent``)
- [ ] Si la route est ``app_teams``, lancer le traitement de la class TeamService