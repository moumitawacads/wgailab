<h2>URZ - Weekly Compensation Requests</h2>

<p>
    Weekend Range:
    <strong>{{ \Carbon\Carbon::parse($data['start'])->format('d M Y') }}</strong>
    -
    <strong>{{ \Carbon\Carbon::parse($data['end'])->format('d M Y') }}</strong>
</p>

<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>User</th>
            <th>Email</th>
            <th>Week</th>
            <th>Notes</th>
            <th>Status</th>
            <th>Requested On</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['requests'] as $req)
            <tr>
                <td>{{ $req->user_name }}</td>
                <td>{{ $req->email }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($req->week_start)->format('d M') }}
                    -
                    {{ \Carbon\Carbon::parse($req->week_end)->format('d M') }}
                </td>
                <td>{{ $req->notes ?? '-' }}</td>
                <td>
                    @if($req->status == 0) Pending
                    @elseif($req->status == 1) Approved
                    @else Rejected
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($req->created_at)->format('d M Y H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p style="margin-top:20px;">
    Regards,<br>
    <strong>URZ Team</strong>
</p>