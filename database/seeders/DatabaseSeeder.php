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

        // Create two completed events with specific configurations
        $eventConfigs = [
            [
                'name' => 'Grand Pageant Championship 2025 (Max 10)',
                'venue' => 'VSU Grand Gymnasium',
                'status' => 'completed',
                'division' => 'standard',
                'global_max_score' => 10,
                'days_offset' => -15,
            ],
            [
                'name' => 'Spring Beauty Contest 2025 (Max 100)',
                'venue' => 'University Auditorium',
                'status' => 'completed',
                'division' => 'standard',
                'global_max_score' => 100,
                'days_offset' => -10,
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
            'global_max_score' => $config['global_max_score'],
            'created_by' => $admin->user_id,
            'cover_photo' => null,
            'statisticians' => [['name' => 'Dr. Maria Santos']],
        ]);

        // Create exactly 5 judges for each event
        $judgePool = [
            ['Tracy', 'Johnson'], 
            ['Raed', 'Al-Mansouri'], 
            ['Derick', 'Thompson'],
            ['Lois', 'Anderson'], 
            ['Kristine', 'Peterson']
        ];

        $judges = collect();
        foreach ($judgePool as $i => [$first, $last]) {
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

        // Create exactly 2 stages: Preliminaries and Finals (both finalized)
        $stages = collect();
        $stageConfigs = [
            ['name' => 'Preliminaries', 'status' => 'finalized'],
            ['name' => 'Finals', 'status' => 'finalized'],
        ];

        foreach ($stageConfigs as $stageConfig) {
            $stages->push(Stage::create([
                'event_id' => $event->event_id,
                'stage_name' => $stageConfig['name'],
                'status' => $stageConfig['status'],
                'top_candidates_count' => null,
            ]));
        }

        // Create exactly 22 candidates (11 male, 11 female)
        $maleNames = [
            ['Alexander', 'Champion'], ['Michael', 'Sterling'], ['William', 'Valor'],
            ['James', 'Prestige'], ['Benjamin', 'Excellence'], ['Lucas', 'Distinction'],
            ['Henry', 'Achievement'], ['Sebastian', 'Success'], ['Owen', 'Victory'],
            ['Theodore', 'Triumph'], ['Gabriel', 'Majesty']
        ];

        $femaleNames = [
            ['Isabella', 'Grace'], ['Sophia', 'Radiance'], ['Emma', 'Elegance'],
            ['Olivia', 'Brilliance'], ['Charlotte', 'Sophistication'], ['Amelia', 'Magnificence'],
            ['Harper', 'Splendor'], ['Evelyn', 'Glamour'], ['Abigail', 'Allure'],
            ['Emily', 'Enchantment'], ['Victoria', 'Serenity']
        ];

        $teams = [
            'Team Phoenix', 'Team Nova', 'Team Aurora', 'Team Stellar', 'Team Zenith',
            'Team Apex', 'Team Elite', 'Team Prime', 'Team Supreme', 'Team Ultimate', 'Team Dynasty'
        ];

        $candidates = collect();
        
        // Create 11 male candidates
        foreach ($maleNames as $num => [$first, $last]) {
            $candidates->push(Candidate::create([
                'event_id' => $event->event_id,
                'candidate_number' => $num + 1,
                'first_name' => $first,
                'last_name' => $last,
                'sex' => 'M',
                'team' => $teams[$num],
                'is_active' => true,
                'photo' => null,
            ]));
        }

        // Create 11 female candidates
        foreach ($femaleNames as $num => [$first, $last]) {
            $candidates->push(Candidate::create([
                'event_id' => $event->event_id,
                'candidate_number' => $num + 1,
                'first_name' => $first,
                'last_name' => $last,
                'sex' => 'F',
                'team' => $teams[$num],
                'is_active' => true,
                'photo' => null,
            ]));
        }

        // Create categories for each stage
        $categories = collect();
        
        foreach ($stages as $stage) {
            if ($stage->stage_name === 'Preliminaries') {
                // Preliminaries: 6 categories
                $preliminaryCategories = [
                    ['Beauty', 20],
                    ['Q & A', 10],
                    ['Talent', 10],
                    ['Performance', 20],
                    ['Swim Wear', 20],
                    ['Formal Wear', 20],
                ];

                foreach ($preliminaryCategories as [$name, $weight]) {
                    $categories->push(Category::create([
                        'event_id' => $event->event_id,
                        'stage_id' => $stage->stage_id,
                        'category_name' => $name,
                        'category_weight' => $weight,
                        'max_score' => $config['global_max_score'],
                        'status' => 'finalized',
                    ]));
                }
            } else { // Finals
                // Finals: 2 categories
                $finalCategories = [
                    ['Q & A', 50],
                    ['Beauty', 50],
                ];

                foreach ($finalCategories as [$name, $weight]) {
                    $categories->push(Category::create([
                        'event_id' => $event->event_id,
                        'stage_id' => $stage->stage_id,
                        'category_name' => $name,
                        'category_weight' => $weight,
                        'max_score' => $config['global_max_score'],
                        'status' => 'finalized',
                    ]));
                }
            }
        }

        // Define candidate performance levels for realistic scoring
        $candidatePerformanceLevels = [];
        for ($i = 1; $i <= 11; $i++) {
            $candidatePerformanceLevels[$i] = [
                'base' => 0.95 - ($i - 1) * 0.03, // Decreasing performance from 0.95 to 0.65
                'variance' => 0.04 + ($i - 1) * 0.003, // Increasing variance
            ];
        }

        // Judge bias/preferences
        $judgeBiases = [];
        foreach ($judges as $index => $judge) {
            $judgeBiases[$judge->judge_id] = [
                'generosity' => 0.97 + ($index * 0.02), // Range from 0.97 to 1.05
                'consistency' => 0.02 + ($index * 0.005), // Range from 0.02 to 0.04
            ];
        }

        // Generate scores for all finalized stages and categories
        foreach ($judges as $judge) {
            foreach ($candidates as $candidate) {
                foreach ($stages as $stage) {
                    foreach ($categories->where('stage_id', $stage->stage_id) as $category) {
                        $score = $this->generateScore(
                            $candidate->candidate_number,
                            $judge->judge_id,
                            $category->category_name,
                            $config['global_max_score'],
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
                            'comments' => $this->generateComment($score, $config['global_max_score']),
                        ]);
                    }
                }
            }
        }
    }

    private function generateScore($candidateNumber, $judgeId, $categoryName, $maxScore, $performanceLevels, $judgeBiases, $extraVariance = 0)
    {
        // Get candidate's base performance level
        $performance = $performanceLevels[$candidateNumber] ?? $performanceLevels[6];
        
        // Get judge bias
        $judgeBias = $judgeBiases[$judgeId] ?? ['generosity' => 1.0, 'consistency' => 0.03];
        
        // Add judge-specific preferences
        $judgePreference = 1.0;
        $judgeIndex = $judgeId % 5;
        if (($judgeIndex === 0 && in_array($candidateNumber, [1, 3, 7])) ||
            ($judgeIndex === 1 && in_array($candidateNumber, [2, 4, 8])) ||
            ($judgeIndex === 2 && in_array($candidateNumber, [1, 5, 9])) ||
            ($judgeIndex === 3 && in_array($candidateNumber, [3, 6, 10])) ||
            ($judgeIndex === 4 && in_array($candidateNumber, [2, 6, 11]))) {
            $judgePreference = 1.08; // 8% bonus for preferred candidates
        }
        
        // Category-specific adjustments
        $categoryMultiplier = 1.0;
        switch ($categoryName) {
            case 'Beauty':
                if (in_array($candidateNumber, [1, 2, 5, 8])) $categoryMultiplier = 1.05;
                if (in_array($candidateNumber, [9, 10, 11])) $categoryMultiplier = 0.95;
                break;
            case 'Q & A':
                if (in_array($candidateNumber, [1, 3, 4, 6])) $categoryMultiplier = 1.06;
                if (in_array($candidateNumber, [8, 9, 10])) $categoryMultiplier = 0.94;
                break;
            case 'Talent':
                if (in_array($candidateNumber, [2, 4, 7, 9])) $categoryMultiplier = 1.07;
                if (in_array($candidateNumber, [5, 6, 11])) $categoryMultiplier = 0.93;
                break;
            case 'Performance':
                if (in_array($candidateNumber, [1, 3, 5, 7])) $categoryMultiplier = 1.04;
                if (in_array($candidateNumber, [8, 10, 11])) $categoryMultiplier = 0.96;
                break;
            case 'Swim Wear':
                if (in_array($candidateNumber, [2, 3, 6, 8])) $categoryMultiplier = 1.05;
                if (in_array($candidateNumber, [9, 10, 11])) $categoryMultiplier = 0.95;
                break;
            case 'Formal Wear':
                if (in_array($candidateNumber, [1, 4, 5, 9])) $categoryMultiplier = 1.06;
                if (in_array($candidateNumber, [7, 10, 11])) $categoryMultiplier = 0.94;
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