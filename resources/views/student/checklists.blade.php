@extends('admin.layout')

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4">
        <h1 class="h3 mb-0"><strong>My</strong> Checklists</h1>
        <p class="text-muted">Track and complete your pending tasks</p>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="">
                <div class="card-title d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Tasks & Requirements</h5>
                    <div class="filter-area">
                        <div class="btn-group dom-primary-btn" role="group">
                            <button type="button" class="btn-sm filter-btn active" data-filter="all">
                                All
                            </button>
                            <button type="button" class="btn-sm filter-btn" data-filter="pending">
                                Pending
                            </button>
                            <button type="button" class="btn-sm filter-btn" data-filter="completed">
                                Completed
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($checklists->count() > 0)
                        <div class="table-responsive custom-table">
                            <table class="table table-hover" id="checklists-table">
                                <thead>
                                    <tr>
                                        <th width="50">Action</th>
                                        <th>Status</th>
                                        <th>Task</th>
                                        <th>Description</th>
                                        <th width="120">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($checklists as $checklist)
                                        @php
                                            $userChecklist = \Illuminate\Support\Facades\DB::table('checklist_user')
                                                ->where('checklist_id', $checklist->id)
                                                ->where('user_id', auth()->id())
                                                ->first();
                                            $isCompleted = $userChecklist && $userChecklist->is_completed;
                                            $completedAt = $userChecklist ? $userChecklist->completed_at : null;
                                        @endphp
                                        <tr class="checklist-row {{ $isCompleted ? 'completed-row' : 'pending-row' }}" 
                                            data-status="{{ $isCompleted ? 'completed' : 'pending' }}">
                                            <td>
                                                <div class="form-check p-0">
                                                    <input type="checkbox" 
                                                           class="action-check checklist-checkbox" 
                                                           data-id="{{ $checklist->id }}"
                                                           data-url="{{ route('checklist.complete', $checklist) }}"
                                                           data-incomplete-url="{{ route('checklist.incomplete', $checklist) }}"
                                                           {{ $isCompleted ? 'checked' : '' }} {{ $isCompleted ? 'disabled' : '' }}>
                                                </div>
                                            </td>
                                            <td><span class="badge {{ $isCompleted ? 'bg-success' : 'bg-warning' }}">{{ $isCompleted ? 'Completed' : 'Pending' }}</span></td>
                                            <td>
                                                <strong>{{ $checklist->title }}</strong>
                                                @if($completedAt)
                                                    <br>
                                                    <small class="text-success">
                                                        Completed At: {{ \Carbon\Carbon::parse($completedAt)->format('M d, Y h:i A') }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $checklist->description ?? 'No description provided' }}
                                            </td>
                                            <td>
                                                @if($checklist->link)
                                                    <a href="{{ $checklist->link }}" target="_blank" class="dom-primary-btn">
                                                        <i class="align-middle me-1" data-feather="external-link"></i> View
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $checklists->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5>All caught up!</h5>
                            <p class="text-muted">You have no pending tasks at the moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.checklist-checkbox');
    const filterBtns = document.querySelectorAll('.filter-btn');
    const rows = document.querySelectorAll('.checklist-row');
    
    // Handle checkbox changes
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function(e) {
            const checklistId = this.dataset.id;
            const isChecked = this.checked;
            const url = isChecked ? this.dataset.url : this.dataset.incompleteUrl;
            const row = this.closest('tr');
            
            // Show loading state
            Swal.fire({
                title: isChecked ? 'Completing task...' : 'Marking as incomplete...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success) {
                    // Update row classes
                    if (isChecked) {
                        row.classList.remove('pending-row');
                        row.classList.add('completed-row');
                        row.dataset.status = 'completed';
                        
                        // Update completed timestamp display
                        const now = new Date();
                        const formattedDate = now.toLocaleDateString('en-US', { 
                            month: 'short', 
                            day: 'numeric', 
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        
                        const strongElement = row.querySelector('td:nth-child(2) strong');
                        if (strongElement && !row.querySelector('td:nth-child(2) small')) {
                            const completedSpan = document.createElement('br');
                            const smallElement = document.createElement('small');
                            smallElement.className = 'text-success';
                            smallElement.innerHTML = `Completed: ${formattedDate}`;
                            strongElement.parentNode.appendChild(completedSpan);
                            strongElement.parentNode.appendChild(smallElement);
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Task Completed!',
                            text: 'Great job! Keep up the good work.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        row.classList.remove('completed-row');
                        row.classList.add('pending-row');
                        row.dataset.status = 'pending';
                        
                        // Remove completed timestamp
                        const timeElement = row.querySelector('td:nth-child(2) small.text-success');
                        if (timeElement) {
                            timeElement.remove();
                            const brElement = timeElement.previousSibling;
                            if (brElement && brElement.tagName === 'BR') {
                                brElement.remove();
                            }
                        }
                        
                        Swal.fire({
                            icon: 'info',
                            title: 'Task Marked as Incomplete',
                            text: 'You can complete it again when ready.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                    
                    // Apply current filter
                    const activeFilter = document.querySelector('.filter-btn.active').dataset.filter;
                    applyFilter(activeFilter);
                } else {
                    // Revert checkbox
                    checkbox.checked = !isChecked;
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'An error occurred. Please try again.',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                checkbox.checked = !isChecked;
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Network error. Please try again.',
                    confirmButtonColor: '#dc3545'
                });
            });
        });
    });
    
    // Filter functionality
    function applyFilter(filter) {
        rows.forEach(row => {
            const status = row.dataset.status;
            if (filter === 'all') {
                row.style.display = '';
            } else if (filter === 'pending' && status === 'pending') {
                row.style.display = '';
            } else if (filter === 'completed' && status === 'completed') {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Update active button
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            applyFilter(filter);
        });
    });
    
    // Initialize Feather icons if you're using them
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endpush

@push('styles')
<!--<style>
    .completed-row {
        background-color: #f8f9fa;
        opacity: 0.85;
    }
    
    .completed-row td:first-child .form-check-input {
        background-color: #28a745;
        border-color: #28a745;
    }
    
    .completed-row strong {
        text-decoration: line-through;
        color: #6c757d;
    }
    
    .pending-row {
        background-color: white;
    }
    
    .filter-btn.active {
        background-color: #0d6efd;
        color: white;
    }
    
    .table-responsive {
        min-height: 400px;
    }
    
    .checklist-checkbox {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
    
    .checklist-checkbox:checked {
        background-color: #28a745;
        border-color: #28a745;
    }
</style>-->
@endpush
@endsection