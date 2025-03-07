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

namespace app\api\controller;

use app\api\logic\AfterSaleLogic;

/**
 * 售后
 * Class Order
 * @package app\api\controller
 */
class AfterSale extends ApiBase
{
    public function lists()
    {
        $type = $this->request->get('type', 'normal');
        $lists = AfterSaleLogic::lists($this->user_id, $type, $this->page_no, $this->page_size);
        $this->_success('',$lists);
    }


    public function add()
    {
        $post = $this->request->post();
        $post['user_id'] = $this->user_id;
        $check = $this->validate($post, 'app\api\validate\AfterSale.add');
        if (true !== $check) {
            $this->_error($check);
        }
        return AfterSaleLogic::add($post, $this->user_id);
    }


    //售后商品信息
    public function goodsInfo()
    {
        $get = $this->request->get();
        $check = $this->validate($get, 'app\api\validate\AfterSale.info');
        if (true !== $check) {
            $this->_error($check);
        }
        $this->_success('', AfterSaleLogic::info($get['item_id'], $get['order_id']));
    }


    public function express()
    {
        $post = $this->request->post();
        $check = $this->validate($post, 'app\api\validate\AfterSale.express');
        if (true !== $check) {
            $this->_error($check);
        }
        return AfterSaleLogic::express($this->user_id, $post);
    }


    public function cancel()
    {
        $post = $this->request->post();
        $check = $this->validate($post, 'app\api\validate\AfterSale.cancel');
        if (true !== $check) {
            $this->_error($check);
        }
        AfterSaleLogic::cancel($this->user_id, $post);
        $this->_success('撤销成功');
    }


    
    public function detail()
    {
        $get = $this->request->get();
        $this->_success('', AfterSaleLogic::detail($get));
    }



    public function again()
    {
        $post = $this->request->post();
        $check = $this->validate($post, 'app\api\validate\AfterSale.again');
        if (true !== $check) {
            $this->_error($check);
        }
        return AfterSaleLogic::again($this->user_id, $post);
    }
}