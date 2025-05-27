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
            // Add this new event configuration for testing finalization
            [
                'name' => 'Ready to Finalize Event 2025',
                'venue' => 'Test Arena',
                'status' => 'active',
                'division' => 'standard',
                'max_score' => 100,
                'days_offset' => -1,
                'scenarios' => ['finalized', 'finalized', 'finalized'], // All stages finalized
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

        // Define candidate performance levels for more realistic scoring
        $candidatePerformanceLevels = [
            1 => ['base' => 0.92, 'variance' => 0.04], // Excellent performer
            2 => ['base' => 0.88, 'variance' => 0.05], // Very good
            3 => ['base' => 0.85, 'variance' => 0.06], // Good
            4 => ['base' => 0.82, 'variance' => 0.05], // Above average
            5 => ['base' => 0.78, 'variance' => 0.07], // Average
            6 => ['base' => 0.75, 'variance' => 0.06], // Below average
            7 => ['base' => 0.72, 'variance' => 0.08], // Needs improvement
            8 => ['base' => 0.70, 'variance' => 0.07], // Poor
            9 => ['base' => 0.68, 'variance' => 0.09], // Very poor
            10 => ['base' => 0.65, 'variance' => 0.08], // Worst performer
        ];

        // Judge bias/preferences (some judges are stricter/more generous)
        $judgeBiases = [];
        foreach ($judges as $index => $judge) {
            $judgeBiases[$judge->judge_id] = [
                'generosity' => 0.97 + ($index * 0.015), // Range from 0.97 to ~1.06
                'consistency' => 0.02 + ($index * 0.005), // Range from 0.02 to ~0.06
            ];
        }

        // Generate scores for finalized stages
        foreach ($judges as $judge) {
            foreach ($candidates as $candidate) {
                foreach ($stages->where('status', 'finalized') as $stage) {
                    foreach ($categories->where('stage_id', $stage->stage_id) as $category) {
                        $score = $this->generateScore(
                            $candidate->candidate_number,
                            $judge->judge_id,
                            $category->category_name,
                            $config['max_score'],
                            $candidatePerformanceLevels,
                            $judgeBiases
                        );

                        Score::create([
                            'event_id' => $event->event_id,
                            'judge_id' => $judge->judge_id,
                            'stage_id' => $stage->stage_id,
                            'category_id' => $category->category_id,
                            'candidate_id' => $candidate->candidate_id,
                            'score' => $score,
                            'status' => 'confirmed',
                            'comments' => $this->generateComment($score, $config['max_score']),
                        ]);
                    }
                }
            }
        }

        // Add partial scores for active events with varied scoring
        if ($event->status === 'active') {
            $activeStage = $stages->firstWhere('status', 'active');
            if ($activeStage) {
                $activeCategories = $categories->where('stage_id', $activeStage->stage_id);
                $activeCandidates = $candidates->where('is_active', 1)->take(4);
                $scoringJudges = $judges->take(ceil($judges->count() * 0.6));

                foreach ($scoringJudges as $judge) {
                    foreach ($activeCandidates as $candidate) {
                        foreach ($activeCategories as $category) {
                            // For active stage, add some improvement/decline from previous performance
                            $score = $this->generateScore(
                                $candidate->candidate_number,
                                $judge->judge_id,
                                $category->category_name,
                                $config['max_score'],
                                $candidatePerformanceLevels,
                                $judgeBiases,
                                0.03 // Additional variance for active stage
                            );

                            Score::create([
                                'event_id' => $event->event_id,
                                'judge_id' => $judge->judge_id,
                                'stage_id' => $activeStage->stage_id,
                                'category_id' => $category->category_id,
                                'candidate_id' => $candidate->candidate_id,
                                'score' => $score,
                                'status' => 'confirmed',
                                'comments' => $this->generateComment($score, $config['max_score']),
                            ]);
                        }
                    }
                }
            }
        }
    }

    // In the generateScore method of DatabaseSeeder.php
    private function generateScore($candidateNumber, $judgeId, $categoryName, $maxScore, $performanceLevels, $judgeBiases, $extraVariance = 0)
    {
        // Get candidate's base performance level
        $performance = $performanceLevels[$candidateNumber] ?? $performanceLevels[5];
        
        // Get judge bias
        $judgeBias = $judgeBiases[$judgeId] ?? ['generosity' => 1.0, 'consistency' => 0.03];
        
        // Add judge-specific preferences (some judges prefer certain candidates)
        $judgePreference = 1.0;
        $judgeIndex = $judgeId % 5; // Cycle through judge preferences
        if (($judgeIndex === 0 && in_array($candidateNumber, [1, 3])) ||
            ($judgeIndex === 1 && in_array($candidateNumber, [2, 4])) ||
            ($judgeIndex === 2 && in_array($candidateNumber, [1, 5])) ||
            ($judgeIndex === 3 && in_array($candidateNumber, [3, 6])) ||
            ($judgeIndex === 4 && in_array($candidateNumber, [2, 6]))) {
            $judgePreference = 1.1; // 10% bonus for preferred candidates
        }
        
        // Category-specific adjustments
        $categoryMultiplier = 1.0;
        switch ($categoryName) {
            case 'Physical Fitness':
            case 'Conditioning':
                if (in_array($candidateNumber, [1, 3, 5])) $categoryMultiplier = 1.08;
                if (in_array($candidateNumber, [2, 4, 6])) $categoryMultiplier = 0.95;
                break;
            case 'Stage Presence':
            case 'Stage Impact':
                if (in_array($candidateNumber, [2, 4, 6])) $categoryMultiplier = 1.06;
                if (in_array($candidateNumber, [1, 3, 5])) $categoryMultiplier = 0.97;
                break;
            case 'Communication Skills':
                if (in_array($candidateNumber, [1, 2, 4])) $categoryMultiplier = 1.05;
                if (in_array($candidateNumber, [3, 5, 6])) $categoryMultiplier = 0.96;
                break;
            case 'Overall Appeal':
            case 'Confidence':
                if (in_array($candidateNumber, [1, 2])) $categoryMultiplier = 1.03;
                if (in_array($candidateNumber, [5, 6])) $categoryMultiplier = 0.98;
                break;
        }
        
        // Calculate base score with judge preference
        $baseScore = $performance['base'] * $categoryMultiplier * $judgeBias['generosity'] * $judgePreference * $maxScore;
        
        // Add random variance
        $totalVariance = $performance['variance'] + $judgeBias['consistency'] + $extraVariance;
        $variance = (rand(-100, 100) / 100) * $totalVariance * $maxScore;
        
        $finalScore = $baseScore + $variance;
        
        // Ensure score is within bounds
        $finalScore = max(0, min($maxScore, $finalScore));
        
        return round($finalScore, 1);
    }

    private function generateComment($score, $maxScore)
    {
        $percentage = ($score / $maxScore) * 100;
        
        if ($percentage >= 90) {
            return collect([
                'Outstanding performance with exceptional execution.',
                'Truly impressive display of skill and confidence.',
                'Exemplary presentation in all aspects.',
                'Remarkable performance that stands out.',
            ])->random();
        } elseif ($percentage >= 80) {
            return collect([
                'Strong performance with good technique.',
                'Well-executed with room for minor improvements.',
                'Solid presentation with confident delivery.',
                'Good performance showing clear preparation.',
            ])->random();
        } elseif ($percentage >= 70) {
            return collect([
                'Adequate performance with some areas needing work.',
                'Shows potential but needs more consistency.',
                'Fair execution with room for improvement.',
                'Average performance with mixed results.',
            ])->random();
        } else {
            return collect([
                'Performance needs significant improvement.',
                'Requires more preparation and practice.',
                'Below expectations in several areas.',
                'Needs to work on fundamental skills.',
            ])->random();
        }
    }
}