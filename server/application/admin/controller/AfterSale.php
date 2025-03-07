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
// | likeshop系列产品收费版本务必购买商业授权，购买去版权授权后，方可去除前后端官方版权标识
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | likeshop团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshop.cn.team
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\logic\AfterSaleLogic;
use app\common\model\AfterSale as CommonAfterSale;

class AfterSale extends AdminBase
{
    /**
     * User: 意象信息科技 mjf
     * Desc: 列表
     */
    public function lists()
    {
        if ($this->request->isAjax()) {
            $get = $this->request->get();
            $this->_success('', AfterSaleLogic::lists($get));
        }
        $this->assign('status', CommonAfterSale::getStatusDesc(true));
        return $this->fetch();
    }

    /**
     * User: 意象信息科技 mjf
     * Desc: 详情
     */
    public function detail()
    {
        $id = $this->request->get('id');
        $detail = AfterSaleLogic::getDetail($id);
        $this->assign('detail', $detail);
        return $this->fetch();
    }


    /**
     * User: 意象信息科技 mjf
     * Desc: 同意
     */
    public function agree()
    {
        if ($this->request->isAjax()) {
            $post = $this->request->post('');
            $check = $this->validate($post, 'app\admin\validate\AfterSale.agree');
            if (true !== $check) {
                $this->_error($check);
            }
            AfterSaleLogic::agree($post['id'],$this->admin_id);
            $this->_success('操作成功');
        }
    }

    /**
     * User: 意象信息科技 mjf
     * Desc: 拒绝
     */
    public function refuse()
    {
        if ($this->request->isAjax()) {
            $post = $this->request->post('');
            $check = $this->validate($post, 'app\admin\validate\AfterSale.refuse');
            if (true !== $check) {
                $this->_error($check);
            }
            AfterSaleLogic::refuse($post,$this->admin_id);
            $this->_success('操作成功');
        }
    }

    /**
     * User: 意象信息科技 mjf
     * Desc: 收货
     */
    public function take()
    {
        if ($this->request->isAjax()) {
            $post = $this->request->post('');
            $check = $this->validate($post, 'app\admin\validate\AfterSale.take');
            if (true !== $check) {
                $this->_error($check);
            }
            AfterSaleLogic::takeGoods($post,$this->admin_id);
            $this->_success('操作成功');
        }
    }

    /**
     * User: 意象信息科技 mjf
     * Desc: 拒绝收货
     */
    public function refuseGoods()
    {
        if ($this->request->isAjax()) {
            $post = $this->request->post('');
            $check = $this->validate($post, 'app\admin\validate\AfterSale.refuse_goods');
            if (true !== $check) {
                $this->_error($check);
            }
            AfterSaleLogic::refuseGoods($post,$this->admin_id);
            $this->_success('操作成功');
        }
    }


    /**
     * User: 意象信息科技 mjf
     * Desc: 确认退款
     */
    public function confirm()
    {
        if ($this->request->isAjax()) {
            $post = $this->request->post('');
            $check = $this->validate($post, 'app\admin\validate\AfterSale.confirm');
            if (true !== $check) {
                $this->_error($check);
            }
            $res = AfterSaleLogic::confirm($post,$this->admin_id);
            if ($res === true){
                $this->_success('操作成功');
            }
            $this->_error($res);
        }
    }
}