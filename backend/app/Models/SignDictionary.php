<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignDictionary extends Model
{
    protected $table = 'sign_dictionary';

    protected $fillable = [
        'word',
        'video_url',
        'thumbnail_url',
        'category',
        'description',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
