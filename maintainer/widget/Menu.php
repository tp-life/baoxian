<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/15
 * Time: 10:09
 */

namespace maintainer\widget;

use yii\bootstrap\Widget;
use Yii;

class Menu extends Widget
{

    private $_menu;

    public function init()
    {
        parent::init();
        $this->_menu = $this->initMenuData();
    }

    public function run()
    {
        return $this->render('menu', ['data' => $this->_menu]);
    }

    /**
     * 菜单处理  控制器中使用 Yii::$app->params['_menu'] = 'settle_index'; 指定默认的模块
     * @return mixed
     */
    protected function initMenuData()
    {
        $sellerInfo = Yii::$app->user->identity->getSellerInfo();
        $role=[];
        $level=1;
        if($sellerInfo->is_insurance){
            $role[]='insurance';
        }
        if($sellerInfo -> is_repair){
            $role[]='repair';
        }
        if($sellerInfo->pid){
            $level =2;
        }
        $controllerId = Yii::$app->controller->id;
        $actionId = Yii::$app->controller->action->id;
        $menuData = Yii::$app->params['menu_weixiushangjia'];
        $setAria = Yii::$app->params['_menu'];
        $setAria = $setAria ? $setAria : '';
        $ac='';
        if(strpos($setAria,'_') !==false){
            list($setAria,$ac)=explode('_',$setAria);
        }
        //处理 选中模块 和菜单选项
        foreach ($menuData as $k_y=>&$menuBlock) {
            if ($menuBlock['group']) {
                foreach ($menuBlock['group'] as $key => $item) {
                    foreach ($item['nodes'] as $k_i => $v) {
                        if(isset($v['role']) && $v['role'] && !in_array($v['role'],$role)){
                            unset($menuBlock['group'][$key]);
                            continue;
                        }
                        if(isset($v['level']) && $v['level'] && $v['level'] != $level ){
                            unset($menuBlock['group'][$key]);
                            continue;
                        }
                        if ($v['module'] == $controllerId || strtoupper($v['module']) === strtoupper($setAria)) {
                            $menuBlock['active'] = true;
                            $menuBlock['group'][$key]['active'] = true;
                            if ($v['action'] == $actionId || $ac == $v['action']) {
                                $menuBlock['group'][$key]['nodes'][$k_i]['active'] = true;
                            }
                        }
                    }
                    if(!$menuBlock['group']){
                        unset($menuData[$k_y]); continue;
                    }
                }
            }
        }
        return $menuData;
    }

}