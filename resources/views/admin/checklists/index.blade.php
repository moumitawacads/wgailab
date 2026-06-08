@extends('admin.layout')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0"><strong>Checklist</strong> Management</h1>
        <a href="{{ route('admin.checklists.create') }}" class="dom-primary-btn">
            <i class="align-middle me-1" data-feather="plus"></i> Add Checklist Item
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" id="success-alert" style="background-color: #d1fae5; color: #065f46; padding: 1rem; border: 1px solid #10b981; border-radius: 0.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
        <span>
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </span>
        <button type="button" onclick="this.parentElement.style.display='none'" style="background: none; border: none; color: #065f46; cursor: pointer; font-size: 1.2rem;">&times;</button>
    </div>
@endif

    
        <div class="table-responsive custom-table">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Target</th>
                        <th>Status</th>
                        <th>Assignments</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="sortable">
                    @if($checklists->count() > 0)
                        @foreach($checklists as $checklist)
                        <tr data-id="{{ $checklist->id }}">
                            <td>
                                <strong>{{ $checklist->title }}</strong>
                                @if($checklist->description)
                                    <br><small class="text-muted">{{ Str::limit($checklist->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($checklist->target_type == 'all')
                                    <span class="badge bg-info">All Participants</span>
                                @else
                                    <span class="badge bg-warning">Selected ({{ $checklist->users->count() }})</span>
                                @endif
                            </td>
                            <td>
                                @if($checklist->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewUsersModal{{ $checklist->id }}">
                                    View Assigned
                                </button>
                            </td>
                            <td class="d-md-table-cell">
                                <div class="action-wrapper">
                                    <a href="{{ route('admin.checklists.edit', $checklist) }}">
                                        <button class="bg-black btn-sm">
                                            <i class="align-middle me-1" data-feather="edit"></i> <span class="align-middle"></span>
                                        </button>
                                    </a>
                                    <form action="{{ route('admin.checklists.destroy', $checklist) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="align-middle" data-feather="trash-2"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        
                        {{-- Modal for viewing assigned users --}}
                        <div class="modal fade" id="viewUsersModal{{ $checklist->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Assigned Users - {{ $checklist->title }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        @if($checklist->target_type == 'all')
                                            <p>All participants are assigned to this task.</p>
                                        @else
                                            <ul>
                                                @foreach($checklist->users as $user)
                                                    <li>{{ $user->name }} ({{ $user->email }})</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5">
                                <div class="text-center py-5">
                                    <p class="text-muted">No checklist items created yet.</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
@endsection