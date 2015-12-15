<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Common\Controller;
use Think\Controller;
/**
 * 公共控制器
 * @author jry <598821125@qq.com>
 */
class CommonController extends Controller {
    /**
     * 模板显示 调用内置的模板引擎显示方法，
     * @access protected
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @param string $charset 输出编码
     * @param string $contentType 输出类型
     * @param string $content 输出内容
     * @param string $prefix 模板缓存前缀
     * @return void
     */
    protected function display($template='', $charset='', $contentType='', $content='', $prefix='') {
        $depr     = C('TMPL_FILE_DEPR');
        $template = str_replace(':', $depr, $template);
        if (C('CURRENT_THEME') && !(strstr($template, ".html"))) {
            // 分析模板文件规则
            $controller_name = explode('/', CONTROLLER_NAME);
            if ('' == $template) {
                // 如果模板文件名为空 按照默认规则定位
                if (sizeof($controller_name) === 2) {
                    $template = $controller_name[1] . $depr . ACTION_NAME;
                } else {
                    $template = $controller_name[0] . $depr . ACTION_NAME;
                }
            } else if (false === strpos($template, $depr)) { // 没有/
                $template = CONTROLLER_NAME . $depr . $template;
                if (sizeof($controller_name) === 2) {
                    $template = $controller_name[1] . $depr . $template;
                } else {
                    $template = $controller_name[0] . $depr . $template;
                }
            }

            $template = './Theme/'.C('CURRENT_THEME').$depr.MODULE_NAME.$depr.$template.C('TMPL_TEMPLATE_SUFFIX');

            if (!is_file($template)) {
                header("Content-type: text/html; charset=utf-8");
                echo ('模版文件' . $template . '不存在');
                return false;
            }
        }
        $this->view->display($template, $charset, $contentType, $content, $prefix);
    }
}
