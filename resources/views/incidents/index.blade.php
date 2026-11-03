@extends('layouts.app')

@section('title', 'Incidents')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Incidents</h1>
    <div>
        <a href="{{ route('incidents.export', request()->query()) }}" class="btn btn-outline-success"><i class="bi bi-filetype-csv"></i> Exporter</a>
        <a href="{{ route('incidents.create') }}" class="btn btn-danger"><i class="bi bi-plus-lg"></i> Nouvel incident</a>
    </div>
</div>

<form method="GET" class="card card-body mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small">Recherche</label>
            <input type="search" name="q" value="{{ $filtres['q'] ?? '' }}" class="form-control" placeholder="Référence ou titre">
        </div>
        <div class="col-md-2">
            <label class="form-label small">Statut</label>
            <select name="statut" class="form-select">
                <option value="">Tous</option>
                @foreach (\App\Enums\StatutIncident::cases() as $statut)
                    <option value="{{ $statut->value }}" @selected(($filtres['statut'] ?? '') === $statut->value)>{{ $statut->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small">Sévérité</label>
            <select name="severite" class="form-select">
                <option value="">Toutes</option>
                @foreach (\App\Enums\Severite::cases() as $severite)
                    <option value="{{ $severite->value }}" @selected(($filtres['severite'] ?? '') === $severite->value)>{{ $severite->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small">Site</label>
            <select name="site_id" class="form-select">
                <option value="">Tous</option>
                @foreach ($sites as $site)
                    <option value="{{ $site->id }}" @selected(($filtres['site_id'] ?? '') == $site->id)>{{ $site->code }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1">
            <label class="form-label small">Du</label>
            <input type="date" name="du" value="{{ $filtres['du'] ?? '' }}" class="form-control">
        </div>
        <div class="col-md-1">
            <label class="form-label small">Au</label>
            <input type="date" name="au" value="{{ $filtres['au'] ?? '' }}" class="form-control">
        </div>
        <div class="col-md-1 d-flex gap-1">
            <button class="btn btn-primary w-100" title="Filtrer"><i class="bi bi-funnel"></i></button>
            <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary" title="Réinitialiser"><i class="bi bi-x-lg"></i></a>
        </div>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Référence</th><th>Titre</th><th>Sévérité</th><th>Statut</th><th>Site</th><th>Assigné à</th><th>Début</th><th>Durée</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($incidents as $incident)
                <tr>
                    <td class="text-nowrap"><a href="{{ route('incidents.show', $incident) }}" class="fw-semibold">{{ $incident->reference }}</a></td>
                    <td>{{ $incident->titre }}<div class="small text-muted">{{ $incident->categorie->label() }}</div></td>
                    <td><x-badge :valeur="$incident->severite" /></td>
                    <td><x-badge :valeur="$incident->statut" /></td>
                    <td>{{ $incident->site->code }}</td>
                    <td>{{ $incident->assigneA?->name ?? '—' }}</td>
                    <td class="text-nowrap">{{ $incident->debut_at->format('d/m/Y H:i') }}</td>
                    <td class="text-nowrap">{{ $incident->dureeLisible() }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Aucun incident ne correspond aux filtres.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($incidents->hasPages())
        <div class="card-footer bg-white">{{ $incidents->links() }}</div>
    @endif
</div>
@endsection
