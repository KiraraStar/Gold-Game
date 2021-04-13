<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
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
    table
    {
        border-collapse: collapse;
        margin: 0 auto;
        text-align:center;
    }
    table td, table th
    {
        border: 1px solid #cad9ea;
        color: #666;
        height: 30px;
    }
    table thead th
    {
        background-color: #CCE8EB;
        width: 100px;
    }
    table tr:nth-child(odd)
    {
        background: #fff;
    }
    table tr:nth-child(even)
    {
        background: #F5FAFA;
    }
</style>
<body style="text-align: center">
@foreach($sc as $s)
    {{"用户名:".$s->userName}}
@endforeach
{{--@foreach($sc as $s)--}}
{{--    {!! $s->scores !!}--}}
{{--@endforeach--}}
{!! $sc2 !!}
</body>
</html>
