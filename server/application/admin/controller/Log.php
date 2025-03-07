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


namespace app\admin\controller;

use app\admin\logic\LogLogic;
use think\helper\Time;

class Log extends AdminBase
{

    /**
     * 系统日志
     * @return mixed
     */
    public function lists()
    {


        if ($this->request->isAjax()) {
            $limit = $this->request->get('limit', 20);
            $page_no = $this->request->get('page', 1);
            $get = $this->request->get();
            $this->_success('', LogLogic::lists($page_no, $limit, $get));
        }


        $today = array_map(function ($time) {
            return date('Y-m-d H:i:s', $time);
        }, Time::today());
        $this->assign('today', $today);

        $yesterday = array_map(function ($time) {
            return date('Y-m-d H:i:s', $time);
        }, Time::yesterday());
        $this->assign('yesterday', $yesterday);


        $days_ago7 = array_map(function ($time) {
            return date('Y-m-d H:i:s', $time);
        }, Time::dayToNow(7));
        $this->assign('days_ago7', $days_ago7);

        $days_ago30 = array_map(function ($time) {
            return date('Y-m-d H:i:s', $time);
        }, Time::dayToNow(30, true));
        $this->assign('days_ago30', $days_ago30);
        return $this->fetch();
    }

}