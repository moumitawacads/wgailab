@extends('admin.layout')

@section('content')
<div class="mb-4">
    <h1 class="h3 mb-0"><strong>Edit</strong> Checklist Item</h1>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.checklists.update', $checklist) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                       id="title" name="title" value="{{ old('title', $checklist->title) }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="3">{{ old('description', $checklist->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="link" class="form-label">Link (Optional)</label>
                <input type="url" class="form-control @error('link') is-invalid @enderror" 
                       id="link" name="link" value="{{ old('link', $checklist->link) }}" placeholder="https://example.com">
                @error('link')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label">Target Audience</label>
                <div class="mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="target_type" id="target_all" 
                               value="all" {{ old('target_type', $checklist->target_type) == 'all' ? 'checked' : '' }}>
                        <label class="form-check-label" for="target_all">
                            All Participants
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="target_type" id="target_selected" 
                               value="selected" {{ old('target_type', $checklist->target_type) == 'selected' ? 'checked' : '' }}>
                        <label class="form-check-label" for="target_selected">
                            Select Specific Participants
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="mb-3" id="userSelectionArea" style="display: {{ $checklist->target_type == 'selected' ? 'block' : 'none' }};">
                <label class="form-label">Select Participants</label>
                
                <select class="form-select select2 @error('selected_users') is-invalid @enderror" 
                        name="selected_users[]" id="selected_users" multiple size="10">
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" 
                            {{ in_array($user->id, $selectedUsers) ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
                @error('selected_users')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            
            <div class="mt-4 d-flex gap-10">
                <button type="submit" class="dom-primary-btn">Update</button>
                <a href="{{ route('admin.checklists.index') }}" class="back-btn">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const targetAllRadio = document.getElementById('target_all');
        const targetSelectedRadio = document.getElementById('target_selected');
        const userSelectionArea = document.getElementById('userSelectionArea');
        const selectAllBtn = document.getElementById('selectAllBtn');
        const deselectAllBtn = document.getElementById('deselectAllBtn');
        const usersSelect = document.getElementById('selected_users');
        
        function toggleUserSelection() {
            if (targetSelectedRadio.checked) {
                userSelectionArea.style.display = 'block';
            } else {
                userSelectionArea.style.display = 'none';
            }
        }
        
        targetAllRadio.addEventListener('change', toggleUserSelection);
        targetSelectedRadio.addEventListener('change', toggleUserSelection);
        
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                for (let i = 0; i < usersSelect.options.length; i++) {
                    usersSelect.options[i].selected = true;
                }
            });
        }
        
        if (deselectAllBtn) {
            deselectAllBtn.addEventListener('click', function() {
                for (let i = 0; i < usersSelect.options.length; i++) {
                    usersSelect.options[i].selected = false;
                }
            });
        }
    });

    jQuery(document).ready(function() {
        jQuery('.select2').select2({
            width: '100%',
            placeholder: "Select option"
        });
    });
</script>
@endpush
@endsection