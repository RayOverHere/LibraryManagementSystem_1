<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'isbn',
        'category_id',
        'stock',
        'image',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    /**
     * One-to-Many (inverse): A book belongs to one category.
     * FK: books.category_id → categories.id
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Many-to-Many: A book can have multiple authors via the book_author pivot table.
     * Pivot: book_author (book_id, author_id)
     */
    public function authors()
    {
        return $this->belongsToMany(Author::class, 'book_author');
    }

    /**
     * One-to-Many: A book can have many transaction records.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Filtered relationship: Only active borrows (borrowed or overdue).
     * Used by withCount to compute availability dynamically.
     */
    public function activeBorrows()
    {
        return $this->hasMany(Transaction::class)
                    ->whereIn('status', ['borrowed', 'overdue']);
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    /**
     * Scope: Attach a computed 'available' count to each book using a LEFT JOIN subquery.
     *
     * This replaces the old redundant 'available' column with a 3NF-compliant
     * dynamic computation: available = stock - COUNT(active borrows).
     *
     * Under the hood, Eloquent's withCount generates:
     *   LEFT JOIN (SELECT book_id, COUNT(*) ... FROM transactions
     *              WHERE status IN ('borrowed','overdue')
     *              GROUP BY book_id) AS active_borrows_count
     *
     * Usage: Book::withAvailability()->get()
     */
    public function scopeWithAvailability($query)
    {
        return $query->withCount(['activeBorrows as active_borrows_count']);
    }

    // =========================================================================
    // Accessors
    // =========================================================================

    /**
     * Accessor: Compute the number of available copies dynamically.
     *
     * If the model was loaded with withAvailability() scope, it uses the
     * preloaded count. Otherwise it falls back to a live query.
     *
     * This replaces the old redundant 'available' database column.
     * Views can still call $book->available seamlessly.
     *
     * Formula: available = stock - active_borrows_count
     */
    public function getAvailableAttribute()
    {
        // Use preloaded count from withAvailability() scope
        if (array_key_exists('active_borrows_count', $this->attributes)) {
            return $this->stock - (int) $this->attributes['active_borrows_count'];
        }

        // Fallback: live query (less efficient, used when scope was not applied)
        return $this->stock - $this->activeBorrows()->count();
    }
}
