<!DOCTYPE html>
<html>
<head>
    <title>VSU Pageant Scoring System Notification: VSU Pageant Scoring System: Password reset request</title>
</head>
<body>
    <p>Hi {{ $user->name }},</p>
    
    <p>A password reset was requested for your account '{{ $user->username }}' at Visayas State University Pageant Scoring System.</p>
    
    <p>To confirm this request, and set a new password for your account, please go to the following web address:</p>
    
    <p><a href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>
    
    <p>(This link is valid for 30 minutes from the time this reset was first requested)</p>
    
    <p>If this password reset was not requested by you, no action is needed.</p>
    
    <p>If you need help, please contact the site administrator,</p>
    
    <p>
        Pageant Scoring System VSU Administrator<br>
        <a href="mailto:21-1-01027@vsu.edu.ph">21-1-01027@vsu.edu.ph</a>
    </p>
</body>
</html>