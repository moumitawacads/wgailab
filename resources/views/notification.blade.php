@extends('admin.layout')
@section('content')

    <h1 class="h3 mb-3"><strong>Notifications</strong> List</h1>

    <div class="row">
        

    <div class="list-group">
        @if(count($notifications)>0)
        @foreach($notifications as $item)
        <a href="#" class="list-group-item">
            <div class="row g-5 align-items-center">
                <div class="col-auto">
                    <i class="text-{{$item->type ?? 'warning'}}" data-feather="{{$item->icon ?? 'bell'}}"></i>
                </div>
                <div class="col-10">
                    <div class="no-main-head">{{$item->title}}</div>
                    <div class="text-muted small no-para">{{$item->message}}</div>
                    <div class="text-muted small no-time">{{ $item->created_at->diffForHumans() }}</div>
                </div>
            </div>
        </a>
        @endforeach
        @else
        No notifications found!!
        @endif
    </div>

    {{ $notifications->links() }}


    </div>


@endsection