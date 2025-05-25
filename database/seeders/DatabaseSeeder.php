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

        // Create Event with Statisticians
        $event = Event::create([
            'event_name' => 'Grand Pageant Championship 2025',
            'venue' => 'VSU Grand Gymnasium',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays(2),
            'description' => 'A prestigious beauty pageant showcasing talent, intelligence, and grace.',
            'status' => 'active',
            'division' => 'standard',
            'created_by' => $tabulator->user_id,
            'cover_photo' => null,
            'statisticians' => [
                ['name' => 'Dr. Maria Santos'],
                ['name' => 'Prof. John Dela Cruz'],
                ['name' => 'Ms. Ana Rodriguez']
            ]
        ]);

        // Create 5 Judges with realistic names for enhanced scoring
        $judgeNames = [
            ['Tracy', 'Johnson'],
            ['Raed', 'Al-Mansouri'],
            ['Derick', 'Thompson'],
            ['Lois', 'Anderson'],
            ['Kristine', 'Peterson']
        ];
        
        $judges = collect();
        foreach ($judgeNames as $index => [$firstName, $lastName]) {
            $user = User::create([
                'username' => 'judge' . $index,
                'email' => "judge{$index}@example.com",
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

        // Test enhanced scoring with multiple scenarios
        $statusMap = ['finalized', 'finalized', 'active']; // First two completed, third active
        $activeCount = 6; // Top 6 from previous rounds

        // Create 3 Stages
        $stageNames = ['Preliminary Round', 'Swimsuit Competition', 'Evening Gown & Q&A'];
        $stages = collect();

        foreach ($stageNames as $i => $name) {
            $stages->push(Stage::create([
                'event_id' => $event->event_id,
                'stage_name' => $name,
                'status' => $statusMap[$i],
                'top_candidates_count' => $i === 0 ? 12 : ($i === 1 ? 6 : null),
            ]));
        }

        // Create diverse candidate pairs with realistic names and teams
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
        foreach ($candidateData as $num => $data) {
            [$maleName, $maleLast, $femaleName, $femaleLast, $teamName] = $data;
            $candidateNum = $num + 1;

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

        // Create 4 Categories per Stage with varying weights
        $categoryDataByStage = [
            // Preliminary Round
            [
                ['Physical Fitness', 25],
                ['Stage Presence', 30],
                ['Communication Skills', 25],
                ['Overall Appeal', 20]
            ],
            // Swimsuit Competition
            [
                ['Physique & Fitness', 40],
                ['Confidence & Poise', 30],
                ['Stage Projection', 20],
                ['Overall Presentation', 10]
            ],
            // Evening Gown & Q&A
            [
                ['Evening Gown Presentation', 30],
                ['Intelligence & Articulation', 35],
                ['Confidence & Composure', 20],
                ['Overall Excellence', 15]
            ]
        ];

        $categories = collect();
        foreach ($stages as $stageIndex => $stage) {
            foreach ($categoryDataByStage[$stageIndex] as [$catName, $weight]) {
                $categories->push(Category::create([
                    'event_id' => $event->event_id,
                    'stage_id' => $stage->stage_id,
                    'category_name' => $catName,
                    'category_weight' => $weight,
                    'max_score' => 100,
                    'status' => $stage->status === 'finalized' ? 'finalized' : 'pending',
                ]));
            }
        }

        // Generate Enhanced Realistic Scores with Judge-specific Patterns
        $judgeProfiles = [
            0 => ['bias' => 2, 'strictness' => 0.9],   // Tracy: slightly generous
            1 => ['bias' => -1, 'strictness' => 1.1],  // Raed: slightly strict
            2 => ['bias' => 0, 'strictness' => 1.0],   // Derick: balanced
            3 => ['bias' => 1, 'strictness' => 0.95],  // Lois: slightly lenient
            4 => ['bias' => -2, 'strictness' => 1.05], // Kristine: more critical
        ];

        foreach ($judges as $judgeIndex => $judge) {
            $judgeProfile = $judgeProfiles[$judgeIndex];
            
            foreach ($candidates as $candidateIndex => $candidate) {
                // Skip inactive candidates for non-final stages
                if (!$candidate->is_active && $candidateIndex >= $activeCount * 2) {
                    continue;
                }
                
                foreach ($stages->where('status', 'finalized') as $stageIndex => $stage) {
                    $stageCategories = $categories->where('stage_id', $stage->stage_id);
                    
                    // Create realistic candidate skill progression
                    $candidateSkill = $this->getCandidateSkillLevel($candidateIndex, $stageIndex);
                    
                    foreach ($stageCategories as $categoryIndex => $category) {
                        // Category-specific adjustments
                        $categoryMultiplier = $this->getCategoryMultiplier($category->category_name, $candidateIndex);
                        
                        // Calculate base score with judge bias and candidate skill
                        $baseScore = $candidateSkill * $categoryMultiplier * $judgeProfile['strictness'] + $judgeProfile['bias'];
                        
                        // Add realistic variation
                        $variation = rand(-3, 3);
                        $finalScore = max(65, min(100, round($baseScore + $variation, 1)));
                        
                        Score::create([
                            'event_id' => $event->event_id,
                            'judge_id' => $judge->judge_id,
                            'stage_id' => $stage->stage_id,
                            'category_id' => $category->category_id,
                            'candidate_id' => $candidate->candidate_id,
                            'score' => $finalScore,
                            'status' => 'confirmed',
                            'comments' => $this->getContextualComment($finalScore, $category->category_name),
                        ]);
                    }
                }
            }
        }

        // Add some scores for the active stage to test partial results
        $activeStage = $stages->where('status', 'active')->first();
        if ($activeStage) {
            $activeCategories = $categories->where('stage_id', $activeStage->stage_id);
            $activeCandidates = $candidates->where('is_active', true)->take(4); // Score first 4 candidates
            
            foreach ($judges->take(3) as $judgeIndex => $judge) { // Only 3 judges scored so far
                foreach ($activeCandidates as $candidateIndex => $candidate) {
                    foreach ($activeCategories as $category) {
                        $candidateSkill = $this->getCandidateSkillLevel($candidateIndex, 2); // Stage 3 skills
                        $judgeProfile = $judgeProfiles[$judgeIndex];
                        $categoryMultiplier = $this->getCategoryMultiplier($category->category_name, $candidateIndex);
                        
                        $baseScore = $candidateSkill * $categoryMultiplier * $judgeProfile['strictness'] + $judgeProfile['bias'];
                        $variation = rand(-2, 4);
                        $finalScore = max(70, min(100, round($baseScore + $variation, 1)));
                        
                        Score::create([
                            'event_id' => $event->event_id,
                            'judge_id' => $judge->judge_id,
                            'stage_id' => $activeStage->stage_id,
                            'category_id' => $category->category_id,
                            'candidate_id' => $candidate->candidate_id,
                            'score' => $finalScore,
                            'status' => 'confirmed',
                            'comments' => $this->getContextualComment($finalScore, $category->category_name),
                        ]);
                    }
                }
            }
        }
    }
    
    private function getCandidateSkillLevel($candidateIndex, $stageIndex)
    {
        // Create realistic skill distributions with progression
        $baseSkills = [95, 89, 92, 85, 88, 91, 82, 87, 84, 86, 80, 83, 78, 81, 76, 79, 75, 77, 74, 76];
        $baseSkill = $baseSkills[$candidateIndex % 20];
        
        // Skills improve slightly in later stages
        $progression = $stageIndex * 1.5;
        
        return min(100, $baseSkill + $progression);
    }
    
    private function getCategoryMultiplier($categoryName, $candidateIndex)
    {
        // Different candidates excel in different areas
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
    
    private function getContextualComment($score, $categoryName)
    {
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
            'Communication Skills' => [
                'Articulate and engaging communication style',
                'Clear speech and excellent verbal skills',
                'Good communication with minor hesitations',
                'Adequate communication skills',
                'Communication needs significant improvement'
            ],
            'Intelligence & Articulation' => [
                'Demonstrates exceptional intelligence and wit',
                'Well-articulated responses showing depth',
                'Good intellectual capacity and reasoning',
                'Decent responses with some clarity issues',
                'Responses lacked depth and clarity'
            ],
            'Evening Gown Presentation' => [
                'Stunning gown presentation with perfect poise',
                'Elegant and graceful gown presentation',
                'Good gown choice and decent presentation',
                'Adequate gown presentation',
                'Gown presentation needs improvement'
            ],
            'Physique & Fitness' => [
                'Outstanding physique and excellent conditioning',
                'Great physical form and muscle definition',
                'Good physique with solid fitness level',
                'Adequate physique for competition',
                'Physique needs more development'
            ]
        ];
        
        $categoryComments = $comments[$categoryName] ?? [
            'Outstanding performance',
            'Strong showing in this category',
            'Good effort with room for growth',
            'Adequate performance',
            'Needs improvement in this area'
        ];
        
        $commentIndex = $score >= 95 ? 0 : ($score >= 85 ? 1 : ($score >= 75 ? 2 : ($score >= 65 ? 3 : 4)));
        
        return $categoryComments[$commentIndex];
    }
}