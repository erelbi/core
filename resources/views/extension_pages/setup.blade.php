@extends('layouts.app')

@section('content_header')
    <h1>{{request('server')->name}} sunucusu <b>{{$extension->name}}</b> ayarları</h1>
@stop

@section('content')

    <button class="btn btn-success" onclick="history.back()">{{__("Geri Dön")}}</button>
    <form action="{{route('extension_server_settings',[
                        "extension_id" => request()->route('extension_id'),
                        "server_id" => request()->route('server_id')
                    ])}}" method="POST">
        @csrf
        @foreach($extension->database as $item)
            @include('l.inputs',[
                "inputs" => [
                    $item["name"] => $item["variable"] . ":" . $item["type"]
                ]
            ])
        @endforeach
        <button type="submit" class="btn btn-success">Kaydet</button>
    </form>
@endsection