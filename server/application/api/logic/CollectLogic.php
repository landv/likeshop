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
namespace app\api\logic;

use app\api\model\Goods;use think\Db;

class CollectLogic{
    public static function getCollectGoods($user_id,$page,$size){
        $goods = new Goods();
        $count = $goods->alias('g')
                ->leftJoin('goods_collect c','g.id=c.goods_id')
                ->where(['g.del' => 0,'status'=>1,'user_id'=>$user_id])
                ->count();

        $list = $goods->alias('g')
            ->leftJoin('goods_collect c','g.id=c.goods_id')
            ->where(['g.del' => 0,'user_id'=>$user_id])
            ->field('g.id,name,image,min_price as price')
            ->order('c.id desc')
            ->page($page, $size)
            ->select();
        $more = is_more($count,$page,$size);  //是否有下一页
        return [
            'list'      => $list,
            'count'     => $count,
            'page_no'   => $page,
            'page_size' => $size,
            'more'      =>$more
        ];

    }
    public static function handleCollectGoods($post,$user_id){
        if($post['is_collect']==1){
            $data =[
                'user_id'=>$user_id,
                'goods_id'=>$post['goods_id'],
                'create_time'=>time(),
            ];
            return Db::name('goods_collect')->insert($data);
        }
        return Db::name('goods_collect')->where(['goods_id'=>$post['goods_id']])->delete();

    }
}