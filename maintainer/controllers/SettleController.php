<?php
/**
 * Created by PhpStorm.
 * User: tp
 * Date: 16/9/12
 * Time: 上午10:27
 */

namespace maintainer\controllers;


use common\models\Seller;
use common\models\SellerSettle;
use common\models\SellerSettleLog;
use maintainer\components\LoginedController;
use Yii;
use yii\data\ActiveDataProvider;

class SettleController extends LoginedController
{
    public function actionIndex()
    {

        $seller_id=Yii::$app->user->identity->getSellerInfo()->seller_id;
        $model=SellerSettle::find();
        $model->where(['seller_id'=>$seller_id,'status'=>1])->andWhere(['<=','settle_time',time()]);
        $rawSql = $model->createCommand()->getRawSql();
        $rawSql =explode('WHERE',$rawSql);
        $sql='SELECT SUM(`price` * ( 1 - `expenses` / 100 )) as price,SUM(`price`) as total,count(id) as n FROM '.SellerSettle::tableName().' WHERE '.$rawSql[1];
        $total=Yii::$app->getDb()->createCommand($sql)->queryAll();
        $count=$total[0]['n'];
        $sum=$total[0]['price'];
        $this->render('index',['sum'=>number_format($sum,2),'count'=>$count,'total'=>number_format($total[0]['total'],2)]);
    }

    public function actionGetdata()
    {
        $pageSize = Yii::$app->request->post('length', 10);
        $start = Yii::$app->request->post('start', 0);//偏移量
        $model = $this->_condition();
        $count = $model->count('*');
        $dataProvider = new ActiveDataProvider([
            'query' => $model->orderBy('status asc,id DESC')->limit($pageSize)->offset($start),
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
            $p = number_format($val->price * $val->expenses / 100, 2);
            $btn = ($val->status != 1 || $val->settle_time > time()) ?
                '' : '<a class="btn oranged  btn-default" onClick="handleStatus(' . $val->id . ',1,'.($val->price - $p).')" href="javascript:;"><i class="fa fa-check"></i> 申请提现 </a>';
            $btn.=$val->status ==1?'':'<a class="btn green btn-default" href="'.$this->createUrl(['withdraw/index','order_id'=>$val->m_order_id]).'"><i class="fa fa-eye"></i> 查看提现明细 </a>';

            $status = !$val->status ? 'font-red-intense' : '';
            $data['data'][] = array(
                $val->status == 1 && $val->settle_time <= time() ?'<input type="checkbox" class="settle_checkbox" data-price="'.($val->price - $p).'" name="id[]" value="' . $val->id . '">':'',
                $val->id,
                Seller::getSellerInfo($val->seller_id)->seller_name,
                '<a href="'.$this->createUrl(['order/view','order_id'=>$val->m_order_id]).'" >' . $val->m_order_id . '</a>',
                $val->price,
                $val->expenses . ' % ( ￥' . $p . ' )',
                '<span class="font-purple-medium">￥' . ($val->price - $p).'</span>',
                $val->settle_time ? date('Y-m-d', $val->settle_time) : '',
                '<span class="font-purple-medium  '.$status.'" >' . SellerSettle::getStatusMsg($val->status) . '</span>',
                $val->finsh_time ? date('Y-m-d H:i:s', $val->finsh_time) : '',
                $btn
            );
        }

        return json_encode($data);
    }

    public function actionChange()
    {
        if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
            $this->showMessage('非法访问!');
        }
        $id = Yii::$app->request->post('id', '');
        $mark='商家'.Yii::$app->user->identity->getSellerInfo()->seller_name.'于 '.date('Y-m-d H:i:s').' 申请提现';
        if (!$id) {
            return $this->getCheckNo('参数错误');
        }

        $model = new SellerSettle();
        if($id === '-1'){
            $seller_id=Yii::$app->user->identity->getSellerInfo()->seller_id;
            $result=$model->find()->where(['seller_id'=>$seller_id,'status'=>1])->andWhere(['<=','settle_time',time()])->asArray()->all();
            $ids=array_column($result,'id');
            $id = join(',',$ids);
        }
        if ($model->updateAll(['status' => 0], 'id IN ('.$id.') AND status = :status AND settle_time <= :time', [ ':status' => SellerSettle::SETTLE_WAIT,':time'=>time()])) {
            $model ->setLog($id,$mark,Yii::$app->user->identity->getSellerInfo()->seller_name,'withdrawal',['send_id'=>Yii::$app->user->identity->id,'seller_id'=>0]);
            return $this->getCheckYes([], '提现申请成功');
        }
        return $this->getCheckNo('提现申请失败,请稍后重试...');
    }

    private function _condition($condition = array())
    {
        $model = SellerSettle::find();
        $get = Yii::$app->request->get();
        $post = Yii::$app->request->post();
        $tj = array_merge($get, $post);
        $where = [];
        $seller_id=Yii::$app->user->identity->getSellerInfo()->seller_id;
        $model->where(['seller_id'=>$seller_id]);
        if (isset($tj['name']) && !empty($tj['name'])) {
            $model->andWhere(['m_order_id' => $tj['name']]);
        }
        if (isset($tj['status']) && (!empty($tj['status']) || $tj['status'] === '0')) {
            $where['status'] = $tj['status'];
        }
        $condition = array_merge($where, $condition);
        $model->andWhere($condition);
        return $model;
    }
}