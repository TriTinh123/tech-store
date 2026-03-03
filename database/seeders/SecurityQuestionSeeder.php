<?php

namespace Database\Seeders;

use App\Models\SecurityQuestion;
use Illuminate\Database\Seeder;

class SecurityQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            [
                'question' => 'What is your mother\'s maiden name?',
                'description' => 'Your mother\'s family name before marriage',
                'is_active' => true,
            ],
            [
                'question' => 'In what city were you born?',
                'description' => 'Your birthplace city',
                'is_active' => true,
            ],
            [
                'question' => 'What is the name of your first pet?',
                'description' => 'Your first pet\'s name',
                'is_active' => true,
            ],
            [
                'question' => 'What is your favorite book?',
                'description' => 'Your favorite book title',
                'is_active' => true,
            ],
            [
                'question' => 'What is the name of the street you grew up on?',
                'description' => 'Street name from your childhood',
                'is_active' => true,
            ],
            [
                'question' => 'What is your favorite movie?',
                'description' => 'Your favorite movie title',
                'is_active' => true,
            ],
            [
                'question' => 'What was your first car?',
                'description' => 'Your first vehicle make/model',
                'is_active' => true,
            ],
            [
                'question' => 'What is your favorite sports team?',
                'description' => 'Your favorite sports team name',
                'is_active' => true,
            ],
            [
                'question' => 'What is the name of your best friend in high school?',
                'description' => 'Close friend\'s name from high school',
                'is_active' => true,
            ],
            [
                'question' => 'What is your favorite food?',
                'description' => 'Your favorite dish or food',
                'is_active' => true,
            ],
            [
                'question' => 'What is the name of your elementary school?',
                'description' => 'Your primary/elementary school name',
                'is_active' => true,
            ],
            [
                'question' => 'What is your favorite color?',
                'description' => 'Your favorite color',
                'is_active' => true,
            ],
            [
                'question' => 'What is the name of the company of your first job?',
                'description' => 'Your first employer\'s company name',
                'is_active' => true,
            ],
            [
                'question' => 'What is your favorite song?',
                'description' => 'Your favorite song title',
                'is_active' => true,
            ],
            [
                'question' => 'What was your childhood nickname?',
                'description' => 'A nickname you had as a child',
                'is_active' => true,
            ],
            [
                'question' => 'What is the name of the city where your father was born?',
                'description' => 'Your father\'s birthplace',
                'is_active' => true,
            ],
            [
                'question' => 'What is your favorite hobby?',
                'description' => 'Your favorite leisure activity',
                'is_active' => true,
            ],
            [
                'question' => 'What is the model of your first mobile phone?',
                'description' => 'Your first phone model',
                'is_active' => true,
            ],
            [
                'question' => 'What is your favorite vacation destination?',
                'description' => 'Your favorite place to visit',
                'is_active' => true,
            ],
            [
                'question' => 'What is the name of your high school?',
                'description' => 'Your secondary school name',
                'is_active' => true,
            ],
        ];

        foreach ($questions as $question) {
            SecurityQuestion::updateOrCreate(
                ['question' => $question['question']],
                $question
            );
        }
    }
}
