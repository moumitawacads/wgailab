<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>

    <h2>Hello {{ $user->name }},</h2>

    <p>
        Your account has been registered successfully in our system.
    </p>

    <p>
        Your credentials are as follows:
    </p>

    <p>
        <strong>Email:</strong>
        {{ $user->email }}
    </p>

    <p>
        <strong>Password:</strong>
        {{ $password }}
    </p>

    <p>
        Please login through the link below:
    </p>

    <p>
        <a href="{{ url('/') }}">
            {{ url('/') }}
        </a>
    </p>

    <p>
        We recommend changing your password after your first login.
    </p>

    <br>

    <p>
        Thank You,<br>
        {{ config('app.name') }}
    </p>

</body>
</html>