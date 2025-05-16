<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $event->event_name }} Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #1a73e8;
            font-size: 24px;
            margin-bottom: 20px;
        }
        h2 {
            color: #444;
            font-size: 18px;
            margin-top: 30px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .page-break {
            page-break-after: always;
        }
        p.no-results {
            font-style: italic;
            color: #666;
            font-size: 14px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>{{ $event->event_name }} Results</h1>

    @foreach ($stages as $index => $stage)
        <h2>{{ $stage->stage_name ?? 'Stage ' . ($index + 1) }}</h2>
        @if ($stage->results->isEmpty())
            <p class="no-results">No results available for this stage.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Candidate</th>
                        <th>Average Score</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stage->results as $result)
                        <tr>
                            <td>
                                {{ $result->first_name }} {{ $result->last_name }}
                                ({{ $result->candidate_number ? '#' . $result->candidate_number : 'N/A' }})
                            </td>
                            <td>{{ number_format($result->raw_average ?? 0, 2) }}/100</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        @if ($index < $stages->count() - 1)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>