<?php

namespace common\modules\mweixin\controllers;
use common\models\Article;
use common\models\InsuranceCoverage;
use weixin\components\BaseController;
use Yii;
/**
 * api for 商品详情
*/

class GoodsController extends BaseController
{
    /**
     * 商品列表
     * @return array
     */
    public function actionIndex()
    {
        $model = InsuranceCoverage::find();
        $field='id,image,official_price,max_payment,coverage_code,period,coverage_name';
        $result=$model->select($field)->where(['status'=>1])->orderBy('id asc')->asArray()->all();
        return $this->getCheckYes($result);
    }

    /**
     * 获取商品详细信息
     * @return array
     */
    public function actionInfo(){
        $id =Yii::$app->request->get('id','');
        if(!$id){
            return $this -> getCheckNo('参数错误');
        }
        $field='id,image,official_price,max_payment,coverage_code,period,coverage_name,type_id';
        $info = InsuranceCoverage::find()->select($field)->where(['id'=>$id])->asArray()->one();
        $article=Article::findOne(['coverage_type_id'=>$info['type_id'],'tag_id'=>1]);
        $info['coverage_info']=$article?$article->content:'';
        return $info?$this->getCheckYes($info):$this->getCheckNo('当前商品不存在');
    }

}
