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

namespace app\admin\validate;

use think\Db;
use think\Validate;

class AfterSale extends Validate
{
    protected $rule = [
        'id' => 'require|checkAfterSale',
        'remark' => 'require',
    ];

    protected $message = [
        'id.require' => '参数缺失',
        'remark.require' => '请填写拒绝原因',
    ];

    //商家同意
    public function sceneAgree()
    {
        $this->only(['id']);
    }

    //商家拒绝
    public function sceneRefuse()
    {
        $this->only(['id','remark']);
    }

    //商家收货
    public function sceneTake()
    {
        $this->only(['id']);
    }

    //拒绝收货
    public function sceneRefuseGoods()
    {
        $this->only(['id','remark']);
    }

    //确认退款
    public function sceneConfirm()
    {
        $this->only(['id'])->append('id','checkIsRefund');
    }

    protected function checkAfterSale($value, $rule, $data)
    {
        $after_sale = \app\common\model\AfterSale::where('del',0)
            ->where('id',$value)
            ->find();

        if (!$after_sale){
            return '订单异常,暂不可操作!';
        }
        return true;
    }

    protected function checkIsRefund($value, $rule, $data)
    {
        $after_sale = \app\common\model\AfterSale::where('del',0)
            ->where('id',$value)
            ->find();

        if ($after_sale['status'] == \app\common\model\AfterSale::STATUS_SUCCESS_REFUND){
            return '此订单已退款';
        }
        return true;
    }
}