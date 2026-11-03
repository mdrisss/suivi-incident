<?php

namespace App\Http\Controllers;

use App\Models\Equipement;
use App\Models\Site;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EquipementController extends Controller
{
    public function index(Request $request): View
    {
        $equipements = Equipement::query()
            ->with('site')
            ->withCount(['incidents' => fn ($q) => $q->actifs()])
            ->when($request->input('site_id'), fn ($q, $siteId) => $q->where('site_id', $siteId))
            ->when($request->input('q'), fn ($q, $texte) => $q->where('hostname', 'like', "%{$texte}%"))
            ->orderBy('hostname')
            ->paginate(20)
            ->withQueryString();

        return view('equipements.index', [
            'equipements' => $equipements,
            'sites' => Site::orderBy('code')->get(),
        ]);
    }

    public function create(): View
    {
        return view('equipements.form', ['equipement' => new Equipement(['statut' => 'actif']), 'sites' => Site::orderBy('code')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Equipement::create($this->valider($request));

        return redirect()->route('equipements.index')->with('success', 'Équipement créé.');
    }

    public function edit(Equipement $equipement): View
    {
        return view('equipements.form', ['equipement' => $equipement, 'sites' => Site::orderBy('code')->get()]);
    }

    public function update(Request $request, Equipement $equipement): RedirectResponse
    {
        $equipement->update($this->valider($request, $equipement));

        return redirect()->route('equipements.index')->with('success', 'Équipement mis à jour.');
    }

    public function destroy(Equipement $equipement): RedirectResponse
    {
        $equipement->delete();

        return redirect()->route('equipements.index')->with('success', 'Équipement supprimé.');
    }

    /**
     * @return array<string, mixed>
     */
    private function valider(Request $request, ?Equipement $equipement = null): array
    {
        return $request->validate([
            'site_id' => ['required', 'exists:sites,id'],
            'hostname' => ['required', 'string', 'max:255', Rule::unique('equipements')->ignore($equipement)],
            'type' => ['required', Rule::in(Equipement::TYPES)],
            'constructeur' => ['required', Rule::in(Equipement::CONSTRUCTEURS)],
            'modele' => ['nullable', 'string', 'max:255'],
            'ip_management' => ['nullable', 'ip'],
            'statut' => ['required', Rule::in(array_keys(Equipement::STATUTS))],
        ]);
    }
}
