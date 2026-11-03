@extends('layouts.app')

@section('title', 'Liaisons')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Liaisons backbone</h1>
    <a href="{{ route('liaisons.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nouvelle liaison</a>
</div>

@php($couleurs = ['actif' => 'success', 'degrade' => 'warning', 'coupe' => 'danger'])
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Code</th><th>Extrémités</th><th>Type</th><th>Capacité</th><th>Opérateur</th><th>Statut</th><th>Incidents actifs</th><th></th></tr></thead>
            <tbody>
            @forelse ($liaisons as $liaison)
                <tr>
                    <td class="fw-semibold font-monospace">{{ $liaison->code }}</td>
                    <td>{{ $liaison->siteA->code }} <i class="bi bi-arrow-left-right text-muted"></i> {{ $liaison->siteB->code }}</td>
                    <td>{{ $liaison->type }}</td>
                    <td>{{ $liaison->capacite }}</td>
                    <td>{{ $liaison->operateur }}</td>
                    <td><span class="badge text-bg-{{ $couleurs[$liaison->statut] ?? 'secondary' }}">{{ $liaison->statutLabel() }}</span></td>
                    <td>{{ $liaison->incidents_count }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('liaisons.edit', $liaison) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <x-bouton-supprimer :action="route('liaisons.destroy', $liaison)" />
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Aucune liaison.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
