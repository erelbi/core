<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="{{asset('css/terminal/xterm.min.css')}}" rel="stylesheet" type="text/css"/>
</head>
<body onload="start({{$id}})">

<div class="container">
    <div id="terminal"></div>
</div>

<script src="{{asset('js/terminal/xterm.min.js')}}"></script>
<script src="{{asset('js/terminal/main.js')}}"></script>
</body>
</html>