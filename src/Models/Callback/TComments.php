<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TComments extends Model
{
    protected $table = 't_comments';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'comment',
        'rating',
        'product_id',
        'is_moderated',
    ];

    protected $casts = [
        'rating' => 'integer',
        'product_id' => 'integer',
        'is_moderated' => 'boolean',
    ];

    /**
     * Get the product associated with the comment
     */
    public function product(): BelongsTo
    {
        if (class_exists('App\Models\TProduct')) {
            return $this->belongsTo(\App\Models\TProduct::class, 'product_id');
        }
        return $this->belongsTo(Model::class, 'product_id');
    }
}
