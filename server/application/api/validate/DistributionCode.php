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


namespace app\api\validate;


use think\Db;
use think\Validate;

class DistributionCode extends Validate
{
    protected $rule = [
        'code' => 'require|checkCode',
    ];

    protected $message = [
        'code.require' => '请输入邀请码',
    ];

    /**
     * 填写邀请码验证
     * @param $code
     * @param $other
     * @param $data
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function checkCode($code, $other, $data)
    {
        $my_first_leader = Db::name('user')
            ->where(['id' => $data['user_id']])
            ->value('first_leader');
        if ($my_first_leader) {
            return '已有邀请人';
        }
        $first_leader = Db::name('user')
            ->field(['is_distribution', 'id', 'ancestor_relation'])
            ->where(['distribution_code' => $code])
            ->find();
        if (empty($first_leader)) {
            return '请填写有效的邀请码';
        }
        if ($first_leader['is_distribution'] == 0) {
            return '对方不是分销会员';
        }
        if ($first_leader['id'] == $data['user_id']) {
            return '不能邀请自己';
        }
        $ancestor_relation = explode(',', $first_leader['ancestor_relation']);
        if (!empty($ancestor_relation) && in_array($data['user_id'], $ancestor_relation)) {
            return '不能填写所有下级的任意一人的邀请码';
        }

        return true;
    }
}