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

        $eventConfigs = [
            [
                'name' => 'Grand Pageant Championship 2025',
                'venue' => 'VSU Grand Gymnasium',
                'status' => 'active',
                'division' => 'standard',
                'max_score' => 100,
                'days_offset' => 0,
                'scenarios' => ['finalized', 'active', 'pending'],
                'active_candidates' => 6,
            ],
            [
                'name' => 'Spring Beauty Contest 2025',
                'venue' => 'University Auditorium',
                'status' => 'completed',
                'division' => 'standard',
                'max_score' => 100,
                'days_offset' => -10,
                'scenarios' => ['finalized', 'finalized', 'finalized'],
                'active_candidates' => 10,
            ],
            [
                'name' => 'Male Excellence Competition',
                'venue' => 'Sports Complex Arena',
                'status' => 'inactive',
                'division' => 'male-only',
                'max_score' => 100,
                'days_offset' => 5,
                'scenarios' => ['pending', 'pending', 'pending'],
                'active_candidates' => 8,
            ],
        ];

        foreach ($eventConfigs as $index => $config) {
            $this->createEvent($admin, $tabulator, $config, $index);
        }
    }

    private function createEvent($admin, $tabulator, $config, $eventIndex = 0)
    {
        $event = Event::create([
            'event_name' => $config['name'],
            'venue' => $config['venue'],
            'start_date' => Carbon::now()->addDays($config['days_offset']),
            'end_date' => Carbon::now()->addDays($config['days_offset'] + 2),
            'description' => 'A prestigious competition showcasing talent, intelligence, and excellence.',
            'status' => $config['status'],
            'division' => $config['division'],
            'global_max_score' => $config['max_score'],
            'created_by' => $admin->user_id,
            'cover_photo' => null,
            'statisticians' => [['name' => 'Dr. Maria Santos']],
        ]);

        $judgePool = [
            ['Tracy', 'Johnson'], ['Raed', 'Al-Mansouri'], ['Derick', 'Thompson'],
            ['Lois', 'Anderson'], ['Kristine', 'Peterson'], ['Marcus', 'Chen'],
            ['Sofia', 'Rodriguez'], ['David', 'Kim'], ['Emma', 'Wilson']
        ];
        $judgesForEvent = array_slice($judgePool, 0, 5 + $eventIndex);

        $judges = collect();
        foreach ($judgesForEvent as $i => [$first, $last]) {
            $user = User::create([
                'username' => "judge_{$eventIndex}_{$i}",
                'email' => "judge_{$eventIndex}_{$i}@example.com",
                'first_name' => $first,
                'last_name' => $last,
                'role' => 'judge',
                'password' => Hash::make('judgepassword'),
            ]);

            $pin = strtoupper(Str::random(6));
            $judges->push(Judge::create([
                'user_id' => $user->user_id,
                'event_id' => $event->event_id,
                'pin_code' => $pin,
            ]));
        }

        $stages = collect();
        $stageConfigs = match ($config['division']) {
            'male-only' => [
                ['name' => 'Physical Fitness', 'top_count' => 8],
                ['name' => 'Talent & Interview', 'top_count' => 4],
                ['name' => 'Formal Wear Finals', 'top_count' => null],
            ],
            default => [
                ['name' => 'Preliminary Round', 'top_count' => 12],
                ['name' => 'Swimsuit/Talent Competition', 'top_count' => 6],
                ['name' => 'Evening Gown & Q&A', 'top_count' => null],
            ],
        };

        foreach ($stageConfigs as $i => $stageConfig) {
            $stages->push(Stage::create([
                'event_id' => $event->event_id,
                'stage_name' => $stageConfig['name'],
                'status' => $config['scenarios'][$i] ?? 'pending',
                'top_candidates_count' => $stageConfig['top_count'],
            ]));
        }

        $candidateData = [
            ['Alexander', 'Champion', 'Isabella', 'Grace', 'Team Phoenix'],
            ['Michael', 'Sterling', 'Sophia', 'Radiance', 'Team Nova'],
            ['William', 'Valor', 'Emma', 'Elegance', 'Team Aurora'],
            ['James', 'Prestige', 'Olivia', 'Brilliance', 'Team Stellar'],
            ['Benjamin', 'Excellence', 'Charlotte', 'Sophistication', 'Team Zenith'],
            ['Lucas', 'Distinction', 'Amelia', 'Magnificence', 'Team Apex'],
            ['Henry', 'Achievement', 'Harper', 'Splendor', 'Team Elite'],
            ['Sebastian', 'Success', 'Evelyn', 'Glamour', 'Team Prime'],
            ['Owen', 'Victory', 'Abigail', 'Allure', 'Team Supreme'],
            ['Theodore', 'Triumph', 'Emily', 'Enchantment', 'Team Ultimate'],
        ];

        $candidates = collect();
        foreach ($candidateData as $num => [$mFirst, $mLast, $fFirst, $fLast, $team]) {
            $id = $num + 1;
            if (in_array($config['division'], ['standard', 'male-only'])) {
                $candidates->push(Candidate::create([
                    'event_id' => $event->event_id,
                    'candidate_number' => $id,
                    'first_name' => $mFirst,
                    'last_name' => $mLast,
                    'sex' => 'M',
                    'team' => $team,
                    'is_active' => $id <= $config['active_candidates'] ? 1 : 0,
                    'photo' => null,
                ]));
            }

            if (in_array($config['division'], ['standard', 'female-only'])) {
                $candidates->push(Candidate::create([
                    'event_id' => $event->event_id,
                    'candidate_number' => $id,
                    'first_name' => $fFirst,
                    'last_name' => $fLast,
                    'sex' => 'F',
                    'team' => $team,
                    'is_active' => $id <= $config['active_candidates'] ? 1 : 0,
                    'photo' => null,
                ]));
            }
        }

        $categorySets = [
            'standard' => [
                ['Physical Fitness', 25], ['Stage Presence', 30],
                ['Communication Skills', 25], ['Overall Appeal', 20]
            ],
            'male-only' => [
                ['Conditioning', 30], ['Stage Impact', 40], ['Confidence', 30]
            ]
        ];

        $categoryData = $categorySets[$config['division']] ?? $categorySets['standard'];
        $categories = collect();
        foreach ($stages as $stage) {
            foreach ($categoryData as [$name, $weight]) {
                $categories->push(Category::create([
                    'event_id' => $event->event_id,
                    'stage_id' => $stage->stage_id,
                    'category_name' => $name,
                    'category_weight' => $weight,
                    'max_score' => $config['max_score'],
                    'status' => $stage->status === 'finalized' ? 'finalized' : 'pending',
                ]));
            }
        }

        foreach ($judges as $judge) {
            foreach ($candidates as $candidate) {
                foreach ($stages->where('status', 'finalized') as $stage) {
                    foreach ($categories->where('stage_id', $stage->stage_id) as $category) {
                        $score = round($config['max_score'] * 0.85, 1);
                        Score::create([
                            'event_id' => $event->event_id,
                            'judge_id' => $judge->judge_id,
                            'stage_id' => $stage->stage_id,
                            'category_id' => $category->category_id,
                            'candidate_id' => $candidate->candidate_id,
                            'score' => $score,
                            'status' => 'confirmed',
                            'comments' => 'Consistent and well-prepared performance.',
                        ]);
                    }
                }
            }
        }

        // ➕ Add partial scores only for active events
        if ($event->status === 'active') {
            $activeStage = $stages->firstWhere('status', 'active');
            if ($activeStage) {
                $activeCategories = $categories->where('stage_id', $activeStage->stage_id);
                $activeCandidates = $candidates->where('is_active', 1)->take(4);
                $scoringJudges = $judges->take(ceil($judges->count() * 0.6));

                foreach ($scoringJudges as $judge) {
                    foreach ($activeCandidates as $candidate) {
                        foreach ($activeCategories as $category) {
                            $score = round($config['max_score'] * 0.78, 1);
                            Score::create([
                                'event_id' => $event->event_id,
                                'judge_id' => $judge->judge_id,
                                'stage_id' => $activeStage->stage_id,
                                'category_id' => $category->category_id,
                                'candidate_id' => $candidate->candidate_id,
                                'score' => $score,
                                'status' => 'confirmed',
                                'comments' => 'Partial score under review.',
                            ]);
                        }
                    }
                }
            }
        }
    }
}
