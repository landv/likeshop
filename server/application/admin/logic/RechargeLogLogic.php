<?php
// +----------------------------------------------------------------------
// | likeshop开源商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop系列产品在gitee、github等公开渠道开源版本可免费商用，未经许可不能去除前后端官方版权标识
// |  likeshop系列产品收费版本务必购买商业授权，购买去版权授权后，方可去除前后端官方版权标识
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | likeshop团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------

// | author: likeshop.cn.team
// +----------------------------------------------------------------------
namespace app\admin\logic;
use app\common\model\Pay;
use think\Db;
use think\helper\Time;

class RechargeLogLogic{
    public static function lists($get){
        $where = [];
        if(isset($get['keyword']) && $get['keyword']){
            $where[] = [$get['type'],'like','%'.$get['keyword'].'%'];
        }
        if(isset($get['order_source']) && $get['order_source']){
            $where[] = ['order_source','=',$get['order_source']];
        }
        if(isset($get['pay_status']) && $get['pay_status']){
            $where[] = ['pay_status','=',$get['pay_status']];
        }
        if(isset($get['pay_way']) && $get['pay_way']){
            $where[] = ['pay_way','=',$get['pay_way']];
        }
        if (isset($get['start_time']) && $get['start_time'] && isset($get['end_time']) && $get['end_time']) {
            $where[] = ['ro.create_time', 'between', [strtotime($get['start_time']), strtotime($get['end_time'])]];
        }else{
            $where[] = ['ro.create_time', 'between', Time::today()];
        }
        if(isset($get['create_end']) && $get['create_end']!=''){
            $where[] = ['ro.create_time','<=',strtotime($get['create_end'])];
        }

        $count = Db::name('recharge_order')->alias('ro')
                ->join('user u','ro.user_id = u.id')
                ->where($where)
                ->count();
        $list =  Db::name('recharge_order')->alias('ro')
                ->join('user u','ro.user_id = u.id')
                ->where($where)
                ->field('ro.id,order_sn,order_amount,give_money,give_integral,give_growth,pay_time,pay_way,pay_status,ro.create_time')
                ->page($get['page'],$get['limit'])
                ->order('ro.id desc')
                ->select();
        foreach ($list as &$order){
            $order['order_amount'] = '￥'.$order['order_amount'];
            $order['pay_way'] = Pay::getPayWay($order['pay_way']);
            $order['pay_status'] = Pay::getPayStatus($order['pay_status']);
            if($order['pay_time']){
                $order['pay_time'] = date('Y-m-d H:i:s',$order['pay_time']);
            }
            $order['create_time'] = date('Y-m-d H:i:s',$order['create_time']);
        }
        return ['count'=>$count , 'lists'=>$list];
    }
}