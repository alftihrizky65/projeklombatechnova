<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            [
                'title' => 'Kelas Isyarat Salam',
                'description' => 'Pelajari cara menyapa dalam bahasa isyarat BISINDO.',
                'category' => 'Salam',
                'difficulty' => 'beginner',
                'required_practical_count' => 3
            ],
            [
                'title' => 'Kelas Alfabet Jari',
                'description' => 'Kuasai seluruh huruf alfabet A-Z dengan tangan.',
                'category' => 'Alfabet',
                'difficulty' => 'intermediate',
                'required_practical_count' => 5
            ],
            [
                'title' => 'Kelas Angka & Berhitung',
                'description' => 'Mengenal angka 1-100 dalam bahasa isyarat.',
                'category' => 'Angka',
                'difficulty' => 'intermediate',
                'required_practical_count' => 5
            ]
        ];

        foreach ($classes as $c) {
            $learningClass = \App\Models\LearningClass::create($c);
            
            // Link existing quizzes to these classes based on category
            \App\Models\Quiz::where('category', $c['category'])
                ->update(['learning_class_id' => $learningClass->id]);
        }
    }
}
