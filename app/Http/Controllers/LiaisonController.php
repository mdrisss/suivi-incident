<?php

namespace App\Http\Controllers;

use App\Models\Liaison;
use App\Models\Site;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LiaisonController extends Controller
{
    public function index(): View
    {
        return view('liaisons.index', [
            'liaisons' => Liaison::with(['siteA', 'siteB'])
                ->withCount(['incidents' => fn ($q) => $q->actifs()])
                ->orderBy('code')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('liaisons.form', ['liaison' => new Liaison(['statut' => 'actif']), 'sites' => Site::orderBy('code')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Liaison::create($this->valider($request));

        return redirect()->route('liaisons.index')->with('success', 'Liaison créée.');
    }

    public function edit(Liaison $liaison): View
    {
        return view('liaisons.form', ['liaison' => $liaison, 'sites' => Site::orderBy('code')->get()]);
    }

    public function update(Request $request, Liaison $liaison): RedirectResponse
    {
        $liaison->update($this->valider($request, $liaison));

        return redirect()->route('liaisons.index')->with('success', 'Liaison mise à jour.');
    }

    public function destroy(Liaison $liaison): RedirectResponse
    {
        $liaison->delete();

        return redirect()->route('liaisons.index')->with('success', 'Liaison supprimée.');
    }

    /**
     * @return array<string, mixed>
     */
    private function valider(Request $request, ?Liaison $liaison = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:30', Rule::unique('liaisons')->ignore($liaison)],
            'site_a_id' => ['required', 'exists:sites,id'],
            'site_b_id' => ['required', 'exists:sites,id', 'different:site_a_id'],
            'type' => ['required', Rule::in(Liaison::TYPES)],
            'capacite' => ['required', 'string', 'max:20'],
            'operateur' => ['nullable', 'string', 'max:255'],
            'statut' => ['required', Rule::in(array_keys(Liaison::STATUTS))],
        ], [], ['site_a_id' => 'site A', 'site_b_id' => 'site B', 'capacite' => 'capacité', 'operateur' => 'opérateur']);
    }
}
