<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningClass extends Model
{
    protected $fillable = ['title', 'description', 'category', 'difficulty', 'required_practical_count'];

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'learning_class_id');
    }
}
