@extends('layouts.app')

@section('title', 'Équipements')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Équipements</h1>
    <a href="{{ route('equipements.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nouvel équipement</a>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-4"><input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Rechercher un hostname"></div>
    <div class="col-md-3">
        <select name="site_id" class="form-select">
            <option value="">Tous les sites</option>
            @foreach ($sites as $site)
                <option value="{{ $site->id }}" @selected(request('site_id') == $site->id)>{{ $site->libelle() }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2"><button class="btn btn-outline-primary w-100"><i class="bi bi-funnel"></i> Filtrer</button></div>
</form>

@php($couleurs = ['actif' => 'success', 'maintenance' => 'warning', 'hors_service' => 'danger'])
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Hostname</th><th>Site</th><th>Type</th><th>Constructeur / modèle</th><th>IP management</th><th>Statut</th><th>Incidents actifs</th><th></th></tr></thead>
            <tbody>
            @forelse ($equipements as $equipement)
                <tr>
                    <td class="fw-semibold font-monospace">{{ $equipement->hostname }}</td>
                    <td>{{ $equipement->site->code }}</td>
                    <td>{{ $equipement->type }}</td>
                    <td>{{ $equipement->constructeur }} <span class="text-muted">{{ $equipement->modele }}</span></td>
                    <td class="font-monospace">{{ $equipement->ip_management }}</td>
                    <td><span class="badge text-bg-{{ $couleurs[$equipement->statut] ?? 'secondary' }}">{{ $equipement->statutLabel() }}</span></td>
                    <td>{{ $equipement->incidents_count }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('equipements.edit', $equipement) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <x-bouton-supprimer :action="route('equipements.destroy', $equipement)" />
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Aucun équipement.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($equipements->hasPages())
        <div class="card-footer bg-white">{{ $equipements->links() }}</div>
    @endif
</div>
@endsection
