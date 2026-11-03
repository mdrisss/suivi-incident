@extends('layouts.app')

@section('title', $site->exists ? 'Modifier un site' : 'Nouveau site')

@section('content')
<h1 class="h3 mb-3">{{ $site->exists ? 'Modifier le site '.$site->code : 'Nouveau site' }}</h1>

<form method="POST" action="{{ $site->exists ? route('sites.update', $site) : route('sites.store') }}" class="card card-body" style="max-width: 720px">
    @csrf
    @if ($site->exists) @method('PUT') @endif

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Code *</label>
            <input type="text" name="code" value="{{ old('code', $site->code) }}" class="form-control @error('code') is-invalid @enderror" placeholder="BKO-POP1" required>
            <x-champ-erreur nom="code" />
        </div>
        <div class="col-md-8">
            <label class="form-label">Nom *</label>
            <input type="text" name="nom" value="{{ old('nom', $site->nom) }}" class="form-control @error('nom') is-invalid @enderror" required>
            <x-champ-erreur nom="nom" />
        </div>
        <div class="col-md-6">
            <label class="form-label">Ville *</label>
            <input type="text" name="ville" value="{{ old('ville', $site->ville) }}" class="form-control @error('ville') is-invalid @enderror" required>
            <x-champ-erreur nom="ville" />
        </div>
        <div class="col-md-6">
            <label class="form-label">Type *</label>
            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                @foreach (\App\Models\Site::TYPES as $type)
                    <option @selected(old('type', $site->type) === $type)>{{ $type }}</option>
                @endforeach
            </select>
            <x-champ-erreur nom="type" />
        </div>
        <div class="col-12">
            <label class="form-label">Adresse</label>
            <input type="text" name="adresse" value="{{ old('adresse', $site->adresse) }}" class="form-control">
        </div>
    </div>

    <div class="mt-4">
        <button class="btn btn-primary">Enregistrer</button>
        <a href="{{ route('sites.index') }}" class="btn btn-outline-secondary">Annuler</a>
    </div>
</form>
@endsection
