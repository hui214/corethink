<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Common\Behavior;
use Think\Behavior;
defined('THINK_PATH') or exit();
/**
 * 根据不同情况读取数据库的配置信息并与本地配置合并
 * 本行为扩展很重要会影响核心系统前后台、模块功能及模版主题使用
 * 如非必要或者并不是十分了解系统架构不推荐更改
 * @author jry <598821125@qq.com>
 */
class InitConfigBehavior extends Behavior {
    /**
     * 行为扩展的执行入口必须是run
     * @author jry <598821125@qq.com>
     */
    public function run(&$content) {
        // 安装模式下直接返回
        if(defined('BIND_MODULE') && BIND_MODULE === 'Install') return;

        // 数据缓存前缀
        // 获取ThinkPHP控制器分级时控制器名称
        $controller_name = explode('/', CONTROLLER_NAME);
        if (sizeof($controller_name) === 2) {
            C('DATA_CACHE_PREFIX', ENV_PRE.MODULE_NAME.'_'.$controller_name[0].'_');
        } else {
            C('DATA_CACHE_PREFIX', ENV_PRE.MODULE_NAME.'_');
        }

        // 读取数据库中的配置
        $system_config = S('DB_CONFIG_DATA');
        if (!$system_config) {
            // 获取所有系统配置
            $system_config = D('Admin/Config')->lists();

            // 获取所有安装的模块配置
            $module_list = D('Admin/Module')->where(array('status' => '1'))->select();
            foreach ($module_list as $val) {
                $module_config[strtolower($val['name'].'_config')] = json_decode($val['config'], true);
                $module_config[strtolower($val['name'].'_config')]['module_name'] = $val['name'];
            }
            if ($module_config) {
                // 合并模块配置
                $system_config = array_merge($system_config, $module_config);

                // 加载模块标签库及行为扩展
                $system_config['TAGLIB_PRE_LOAD'] = explode(',', C('TAGLIB_PRE_LOAD'));  // 先取出配置文件中定义的否则会被覆盖
                foreach ($module_config as $key => $val) {
                    // 加载模块标签库
                    if ($val['taglib']) {
                        foreach ($val['taglib'] as $tag) {
                            $system_config['TAGLIB_PRE_LOAD'][] = $val['module_name'].'\\TagLib\\'.$tag.'';
                        }
                    }

                    // 加载模块行为扩展
                    if ($val['behavior']) {
                        foreach ($val['behavior'] as $bhv) {
                            \Think\Hook::add('corethink_behavior', $val['module_name'].'\\Behavior\\'.$bhv.'Behavior');
                        }
                    }
                }
                $system_config['TAGLIB_PRE_LOAD'] = implode(',', $system_config['TAGLIB_PRE_LOAD']);
            }

            // 不直接在config里配置这些参数而要在这里配置是为了支持功能模块的相关架构
            if (MODULE_NAME === 'Admin' || $controller_name[0] === 'Admin') {
                // Admin后台与模块后台标记
                $system_config['MODULE_MARK'] = 'Admin';

                // SESSION与COOKIE与前缀设置避免冲突
                $system_config['SESSION_PREFIX'] = ENV_PRE.'Admin_';  // Session前缀
                $system_config['COOKIE_PREFIX']  = ENV_PRE.'Admin_';  // Cookie前缀

                // 错误页面模板
                $system_config['TMPL_ACTION_ERROR']   = APP_PATH.'Admin/View/Public/think/error.html';      // 错误跳转对应的模板文件
                $system_config['TMPL_ACTION_SUCCESS'] = APP_PATH.'Admin/View/Public/think/success.html';    // 成功跳转对应的模板文件
                $system_config['TMPL_EXCEPTION_FILE'] = APP_PATH.'Admin/View/Public/think/exception.html';  // 异常页面的模板文件
            } else if (MODULE_NAME === 'Home' || $controller_name[0] === 'Home') {
                // Home前台与模块前台标记
                $system_config['MODULE_MARK'] = 'Home';

                // SESSION与COOKIE与前缀设置避免冲突
                $system_config['SESSION_PREFIX'] = ENV_PRE.'Home_';  // Session前缀
                $system_config['COOKIE_PREFIX']  = ENV_PRE.'Home_';  // Cookie前缀

                // 错误页面模板
                $system_config['TMPL_ACTION_ERROR']   = APP_PATH.'Home/View/Public/think/error.html';      // 错误跳转对应的模板文件
                $system_config['TMPL_ACTION_SUCCESS'] = APP_PATH.'Home/View/Public/think/success.html';    // 成功跳转对应的模板文件
                $system_config['TMPL_EXCEPTION_FILE'] = APP_PATH.'Home/View/Public/think/exception.html';  // 异常页面的模板文件
            }

            // 获取当前主题的名称
            $current_theme = D('Admin/Theme')->where(array('current' => 1))->order('id asc')->find();

            // 当前模块模版参数配置
            $system_config['TMPL_PARSE_STRING'] = C('TMPL_PARSE_STRING');  // 先取出配置文件中定义的否则会被覆盖
            $system_config['TMPL_PARSE_STRING']['__IMG__']  = __ROOT__.'/'.APP_PATH.MODULE_NAME.'/View/Public/img';
            $system_config['TMPL_PARSE_STRING']['__CSS__']  = __ROOT__.'/'.APP_PATH.MODULE_NAME.'/View/Public/css';
            $system_config['TMPL_PARSE_STRING']['__JS__']   = __ROOT__.'/'.APP_PATH.MODULE_NAME.'/View/Public/js';
            $system_config['TMPL_PARSE_STRING']['__LIBS__'] = __ROOT__.'/'.APP_PATH.MODULE_NAME.'/View/Public/libs';
            $system_config['USER_CENTER_SIDE'] = './Application/User/View/Home/default/side.html';
            $system_config['USER_CENTER_FORM'] = './Application/User/View/Home/builder/form.html';
            $system_config['USER_CENTER_LIST'] = './Application/User/View/Home/builder/list.html';
            $system_config['ADMIN_PUBLIC_LAYOUT'] = './Application/Admin/View/Public/layout.html';
            $system_config['HOME_PUBLIC_LAYOUT']  = './Application/Home/View/Public/layout.html';
            if ($current_theme) {
                $current_theme['module'] = explode(',', $current_theme['module']);
                $current_theme['module'][] = 'Home';  // 强制支持主题对Home的支持

                if (in_array('User', $current_theme['module'])) {
                    $system_config['USER_CENTER_SIDE'] = './Theme/'.$current_theme['name'].'/User/default/side.html';
                    $system_config['USER_CENTER_FORM'] = './Theme/'.$current_theme['name'].'/User/builder/form.html';
                    $system_config['USER_CENTER_LIST'] = './Theme/'.$current_theme['name'].'/User/builder/list.html';
                }
                $system_config['HOME_PUBLIC_LAYOUT']  = './Theme/'.$current_theme['name'].'/Home/Public/layout.html';

                // 一旦开启主题那么前台必须启用主题相关模版(后台无需支持主题)
                $theme_public_path = './Theme/'.$current_theme['name'].'/Home/Public';
                if (is_dir($theme_public_path)) {
                    $system_config['TMPL_PARSE_STRING']['__HOME_IMG__']   = __ROOT__.'/'.$theme_public_path.'/img';
                    $system_config['TMPL_PARSE_STRING']['__HOME_CSS__']   = __ROOT__.'/'.$theme_public_path.'/css';
                    $system_config['TMPL_PARSE_STRING']['__HOME_JS__']    = __ROOT__.'/'.$theme_public_path.'/js';
                    $system_config['TMPL_PARSE_STRING']['__HOME_LIBS__']  = __ROOT__.'/'.$theme_public_path.'/libs';
                }

                // 如果启用的主题配置里勾选了当前模块那么当前模块启用主题相关模版
                if (in_array(MODULE_NAME, $current_theme['module']) && $system_config['MODULE_MARK'] === 'Home') {
                    $system_config['CURRENT_THEME'] = $current_theme['name'];  // 设置当前主题
                    $theme_public_path = './Theme/'.$current_theme['name'].'/'.MODULE_NAME.'/Public';
                    $theme_wap_path    = './Theme/'.$current_theme['name'].'/'.MODULE_NAME.'/wap';
                    if (is_dir($theme_public_path)) {
                        $system_config['TMPL_PARSE_STRING']['__IMG__']  = __ROOT__.'/'.$theme_public_path.'/img';
                        $system_config['TMPL_PARSE_STRING']['__CSS__']  = __ROOT__.'/'.$theme_public_path.'/css';
                        $system_config['TMPL_PARSE_STRING']['__JS__']   = __ROOT__.'/'.$theme_public_path.'/js';
                        $system_config['TMPL_PARSE_STRING']['__LIBS__'] = __ROOT__.'/'.$theme_public_path.'/libs';
                    }
                    // 判断是否含有wap专用模版
                    if (is_dir($theme_wap_path)) {
                        $system_config['HAS_WAP'] = 'wap';
                    }
                }
            }

            // 加载Formbuilder扩展类型
            $system_config['FORM_ITEM_TYPE'] = C('FORM_ITEM_TYPE');
            $formbuilder_extend = explode(',', D('Admin/Hook')->getFieldByName('FormBuilderExtend', 'addons'));
            if ($formbuilder_extend) {
                $addon_object = D('Admin/Addon');
                foreach ($formbuilder_extend as $val) {
                    $temp = json_decode($addon_object->getFieldByName($val, 'config'), true);
                    if ($temp['status']) {
                        $form_type[$temp['form_item_type_name']] = array($temp['form_item_type_title'], $temp['form_item_type_field']);
                        $system_config['FORM_ITEM_TYPE'] = array_merge($system_config['FORM_ITEM_TYPE'], $form_type);
                    }
                }
            }

            S('DB_CONFIG_DATA', $system_config, 3600);  // 缓存配置
        }

        C($system_config);  // 添加配置
    }
}
