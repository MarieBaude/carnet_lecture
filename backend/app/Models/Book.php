<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Book
 *
 * @property int $id
 * @property string $title Titre du livre
 * @property string|null $isbn ISBN-13
 * @property int $cover_variant Variante de couverture (1-10)
 * @property string|null $publisher Maison d'édition
 * @property string|null $published_date Date de publication
 * @property int|null $page_count Nombre de pages
 * @property string $language Code langue ISO 639-1
 * @property string|null $summary Résumé
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Author[] $authors
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Genre[] $genres
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Saga[] $sagas
 */
class Book extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Attributs assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'isbn',
        'cover_variant',
        'publisher',
        'published_date',
        'page_count',
        'language',
        'summary',
    ];

    /**
     * Attributs à caster automatiquement.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_date' => 'date',
            'page_count' => 'integer',
            'cover_variant' => 'integer',
        ];
    }

    /**
     * Auteurs du livre.
     * 
     * Relation Many-to-Many via author_book.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function authors(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'author_book')
                    ->withTimestamps();
    }

    /**
     * Genres du livre.
     * 
     * Relation Many-to-Many via book_genre.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function genres(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'book_genre')
                    ->withTimestamps();
    }

    /**
     * Sagas auxquelles appartient le livre.
     * 
     * Relation Many-to-Many via book_saga.
     * Inclut le numéro de tome.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sagas(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Saga::class, 'book_saga')
                    ->withPivot('tome_number')
                    ->withTimestamps();
    }

    /**
     * Scope pour la recherche full-text en français.
     * 
     * Exemple d'utilisation :
     * Book::search("seigneur des anneaux")->get();
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $term Termes de recherche
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->whereRaw(
            "to_tsvector('french', coalesce(title, '') || ' ' || coalesce(summary, '')) @@ plainto_tsquery('french', ?)",
            [$term]
        );
    }
}