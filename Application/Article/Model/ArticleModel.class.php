<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Article\Model;
use Think\Model;
/**
 * 文章模型
 * @author jry <598821125@qq.com>
 */
class ArticleModel extends Model{
    /**
     * 数据库真实表名
     * 一般为了数据库的整洁，同时又不影响Model和Controller的名称
     * 我们约定每个模块的数据表都加上相同的前缀，比如微信模块用weixin作为数据表前缀
     * @author jry <598821125@qq.com>
     */
    protected $tableName = 'article_base';

    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('doc_type', 'require', '文档类型不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('cid', 'require', '分类不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('cid', 'checkPostAuth', '该分类禁止投稿', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH),
    );

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_auto = array(
        array('uid', 'is_login', self::MODEL_INSERT, 'function'),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_INSERT),
    );

    /**
     * 检查分类是否允许前台会员投稿
     * @return int 时间戳
     * @author jry <598821125@qq.com>
     */
    protected function checkPostAuth() {
        if (MODULE_NAME == 'Home') {
            $category_post_auth = D('Article/Category')->getFieldById(I('post.cid'), 'post_auth');
            if (!$category_post_auth) {
                return false;
            }
        }
        return true;
    }

    /**
     * 新增或更新一个文章
     * @author jry <598821125@qq.com>
     */
    public function update() {
        // 处理数据
        $_POST = format_data();

        //调用create方法构造数据
        $cid = I('post.cid');
        $category_info = D('Article/Category')->find($cid);
        $doc_type_info = D('Article/Type')->where(array('id' => $category_info['doc_type']))->find();
        $_POST['doc_type'] = $doc_type_info['id'];
        $base_data = $this->create();
        if ($base_data) {
            //获取当前分类
            $extend_table_object = D('Article/Article'.ucfirst($doc_type_info['name']));
            $extend_data = $extend_table_object->create(); //子模型数据验证
            if (!$extend_data) {
                $this->error = $extend_table_object->getError();
            }
            if ($extend_data) {
                if (empty($base_data['id'])) { //新增基础内容
                    $base_id = $this->add();
                    if ($base_id) {
                        $extend_data['id'] = $base_id;
                        $extend_id = $extend_table_object->add($extend_data);
                        if (!$extend_id) {
                            $this->delete($base_id);
                            $this->error = '新增扩展内容出错！';
                            return false;
                        }
                        return $base_id;
                    } else {
                        $this->error = '新增基础内容出错！';
                        return false;
                    }
                } else {
                    $status = $this->save(); //更新基础内容
                    if ($status) {
                        $status = $extend_table_object->save(); //更新基础内容
                        if (false === $status) {
                            $this->error = '更新扩展内容出错！';
                            return false;
                        }
                        return $extend_data;
                    } else {
                        $this->error = '更新基础内容出错！';
                        return false;
                    }
                }
            }
        }
        return false;
    }

    /**
     * 获取文章列表
     * @author jry <598821125@qq.com>
     */
    public function getList($cid, $limit = 10, $order = null, $map = null) {
        //获取分类信息
        $category_info = D('Article/Category')->find($cid);
        $base_table = C('DB_PREFIX').$this->tableName;

        //获取该分类绑定文章模型的主要字段
        $type_object = D('Article/Type');
        $type = $type_object->where('system = 0')->find($category_info['doc_type']);

        $con["cid"] = array("eq", $cid);
        $con["status"] = array("eq", '1');
        if ($map) {
            $map = array_merge($con, $map);
        }
        if (!$order) {
            $order = 'sort desc,'.$base_table.'.id desc';
        }
        $extend_table = C('DB_PREFIX').'article_'.strtolower($type['name']);
        $article_list = $this->page(!empty($_GET["p"]) ? $_GET["p"] : 1, $limit)
                              ->order($order)
                              ->join($extend_table.' ON '.$base_table.'.id = '.$extend_table.'.id')
                              ->where($map)
                              ->select();
        return $article_list;
    }

    /**
     * 获取文章列表
     * @author jry <598821125@qq.com>
     */
    public function getNewList($doc_type = 3, $limit = 10, $order = null, $map = null) {
        //获取该分类绑定文章模型的主要字段
        $type_object = D('Article/Type');
        $type = $type_object->find($doc_type);
        if (!$type) {
            return false;
        }

        // 获取文章列表
        $base_table = C('DB_PREFIX').$this->tableName;
        $con["status"] = array("eq", '1');
        $con["doc_type"] = array("eq", $doc_type);
        if ($map) {
            $map = array_merge($con, $map);
        }
        if (!$order) {
            $order = 'sort desc,'.$base_table.'.id desc';
        }
        $extend_table = C('DB_PREFIX').'article_'.strtolower($type['name']);
        $article_list = $this->page(!empty($_GET["p"]) ? $_GET["p"] : 1, $limit)
                             ->order($order)
                             ->join($extend_table.' ON '.$base_table.'.id = '.$extend_table.'.id')
                             ->where($map)
                             ->select();
        return $article_list;
    }

    /**
     * 获取文章详情
     * @author jry <598821125@qq.com>
     */
    public function detail($id) {
        //获取基础表信息
        $con = array();
        $con['id'] = $id;
        $con['status'] = array('egt', 1);  // 正常、隐藏两种状态是可以访问的
        $info = $this->where($con)->find();
        if (!is_array($info)) {
            $this->error = '文章被禁用或已删除！';
            return false;
        }

        // 阅读量加1
        $result = $this->where(array('id' => $id))->SetInc('view');

        // 获取作者信息
        $info['user'] = get_user_info($info['uid']);

        // 获取发帖数量
        $info['user']['post_count'] = $this->where(array('uid' => $info['uid']))->count();

        // 获取分类及文档模型相关信息
        $category_info = D('Article/Category')->find($info['cid']);
        $doc_type_info = D('Article/Type')->find($category_info['doc_type']);
        if ($doc_type_info['system']) {
            $this->error = '文档类型错误！';
            return false;
        }
        $info['category'] = $category_info;

        // 根据文章模型获取扩展表的息
        $extend_table_object = D('Article/Article'.ucfirst($doc_type_info['name']));
        $extend_data = $extend_table_object->find($id);

        // 基础信息与扩展信息合并
        if (is_array($extend_data)) {
            $info = array_merge($info, $extend_data);
        }

        // 获取筛选字段
        $con = array();
        $con['id'] = array('in', $doc_type_info['filter_field']);
        $attribute_object = D('Article/Attribute');
        $filter_field_list = $attribute_object->where($con)->select();
        $new_filter_field_list = array();
        foreach ($filter_field_list as $key => $val) {
            $val['options'] = parse_attr($val['options']);
            $new_filter_field_list[$val['name']] = $val;
        }
        $info['filter_field_list'] = $new_filter_field_list;

        // 给文档主要字段赋值，如：文章标题、商品名称
        $type_main_field  = $attribute_object->getFieldById($doc_type_info['main_field'], 'name');
        $info['main_field'] = $info[$type_main_field];

        // 下载文件地址加密
        if ($info['file']) {
            $file_list = explode(',', $info['file']);
            foreach ($file_list as &$file) {
                $file = D('Home/Upload')->find($file);
                $uid = is_login();
                if ($uid) {
                    $file['token'] = \Think\Crypt::encrypt($file['md5'], user_md5($uid), 3600);
                } else {
                    $file['token'] = 'please login';
                }
            }
            $info['file_list'] = $file_list;
        }

        // 获取上一篇和下一篇文章信息
        $info['previous'] = $this->getPrevious($info);
        $info['next']     = $this->getNext($info);
        return $info;
    }

    /**
     * 获取当前分类上一篇文章
     * @author jry <598821125@qq.com>
     */
    private function getPrevious($info) {
        // 获取文档信息
        $map['status'] = array('eq', 1);
        $map['id'] = array('lt', $info['id']);
        $map['cid'] = array('eq', $info['cid']);
        $previous = $this->where($map)->order('id desc')->find();

        // 获取扩展信息
        if ($previous) {
            $type = D('Article/Type')->find($previous['doc_type']);
            $main_field_name = D('Article/Attribute')->getFieldById($type['main_field'], 'name');
            $previous['title'] = D('Article/Article'.ucfirst($type['name']))->getFieldById($previous['id'], $main_field_name);
        }

        if (!$previous) {
            $previous['title'] = '没有了';
            $previous['href'] = '#';
        } else {
            $previous['href'] = U('Article/Home/Article/detail', array('id' => $previous['id']));
        }
        return $previous;
    }

    /**
     * 获取当前分类下一篇文章
     * @author jry <598821125@qq.com>
     */
    private function getNext($info) {
        // 获取文档信息
        $map['status'] = array('eq', 1);
        $map['id'] = array('gt', $info['id']);
        $map['cid'] = array('eq', $info['cid']);
        $next = $this->where($map)->order('id asc')->find();

        // 获取扩展信息
        if ($next) {
            $type = D('Article/Type')->find($next['doc_type']);
            $main_field_name = D('Article/Attribute')->getFieldById($type['main_field'], 'name');
            $next['title'] = D('Article/Article'.ucfirst($type['name']))->getFieldById($next['id'], $main_field_name);
        }

        if (!$next) {
            $next['title'] = '没有了';
            $next['href'] = '#';
        } else {
            $next['href'] = U('Article/Home/Article/detail', array('id' => $next['id']));
        }
        return $next;
    }
}
