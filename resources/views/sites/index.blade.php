@extends('layouts.app')

@section('title', 'Sites')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Sites</h1>
    <a href="{{ route('sites.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nouveau site</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Code</th><th>Nom</th><th>Ville</th><th>Type</th><th>Équipements</th><th>Incidents actifs</th><th></th></tr></thead>
            <tbody>
            @forelse ($sites as $site)
                <tr>
                    <td class="fw-semibold">{{ $site->code }}</td>
                    <td>{{ $site->nom }}</td>
                    <td>{{ $site->ville }}</td>
                    <td>{{ $site->type }}</td>
                    <td><a href="{{ route('equipements.index', ['site_id' => $site->id]) }}">{{ $site->equipements_count }}</a></td>
                    <td>
                        @if ($site->incidents_count)
                            <a href="{{ route('incidents.index', ['site_id' => $site->id]) }}" class="badge text-bg-danger text-decoration-none">{{ $site->incidents_count }}</a>
                        @else
                            <span class="text-muted">0</span>
                        @endif
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('sites.edit', $site) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <x-bouton-supprimer :action="route('sites.destroy', $site)" />
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Aucun site.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
