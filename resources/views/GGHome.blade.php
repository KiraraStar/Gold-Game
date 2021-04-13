<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>home</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
        cursor: default;
    }
</style>
<body>
<p>游戏房间</p>
<p id="timer"></p>
<p>最大人数：{{$info->maxplayer}}</p>
<p id="nownum">当前人数：{{$info->nowplayer}}</p>
<p>当前房间id:{{$id}}</p>
<div id="nameHolder">
    @foreach($name as $n)
        <p>player:{{$n}}</p>
    @endforeach
</div>
<div>
    @for($i=0;$i< $turn ;$i++)
        @php($pointi = "point".$i)
        <input type="text" placeholder="输入点数" name="{{$pointi}}" id="{{$pointi}}">
    @endfor
    <button type="button"  id="submit">提交</button>
</div>
<div id="last-score"></div>
</body>
<script>
    var gameFlag = false;
    var sendFlag = false;
    var waitTime = 30;
    $(function () {
        setInterval(sendAjax,1500)
        setInterval(gameTimer,1000)
        $('#submit').click(function () {
            sendScore()
        })
    })
    function gameTimer() {
        if (gameFlag && waitTime > 0){
            waitTime -= 1;
            $('#timer').html("开始，请提交结果！" + waitTime)
        }
    }
    function sendAjax() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'post',
            url: '/multiHome/' + {{$id}},
            datatype: 'json',
            data: {
                'method': 'update',
                'aid': '{{$id}}',
            },
            success:function (data) {
                $('#nownum').html('当前人数：' + data.num);
                $('#nameHolder').html('')
               for (let i=0;i<data.name.length;i++){
                   $('#nameHolder').append('<p>player:' + data.name[i] + '</p>')
                   console.log(data.name[i])
                   if (data.gf === 'true' && gameFlag === false){
                       gameFlag = true
                   }
                   if(data.endFlag === 'true'){
                       for(let i=0;i<{{$turn}};i++){
                           $('#point' + i).remove();
                       }
                       $('#submit').remove();
                       let scoreabord = "";
                       for(let i=0;i<{{$turn}};i++){
                           scoreabord += "<p>" + data.win[i] + "</p>";
                       }
                       scoreabord += "<p>最终成绩:" + data.endscore + "</p>"
                       $('#last-score').html(scoreabord);
                   }
               }
            },
            error: function(request, status, error){
                alert(error);
            },
        })
    }
   function sendScore() {
       $.ajaxSetup({
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           }
       });
       var thisPlayerScore = ''
       for(let i=0;i<{{$turn}};i++){
           thisPlayerScore += '<' + $('#point'+ (i)).val() + '>'
       }
       $.ajax({
           type: 'post',
           url: '/multiHome/' + {{$id}},
           datatype: 'json',
           data: {
               'method': 'score',
               'score': thisPlayerScore,
           },
           success:function (data) {
               for (let i=0;i<data.name.length;i++){
                   console.log(data.name[i])
               }
           },
           error: function(request, status, error){
               alert(error);
           },
       })
   }
</script>
</html>
