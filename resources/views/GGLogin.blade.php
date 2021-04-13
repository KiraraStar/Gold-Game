<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GoldGame-Login</title>
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
    .main{
        text-align: center;
        position: absolute;
        top:200px;
        width: 300px;
        left: 50%;
        transform: translate(-50%,0);
        cursor: default;
        background-color: rgba(180,180,180,0.8);
        border-radius: 15px;
    }
    .main p:nth-child(1){
        font-weight: bolder;
    }
    .main input{
        background-color: transparent;
        display: block;
        height: 20px;
        width: 100%;
        border: 0;
        padding: 0;
        margin-top: 10px;
        margin-bottom: 10px;
        font-family: 仿宋;
    }
    .main input:focus{
        outline: none;
    }
    #submit{
        /*border: rgb(174,174,174);
        background-color: rgb(174,174,174);*/
        padding: 5px;
        width: 50%;
        height: auto;

    }
</style>
<body>
<canvas id="Mycanvas"></canvas>
<div class="main">
    <p>在此注册或登录，未注册会自动注册</p>
    <form method="post">
        @csrf
        <input type="text" name="userName" id="userName" placeholder="用户名" style="font-family: 仿宋;text-align: center">
        <input type="text" name="userPw" id="userPw" placeholder="密码" style="font-family: 仿宋;text-align: center">
        <input type="submit" value="提交" id="submit">
        @if(session('message'))
            <p style="color: red">{{session('message')}}</p>
        @endif
    </form>
</div>
</body>
<script src="/extra/canvas/canvas.js"></script>
</html>
