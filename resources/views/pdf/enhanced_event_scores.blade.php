<!DOCTYPE html>
<html>
<head>
    <title>{{ $event->event_name }} - Final Results</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0.5in;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .results-table th,
        .results-table td {
            border: 1px solid #333;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
        }
        .results-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 9px;
        }
        .results-table td {
            font-size: 8px;
        }
        .candidate-info {
            text-align: left;
        }
        .judge-header {
            background-color: #e6f3ff;
        }
        .judge-subheader {
            background-color: #f0f8ff;
            font-size: 8px;
        }
        .mean-scores {
            background-color: #fff2cc;
            font-weight: bold;
        }
        .overall-rank {
            background-color: #d4edda;
            font-weight: bold;
            font-size: 12px;
        }
        .sex-section {
            margin-bottom: 30px;
        }
        .sex-title {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 8px;
            font-size: 14px;
            font-weight: bold;
        }
        .scoring-explanation {
            margin: 20px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            font-size: 11px;
            line-height: 1.4;
        }
        
        /* Signature Section Styles */
        .signature-section {
            margin-top: 30px;
            padding: 15px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        
        .signature-section, .statisticians {
            text-align: center;
        }
        .signature-container {
            display: inline-flex !important;
            flex-direction: row !important;
            gap: 20px !important;
            justify-content: flex-start !important; /* default, since parent is centered */
            align-items: flex-end !important;
            margin: 0 auto 20px auto !important;
            width: auto !important;
        }
        .signature-box {
            text-align: center;
            min-width: 100px !important;
            max-width: 200px !important;
            display:inline-block !important;
            margin: 40px 40px 0 0 !important;
        }

        
        .signature-line {
            height: 40px;
            border-bottom: 1px solid #333;
            margin-bottom: 8px;
            width: 100%;
        }
        
        .signature-name {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 2px;
        }
        
        .signature-label {
            font-size: 9px;
            color: #666;
        }
        
        /* Statisticians Section */
        .statisticians {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            background-color: #f5f5f5;
        }
        
        .statisticians-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $event->event_name }} - Final Results</h1>
        <p>Date: {{ \Carbon\Carbon::parse($event->start_date)->format('F j, Y') }}</p>
        <p>Venue: {{ $event->venue ?? 'Not specified' }}</p>
    </div>

    @php
        $judges = $finalJudges ?? [];
        $maleResults = collect($results)->filter(fn($r) => strtolower($r['sex']) === 'm')->sortBy('overall_rank');
        $femaleResults = collect($results)->filter(fn($r) => strtolower($r['sex']) === 'f')->sortBy('overall_rank');
    @endphp

    @foreach(['Male' => $maleResults, 'Female' => $femaleResults] as $sexTitle => $sexResults)
        @if($sexResults->count() > 0)
            <div class="sex-section">
                <div class="sex-title">{{ $sexTitle }} Candidates</div>
                
                <table class="results-table">
                    <thead>
                        <tr>
                            <th rowspan="3" style="width: 8%;">Candidate<br>Number</th>
                            <th rowspan="3" style="width: 15%;">Candidate Name<br>(First, Last)</th>
                            <th rowspan="3" style="width: 12%;">Team Name</th>
                            <th colspan="{{ count($judges) * 2 }}" class="judge-header">Individual Judge Scores and Ranks</th>
                            <th colspan="2" style="width: 12%;" class="mean-scores">Mean Scores<br>and Ranks</th>
                            <th rowspan="3" style="width: 6%;" class="overall-rank">Overall<br>Rank</th>
                        </tr>
                        <tr>
                            @foreach($judges as $judge)
                                <th colspan="2" class="judge-subheader">Judge: {{ $judge['name'] ?? 'Unknown' }}</th>
                            @endforeach
                            <th rowspan="2" class="mean-scores">Rating</th>
                            <th rowspan="2" class="mean-scores">Rank</th>
                        </tr>
                        <tr>
                            @foreach($judges as $judge)
                                <th class="judge-subheader">Score</th>
                                <th class="judge-subheader">Rank</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sexResults as $result)
                            <tr>
                                <td>{{ str_pad($result['candidate']['candidate_number'], 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="candidate-info">{{ $result['candidate']['first_name'] }} {{ $result['candidate']['last_name'] }}</td>
                                <td>{{ $result['candidate']['team'] ?? 'N/A' }}</td>
                                
                                @foreach($judges as $judge)
                                    @php
                                        $judgeScore = $result['judge_scores'][$judge['judge_id']] ?? null;
                                    @endphp
                                    <td>{{ $judgeScore ? number_format($judgeScore['score'], 1) : 'N/A' }}</td>
                                    <td>{{ $judgeScore['rank'] ?? 'N/A' }}</td>
                                @endforeach
                                
                                <td class="mean-scores">{{ number_format($result['mean_rating'], 2) }}</td>
                                <td class="mean-scores">{{ number_format($result['mean_rank'] ?? $result['overall_rank'], 2) }}</td>
                                <td class="overall-rank">{{ $result['overall_rank'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endforeach

    <div class="scoring-explanation">
        <strong>Scoring Process Explanation:</strong><br/>
        • <strong>Mean Rating</strong> = (Sum of Judges' Scores) ÷ (Number of Judges)<br/>
        • <strong>Mean Rank</strong> = (Sum of Judges' Assigned Ranks) ÷ (Number of Judges)<br/>
        • <strong>Overall Rank</strong> determined primarily by Mean Rank; Mean Rating used as tiebreaker<br/>
        <br/>
        <strong>Judges:</strong> {{ collect($judges)->pluck('name')->implode(', ') }}
        <br/>
        <strong>Statistician(s):</strong> {{ collect($event->statisticians)->map(fn($s) => is_array($s) ? $s['name'] : $s)->implode(', ') }}
    </div>

    <!-- Judge Signatures Section -->
    @php 
        $judgeCount = count($judges);
        $judgeCountClass = $judgeCount <= 6 ? "count-{$judgeCount}" : "count-many";
    @endphp
    @if($judgeCount > 0)
        <div class="signature-section">
            <div class="statisticians-title">Judge Signatures</div>
            <div class="signature-container {{ $judgeCountClass }}">
                @foreach($judges as $judge)
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $judge['name'] ?? 'Judge' }}</div>
                        <div class="signature-label">Signature</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
   
    <!-- Statisticians Section -->
    @if($event->statisticians && is_array($event->statisticians) && count($event->statisticians) > 0)
        @php 
            $statCount = count($event->statisticians);
            $statCountClass = $statCount <= 6 ? "count-{$statCount}" : "count-many";
        @endphp
        <div class="statisticians">
            <div class="statisticians-title">Statistician Signatures</div>
            <div class="signature-container {{ $statCountClass }}">
                @foreach($event->statisticians as $statistician)
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ is_array($statistician) ? ($statistician['name'] ?? 'Statistician') : $statistician }}</div>
                        <div class="signature-label">Signature</div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="statisticians">
            <div class="statisticians-title">Statistician/s: Not specified</div>
        </div>
    @endif    
</body>
</html>