---
semaine: 7
titre: Tableau de bord, rapport hebdomadaire et rôles
duree: 5 jours
competences: Agrégations SQL · Collections · Dates ISO · Autorisations
---

## Intentions pédagogiques

L'apprenant produit de la **valeur pour le management** : indicateurs et fiche hebdomadaire. C'est le moment de vérifier la **justesse des chiffres** : faites recalculer un MTTR à la main, c'est souvent là qu'on découvre des erreurs de période ou de filtre.

Côté sécurité : la leçon est « **cacher n'est pas protéger** ». Faites-lui tester l'accès direct aux URL avec un compte technicien.

## Déroulé conseillé

| Jour | Activité |
|---|---|
| J1 | Tableau de bord : cartes par statut, sévérités, derniers incidents. |
| J2 | MTTR, top sites, P1 en cours. Vérification à la main. |
| J3 | Rapport hebdomadaire : sélection de semaine, indicateurs, répartitions. |
| J4 | Gate `admin`, gestion des utilisateurs, protections. |
| J5 | Exercices SQL, impression du rapport, point hebdomadaire. |

## Corrigé : fichiers de référence

- `app/Http/Controllers/DashboardController.php`, `RapportController.php`, `UserController.php`
- `app/Providers/AppServiceProvider.php` (Gate `admin`)
- `resources/views/dashboard.blade.php`, `rapports/hebdomadaire.blade.php`, `users/*`

Incidents encore ouverts en fin de semaine :

```php
Incident::query()
    ->where('debut_at', '<=', $fin)
    ->where(fn ($q) => $q->whereNull('resolu_at')->orWhere('resolu_at', '>', $fin))
    ->get();
```

Protection à trois niveaux :

```php
Gate::define('admin', fn (User $user) => $user->estAdmin());                     // définition
Route::resource('users', UserController::class)->middleware('can:admin');        // route
Gate::authorize('admin');                                                         // IncidentController::destroy
```

## Corrigé des exercices SQL

```sql
-- 1. Par statut
SELECT statut, COUNT(*) AS total FROM incidents GROUP BY statut;

-- 2. MTTR global (minutes)
SELECT ROUND(AVG(TIMESTAMPDIFF(MINUTE, debut_at, resolu_at))) AS mttr_min
FROM incidents WHERE resolu_at IS NOT NULL;

-- 3. MTTR par catégorie
SELECT categorie, COUNT(*) AS nb,
       ROUND(AVG(TIMESTAMPDIFF(MINUTE, debut_at, resolu_at))) AS mttr_min
FROM incidents WHERE resolu_at IS NOT NULL
GROUP BY categorie ORDER BY mttr_min DESC;

-- 4. Par semaine ISO (mode 3 = lundi, semaine ISO)
SELECT YEARWEEK(debut_at, 3) AS semaine, COUNT(*) AS total
FROM incidents GROUP BY semaine ORDER BY semaine;

-- 5. Top 3 sites en P1 sur 30 jours
SELECT s.code, s.nom, COUNT(*) AS nb_p1
FROM incidents i JOIN sites s ON s.id = i.site_id
WHERE i.severite = 'critique' AND i.debut_at >= NOW() - INTERVAL 30 DAY
GROUP BY s.id, s.code, s.nom
ORDER BY nb_p1 DESC LIMIT 3;
```

**Exercice 6** : les deux MTTR diffèrent normalement. Le tableau de bord ne prend que les incidents **résolus dans les 30 derniers jours** ; la requête 2 prend **tous** les résolus. Attendu : qu'il trouve lui-même l'explication. C'est la leçon principale : **un indicateur n'a de sens qu'avec sa période et son périmètre**.

## Erreurs fréquentes

| Erreur | Symptôme | Piste |
|---|---|---|
| `endOfWeek()` sans `copy()` | Début et fin identiques, rapport vide | Carbon est mutable |
| Incidents ouverts inclus dans le MTTR | MTTR qui augmente en continu | Ne prendre que `resolu_at` non nul |
| Bouton caché sans protection serveur | Un technicien supprime via une requête forgée | `Gate::authorize` / middleware `can` |
| `YEARWEEK` sans mode | Semaines décalées le dimanche | `YEARWEEK(date, 3)` |
| SQL MySQL dans le contrôleur | Les tests SQLite plantent | Calculer en PHP ou isoler le SQL spécifique |
| Admin se supprime lui-même | Plus aucun admin | Contrôle `$user->is($request->user())` |

## Questions pour le point hebdomadaire

1. **Calcule à la main le MTTR de 3 incidents de ton rapport et compare avec l'écran.**
   *Attendu :* identique à la minute près.
2. **Pourquoi ton MTTR SQL global diffère-t-il de celui du tableau de bord ?**
   *Attendu :* période différente (30 derniers jours vs tout l'historique).
3. **Un technicien voit-il le bouton Supprimer ? Peut-il quand même supprimer ? Prouve-le.**
   *Attendu :* il ne le voit pas, et une requête DELETE forgée renvoie 403.
4. **Quand préférer un calcul SQL à une collection ?**
   *Attendu :* gros volumes → SQL ; petits volumes déjà chargés → collection, plus lisible.
5. **Comment est définie la semaine 1 en ISO ?**
   *Attendu :* la semaine qui contient le premier jeudi de l'année, du lundi au dimanche.

## Grille d'évaluation (sur 20)

| Critère | Points |
|---|---|
| Tableau de bord complet et juste | 4 |
| MTTR correct (vérifié à la main) | 3 |
| Rapport hebdomadaire : bornes de semaine et « encore ouverts » justes | 4 |
| Rapport imprimable et lisible | 2 |
| Autorisations protégées côté serveur, gestion des utilisateurs | 4 |
| Exercices SQL et analyse de l'écart de MTTR | 3 |

## Adapter le rythme

- **En avance** : graphique Chart.js des incidents par jour, ou envoi automatique du rapport chaque lundi (commande artisan + planificateur).
- **En difficulté** : fournir le calcul des bornes de semaine ; il se concentre sur les indicateurs et les autorisations.
