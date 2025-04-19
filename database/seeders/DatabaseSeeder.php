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

class DatabaseSeeder extends Seeder
{
    /* *
     * Seed the application's database.
     */
    /* public function run(): void
    {
        // Create admin + tabulator
        $admin = User::factory()->create(['role' => 'Admin', 'email' => 'admin@example.com']);
        $tabulator = User::factory()->create(['role' => 'Tabulator']);

        // Create an event
        $event = Event::factory()->create(['created_by' => $admin->user_id]);

        // Judges (linked to event)
        $judges = User::factory()->count(3)->state(['role' => 'Judge'])->create();
        foreach ($judges as $judgeUser) {
            Judge::factory()->create([
                'user_id' => $judgeUser->user_id,
                'event_id' => $event->event_id,
            ]);
        }

        // Categories for the event
        $categories = Category::factory()->count(3)->create(['event_id' => $event->event_id]);

        // Candidates for the event
        $candidates = Candidate::factory()->count(10)->create(['event_id' => $event->event_id]); */

        /* // Explicit test for duplicate candidate number with different sex
        $candidates = collect([
            Candidate::create([
                'event_id' => $event->event_id,
                'candidate_number' => '1',
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'sex' => 'male',
                'team' => 'Gladiators',
            ]),
            Candidate::create([
                'event_id' => $event->event_id,
                'candidate_number' => '1',
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'sex' => 'female',
                'team' => 'Gladiators',
            ]),
        ]); */

        /* // Scores
        $judgeIDs = Judge::where('event_id', $event->event_id)->pluck('judge_id');
        $categoryIDs = $categories->pluck('category_id');
        $candidateIDs = $candidates->pluck('candidate_id');

        foreach ($judgeIDs as $judge_id) {
            foreach ($candidateIDs as $candidate_id) {
                foreach ($categoryIDs as $category_id) {
                    Score::create([
                        'judge_id' => $judge_id,
                        'candidate_id' => $candidate_id,
                        'category_id' => $category_id,
                        'score' => rand(1, 10),
                    ]);
                }
            }
        }   
    } */

/*     public function run(): void
    {
        // Create Admin & Tabulator
        $admin = User::factory()->create([
            'role' => 'Admin',
            'email' => 'admin@example.com',
            'username' => 'admin',
        ]);

        $tabulator = User::factory()->create([
            'role' => 'Tabulator',
            'email' => 'tab@example.com',
            'username' => 'tabulator',
        ]);

        // Create 3 events: inactive, active, completed
        $inactiveEvent = Event::factory()->create([
            'event_name' => 'Mr. & Ms. Chill VSU',
            'status' => 'inactive',
            'created_by' => $admin->user_id,
        ]);

        $activeEvent = Event::factory()->create([
            'event_name' => 'Mr. & Ms. Active VSU',
            'status' => 'active',
            'created_by' => $admin->user_id,
        ]);

        $completedEvent = Event::factory()->create([
            'event_name' => 'Mr. & Ms. Completed VSU',
            'status' => 'completed',
            'created_by' => $admin->user_id,
        ]);

        // Create 3 judges for the active event
        $judges = User::factory()->count(3)->state(['role' => 'Judge'])->create();
        foreach ($judges as $judgeUser) {
            Judge::factory()->create([
                'user_id' => $judgeUser->user_id,
                'event_id' => $activeEvent->event_id,
            ]);
        }

        // Create judges for inactive and completed events
        $inactiveJudges = User::factory()->count(2)->state(['role' => 'Judge'])->create();
        foreach ($inactiveJudges as $user) {
            Judge::factory()->create([
                'user_id' => $user->user_id,
                'event_id' => $inactiveEvent->event_id,
            ]);
        }

        $completedJudges = User::factory()->count(2)->state(['role' => 'Judge'])->create();
        foreach ($completedJudges as $user) {
            Judge::factory()->create([
                'user_id' => $user->user_id,
                'event_id' => $completedEvent->event_id,
            ]);
        }

        // Assign categories to the active event
        $categories = Category::factory()->count(3)->create([
            'event_id' => $activeEvent->event_id,
        ]);

        // Assign categories to the inactive event
        $categories = Category::factory()->count(3)->create([
            'event_id' => $inactiveEvent->event_id,
        ]);

        // Assign categories to the completed event
        $categories = Category::factory()->count(3)->create([
            'event_id' => $completedEvent->event_id,
        ]);

        // Insert duplicate candidate numbers (Gladiators Test)
        $candidates = collect([
            Candidate::create([
                'event_id' => $activeEvent->event_id,
                'candidate_number' => '1',
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'sex' => 'male',
                'team' => 'Gladiators',
            ]),
            Candidate::create([
                'event_id' => $activeEvent->event_id,
                'candidate_number' => '1',
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'sex' => 'female',
                'team' => 'Gladiators',
            ]),
            Candidate::create([
                'event_id' => $activeEvent->event_id,
                'candidate_number' => '2',
                'first_name' => 'Jose',
                'last_name' => 'Dela Peña',
                'sex' => 'male',
                'team' => 'Sphinx',
            ]),
            Candidate::create([
                'event_id' => $activeEvent->event_id,
                'candidate_number' => '2',
                'first_name' => 'Mariana',
                'last_name' => 'Santisima',
                'sex' => 'female',
                'team' => 'Sphinx',
            ]),
        ]);

        // Seed scores for active event only
        $activeJudges = Judge::where('event_id', $activeEvent->event_id)->pluck('judge_id');
        $categoryIDs = $categories->pluck('category_id');
        $candidateIDs = $candidates->pluck('candidate_id');

        foreach ($activeJudges as $judge_id) {
            foreach ($candidateIDs as $candidate_id) {
                foreach ($categoryIDs as $category_id) {
                    Score::updateOrCreate(
                        [
                            'judge_id' => $judge_id,
                            'candidate_id' => $candidate_id,
                            'category_id' => $category_id,
                            'event_id' => $activeEvent->event_id,
                        ],
                        ['score' => rand(1, 10)]
                    );
                }
            }
        }

        // Create candidates for the completed event, but don't seed scores
        Candidate::factory()->count(5)->create([
            'event_id' => $completedEvent->event_id,
        ]);

        // Create candidates for the inactive event, but don't seed scores
        Candidate::factory()->count(5)->create([
            'event_id' => $inactiveEvent->event_id,
        ]);
    } */

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

    public function run(): void
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
        $judges->first()->update([
            'user_id' => $testUser->user_id,
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
    }

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
}
