@extends('layouts.app')

@section('title', 'Utilisateurs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Utilisateurs</h1>
    <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="bi bi-person-plus"></i> Nouvel utilisateur</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Nom</th><th>E-mail</th><th>Téléphone</th><th>Rôle</th><th>Incidents actifs assignés</th><th></th></tr></thead>
            <tbody>
            @foreach ($users as $user)
                <tr>
                    <td class="fw-semibold">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->telephone }}</td>
                    <td><span class="badge text-bg-{{ $user->estAdmin() ? 'dark' : 'secondary' }}">{{ $user->role->label() }}</span></td>
                    <td>{{ $user->incidents_assignes_count }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        @unless ($user->is(auth()->user()))
                            <x-bouton-supprimer :action="route('users.destroy', $user)" />
                        @endunless
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
