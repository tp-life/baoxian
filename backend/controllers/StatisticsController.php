<?php
/**
 * 统计
 */
namespace backend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\components\LoginedController;
use common\models\Seller;
use common\models\CardCouponsGrant;
use common\models\CardCouponsLog;
use common\models\Order;
use common\models\OrderExtend;
use common\models\BrandModel;


class StatisticsController extends LoginedController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 商户列表
     */
    public function actionSellerlist()
    {
        $filed = Yii::$app->request->post('filter', '');
        $val = Yii::$app->request->post('status', '');
        $pageSize = Yii::$app->request->post('length', 10);
        $start = Yii::$app->request->post('start', 0);//偏移量
        $user = Seller::find()->leftJoin('fj_bank as bank', 'bank.member_id = fj_seller.member_id')->select('fj_seller.*,bank.brank_name,bank.account_holder,bank.brank_account')->where(['is_insurance' => 1]);

        if ($filed) {
            $user->andWhere(['or', 'fj_seller.seller_name like \'%' . $filed . '%\'', 'fj_seller.seller_id = ' . (int)$filed]);
        }
        if ($val) {
            $user->andWhere(['fj_seller.status' => $val - 1]);
        }
        $count = $user->count('*');
        $dataProvider = new ActiveDataProvider([
            'query' => $user->limit($pageSize)->offset($start)->asArray(),
            'pagination' => [
                'pageSize' => $pageSize,
                'page' => intval($start / $pageSize),
                'totalCount' => $count
            ]
        ]);

        $member = $dataProvider->getModels();

        $data = [
            'draw' => intval($_REQUEST['draw']),
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => []
        ];
        foreach ($member as $key => $val) {

            $total_num = CardCouponsGrant::getSellerCardCouponsNum($val['seller_id']);
            $activate_num = CardCouponsGrant::getSellerCardCouponsNum($val['seller_id'],CardCouponsGrant::__STATUS_ACTIVE);
			$default_num = CardCouponsGrant::getSellerCardCouponsNum($val['seller_id'],CardCouponsGrant::__STATUS_DEFAULT);
			$fail_num = CardCouponsGrant::getSellerCardCouponsNum($val['seller_id'],CardCouponsGrant::__STATUS_FAIL);
			$froze_num = CardCouponsGrant::getSellerCardCouponsNum($val['seller_id'],CardCouponsGrant::__STATUS_FROZE);

			$lipei_num = CardCouponsGrant::countCardRelationMaintenOrder($val['seller_id']);

            $btn = '<a class="btn default btn-xs  btn-default" href="' . $this->createUrl(['statistics/cardloglist', 'seller_id' => $val['seller_id']]) . '"><i class="fa fa-share"></i> 商家日志</a>';
            $btn .= '<a class="btn green btn-xs  btn-default" href="' . $this->createUrl(['statistics/activatelist', 'seller_id' => $val['seller_id']]) . '" ><i class="fa fa-share">激活详细</i></a>';

            $data['data'][] = array(
                Html::a($val['seller_name'],['seller/view','id'=>$val['seller_id']],['target'=>'_blank','title'=>'商家信息']),
                $val['concat'].'&nbsp;['.$val['concat_tel'].']',
                $val['status'] == 1 ? '<span class="font-green-sharp">合作中</span>' : '<span class="font-red-thunderbird">已终止</span>',
				$activate_num,
				$default_num,
				$fail_num,
				$froze_num,
				$total_num,
				$lipei_num,
                $btn
            );
        }

        return json_encode($data);
    }

    /**
     * 合并日志
     */
    public function actionCardloglist()
    {
        if (Yii::$app->request->isAjax) {
            $seller_id = Yii::$app->request->post('seller_id',0);

            $pageSize = Yii::$app->request->post('length', 10);
            $start = Yii::$app->request->post('start', 0);//偏移量
            $log = CardCouponsLog::find()->where(['from_seller_id' => $seller_id]);

            $count = $log->count('*');
            $dataProvider = new ActiveDataProvider([
                'query' => $log->limit($pageSize)->offset($start)->asArray(),
                'pagination' => [
                    'pageSize' => $pageSize,
                    'page' => intval($start / $pageSize),
                    'totalCount' => $count
                ]
            ]);

            $list = $dataProvider->getModels();
            //echo '<pre/>';print_r($list);die;
            $data = [
                'draw' => intval($_REQUEST['draw']),
                'recordsTotal' => $count,
                'recordsFiltered' => $count,
                'data' => []
            ];
            foreach ($list as $key => $val) {
                //激活2  发放1 生成0  3 转出
                $hand_type_txt = '';
                switch($val['hand_type']){
                    case 0: $hand_type_txt = '生成'; break;
                    case 1: $hand_type_txt = '发放'; break;
                    case 2: $hand_type_txt = '激活'; break;
                    case 3: $hand_type_txt = '转出'; break;
                }
                $data['data'][] = array(
                    $val['id'],
                    $hand_type_txt,
                    $val['message'],
                    $val['created']
                );
            }

            return json_encode($data);
        }


        $seller_id = Yii::$app->request->get('seller_id', 0);

        if(!$seller_id){
            $this->showMessage('参数错误', '操作提示', __MSG_DANGER, Url::to(['/statistics/sellerlist']));
        }

        return $this->render('cardloglist', [
            'seller_id' => $seller_id,
        ]);
    }

    /**
     * 激活详情列表
     */
    public function actionActivatelist()
    {
        if (Yii::$app->request->isAjax) {


            $pageSize = Yii::$app->request->post('length', 10);
            $start = Yii::$app->request->post('start', 0);//偏移量

            $ccg = CardCouponsGrant::tableName();
            $oex = OrderExtend::tableName();
            $o = Order::tableName();
            $brand = BrandModel::tableName();


            $map = $this->_create_time_where();

            $card = CardCouponsGrant::find()
                ->rightJoin($o.' as o', $ccg.'.order_id = o.order_id')
                ->rightJoin($oex.' as oex', $ccg.'.order_id = oex.order_id')
                ->rightJoin($brand.' as brand', 'oex.brand_id = brand.id')
                ->select($ccg.'.*,oex.imei_code,o.member_id,o.member_name,o.member_phone,brand.model_name')
                ->where($map);


            $count = $card->count('*');
            $dataProvider = new ActiveDataProvider([
                'query' => $card->limit($pageSize)->offset($start)->asArray(),
                'pagination' => [
                    'pageSize' => $pageSize,
                    'page' => intval($start / $pageSize),
                    'totalCount' => $count
                ]
            ]);

            $list = $dataProvider->getModels();
            //echo '<pre/>';print_r($list);die;
            $data = [
                'draw' => intval($_REQUEST['draw']),
                'recordsTotal' => $count,
                'recordsFiltered' => $count,
                'data' => []
            ];

            foreach ($list as $key => $val) {
                $data['data'][] = array(
                    //'<input type="checkbox" name="id[]" value="' . $val['from_seller_id'] . '">',
                    $val['id'],
                    date('Y-m-d H:i:s',$val['active_time']),
                    $val['card_number'],
                    $val['member_name'] ? $val['member_name'] : $val['member_phone'],
                    $val['member_phone'],
                    $val['coverage_code'],
                    $val['model_name'],
                    $val['imei_code']
                );
            }

            return json_encode($data);
        }
        $seller_id = Yii::$app->request->get('seller_id', 0);

        if(!$seller_id){
            $this->showMessage('参数错误', '操作提示', __MSG_DANGER, Url::to(['/statistics/sellerlist']));
        }
        return $this->render('activatelist', [
            'seller_id' => $seller_id,
        ]);
    }

    /**
     * 组织时间搜索条件
     * 规定字段 :
     *      时间  datetime(0 1 2 3)  如果为3 同时传递 start_date  end_date
     */
    public function _create_time_where() {
        $datetime = Yii::$app->request->post('datetime',0);
        $seller_id = Yii::$app->request->post('seller_id',0);
        $s_time = Yii::$app->request->post('s_time',0);
        $e_time = Yii::$app->request->post('e_time',0);
        $ccg = CardCouponsGrant::tableName();

        $where = $ccg.'.`status`=1 and  '.$ccg.'.seller_id='.$seller_id;
        switch ($datetime) {
            case 0:
                //最近7天
                $today_start_date = strtotime("-6 day");
                $where .= " and ".$ccg.".active_time >= '$today_start_date'";
                break;
            case 1:
                //本月数据条件
                $this_month_start_date = strtotime(date("Ym").'01');
                $where .= " and ".$ccg.".active_time >= '".$this_month_start_date."'";
                break;
            case 2:
                //最近30天
                $today_start_date =strtotime("-29 day");
                $where .= " and ".$ccg.".active_time >= '$today_start_date'";
                break;
            case 3:
                //自定义时间
                if(isset($s_time) && !empty($s_time) && isset($e_time) && !empty($e_time)){
                    $s_time = trim(strtotime($s_time));
                    $e_time = trim(strtotime($e_time));
                    $where .= " and ".$ccg.".active_time between '$s_time' and '$e_time'";
                }else if(isset($s_time) && !empty($s_time)){
                    $s_time = trim(strtotime($s_time));
                    $where .= " and ".$ccg.".active_time >= '$s_time'";
                }else if(isset($e_time) && !empty($e_time)){
                    $e_time = trim(strtotime($e_time));
                    $where .= " and ".$ccg.".active_time <= '$e_time'";
                }
                break;
        }
        return $where;
    }

}
