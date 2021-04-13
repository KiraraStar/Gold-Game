<?php

namespace App\Console;

use App\Console\Commands\minTimer;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        minTimer::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command("command:minTimer")->everyMinute();
        // $schedule->command('inspire')->hourly();
        $schedule->call(function (){
            Log::info("startTime".date("Y-m-d G:i:s",time()));
            for($j=0;$j<20;$j++){
                $sc = DB::table('onnumber')->get()->all();
                //$homeUserId = DB::table('gg_user')->get()->all();
                for ($i=1;$i<count($sc)-1;$i++){
                    //查看onnumber里面的用户登录时间，更新时间超过10s视为下线
                    if (time()-strtotime($sc[$i]->ontime) > 10){
                        $username = $sc[$i]->onuser;
                        DB::table('onnumber')->where('onuser', '=', $sc[$i]->onuser)->delete();
                        $nums = DB::table('onnumber')->where('id', '=', '1')->get()->first()->onnumber;
                        /*if($nums == 1){

                        }else{
                            DB::update('update onnumber set onnumber=? where id=?',[$nums-1,1]);
                        }*/
                        DB::update('update onnumber set onnumber=? where id=?',[$nums-1,1]);
                        //更新房间状态为空
                        DB::update('update gg_user set nowHome=? where userName=?',[0,$username]);
                        Log::info('login_out');
                    }
                }
                //获得全部游戏房间
                $homeNowUser = DB::table('gamehome')->get()->all();
                for ($i = 0;$i<count($homeNowUser);$i++){
                    //Log::info();
                    //获得房间内的游戏玩家
                    $exResult = explode('_.',$homeNowUser[$i]->playname,-1);
                    for($j=0;$j<count($exResult);$j++){
                        //如果用户的NowHome不等于当前的检测房间id
                        //获得玩家id
                        $uid = DB::table('gg_user')->where('userName','=',$exResult[$j])->get()->first();
                        //dd(object_get($uid->nowHome));
                        //dd($uid);
                        if(($uid->nowHome == 0) || ($uid->nowHome!= $homeNowUser[$i]->id)){
                            //str_replace替换游戏玩家的字符串
                            $exReplace = str_replace($exResult[$j].'_.','',$homeNowUser[$i]->playname);
                            //更新 当前id的 playname set exreplace
                            DB::update('update gamehome set playname=? where id=?',[$exReplace,$homeNowUser[$i]->id]);
                            //更新 当前房间玩家数量-1
                            DB::update('update gamehome set nowplayer=? where  id=?',[$homeNowUser[$i]->nowplayer-1,$homeNowUser[$i]->id]);
                            Log::info("已经清除".$homeNowUser[$i]->id);
                        }
                    }

                    //Log::info($homeNowUser[$i]->playname.count($exResult));
                }
                sleep(3);
            }
        })->everyMinute();

        /*$schedule->call(function (){

        })->everyMinute();*/
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
