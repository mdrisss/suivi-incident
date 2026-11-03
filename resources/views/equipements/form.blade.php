@extends('layouts.app')

@section('title', $equipement->exists ? 'Modifier un équipement' : 'Nouvel équipement')

@section('content')
<h1 class="h3 mb-3">{{ $equipement->exists ? 'Modifier '.$equipement->hostname : 'Nouvel équipement' }}</h1>

<form method="POST" action="{{ $equipement->exists ? route('equipements.update', $equipement) : route('equipements.store') }}" class="card card-body" style="max-width: 720px">
    @csrf
    @if ($equipement->exists) @method('PUT') @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Hostname *</label>
            <input type="text" name="hostname" value="{{ old('hostname', $equipement->hostname) }}" class="form-control font-monospace @error('hostname') is-invalid @enderror" required>
            <x-champ-erreur nom="hostname" />
        </div>
        <div class="col-md-6">
            <label class="form-label">Site *</label>
            <select name="site_id" class="form-select @error('site_id') is-invalid @enderror" required>
                <option value="">— Choisir —</option>
                @foreach ($sites as $site)
                    <option value="{{ $site->id }}" @selected(old('site_id', $equipement->site_id) == $site->id)>{{ $site->libelle() }}</option>
                @endforeach
            </select>
            <x-champ-erreur nom="site_id" />
        </div>
        <div class="col-md-4">
            <label class="form-label">Type *</label>
            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                @foreach (\App\Models\Equipement::TYPES as $type)
                    <option @selected(old('type', $equipement->type) === $type)>{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Constructeur *</label>
            <select name="constructeur" class="form-select @error('constructeur') is-invalid @enderror" required>
                @foreach (\App\Models\Equipement::CONSTRUCTEURS as $constructeur)
                    <option @selected(old('constructeur', $equipement->constructeur) === $constructeur)>{{ $constructeur }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Modèle</label>
            <input type="text" name="modele" value="{{ old('modele', $equipement->modele) }}" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">IP de management</label>
            <input type="text" name="ip_management" value="{{ old('ip_management', $equipement->ip_management) }}" class="form-control font-monospace @error('ip_management') is-invalid @enderror" placeholder="10.10.1.1">
            <x-champ-erreur nom="ip_management" />
        </div>
        <div class="col-md-6">
            <label class="form-label">Statut *</label>
            <select name="statut" class="form-select" required>
                @foreach (\App\Models\Equipement::STATUTS as $valeur => $libelle)
                    <option value="{{ $valeur }}" @selected(old('statut', $equipement->statut) === $valeur)>{{ $libelle }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mt-4">
        <button class="btn btn-primary">Enregistrer</button>
        <a href="{{ route('equipements.index') }}" class="btn btn-outline-secondary">Annuler</a>
    </div>
</form>
@endsection
