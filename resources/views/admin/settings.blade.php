@extends('admin.layout')
@section('content')

    <h1 class="h3 mb-3"><strong>{{$title}}</strong> Settings</h1>
    @if ($errors->any())
        <div class="alert alert-danger" style="color: red; background: #fee2e2; border: 1px solid #ef4444; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <ul style="margin: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success" id="success-alert" style="background-color: #d1fae5; color: #065f46; padding: 1rem; border: 1px solid #10b981; border-radius: 0.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
            <span>
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            </span>
            <button type="button" onclick="this.parentElement.style.display='none'" style="background: none; border: none; color: #065f46; cursor: pointer; font-size: 1.2rem;">&times;</button>
        </div>
    @endif
    <div class="row">
        <div class="col-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form enctype="multipart/form-data" method="post" action="{{ route('save_settings') }}"> 
                        @csrf
                    <div class="mb-3">
                        <label for="admin_email" class="form-label">Admin Email</label>
                        <input type="text" name="admin_email" class="form-control" placeholder="Enter Admin Email" value="{{ admin_settings('admin_email') }}">
                    </div>

                    <div class="mb-3">
                        <label for="compensation_list_email" class="form-label">Email to Notify Compensation List</label>
                        <input type="text" name="compensation_list_email" class="form-control" placeholder="Enter Email" value="{{ admin_settings('compensation_list_email') }}">
                        <div class="form-text">
                            You can use commas (,) to add multiple email addresses.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="stipend_pay_out_emails" class="form-label">Stipend Payout Notification Email</label>
                        <input type="text" name="stipend_pay_out_emails" class="form-control" placeholder="Enter Email" value="{{ admin_settings('stipend_pay_out_emails') }}">
                        <div class="form-text">
                            You can use commas (,) to add multiple email addresses.
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="mb-3 col-12 col-lg-6">
                            <label for="week_start_day" class="form-label">Week Start Day</label>
                            <select name="week_start_day" class="form-control">
                                <option value="">Select</option>
                                <option value="0" {{ admin_settings('week_start_day') == '0' ? 'selected' : '' }}>Sunday</option>
                                <option value="1" {{ admin_settings('week_start_day') == '1' ? 'selected' : '' }}>Monday</option>
                                <option value="2" {{ admin_settings('week_start_day') == '2' ? 'selected' : '' }}>Tuesday</option>
                                <option value="3" {{ admin_settings('week_start_day') == '3' ? 'selected' : '' }}>Wednesday</option>
                                <option value="4" {{ admin_settings('week_start_day') == '4' ? 'selected' : '' }}>Thursday</option>
                                <option value="5" {{ admin_settings('week_start_day') == '5' ? 'selected' : '' }}>Friday</option>
                                <option value="6" {{ admin_settings('week_start_day') == '6' ? 'selected' : '' }}>Saturday</option>
                            </select>
                        </div>
                        <div class="mb-3 col-12 col-lg-6">
                            <label for="stipend_amount" class="form-label">Stipend Amount</label>
                            <input type="text" class="form-control" value="{{admin_settings('stipend_amount')}}" name="stipend_amount" />
                        </div>    
                        <!--<div class="mb-3 col-12 col-lg-6">
                            <label for="week_end_day" class="form-label">Week End Day</label>
                            <select name="week_end_day" class="form-control">
                                <option value="">Select</option>
                                <option value="0" {{ admin_settings('week_end_day') == '0' ? 'selected' : '' }}>Sunday</option>
                                <option value="1" {{ admin_settings('week_end_day') == '1' ? 'selected' : '' }}>Monday</option>
                                <option value="2" {{ admin_settings('week_end_day') == '2' ? 'selected' : '' }}>Tuesday</option>
                                <option value="3" {{ admin_settings('week_end_day') == '3' ? 'selected' : '' }}>Wednesday</option>
                                <option value="4" {{ admin_settings('week_end_day') == '4' ? 'selected' : '' }}>Thursday</option>
                                <option value="5" {{ admin_settings('week_end_day') == '5' ? 'selected' : '' }}>Friday</option>
                                <option value="6" {{ admin_settings('week_end_day') == '6' ? 'selected' : '' }}>Saturday</option>
                            </select>                                        
                        </div> -->
                    </div>
                    <div class="mb-3">
                        <label for="media_url" class="form-label">Media URL (Video)</label>
                        <input type="url" name="media_url" class="form-control" placeholder="https://youtube.com/watch?v=..." value="{{ old('media_url', admin_settings('media_url')  ?? '') }}">
                        <small class="text-muted">Supported: Videos (YouTube, Vimeo, direct MP4)</small>
                        
                        @if(admin_settings('media_url'))
                            <div class="mt-2">
                                <label class="form-label">Current Media Preview:</label>
                                <div class="mt-1">
                                    <video src="{{ admin_settings('media_url') }}" style="max-height: 100px;" controls></video>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">                                        
                        <div class="mt-3">
                            <button type="submit" class="dom-primary-btn"><i class="align-middle me-1" ></i>Save<span class="align-middle"></span></button>    
                        </div>                                        
                    </div>
                    </form>
        </div>
    </div>


@endsection