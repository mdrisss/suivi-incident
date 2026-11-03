---
semaine: 1
titre: Cadrage métier et environnement
duree: 5 jours
competences: Métier NOC · Outils du développeur · Git · Modélisation
---

## Intentions pédagogiques

Cette semaine pose les fondations : **comprendre le besoin** avant de coder. Le piège classique d'un stagiaire est de foncer dans le code. Insistez sur le schéma : une bonne modélisation évite des semaines de corrections.

L'installation peut prendre du temps (droits, versions de PHP, mot de passe MySQL). C'est normal et formateur : laissez-le chercher, mais pas plus d'une demi-journée bloqué.

## Déroulé conseillé

| Jour | Activité |
|---|---|
| J1 | Présentation du NOC (idéalement, visite ou capture d'écran de l'outil de supervision réel). Installation des outils. |
| J2 | Création du projet, base MySQL, utilisateur dédié, `.env`, premier `migrate`. Premier commit. |
| J3 | Découverte de la structure Laravel : suivre une requête de la route à la vue (page d'accueil). |
| J4 | Modélisation sur papier. **Point intermédiaire de 15 min** pour corriger le schéma avant qu'il ne parte sur une mauvaise piste. |
| J5 | Finalisation du schéma, liste des écrans, point hebdomadaire. |

## Corrigé : le modèle de données attendu

```text
users (id, name, email, password, role, telephone)
  │1
  ├──N incidents.declare_par_id
  ├──N incidents.assigne_a_id     (nullable)
  └──N suivis.user_id

sites (id, code UNIQUE, nom, ville, type, adresse)
  │1
  ├──N equipements.site_id
  ├──N liaisons.site_a_id
  ├──N liaisons.site_b_id
  └──N incidents.site_id

equipements (id, site_id, hostname UNIQUE, type, constructeur, modele, ip_management, statut)
  └──1-N incidents.equipement_id  (nullable)

liaisons (id, code UNIQUE, site_a_id, site_b_id, type, capacite, operateur, statut)
  └──1-N incidents.liaison_id     (nullable)

incidents (id, reference UNIQUE, titre, description, severite, statut, categorie,
           site_id, equipement_id?, liaison_id?, declare_par_id, assigne_a_id?,
           debut_at, resolu_at?, impact?, cause_racine?, solution?)
  └──1-N suivis.incident_id

suivis (id, incident_id, user_id, type, message, created_at)
```

Points à vérifier sur le schéma de l'apprenant :

- **deux relations** entre `users` et `incidents` (déclarant / assigné) ;
- **deux relations** entre `sites` et `liaisons` (A / B) ;
- `equipement_id` et `liaison_id` **facultatifs** : un incident d'énergie concerne tout un site ;
- `site_id` sur l'incident même quand l'équipement est connu (dénormalisation assumée : simplifie filtres et statistiques) ;
- la **durée n'est pas stockée** : elle se calcule à partir de `debut_at` et `resolu_at`.

Liste d'écrans attendue (ou proche) : connexion, tableau de bord, liste des incidents, création / modification d'incident, détail d'un incident avec journal, sites, équipements, liaisons, rapport hebdomadaire, gestion des utilisateurs.

## Erreurs fréquentes

| Erreur | Comment la repérer | Piste à donner |
|---|---|---|
| `Access denied for user 'root'` | `php artisan migrate` échoue | Vérifier `DB_USERNAME` / `DB_PASSWORD` ; mot de passe avec `#` → le mettre entre guillemets |
| `could not find driver` | Erreur PDO | Extension `pdo_mysql` absente de PHP |
| `.env` commité | `git log --stat` | Le retirer : `git rm --cached .env` |
| Une table « incident_equipement » | Schéma | Un incident concerne **un** équipement au plus : pas de table pivot |
| Durée stockée en colonne | Schéma | Donnée calculable = risque d'incohérence |
| Statut sans liste fermée | Schéma | Préparer l'idée d'enum de la semaine 2 |

## Questions pour le point hebdomadaire

1. **Quelle différence entre P1 et P2 ? Donne un exemple réseau de chaque.**
   *Attendu :* P1 = coupure franche avec impact client (fibre coupée sans redondance, ville isolée). P2 = service dégradé ou redondance perdue (un des deux liens d'un anneau coupé).
2. **Comment calcule-t-on le MTTR ? Pourquoi un NOC le suit-il ?**
   *Attendu :* moyenne des durées début → rétablissement des incidents résolus. Mesure l'efficacité de l'équipe, sert d'engagement contractuel (SLA).
3. **Pourquoi ne pas utiliser `root` dans `.env` ?**
   *Attendu :* principe du moindre privilège ; si l'application est compromise, l'attaquant n'a accès qu'à sa base.
4. **Que se passe-t-il quand tu tapes une URL dans le navigateur, côté Laravel ?**
   *Attendu :* `public/index.php` → routes → contrôleur → (modèle) → vue → réponse HTML.
5. **Pourquoi un incident a-t-il deux liens vers `users` ?**
   *Attendu :* déclarant et technicien assigné sont deux rôles différents, souvent deux personnes différentes.

## Grille d'évaluation (sur 20)

| Critère | Points |
|---|---|
| Environnement fonctionnel (Laravel + MySQL, utilisateur dédié) | 4 |
| Git utilisé correctement (commits, `.env` exclu) | 2 |
| Schéma : 6 tables, attributs pertinents | 4 |
| Schéma : relations et cardinalités justes (doubles relations, nullables) | 4 |
| Liste des écrans cohérente avec le besoin | 2 |
| Compréhension métier à l'oral (P1/P2/P3, cycle de vie, MTTR) | 4 |

## Adapter le rythme

- **En avance** : lui faire lire le chapitre « Eloquent: Getting Started » de la documentation et écrire les migrations `sites` et `equipements`.
- **En difficulté** : fournir le schéma corrigé ci-dessus et lui demander de l'**expliquer** table par table à l'oral ; c'est la compréhension qui compte.
