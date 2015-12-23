<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Addons\SocialComment;
use Common\Controller\Addon;
/**
 * 通用社交化评论插件
 * @author thinkphp
 */
class SocialCommentAddon extends Addon {
    /**
     * 插件信息
     * @author jry <598821125@qq.com>
     */
    public $info = array(
        'name'        => 'SocialComment',
        'title'       => '通用社交化评论',
        'description' => '集成了各种社交化评论插件，轻松集成到系统中。',
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
        '0' => 'SocialComment',
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
     * 实现的SocialComment钩子方法
     * @author jry <598821125@qq.com>
     */
    public function SocialComment($param){
        //检查插件是否开启
        $config = $this->getConfig();
        if($config['status']){
            $this->assign('addons_config', $config);
            $this->display('comment');
        }
    }
}
