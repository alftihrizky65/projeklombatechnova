<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiLog extends Model
{
    protected $fillable = [
        'user_id',
        'sign_detected',
        'confidence',
        'is_correct',
        'response_time_ms',
    ];

    protected function casts(): array
    {
        return [
            'confidence' => 'float',
            'is_correct' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
