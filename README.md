# Suivi d'incidents backbone

Application Laravel (Blade + Bootstrap 5 + MySQL) pour suivre les incidents d'un réseau backbone : déclaration, qualification, suivi, résolution et fiche hebdomadaire.

## Fonctionnalités

- **Incidents** : référence automatique (`INC-2026-00042`), sévérité P1/P2/P3, catégorie (coupure fibre, panne matérielle, énergie, configuration, saturation, sécurité), statut Ouvert → En cours → Résolu → Clos, rattachement à un site, un équipement ou une liaison, cause racine et solution obligatoires à la résolution.
- **Journal de suivi** : commentaires, interventions terrain et changements de statut tracés automatiquement.
- **Liste** filtrable (texte, statut, sévérité, site, période), paginée, **export CSV** compatible Excel.
- **Inventaire** : sites (POP, data centers, stations FH), équipements (routeurs, switchs, DWDM…), liaisons entre sites (fibre, FH, satellite).
- **Tableau de bord** : incidents par statut, actifs par sévérité, MTTR 30 jours, sites les plus touchés, P1 en cours.
- **Rapport hebdomadaire** imprimable : indicateurs de la semaine, répartitions, détail, incidents encore ouverts.
- **Rôles** : administrateur (gestion des utilisateurs, suppression d'incidents) et technicien NOC.

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Créer la base puis renseigner `DB_USERNAME` / `DB_PASSWORD` dans `.env` :

```sql
CREATE DATABASE suivi_incident CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

```bash
php artisan migrate --seed
php artisan serve
```

Ouvrir http://localhost:8000.

## Comptes de démonstration

| Rôle | E-mail | Mot de passe |
|---|---|---|
| Administrateur | admin@backbone.local | password |
| Technicien | tech1@backbone.local … tech4@backbone.local | password |

## Tests

```bash
php artisan test
```

Les tests tournent sur SQLite en mémoire (voir `phpunit.xml`).

## Programme d'apprentissage

Les fiches hebdomadaires pour reconstruire ce projet pas à pas sont dans [docs/fiches](docs/fiches/README.md).
