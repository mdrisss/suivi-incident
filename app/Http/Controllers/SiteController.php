<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function index(): View
    {
        return view('sites.index', [
            'sites' => Site::withCount(['equipements', 'incidents' => fn ($q) => $q->actifs()])->orderBy('code')->get(),
        ]);
    }

    public function create(): View
    {
        return view('sites.form', ['site' => new Site]);
    }

    public function store(Request $request): RedirectResponse
    {
        Site::create($this->valider($request));

        return redirect()->route('sites.index')->with('success', 'Site créé.');
    }

    public function edit(Site $site): View
    {
        return view('sites.form', ['site' => $site]);
    }

    public function update(Request $request, Site $site): RedirectResponse
    {
        $site->update($this->valider($request, $site));

        return redirect()->route('sites.index')->with('success', 'Site mis à jour.');
    }

    public function destroy(Site $site): RedirectResponse
    {
        if ($site->equipements()->exists() || $site->incidents()->exists()) {
            return back()->with('error', 'Impossible de supprimer un site qui possède des équipements ou des incidents.');
        }

        $site->delete();

        return redirect()->route('sites.index')->with('success', 'Site supprimé.');
    }

    /**
     * @return array<string, mixed>
     */
    private function valider(Request $request, ?Site $site = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('sites')->ignore($site)],
            'nom' => ['required', 'string', 'max:255'],
            'ville' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(Site::TYPES)],
            'adresse' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
