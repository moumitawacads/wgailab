@extends('admin.layout')
@section('content')

    <h1 class="h3 mb-3"><strong>Upcoming</strong> Sessions</h1>

    <div class="row mt-4">
        <div class="col-12">
            <div class="">
                <div class="card-header">
                    <h5 class="card-title">Upcoming Classes</h5>
                </div>

                <div class="card-body custom-table">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Session (Duration)</td>
                                <th>Class Name</th>
                                <th>Objectives</th>
                                <th>Zoom Link</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($upcomingClasses as $key => $class)
                                <tr>
                                    <td>{{$class->session->session_name}}<br/>({{$class->session->session_duration}} mins)</td>
                                    <td>{{ $class->mainclass->name ?? '-' }}</td>
                                    <td class="obj-detail">{!! nl2br(e($class->session->session_objectives)) !!}</td>
                                    <td>@if($class->attendances->isNotEmpty())
                                            {{-- {{ $class->session->zoom_link ?? '-' }}
                                            <a href="{{ $class->session->zoom_link ?? '-' }}" target="_blank"><i class="align-middle me-1" data-feather="external-link"></i></a> --}}
                                            <a href="{{ $class->session->getZoomMeetingLinkForSession($class->session->id) ?? '-' }}" target="_blank"><i class="align-middle me-1" data-feather="external-link"></i>Join Zoom Meeting</a>
                                        @else
                                            {{'Please clock-in for meeting link'}}
                                        @endif
                                    </td>

                                    <td>
                                        {{ \Carbon\Carbon::parse($class->schedule_date)->format('d M Y') }}
                                    </td>

                                    <td>
                                        {{ \Carbon\Carbon::parse($class->schedule_time)->format('h:i A') }}
                                    </td>

                                    <td>
                                        @if($class->attendances->isNotEmpty())
                                            {{ \Carbon\Carbon::parse($class->attendances->first()->clock_in_time)->format('h:i A') }}
                                        @else
                                        <button 
                                                class="dom-primary-btn clock-in-btn"
                                                data-id="{{ $class->id }}"
                                                data-date="{{ $class->schedule_date }}"
                                                data-time="{{ $class->schedule_time }}"
                                                data-url="{{ route('attendance.clockin') }}"
                                            ><i class="align-middle me-1" data-feather="clock"></i> Clock In
                                            </button>
                                        @endif    
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        No upcoming classes
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


@endsection