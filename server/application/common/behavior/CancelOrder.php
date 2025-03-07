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

namespace app\common\behavior;

use app\api\logic\DistributionLogic;
use app\api\model\Order;
use app\common\logic\IntegralLogic;
use app\common\logic\OrderGoodsLogic;
use app\common\logic\OrderLogLogic;
use app\common\model\AccountLog;
use app\common\model\OrderLog;


/**
 * 取消订单后的操作
 * Class CancelOrder
 * @package app\common\behavior
 */
class CancelOrder
{
    public function run($params)
    {

        $order_id = $params['order_id'];
        $handle_id = $params['handle_id'] ?? 0;
        $handle_type = $params['handle_type'] ?? 0;

        $order = Order::get(['id' => $order_id], ['orderGoods']);
        if (empty($order)){
            return false;
        }

        $user_id = $order['user_id'];

        //订单取消后更新分销订单为已失效状态
        DistributionLogic::setDistributionOrderFail($order_id);

        //下单扣库存的话(deduct_type=1),回退库存,支付后扣库存的话(deduct_type=0),判断订单是否支付才去回退库存
        OrderGoodsLogic::backStock($order['orderGoods'], $order['pay_status']);

        //扣除每日首单奖励
//        IntegralLogic::backIntegral($user_id, $order_id);

        //下单是否使用积分
        if ($order['use_integral'] > 0){
            IntegralLogic::handleIntegral(
                $user_id,
                $order['use_integral'],
                AccountLog::cancel_order_refund_integral,
                $order_id
            );
        }

        //订单日志
        switch ($handle_type){
            case OrderLog::TYPE_USER:
                OrderLogLogic::record(
                    OrderLog::TYPE_USER,
                    OrderLog::USER_CANCEL_ORDER,
                    $order_id,
                    $user_id,
                    OrderLog::USER_CANCEL_ORDER
                );
                break;

            case OrderLog::TYPE_SHOP:
                OrderLogLogic::record(
                    OrderLog::TYPE_SHOP,
                    OrderLog::SHOP_CANCEL_ORDER,
                    $order_id,
                    $handle_id,
                    OrderLog::SHOP_CANCEL_ORDER
                );
                break;

            default:
                OrderLogLogic::record(
                    OrderLog::TYPE_SYSTEM,
                    OrderLog::SYSTEM_CANCEL_ORDER,
                    $order_id,
                    0,
                    OrderLog::SYSTEM_CANCEL_ORDER
                );
        }
        return true;
    }
}