@extends('admin.layout')
@section('content')

    <h1 class="h3 mb-3"><strong>Assigned</strong> Homework</h1>

        @if(session('success'))
        <div class="alert alert-success" id="success-alert" style="background-color: #d1fae5; color: #065f46; padding: 1rem; border: 1px solid #10b981; border-radius: 0.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
            <span>
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            </span>
            <button type="button" onclick="this.parentElement.style.display='none'" style="background: none; border: none; color: #065f46; cursor: pointer; font-size: 1.2rem;">&times;</button>
        </div>
    @endif

    <div class="row mt-4">
        <div class="col-12">
            <div class="">
                <div class="card-header">
                    <h5 class="card-title">Upcoming Classes</h5>
                </div>

                <div class="card-body custom-table">
                    
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Session Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($sessions as $key => $session)
                                <tr>
                                    @php
                                        $domework = $session->assignedDomeworks->first();
                                    @endphp
                                    <td>{{$session->session_name}}</td>
                                    <td>{{ $domework->status == "0" ? 'Not Completed' : 'Completed' }}</td>
                                    <td class="action-wrapper dom-action"> 
                                        <div class="d-flex gap-10 justify-content-center">
                                            {{-- @if($domework->status==0) --}}
                                        <a target="_blank" href="{{ route('se.session.start', $session->id) }}"
                                            class="dom-primary-btn">

                                                <i class="align-middle me-1" data-feather="book"></i>
                                                Edit/View
                                            </a>
                                        @if($domework->status!=0)
                                        <a href="{{ route('worksheet.pdf', $session->id) }}"
                                            class="down-btn">

                                                <i class="align-middle me-1" data-feather="download"></i>
                                                Download
                                            </a>
                                        @endif   
                                        </div> 
                                    </td>
                                </tr>
                            @endforeach
                                
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


@endsection