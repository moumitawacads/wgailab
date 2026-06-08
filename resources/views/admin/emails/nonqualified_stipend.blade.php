<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Attendance Alert</title>
</head>
<body>
    <h3>Hello {{ $data['name'] }},</h3>

    <p>
        Your attendance for the week 
        <strong>{{ $data['week_start'] }} - {{ $data['week_end'] }}</strong> 
        is <strong>{{ $data['percentage'] }}%</strong>.
    </p>

    <p style="color:red;">
        You do not qualify for stipend for this week.
    </p>

    <p>
        If you need any alterations or changes, you may submit a request through the portal.
        <br/>Last date of submission for compensation form is on <b>{{ now()->next('Sunday')->setTime(23, 59)->format('l jS F g:ia') }}</b>
    </p>

    <br>

    <p>Regards,<br>URZ Team</p>
</body>
</html>