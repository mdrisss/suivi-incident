@props(['action', 'message' => 'Confirmer la suppression ?'])
<form method="POST" action="{{ $action }}" class="d-inline" onsubmit="return confirm(@js($message))">
    @csrf
    @method('DELETE')
    <button {{ $attributes->merge(['class' => 'btn btn-sm btn-outline-danger']) }}><i class="bi bi-trash"></i>{{ $slot }}</button>
</form>
