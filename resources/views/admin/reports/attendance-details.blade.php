@extends('admin.layout')

@section('title', 'Attendance Details Report')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><strong>Attendance</strong> Details Report</h1>
    <a href="{{ route('admin.dashboard', ['period' => request('period', 'this_week'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="back-btn">
        <i class="align-middle" data-feather="arrow-left"></i> Back to Dashboard
    </a>
</div>

<div class="row">
    <div class="col-12">
        <div class="">
            <div class="card-body">
                @if($users->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No SEs found in this category for the selected period.</p>
                    </div>
                @else
                    <div class="table-responsive custom-table">
                        <div class="card-header table-head-area">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    {{ $categoryLabel ?? 'All SEs' }}
                                    {{-- <span class="badge bg-primary ms-2">{{ $users->count() }} SEs</span> --}}
                                </h5>
                                <div>
                                    <span class="text-muted">
                                        {{ $startDate }} - {{ $endDate }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="user-name-head">Name</th>
                                    <th>Total Classes</th>
                                    <th>Present</th>
                                    <th>Attendance %</th>
                                    <th>Category</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $index => $user)
                                    @php
                                        $percentage = $user->attendance_percentage ?? 0;
                                        $category = $percentage >= 80 ? 'above_80' : 
                                                   ($percentage >= 70 ? 'between_70_80' : 
                                                   ($percentage >= 60 ? 'between_60_70' : 'below_60'));
                                        $badgeColor = $percentage >= 80 ? 'success' : 
                                                     ($percentage >= 70 ? 'info' : 
                                                     ($percentage >= 60 ? 'warning' : 'danger'));
                                    @endphp
                                    <tr>
                                        <td>{{ $users->firstItem() + $index }}</td>
                                        <td>
                                            <div class="user-detail">
                                                <strong>{{ $user->name }}</strong>
                                                <br>
                                                <small class="text-muted"><strong>Email: </strong>{{ $user->email }}</small><br>
                                                <small class="text-muted"><strong>Phone: </strong>{{ $user->phone ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $user->total_classes }}</td>
                                        <td>{{ $user->present_count }}</td>
                                        <td>
                                            <span class="badge bg-{{ $badgeColor }}">
                                                {{ $percentage }}%
                                            </span>
                                        </td>
                                        <td>
                                            @switch($category)
                                                @case('above_80')
                                                    <span class="badge bg-success">80%+</span>
                                                    @break
                                                @case('between_70_80')
                                                    <span class="badge bg-info">70-80%</span>
                                                    @break
                                                @case('between_60_70')
                                                    <span class="badge bg-warning">60-70%</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-danger">&lt;60%</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-center">
                                                <a href="{{ route('admin.attendance.user', ['user_id' => $user->id, 'period' => request('period', 'this_week'), 'category' => request('category', 'all'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" 
                                                class="dom-primary-btn gap-2">
                                                    <i class="align-middle" data-feather="eye"></i> View
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection