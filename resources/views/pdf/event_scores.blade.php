<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Event Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    </style>
</head>
<body>
    <h1>{{ $event->event_name }} - Report</h1>

    <h2>Judges</h2>
    <ul>
        @foreach($judges as $judge)
            <li>{{ $judge->user->first_name }} {{ $judge->user->last_name }}</li>
        @endforeach
    </ul>

    <h2>Candidates</h2>
    <ul>
        @foreach($candidates as $candidate)
            <li>#{{ $candidate->candidate_number }} - {{ $candidate->first_name }} {{ $candidate->last_name }} ({{ ucfirst($candidate->sex) }})</li>
        @endforeach
    </ul>

    <h2>Categories</h2>
    <ul>
        @foreach($categories as $category)
            <li>{{ $category->category_name }} ({{ $category->category_weight }}%)</li>
        @endforeach
    </ul>

    <h2>Scores</h2>
    <ul>
        @foreach($scores as $score)
            <li>Candidate {{ $score->candidate_id }} | Category {{ $score->category_id }} | Judge {{ $score->judge_id }} : {{ $score->score }}</li>
        @endforeach
    </ul>
</body>
</html>
