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

class Auth extends Validate
{

    protected $rule = [
        'name' => 'require',
        'disable' => 'require|in:0,1',
        'uri' => 'uri',
        'type' => 'type',
        'sort' => 'integer|between:1,1000'
    ];

    protected $message = [
        'name.require' => '菜单名称不能为空',
        'uri.uri' => '地址不存在',
        'sort.integer' => '排序必须为正整数',
        'sort.between' => '排序范围必须在1到1000之间',
    ];


    /**
     * url规则验证
     * @param $uri
     * @return bool
     */
    public function uri($uri)
    {
        if ($uri === '') {
            return true;
        }
        if (count(explode('/', $uri)) != 2) {
            return false;
        }

        list($controller, $function) = explode('/', $uri);
        $controller = 'app\admin\controller\\' . ucfirst($controller);
        if (!class_exists($controller)) {
            return false;
        }
        $class = new $controller;
        if (!method_exists($class, $function)) {
            return false;
        }
        return true;
    }


    /**
     * 类型验证
     * @param $type
     * @param $other
     * @param $data
     * @return bool|string
     */
    public function type($type, $other, $data)
    {
        if ($type == 2 && $data['pid'] == 0) {
            return '类型为权限的不能设置为顶级';
        }
        $data['id'] = empty($data['id']) ? 0 : $data['id'];
        $result = Db::name('dev_auth')
            ->where('id', '<>', $data['id'])
            ->where(['pid' => $data['pid'], 'del' => 0])
            ->value('type');
        if (!empty($result) && $result != $data['type']) {
            return '类型错误，必须设置与同一级一样';
        }
        return true;
    }
}