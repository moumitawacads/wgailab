@extends('admin.layout')
@section('content')

    <h1 class="h3 mb-3"><strong>Analytics</strong> Dashboard</h1>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Upcoming Classes</h5>
                </div>

                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Class Name</th>
                                <th>Zoom Link</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($upcomingClasses as $key => $class)
                                <tr>
                                    <td>{{ $key + 1 }}</td>

                                    <td>{{ $class->mainclass->name ?? '-' }}</td>

                                    <td>@if($class->attendances->isNotEmpty())
                                        {{ $class->zoom_link ?? '-' }}
                                        <a href="{{ $class->zoom_link ?? '-' }}" target="_blank"><i class="align-middle me-1" data-feather="external-link"></i></a>
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
                                                class="btn btn-sm btn-success clock-in-btn"
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
                                    <td colspan="5" class="text-center">
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