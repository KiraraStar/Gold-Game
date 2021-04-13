<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GoldGame1</title>
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
    }
    .beginBtn{
        left: 50%;
        top: 200px;
        transform: translate(-50%,0);
        height: 200px;
        background-color: rgba(152,152,152,0.8);
        width: 300px;
        position: absolute;
        border-radius: 10px;
        border: rgba(99,99,99,0.8) solid;
    }
    .modelBtn{
        margin-top: 10px;
        margin-bottom: 10px;
        height: 79px;
        width: 100%;
        font-size: 35px;
        line-height: 80px;
        text-align: center;
        cursor: pointer;
    }
</style>
<style>
    .typeBtn{
        left: 50%;
        top: 200px;
        transform: translate(-50%,0);
        height: auto;
        background-color: rgba(152,152,152,0.8);
        width: 300px;
        position: absolute;
        border-radius: 10px;
        border: rgba(99,99,99,0.8) solid;
        display: none;
    }
    .typeBtn div{
        display: block;
        text-align: center;
        margin-top: 5px;
    }
    .typeBtn div label{
        display: block;
        font-weight: normal;
        font-size: 20px;
    }
    .typeBtn div span{
        color: red;
        font-size: 10px;
        cursor: default;
        display: none;
    }
    .typeBtn div input{
        border: 0;
        display: block;
        background-color: transparent;
        font-family: 仿宋;
        width: 100%;
        height: 35px;
        font-size: 20px;
        text-align: center;
    }
    .typeBtn div input:focus{
        border: 0;
        outline: none;
    }
    .reBtn{
        display: inline-block;
        float: left;
        height: 25px;
        width: 50%;
        font-size: 20px;
        line-height: 25px;
        text-align: center;
        cursor: pointer;
        margin-top: 15px;
        margin-right: 0;
    }

</style>
<style>
    .gameHolder{
        left: 50%;
        top: 50px;
        transform: translate(-50%,0);
        height: auto;
        background-color: rgba(152,152,152,0.8);
        width: 400px;
        position: absolute;
        border-radius: 10px;
        border: rgba(99,99,99,0.8) solid;
        display: none;
    }
    .gameHolder div{
        overflow: hidden;
        height: auto;
    }
    .gameHolder div div{
        display: inline-block;
        text-align: center;
        float: left;
        width: 100px;
    }
    .gameHolder div div label{
        display: block;
        font-weight: normal;
        font-size: 20px;
    }
    .gameHolder div div input{
        border: 0;
        display: block;
        background-color: transparent;
        font-family: 仿宋;
        width: 100%;
        height: 35px;
        font-size: 20px;
        text-align: center;
    }
    .gameHolder div div input:focus{
        border: 0;
        outline: none;
    }
    .gameHolder p{
        text-align: center;
    }
    .last1{
        font-size: 20px;
    }
    .last1,.last2{
        cursor: default;
    }
    .subBtn{
        display: block;
        height: 25px;
        width: 100%;
        font-size: 20px;
        line-height: 25px;
        text-align: center;
        cursor: pointer;
        margin-top: 15px;
        margin-right: 0;
        margin-bottom: 15px;
    }
</style>
<style>
    .GG-login{
        top: 0;
        position: absolute;
        right: 0;
        width: auto;
        height: 30px;
        line-height: 30px;
        font-size: 20px;
        text-align: center;
        cursor: pointer;
        margin-right: 20px;
    }
</style>
<body>
<canvas id="Mycanvas"></canvas>
@if(session('isLogin')=='yes')
    <div class="GG-login">欢迎你,{{session('nameLogin')}}</div>
    <div class="GG-login" id="GG-score" style="top: 30px">查看结果记录</div>
    <!--div class="GG-login" id="GG-now" style="top: 90px">当前在线人数:</div-->
    <div class="GG-login" id="GG-exit" style="top: 60px">退出登录</div>
@else
    <div class="GG-login" id="GG-login">注册/登录</div>
@endif
<div class="beginBtn">
    <div class="modelBtn" id="SP">单人模式</div>
    <div style="height: 2px;background-color: rgba(99,99,99,0.8);"></div>
    <div class="modelBtn" id="MP">多人模式</div>
</div>
<div class="typeBtn">
    <div>
        <p style="font-size: 30px;font-weight: bolder;cursor: default">黄 金 点</p>
        <label for="PNumber">选择游戏人数</label>
        <input type="text" id="PNumber" name="PNumber" value="" placeholder="人数">
        <span id="Err1">最多供15人游玩</span>
    </div>
    <div>
        <label for="TNumber">选择游戏轮次</label>
        <input type="text" id="TNumber" name="TNumber" value="" placeholder="轮次">
        <span id="Err2">最多10轮</span>
    </div>
    <div>
        <div class="reBtn" id="start">开始游戏</div>
        <div class="reBtn" id="re1">返回</div>
    </div>
</div>
<div class="gameHolder">
    <div id="playerHolder"></div>
    <p class="result"></p>
    <p class="last1"></p>
    <div class="last2"></div>
    <div class="subBtn" id="sub">提交</div>
    <div class="subBtn" id="nextTurn" style="display: none">下一轮</div>
</div>
</body>
<script src="/extra/canvas/canvas.js"></script>
<script>
    var player;
    var times;
    var nowTime;
    var numberList;
    var isClick = false;
    var loadScore = '';
    var loadFlag = false;
    $(function () {
        console.log(innerWidth,innerHeight)
        $('#SP').click(function () {
            $('.beginBtn').hide()
            $('.typeBtn').show()
        })//单人游戏，打开页面2
        $('#re1').click(function () {
            $('.beginBtn').show()
            $('.typeBtn').hide()
        })//返回页面1
        $('#start').click(function () {
            if ($('#PNumber').val()> 15 || $('#PNumber').val() <= 0){
                $('#Err1').show()
                return 0
            }else if ($('#TNumber').val()>10 ||  $('#TNumber').val() <= 0){
                $('#Err2').show()
                return 0
            }else if($('#TNumber').val() == '' || $('#PNumber').val() == ''){
                console.log($('#TNumber').val())
                window.alert('不能为空啊！')
            }else if($('#PNumber').val() == 1){
                window.alert('一个人不能玩！')
            }
            else{
                $('#Err1').hide()
                $('#Err2').hide()
                player = new Array(parseInt($('#PNumber').val()))
                for (var i=0;i<player.length;i++){
                    player[i] = 0
                }
                numberList = new Array(parseInt($('#PNumber').val()))
                times = $('#TNumber').val()
                nowTime = 1
                console.log(player,numberList)
                $('.typeBtn').hide()
                $('.gameHolder').show()
                for (var i=0;i<$('#PNumber').val();i++){
                    var PDiv = "<div><label for=\"Point\">输入点数" + (i+1) + "</label>\n" +
                        "<input type=\"text\" class=\"inputP\" id=\"Point" + i + " \"  name=\"Point" + i + " \" value=\"\" placeholder=\"点数\"></div>"
                    $('#playerHolder').append(PDiv)
                    console.log(i)
                }
                if (times>1 && nowTime < times){
                    $('#nextTurn').css('display','block')
                }
            }
        })//开始游戏，打开页面3
        $('#sub').click(function () {
            if (isClick === false){
                for (var i = 0;i<numberList.length;i++){
                    if(numberList[i] == null){
                        window.alert('有未填写的数字!')
                        console.log('nonumber')
                        return 0
                    }
                }

                isClick = true //被点击过一次
                var average = 0
                var closestNumber = 0
                var fartherNumber = 0
                var closestSub = 100
                var fartherSub = 0
                console.log('begin')
                for(let k=0;k<numberList.length;k++){
                    //填充提交数据
                    loadScore += "<p>第" + (k+1) + "人:" + numberList[k] + "</p>"
                    //
                    average += parseFloat(numberList[k])
                }
                loadScore += "*";
                average = average / 10 * 0.618
                console.log(average)
                for (var k=0;k<numberList.length;k++){
                    var playerSub = Math.abs(parseFloat(numberList[k]) - average)
                    if (k == 0){
                        closestSub = playerSub
                        fartherSub = playerSub
                        closestNumber = 0
                        fartherNumber = 0
                    }
                    else {
                        if (playerSub < closestSub){
                            closestNumber = k
                        }
                        if (playerSub > fartherSub){
                            fartherNumber = k
                        }
                    }
                }
                $('.gameHolder .result').html("Win:" + (closestNumber+1) + " Lose:" + (fartherNumber+1))
                player[parseInt(closestNumber)] += 10
                player[parseInt(fartherNumber)] -= 2
                if (nowTime === parseInt(times)){
                    $('.last1').html('最终结果')
                    var last2 = ''
                    for (var i=0;i<player.length;i++){
                        loadScore += "<p>玩家" + (i+1) + "获得" + player[i] + "分</p>"
                        last2 += "<p>玩家" + (i+1) + "获得" + player[i] + "分</p>"
                    }
                    loadScore += "*"
                    $('.last2').html(last2)
                    $('.gameHolder').append("<div class=\"subBtn\" id=\"upload\">上传保存结果</div>")
                    $('.gameHolder').append("<div class=\"subBtn\" style= \"cursor: default;display: none;font-size: 10px;color: red;\" id=\'upShow\'>上传成功</div>")
                    $('.gameHolder').append("<div class=\"subBtn\" id=\"reBegin\">返回开始菜单</div>")
                }//最终结果 游戏结束
            } //确保是第一次点击
        })//提交数据

    })
    //游戏结束返回主菜单 初始化
    $('.gameHolder').on('click','#reBegin',function () {
        $('.gameHolder').css('display','none')
        $('.beginBtn').css('display','block')
        $('#PNumber').val('')
        $('#TNumber').val('')
        $('.inputP').remove()
        $('#playerHolder div label').remove()
        $('.last1').css('display','none')
        $('.last2 p').remove()
        $('.result').html('')
        $('#upload').remove()
        $('#reBegin').remove()
        $('#upShow').remove()
        isClick = false
        loadFlag = false
        loadScore = ''
    })
    $('.gameHolder').on('click','#nextTurn',function () {
        if (isClick == true){
            isClick = false
            $('.inputP').val('')
            $('.result').html('')
            console.log($('.inputP').val())
            nowTime += 1
            if (times>1 && nowTime < times){
                $('#nextTurn').css('display','block')
            }else {
                $('#nextTurn').css('display','none')
            }
            for (var i=0;i<numberList.length;i++){
                numberList[i] = null //清空数字集
            }
        }
    })//点击下一轮
    $('#playerHolder').on('change',function () {
        console.log(numberList.length)
        var id = event.target.id
        var idNum = event.target.id
        idNum = parseInt(idNum.split('t')[1])
        if (document.getElementById(event.target.id).value > 0 && document.getElementById(event.target.id).value < 100){
            numberList[idNum] = document.getElementById(event.target.id).value
            console.log('成功更改')
        }else {
            window.alert('输入需要0-100的数字')
            document.getElementById(event.target.id).value = ''
        }
        console.log(numberList)
    })
    $('#GG-login').click(function () {
        window.location.href = '/login'
    })
    $('#GG-score').click(function () {
        window.location.href = '/score'
    })
    $('#GG-exit').click(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type:'post',
            url:'/',
            datatype:'json',
            data:{
                "ClearSssion": 'yes',
                "ajaxId": '1',
            },
            success:function (data) {
                window.location.href = '/'
            },
            error: function(request, status, error){
                alert(error);
            },
        })
        window.location.href = '/'
    })
    $('.gameHolder').on('click','#upload',function () {
        if (loadFlag == false){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type:'post',
                url: '/',
                datatype: 'json',
                data:{
                    "score": loadScore,
                    "ajaxId": '2',
                },
                success:function (data) {
                    loadFlag = true;
                    console.log('loadsuccess');
                    $('#upShow').show()
                },
                error: function(request, status, error){
                    alert(error);
                },
            })
        }
    })

</script>
@if(session('isLogin') == 'yes')
    <script>
        $(function () {
            setInterval(PostNowLogin,1000)
            $('#MP').click(function () {
                window.location.href = "/multiHome";
            })
        })
        function PostNowLogin() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type:'post',
                url: '/',
                datatype: 'json',
                data:{
                    "user": '{{session('nameLogin')}}',
                    "ajaxId": '3',
                    "status": 'on',
                },
                success:function (data) {
                    console.log('post')
                    //$('#GG-now').html("当前在线人数:"+ data.nownumber)
                },
                error: function(request, status, error){
                    alert(error);
                },
            })
        }

    </script>
@endif
</html>
