@extends('layouts.app')

@section('title', $incident->exists ? 'Modifier '.$incident->reference : 'Nouvel incident')

@section('content')
<h1 class="h3 mb-3">{{ $incident->exists ? 'Modifier l\'incident '.$incident->reference : 'Déclarer un incident' }}</h1>

<form method="POST" action="{{ $incident->exists ? route('incidents.update', $incident) : route('incidents.store') }}">
    @csrf
    @if ($incident->exists) @method('PUT') @endif

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header bg-white fw-semibold">Description</div>
                <div class="card-body row g-3">
                    <div class="col-12">
                        <label class="form-label">Titre *</label>
                        <input type="text" name="titre" value="{{ old('titre', $incident->titre) }}" class="form-control @error('titre') is-invalid @enderror" required>
                        <x-champ-erreur nom="titre" />
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description *</label>
                        <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $incident->description) }}</textarea>
                        <x-champ-erreur nom="description" />
                    </div>
                    <div class="col-12">
                        <label class="form-label">Impact (clients, services touchés)</label>
                        <textarea name="impact" rows="2" class="form-control">{{ old('impact', $incident->impact) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-white fw-semibold">Localisation</div>
                <div class="card-body row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Site *</label>
                        <select name="site_id" class="form-select @error('site_id') is-invalid @enderror" required>
                            <option value="">— Choisir —</option>
                            @foreach ($sites as $site)
                                <option value="{{ $site->id }}" @selected(old('site_id', $incident->site_id) == $site->id)>{{ $site->libelle() }}</option>
                            @endforeach
                        </select>
                        <x-champ-erreur nom="site_id" />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Équipement concerné</label>
                        <select name="equipement_id" class="form-select @error('equipement_id') is-invalid @enderror">
                            <option value="">— Aucun —</option>
                            @foreach ($equipements as $equipement)
                                <option value="{{ $equipement->id }}" @selected(old('equipement_id', $incident->equipement_id) == $equipement->id)>{{ $equipement->hostname }} ({{ $equipement->site->code }})</option>
                            @endforeach
                        </select>
                        <x-champ-erreur nom="equipement_id" />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Liaison concernée</label>
                        <select name="liaison_id" class="form-select @error('liaison_id') is-invalid @enderror">
                            <option value="">— Aucune —</option>
                            @foreach ($liaisons as $liaison)
                                <option value="{{ $liaison->id }}" @selected(old('liaison_id', $incident->liaison_id) == $liaison->id)>{{ $liaison->code }} ({{ $liaison->capacite }})</option>
                            @endforeach
                        </select>
                        <x-champ-erreur nom="liaison_id" />
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-white fw-semibold">Résolution <small class="text-muted fw-normal">(obligatoire pour un incident résolu ou clos)</small></div>
                <div class="card-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Cause racine</label>
                        <textarea name="cause_racine" rows="3" class="form-control @error('cause_racine') is-invalid @enderror">{{ old('cause_racine', $incident->cause_racine) }}</textarea>
                        <x-champ-erreur nom="cause_racine" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Solution apportée</label>
                        <textarea name="solution" rows="3" class="form-control @error('solution') is-invalid @enderror">{{ old('solution', $incident->solution) }}</textarea>
                        <x-champ-erreur nom="solution" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header bg-white fw-semibold">Qualification</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Sévérité *</label>
                        <select name="severite" class="form-select @error('severite') is-invalid @enderror" required>
                            <option value="">— Choisir —</option>
                            @foreach ($severites as $severite)
                                <option value="{{ $severite->value }}" @selected(old('severite', $incident->severite?->value) === $severite->value)>{{ $severite->label() }}</option>
                            @endforeach
                        </select>
                        <x-champ-erreur nom="severite" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catégorie *</label>
                        <select name="categorie" class="form-select @error('categorie') is-invalid @enderror" required>
                            <option value="">— Choisir —</option>
                            @foreach ($categories as $categorie)
                                <option value="{{ $categorie->value }}" @selected(old('categorie', $incident->categorie?->value) === $categorie->value)>{{ $categorie->label() }}</option>
                            @endforeach
                        </select>
                        <x-champ-erreur nom="categorie" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Statut *</label>
                        <select name="statut" class="form-select @error('statut') is-invalid @enderror" required>
                            @foreach ($statuts as $statut)
                                <option value="{{ $statut->value }}" @selected(old('statut', $incident->statut?->value) === $statut->value)>{{ $statut->label() }}</option>
                            @endforeach
                        </select>
                        <x-champ-erreur nom="statut" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assigné à</label>
                        <select name="assigne_a_id" class="form-select">
                            <option value="">— Non assigné —</option>
                            @foreach ($techniciens as $technicien)
                                <option value="{{ $technicien->id }}" @selected(old('assigne_a_id', $incident->assigne_a_id) == $technicien->id)>{{ $technicien->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Début de l'incident *</label>
                        <input type="datetime-local" name="debut_at" value="{{ old('debut_at', $incident->debut_at?->format('Y-m-d\TH:i')) }}" class="form-control @error('debut_at') is-invalid @enderror" required>
                        <x-champ-erreur nom="debut_at" />
                    </div>
                    <div>
                        <label class="form-label">Rétablissement</label>
                        <input type="datetime-local" name="resolu_at" value="{{ old('resolu_at', $incident->resolu_at?->format('Y-m-d\TH:i')) }}" class="form-control @error('resolu_at') is-invalid @enderror">
                        <div class="form-text">Laissé vide, il est fixé à maintenant lors de la résolution.</div>
                        <x-champ-erreur nom="resolu_at" />
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Enregistrer</button>
                <a href="{{ $incident->exists ? route('incidents.show', $incident) : route('incidents.index') }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </div>
    </div>
</form>
@endsection
