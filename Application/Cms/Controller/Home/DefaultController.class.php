<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Cms\Controller\Home;
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
            $doc_type_list = D(D('Default')->moduleName.'/Type')->where($con)->select();

            // 获取字段信息
            $new_doc_type_list = array();
            $attribute_object = D(D('Default')->moduleName.'/Attribute');
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
        $this->assign('_index_cate', parse_attr(C('cms_config.cate')));  // 获取首页栏目自定义配置
        $this->assign('meta_title', '官网');
        $this->display();
    }

    /**
     * 文章列表方法
     * @author jry <598821125@qq.com>
     */
    public function lists($cid) {
        // 获取分类信息
        $map['cid'] = $cid;
        $category_info = D('Category')->find($cid);
        switch ($category_info['doc_type']) {
            case 1:  // 链接
                if (stristr($category_info['url'], 'http://') || stristr($category_info['url'], 'https://')) {
                    redirect($category_info['url']);
                } else {
                    $this->redirect($category_info['url']);
                }
                break;
            case 2:  // 单页
                redirect(U(D('Default')->moduleName.'/Home/Category/detail', array("id" => $category_info["id"])));
                break;
            default :
                // 获取文档公共属性信息
                if(C('CURRENT_THEME')){
                    if ($category_info['index_template']) {
                        $template = 'default/'.$category_info['index_template'];
                    } else {
                        $template = 'default/index_default';
                    }
                } else {
                    $template = $category_info['index_template'] ? 'Home/default/'.$category_info['index_template'] : 'Home/default/index_default';
                }

                // 获取该分类绑定文档模型的主要字段
                $type_object      = D('Type');
                $attribute_object = D('Attribute');
                $doc_type_info    = $type_object->find($category_info['doc_type']);
                $type_main_field  = $attribute_object->getFieldById($doc_type_info['main_field'], 'name');

                // 获取筛选字段
                $con = array();
                $con['id'] = array('in', $doc_type_info['filter_field']);
                $filter_field_list = $attribute_object->where($con)->select();
                $new_filter_field_list = array();
                foreach ($filter_field_list as $key => $val) {
                    $val['options'] = parse_attr($val['options']);
                    $new_filter_field_list[$val['name']] = $val;
                }

                // 关键字搜索
                if (I('keywords')) {
                    $map[$type_main_field] = array('like', '%'.I('keywords').'%');
                }

                // 获取文档列表
                $map['status'] = array('eq', 1);
                $base_table   = C('DB_PREFIX').D('Default')->tableName;
                $extend_table = strtolower(C('DB_PREFIX').D('Default')->moduleName.'_'.$doc_type_info['name']);
                $document_list = D('Default')
                               ->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))
                               ->order('sort desc,'.$base_table.'.id desc')
                               ->where($map)
                               ->join($extend_table.' ON '.$base_table.'.id = '.$extend_table.'.id')
                               ->select();
                $page = new Page(
                    D('Default')->where($map)->join($extend_table.' ON '.$base_table.'.id = '.$extend_table.'.id')->count(),
                    C('ADMIN_PAGE_ROWS')
                );

                // 如果当前分类下无文档则获取子分类文档
                if (!$document_list) {
                    // 获取当前分类的子分类ID列表
                    $child_cagegory_id_list = D('Category')->where(array('pid' => $cid))->getField('id',true);
                    if ($child_cagegory_id_list) {
                        $map['cid'] = array('in', $child_cagegory_id_list);
                        $document_list = D('Default')
                                       ->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))
                                       ->order('sort desc,'.$base_table.'.id desc')
                                       ->where($map)
                                       ->join($extend_table.' ON '.$base_table.'.id = '.$extend_table.'.id')
                                       ->select();
                        $page = new Page(
                            D('Default')->where($map)->join($extend_table.' ON '.$base_table.'.id = '.$extend_table.'.id')->count(),
                            C('ADMIN_PAGE_ROWS')
                        );
                    }
                }

                // 给文档主要字段赋值，如：文章标题、商品名称
                foreach ($document_list as &$doc) {
                    // 给文档主要字段赋值，如：文章标题、商品名称
                    $doc['main_field'] = $doc[$type_main_field];
                }

                // 模版赋值
                $this->assign('_current_category', $category_info);
                $this->assign('_filter_field_list', $new_filter_field_list);
                $this->assign('_search_url', U('', $_GET));  // 构造搜索地址
                $this->assign('_category_info', $category_info);
                $this->assign('volist', $document_list);
                $this->assign('page', $page->show());
                $this->meta_title = $category_info['title'].'列表';
                Cookie('__forward__', $_SERVER['REQUEST_URI']);
                $this->display($template);
                break;
        }
    }

    /**
     * 文章信息
     * @author jry <598821125@qq.com>
     */
    public function detail($id) {
        $article_object = D('Default');
        $info = $article_object->where('status=1')->detail($id);
        if (!$info) {
            $this->error('错误：'. $article_object->getError());
        }

        // 设置文档显示模版
        if ($info['category']['detail_template']) {
            $template = 'default/'.$info['category']['detail_template'];
        } else {
            $template = 'default/detail_default';
        }
        if (!C('CURRENT_THEME')) {
            $template = 'Home/'.$template;
        }

        $this->assign('info', $info);
        $this->assign('_current_category', $info['category']);
        $this->assign('_filter_field_list', $info['filter_field_list']);
        $this->assign('_search_url', U('index', array('cid' => $info['category']['id'])));  // 构造搜索地址
        $this->assign('meta_title', $info['main_field']);
        $this->assign('meta_keywords', $info['tags'] ? : C('WEB_SITE_KEYWORD'));
        $this->assign('meta_description', $info['abstract'] ? : C('WEB_SITE_DESCRIPTION'));
        $this->assign('meta_cover', $info['cover']);
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->display($template);
    }
}