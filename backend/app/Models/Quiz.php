<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'title',
        'description',
        'difficulty',
        'category',
        'is_published',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class);
    }

    public function learningClass()
    {
        return $this->belongsTo(LearningClass::class, 'learning_class_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
