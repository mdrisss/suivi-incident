<?php

namespace Tests\Feature;

use App\Enums\StatutIncident;
use App\Enums\TypeSuivi;
use App\Models\Incident;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncidentTest extends TestCase
{
    use RefreshDatabase;

    public function test_les_invites_sont_rediriges_vers_la_connexion(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get(route('incidents.index'))->assertRedirect(route('login'));
    }

    public function test_un_utilisateur_peut_se_connecter(): void
    {
        $user = User::factory()->create();

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_toutes_les_pages_principales_s_affichent(): void
    {
        $admin = User::factory()->admin()->create();
        $incident = Incident::factory()->resolu()->create();

        $this->actingAs($admin);

        foreach (['dashboard', 'incidents.index', 'incidents.create', 'sites.index', 'equipements.index', 'liaisons.index', 'users.index', 'rapports.hebdomadaire'] as $route) {
            $this->get(route($route))->assertOk();
        }

        $this->get(route('incidents.show', $incident))->assertOk()->assertSee($incident->reference);
        $this->get(route('incidents.edit', $incident))->assertOk();
        $this->get(route('incidents.export'))->assertOk()->assertDownload();
    }

    public function test_la_creation_genere_une_reference_et_un_suivi(): void
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();

        $this->actingAs($user)->post(route('incidents.store'), [
            'titre' => 'Coupure fibre Bamako-Kati',
            'description' => 'Perte de lumière sur le lien.',
            'severite' => 'critique',
            'statut' => 'ouvert',
            'categorie' => 'coupure_fibre',
            'site_id' => $site->id,
            'debut_at' => '2026-10-26 08:30',
        ])->assertRedirect();

        $incident = Incident::sole();
        $this->assertSame(sprintf('INC-2026-%05d', $incident->id), $incident->reference);
        $this->assertTrue($incident->declarePar->is($user));
        $this->assertSame(1, $incident->suivis()->count());
    }

    public function test_un_incident_resolu_exige_cause_et_solution(): void
    {
        $user = User::factory()->create();
        $incident = Incident::factory()->create();

        $this->actingAs($user)->put(route('incidents.update', $incident), [
            ...$incident->only(['titre', 'description', 'site_id']),
            'severite' => $incident->severite->value,
            'categorie' => $incident->categorie->value,
            'debut_at' => $incident->debut_at->format('Y-m-d H:i'),
            'statut' => 'resolu',
        ])->assertSessionHasErrors(['cause_racine', 'solution']);
    }

    public function test_la_resolution_date_l_incident_et_trace_le_changement(): void
    {
        $user = User::factory()->create();
        $incident = Incident::factory()->create(['debut_at' => now()->subHours(3)]);

        $this->actingAs($user)->put(route('incidents.update', $incident), [
            ...$incident->only(['titre', 'description', 'site_id']),
            'severite' => $incident->severite->value,
            'categorie' => $incident->categorie->value,
            'debut_at' => $incident->debut_at->format('Y-m-d H:i'),
            'statut' => 'resolu',
            'cause_racine' => 'Carte défectueuse',
            'solution' => 'Carte remplacée',
        ])->assertRedirect(route('incidents.show', $incident));

        $incident->refresh();
        $this->assertSame(StatutIncident::Resolu, $incident->statut);
        $this->assertNotNull($incident->resolu_at);
        $this->assertTrue($incident->suivis()->where('type', TypeSuivi::ChangementStatut)->exists());
    }

    public function test_on_peut_ajouter_un_suivi(): void
    {
        $incident = Incident::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('incidents.suivis.store', $incident), ['type' => 'intervention', 'message' => 'Équipe envoyée sur site.'])
            ->assertRedirect();

        $this->assertSame('Équipe envoyée sur site.', $incident->suivis()->first()->message);
    }

    public function test_un_technicien_ne_gere_pas_les_utilisateurs_ni_ne_supprime(): void
    {
        $incident = Incident::factory()->create();

        $this->actingAs(User::factory()->create());

        $this->get(route('users.index'))->assertForbidden();
        $this->delete(route('incidents.destroy', $incident))->assertForbidden();
    }

    public function test_les_filtres_restreignent_la_liste(): void
    {
        $ouvert = Incident::factory()->create(['titre' => 'Lien sature']);
        $resolu = Incident::factory()->resolu()->create(['titre' => 'BGP down']);

        $this->actingAs(User::factory()->create())
            ->get(route('incidents.index', ['statut' => 'ouvert']))
            ->assertSee($ouvert->reference)
            ->assertDontSee($resolu->reference);
    }

    public function test_le_rapport_hebdomadaire_ne_compte_que_la_semaine_choisie(): void
    {
        $dansLaSemaine = Incident::factory()->create(['debut_at' => '2026-10-21 10:00']); // semaine 43
        $horsSemaine = Incident::factory()->resolu()->create([
            'debut_at' => '2026-10-10 10:00',
            'resolu_at' => '2026-10-10 12:00',
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('rapports.hebdomadaire', ['semaine' => '2026-W43']))
            ->assertOk()
            ->assertSee($dansLaSemaine->reference)
            ->assertDontSee($horsSemaine->titre);
    }
}
