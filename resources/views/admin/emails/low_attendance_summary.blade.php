
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>

    <h2>Users Below 80% Attendance</h2>

    <table border="1" cellpadding="10" cellspacing="0" width="100%">

        <thead>
            <tr>
                <th>User Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Attendance Percentage</th>
            </tr>
        </thead>

        <tbody>

            @foreach($users as $user)

                <tr>
                    <td>{{ $user['name'] }}</td>
                    <td>{{ $user['email'] }}</td>
                    <td>{{ $user['phone'] }}</td>
                    <td>{{ $user['percentage'] }}%</td>
                </tr>

            @endforeach

        </tbody>

    </table>

</body>
</html>