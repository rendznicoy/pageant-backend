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

        // Create multiple events with different scenarios
        $eventConfigs = [
            [
                'name' => 'Grand Pageant Championship 2025',
                'venue' => 'VSU Grand Gymnasium',
                'status' => 'active',
                'division' => 'standard',
                'max_score' => 100,
                'days_offset' => 0,
                'scenarios' => ['finalized', 'finalized', 'active'],
                'active_candidates' => 6,
            ],
            [
                'name' => 'Spring Beauty Contest 2025',
                'venue' => 'University Auditorium',
                'status' => 'completed',
                'division' => 'standard',
                'max_score' => 50,
                'days_offset' => -10,
                'scenarios' => ['finalized', 'finalized', 'finalized'],
                'active_candidates' => 10,
            ],
            [
                'name' => 'Male Excellence Competition',
                'venue' => 'Sports Complex Arena',
                'status' => 'inactive',
                'division' => 'male-only',
                'max_score' => 75,
                'days_offset' => 5,
                'scenarios' => ['pending', 'pending', 'pending'],
                'active_candidates' => 8,
            ],
            [
                'name' => 'Female Empowerment Pageant',
                'venue' => 'Cultural Center Hall',
                'status' => 'active',
                'division' => 'female-only',
                'max_score' => 80,
                'days_offset' => 2,
                'scenarios' => ['finalized', 'active', 'pending'],
                'active_candidates' => 7,
            ],
        ];

        foreach ($eventConfigs as $index => $config) {
            $this->createEvent($admin, $tabulator, $config, $index);
        }

    }

    private function createEvent($admin, $tabulator, $config, $eventIndex)
    {
        // Create Event
        $event = Event::create([
            'event_name' => $config['name'],
            'venue' => $config['venue'],
            'start_date' => Carbon::now()->addDays($config['days_offset']),
            'end_date' => Carbon::now()->addDays($config['days_offset'] + 2),
            'description' => 'A prestigious competition showcasing talent, intelligence, and excellence.',
            'status' => $config['status'],
            'division' => $config['division'],
            'global_max_score' => $config['max_score'],
            'created_by' => $eventIndex % 2 === 0 ? $tabulator->user_id : $admin->user_id,
            'cover_photo' => null,
            'statisticians' => [
                ['name' => 'Dr. Maria Santos'],
                ['name' => 'Prof. John Dela Cruz'],
                ['name' => 'Ms. Ana Rodriguez']
            ]
        ]);

        // Create Judges - vary the number per event
        $judgeCount = [5, 3, 4, 6][$eventIndex]; // Different judge counts per event
        $judgeNames = [
            ['Tracy', 'Johnson'], ['Raed', 'Al-Mansouri'], ['Derick', 'Thompson'],
            ['Lois', 'Anderson'], ['Kristine', 'Peterson'], ['Marcus', 'Chen'],
            ['Sofia', 'Rodriguez'], ['David', 'Kim'], ['Emma', 'Wilson']
        ];
        
        $judges = collect();
        for ($i = 0; $i < $judgeCount; $i++) {
            [$firstName, $lastName] = $judgeNames[$i];
            
            $user = User::create([
                'username' => "judge{$eventIndex}_{$i}",
                'email' => "judge{$eventIndex}_{$i}@example.com",
                'first_name' => $firstName,
                'last_name' => $lastName,
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

        // Create Stages based on division and event type
        $stageConfigs = $this->getStageConfigs($config['division'], $eventIndex);
        $stages = collect();

        foreach ($stageConfigs as $i => $stageConfig) {
            $stages->push(Stage::create([
                'event_id' => $event->event_id,
                'stage_name' => $stageConfig['name'],
                'status' => $config['scenarios'][$i] ?? 'pending',
                'top_candidates_count' => $stageConfig['top_count'] ?? null,
            ]));
        }

        // Create Candidates based on division
        $candidates = $this->createCandidates($event, $config);

        // Create Categories with appropriate max scores
        $categories = $this->createCategories($event, $stages, $config);

        // Generate Scores
        $this->generateScores($event, $judges, $candidates, $stages, $categories, $config);
    }

    private function getStageConfigs($division, $eventIndex)
    {
        $configs = [
            'standard' => [
                ['name' => 'Preliminary Round', 'top_count' => 12],
                ['name' => 'Swimsuit/Talent Competition', 'top_count' => 6],
                ['name' => 'Evening Gown & Q&A', 'top_count' => null],
            ],
            'male-only' => [
                ['name' => 'Physical Fitness', 'top_count' => 8],
                ['name' => 'Talent & Interview', 'top_count' => 4],
                ['name' => 'Formal Wear Finals', 'top_count' => null],
            ],
            'female-only' => [
                ['name' => 'Introduction & Casual Wear', 'top_count' => 10],
                ['name' => 'Talent Competition', 'top_count' => 5],
                ['name' => 'Evening Gown & Q&A', 'top_count' => null],
            ],
        ];

        return $configs[$division] ?? $configs['standard'];
    }

    private function createCandidates($event, $config)
    {
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
        $activeCount = $config['active_candidates'];

        foreach ($candidateData as $num => $data) {
            [$maleName, $maleLast, $femaleName, $femaleLast, $teamName] = $data;
            $candidateNum = $num + 1;

            // Create male candidate for standard or male-only divisions
            if (in_array($config['division'], ['standard', 'male-only'])) {
                $candidates->push(Candidate::create([
                    'event_id' => $event->event_id,
                    'candidate_number' => $candidateNum,
                    'first_name' => $maleName,
                    'last_name' => $maleLast,
                    'sex' => 'M',
                    'team' => $teamName,
                    'is_active' => $candidateNum <= $activeCount ? 1 : 0,
                    'photo' => null,
                ]));
            }

            // Create female candidate for standard or female-only divisions
            if (in_array($config['division'], ['standard', 'female-only'])) {
                $candidates->push(Candidate::create([
                    'event_id' => $event->event_id,
                    'candidate_number' => $candidateNum,
                    'first_name' => $femaleName,
                    'last_name' => $femaleLast,
                    'sex' => 'F',
                    'team' => $teamName,
                    'is_active' => $candidateNum <= $activeCount ? 1 : 0,
                    'photo' => null,
                ]));
            }
        }

        return $candidates;
    }

    private function createCategories($event, $stages, $config)
    {
        $categoryDataByDivision = [
            'standard' => [
                [
                    ['Physical Fitness', 25],
                    ['Stage Presence', 30],
                    ['Communication Skills', 25],
                    ['Overall Appeal', 20]
                ],
                [
                    ['Physique & Fitness', 40],
                    ['Confidence & Poise', 30],
                    ['Stage Projection', 20],
                    ['Overall Presentation', 10]
                ],
                [
                    ['Evening Gown Presentation', 30],
                    ['Intelligence & Articulation', 35],
                    ['Confidence & Composure', 20],
                    ['Overall Excellence', 15]
                ]
            ],
            'male-only' => [
                [
                    ['Physical Conditioning', 40],
                    ['Athletic Performance', 35],
                    ['Stage Presence', 25]
                ],
                [
                    ['Talent Performance', 50],
                    ['Interview Skills', 30],
                    ['Confidence', 20]
                ],
                [
                    ['Formal Wear Presentation', 35],
                    ['Final Interview', 40],
                    ['Overall Excellence', 25]
                ]
            ],
            'female-only' => [
                [
                    ['Introduction & Poise', 30],
                    ['Casual Wear Presentation', 35],
                    ['Personality', 35]
                ],
                [
                    ['Talent Performance', 60],
                    ['Stage Presence', 40]
                ],
                [
                    ['Evening Gown', 30],
                    ['Question & Answer', 45],
                    ['Overall Excellence', 25]
                ]
            ]
        ];

        $categoryData = $categoryDataByDivision[$config['division']] ?? $categoryDataByDivision['standard'];
        $categories = collect();

        foreach ($stages as $stageIndex => $stage) {
            if (isset($categoryData[$stageIndex])) {
                foreach ($categoryData[$stageIndex] as [$catName, $weight]) {
                    $categories->push(Category::create([
                        'event_id' => $event->event_id,
                        'stage_id' => $stage->stage_id,
                        'category_name' => $catName,
                        'category_weight' => $weight,
                        'max_score' => $config['max_score'], // Use event's max score
                        'status' => $stage->status === 'finalized' ? 'finalized' : 'pending',
                    ]));
                }
            }
        }

        return $categories;
    }

    private function generateScores($event, $judges, $candidates, $stages, $categories, $config)
    {
        // Create judge profiles for consistent scoring patterns
        $judgeProfiles = [];
        foreach ($judges as $index => $judge) {
            $judgeProfiles[$index] = [
                'bias' => rand(-3, 3),
                'strictness' => 0.9 + (rand(0, 20) / 100), // 0.9 to 1.1
            ];
        }

        foreach ($judges as $judgeIndex => $judge) {
            $judgeProfile = $judgeProfiles[$judgeIndex];
            
            foreach ($candidates as $candidateIndex => $candidate) {
                // Skip inactive candidates for non-final stages
                if (!$candidate->is_active && $stages->where('status', '!=', 'finalized')->count() > 0) {
                    continue;
                }
                
                foreach ($stages->where('status', 'finalized') as $stageIndex => $stage) {
                    $stageCategories = $categories->where('stage_id', $stage->stage_id);
                    
                    $candidateSkill = $this->getCandidateSkillLevel($candidateIndex, $stageIndex, $config['max_score']);
                    
                    foreach ($stageCategories as $category) {
                        $categoryMultiplier = $this->getCategoryMultiplier($category->category_name, $candidateIndex);
                        
                        // Calculate score as percentage of max score
                        $basePercentage = ($candidateSkill / 100) * $categoryMultiplier * $judgeProfile['strictness'];
                        $baseScore = $basePercentage * $config['max_score'] + $judgeProfile['bias'];
                        
                        $variation = rand(-3, 3);
                        $finalScore = max($config['max_score'] * 0.6, min($config['max_score'], round($baseScore + $variation, 1)));
                        
                        Score::create([
                            'event_id' => $event->event_id,
                            'judge_id' => $judge->judge_id,
                            'stage_id' => $stage->stage_id,
                            'category_id' => $category->category_id,
                            'candidate_id' => $candidate->candidate_id,
                            'score' => $finalScore,
                            'status' => 'confirmed',
                            'comments' => $this->getContextualComment($finalScore, $category->category_name, $config['max_score']),
                        ]);
                    }
                }
            }
        }

        // Add partial scores for active stages
        $activeStage = $stages->where('status', 'active')->first();
        if ($activeStage) {
            $this->createPartialScores($event, $judges, $candidates, $activeStage, $categories, $config, $judgeProfiles);
        }
    }

    private function createPartialScores($event, $judges, $candidates, $activeStage, $categories, $config, $judgeProfiles)
    {
        $activeCategories = $categories->where('stage_id', $activeStage->stage_id);
        $activeCandidates = $candidates->where('is_active', true)->take(4);
        
        // Only some judges have scored
        $judgesWhoScored = $judges->take(ceil($judges->count() * 0.6));
        
        foreach ($judgesWhoScored as $judgeIndex => $judge) {
            foreach ($activeCandidates as $candidateIndex => $candidate) {
                foreach ($activeCategories as $category) {
                    $judgeProfile = $judgeProfiles[$judgeIndex];
                    $candidateSkill = $this->getCandidateSkillLevel($candidateIndex, 2, $config['max_score']);
                    $categoryMultiplier = $this->getCategoryMultiplier($category->category_name, $candidateIndex);
                    
                    $basePercentage = ($candidateSkill / 100) * $categoryMultiplier * $judgeProfile['strictness'];
                    $baseScore = $basePercentage * $config['max_score'] + $judgeProfile['bias'];
                    
                    $variation = rand(-2, 4);
                    $finalScore = max($config['max_score'] * 0.7, min($config['max_score'], round($baseScore + $variation, 1)));
                    
                    Score::create([
                        'event_id' => $event->event_id,
                        'judge_id' => $judge->judge_id,
                        'stage_id' => $activeStage->stage_id,
                        'category_id' => $category->category_id,
                        'candidate_id' => $candidate->candidate_id,
                        'score' => $finalScore,
                        'status' => 'confirmed',
                        'comments' => $this->getContextualComment($finalScore, $category->category_name, $config['max_score']),
                    ]);
                }
            }
        }
    }

    private function getCandidateSkillLevel($candidateIndex, $stageIndex, $maxScore)
    {
        $baseSkills = [95, 89, 92, 85, 88, 91, 82, 87, 84, 86, 80, 83, 78, 81, 76, 79, 75, 77, 74, 76];
        $baseSkill = $baseSkills[$candidateIndex % 20];
        $progression = $stageIndex * 1.5;
        
        return min(100, $baseSkill + $progression);
    }
    
    private function getCategoryMultiplier($categoryName, $candidateIndex)
    {
        $multipliers = [
            'Physical Fitness' => [1.05, 0.98, 1.02, 0.95, 1.00, 1.03, 0.97, 1.01, 0.99, 1.04],
            'Stage Presence' => [1.03, 1.05, 0.98, 1.02, 0.96, 1.01, 1.04, 0.99, 1.03, 0.97],
            'Communication Skills' => [0.98, 1.02, 1.05, 1.03, 1.01, 0.97, 1.00, 1.04, 0.96, 1.02],
            'Intelligence & Articulation' => [1.04, 1.01, 1.03, 1.05, 0.98, 1.02, 0.96, 1.00, 1.04, 0.99],
            'Evening Gown Presentation' => [1.02, 0.99, 1.01, 0.97, 1.04, 1.00, 1.03, 0.98, 1.02, 1.01],
            'Physique & Fitness' => [1.05, 0.97, 1.03, 0.99, 1.01, 1.04, 0.96, 1.02, 1.00, 1.03],
        ];
        
        $index = $candidateIndex % 10;
        return $multipliers[$categoryName][$index] ?? 1.0;
    }
    
    private function getContextualComment($score, $categoryName, $maxScore)
    {
        $percentage = ($score / $maxScore) * 100;
        
        $comments = [
            'Physical Fitness' => [
                'Excellent physical conditioning and fitness level',
                'Strong athletic build and great stamina', 
                'Good overall fitness, room for improvement',
                'Adequate fitness level for competition',
                'Needs more focus on physical conditioning'
            ],
            'Stage Presence' => [
                'Commands the stage with natural confidence',
                'Strong stage presence and audience connection',
                'Good stage awareness and positioning',
                'Decent stage presence, could be more dynamic',
                'Stage presence needs development'
            ],
            // Add more categories as needed...
        ];
        
        $categoryComments = $comments[$categoryName] ?? [
            'Outstanding performance in this category',
            'Strong showing with great potential',
            'Good effort with room for growth',
            'Adequate performance overall',
            'Needs improvement in this area'
        ];
        
        $commentIndex = $percentage >= 90 ? 0 : ($percentage >= 80 ? 1 : ($percentage >= 70 ? 2 : ($percentage >= 60 ? 3 : 4)));
        
        return $categoryComments[$commentIndex];
    }
 }