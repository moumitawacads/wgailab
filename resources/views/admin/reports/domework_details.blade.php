@extends('admin.layout')

@section('title', 'Homework Completion Details')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><strong>Homework</strong> Details Report</h1>
    <a href="{{ route('admin.dashboard', ['period' => request('period', 'this_week'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="back-btn">
        <i class="align-middle" data-feather="arrow-left"></i> Back to Dashboard
    </a>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            {{-- <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        
                    </div>
                </div>
            </div> --}}
            <div class="card-body">
                <!-- Summary Statistics -->
                @if(count($users) > 0)
                    @php
                        $totalAssigned = array_sum(array_column($users, 'total_assigned'));
                        $totalCompleted = array_sum(array_column($users, 'completed'));
                        $totalPending = array_sum(array_column($users, 'pending'));
                        $overallRate = $totalAssigned > 0 ? round(($totalCompleted / $totalAssigned) * 100) : 0;
                    @endphp
                    
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card h-100 bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-1">Total Homeworks Assigned</h6>
                                    <h3 class="mb-0">{{ $totalAssigned }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card h-100 bg-success bg-opacity-10">
                                <div class="card-body text-center">
                                    <h6 class="text-success mb-1">Completed</h6>
                                    <h3 class="mb-0 text-success">{{ $totalCompleted }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card h-100 bg-warning bg-opacity-10">
                                <div class="card-body text-center">
                                    <h6 class="text-warning mb-1">Pending</h6>
                                    <h3 class="mb-0 text-warning">{{ $totalPending }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card h-100 bg-primary bg-opacity-10">
                                <div class="card-body text-center">
                                    <h6 class="text-primary mb-1">Overall Completion Rate</h6>
                                    <h3 class="mb-0 text-primary">{{ $overallRate }}%</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- User List Table -->
                <div class="table-responsive custom-table">
                    <div class="card-header table-head-area">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                @switch($category)
                                    @case('above_75')
                                        <span class="">75%+ Homework Completion</span>
                                        @break
                                    @case('above_50')
                                        <span class="">50+% Homework Completion</span>
                                        @break
                                    @case('above_25')
                                        <span class="">25+% Homework Completion</span>
                                        @break
                                    @case('below_25')
                                        <span class="">Below 25% Homework Completion</span>
                                        @break
                                    @default
                                        Homework Completion Details
                                @endswitch
                            </h5>
                            <div class="mt-1">
                                <span class="badge bg-secondary me-2">
                                    <i class="align-middle" data-feather="calendar"></i>
                                    {{ $dateRange['start_label'] ?? '' }} - {{ $dateRange['end_label'] ?? '' }}
                                </span>
                                <span class="badge bg-primary">
                                    <i class="align-middle" data-feather="users"></i>
                                    {{ count($users) }} SEs
                                </span>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped table-hover" id="domeworkTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class="user-name-head">Name</th>
                                <th>Total Assigned</th>
                                <th>Completed</th>
                                <th>Pending</th>
                                <th>Completion Rate</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="user-detail">
                                            <strong>{{ $user['user_name'] }}</strong>
                                            <br><small class="text-muted">{{ $user['user_email'] }}</small>
                                            <br>
                                            <small class="text-muted">{{ $user['user_phone'] ?? 'N/A' }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $user['total_assigned'] }}</td>
                                    <td class="text-success">{{ $user['completed'] }}</td>
                                    <td class="text-warning">{{ $user['pending'] }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 8px; max-width: 100px;">
                                                <div class="progress-bar bg-{{ $user['completion_rate'] >= 75 ? 'success' : ($user['completion_rate'] >= 50 ? 'info' : ($user['completion_rate'] >= 25 ? 'warning' : 'danger')) }}" 
                                                     role="progressbar" 
                                                     style="width: {{ $user['completion_rate'] }}%;" 
                                                     aria-valuenow="{{ $user['completion_rate'] }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                            <span>{{ $user['completion_rate'] }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($user['completion_rate'] >= 75)
                                            <span class="badge bg-success">Excellent</span>
                                        @elseif($user['completion_rate'] >= 50)
                                            <span class="badge bg-info">Good</span>
                                        @elseif($user['completion_rate'] >= 25)
                                            <span class="badge bg-warning">Average</span>
                                        @else
                                            <span class="badge bg-danger">Needs Improvement</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.user.domework.details', [
                                            'user_id' => $user['user_id'],
                                            'period' => $period,
                                            'start_date' => request('start_date'),
                                            'end_date' => request('end_date')
                                        ]) }}" class="dom-primary-btn">
                                            <i class="align-middle" data-feather="eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="align-middle" data-feather="users" style="width: 48px; height: 48px;"></i>
                                            <p class="mt-2">No SEs found in this category for the selected period.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if(count($users) > 0)
                    <div class="mt-3">
                        <p class="text-muted">
                            <strong>Total SEs:</strong> {{ count($users) }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Reinitialize Feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>

@endsection