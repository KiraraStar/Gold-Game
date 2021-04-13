<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoldGameController extends Controller
{
    //游戏主页面
    public function game(Request $request){
        if ($request->isMethod('post')){
            //ajax1 退出登录
            if ($request->ajax()){
                if($request['ajaxId'] == '1'){
                    session(['isLogin'=>'no']);
                    session(['nameLogin'=>'']);
                    return response()->json();
                }
                //ajax2 上传成绩
                if($request['ajaxId'] == '2' && session('nameLogin') != null){
                    $ifHasUser = DB::table('gg_score')->where('userName','=',session('nameLogin'))->first();
                    if ($ifHasUser == null){
                        DB::insert('insert into gg_score(scoreId,userName,scores) values (?,?,?)',[null,
                            session('nameLogin'),"<p>".date('Y-m-d h:i:s', time())."</p>"."*".$request['score']."|"]);
                    }else{
                        $score = DB::table('gg_score')->where('userName','=',session('nameLogin'))->first()->scores;
                        $score = "<p>".date('Y-m-d h:i:s', time())."</p>"."*".$request['score']."|".$score;
                        DB::update('update gg_score set scores=? where userName=?',[$score,session('nameLogin')]);
                    }
                    return response()->json(['userName'=>session('nameLogin')]);
                }
                //ajax3 更新当前在线人数信息
                if($request['ajaxId'] == '3'){
                    $onnumber = DB::table('onnumber')->get()->first()->onnumber;
                    if (DB::table('onnumber')->where('onuser','=',$request['user'])->get()->first() == null){
                        DB::insert('insert into onnumber(id,onnumber,ontime,onuser) values (?,?,?,?)',[null,null,null,$request['user']]);
                        DB::update('update onnumber set onnumber=? where id=?',[$onnumber+1,1]);
                    }else{
                        DB::update('update onnumber set ontime=? where onuser=?',[null,$request['user']]);
                    }
                    $sc = DB::table('onnumber')->where('onuser','=',$request['user'])->get()->first()->ontime;
                    session(['name'=>$request['user']]);
                    return response()->json(['data'=>$request->input(),'nownumber'=>$onnumber,'sc'=>strtotime($sc),'nowtime',time()]);
                }
            }
        }else return view('GG');
    }
    //用户注册的逻辑
    public function gameLogin(Request $request){
        if ($request->isMethod('post')){
            session(['sef'=>'yes']);
            $gg_user = DB::table('gg_user');
            $ifHasUser = $gg_user->where('userName','=', $request['userName'])->first();
            if ($request['userName'] == null || $request['userPw'] == null){
                return back()->with('message','请填入完整信息');
            }
            if ($ifHasUser == null){
                DB::insert('insert into gg_user(id,userName,userPw,nowHome) values (?,?,?,?)',[null,$request['userName'],$request['userPw'],null]);
                session(['isLogin'=>'yes','nameLogin'=>$request['userName']]);
                return redirect('/');
            }else{
                if ($ifHasUser->userName != $request['userName']) {
                    return back()->with('message', "用户名错误");
                } elseif ($ifHasUser->userPw != $request['userPw']) {
                    return back()->with('message', "密码错误");
                }
                session(['isLogin'=>'yes','nameLogin'=>$request['userName']]);
                return redirect('/');
            }
        }else {
            return view('GGLogin');
        }
    }
    //查看用户已有的成绩
    public function gameScore(){
        $userScore = DB::table('gg_score')->where('userName','=',session('nameLogin'))->get();
        if(count($userScore) == 0){
            $endinfo = "";
        }else{
            $userScore1 = DB::table('gg_score')->where('userName','=',session('nameLogin'))->get()->first()->scores;
            $eachturn = explode("|",$userScore1,-1);
            $endinfo = "";
            for($i=0;$i<count($eachturn);$i++){
                $patend = "";
                Log::info($eachturn[$i]);
                $ss = preg_match_all("/.*?\*/",$eachturn[$i],$patend);
                $endinfo = $endinfo.explode("*",$patend[0][0],-1)[0]."<table border='1' >";
                $c_sc = "";
                Log::info($patend);
                for ($j=1;$j<count($patend[0]);$j++){
                    //Log::info($j."1");
                    //Log::info($patend);
                    $endinfo = $endinfo."<tr>";
                    $ssma = preg_match_all("/<p>(.*?)<\/p>/",$patend[0][$j],$ssp);
                    for($k=0;$k<count($ssp[1]);$k++) {
                        $endinfo = $endinfo . "<td>" . $ssp[1][$k] . "</td>";
                    }
                    $endinfo =$endinfo."</tr>";
                }
                $endinfo = $endinfo."</table>";
            }
            Log::info($endinfo);
            Log::info($eachturn);
        }
        //$ss = preg_match_all("/.*?\*/",$eachturn[1],$patend);
        //Log::info($patend);
        return view('GGScore',['sc'=>$userScore,'sc2'=>$endinfo]);
    }
    //退出登录的检测
    public function gameCheck(Request $request){
        if ($request->isMethod('post')){
            session(['isLogin'=>'no']);
            session(['nameLogin'=>'']);
            return redirect('/');
        }else {
            return view('/GGCheck');
        }
    }
    //多人游戏大厅
    public function gameMulti(Request $request){
        if ($request->isMethod('post')){
            if($request->ajax()){
                $onnumber = DB::table('onnumber')->get()->first()->onnumber;
                if (DB::table('onnumber')->where('onuser','=',session('nameLogin'))->get()->first() == null){
                    DB::insert('insert into onnumber(id,onnumber,ontime,onuser) values (?,?,?,?)',[null,null,null,session('nameLogin')]);
                    DB::update('update onnumber set onnumber=? where id=?',[$onnumber+1,1]);
                }else{
                    DB::update('update onnumber set ontime=? where onuser=?',[null,session('nameLogin')]);
                }
                //$sc = DB::table('onnumber')->where('onuser','=',$request['user'])->get()->first()->ontime;
                //session(['name'=>$request['user']]);
                return  response()->json();
            }else{
                DB::insert("insert into gamehome(id,maxplayer,nowplayer,score,maxturn,nowturn,playname,lastscore,end) values (?,?,?,?,?,?,?,?,?)",
                    [null,$request["persons"],0,null,$request["turn"],0,null,null,0]);
            }
        }
        $home = DB::table('gamehome')->get()->all();
        return view('GGMulti',['homeNumber'=>count($home),'hh'=>$home]);
    }
    //游戏房间的认证
    public function gameHome(Request $request,$homeid){
        if ($request->isMethod('get')){
            //获得当前游戏房间的Id信息
            $idinfo = DB::table('gamehome')->where('id','=',$homeid)->get()->first();
            if ($idinfo != null){ // 存在房间
                DB::update('update gg_user set nowHome=? where userName=?',[$homeid,session('nameLogin')]);
                $playname = $idinfo->playname;
                if (preg_match("/".session('nameLogin')."/i",$playname)){
                }else{
                    DB::update('update gamehome set playname=?  where id=?',[$playname.session('nameLogin').'_.',$homeid]);
                    DB::update('update gamehome set nowplayer=?  where id=?',[$idinfo->nowplayer+1,$homeid]);
                }
                //游戏玩家的容器
                $db = DB::table('gamehome')->where('id','=',$homeid)->get()->first();
                $nameHolder = $db->playname; //房间内游戏人员
                $nownum = $db->nowplayer; //当前房间人数
                $turn = $db->maxturn;
                $nameList = explode('_.',$nameHolder,-1);
                return view('GGHome',['info'=>$idinfo,'name'=>$nameList,'id'=>$homeid,'nownum'=>$nownum,'turn'=>$turn]);
            }
        }
        if ($request->isMethod('post')){
            if ($request->ajax()){
                //ajax = 自动更新
                if ($request['method'] == 'update'){
                    DB::update('update onnumber set ontime=? where onuser=?',[null,session('nameLogin')]);
                    $nameHolder = DB::table('gamehome')->where('id','=',$homeid)->get()->first()->playname;
                    $nownum = DB::table('gamehome')->where('id','=',$homeid)->get()->first()->nowplayer;
                    $nameList = explode('_.',$nameHolder,-1);
                    //$endFlag = 'false';
                    //房间人数已经满
                    if ($nownum >= DB::table('gamehome')->where('id','=',$homeid)->get()->first()->maxplayer){
                        $gameStart = 'true';
                        //查找现在成绩的数量
                        $m  =DB::table('gamehome')->where('id','=',$homeid)->get()->first()->score;
                        $x = preg_match_all('/]/',$m); //匹配玩家有几个成绩

                        //成绩全部提交
                        if ($x == DB::table('gamehome')->where('id','=',$homeid)->get()->first()->maxplayer){
                            Log::info('人数已经满了,获得所有成绩');

                            //endFLag 游戏结束
                            DB::update('update gamehome set end=?  where id=?',[1,$homeid]);
                            $endFlag = 'true';
                            $lastsc = DB::table('gamehome')->where('id','=',$homeid)->get()->first()->score;
                            $eachplayer = explode("*",$lastsc,-1);
                            //"\/[40]<9><79><8>
                            $eachscore = explode("<",$eachplayer[0]);
                            //["\/[40]","9>","79>","8>"]

                            //开始处理成绩


                            $gamenumber =  count($eachplayer); //游戏的人数
                            $turn = DB::table('gamehome')->where('id','=',$homeid)->get()->first()->maxturn;//游戏的轮数
                            $scoreholder = array(); //成绩容器
                            $endscore = array();
                            $nameHolder = array();//名称容器
                            for($i=0;$i<$gamenumber;$i++){
                                $endscore[$i] = 0;
                                $nameHolder[$i] = explode("]",explode("[",$eachplayer[$i])[1])[0];
                                $scoreholder[$i] = array();
                                for($j=0;$j<$turn;$j++){
                                    $scoreholder[$i][$j] = explode(">",explode("<",$eachplayer[$i])[$j+1])[0];
                                }
                            }
                            //开始计算
                            $winner = array();
                            for($i=0;$i<$turn;$i++){
                                $average = 0;
                                $closestNumber = 0;
                                $fartherNumber = 0;
                                $closestSub = 100;
                                $fartherSub = 0;
                                for($j=0;$j<$gamenumber;$j++){
                                    //获得平均分
                                    $average += $scoreholder[$j][$i];
                                }
                                $average = $average / 10 * 0.618;
                                for($j=0;$j<$gamenumber;$j++){
                                    $playerSub = abs($scoreholder[$j][$i] - $average);
                                    if($j==0){
                                        $closestSub = $playerSub;
                                        $fartherSub = $playerSub;
                                        $closestNumber = 0;
                                        $fartherNumber = 0;
                                    }
                                    else {
                                        if ($playerSub < $closestSub){
                                            $closestNumber = $j;
                                        }
                                        if ($playerSub > $fartherSub){
                                            $fartherNumber = $j;
                                        }
                                    }
                                }
                                $endscore[$closestNumber] += 10;
                                $endscore[$fartherNumber] -= 2;
                                $winner[$i] = "第".($i+1)."局平均数:".$average.",winner:".($closestNumber+1).",loser:".($fartherNumber+1);
                            }
                            for($i=0;$i<$gamenumber;$i++){
                                $j = DB::table("gg_user")->where("id","=",$nameHolder[$i])->get()->first()->userName;
                                $endscore[$i] = "玩家".$j.":".$endscore[$i];
                            }
                            //存入数据库
                        }else{
                            $endFlag = 'false';
                            $lastsc = 'nodata';
                            $eachscore = "1";
                            $eachplayer = "1";
                            $winner = "1";
                            $endscore = "1";
                        }
                    }else{
                        $endFlag = 'false';
                        $lastsc = 'nodata';
                        $gameStart = 'false';
                        $eachscore = "1";
                        $eachplayer = "1";
                        $winner = "1";
                        $endscore = "1";
                    }
                    return response()->json(['id1'=>$request->input(),'name'=>$nameList,'num'=>$nownum,'gf'=>$gameStart, 'endFlag'=>$endFlag,'lsc'=>$lastsc,'win'=>$winner,'endscore'=>$endscore,]);
                }
                elseif($request['method'] == 'score'){
                    //获得玩家id
                    $userid = DB::table('gg_user')->where('userName','=',session('nameLogin'))->get()->first()->id;
                    //
                    $scoreUp = '/['.$userid.']'.$request['score'].'*';
                    //获得原始成绩数据
                    $scoreOri = DB::table('gamehome')->where('id','=',$homeid)->get()->first()->score;
                    $scoreUp = $scoreOri.$scoreUp;
                    //没有提交过数据的话可以更新
                    if (!preg_match('/\['.$userid.']/i',$scoreOri)){
                        DB::update('update gamehome set score=?  where id=?',[$scoreUp,$homeid]);
                    }
                    return response()->json();
                }
            }
        }
    }
}
