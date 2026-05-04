<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name Nom complet de l'utilisateur
 * @property string $email Email unique
 * @property \Carbon\Carbon|null $email_verified_at Date de vérification email
 * @property string $password Mot de passe hashé
 * @property string|null $bio Biographie du profil
 * @property string|null $avatar_url URL de l'avatar
 * @property string $role Rôle (user ou admin)
 * @property string|null $remember_token Token "se souvenir de moi"
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Attributs assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'bio',
        'avatar_url',
        'role',
    ];

    /**
     * Attributs cachés pour la sérialisation.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attributs à caster automatiquement.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Bibliothèque personnelle de l'utilisateur.
     */
    public function library(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'user_books')
            ->withPivot(['status', 'rating', 'current_page', 'started_at', 'finished_at', 'user_comment', 'shelf'])
            ->withTimestamps();
    }

    public function wishlist(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->library()->wherePivot('status', 'wishlist');
    }

    public function reading(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->library()->wherePivot('status', 'reading');
    }

    public function read(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->library()->wherePivot('status', 'read');
    }

    public function owned(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->library()->wherePivot('status', 'owned');
    }

    public function dropped(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->library()->wherePivot('status', 'dropped');
    }
}
