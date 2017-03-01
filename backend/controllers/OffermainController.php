<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/9
 * Time: 14:47
 */

namespace backend\controllers;


use backend\components\LoginedController;
use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use common\models\BrandModel;
use common\models\BrandOffer;
use common\models\MaintenanceOffer;
use common\models\Seller;

class OffermainController extends LoginedController
{
    public function actionIndex(){
        if(Yii::$app->request->isAjax && Yii::$app->request->isPost){
            $pageSize = Yii::$app->request->post('length', 10);
            $start = Yii::$app->request->post('start', 0);
            $model = $this->_condition();
            $count = $model->count('*');
            $dataProvider = new ActiveDataProvider([
                'query' => $model->limit($pageSize)->offset($start),
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
            $brand = $this->getBrand();
            $seller_id = array_column($brand_offer,'seller_id');
            $seller = $this->getSeller($seller_id);
            foreach ($brand_offer as $key => $val) {
                $data['data'][] = array(
                    $val['id'],
                    $seller[$val['seller_id']]['seller_name'],
                    $val['name'],
                    $brand[$val['brand_id']] ? $brand[$val['brand_id']]['model_name'] : '',
                    $brand[$val['model_id']] ? $brand[$val['model_id']]['model_name'] : '',
                    $brand[$val['color_id']] ? $brand[$val['color_id']]['model_name'] : '',
                    $val['inner_screen'],
                    $val['outer_screen'],
                    $val['commission'] . ' %',
                    $val['status'] == 1 ? '<span class="font-green-sharp">正常</span>' : '<span class="">暂停</span>',
                );
            }
            exit(json_encode($data));
        }
        $offer_id = Yii::$app->request->get('offer_id');
        $this->render('index',['offer_id'=>$offer_id]);
    }

    private function _condition($condition = array())
    {

        $model = new Query();
        $field = 'b_r.name,b_r.brand_id,b_r.model_id,b_r.color_id,b_r.inner_screen,b_r.outer_screen,b_r.commission,m_r.id,m_r.status,m_r.offer_id,m_r.seller_id';
        $model->select($field)->from(['b_r' => BrandOffer::tableName(), 'm_r' => MaintenanceOffer::tableName()])->where(
            'm_r.offer_id = b_r.offer_id  '
        );
        $get = Yii::$app->request->get();
        $post = Yii::$app->request->post();
        $tj = array_merge($get, $post);
        $where = [];
        if (isset($tj['name']) && !empty($tj['name'])) {
            if($tj['type'] == 1){
                $model->andwhere(['like', 'b_r.name', $tj['name']]);
            }else{
                $child = Seller::find()->select('seller_id')->where(['like','seller_name',$post['name']])->asArray()->all();
                if($child){
                    $to_seller_id =array_column($child,'seller_id');
                    $where['m_r.seller_id'] = $to_seller_id;
                }else{
                    $where['m_r.seller_id'] = -1;
                }
            }
        }
        if(isset($tj['offer_id']) && $tj['offer_id']){
            $where['b_r.offer_id'] = (int)$tj['offer_id'];
        }

        $condition = array_merge($where, $condition);
        $model->andWhere($condition);
        return $model;
    }

    /**
     * 获取品牌
     * @return array
     */
    private function getBrand()
    {
        $brand = BrandModel::find()->asArray()->all();
        $data = [];
        foreach ($brand as $val) {
            $data[$val['id']] = $val;
        }
        return $data;
    }

    /**
     * 以商家Id为键获取商家
     * @param array $seller_id
     * @return array
     */
    private function getSeller($seller_id=[]){
        $result=Seller::find()->where(['seller_id'=>$seller_id])->asArray()->all();
        $seller=[];
        foreach ($result as $val){
            $seller[$val['seller_id']]=$val;
        }
        return $seller;
    }
}