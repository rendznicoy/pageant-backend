<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use App\Models\Judge;
use App\Models\Score;
use App\Models\Category;
use App\Models\Candidate;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /* public function run(): void
    {
        // Create an event and immediately retrieve it from the database to ensure we have the correct ID
        $event = Event::factory()->active()->create();
        $event = Event::find($event->event_id); // Refresh to get the actual database-assigned ID

        // Create judges with the refreshed event ID
        $judges = Judge::factory()
            ->count(5)
            ->create([
                'event_id' => $event->event_id // Use the primary key value directly
            ]);

        // Create categories
        $categories = Category::factory()->count(3)->create();

        // Shared candidate numbering system for this event
        $candidateNumbers = range(1, 5); // Assuming 5 candidates per gender

        // Create male candidates with shared candidate numbers
        $maleCandidates = collect($candidateNumbers)->map(function ($number) use ($event) {
            return Candidate::factory()->male()->create([
                'candidate_number' => $number,
                'event_id' => $event->event_id, // Associate with the current event
            ]);
        });

        // Create female candidates with shared candidate numbers
        $femaleCandidates = collect($candidateNumbers)->map(function ($number) use ($event) {
            return Candidate::factory()->female()->create([
                'candidate_number' => $number,
                'event_id' => $event->event_id, // Associate with the current event
            ]);
        });

        // Assign scores to candidates in each category
        foreach ($judges as $judge) {
            foreach ($categories as $category) {
                foreach ($maleCandidates->concat($femaleCandidates) as $candidate) {
                    Score::factory()->create([
                        'score' => rand(1, 10),
                    ]);
                }
            }
        }
    } */

    /* public function run(): void
    {
        // Create a test user you can log in with on Postman
        $testUser = User::create([
            'username' => 'TestUser',
            'email' => 'testuser@example.com',
            'password' => Hash::make('testpassword'), // Must be hashed
            'role' => 'tabulator', // or 'admin' depending on your logic
            'first_name' => 'Test',
            'last_name' => 'User',
        ]);

        // Create a test user you can log in with on Postman
        $testUser = User::create([
            'username' => 'AdminUser',
            'email' => 'adminuser@example.com',
            'password' => Hash::make('adminpassword'), // Must be hashed
            'role' => 'admin', // or 'admin' depending on your logic
            'first_name' => 'Admin',
            'last_name' => 'User',
        ]);

        // Create an event owned by test user
        $event = Event::factory()->active()->create([
            'created_by' => $testUser->user_id,
        ]);

        // Re-fetch event (if needed)
        $event = Event::find($event->event_id);

        // Create judges and link first one to test user
        $judges = Judge::factory()->count(5)->create([
            'event_id' => $event->event_id,
        ]);

        // Link first judge to test user for testing
        $judgeUser = User::factory()->create([
            'username' => 'JudgeUser',
            'email' => 'judgeuser@example.com',
            'password' => Hash::make('judgepassword'),
            'role' => 'judge',
            'first_name' => 'Judge',
            'last_name' => 'User',
        ]);

        // Create judges and assign one to the judge user
        $judges = Judge::factory()->count(5)->create([
            'event_id' => $event->event_id,
        ]);

        $judges->first()->update([
            'user_id' => $judgeUser->user_id,
        ]);

        // Create categories linked to the event
        $categories = Category::factory()->count(3)->create([
            'event_id' => $event->event_id,
        ]);

        // Candidate numbers
        $candidateNumbers = range(1, 5);

        // Male + Female candidates
        $maleCandidates = collect($candidateNumbers)->map(fn($num) => 
            Candidate::factory()->male()->create([
                'candidate_number' => $num,
                'event_id' => $event->event_id,
            ])
        );

        $femaleCandidates = collect($candidateNumbers)->map(fn($num) =>
            Candidate::factory()->female()->create([
                'candidate_number' => $num,
                'event_id' => $event->event_id,
            ])
        );

        // Assign scores for all judges, categories, and candidates
        foreach ($judges as $judge) {
            foreach ($categories as $category) {
                foreach ($maleCandidates->concat($femaleCandidates) as $candidate) {
                    Score::factory()->create([
                        'judge_id' => $judge->judge_id,
                        'candidate_id' => $candidate->candidate_id,
                        'category_id' => $category->category_id,
                        'score' => rand(1, 10),
                    ]);
                }
            }
        }
    } */

    /* public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'testuser',
            'password' => Hash::make('password'), // Must be hashed
            'role' => 'tabulator', // or 'admin' depending on your logic
            'email' => 'test@example.com',
        ]);
    } */

    public function run(): void
    {
        // Create fixed users
        $admin = User::create([
            'username' => 'AdminUser',
            'email' => 'admin@example.com',
            'password' => Hash::make('adminpassword'),
            'first_name' => 'Admin',
            'last_name' => 'User',
            'role' => 'admin',
        ]);

        $tabulator = User::create([
            'username' => 'TabulatorUser',
            'email' => 'tabulator@example.com',
            'password' => Hash::make('tabulatorpassword'),
            'first_name' => 'Tab',
            'last_name' => 'User',
            'role' => 'tabulator',
        ]);

        // Create one event for each status
        $eventStatuses = ['active', 'inactive', 'completed'];
        $events = [];

        foreach ($eventStatuses as $status) {
            $events[$status] = Event::factory()->create([
                'status' => $status,
                'created_by' => $tabulator->user_id,
            ]);
        }

        // Use only the active event for the full scoring setup
        $event = $events['active'];

        // Create 5 judges for the active event
        $judges = collect();
        for ($i = 0; $i < 5; $i++) {
            $user = User::create([
                'username' => 'judge' . $i,
                'email' => "judge{$i}@example.com",
                'first_name' => 'Judge' . $i,
                'last_name' => 'Lastname' . $i,
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

        // Create 3 categories for the active event
        $categories = Category::factory()->count(3)->create([
            'event_id' => $event->event_id,
        ]);

        // Create 5 male and 5 female candidates
        $candidates = collect();
        foreach (range(1, 5) as $num) {
            $candidates->push(Candidate::factory()->male()->create([
                'candidate_number' => $num,
                'event_id' => $event->event_id,
            ]));
            $candidates->push(Candidate::factory()->female()->create([
                'candidate_number' => $num,
                'event_id' => $event->event_id,
            ]));
        }

        // Create scores for each combination
        foreach ($judges as $judge) {
            foreach ($categories as $category) {
                foreach ($candidates as $candidate) {
                    Score::create([
                        'judge_id' => $judge->judge_id,
                        'event_id' => $event->event_id,
                        'candidate_id' => $candidate->candidate_id,
                        'category_id' => $category->category_id,
                        'score' => rand(1, 10),
                    ]);
                }
            }
        }
    }
}
