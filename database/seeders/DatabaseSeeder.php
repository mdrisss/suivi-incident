<?php

namespace Database\Seeders;

use App\Enums\Categorie;
use App\Enums\Severite;
use App\Enums\StatutIncident;
use App\Enums\TypeSuivi;
use App\Models\Equipement;
use App\Models\Incident;
use App\Models\Liaison;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Pas de WithoutModelEvents : l'événement "created" de Incident génère la référence.
     */
    public function run(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Administrateur NOC',
            'email' => 'admin@backbone.local',
        ]);

        $techniciens = collect(['Awa Traoré', 'Moussa Diarra', 'Fatoumata Coulibaly', 'Ibrahim Keïta'])
            ->map(fn (string $nom, int $i) => User::factory()->create([
                'name' => $nom,
                'email' => 'tech'.($i + 1).'@backbone.local',
            ]));

        $equipe = $techniciens->concat([$admin]);

        $sites = collect([
            ['BKO-DC1', 'Data center Bamako ACI', 'Bamako', 'Data center'],
            ['BKO-POP2', 'POP Bamako Kalaban', 'Bamako', 'POP'],
            ['KTI-POP1', 'POP Kati', 'Kati', 'POP'],
            ['SGU-POP1', 'POP Ségou', 'Ségou', 'POP'],
            ['SKS-POP1', 'POP Sikasso', 'Sikasso', 'POP'],
            ['KYS-POP1', 'POP Kayes', 'Kayes', 'POP'],
            ['MPT-POP1', 'POP Mopti', 'Mopti', 'POP'],
            ['GAO-STA1', 'Station FH Gao', 'Gao', 'Station FH'],
        ])->mapWithKeys(fn (array $s) => [$s[0] => Site::create([
            'code' => $s[0], 'nom' => $s[1], 'ville' => $s[2], 'type' => $s[3],
        ])]);

        $equipements = collect();
        foreach ($sites as $code => $site) {
            $prefixe = strtolower(str_replace('-', '', $code));
            $ip = '10.10.'.$site->id.'.';

            $equipements->push(Equipement::create([
                'site_id' => $site->id, 'hostname' => "{$prefixe}-core-rtr01", 'type' => 'Routeur',
                'constructeur' => 'Cisco', 'modele' => 'ASR 9006', 'ip_management' => $ip.'1',
            ]));
            $equipements->push(Equipement::create([
                'site_id' => $site->id, 'hostname' => "{$prefixe}-agg-sw01", 'type' => 'Switch',
                'constructeur' => 'Huawei', 'modele' => 'S6730', 'ip_management' => $ip.'2',
            ]));
            if ($site->type !== 'Station FH') {
                $equipements->push(Equipement::create([
                    'site_id' => $site->id, 'hostname' => "{$prefixe}-dwdm01", 'type' => 'DWDM',
                    'constructeur' => 'Nokia', 'modele' => '1830 PSS', 'ip_management' => $ip.'3',
                ]));
            }
        }

        $liaisons = collect([
            ['FO-BKO-KTI', 'BKO-DC1', 'KTI-POP1', 'Fibre optique', '100G'],
            ['FO-BKO-BKO2', 'BKO-DC1', 'BKO-POP2', 'Fibre optique', '100G'],
            ['FO-BKO-SGU', 'BKO-POP2', 'SGU-POP1', 'Fibre optique', '10G'],
            ['FO-BKO-SKS', 'BKO-POP2', 'SKS-POP1', 'Fibre optique', '10G'],
            ['FO-KTI-KYS', 'KTI-POP1', 'KYS-POP1', 'Fibre optique', '10G'],
            ['FO-SGU-MPT', 'SGU-POP1', 'MPT-POP1', 'Fibre optique', '10G'],
            ['FH-MPT-GAO', 'MPT-POP1', 'GAO-STA1', 'Faisceau hertzien', '1G'],
        ])->map(fn (array $l) => Liaison::create([
            'code' => $l[0], 'site_a_id' => $sites[$l[1]]->id, 'site_b_id' => $sites[$l[2]]->id,
            'type' => $l[3], 'capacite' => $l[4], 'operateur' => 'Backbone national',
        ]));

        $scenarios = [
            [Categorie::CoupureFibre, 'Coupure fibre suite à des travaux de voirie', 'Câble sectionné par une pelleteuse.', 'Épissurage de la fibre par l\'équipe terrain.'],
            [Categorie::PanneMaterielle, 'Carte de ligne en défaut', 'Carte 10GE défectueuse.', 'Remplacement de la carte (RMA).'],
            [Categorie::Energie, 'Coupure secteur prolongée, batteries épuisées', 'Panne du groupe électrogène.', 'Réparation du groupe et recharge des batteries.'],
            [Categorie::Configuration, 'Perte des sessions BGP après un changement', 'Erreur dans la route-map appliquée.', 'Rollback de la configuration.'],
            [Categorie::Saturation, 'Saturation du lien aux heures de pointe', 'Trafic supérieur à 95 % de la capacité.', 'Activation d\'un lien de délestage.'],
            [Categorie::Securite, 'Attaque DDoS sur un préfixe client', 'Volumétrie UDP anormale.', 'Blackholing et filtrage en amont.'],
        ];

        for ($i = 0; $i < 45; $i++) {
            [$categorie, $titre, $cause, $solution] = fake()->randomElement($scenarios);
            $liaison = $categorie === Categorie::CoupureFibre ? $liaisons->random() : null;
            $equipement = $liaison ? null : $equipements->random();
            $debut = fake()->dateTimeBetween('-6 weeks');
            $statut = $i < 8
                ? fake()->randomElement(StatutIncident::actifs())
                : fake()->randomElement([StatutIncident::Resolu, StatutIncident::Clos]);
            $termine = $statut->estTermine();

            $incident = Incident::create([
                'titre' => $titre,
                'description' => "Alarme remontée par la supervision : {$titre}.",
                'severite' => fake()->randomElement(Severite::cases()),
                'statut' => $statut,
                'categorie' => $categorie,
                'site_id' => $liaison?->site_a_id ?? $equipement->site_id,
                'equipement_id' => $equipement?->id,
                'liaison_id' => $liaison?->id,
                'declare_par_id' => $equipe->random()->id,
                'assigne_a_id' => $techniciens->random()->id,
                'debut_at' => $debut,
                'resolu_at' => $termine ? (clone $debut)->modify('+'.fake()->numberBetween(20, 1800).' minutes') : null,
                'impact' => fake()->randomElement([
                    'Clients entreprises de la zone', 'Trafic mobile 4G de la région',
                    'Aucun impact (redondance active)', 'Capacité internationale dégradée',
                ]),
                'cause_racine' => $termine ? $cause : null,
                'solution' => $termine ? $solution : null,
            ]);

            $incident->suivis()->create([
                'user_id' => $incident->declare_par_id,
                'type' => TypeSuivi::Commentaire,
                'message' => 'Incident déclaré depuis la supervision.',
            ]);

            if ($termine) {
                $incident->suivis()->create([
                    'user_id' => $incident->assigne_a_id,
                    'type' => TypeSuivi::ChangementStatut,
                    'message' => "Statut passé à « {$statut->label()} ». {$solution}",
                ]);
            }
        }
    }
}
