<?php
namespace backend\controllers;
use common\library\helper;
use common\models\CardCouponsGrant;
use common\models\CardOrderPayback;
use common\models\CardRefund;
use common\models\CardRefundLog;
use common\models\Seller;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use backend\components\LoginedController;
use yii\web\NotFoundHttpException;


/**
 * CardrefundController implements the CRUD actions for OrderMaintenance model.
 */
class CardrefundController extends LoginedController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
		return [];
    }

    /**
     * Lists all Role models.
     * @return mixed
     */
	public function actionIndex()
	{
		if (Yii::$app->request->isAjax) {

				$query = CardRefund::find();
				$query->andWhere(['to_seller_id'=>Seller::$lehuanxin]);
				$respon = array();
				$pageSize = Yii::$app->request->post('length', 10);
				$start = Yii::$app->request->post('start', 0);//偏移量

				$status = trim(Yii::$app->request->post('status', ''));
				if ($status!=='') {
					$query->andWhere(['status'=>intval($status)]);
				}

				if ($add_time_from = Yii::$app->request->post('add_time_from', '')) {
					$query->andFilterCompare('add_time', strtotime($add_time_from), '>=');
				}
				if ($add_time_to = Yii::$app->request->post('add_time_to', '')) {
					$add_time_to = $add_time_to . " 23:59:59";
					$query->andFilterCompare('add_time', strtotime($add_time_to), '<=');
				}
				$total = $query->count('id');
				$dataProvider = new ActiveDataProvider([
					'query' => $query->orderBy('id DESC')->limit($pageSize)->offset($start),
					'pagination' => [
						'pageSize' => $pageSize,
						'page' => intval($start / $pageSize),
						'totalCount' => $total
					],
				]);
				if ($data = $dataProvider->models) {
					foreach ($data as $item) {
                        $seller_name=Seller::getSellerInfo($item->from_seller_id)->seller_name;
						$btn = '<a class="btn green btn-xs  btn-default"  href="' . $this->createUrl(['cardrefund/view', 'id' => $item['id']]) . '"><i class="fa fa-share"></i> 查看详细</a>';
						$respon[] = [
							$item->formatId,
                            $seller_name,
							$item->number,
							$item->total_price,
							$item->getStatusText(),
							$item->add_time?date('Y-m-d H:i:s',$item->add_time):'',
							$btn
						];
					}
				}
				return json_encode(array('data' => $respon, 'recordsTotal' => $total, 'recordsFiltered' => $total));
			}

		return $this->render('index');
	}

    /**
     * Displays a single Role model.
     * @param integer $id
     * @return mixed
     */
	public function actionView($id)
	{
		$model = $this->findModel($id);
        $pay_info = CardOrderPayback::findOne(['pay_sn'=>$model->pay_sn]);
		return $this->render('view', ['model' => $model,'pay_info'=>$pay_info]);
	}

    /**
     * Finds the Role model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Role the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CardRefund::findOne($id)) !== null) {
            return $model;
        } else {
			if(Yii::$app->request->isAjax){
				return $this->getCheckNo('查无维保记录');
			}
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	/**
	 * 处理 退回请求处理
	 * eg:
	 * Array
	 * (
	 * [_csrf-backend] => Z2FqOVJKZ29SCQhSDRIAWipQBHwTGz8XDhBeWCcSIQIMCQ9DOh0uJQ==
	 * [status] => yes
	 * [note] => yes
	 * [refund_id] => 1
	 */
	public function actionChangestate()
	{
		if(!Yii::$app->request->isAjax){
			return $this->getCheckNo('非法请求');
		}
		$model = $this->findModel(Yii::$app->request->post('refund_id',0));
        $err_cards =Yii::$app->request->post('err_card','');

		$note = trim($_POST['note']);
		if(empty($note)){
			return $this->getCheckNo('备注不能为空');
		}
		$status = strtolower(trim($_POST['status']));
		if(!in_array($status,['yes','no'])){
			return $this->getCheckNo('非法参数数据');
		}

		/** 退回取消操作处理 **/
		if($status === 'no'){
            $tran =Yii::$app->db->beginTransaction();
            try{
                $cards=explode(',',$model->card_numbers);
                //取消处理
                $model->status = CardRefund::_RF_STATE_FAIL;
                if($model->save()){
                    if(CardCouponsGrant::changeStatus($cards,CardCouponsGrant::__STATUS_DEFAULT)){
                        $tran->commit();
                        CardRefundLog::addLog($model->id,$note);
                        return $this->getCheckYes([],'退回已取消');
                    }
                }
                throw  new Exception('退回取消失败');
            }catch (Exception $e){
                $tran->rollBack();
                return $this->getCheckNo('退回取消失败');
            }


		}

		/** 退回确认并处理业务 **/
        $card= helper::creadCard($err_cards);
        $rs = $model->cardRefund($note,$card);
		if($rs['code'] == 'no'){
			return $this->getCheckNo($rs['message']);
		}
		return $this->getCheckYes([],$rs['message']);

	}




}
