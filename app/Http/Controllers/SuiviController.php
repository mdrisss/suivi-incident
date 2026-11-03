<?php

namespace App\Http\Controllers;

use App\Enums\TypeSuivi;
use App\Models\Incident;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SuiviController extends Controller
{
    public function store(Request $request, Incident $incident): RedirectResponse
    {
        $donnees = $request->validate([
            'type' => ['required', Rule::enum(TypeSuivi::class)->only([TypeSuivi::Commentaire, TypeSuivi::Intervention])],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $incident->suivis()->create($donnees + ['user_id' => $request->user()->id]);

        return back()->with('success', 'Suivi ajouté.');
    }
}
