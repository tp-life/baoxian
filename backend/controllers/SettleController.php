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

class SettleController extends LoginedController
{
    public function actionIndex()
    {
        $this->render('index');
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

            $btn = $val->status ?
                '' : '<a class="btn yellow btn-xs btn-default" onClick="handleStatus(' . $val->id . ')" href="javascript:;"><i class="fa fa-check"></i> 结算打款 </a>';
            $btn.=$val->status ==1?'':'<a class="btn green btn-xs btn-default" href="'.$this->createUrl(['withdraw/index','order_id'=>$val->m_order_id]).'"><i class="fa fa-eye"></i> 查看提现明细 </a>';
            $p = number_format($val->price * $val->expenses / 100, 2);
            $status = !$val->status ? 'green' : '';
            $data['data'][] = array(
                '<input type="checkbox" class="settle_checkbox" name="id[]" value="' . $val->id . '">',
                $val->id,
                Seller::getSellerInfo($val->seller_id)->seller_name,
                '<a href="'.$this->createUrl(['ordermainten/view','id'=>$val->m_order_id]).'" >' . $val->m_order_id . '</a>',
                $val->price,
                $val->expenses . ' % ( ￥' . $p . ' )',
                '￥' . ($val->price - $p),
                $val->settle_time ? date('Y-m-d', $val->settle_time) : '',
                '<label class="btn btn-xs ' . $status . ' " >' . SellerSettle::getStatusMsg($val->status) . '</label>',
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
        $mark=Yii::$app->request->post('remark','结算成功,款项已打至商家账户');
        if (!$id) {
            return $this->getCheckNo('参数错误');
        }
        $model = new SellerSettle();
        if ($model->updateAll(['status' => 2, 'finsh_time' => time()], 'id IN ('.$id.') AND status = :status', [ ':status' => SellerSettle::SETTLE_LOAD])) {
            $model ->setLog($id,$mark,Yii::$app->user->identity->username);
            return $this->getCheckYes([], '结算打款成功');
        }
        return $this->getCheckNo('结算打款失败,请稍后重试...');
    }

    private function _condition($condition = array())
    {
        $model = SellerSettle::find();
        $get = Yii::$app->request->get();
        $post = Yii::$app->request->post();
        $tj = array_merge($get, $post);
        $where = [];
        if (isset($tj['name']) && !empty($tj['name'])) {
            $seller_model = Seller::find()->select(['seller_id'])->where(['like', 'seller_name', $tj['name']])->all();
            $seller_id = [];
            foreach ($seller_model as $val) {
                $seller_id[] = $val->seller_id;
            }
            $model->where(['in', 'seller_id', $seller_id]);
            $model->orWhere(['m_order_id' => $tj['name']]);
        }
        if (isset($tj['status']) && (!empty($tj['status']) || $tj['status'] === '0')) {
            $where['status'] = $tj['status'];
        }
        $condition = array_merge($where, $condition);
        $model->andWhere($condition);
        return $model;
    }
}