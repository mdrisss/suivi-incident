@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Tableau de bord</h1>
    <a href="{{ route('incidents.create') }}" class="btn btn-danger"><i class="bi bi-exclamation-octagon"></i> Déclarer un incident</a>
</div>

<div class="row g-3 mb-4">
    @foreach ($statuts as $statut)
        <div class="col-6 col-lg-3">
            <a href="{{ route('incidents.index', ['statut' => $statut->value]) }}" class="text-decoration-none">
                <div class="card h-100 border-start border-4 border-{{ $statut->color() }}">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase">{{ $statut->label() }}</div>
                        <div class="stat text-{{ $statut->color() }}">{{ $parStatut[$statut->value] ?? 0 }}</div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Incidents actifs par sévérité</div>
            <ul class="list-group list-group-flush">
                @foreach ($severites as $severite)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <x-badge :valeur="$severite" />
                        <span class="fs-5 fw-semibold">{{ $actifsParSeverite[$severite->value] ?? 0 }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">MTTR (30 derniers jours)</div>
            <div class="card-body d-flex flex-column justify-content-center text-center">
                <div class="stat text-primary">{{ \App\Models\Incident::formaterDuree($mttr) }}</div>
                <div class="text-muted mt-2">Temps moyen de rétablissement sur {{ $nbResolus30j }} incident(s) résolu(s)</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Sites les plus touchés (30 j)</div>
            <ul class="list-group list-group-flush">
                @foreach ($topSites as $site)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $site->libelle() }}</span>
                        <span class="badge text-bg-secondary rounded-pill">{{ $site->incidents_count }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-danger text-white fw-semibold"><i class="bi bi-fire"></i> Incidents critiques en cours</div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 align-middle">
                    <tbody>
                    @forelse ($critiquesOuverts as $incident)
                        <tr>
                            <td class="text-nowrap"><a href="{{ route('incidents.show', $incident) }}">{{ $incident->reference }}</a></td>
                            <td>{{ $incident->titre }}<div class="small text-muted">{{ $incident->site->code }} · {{ $incident->assigneA?->name ?? 'Non assigné' }}</div></td>
                            <td class="text-end text-danger fw-semibold text-nowrap">{{ $incident->dureeLisible() }}</td>
                        </tr>
                    @empty
                        <tr><td class="text-center text-muted py-4">Aucun incident critique en cours <i class="bi bi-emoji-smile"></i></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Derniers incidents</div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 align-middle">
                    <tbody>
                    @foreach ($derniers as $incident)
                        <tr>
                            <td class="text-nowrap"><a href="{{ route('incidents.show', $incident) }}">{{ $incident->reference }}</a></td>
                            <td>{{ Str::limit($incident->titre, 45) }}</td>
                            <td><x-badge :valeur="$incident->severite" /></td>
                            <td><x-badge :valeur="$incident->statut" /></td>
                            <td class="small text-muted text-nowrap">{{ $incident->debut_at->format('d/m H:i') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
