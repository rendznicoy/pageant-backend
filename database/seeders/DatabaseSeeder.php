<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use App\Models\Judge;
use App\Models\Stage;
use App\Models\Category;
use App\Models\Candidate;
use App\Models\Score;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        $admin = User::create([
            'username' => 'AdminUser',
            'email' => 'admin@example.com',
            'password' => Hash::make('adminpassword'),
            'first_name' => 'Admin',
            'last_name' => 'User',
            'role' => 'admin',
        ]);

        // Create Tabulator
        $tabulator = User::create([
            'username' => 'TabulatorUser',
            'email' => 'tabulator@example.com',
            'password' => Hash::make('tabulatorpassword'),
            'first_name' => 'Tab',
            'last_name' => 'User',
            'role' => 'tabulator',
        ]);

        // Create Event
        $event = Event::create([
            'event_name' => 'Grand Pageant 2025',
            'event_code' => 'GP2025',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays(2),
            'description' => 'A prestigious event highlighting beauty, intelligence, and grace.',
            'status' => 'active',
            'created_by' => $tabulator->user_id,
            'cover_photo' => null,
        ]);

        // Create Judges
        $judges = collect();
        foreach (['Simon', 'Clara', 'James'] as $index => $name) {
            $user = User::create([
                'username' => 'judge' . $index,
                'email' => "judge{$index}@example.com",
                'first_name' => $name,
                'last_name' => 'Judge',
                'role' => 'judge',
                'password' => null,
            ]);

            do {
                $pin = strtoupper(Str::random(6));
            } while (Judge::where('pin_code', $pin)->exists());

            $judges->push(Judge::create([
                'user_id' => $user->user_id,
                'event_id' => $event->event_id,
                'pin_code' => $pin,
            ]));
        }

        // Create 10 Candidate Pairs (10 male + 10 female), each pair with unique team
        $candidates = collect();
        foreach (range(1, 10) as $num) {
            $teamName = "Team Pair {$num}";

            $candidates->push(Candidate::create([
                'event_id' => $event->event_id,
                'candidate_number' => $num,
                'first_name' => 'Male' . $num,
                'last_name' => 'Candidate',
                'sex' => 'male',
                'team' => $teamName,
                'photo' => null,
            ]));

            $candidates->push(Candidate::create([
                'event_id' => $event->event_id,
                'candidate_number' => $num,
                'first_name' => 'Female' . $num,
                'last_name' => 'Candidate',
                'sex' => 'female',
                'team' => $teamName,
                'photo' => null,
            ]));
        }

        // Create 3 Stages
        $stageNames = ['Preliminary', 'Swimsuit Show', 'Evening Gown'];
        $stages = collect();
        foreach ($stageNames as $index => $name) {
            $stages->push(Stage::create([
                'event_id' => $event->event_id,
                'stage_name' => $name,
                'status' => $index === 0 ? 'finalized' : 'pending',
            ]));
        }

        // Create 4 Categories per Stage
        $categoryNames = ['Beauty & Physique', 'Swim Wear', 'Formal Wear', 'Q & A'];
        $categories = collect();

        foreach ($stages as $stage) {
            foreach ($categoryNames as $catName) {
                $categories->push(Category::create([
                    'event_id' => $event->event_id,
                    'stage_id' => $stage->stage_id,
                    'category_name' => $catName,
                    'category_weight' => 25,
                    'max_score' => 100,
                    'status' => $stage->status === 'finalized' ? 'finalized' : 'pending',
                ]));
            }
        }

        // Fill Scores for Finalized Stage only
        $finalStage = $stages->firstWhere('status', 'finalized');
        $finalCategories = $categories->where('stage_id', $finalStage->stage_id);

        foreach ($judges as $judge) {
            foreach ($candidates as $candidate) {
                foreach ($finalCategories as $category) {
                    Score::create([
                        'event_id' => $event->event_id,
                        'judge_id' => $judge->judge_id,
                        'stage_id' => $finalStage->stage_id,
                        'category_id' => $category->category_id,
                        'candidate_id' => $candidate->candidate_id,
                        'score' => rand(60, 100),
                        'status' => 'confirmed',
                    ]);
                }
            }
        }
    }
}