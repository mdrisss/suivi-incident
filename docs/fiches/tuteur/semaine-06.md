---
semaine: 6
titre: Journal de suivi, recherche et export
duree: 5 jours
competences: Relations · Query scopes · Pagination · Index MySQL · Export CSV
---

## Intentions pédagogiques

L'application devient utilisable au quotidien. Deux notions structurantes : le **principe DRY** (un seul scope pour la liste et l'export) et la **performance MySQL** avec `EXPLAIN`. C'est souvent la première fois qu'un stagiaire « voit » ce que fait la base de données : prenez le temps.

## Déroulé conseillé

| Jour | Activité |
|---|---|
| J1 | Modèle `Suivi`, formulaire et timeline sur la page de détail. |
| J2 | Entrées automatiques (création, changement de statut). |
| J3 | Scope `filtrer`, formulaire de filtres, pagination avec `withQueryString`. |
| J4 | Export CSV. `EXPLAIN` et index. |
| J5 | Exercices SQL, point hebdomadaire. |

## Corrigé : fichiers de référence

- `app/Http/Controllers/SuiviController.php`
- `app/Models/Incident.php` (`suivis`, `scopeActifs`, `scopeFiltrer`)
- `app/Http/Controllers/IncidentController.php` (`index`, `update`, `export`)
- `resources/views/incidents/index.blade.php`, `show.blade.php`

Interdire l'envoi manuel d'un « changement de statut » :

```php
'type' => ['required', Rule::enum(TypeSuivi::class)->only([TypeSuivi::Commentaire, TypeSuivi::Intervention])],
```

Détection du changement de statut :

```php
$ancienStatut = $incident->statut;
$incident->update(...);

if ($incident->statut !== $ancienStatut) {
    $incident->suivis()->create([...]);
}
```

## Corrigé des exercices SQL

```sql
-- 1. EXPLAIN
EXPLAIN SELECT * FROM incidents WHERE statut = 'ouvert';
-- type = ref, key = incidents_statut_index → index utilisé
EXPLAIN SELECT * FROM incidents WHERE categorie = 'energie';
-- type = ALL, key = NULL → parcours complet : categorie n'est pas indexée

-- 2. Recherche
SELECT reference, titre FROM incidents WHERE titre LIKE '%fibre%';

-- 3. Nombre de suivis par incident
SELECT i.reference, COUNT(s.id) AS nb_suivis
FROM incidents i
LEFT JOIN suivis s ON s.incident_id = i.id
GROUP BY i.id, i.reference
ORDER BY nb_suivis DESC;

-- 4. 5 derniers suivis
SELECT s.created_at, i.reference, u.name, s.type, s.message
FROM suivis s
JOIN incidents i ON i.id = s.incident_id
JOIN users u ON u.id = s.user_id
ORDER BY s.created_at DESC
LIMIT 5;

-- 5. LIMIT 15 OFFSET 30 = 3e page de 15 éléments
```

> Sur la petite base de démo, MySQL peut choisir de **ne pas** utiliser l'index même s'il existe (lire 45 lignes est plus rapide). Si c'est le cas, c'est une excellente occasion d'expliquer que l'optimiseur décide selon le volume.

## Erreurs fréquentes

| Erreur | Symptôme | Piste |
|---|---|---|
| `orWhere` non regroupé | Le filtre statut ne marche plus quand on cherche un texte | Fonction englobante ; montrer `toRawSql()` |
| Filtrage dupliqué dans l'export | Export et liste divergent | Réutiliser le scope |
| Pas de BOM | Accents cassés dans Excel | `"\xEF\xBB\xBF"` |
| Séparateur `,` | Tout dans une colonne sous Excel FR | Séparateur `;` |
| Suivis non triés | Ordre aléatoire | `->latest()` dans la relation |
| `$incident->statut` relu après `update` | Changement jamais détecté | Mémoriser avant la mise à jour |

## Questions pour le point hebdomadaire

1. **Montre-moi le SQL généré par ton scope avec une recherche texte et un statut.**
   *Attendu :* `... WHERE (reference LIKE ? OR titre LIKE ?) AND statut = ?` avec les parenthèses.
2. **Pourquoi l'export utilise-t-il le même scope que la liste ?**
   *Attendu :* une seule source de vérité ; corriger un bug une fois le corrige partout.
3. **Lis-moi ce `EXPLAIN`. Faut-il ajouter un index sur `categorie` ?**
   *Attendu :* lecture de `type` / `key` / `rows`. Index utile seulement si on filtre souvent par catégorie et que la table grossit.
4. **Pourquoi le journal enregistre-t-il automatiquement les changements de statut ?**
   *Attendu :* traçabilité ; on ne peut pas compter sur l'humain pour le noter à chaque fois.
5. **Que fait `streamDownload` par rapport à écrire un fichier puis le télécharger ?**
   *Attendu :* envoie au fil de l'eau, pas de fichier temporaire à nettoyer.

## Grille d'évaluation (sur 20)

| Critère | Points |
|---|---|
| Journal de suivi : ajout et timeline | 3 |
| Entrées automatiques (création, changement de statut) | 3 |
| Filtres combinables, regroupement correct du `orWhere` | 3 |
| Pagination qui conserve les filtres | 2 |
| Export CSV lisible dans Excel, même scope que la liste | 3 |
| Analyse `EXPLAIN` comprise | 3 |
| Exercices SQL | 3 |

## Adapter le rythme

- **En avance** : notification e-mail à l'assignation (Mailable + driver `log`), ou tri de la liste par colonne.
- **En difficulté** : limiter les filtres à statut + site ; l'export CSV peut être repoussé en semaine 8.
