<?php
/**
 * 卡券生成管理
 */

namespace maintainer\controllers;

use maintainer\components\LoginedController;
use common\models\BrandModel;
use common\models\Order;
use common\models\OrderExtend;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use common\models\CardCouponsGrant;
use m35\thecsv\theCsv;

/**
 * CardController implements the CRUD actions for CardCouponsGrant model.
 */
class BrandstController extends LoginedController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [];
    }


    /**c
     * Lists all CardCouponsGrant models.
     * @return mixed
     */
    public function actionIndex()
    {

        if (Yii::$app->request->isAjax) {
            $respon = array();
            $pageSize = Yii::$app->request->post('length', 10);
            $start = Yii::$app->request->post('start', 0);//偏移量
            $model = $this->_condition();
            $total = $model->count();
            $total_card = $model->sum('total_card');
            $total_settle = $model->sum('total_settle');
            $dataProvider = new ActiveDataProvider([
                'query' => $model->limit($pageSize)->offset($start),
                'pagination' => [
                    'pageSize' => $pageSize,
                    'page' => intval($start / $pageSize),
                    'totalCount' => $total
                ],
            ]);
            $total_se = $total_card ? round(100 * $total_settle / $total_card, 2) : 0;
            if ($data = $dataProvider->models) {
                foreach ($data as $val){
                    $brand_info = BrandModel::getInfo($val['brand_id']);
                    $model_info = BrandModel::getInfo($val['model_id']);
                    $se = round(100*$val['total_settle']/$val['total_card'],2);
                    $temp=[
                        $brand_info?$brand_info->model_name:'',
                        $model_info?$model_info->model_name:'',
                        $val['total_card'],
                        $val['total_settle'],
                        $se>0?$se.'%':0
                    ];
                    $respon[]=$temp;
                }
                $s_text = '激活数： <b class="font-red-mint">' . $total_card . '</b>；合计理赔数： <b class="font-red-mint">' . $total_settle . '</b>；理赔率： ' .
                    '<b class="font-red-mint"> ' . ($total_se > 0 ? $total_se . '%' : 0) . '</b>';
            }
            return json_encode(array('data' => $respon, 'recordsTotal' => $total, 'recordsFiltered' => $total, 'statistics_text' => $s_text));
        }
        $data = $this->getBrand();
        $area=[
            ['id'=>0,'name'=>'全部品牌','pId'=>0,'open'=>true]
        ];
        foreach ($data as $val){
            $temp=[
                'id'=>$val['id'],
                'name'=>$val['model_name'],
                'pId'=>$val['parent_id']
            ];
            if (!$temp['pId']) {
                $temp['open'] = true;
            }
            if(!isset($area[$val['id']])){
                $area[$val['id']]=$temp;
            }

        }
        return $this->render('index',['area'=>array_values($area)]);

    }


    public function actionExport()
    {
        $get = Yii::$app->request->get();
        $model = $this->_condition([], $get);
        $data = $model->limit(5000)->all();
        $respon=[];
        foreach ($data as $val){
            $brand_info = BrandModel::getInfo($val['brand_id']);
            $model_info = BrandModel::getInfo($val['model_id']);
            $se = round(100*$val['total_settle']/$val['total_card'],2);
            $temp=[
                $brand_info?$brand_info->model_name:'',
                $model_info?$model_info->model_name:'',
                $val['total_card'],
                $val['total_settle'],
                $se>0?$se.'%':0
            ];
            $respon[]=$temp;
        }
        set_time_limit(0);
        $this->_export($respon);
    }


    private function getBrand(){
        $card_tb= CardCouponsGrant::tableName();
        $b_tb = BrandModel::tableName();
        $oe_tb = OrderExtend::tableName();
        $query = new Query();
        $result= $query->select(['b.id','b.model_name','b.parent_id'])
            ->from(['c'=>$card_tb])->innerJoin(['oe'=>$oe_tb],'c.order_id = oe.order_id')->innerJoin(['b'=>$b_tb],'oe.brand_id = b.id OR oe.model_id = b.id')
            ->all();
        return $result;
    }




    private function _export($respon)
    {
        theCsv::export([
            'data' => $respon,
            'name' => "seller_brand_statistics_" . date('Y_m_d_H', time()) . ".csv",    // 自定义导出文件名称
            'header' => ['品牌', '型号', '激活量', '理赔量',  '理赔率'],
        ]);
    }




    private function _condition($condition = [], $request = [])
    {
        //根据pid判断当前是否是二级商家，二级商家根据order_extend 的商家所属进行判断，一级商家根据卡券归属进行查询
        if($this->seller->pid){
            $where = ['oe.seller_id'=>$this->seller->seller_id];
        }else{
            $where = ['c.seller_id'=>$this->seller->seller_id];
        }
        $post = Yii::$app->request->post();
        if ($request) {
            $post = array_merge($post, $request);
        }
        $condition = array_merge($where, $condition);
        $card_tb= CardCouponsGrant::tableName();
        $or_tb = Order::tableName();
        $oe_tb = OrderExtend::tableName();
        $query = new Query();
        $query->select(['oe.brand_id','oe.model_id','o.order_state','count(c.id) as total_card','COUNT( if(o.order_state = 40,true,null ) ) as total_settle'])->from(['c'=>$card_tb])->innerJoin(['o'=>$or_tb],'c.order_id = o.order_id')->innerJoin(['oe'=>$oe_tb],'o.order_id = oe.order_id')
            ->where($condition)->andWhere(['not in','o.order_state',[Order::__ORDER_CACEL,Order::__ORDER_REFUND_SUCC,Order::__ORDER_TO_CACEL]]);
        if(isset($post['brand_id']) && !empty($post['brand_id'])){
            $query->andWhere(['or','oe.brand_id = '.intval($post['brand_id']),'oe.model_id = '.intval($post['brand_id'])]);
        }
        $query->groupBy('oe.model_id');
        return $query->orderBy('oe.brand_id ASC ');
    }


}
