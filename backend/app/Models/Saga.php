<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Saga
 *
 * @property int $id
 * @property string $name Nom de la saga
 * @property string|null $description Description globale
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Book[] $books
 */
class Saga extends Model
{
    use HasFactory;

    /**
     * Attributs assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Livres faisant partie de cette saga.
     * 
     * Relation Many-to-Many via la table pivot book_saga.
     * Inclut le numéro de tome dans le pivot.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function books(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_saga')
                    ->withPivot('tome_number')
                    ->withTimestamps()
                    ->orderByPivot('tome_number'); // Tri par ordre de tome
    }
}