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


namespace app\admin\validate;

use think\Validate;

/**
 * 砍价活动 数据校验
 * Class Bargain
 * @Author 张无忌
 * @package app\admin\validate
 */
class Bargain extends Validate
{
    protected $rule = [
        'id'                  => 'require|number',
        'goods_id'            => 'require|number',
        'time_limit'          => 'require',
        'activity_start_time' => 'require',
        'activity_end_time'   => 'require|endTime',
        'payment_where'       => 'require|in:1,2',
        'knife_type'          => 'require|in:1,2',
        'status'              => 'require|in:0,1'
    ];

    protected $message = [
        'id'         => 'ID不可为空',
        'id.number'  => 'ID必须为数字',
        'goods_id.require'   => '未选择砍价商品',
        'goods_id.number'    => '选择砍价商品异常',
        'time_limit.require' => '请填写砍价活动有效期',
        'time_limit.number'  => '砍价活动有效期必须为数字',
        'activity_start_time.require' => '请选择活动开始时间',
        'activity_end_time.require'   => '请选择活动结束时间',
        'payment_where.require' => '请选择购买方式',
        'payment_where.number'  => '选择的购买方式异常',
        'knife_type.require'    => '请选择砍价金额方式',
        'knife_type.number'     => '选择的砍价方式异常',
        'status.require'        => '请选择砍价活动状态',
        'status.in'             => '砍价状态选择异常',
    ];

    // 添加场景
    public function sceneAdd()
    {
        $this->remove('id');
    }

    public function endTime($value, $rule, $data)
    {
        if (strtotime($value) <= time()) {
            return '结束时间不能少于当前时间';
        }
        if (strtotime($value) <= $data['activity_start_time']) {
            return '结束时间不能少于或等于开始时间';
        }

        return true;
    }
}