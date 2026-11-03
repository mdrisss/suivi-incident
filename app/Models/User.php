<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'telephone'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
        ];
    }

    public function estAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    /**
     * @return HasMany<Incident, $this>
     */
    public function incidentsDeclares(): HasMany
    {
        return $this->hasMany(Incident::class, 'declare_par_id');
    }

    /**
     * @return HasMany<Incident, $this>
     */
    public function incidentsAssignes(): HasMany
    {
        return $this->hasMany(Incident::class, 'assigne_a_id');
    }
}
