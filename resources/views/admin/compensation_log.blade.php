@extends('admin.layout')
@section('content')

    <h1 class="h3 mb-3"><strong>Compensation </strong> Requests</h1>

    @if(session('success'))
        <div class="alert alert-success" id="success-alert" style="background-color: #d1fae5; color: #065f46; padding: 1rem; border: 1px solid #10b981; border-radius: 0.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
            <span>
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            </span>
            <button type="button" onclick="this.parentElement.style.display='none'" style="background: none; border: none; color: #065f46; cursor: pointer; font-size: 1.2rem;">&times;</button>
        </div>
    @endif

    
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
                            <th>Status</th>
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
                                    @if($row->status==0)
                                    <span class="badge bg-warning">Pending</span>
                                    @elseif($row->status==1)
                                    <span class="badge bg-success">Approved</span>
                                    @elseif($row->status==2)
                                    <span class="badge bg-danger">Cancelled</span>
                                    @endif
                                </td>
                                <td class="d-flex gap-2">
                                    <button 
                                        class=" btn-sm dom-primary-btn view-schedules"
                                        data-user="{{ $row->user_id }}"
                                        data-start="{{ $row->week_start }}"
                                        data-end="{{ $row->week_end }}"
                                        data-note="{{$row->notes}}"
                                        data-url="{{ route('admin.schedule_details') }}">
                                        View
                                    </button>

                                    {{-- Approve / Reject Dropdown --}}
                                    @if(!is_null($row->status) && $row->status == 0)
                                        <div class="dropdown">
                                            <button class="btn-sm dropdown-toggle custom-nav action-btn" type="button" data-bs-toggle="dropdown">
                                                Action
                                            </button>

                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="#" 
                                                    class="dropdown-item approve-request"
                                                    data-id="{{ $row->id }}"
                                                    data-weekid="{{ $row->week_id }}"
                                                    data-status="1">
                                                        Approve
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" 
                                                    class="dropdown-item reject-request"
                                                    data-id="{{ $row->id }}"
                                                    data-weekid="{{ $row->week_id }}"
                                                    data-status="2">
                                                        Reject
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
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
                <div id="schedule-notes" class="alert alert-info d-none">
                    <strong>Notes:</strong>
                    <div id="notes-content"></div>
                </div>
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