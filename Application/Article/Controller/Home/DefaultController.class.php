<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Article\Controller\Home;
use Home\Controller\HomeController;
use Common\Util\Think\Page;
/**
 * 默认控制器
 * @author jry <598821125@qq.com>
 */
class DefaultController extends HomeController {
    /**
     * 默认方法
     * @author jry <598821125@qq.com>
     */
    public function index() {
        $new_doc_type_list = S('doc_type_list');
        if (!$new_doc_type_list) {
            // 获取筛选字段
            $con = array();
            $con['status'] = 1;
            $con['system'] = 0;
            $doc_type_list = D('Article/Type')->where($con)->select();

            // 获取字段信息
            $new_doc_type_list = array();
            $attribute_object = D('Article/Attribute');
            foreach ($doc_type_list as $key => &$val) {
                $con['id'] = array('in', $val['filter_field']);
                $filter_field_list = $attribute_object->where($con)->select();
                $new_filter_field_list = array();
                foreach ($filter_field_list as $key2 => &$val2) {
                    $val2['options'] = parse_attr($val2['options']);
                    $new_filter_field_list[$val2['name']] = $val2;
                }
                $val['filter_field_list'] = $new_filter_field_list;
                $new_doc_type_list[$val['name']] = $val;
                S('doc_type_list', $system_config, 3600);  // 缓存
            }
        }

        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->assign('doc_type_list', $new_doc_type_list);
        $this->assign('_index_cate', parse_attr(C('article_config.cate')));  // 获取首页栏目自定义配置
        $this->assign('meta_title', '官网');
        $this->display();
    }
}