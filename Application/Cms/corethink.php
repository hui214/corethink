<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
// 模块信息配置
return array(
    // 模块信息
    'info' => array(
        'name'        => 'Cms',
        'title'       => 'CMS门户',
        'icon'        => 'fa fa-newspaper-o',
        'icon_color'  => '#9933FF',
        'description' => 'CMS门户模块',
        'developer'   => '南京科斯克网络科技有限公司',
        'website'     => 'http://www.corethink.cn',
        'version'     => '1.1.1',
        'beta'        => 'false',
        'dependences' => array(
            'Admin'   => '1.1.0',
        )
    ),

    // 模块配置
    'config' => array(
        'need_check' => array(
            'title'   => '前台发布审核',
            'type'    => 'radio',
            'options' => array(
                '1'   => '需要',
                '0'   => '不需要',
            ),
            'value'   => '0',
        ),
        'toggle_comment' => array(
            'title'  => '是否允许评论文档',
            'type'   =>'radio',
            'options' => array(
                '1'   => '允许',
                '0'   => '不允许',
            ),
            'value'  => '1',
        ),
        'group_list' => array(
            'title'  => '栏目分组',
            'type'   =>'array',
            'value'  => '1:默认',
        ),
        'cate' => array(
            'title'  => '首页栏目自定义',
            'type'   =>'array',
            'value'  => 'a:1',
        ),
        'taglib' => array(
            'title'  => '加载标签库',
            'type'   =>'checkbox',
            'options'=> array(
                'Cms' => 'Cms',
            ),
            'value'  => array(
                '0'  => 'Cms',
            ),
        ),
    ),

    // 后台菜单及权限节点配置
    'admin_menu' => array(
        '1' => array(
            'id'    => '1',
            'pid'   => '0',
            'title' => '内容',
            'icon'  => 'fa fa-newspaper-o',
        ),
        '2' => array(
            'pid'   => '1',
            'title' => '内容管理',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '3' => array(
            'pid'   => '2',
            'title' => '文章配置',
            'icon'  => 'fa fa-wrench',
            'url'   => 'Cms/Admin/Default/module_config',
        ),
        '4' => array(
            'pid'   => '2',
            'title' => '文档模型',
            'icon'  => 'fa fa-th-large',
            'url'   => 'Cms/Admin/Type/index',
        ),
        '5' => array(
            'pid'   => '4',
            'title' => '新增',
            'url'   => 'Cms/Admin/Type/add',
        ),
        '6' => array(
            'pid'   => '4',
            'title' => '编辑',
            'url'   => 'Cms/Admin/Type/edit',
        ),
        '7' => array(
            'pid'   => '4',
            'title' => '设置状态',
            'url'   => 'Cms/Admin/Type/setStatus',
        ),
        '8' => array(
            'pid'   => '4',
            'title' => '字段管理',
            'icon'  => 'fa fa-database',
            'url'   => 'Cms/Admin/Attribute/index',
        ),
        '9' => array(
            'pid'   => '8',
            'title' => '新增',
            'url'   => 'Cms/Admin/Attribute/add',
        ),
        '10' => array(
            'pid'   => '8',
            'title' => '编辑',
            'url'   => 'Cms/Admin/Attribute/edit',
        ),
        '11' => array(
            'pid'   => '8',
            'title' => '设置状态',
            'url'   => 'Cms/Admin/Attribute/setStatus',
        ),
        '12' => array(
            'pid'   => '2',
            'title' => '栏目分类',
            'icon'  => 'fa fa-navicon',
            'url'   => 'Cms/Admin/Category/index',
        ),
        '13' => array(
            'pid'   => '12',
            'title' => '新增',
            'url'   => 'Cms/Admin/Category/add',
        ),
        '14' => array(
            'pid'   => '12',
            'title' => '编辑',
            'url'   => 'Cms/Admin/Category/edit',
        ),
        '15' => array(
            'pid'   => '12',
            'title' => '设置状态',
            'url'   => 'Cms/Admin/Category/setStatus',
        ),
        '16' => array(
            'pid'   => '2',
            'title' => '文章管理',
            'icon'  => 'fa fa-edit',
            'url'   => 'Cms/Admin/Default/index',
        ),
        '17' => array(
            'pid'   => '2',
            'title' => '幻灯切换',
            'icon'  => 'fa fa-image',
            'url'   => 'Cms/Admin/Slider/index',
        ),
        '18' => array(
            'pid'   => '17',
            'title' => '新增',
            'url'   => 'Cms/Admin/Slider/add',
        ),
        '19' => array(
            'pid'   => '17',
            'title' => '编辑',
            'url'   => 'Cms/Admin/Slider/edit',
        ),
        '20' => array(
            'pid'   => '17',
            'title' => '设置状态',
            'url'   => 'Cms/Admin/Slider/setStatus',
        ),
        '21' => array(
            'pid'   => '2',
            'title' => '通知公告',
            'icon'  => 'fa fa-bullhorn',
            'url'   => 'Cms/Admin/Notice/index',
        ),
        '22' => array(
            'pid'   => '21',
            'title' => '新增',
            'url'   => 'Cms/Admin/Notice/add',
        ),
        '23' => array(
            'pid'   => '21',
            'title' => '编辑',
            'url'   => 'Cms/Admin/Notice/edit',
        ),
        '24' => array(
            'pid'   => '21',
            'title' => '设置状态',
            'url'   => 'Cms/Admin/Notice/setStatus',
        ),
        '25' => array(
            'pid'   => '2',
            'title' => '底部导航',
            'icon'  => 'fa fa-map-signs',
            'url'   => 'Cms/Admin/Footnav/index',
        ),
        '26' => array(
            'pid'   => '25',
            'title' => '新增',
            'url'   => 'Cms/Admin/Footnav/add',
        ),
        '27' => array(
            'pid'   => '25',
            'title' => '编辑',
            'url'   => 'Cms/Admin/Footnav/edit',
        ),
        '28' => array(
            'pid'   => '25',
            'title' => '设置状态',
            'url'   => 'Cms/Admin/Footnav/setStatus',
        ),
        '29' => array(
            'pid'   => '2',
            'title' => '友情链接',
            'icon'  => 'fa fa-link',
            'url'   => 'Cms/Admin/FriendlyLink/index',
        ),
        '30' => array(
            'pid'   => '29',
            'title' => '新增',
            'url'   => 'Cms/Admin/FriendlyLink/add',
        ),
        '31' => array(
            'pid'   => '29',
            'title' => '编辑',
            'url'   => 'Cms/Admin/FriendlyLink/edit',
        ),
        '32' => array(
            'pid'   => '29',
            'title' => '设置状态',
            'url'   => 'Cms/Admin/FriendlyLink/setStatus',
        ),
        '33' => array(
            'pid'   => '2',
            'title' => '回收站',
            'icon'  => 'fa fa-recycle',
            'url'   => 'Cms/Admin/Default/recycle',
        ),
        '34' => array(
            'pid'   => '33',
            'title' => '设置状态',
            'url'   => 'Cms/Admin/Notice/setStatus',
        ),
    )
);
