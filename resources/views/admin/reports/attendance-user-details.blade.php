@extends('admin.layout')

@section('title', 'User Attendance Details')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h3 mb-0"><strong>{{ $user->name }}</strong> Attendance Details</h1>
        <p class="text-muted mb-0">{{ $user->email }} | {{ $user->phone ?? 'N/A' }}</p>
    </div>
    <div>
        <a href="{{ route('admin.attendance.details', ['category' => $category, 'period' => request('period', 'this_week'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="back-btn">
            <i class="align-middle" data-feather="arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="card-title">Overall Attendance</h6>
                <div class="display-4 {{ $attendancePercentage >= 80 ? 'text-success' : ($attendancePercentage >= 60 ? 'text-warning' : 'text-danger') }}">
                    {{ $attendancePercentage }}%
                </div>
                <p class="text-muted">
                    {{ $totalStarted }} Started Sessions | 
                    {{ $presentCount }} Present | 
                    {{ $absentCount }} Absent
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Session Details</h5>
                <div>
                    <span class="text-muted small">
                        {{ $startDate }} - {{ $endDate }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                @if($sessions->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-muted">No sessions found for this period.</p>
                    </div>
                @else
                    <div class="table-responsive custom-table">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Class</th>
                                    <th>Session</th>
                                    <th>Status</th>
                                    <th>Attendance</th>
                                    <th>Check-in Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sessions as $session)
                                    @php
                                        $statusBadge = [
                                            'upcoming' => 'secondary',
                                            'started' => 'primary'
                                        ][$session->session_status] ?? 'secondary';
                                        
                                        $attendanceBadge = [
                                            'present' => 'success',
                                            'absent' => 'danger',
                                            'upcoming' => 'secondary'
                                        ][$session->attendance_status] ?? 'secondary';
                                        
                                        $attendanceText = [
                                            'present' => 'Present',
                                            'absent' => 'Absent',
                                            'upcoming' => 'Upcoming'
                                        ][$session->attendance_status] ?? 'Unknown';
                                    @endphp
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($session->schedule_date)->format('d M Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($session->schedule_time)->format('h:i A') }}</td>
                                        <td>{{ $session->class_name ?? 'N/A' }}</td>
                                        <td>{{ $session->session_name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $statusBadge }} ">
                                                {{ ucfirst($session->session_status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $attendanceBadge }}">
                                                {{ $attendanceText }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($session->attendance_status === 'present')
                                                {{ \Carbon\Carbon::parse($session->clock_in_time)->format('h:i A') }}
                                                {{-- @if($session->minutes_diff && abs($session->minutes_diff) > 0)
                                                    <small class="text-muted">
                                                        ({{ abs($session->minutes_diff) }} min {{ $session->minutes_diff > 0 ? 'late' : 'early' }})
                                                    </small>
                                                @endif --}}
                                            @elseif($session->attendance_status === 'absent')
                                                @if(isset($session->clock_in_time))
                                                    {{ \Carbon\Carbon::parse($session->clock_in_time)->format('h:i A') }}
                                                @else
                                                    <span class="text-danger">No check-in</span>
                                                @endif
                                            @else
                                                <span class="text-muted">--</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection