{{-- Affiche un enum (Severite, StatutIncident...) sous forme de badge Bootstrap. --}}
@props(['valeur'])
<span {{ $attributes->merge(['class' => 'badge text-bg-'.$valeur->color()]) }}>{{ $valeur->label() }}</span>
