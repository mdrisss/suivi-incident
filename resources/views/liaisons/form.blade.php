@extends('layouts.app')

@section('title', $liaison->exists ? 'Modifier une liaison' : 'Nouvelle liaison')

@section('content')
<h1 class="h3 mb-3">{{ $liaison->exists ? 'Modifier la liaison '.$liaison->code : 'Nouvelle liaison' }}</h1>

<form method="POST" action="{{ $liaison->exists ? route('liaisons.update', $liaison) : route('liaisons.store') }}" class="card card-body" style="max-width: 720px">
    @csrf
    @if ($liaison->exists) @method('PUT') @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Code *</label>
            <input type="text" name="code" value="{{ old('code', $liaison->code) }}" class="form-control font-monospace @error('code') is-invalid @enderror" placeholder="FO-BKO-KTI" required>
            <x-champ-erreur nom="code" />
        </div>
        <div class="col-md-6">
            <label class="form-label">Type *</label>
            <select name="type" class="form-select" required>
                @foreach (\App\Models\Liaison::TYPES as $type)
                    <option @selected(old('type', $liaison->type) === $type)>{{ $type }}</option>
                @endforeach
            </select>
        </div>
        @foreach (['site_a_id' => 'Site A', 'site_b_id' => 'Site B'] as $champ => $libelle)
            <div class="col-md-6">
                <label class="form-label">{{ $libelle }} *</label>
                <select name="{{ $champ }}" class="form-select @error($champ) is-invalid @enderror" required>
                    <option value="">— Choisir —</option>
                    @foreach ($sites as $site)
                        <option value="{{ $site->id }}" @selected(old($champ, $liaison->$champ) == $site->id)>{{ $site->libelle() }}</option>
                    @endforeach
                </select>
                <x-champ-erreur :nom="$champ" />
            </div>
        @endforeach
        <div class="col-md-4">
            <label class="form-label">Capacité *</label>
            <input type="text" name="capacite" value="{{ old('capacite', $liaison->capacite) }}" class="form-control @error('capacite') is-invalid @enderror" placeholder="10G" required>
            <x-champ-erreur nom="capacite" />
        </div>
        <div class="col-md-4">
            <label class="form-label">Opérateur</label>
            <input type="text" name="operateur" value="{{ old('operateur', $liaison->operateur) }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Statut *</label>
            <select name="statut" class="form-select" required>
                @foreach (\App\Models\Liaison::STATUTS as $valeur => $libelle)
                    <option value="{{ $valeur }}" @selected(old('statut', $liaison->statut) === $valeur)>{{ $libelle }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mt-4">
        <button class="btn btn-primary">Enregistrer</button>
        <a href="{{ route('liaisons.index') }}" class="btn btn-outline-secondary">Annuler</a>
    </div>
</form>
@endsection
