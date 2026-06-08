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

    <form method="GET" class="row mb-3 mt-3 align-items-end">

        <div class="col-md-4">
            <label>Select a day from desired Week</label>
            <input type="date" name="week_date" id="week_date" class="form-control" value="{{ request('week_date') }}">
        </div>

        <div class="col-md-8 d-flex justify-content-between">
            <div class="d-flex gap-10">
                <button class="dom-primary-btn me-2">Filter</button>
                <a href="{{ route('admin.attendance_record') }}" class="back-btn">Reset</a>
            </div>

            

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

        <div class="col-lg-12">
            <div class="alert alert-danger">
                <p><strong>Disclaimer:</strong> If you have missed a session and have completed the consideration form you are still required to complete the Domework for the session(s) that were missed. All participants require an overall attendance rate of 80% to remain eligible for the Street Entrepreneurs Program.</p>
            </div>
        </div>
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
                            <th>Compensation Request</th>
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
                                    @if(is_null($row->compensation_status))
                                        <span class="badge bg-secondary">Not Requested</span>

                                    @elseif($row->compensation_status == 0)
                                        <span class="badge bg-warning">Pending</span>

                                    @elseif($row->compensation_status == 1)
                                        <span class="badge bg-success">Approved</span>

                                    @elseif($row->compensation_status == 2)
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-10">
                                        <button 
                                            class="btn-sm dom-primary-btn view-schedules"
                                            data-user="{{ $row->user_id }}"
                                            data-start="{{ $row->week_start }}"
                                            data-end="{{ $row->week_end }}"

                                            @if(auth()->user()->role == 'se')
                                            data-url="{{ route('se.schedule_details') }}">
                                            @else
                                            data-url="{{ route('admin.schedule_details') }}">
                                            @endif
                                            View
                                        </button>
                                        @php
                                            $weekEnd = \Carbon\Carbon::parse($row->week_end);
                                            $lastAllowedDate = $weekEnd->copy()->addDays(1); // Sunday + 2 = Tuesday
                                        @endphp
                                            @if(now()->lte($lastAllowedDate) && $row->settled_stipend_amount < admin_settings('stipend_amount'))
                                            @if(is_null($row->compensation_status))
                                            <button 
                                            class="site-sec-btn compensation_form"
                                            data-user="{{ $row->user_id }}"
                                            data-username="{{$row->user_name}}"
                                            data-start="{{ $row->week_start }}"
                                            data-end="{{ $row->week_end }}"
                                            data-report-id="{{ $row->id }}">
                                            Compensate
                                        </button>
                                        @endif
                                        @endif
                                    </div
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



<!-- Compensation Form Modal -->
<div class="modal fade" id="compensationFormModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Compensation Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-danger">
                    <p><strong>Disclaimer:</strong> If you have missed a session and have completed the consideration form you are still required to complete the Domework for the session(s) that were missed. All participants require an overall attendance rate of 80% to remain eligible for the Street Entrepreneurs Program.</p>
                </div>
                <form id="compensationForm">
                    @csrf
                    <!-- Hidden fields -->
                    <input type="hidden" name="user_id" id="comp_user_id">
                    <input type="hidden" name="week_start" id="comp_week_start">
                    <input type="hidden" name="week_end" id="comp_week_end">
                    <input type="hidden" name="report_id" id="comp_report_id">

                    <!-- Display Info -->
                    <div class="mb-3">
                        <label class="form-label">Requested By:</label>
                        <input type="text" id="display_user" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Week Range</label>
                        <input type="text" id="display_week" class="form-control" readonly>
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label class="form-label">Compensation Notes</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Enter notes..."></textarea>
                    </div>

                    <!-- Submit -->
                    <div class="text-end">
                        <button type="submit" class="dom-primary-btn">Submit</button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    const COMPENSATION_URL = "{{ route('se.compensation.store') }}";
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