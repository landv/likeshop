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
// | author: likeshop.cn.team-段誉
// +----------------------------------------------------------------------


namespace app\common\server;


use Alipay\EasySDK\Kernel\Factory;
use Alipay\EasySDK\Kernel\Config;
use app\common\logic\PayNotifyLogic;
use app\common\model\Client_;
use app\common\model\Pay;
use think\Db;
use think\facade\Log;

class AliPayServer
{


    protected $error = '未知错误';

    public function getError()
    {
        return $this->error;
    }


    public function __construct()
    {
        Factory::setOptions($this->getOptions());
    }


    /**
     * Notes: 支付设置
     * @author 段誉(2021/3/23 10:33)
     * @return Config
     * @throws \Exception
     */
    public function getOptions()
    {
        $result = (new Pay())->where(['code' => 'alipay'])->find();

        if (empty($result)) {
            throw new \Exception('请配置好支付设置');
        }

        $options = new Config();
        $options->protocol = 'https';
        $options->gatewayHost = 'openapi.alipay.com';
//        $options->gatewayHost = 'openapi.alipaydev.com'; //测试沙箱地址
        $options->signType = 'RSA2';
        $options->appId = $result['config']['app_id'] ?? '';
        // 应用私钥
        $options->merchantPrivateKey = $result['config']['private_key'] ?? '';
        //支付宝公钥
        $options->alipayPublicKey = $result['config']['ali_public_key'] ?? '';
        //回调地址
        $options->notifyUrl = url('payment/aliNotify', '', '', true);
        return $options;
    }


    /**
     * Notes: pc支付
     * @param $attach
     * @param $order
     * @author 段誉(2021/3/22 18:38)
     * @return string
     */
    public function pagePay($attach, $order)
    {
        $domain = request()->domain();
        $result = Factory::payment()->page()->optional('passback_params', $attach)->pay(
            '订单:'.$order['order_sn'],
            $order['order_sn'],
            $order['order_amount'],
            $domain.'/pc/user/order'
        );
        return $result->body;
    }


    /**
     * Notes: app支付
     * @param $attach
     * @param $order
     * @author 段誉(2021/3/22 18:38)
     * @return string
     */
    public function appPay($attach, $order)
    {
        $result = Factory::payment()->app()->optional('passback_params', $attach)->pay(
            $order['order_sn'],
            $order['order_sn'],
            $order['order_amount']
        );
        return $result->body;
    }


    /**
     * Notes: 手机网页支付
     * @param $attach
     * @param $order
     * @author 段誉(2021/3/22 18:38)
     * @return string
     */
    public function wapPay($attach, $order)
    {
        $domain = request()->domain();
        $result = Factory::payment()->wap()->optional('passback_params', $attach)->pay(
            '订单:'.$order['order_sn'],
            $order['order_sn'],
            $order['order_amount'],
            $domain.'/mobile/pages/user_order/user_order',
            $domain.'/mobile/pages/user_order/user_order'
        );
        return $result->body;
    }


    /**
     * Notes: 支付
     * @param $from
     * @param $order
     * @param $order_source
     * @author 段誉(2021/3/22 18:33)
     * @return bool|string
     */
    public function pay($from, $order, $order_source)
    {
        try{
            switch ($order_source){
                case Client_::pc:
                    $result = $this->pagePay($from, $order);
                    break;
                case Client_::ios:
                case Client_::android:
                    $result = $this->appPay($from, $order);
                    break;
                case Client_::h5:
                    $result = $this->wapPay($from, $order);
                    break;
                default:
                    throw new \Exception('支付方式错误');
            }
            return $result;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }


    /**
     * Notes: 支付回调验证
     * @param $data
     * @author 段誉(2021/3/22 17:22)
     * @return bool
     */
    public function verifyNotify($data)
    {
        try {
            $verify = Factory::payment()->common()->verifyNotify($data);
            if (false === $verify) {
                throw new \Exception('异步通知验签失败');
            }
            if (!in_array($data['trade_status'], ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
                return true;
            }
            $extra['transaction_id'] = $data['trade_no'];
            //验证订单是否已支付
            switch ($data['passback_params']) {
                case 'order':
                    $order = Db::name('order')->where(['order_sn' => $data['out_trade_no']])->find();
                    if (!$order || $order['pay_status'] >= Pay::ISPAID) {
                        return true;
                    }
                    PayNotifyLogic::handle('order', $data['out_trade_no'], $extra);
                    break;

                case 'recharge':
                    $order = Db::name('recharge_order')->where(['order_sn' => $data['out_trade_no']])->find();
                    if (!$order || $order['pay_status'] >= Pay::ISPAID) {
                        return true;
                    }
                    PayNotifyLogic::handle('recharge', $data['out_trade_no'], $extra);
                    break;
            }

            return true;
        } catch (\Exception $e) {
            $record = [
                __CLASS__, __FUNCTION__, $e->getFile(), $e->getLine(), $e->getMessage()
            ];
            Log::record(implode('-', $record));
            return false;
        }
    }


    /**
     * Notes: 查询订单
     * @param $order_sn
     * @author 段誉(2021/3/23 15:21)
     * @return \Alipay\EasySDK\Payment\Common\Models\AlipayTradeQueryResponse
     * @throws \Exception
     */
    public function checkPay($order_sn)
    {
        return Factory::payment()->common()->query($order_sn);
    }


    /**
     * Notes: 退款
     * @param $order_sn 订单号
     * @param $order_amount 金额
     * @author 段誉(2021/3/25 10:24)
     * @return \Alipay\EasySDK\Payment\Common\Models\AlipayTradeRefundResponse
     * @throws \Exception
     */
    public function refund($order_sn, $order_amount)
    {
        return Factory::payment()->common()->refund($order_sn, $order_amount);
    }


}

