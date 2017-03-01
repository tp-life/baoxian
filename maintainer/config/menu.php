<?php

return [

    /**
     * 具体菜单中 role 字段用于表明当前商家身份，insurance 表明当前商家为保险  repair 为维修商户  为空或者不填表示两者都具备权限
     * level 用于表明商家等级 1 为一级商家 2 为二级商家 0为 适用全部等级商家
     */
    'menu_weixiushangjia' => [

        //系统菜单配置开始
        [
            'id' => 0,
            'name' => '系统管理',
            'icon' => 'icon-settings',
            'active' => false,
            'group' => [

                [
                    'id' => 0,
                    'name' => '系统首页',
                    'icon' => 'icon-users',
                    'active' => false,
                    'nodes' => [
                        ['module' => 'site',
                            'action' => 'index',
                            'name' => '系统首页',
                            'module_id' => 0,
                            'action_id' => 0,
                            'active' => false,
                            'url' => '/site/index',
                        ],
                    ],

                ],

            ],

        ],
        //系统菜单配置结束
        [
            'id' => 1,
            'name' => '报价管理',
            'icon' => 'icon-wallet',
            'active' => false,
            'group' => [
                [
                    'id' => 0,
                    'name' => '品牌报价',
                    'icon' => '',
                    'active' => false,
                    'nodes' => [
                        [
                            'module' => 'offer',
                            'action' => 'index',
                            'module_id' => 0,
                            'action_id' => 0,
                            'active' => false,
                            'role' =>'repair',
                            'level'=>0,
                            'url' => '/offer/index',
                        ],
                    ],
                ],
            ],

        ],
        //帐号管理
        [
            'id' => 1,
            'name' => '帐号管理',
            'icon' => 'icon-wallet',
            'active' => false,
            'group' => [
                [
                    'id' => 0,
                    'name' => '商家资料',
                    'icon' => '',
                    'active' => false,
                    'nodes' => [
                        [
                            'module' => 'account',
                            'action' => 'index',
                            'module_id' => 0,
                            'action_id' => 0,
                            'active' => false,
                            'url' => '/account/index',
                        ],
                    ],
                ],
                [
                    'id' => 1,
                    'name' => '子商户',
                    'icon' => '',
                    'active' => false,
                    'nodes' => [
                        [
                            'module' => 'seller',
                            'action' => 'index',
                            'module_id' => 0,
                            'action_id' => 0,
                            'active' => false,
                            'role' =>'',
                            'level'=>1,
                            'url' => '/seller/index',
                        ],
                    ],
                ],
            ],
        ],

        //订单管理配置开始
        [
            'id' => 0,
            'name' => '订单管理',
            'icon' => 'icon-basket-loaded',
            'active' => false,
            'group' => [

				[
					'id' => 0,
					'name' => '保险订单',
					'icon' => ' icon-target',
					'active' => false,
					'nodes' => [
						['module' => 'border',
							'action' => 'index',
							'name' => '保险订单',
							'module_id' => 0,
							'action_id' => 0,
							'active' => false,
							'role' =>'insurance',
							'level'=>1,
							'url' => '/border/index',
						],

					],

				],

                [
                    'id' => 0,
                    'name' => '维保理赔',
                    'icon' => ' icon-globe',
                    'active' => false,
                    'nodes' => [
                        ['module' => 'order',
                            'action' => 'index',
                            'name' => '维保理赔',
                            'module_id' => 0,
                            'action_id' => 0,
                            'active' => false,
                            'role' =>'repair',
                            'level'=>0,
                            'url' => '/order/index',
                        ],

                    ],

                ],

            ],

        ],
        //订单管理配置结束

        [
            'id' => 2,
            'name' => '财务管理',
            'icon' => 'icon-calculator',
            'active' => false,
            'group' => [
                [
                    'id' => 0,
                    'name' => '提现管理',
                    'icon' => '',
                    'active' => false,
                    'nodes' => [
                        [
                            'module' => 'settle',
                            'action' => 'index',
                            'module_id' => 0,
                            'action_id' => 0,
                            'active' => false,
                            'role' =>'repair',
                            'level'=>0,
                            'url' => '/settle/index',
                        ],
                    ],
                ],

            ],
        ],
        //卡券管理
        [
            'id' => 2,
            'name' => '卡券管理',
            'icon' => 'icon-wallet',
            'active' => false,
            'group' => [
                [
                    'id' => 0,
                    'name' => '我的卡券',
                    'icon' => '',
                    'active' => false,
                    'nodes' => [
                        [
                            'module' => 'card',
                            'action' => 'me',
                            'module_id' => 0,
                            'action_id' => 0,
                            'active' => false,
                            'role' =>'insurance',
                            'level'=>0,
                            'url' => '/card/me',
                        ],
                    ],
                ],
                [
                    'id' => 0,
                    'name' => '卡券发放',
                    'icon' => '',
                    'active' => false,
                    'nodes' => [
                        [
                            'module' => 'card',
                            'action' => 'issue',
                            'module_id' => 0,
                            'action_id' => 0,
                            'active' => false,
                            'role' =>'insurance',
                            'level'=>1,
                            'url' => '/card/issue',
                        ],
                    ],
                ],

                [
                    'id' => 0,
                    'name' => '卡券申请',
                    'icon' => '',
                    'active' => false,
                    'nodes' => [
                        [
                            'module' => 'card',
                            'action' => 'index',
                            'module_id' => 0,
                            'action_id' => 0,
                            'active' => false,
                            'role' =>'insurance',
                            'level'=>0,
                            'url' => '/card/index',
                        ],
                    ],
                ],
                [
                    'id' => 0,
                    'name' => '退回申请',
                    'icon' => '',
                    'active' => false,
                    'nodes' => [
                        [
                            'module' => 'refund',
                            'action' => 'index',
                            'module_id' => 0,
                            'action_id' => 0,
                            'active' => false,
                            'role' =>'insurance',
                            'level'=>0,
                            'url' => '/refund/index',
                        ],
                    ],
                ],

                [
                    'id' => 0,
                    'name' => '下级退回',
                    'icon' => '',
                    'active' => false,
                    'nodes' => [
                        [
                            'module' => 'cardrefund',
                            'action' => 'index',
                            'module_id' => 0,
                            'action_id' => 0,
                            'active' => false,
                            'role' =>'insurance',
                            'level'=>1,
                            'url' => '/cardrefund/index',
                        ],
                    ],
                ],
            ],
        ],
    ],

];

/*

return array('menu_weixiushangjia'=> array (
    0 =>
        array (
            'id' => 15,
            'name' => '系统管理',
            'icon' => 'icon-settings',
            'active' => false,
            'group' =>
                array (
                    0 =>
                        array (
                            'id' => '97',
                            'name' => '管理员列表',
                            'icon' => 'icon-users',
                            'active' => false,
                            'nodes' =>
                                array (
                                    0 =>
                                        array (
                                            'module' => 'admin',
                                            'action' => 'index',
                                            'name' => '管理员列表',
                                            'module_id' => '6',
                                            'action_id' => '4',
                                            'active' => false,
                                            'url' => '/admin/index',
                                        ),
                                )
                        ),
                    1 =>
                        array (
                            'id' => '98',
                            'name' => '全局配置',
                            'icon' => 'icon-star',
                            'active' => false,
                            'nodes' =>
                                array (
                                    0 =>
                                        array (
                                            'module' => 'conf',
                                            'action' => 'index',
                                            'name' => '配置列表',
                                            'module_id' => '7',
                                            'action_id' => '5',
                                            'active' => false,
                                            'url' => '/conf/index',
                                        ),
                                ),
                        ),
                    2 =>
                        array (
                            'id' => '101',
                            'name' => '模块设置',
                            'icon' => 'icon-check ',
                            'active' => false,
                            'nodes' =>
                                array (
                                    0 =>
                                        array (
                                            'module' => 'module',
                                            'action' => 'index',
                                            'name' => '模块列表',
                                            'module_id' => '11',
                                            'action_id' => '10',
                                            'active' => false,
                                            'url' => '/module/index',
                                        ),
                                ),
                        ),
                    3 =>
                        array (
                            'id' => '102',
                            'name' => '菜单设置',
                            'icon' => 'icon-list',
                            'active' => false,
                            'nodes' =>
                                array (
                                    0 =>
                                        array (
                                            'module' => 'group',
                                            'action' => 'index',
                                            'name' => '菜单设置',
                                            'module_id' => '12',
                                            'action_id' => '11',
                                            'active' => false,
                                            'url' => '/group/index',
                                        ),
                                ),
                        ),
                    4 =>
                        array (
                            'id' => '112',
                            'name' => '角色权限',
                            'icon' => 'icon-shield',
                            'active' => false,
                            'nodes' =>
                                array (
                                    0 =>
                                        array (
                                            'module' => 'role',
                                            'action' => 'index',
                                            'name' => '权限分组',
                                            'module_id' => '10',
                                            'action_id' => '9',
                                            'active' => false,
                                            'url' => '/role/index',
                                        ),
                                ),
                        ),
                ),
        ),
    1 =>
        array (
            'id' => 16,
            'name' => '会员管理',
            'icon' => 'icon-user',
            'active' => false,
            'group' =>
                array (
                    0 =>
                        array (
                            'id' => '99',
                            'name' => '维修商家',
                            'icon' => 'icon-user-follow ',
                            'active' => false,
                            'nodes' =>
                                array (
                                    0 =>
                                        array (
                                            'module' => 'mainter',
                                            'action' => 'index',
                                            'name' => '维修商家列表',
                                            'module_id' => '9',
                                            'action_id' => '6',
                                            'active' => false,
                                            'url' => '/mainter/index',
                                        ),
                                ),
                        ),
                    1 =>
                        array (
                            'id' => '100',
                            'name' => '保险商家',
                            'icon' => 'icon-user-following',
                            'active' => false,
                            'nodes' =>
                                array (
                                    0 =>
                                        array (
                                            'module' => 'seller',
                                            'action' => 'index',
                                            'name' => '保险商家列表',
                                            'module_id' => '8',
                                            'action_id' => '7',
                                            'active' => false,
                                            'url' => '/seller/index',
                                        ),
                                ),
                        ),
                ),
        ),
    2 =>
        array (
            'id' => 17,
            'name' => '文章管理',
            'icon' => 'icon-notebook',
            'active' => false,
            'group' =>
                array (
                    0 =>
                        array (
                            'id' => '108',
                            'name' => '文章分类',
                            'icon' => '',
                            'active' => false,
                            'nodes' =>
                                array (
                                    0 =>
                                        array (
                                            'module' => 'articlecategory',
                                            'action' => 'index',
                                            'name' => '文章分类列表',
                                            'module_id' => '19',
                                            'action_id' => '39',
                                            'active' => false,
                                            'url' => '/articlecategory/index',
                                        ),
                                ),
                        ),
                    1 =>
                        array (
                            'id' => '109',
                            'name' => '文章列表',
                            'icon' => '',
                            'active' => false,
                            'nodes' =>
                                array (
                                    0 =>
                                        array (
                                            'module' => 'article',
                                            'action' => 'index',
                                            'name' => '文章列表',
                                            'module_id' => '17',
                                            'action_id' => '33',
                                            'active' => false,
                                            'url' => '/article/index',
                                        ),
                                ),
                        ),
                ),
        )

)
);*/
