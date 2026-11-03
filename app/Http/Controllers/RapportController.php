<?php

namespace App\Http\Controllers;

use App\Enums\Categorie;
use App\Enums\Severite;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class RapportController extends Controller
{
    /**
     * Fiche hebdomadaire : incidents survenus pendant une semaine ISO (lundi → dimanche).
     */
    public function hebdomadaire(Request $request): View
    {
        $request->validate(['semaine' => ['nullable', 'regex:/^\d{4}-W\d{2}$/']]);

        $semaine = $request->input('semaine', now()->format('o-\WW'));
        [$annee, $numero] = explode('-W', $semaine);
        $debut = Carbon::now()->setISODate((int) $annee, (int) $numero)->startOfWeek();
        $fin = $debut->copy()->endOfWeek();

        $incidents = Incident::query()
            ->with(['site', 'equipement', 'liaison', 'assigneA'])
            ->whereBetween('debut_at', [$debut, $fin])
            ->orderBy('debut_at')
            ->get();

        $resolus = $incidents->filter(fn (Incident $i) => $i->resolu_at && $i->resolu_at->lte($fin));

        $toujoursOuverts = Incident::query()
            ->with('site')
            ->where('debut_at', '<=', $fin)
            ->where(fn ($q) => $q->whereNull('resolu_at')->orWhere('resolu_at', '>', $fin))
            ->orderBy('debut_at')
            ->get();

        return view('rapports.hebdomadaire', [
            'semaine' => $semaine,
            'debut' => $debut,
            'fin' => $fin,
            'incidents' => $incidents,
            'nbResolus' => $resolus->count(),
            'mttr' => $resolus->avg(fn (Incident $i) => $i->dureeMinutes()),
            'parSeverite' => collect(Severite::cases())->mapWithKeys(fn (Severite $s) => [$s->value => $incidents->where('severite', $s)->count()]),
            'parCategorie' => collect(Categorie::cases())
                ->map(fn (Categorie $c) => ['categorie' => $c, 'total' => $incidents->where('categorie', $c)->count()])
                ->where('total', '>', 0)
                ->sortByDesc('total'),
            'parSite' => $incidents->groupBy(fn (Incident $i) => $i->site->libelle())->map->count()->sortDesc(),
            'toujoursOuverts' => $toujoursOuverts,
            'severites' => Severite::cases(),
        ]);
    }
}
