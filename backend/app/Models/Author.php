<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Author
 *
 * @property int $id
 * @property string $name Nom complet de l'auteur
 * @property string|null $biography Biographie détaillée
 * @property string|null $birth_date Date de naissance
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Book[] $books
 */
class Author extends Model
{
    use HasFactory;

    /**
     * Attributs assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'biography',
        'birth_date',
    ];

    /**
     * Attributs à caster automatiquement.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    /**
     * Livres écrits par cet auteur.
     * 
     * Relation Many-to-Many via la table pivot author_book.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function books(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'author_book')
                    ->withTimestamps()
                    ->withPivot('id'); // Optionnel si besoin d'accéder au pivot
    }
}