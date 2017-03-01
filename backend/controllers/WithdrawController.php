<?php
/**
 * Created by PhpStorm.
 * User: tp
 * Date: 16/9/12
 * Time: 上午10:27
 */

namespace backend\controllers;


use backend\components\LoginedController;
use common\models\Seller;
use common\models\SellerSettle;
use common\models\SellerSettleLog;
use Yii;
use yii\data\ActiveDataProvider;

class WithdrawController extends LoginedController
{
    public function actionIndex()
    {
        $id=Yii::$app->request->get('order_id','');
        $url=$this->createUrl(['withdraw/getdata','name'=>$id]);
        $this->render('index',['url'=>$url]);
    }

    public function actionGetdata()
    {
        $pageSize = Yii::$app->request->post('length', 10);
        $start = Yii::$app->request->post('start', 0);//偏移量
        $model = $this->_condition();
        $count = $model->count('*');
        $dataProvider = new ActiveDataProvider([
            'query' => $model->orderBy('add_time desc,seller_id asc')->limit($pageSize)->offset($start),
            'pagination' => [
                'pageSize' => $pageSize,
                'page' => intval($start / $pageSize),
                'totalCount' => $count
            ]
        ]);

        $brand_offer = $dataProvider->getModels();
        $data = [
            'draw' => intval($_REQUEST['draw']),
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => []
        ];
        foreach ($brand_offer as $key => $val) {
            $color = !$key?'font-red-intense':'';
            $data['data'][] = array(
                '<span class="'.$color.'">'.$val->id.'</span>',
                '<span class="'.$color.'">'.Seller::getSellerInfo($val->seller_id)->seller_name.'</span>',
                '<a href="'.$this->createUrl(['ordermainten/view','id'=>$val->m_order_id]).'" >' . $val->m_order_id . '</a>',
                '<span class="font-purple-medium">¥ '.number_format($val->price,2).'</span>',
                '<span class="'.$color.'">'.$val->content.'</span>',
                '<span class="'.$color.'">'.$val->name.'</span>',
                '<span class="'.$color.'">'.($val->add_time=='0000-00-00 00:00:00'?"":$val->add_time).'</span>',
            );
        }

        return json_encode($data);
    }

    private function _condition($condition = array())
    {
        $model = SellerSettleLog::find();
        $get = Yii::$app->request->get();
        $post = Yii::$app->request->post();
        if(!$post['name']) unset($post['name']);
        $tj = array_merge($get, $post);
        $where = [];
        if (isset($tj['name']) && !empty($tj['name'])) {
            $seller_id=[];
            if( !is_numeric($tj['name'])) {
                $seller_model = Seller::find()->select(['seller_id'])->where(['like', 'seller_name', $tj['name']])->all();
                foreach ($seller_model as $val) {
                    $seller_id[] = $val->seller_id;
                }
            }
            $model->where(['in', 'seller_id', $seller_id]);
            $model->orWhere(['m_order_id'=>$tj['name']]);
        }else{
            $where['id']=0;
        }
        $condition = array_merge($where, $condition);
        $model->andWhere($condition);
        return $model;
    }
}