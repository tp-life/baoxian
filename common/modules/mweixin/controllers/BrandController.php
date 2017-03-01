<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/15
 * Time: 14:04
 */

namespace common\modules\mweixin\controllers;


use common\models\BrandModel;
use weixin\components\BaseController;
use Yii;

class BrandController extends BaseController
{
    /**
     * 获取品牌型号
     * @param int $pid
     * @return array
     */
    public function actionIndex($pid=0){
        $filed='id,model_name,parent_id,first_word,sort';
        $data= BrandModel::getPrentBrand($pid,$filed);
        return $data !==false?$this->getCheckYes($data):$this->getCheckNo('品牌机型获取失败');
    }


}