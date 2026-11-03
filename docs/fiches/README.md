# Fiches hebdomadaires — Projet « Suivi d'incidents backbone »

Programme de 8 semaines pour apprendre **Laravel**, **MySQL** et le **développement web** en construisant l'application de ce dépôt (qui sert de corrigé).

## Les PDF à distribuer

| Dossier | Contenu |
|---|---|
| [pdf/apprenant](pdf/apprenant) | Une fiche par semaine (cours, exercices MySQL, travail demandé, auto-évaluation) + le livret complet |
| [pdf/tuteur](pdf/tuteur) | Une fiche par semaine (déroulé, corrigés code et SQL, erreurs fréquentes, questions orales, grille sur 20) + le livret complet avec le guide du tuteur |

Donner chaque lundi la fiche apprenant de la semaine ; garder la fiche tuteur pour le point du vendredi.

## Programme

| Semaine | Thème |
|---|---|
| 1 | Cadrage métier et environnement |
| 2 | La base de données MySQL |
| 3 | Blade, Bootstrap et authentification |
| 4 | Inventaire réseau (CRUD) |
| 5 | Le cœur de l'application : les incidents |
| 6 | Journal de suivi, recherche et export |
| 7 | Tableau de bord, rapport hebdomadaire et rôles |
| 8 | Tests, finitions et soutenance |

## Modifier les fiches

Les sources sont en Markdown dans [apprenant](apprenant) et [tuteur](tuteur) (en-tête `semaine`, `titre`, `duree`, `competences`). La mise en page est dans [style.css](style.css).

Après modification, régénérer les PDF (Google Chrome requis) :

```bash
php docs/fiches/build.php
```
