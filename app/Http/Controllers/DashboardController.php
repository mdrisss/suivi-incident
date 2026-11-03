<?php

namespace App\Http\Controllers;

use App\Enums\Severite;
use App\Enums\StatutIncident;
use App\Models\Incident;
use App\Models\Site;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $parStatut = Incident::query()->selectRaw('statut, count(*) as total')->groupBy('statut')->pluck('total', 'statut');

        $actifsParSeverite = Incident::actifs()
            ->selectRaw('severite, count(*) as total')->groupBy('severite')->pluck('total', 'severite');

        $resolus30j = Incident::query()
            ->whereNotNull('resolu_at')
            ->where('resolu_at', '>=', now()->subDays(30))
            ->get(['debut_at', 'resolu_at']);

        $topSites = Site::query()
            ->withCount(['incidents' => fn ($q) => $q->where('debut_at', '>=', now()->subDays(30))])
            ->orderByDesc('incidents_count')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'parStatut' => $parStatut,
            'actifsParSeverite' => $actifsParSeverite,
            'mttr' => $resolus30j->avg(fn (Incident $i) => $i->dureeMinutes()),
            'nbResolus30j' => $resolus30j->count(),
            'topSites' => $topSites,
            'critiquesOuverts' => Incident::actifs()
                ->where('severite', Severite::Critique)
                ->with(['site', 'assigneA'])
                ->orderBy('debut_at')
                ->get(),
            'derniers' => Incident::with('site')->latest('debut_at')->limit(8)->get(),
            'statuts' => StatutIncident::cases(),
            'severites' => Severite::cases(),
        ]);
    }
}
