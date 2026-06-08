<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Compensation Update</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f4f4f4; padding:20px;">

    <div style="max-width:600px; margin:auto; background:#ffffff; padding:20px; border-radius:8px;">
        
        <h2 style="color:#2c3e50;">URZ</h2>

        <p>Hi {{ $data['name'] }},</p>

        <p>
            Your compensation request for the following week has been 
            <strong style="color:{{ $data['status'] == 'Approved' ? 'green' : 'red' }}">
                {{ $data['status'] }}
            </strong>.
        </p>

        <p>
            <strong>Week:</strong> {{ $data['week_start'] }} to {{ $data['week_end'] }} <br>
            <strong>Total Classes:</strong> {{ $data['total_classes'] }} <br>
            <strong>Attended:</strong> {{ $data['present_count'] }}
        </p>

        @if(!empty($data['notes']))
            <p>
                <strong>Remarks:</strong><br>
                {{ $data['notes'] }}
            </p>
        @endif

        <hr>

        <p style="font-size:12px; color:#777;">
            Regards,<br>
            <strong>Team URZ</strong><br>
            This is an automated email, please do not reply.
        </p>

    </div>

</body>
</html>