---
titre: Guide du tuteur
---

Ce livret accompagne les 8 fiches apprenant. L'apprenant construit, semaine après semaine, l'application de suivi d'incidents backbone dont une version complète existe déjà : **le projet `suivi-incident` est le corrigé**.

## Objectifs du parcours

À la fin des 8 semaines, l'apprenant doit savoir :

- **MySQL** : modéliser une base relationnelle, écrire des requêtes avec jointures et agrégats, lire un `EXPLAIN` et choisir un index ;
- **Laravel** : migrations, Eloquent et relations, routes, contrôleurs, Blade, validation, autorisations, tests ;
- **Développement** : Git, sécurité web de base (CSRF, XSS, mass assignment), découpage du code, documentation, présentation de son travail.

## Contenu de chaque fiche tuteur

| Rubrique | Usage |
|---|---|
| Intentions pédagogiques | Ce qui compte vraiment cette semaine |
| Déroulé conseillé | Un découpage jour par jour, à adapter |
| Corrigé | Fichiers de référence dans le projet, extraits de code, réponses SQL |
| Erreurs fréquentes | Symptômes et pistes à donner, sans donner la solution |
| Questions pour le point | Questions orales avec la réponse attendue |
| Grille d'évaluation | Notation sur 20 |
| Adapter le rythme | Prolongements si l'apprenant est en avance, allègements s'il est en difficulté |

## Le point hebdomadaire (30 minutes, chaque vendredi)

1. **Démonstration** (10 min) : l'apprenant montre ce qui fonctionne, dans le navigateur et dans MySQL.
2. **Revue de code** (10 min) : parcourir ensemble un ou deux fichiers et les commits de la semaine.
3. **Questions** (5 min) : 2 ou 3 questions de la fiche ; l'apprenant répond sans ses notes.
4. **Bilan** (5 min) : auto-évaluation de l'apprenant comparée à votre grille, objectifs reportés.

## Conseils d'accompagnement

- **Ne donnez pas le corrigé** d'un bloc ; montrez au plus un fichier pour débloquer un point précis, puis demandez à l'apprenant de le réexpliquer.
- **Faites verbaliser** : « Qu'est-ce que tu as essayé ? Que dit exactement le message d'erreur ? ». C'est la compétence la plus utile à transmettre.
- **Reliez toujours Eloquent et SQL** : « Quelle requête cette ligne envoie-t-elle à MySQL ? ».
- **Valorisez les commits réguliers** plus que la quantité de code.
- Une semaine non terminée n'est pas un échec : reportez ce qui manque en début de semaine suivante. Les rubriques « Adapter le rythme » indiquent ce qui peut être allégé.

## Comptes de démonstration du corrigé

`php artisan migrate --seed` crée un administrateur `admin@backbone.local` et quatre techniciens `tech1@backbone.local` à `tech4@backbone.local`, tous avec le mot de passe `password`.
