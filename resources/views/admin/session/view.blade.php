@extends('admin.layout')
@section('content')

    <h1 class="h3 mb-3">
        <strong>Session Details</strong>
    </h1>

    {{-- SESSION HEADER --}}
    <div class="card mb-4 position-relative">
        <div class="card-body">

            <!-- Status badge -->
            <span class="badge bg-{{ $session->status == 1 ? 'success' : 'danger' }} position-absolute top-0 end-0 m-3">
                {{ $session->status == 1 ? 'Active' : 'Cancelled' }}
            </span>

            <h4 class="mb-1">{{ $session->session_name }}</h4>
            <p class="mb-0 text-muted">
                {{ $session->session_objectives ?? 'No description available' }}
            </p>

        </div>
    </div>

    {{-- BASIC INFO --}}
    <div class="row mb-4">

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <strong>Class</strong><br>
                    {{ $session->schedules->first()->mainclass->name ?? '-' }}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <strong>Duration</strong><br>
                    {{ $session->session_duration ?? '-' }} mins
                </div>
            </div>
        </div>

        {{-- <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <strong>Instructor Zoom Link</strong><br>
                    @if($session->zoom_start_url)
                        <a href="{{ $session->zoom_start_url }}" target="_blank">Join Meeting</a>
                    @else
                        -
                    @endif
                </div>
            </div>
        </div> --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <strong>Instructor Zoom Link</strong><br>
                    @php
                        // Generate fresh start_url dynamically if meeting exists
                        $freshStartUrl = null;
                        if($session->zoom_meeting_id) {
                            try {
                                $zoomService = app(\App\Services\ZoomMeetingService::class);
                                $meetingDetails = $zoomService->getMeetingForHost($session->zoom_meeting_id);
                                $freshStartUrl = $meetingDetails['start_url'] ?? null;
                            } catch (\Exception $e) {
                                \Log::error("Failed to get fresh start_url: " . $e->getMessage());
                                // Fallback to stored URL if dynamic fetch fails
                                $freshStartUrl = $session->zoom_start_url;
                            }
                        }
                    @endphp
                    
                    @if($freshStartUrl)
                        <a href="{{ $freshStartUrl }}" target="_blank" class="dom-primary-btn">
                            <i class="align-middle me-1" data-feather="video"></i> Start Meeting
                        </a>
                    @else
                        <span class="text-muted">Meeting not available</span>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- PARTICIPANTS + INSTRUCTORS --}}
    <div class="row mb-4">

        <div class="col-md-6">
            <div class="card">
                <div class="card-header card-title mb-0"><strong>Participants</strong></div>
                <div class="card-body">
                    {{ $session->schedules->pluck('user.name')->unique()->implode(', ') ?: '-' }}
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header card-title mb-0"><strong>Instructors</strong></div>
                <div class="card-body">
                    @php
                        $ids = explode(',', $session->instructor_ids);
                        $names = \App\Models\User::whereIn('id', $ids)->pluck('name')->implode(', ');
                    @endphp
                    {{ $names ?: '-' }}
                </div>
            </div>
        </div>

    </div>

    {{-- SCHEDULE TABLE --}}
    <div class="">
        <div class="card-title">
            <strong>Schedule Details</strong>
        </div>

        <div class="custom-table">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Participant</th>
                        <th>Instructor</th>
                        <th>Clock-in</th>
                        <th>Meeting Link</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($session->schedules as $schedule)

                        @php
                            $attendance = $schedule->attendances->first() ?? null;

                            
                            $status="";$display_status="";

                            if ($attendance && $attendance->clock_in_time) {
                                $scheduleTime = \Carbon\Carbon::parse($schedule->schedule_date.' '.$schedule->schedule_time);
                                $clockIn = \Carbon\Carbon::parse($attendance->clock_in_time);

                                $diff = $clockIn->diffInMinutes($scheduleTime, false);

                                if (abs($diff) <= 15) {
                                    $status = 'Present';
                                    $display_status="Present";
                                }
                                elseif (abs($diff) > 15 && abs($diff)<30) {
                                    $status = 'Present';
                                    $display_status="Late";
                                }
                                elseif (abs($diff)>30) {
                                    $status = 'Late';
                                    $display_status="Absent";
                                }
                                else{
                                    $status = 'Present';
                                    $display_status="Present";
                                }
                            }
                        @endphp

                        <tr>
                            <td>{{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($schedule->schedule_time)->format('h:i A') }}</td>
                            <td>{{ $schedule->user->name ?? '-' }}</td>

                            <td>
                                @php
                                    $ids = explode(',', $schedule->instructor_id);
                                    $names = \App\Models\User::whereIn('id', $ids)->pluck('name')->implode(', ');
                                @endphp
                                {{ $names ?: '-' }}
                            </td>

                            <td>
                                {{ $attendance 
                                    ? \Carbon\Carbon::parse($attendance->clock_in_time)->format('h:i A') 
                                    : 'Not Clocked In' }}
                            </td>

                            <td> {!!($schedule->zoom_join_url ? '<a href="'.$schedule->zoom_join_url.'" target="_blank">Join Zoom Meeting</a>' : 'Not Generated')!!}</td>
                            <td>
                                <span class="badge 
                                    bg-{{ $status == '' ? '' : ($status == 'Present' ? 'success' : ($status == 'Late' ? 'warning' : 'danger')) }}">
                                    {{ $display_status }}
                                </span>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No schedules found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


@endsection