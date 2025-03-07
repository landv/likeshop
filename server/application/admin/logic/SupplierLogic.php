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
use think\db;

class SupplierLogic{
    /**
     * 列表
     */
    public static function lists($get){
        $where =[];
        if(isset($get['keyword']) && $get['keyword']){
            $where[] = ['name','like','%'.$get['keyword'].'%'];
        }
        $res = db::name('supplier')
            ->where('del',0)
            ->where($where);
        $count = $res->count();
        $lists = $res->page($get['page'],$get['limit'])->select();
        return[
            'count' =>$count,
            'lists' =>$lists,
        ];
    }

    /**
     * 添加
     */
    public static function add($post){

        $data = [
            'name'      => $post['name'],
            'contact'   => $post['contact'],
            'tel'       => $post['tel'],
            'remark'    => $post['remark'],
            'address'   => $post['address'],
            'create_time' => time(),
        ];

        db::name('supplier')
            ->insert($data);
    }

    /**
     * 编辑
     */
    public static function edit($post){

        $data = [
            'name'      => $post['name'],
            'contact'   => $post['contact'],
            'tel'       => $post['tel'],
            'remark'    => $post['remark'],
            'address'   => $post['address'],
            'update_time' => time(),
        ];

        db::name('supplier')
            ->where(['id'=>$post['id']])
            ->update($data);
    }

    /**
     * 信息
     */
    public static function info($id){
        $info = db::name('supplier')
            ->where(['id'=>$id])
            ->find();
        return $info;

    }

    /**
     * 删除
     */
    public static function del($id)
    {

        $data = [
            'del' => 1,
            'update_time' => time()];
        return Db::name('supplier')->where(['del' => 0, 'id' =>$id])->update($data);  //逻辑删除


    }
    /**
     * note 获取所有供应商
     */
    public static function getSupplierList(){
        $list = Db::name('supplier')
                ->field('id,name')
                ->where(['del' => 0])
                ->select();
        return $list;
    }
}