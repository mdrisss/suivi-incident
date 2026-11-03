@extends('layouts.app')

@section('title', 'Rapport hebdomadaire')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h1 class="h3 mb-0">Fiche hebdomadaire des incidents</h1>
        <div class="text-muted">Semaine {{ $debut->isoWeek() }} · du {{ $debut->format('d/m/Y') }} au {{ $fin->format('d/m/Y') }}</div>
    </div>
    <form method="GET" class="d-flex gap-2 no-print">
        <input type="week" name="semaine" value="{{ $semaine }}" class="form-control">
        <button class="btn btn-primary">Afficher</button>
        <button type="button" onclick="window.print()" class="btn btn-outline-secondary text-nowrap"><i class="bi bi-printer"></i> Imprimer</button>
    </form>
</div>

<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3"><div class="card card-body"><div class="text-muted small">Incidents de la semaine</div><div class="stat">{{ $incidents->count() }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="card card-body"><div class="text-muted small">Résolus dans la semaine</div><div class="stat text-success">{{ $nbResolus }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="card card-body"><div class="text-muted small">Encore ouverts en fin de semaine</div><div class="stat text-danger">{{ $toujoursOuverts->count() }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="card card-body"><div class="text-muted small">MTTR</div><div class="stat text-primary">{{ \App\Models\Incident::formaterDuree($mttr) }}</div></div></div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Par sévérité</div>
            <ul class="list-group list-group-flush">
                @foreach ($severites as $severite)
                    <li class="list-group-item d-flex justify-content-between"><x-badge :valeur="$severite" /><strong>{{ $parSeverite[$severite->value] }}</strong></li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Par catégorie</div>
            <ul class="list-group list-group-flush">
                @forelse ($parCategorie as $ligne)
                    <li class="list-group-item d-flex justify-content-between"><span>{{ $ligne['categorie']->label() }}</span><strong>{{ $ligne['total'] }}</strong></li>
                @empty
                    <li class="list-group-item text-muted">—</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Par site</div>
            <ul class="list-group list-group-flush">
                @forelse ($parSite as $site => $total)
                    <li class="list-group-item d-flex justify-content-between"><span>{{ $site }}</span><strong>{{ $total }}</strong></li>
                @empty
                    <li class="list-group-item text-muted">—</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header bg-white fw-semibold">Détail des incidents de la semaine</div>
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead class="table-light"><tr><th>Réf.</th><th>Titre</th><th>Sév.</th><th>Statut</th><th>Site / élément</th><th>Début</th><th>Durée</th><th>Cause racine</th></tr></thead>
            <tbody>
            @forelse ($incidents as $incident)
                <tr>
                    <td class="text-nowrap"><a href="{{ route('incidents.show', $incident) }}">{{ $incident->reference }}</a></td>
                    <td>{{ $incident->titre }}</td>
                    <td><x-badge :valeur="$incident->severite" /></td>
                    <td><x-badge :valeur="$incident->statut" /></td>
                    <td>{{ $incident->site->code }}<div class="small text-muted">{{ $incident->equipement?->hostname ?? $incident->liaison?->code }}</div></td>
                    <td class="text-nowrap">{{ $incident->debut_at->format('d/m H:i') }}</td>
                    <td class="text-nowrap">{{ $incident->dureeLisible() }}</td>
                    <td class="small">{{ $incident->cause_racine ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Aucun incident cette semaine.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($toujoursOuverts->isNotEmpty())
    <div class="card">
        <div class="card-header bg-white fw-semibold text-danger">Incidents encore ouverts au {{ $fin->format('d/m/Y') }}</div>
        <ul class="list-group list-group-flush">
            @foreach ($toujoursOuverts as $incident)
                <li class="list-group-item">
                    <a href="{{ route('incidents.show', $incident) }}">{{ $incident->reference }}</a> · {{ $incident->titre }}
                    <span class="text-muted">({{ $incident->site->code }}, depuis le {{ $incident->debut_at->format('d/m/Y') }})</span>
                </li>
            @endforeach
        </ul>
    </div>
@endif
@endsection
