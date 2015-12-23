<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Addons\BaiduShare;
use Common\Controller\Addon;
/**
 * 百度分享插件
 * @author jry
 */
class BaiduShareAddon extends Addon {
    /**
     * 插件信息
     * @author jry <598821125@qq.com>
     */
    public $info = array(
        'name'        => 'BaiduShare',
        'title'       => '百度分享',
        'description' => '用户将网站内容分享到第三方网站',
        'status'      => 1,
        'author'      => 'CoreThink',
        'version'     => '1.1.1',
        'beta'        => 'false',
    );

    /**
     * 插件所需钩子
     * @author jry <598821125@qq.com>
     */
    public $hooks = array(
        '0' => 'BaiduShare',
    );

    /**
     * 插件安装方法
     * @author jry <598821125@qq.com>
     */
    public function install(){
        return true;
    }

    /**
     * 插件卸载方法
     * @author jry <598821125@qq.com>
     */
    public function uninstall(){
        return true;
    }

    /**
     * 实现的BaiduShare钩子方法
     * @author jry <598821125@qq.com>
     */
    public function BaiduShare($param){
        $this->assign('info', $param);
        $this->assign('addons_config', $this->getConfig());
        $this->display('share');
    }
}
