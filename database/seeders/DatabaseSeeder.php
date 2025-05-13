<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use App\Models\Judge;
use App\Models\Stage;
use App\Models\Category;
use App\Models\Candidate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Tabulator user
        $tabulator = User::create([
            'username' => 'TabulatorUser',
            'email' => 'tabulator@example.com',
            'password' => Hash::make('tabulatorpassword'),
            'first_name' => 'Tab',
            'last_name' => 'User',
            'role' => 'tabulator',
        ]);

        // Create one active event with complete details
        $event = Event::create([
            'event_name' => 'Annual Talent Competition 2025',
            'event_code' => 'ATC2025',
            'start_date' => Carbon::now()->format('Y-m-d H:i:s'),
            'end_date' => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
            'description' => 'A grand talent competition showcasing various skills and performances.',
            'status' => 'active',
            'created_by' => $tabulator->user_id,
            'cover_photo' => null, // Can add a fake path if needed for testing
        ]);

        // Create 3 judges
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

        // Create 10 candidates (5 male, 5 female)
        $candidates = collect();
        $teams = ['Team Alpha', 'Team Beta', 'Team Gamma'];
        foreach (range(1, 5) as $num) {
            $candidates->push(Candidate::create([
                'event_id' => $event->event_id,
                'candidate_number' => $num,
                'first_name' => 'Male' . $num,
                'last_name' => 'Candidate',
                'sex' => 'male',
                'team' => $teams[array_rand($teams)],
                'photo' => null, // Can add a fake path if needed
            ]));
            $candidates->push(Candidate::create([
                'event_id' => $event->event_id,
                'candidate_number' => $num + 5,
                'first_name' => 'Female' . $num,
                'last_name' => 'Candidate',
                'sex' => 'female',
                'team' => $teams[array_rand($teams)],
                'photo' => null, // Can add a fake path if needed
            ]));
        }

        // Create 3 stages
        $stages = collect();
        foreach (['Preliminary', 'Semi-Final', 'Final'] as $stageName) {
            $stages->push(Stage::create([
                'event_id' => $event->event_id,
                'stage_name' => $stageName,
            ]));
        }

        // Create 3 categories per stage
        $categories = collect();
        $categoryNames = ['Performance', 'Creativity', 'Presentation'];
        $weights = [40, 30, 30]; // Sum to 100 for simplicity
        foreach ($stages as $stage) {
            foreach (range(0, 2) as $index) {
                $categories->push(Category::create([
                    'event_id' => $event->event_id,
                    'stage_id' => $stage->stage_id,
                    'category_name' => $categoryNames[$index],
                    'category_weight' => $weights[$index],
                    'max_score' => 10,
                ]));
            }
        }
    }
}