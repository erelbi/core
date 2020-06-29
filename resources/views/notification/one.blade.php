<?php
$item = \App\Notification::where([
    "user_id" => auth()->id(),
    "id" => request('notification_id')
])->first();
if(!$item){
    if(auth()->user()->isAdmin() && \App\AdminNotification::find(request('notification_id'))->exists()){
        header("Location: " . route('system_notification',[
                "notification_id" => request('notification_id')
        ]), true);
        exit();
    }else{
        return redirect()->back();
    }
}
?>

@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12">
            @include('errors')
            <div class="timeline">
                <div class="time-label">
                    <span class="bg-green">
                        {{\Carbon\Carbon::parse($item->created_at)->format("d.m.Y")}}
                    </span>
                </div>
                <div>
                    @if($item->read)
                        <i class="far fa-bell @if($item->type=="error") bg-red @else bg-blue @endif"></i>
                    @else
                        <i class="fas fa-bell @if($item->type=="error") bg-red @else bg-blue @endif"></i>
                    @endif
                    <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> {{\Carbon\Carbon::parse($item->created_at)->format("h:i:s")}}</span>
        
                        <h3 class="timeline-header">
                            @if(!$item->read)<a href="javascript:void(0)">@endif
                                {{$item->title}}
                                @if(!$item->read)</a>@endif
                        </h3>
        
                        <div class="timeline-body">
                            {!! $item->message !!}
                        </div>
                        <div class="timeline-footer">
                            @if(!$item->read)
                                <a class="btn btn-primary btn-xs mark_read"
                                    notification-id="{{$item->id}}">{{__('Okundu Olarak İşaretle')}}</a>
                            @endif
                            <a class="btn btn-danger btn-xs delete_not" notification-id="{{$item->id}}">{{__('Sil')}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('.mark_read').click(function () {
            var data = new FormData();
            data.append('notification_id', $(this).attr('notification-id'));
            request('{{route('notification_read')}}', data, function (response) {
                location.reload();
            }, function(response){
                var error = JSON.parse(response);
                showSwal(error.message,'error',2000);
            });
        });
        $('.delete_not').click(function () {
            var data = new FormData();
            data.append('notification_id', $(this).attr('notification-id'));
            request('{{route('notification_delete')}}', data, function (response) {
                partialPageRequest("{{route('all_user_notifications')}}");
            }, function(response){
                var error = JSON.parse(response);
                showSwal(error.message,'error',2000);
            });
        });
    </script>
@endsection