<?php
// +----------------------------------------------------------------------
// | LikeShop有特色的全开源社交分销电商系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 商业用途务必购买系统授权，以免引起不必要的法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | 微信公众号：好象科技
// | 访问官网：http://www.likemarket.net
// | 访问社区：http://bbs.likemarket.net
// | 访问手册：http://doc.likemarket.net
// | 好象科技开发团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshop.cn.team
// +----------------------------------------------------------------------


namespace app\common\command;


use app\common\model\Bargain;
use app\common\model\BargainLaunch;
use app\common\model\Order;
use app\common\server\ConfigServer;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\facade\Log;

/**
 * 砍价活动结束定时任务
 * Class BargainEnd
 * @Author 张无忌
 * @package app\common\command
 */
class BargainEnd extends Command
{
    protected function configure()
    {
        $this->setName('bargain_end')
            ->setDescription('结束已超时的砍价');
    }

    protected function execute(Input $input, Output $output)
    {
        try {
            $now = time();
            $bargainModel = new Bargain();
            $bargainLaunchModel = new BargainLaunch();

            $succeed_ids = [];
            $defeat_ids = [];
            //砍价成功后的下单时间
            $payment_limit_time = ConfigServer::get('bargain', 'payment_limit_time', 0) * 60;
            if($payment_limit_time > 0){
                $payment_limit_time = $now + $payment_limit_time;
            }
            //找出所有超时未关掉的订单
            $bargainLaunchModel->where([['status','=',BargainLaunch::conductStatus],['launch_end_time','<=',$now]])
                ->chunk(100, function($launchs) use(&$succeed_ids,&$defeat_ids) {

                    foreach ($launchs as $launch){
                        $launch = $launch->toarray();
                        //任意金额购买时，更新砍价成功
                        if(2 == $launch['bargain_snap']['payment_where']){
                            $succeed_ids[] = $launch['id'];
                        }else{
                            $defeat_ids[] = $launch['id'];

                        }
                    }
                });
            //标记成功
            if($succeed_ids){
                $bargainLaunchModel->where(['id'=>$succeed_ids])->update(['status'=>BargainLaunch::successStatus,'payment_limit_time'=>$payment_limit_time,'bargain_end_time'=>$now]);
            }
            //标记失败
            if($defeat_ids){
                $bargainLaunchModel->where(['id'=>$defeat_ids])->update(['status'=>BargainLaunch::failStatus,'bargain_end_time'=>$now]);
            }

            // 查询出要关闭的砍价活动
            $bargain_ids = $bargainModel->where([
                ['activity_end_time', '<', $now],
                ['del', '=', 0],
                ['status', '=', 1]
            ])->column('id');

            // 结束砍价活动(结束时间 < 当前时间)
            $bargainModel->whereIn('id', $bargain_ids)
                ->update(['status' => 0]);


        } catch (\Exception $e) {
            Log::write('结束砍价活动失败:'.$e->getMessage());
        }
    }
}