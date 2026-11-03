@extends('layouts.app')

@section('title', $incident->reference)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
    <div>
        <div class="text-muted">{{ $incident->reference }}</div>
        <h1 class="h3 mb-1">{{ $incident->titre }}</h1>
        <x-badge :valeur="$incident->severite" class="fs-6" />
        <x-badge :valeur="$incident->statut" class="fs-6" />
        <span class="badge text-bg-light fs-6">{{ $incident->categorie->label() }}</span>
    </div>
    <div class="no-print">
        <a href="{{ route('incidents.edit', $incident) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Modifier</a>
        <button onclick="window.print()" class="btn btn-outline-secondary"><i class="bi bi-printer"></i> Imprimer</button>
        @can('admin')
            <x-bouton-supprimer :action="route('incidents.destroy', $incident)" class="btn btn-outline-danger" message="Supprimer définitivement cet incident ?"> Supprimer</x-bouton-supprimer>
        @endcan
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-body">
                <h2 class="h6 text-muted text-uppercase">Description</h2>
                <p style="white-space: pre-line">{{ $incident->description }}</p>
                @if ($incident->impact)
                    <h2 class="h6 text-muted text-uppercase">Impact</h2>
                    <p class="mb-0">{{ $incident->impact }}</p>
                @endif
            </div>
        </div>

        @if ($incident->cause_racine || $incident->solution)
            <div class="card mb-3 border-start border-4 border-success">
                <div class="card-body row">
                    <div class="col-md-6">
                        <h2 class="h6 text-muted text-uppercase">Cause racine</h2>
                        <p class="mb-md-0">{{ $incident->cause_racine }}</p>
                    </div>
                    <div class="col-md-6">
                        <h2 class="h6 text-muted text-uppercase">Solution</h2>
                        <p class="mb-0">{{ $incident->solution }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header bg-white fw-semibold">Journal de suivi</div>
            <div class="card-body">
                <form method="POST" action="{{ route('incidents.suivis.store', $incident) }}" class="mb-4 no-print">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-3">
                            <select name="type" class="form-select">
                                @foreach ($typesSuivi as $type)
                                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-7">
                            <textarea name="message" rows="1" class="form-control @error('message') is-invalid @enderror" placeholder="Action réalisée, constat, échange avec le terrain..." required>{{ old('message') }}</textarea>
                            <x-champ-erreur nom="message" />
                        </div>
                        <div class="col-md-2 d-grid">
                            <button class="btn btn-outline-primary"><i class="bi bi-send"></i> Ajouter</button>
                        </div>
                    </div>
                </form>

                <div class="timeline">
                    @forelse ($incident->suivis as $suivi)
                        <div class="timeline-item">
                            <div class="small text-muted">
                                <i class="bi bi-{{ $suivi->type->icon() }}"></i> {{ $suivi->type->label() }} ·
                                <strong>{{ $suivi->user->name }}</strong> · {{ $suivi->created_at->format('d/m/Y H:i') }}
                            </div>
                            <div style="white-space: pre-line">{{ $suivi->message }}</div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Aucun suivi pour le moment.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><div class="small text-muted">Site</div>{{ $incident->site->libelle() }} <span class="text-muted">({{ $incident->site->ville }})</span></li>
                <li class="list-group-item"><div class="small text-muted">Équipement</div>
                    @if ($incident->equipement)
                        {{ $incident->equipement->hostname }} <span class="text-muted">· {{ $incident->equipement->constructeur }} {{ $incident->equipement->modele }} · {{ $incident->equipement->ip_management }}</span>
                    @else — @endif
                </li>
                <li class="list-group-item"><div class="small text-muted">Liaison</div>
                    @if ($incident->liaison)
                        {{ $incident->liaison->code }} <span class="text-muted">· {{ $incident->liaison->siteA->code }} ↔ {{ $incident->liaison->siteB->code }} · {{ $incident->liaison->capacite }}</span>
                    @else — @endif
                </li>
                <li class="list-group-item"><div class="small text-muted">Déclaré par</div>{{ $incident->declarePar->name }}</li>
                <li class="list-group-item"><div class="small text-muted">Assigné à</div>{{ $incident->assigneA?->name ?? 'Non assigné' }}</li>
                <li class="list-group-item"><div class="small text-muted">Début</div>{{ $incident->debut_at->format('d/m/Y H:i') }}</li>
                <li class="list-group-item"><div class="small text-muted">Rétablissement</div>{{ $incident->resolu_at?->format('d/m/Y H:i') ?? '—' }}</li>
                <li class="list-group-item"><div class="small text-muted">Durée {{ $incident->resolu_at ? 'totale' : 'en cours' }}</div><span class="fs-5 fw-semibold">{{ $incident->dureeLisible() }}</span></li>
            </ul>
        </div>
    </div>
</div>
@endsection
