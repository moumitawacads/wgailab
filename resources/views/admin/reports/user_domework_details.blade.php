@extends('admin.layout')

@section('title', 'User Homework Details')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h3 mb-0">Homework Details: <strong>{{ $user->name }}</strong></h1>
    </div>
    <div>
        <a href="{{ url()->previous() }}" class="back-btn">
            <i class="align-middle" data-feather="arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <div class="mt-1">
                            <span class="badge bg-secondary me-2">
                                <i class="align-middle" data-feather="calendar"></i>
                                {{ $dateRange['start_label'] ?? '' }} - {{ $dateRange['end_label'] ?? '' }}
                            </span>
                            <span class="badge bg-primary">
                                <i class="align-middle" data-feather="book"></i>
                                {{ $details['total_assigned'] }} Homeworks
                            </span>
                            <span class="badge bg-success">
                                <i class="align-middle" data-feather="check-circle"></i>
                                {{ $details['completed'] }} Completed
                            </span>
                            <span class="badge bg-warning">
                                <i class="align-middle" data-feather="clock"></i>
                                {{ $details['pending'] }} Pending
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- User Info Card -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h6 class="text-muted mb-1">Name</h6>
                                        <p class="mb-0">{{ $user->name }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="text-muted mb-1">Email</h6>
                                        <p class="mb-0">{{ $user->email }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="text-muted mb-1">Phone</h6>
                                        <p class="mb-0">{{ $user->phone ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary bg-opacity-10">
                            <div class="card-body text-center">
                                <h6 class="text-primary mb-1">Total Assigned</h6>
                                <h3 class="mb-0 text-primary">{{ $details['total_assigned'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success bg-opacity-10">
                            <div class="card-body text-center">
                                <h6 class="text-success mb-1">Completed</h6>
                                <h3 class="mb-0 text-success">{{ $details['completed'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning bg-opacity-10">
                            <div class="card-body text-center">
                                <h6 class="text-warning mb-1">Pending</h6>
                                <h3 class="mb-0 text-warning">{{ $details['pending'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-{{ $details['completion_rate'] >= 75 ? 'success' : ($details['completion_rate'] >= 50 ? 'info' : ($details['completion_rate'] >= 25 ? 'warning' : 'danger')) }} bg-opacity-10">
                            <div class="card-body text-center">
                                <h6 class="text-{{ $details['completion_rate'] >= 75 ? 'success' : ($details['completion_rate'] >= 50 ? 'info' : ($details['completion_rate'] >= 25 ? 'warning' : 'danger')) }} mb-1">Completion Rate</h6>
                                <h3 class="mb-0 text-{{ $details['completion_rate'] >= 75 ? 'success' : ($details['completion_rate'] >= 50 ? 'info' : ($details['completion_rate'] >= 25 ? 'warning' : 'danger')) }}">{{ $details['completion_rate'] }}%</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Overall Completion Progress</span>
                        <span>{{ $details['completion_rate'] }}%</span>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-{{ $details['completion_rate'] >= 75 ? 'success' : ($details['completion_rate'] >= 50 ? 'info' : ($details['completion_rate'] >= 25 ? 'warning' : 'danger')) }}" 
                             role="progressbar" 
                             style="width: {{ $details['completion_rate'] }}%;" 
                             aria-valuenow="{{ $details['completion_rate'] }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ $details['completion_rate'] }}%
                        </div>
                    </div>
                </div>

                <!-- Domeworks List -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="align-middle" data-feather="list"></i> 
                            Homework Assignments
                            <span class="badge bg-secondary ms-2">{{ count($details['domeworks']) }}</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($details['domeworks']->isEmpty())
                            <div class="text-center py-4">
                                <i class="align-middle" data-feather="book" style="width: 48px; height: 48px; color: #6c757d;"></i>
                                <p class="mt-2 text-muted">No homework assignments found for this period.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="domeworkTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Homework Title</th>
                                            <th>Session</th>
                                            <th>Status</th>
                                            <th>Assigned Date</th>
                                            <th>Answer</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($details['domeworks'] as $index => $domework)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ $domework['title'] }}</strong>
                                                </td>
                                                <td>{{ $domework['session_name'] }}</td>
                                                <td>
                                                    @if($domework['status'] == '1')
                                                        <span class="badge bg-success">
                                                            <i class="align-middle" data-feather="check-circle"></i> Completed
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning">
                                                            <i class="align-middle" data-feather="clock"></i> Pending
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>{{ $domework['assigned_at'] }}</td>
                                                <td>
                                                    @if($domework['status'] == '1' && $domework['answer'])
                                                        <a href="{{ route('admin.worksheet.pdf', ['session_id' => $domework['session_id'], 'user_id' => $user->id]) }}" class="dom-primary-btn btn-sm" 
                                                                >
                                                            <i class="align-middle" data-feather="eye"></i> Download PDF
                                                        </a>
                                                    @else
                                                        <span class="text-muted">No answer submitted</span>
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