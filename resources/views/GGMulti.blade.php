<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<style>
    html{
        height: 100%;
    }
    body{
        margin: 0;
        padding: 0;
        border: 0;
        background-color: #e2e1e4;
        overflow: hidden;
        font-family: 仿宋;
    }
    .box-holder{
        height: auto;
    }
    .home-select{
        float: left;
        width: 198px;
        height: 148px;
        overflow:hidden;
        cursor: pointer;
        border-radius: 10px;
        border: rgba(132,132,132,0.8) solid 1px;
        margin-left: 25px;
        margin-bottom: 15px;
    }
    .home-select p{
        margin-left: 5px;
    }
</style>
<body>
<p style="text-align: center;cursor: default;font-size: 25px">多人游戏房间</p>
<p style="cursor: default;margin-left: 25px">现有房间数{{$homeNumber}}</p>
<div class="box-holder">
    @foreach($hh as $h)
        @if($h->end == 0)
            <div class="home-select" id="{{$h->id}}">
                <p>Id:{{$h->id}}</p>
                <p>最大玩家:{{$h->maxplayer}}</p>
                <p>游戏轮次:{{$h->maxturn}}</p>
            </div>
        @endif
    @endforeach
    <div style="clear: both"></div>
</div>

<form method="post" style="margin-left: 25px">
    @csrf
    <div style="cursor: default">建立房间</div>
    <input style="display: block;margin-bottom: 5px" name="persons" id="persons" type="text" placeholder="最大房间人数">
    <input style="display: block;margin-bottom: 5px" name="turn" id="turn" type="text" placeholder="最大轮数">
    <input style="display: block" type="submit" value="提交">
</form>
<script src="/extra/canvas/canvas.js"></script>
<script>

    $(function () {
        @foreach($hh as $h)
            $('#' + '{{$h->id}}').click(function () {
                window.location.href = '/multiHome/{{$h->id}}'
        })
        @endforeach
        setInterval(sendAjax,1000);
    })
    function sendAjax() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'post',
            url: '/multiHome/',
            datatype: 'json',
            data: {
                'method': 'update',

            },
            success:function (data) {

            },
            error: function(request, status, error){
                alert(error);
            },
        })
    }
</script>
</body>
</html>
