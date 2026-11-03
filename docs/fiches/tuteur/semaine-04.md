---
semaine: 4
titre: Inventaire réseau (CRUD)
duree: 5 jours
competences: CRUD · Validation · Formulaires · Requêtes efficaces (N+1)
---

## Intentions pédagogiques

Trois CRUD presque identiques : c'est voulu. Le **premier** (sites) s'apprend, le **deuxième** (équipements) se consolide, le **troisième** (liaisons) doit aller vite. Si le troisième prend autant de temps que le premier, c'est un signal à creuser.

Notion clé de la semaine : le **N+1**. Montrez-le concrètement en comptant les requêtes avant / après `with()`.

## Déroulé conseillé

| Jour | Activité |
|---|---|
| J1 | Resource controller Sites : liste et création. Validation. |
| J2 | Modification avec formulaire partagé, suppression protégée. Traduction `lang/fr/validation.php`. |
| J3 | Équipements : liste paginée, filtres, validation IP. Mise en évidence du N+1. |
| J4 | Liaisons. Composant `<x-bouton-supprimer>`. |
| J5 | Exercices SQL, point hebdomadaire. |

## Corrigé : fichiers de référence

- `app/Http/Controllers/SiteController.php`, `EquipementController.php`, `LiaisonController.php`
- `resources/views/sites/*`, `equipements/*`, `liaisons/*` (un seul `form.blade.php` par entité)
- `resources/views/components/bouton-supprimer.blade.php`, `champ-erreur.blade.php`
- `lang/fr/validation.php`

Suppression protégée d'un site :

```php
public function destroy(Site $site): RedirectResponse
{
    if ($site->equipements()->exists() || $site->incidents()->exists()) {
        return back()->with('error', 'Impossible de supprimer un site qui possède des équipements ou des incidents.');
    }
    $site->delete();
    return redirect()->route('sites.index')->with('success', 'Site supprimé.');
}
```

*À souligner :* la contrainte MySQL `restrict` protège déjà, mais l'utilisateur verrait une page d'erreur 500. Le contrôle applicatif donne un **message compréhensible**. Les deux niveaux sont complémentaires.

Validation d'une liaison :

```php
'site_b_id' => ['required', 'exists:sites,id', 'different:site_a_id'],
```

## Corrigé des exercices SQL

```sql
-- 1. Ce que génère Site::withCount('equipements')
SELECT sites.*,
       (SELECT COUNT(*) FROM equipements WHERE sites.id = equipements.site_id) AS equipements_count
FROM sites;
-- Variante équivalente :
SELECT s.*, COUNT(e.id) AS equipements_count
FROM sites s LEFT JOIN equipements e ON e.site_id = s.id
GROUP BY s.id;

-- 2. JOIN exclut les sites sans équipement ; LEFT JOIN les garde (avec des NULL / un compte à 0).
--    Attention : COUNT(*) avec LEFT JOIN donne 1 au lieu de 0 → compter COUNT(e.id).

-- 3. Liaisons touchant BKO-DC1
SELECT l.code
FROM liaisons l
JOIN sites s ON s.code = 'BKO-DC1'
WHERE l.site_a_id = s.id OR l.site_b_id = s.id;

-- 4. Mise à jour directe
UPDATE equipements SET statut = 'maintenance' WHERE hostname = 'bkodc1-core-rtr01';
```

## Erreurs fréquentes

| Erreur | Symptôme | Piste |
|---|---|---|
| `unique` sans `ignore` | Impossible d'enregistrer un site modifié | `Rule::unique('sites')->ignore($site)` |
| `old()` oublié | Les champs se vident après une erreur | `old('champ', $modele->champ)` |
| `@method('PUT')` oublié | « The POST method is not supported » | Formulaire de modification |
| N+1 | Page lente, des dizaines de requêtes identiques | `with('site')` |
| `withQueryString()` oublié | Les filtres disparaissent en page 2 | Sur le paginator |
| `COUNT(*)` avec LEFT JOIN | Les sites vides comptent 1 | `COUNT(e.id)` |

## Questions pour le point hebdomadaire

1. **Montre-moi combien de requêtes exécute la liste des équipements, avec et sans `with('site')`.**
   *Attendu :* N+1 vs 2 (ou 3 avec `withCount`). Il doit savoir le prouver (log, Debugbar).
2. **Pourquoi valider côté serveur si le champ HTML a déjà `required` ?**
   *Attendu :* le HTML se contourne en 2 secondes (outils du navigateur, curl). Seul le serveur fait foi.
3. **Que fait le route model binding si l'id n'existe pas ?**
   *Attendu :* 404 automatiquement.
4. **La contrainte MySQL `restrict` suffit-elle pour la suppression d'un site ?**
   *Attendu :* elle protège les données, mais produit une erreur 500 ; le contrôle dans le contrôleur affiche un message clair.
5. **Comment as-tu évité de dupliquer le formulaire ?**
   *Attendu :* un seul `form.blade.php`, `$modele->exists` pour choisir l'action et la méthode.

## Grille d'évaluation (sur 20)

| Critère | Points |
|---|---|
| CRUD Sites complet, suppression protégée avec message | 4 |
| CRUD Équipements : pagination, filtres, validation IP | 4 |
| CRUD Liaisons : sites différents, badges de statut | 3 |
| Formulaires : `old()`, erreurs inline, un seul formulaire par entité | 3 |
| Pas de N+1 (vérifié) | 2 |
| Exercices SQL | 2 |
| Commits réguliers et lisibles | 2 |

## Adapter le rythme

- **En avance** : page de détail d'un site (équipements, liaisons, derniers incidents) ou import CSV d'équipements.
- **En difficulté** : faire Sites en binôme avec lui (pair programming 1 h), puis il fait Équipements seul ; Liaisons peut glisser sur la semaine suivante.
