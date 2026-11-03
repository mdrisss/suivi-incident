<?php

namespace App\Http\Controllers;

use App\Enums\Categorie;
use App\Enums\Severite;
use App\Enums\StatutIncident;
use App\Enums\TypeSuivi;
use App\Http\Requests\IncidentRequest;
use App\Models\Equipement;
use App\Models\Incident;
use App\Models\Liaison;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IncidentController extends Controller
{
    public function index(Request $request): View
    {
        $filtres = $request->only(['q', 'statut', 'severite', 'site_id', 'du', 'au']);

        $incidents = Incident::query()
            ->with(['site', 'assigneA'])
            ->filtrer($filtres)
            ->latest('debut_at')
            ->paginate(15)
            ->withQueryString();

        return view('incidents.index', [
            'incidents' => $incidents,
            'filtres' => $filtres,
            'sites' => Site::orderBy('code')->get(),
        ]);
    }

    public function create(): View
    {
        return view('incidents.form', $this->donneesFormulaire(new Incident([
            'statut' => StatutIncident::Ouvert,
            'debut_at' => now(),
        ])));
    }

    public function store(IncidentRequest $request): RedirectResponse
    {
        $donnees = $this->avecDateResolution($request->validated());
        $incident = Incident::create($donnees + ['declare_par_id' => $request->user()->id]);

        $incident->suivis()->create([
            'user_id' => $request->user()->id,
            'type' => TypeSuivi::Commentaire,
            'message' => 'Incident déclaré.',
        ]);

        return redirect()->route('incidents.show', $incident)
            ->with('success', "Incident {$incident->reference} créé.");
    }

    public function show(Incident $incident): View
    {
        $incident->load(['site', 'equipement', 'liaison.siteA', 'liaison.siteB', 'declarePar', 'assigneA', 'suivis.user']);

        return view('incidents.show', [
            'incident' => $incident,
            'typesSuivi' => [TypeSuivi::Commentaire, TypeSuivi::Intervention],
        ]);
    }

    public function edit(Incident $incident): View
    {
        return view('incidents.form', $this->donneesFormulaire($incident));
    }

    public function update(IncidentRequest $request, Incident $incident): RedirectResponse
    {
        $ancienStatut = $incident->statut;
        $incident->update($this->avecDateResolution($request->validated()));

        if ($incident->statut !== $ancienStatut) {
            $incident->suivis()->create([
                'user_id' => $request->user()->id,
                'type' => TypeSuivi::ChangementStatut,
                'message' => "Statut : {$ancienStatut->label()} → {$incident->statut->label()}",
            ]);
        }

        return redirect()->route('incidents.show', $incident)->with('success', 'Incident mis à jour.');
    }

    public function destroy(Incident $incident): RedirectResponse
    {
        Gate::authorize('admin');
        $incident->delete();

        return redirect()->route('incidents.index')->with('success', "Incident {$incident->reference} supprimé.");
    }

    public function export(Request $request): StreamedResponse
    {
        $incidents = Incident::query()
            ->with(['site', 'equipement', 'liaison', 'assigneA'])
            ->filtrer($request->only(['q', 'statut', 'severite', 'site_id', 'du', 'au']))
            ->latest('debut_at')
            ->get();

        return response()->streamDownload(function () use ($incidents) {
            $sortie = fopen('php://output', 'w');
            fwrite($sortie, "\xEF\xBB\xBF"); // BOM pour qu'Excel lise l'UTF-8
            fputcsv($sortie, ['Référence', 'Titre', 'Sévérité', 'Statut', 'Catégorie', 'Site', 'Équipement', 'Liaison', 'Assigné à', 'Début', 'Résolution', 'Durée (min)'], ';');

            foreach ($incidents as $incident) {
                fputcsv($sortie, [
                    $incident->reference,
                    $incident->titre,
                    $incident->severite->label(),
                    $incident->statut->label(),
                    $incident->categorie->label(),
                    $incident->site->code,
                    $incident->equipement?->hostname,
                    $incident->liaison?->code,
                    $incident->assigneA?->name,
                    $incident->debut_at->format('d/m/Y H:i'),
                    $incident->resolu_at?->format('d/m/Y H:i'),
                    $incident->dureeMinutes(),
                ], ';');
            }

            fclose($sortie);
        }, 'incidents-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Renseigne ou efface la date de résolution selon le statut choisi.
     *
     * @param  array<string, mixed>  $donnees
     * @return array<string, mixed>
     */
    private function avecDateResolution(array $donnees): array
    {
        $termine = StatutIncident::from($donnees['statut'])->estTermine();

        if ($termine && empty($donnees['resolu_at'])) {
            $donnees['resolu_at'] = now();
        }

        if (! $termine) {
            $donnees['resolu_at'] = null;
        }

        return $donnees;
    }

    /**
     * @return array<string, mixed>
     */
    private function donneesFormulaire(Incident $incident): array
    {
        return [
            'incident' => $incident,
            'sites' => Site::orderBy('code')->get(),
            'equipements' => Equipement::with('site')->orderBy('hostname')->get(),
            'liaisons' => Liaison::orderBy('code')->get(),
            'techniciens' => User::orderBy('name')->get(),
            'severites' => Severite::cases(),
            'statuts' => StatutIncident::cases(),
            'categories' => Categorie::cases(),
        ];
    }
}
