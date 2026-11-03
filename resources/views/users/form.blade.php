@extends('layouts.app')

@section('title', $user->exists ? 'Modifier un utilisateur' : 'Nouvel utilisateur')

@section('content')
<h1 class="h3 mb-3">{{ $user->exists ? 'Modifier '.$user->name : 'Nouvel utilisateur' }}</h1>

<form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}" class="card card-body" style="max-width: 720px">
    @csrf
    @if ($user->exists) @method('PUT') @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nom *</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>
            <x-champ-erreur nom="name" />
        </div>
        <div class="col-md-6">
            <label class="form-label">E-mail *</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>
            <x-champ-erreur nom="email" />
        </div>
        <div class="col-md-6">
            <label class="form-label">Téléphone</label>
            <input type="text" name="telephone" value="{{ old('telephone', $user->telephone) }}" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Rôle *</label>
            <select name="role" class="form-select" required>
                @foreach ($roles as $role)
                    <option value="{{ $role->value }}" @selected(old('role', $user->role?->value) === $role->value)>{{ $role->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Mot de passe {{ $user->exists ? '' : '*' }}</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" @required(! $user->exists) autocomplete="new-password">
            @if ($user->exists)<div class="form-text">Laisser vide pour ne pas le changer.</div>@endif
            <x-champ-erreur nom="password" />
        </div>
        <div class="col-md-6">
            <label class="form-label">Confirmation</label>
            <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
        </div>
    </div>

    <div class="mt-4">
        <button class="btn btn-primary">Enregistrer</button>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Annuler</a>
    </div>
</form>
@endsection
