<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'title',
        'title_danish',
        'slug',
        'short_description',
        'long_description',
        'release_date',
        'duration',
        'language',
        'poster',
        'trailer',
        'tmdb_id',
        'imdb_id',
        'dfi_id',
        'is_active',
    ];
}
