@extends('admin.layout')
@section('content')

<h1 class="h3 mb-3"><strong>Attendance</strong> Log</h1>

@if(session('success'))
    <div class="alert alert-success" id="success-alert" style="background-color: #d1fae5; color: #065f46; padding: 1rem; border: 1px solid #10b981; border-radius: 0.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
        <span>
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </span>
        <button type="button" onclick="this.parentElement.style.display='none'" style="background: none; border: none; color: #065f46; cursor: pointer; font-size: 1.2rem;">&times;</button>
    </div>
@endif

<form method="GET" class="row mb-3 mt-3 align-items-end filter-form-wrap">

    <div class="col-md-4">
        <label>Select a day from desired Week</label>
        <input type="date" name="week_date" id="week_date" class="form-control" value="{{ request('week_date') }}">
    </div>

    <div class="col-md-8 d-flex justify-content-between">
        <div class="d-flex">
            <button class="dom-primary-btn me-2">Filter</button>
            <a href="{{ route('admin.attendance_record') }}" class="back-btn">Reset</a>
        </div>

        <a href="{{ route('admin.export_weekly') }}" class="dom-primary-btn">
            Export Excel
        </a>

    </div>

</form>

<div class="row">
    @if(request('week_date'))
        <div class="mb-2 text-muted">
            Showing week:
            {{ \Carbon\Carbon::parse(request('week_date'))->startOfWeek(\Carbon\Carbon::MONDAY)->format('d M Y') }}
            -
            {{ \Carbon\Carbon::parse(request('week_date'))->endOfWeek(\Carbon\Carbon::SUNDAY)->format('d M Y') }}
        </div>
    @endif
    <div class="col-12 col-lg-12 col-xxl-12 d-flex">
        <div class="card custom-table">
        
        <!-- <hr class="my-3"> -->
            <table class="table table-hover my-0">
                <thead>
                    <tr>
                        <th>Week</th>
                        <th>Participant</th>
                        <th>Total Classes</th>
                        <th>Attended Classes</th>
                        <th>Stipend</td>
                        <th>Payment Status</td>
                        <th>Action</td>
                    </tr>
                </thead>

                <tbody>
                    @forelse($weeklyData as $key => $row)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($row->week_start)->format('d M Y').' - '.\Carbon\Carbon::parse($row->week_end)->format('d M Y') ?? '-' }}</td>
                            <td>{{$row->user_name}}</td>
                            <td>{{$row->total_classes}}</td>
                            <td>{{ $row->present_count ?? 0 }}</td>
                            <td>
                                {{$row->settled_stipend_amount}}
                            </td>
                            <td>
                                    @if($row->stipend_payment_status==0)
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($row->stipend_payment_status==1)
                                        <span class="badge bg-success">Paid</span>
                                    @endif
                                </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button 
                                        class="dom-primary-btn btn-sm  view-schedules"
                                        data-user="{{ $row->user_id }}"
                                        data-start="{{ $row->week_start }}"
                                        data-end="{{ $row->week_end }}"
                                        data-url="{{ route('admin.schedule_details') }}">
                                        View 
                                    </button>
                                    @if(!$row->stipend_payment_status)
                                        <form action="{{route('admin.compensation.payment.status-update', $row->id)}}" method="POST">
                                            @csrf
                                            <input type="hidden" value="true" name="payment_status">
                                            <button type="submit" class="action-btn">Pay</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No schedules found</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

            
            {{ $weeklyData->links() }}
            
        </div>
    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Schedule Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div id="schedule-loader">Loading...</div>

                <table class="table table-bordered d-none" id="schedule-table">
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Scheduled Date</th>
                            <th>Scheduled Time</th>
                            <th>Clock-in Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="schedule-data"></tbody>
                </table>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
// document.getElementById('week_date').addEventListener('change', function () {
//     let selectedDate = new Date(this.value);
    
//     // 1 = Monday
//     if (selectedDate.getDay() !== {{admin_settings('week_start_day')}}) {
//         alert('Please select a Monday only.');
//         this.value = '';
//     }
// });
</script>
@endpush
@endsection