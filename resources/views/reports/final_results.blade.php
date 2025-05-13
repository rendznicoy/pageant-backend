<!DOCTYPE html>
<html>
<head>
    <title>Final Results</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Final Results</h1>
    <table>
        <thead>
            <tr>
                <th>Candidate</th>
                <th>Average Score</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($results as $result)
                <tr>
                    <td>{{ $result->candidate->first_name }} {{ $result->candidate->last_name }}</td>
                    <td>{{ number_format($result->average_score, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>