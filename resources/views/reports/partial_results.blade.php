<!DOCTYPE html>
<html>
<head>
    <title>Partial Results</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Partial Results</h1>
    <table>
        <thead>
            <tr>
                <th>Candidate</th>
                <th>Raw Average Score</th>
                <th>Rank</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($results as $result)
                <tr>
                    <td>{{ $result->candidate->first_name }} {{ $result->candidate->last_name }}</td>
                    <td>{{ number_format($result->raw_average, 2) }}/100</td>
                    <td>{{ $result->rank }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>