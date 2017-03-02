<?php

namespace maintainer\controllers;

use common\library\helper;
use common\models\CardCouponsGrant;
use common\models\CardCouponsLog;
use common\models\CardGrantRelation;
use common\models\CardOrderItem;
use common\models\CardOrderItemLog;
use common\models\CardOrderPayback;
use common\models\CardRefund;
use common\models\InsuranceCoverage;
use common\models\Seller;
use m35\thecsv\theCsv;
use maintainer\components\LoginedController;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 *
 */
class CardController extends LoginedController
{
    public function actionIndex()
    {
        if (Yii::$app->request->isAjax) {

            $respon = array();
            $query = CardOrderItem::find()->from(['a' => CardOrderItem::tableName()]);
            $query->leftJoin(['b' => CardOrderPayback::tableName()], 'a.pay_sn=b.pay_sn');
            $query->leftJoin(['c' => InsuranceCoverage::tableName()], 'a.coverage_code=c.coverage_code');
            $query->andWhere(['b.to_seller_id' => $this->seller->seller_id]);

            $pageSize = Yii::$app->request->post('length', 10);
            $start = Yii::$app->request->post('start', 0);//偏移量

            $status = trim(Yii::$app->request->post('status', ''));
            if ($status !== '') {
                $query->andWhere(['a.status' => intval($status)]);
            }
            if ($coverage_code = Yii::$app->request->post('coverage_code', '')) {
                $query->andWhere(['a.coverage_code' => $coverage_code]);
            }
            if ($pay_sn = Yii::$app->request->post('pay_sn', '')) {
                $query->andWhere(['a.pay_sn' => $pay_sn]);
            }
            $apply_type = trim(Yii::$app->request->post('apply_type', ''));
            if ($apply_type !== '') {
                $query->andWhere(['b.apply_type' => intval($apply_type)]);
            }
            $pay_status = trim(Yii::$app->request->post('pay_status', ''));
            if ($pay_status !== '') {
                $query->andWhere(['b.pay_status' => intval($pay_status)]);
            }

            $total = $query->count('a.order_id');
            $data = $query->select('a.*,b.apply_type,b.pay_status,b.handle_type,c.company_name,c.type_name,c.coverage_name')->orderBy('a.order_id DESC')->limit($pageSize)->offset($start)->asArray()->all();

            if ($data) {
                foreach ($data as $item) {
                    $btn = '<a class="btn green btn-xs  btn-default" data-target="#my-card-apply" data-toggle="modal"  href="' . $this->createUrl(['card/info', 'pay_sn' => $item['pay_sn']]) . '"><i class="fa fa-share"></i> 查看详细</a>';
                    $bstop = true;
                    $refund=CardRefund::find()->where(['pay_sn'=>$item['pay_sn'],'coverage_code'=>$item['coverage_code']])->andWhere(['<','status',3])->asArray()->all();

                    if($refund){
                        $r_card = $is_card =[];
                        foreach ($refund as $v){
                            $r_card += explode(',',$v['card_numbers']);
                            $is_card += explode(',',$v['issue_card_numbers']);
                        }

                        $read_card = array_diff($r_card,$is_card);

                        if($item['number'] <= count($read_card)){
                            $bstop =false;
                        }
                    }
                    if ($item['status'] == CardOrderItem::_CD_STATE_SUCCESS && $bstop ) {
                        $btn .= '<a href="' . $this->createUrl(['refund/applf', 'order_id' => $item['order_id']]) . '" class="btn btn-xs btn-outline blue">申请退卡</a>';
                    }
                    $respon[] = [
                        $item['order_id'],
                        $item['company_name'] . ' ' . $item['type_name'] . ' ' . $item['coverage_name'],
                        $item['coverage_code'],
                        $item['number'],
                        $item['price'],
                        $item['pay_sn'],
                        !$item['handle_type']?'':CardOrderPayback::getMsg($item['apply_type']),
                        CardOrderPayback::getTypeMsg($item['pay_status']),
                        CardOrderItem::itemStateData()[$item['status']],
                        $btn
                    ];
                }
            }

            return json_encode(array('data' => $respon, 'recordsTotal' => $total, 'recordsFiltered' => $total));
        }

        $this->render('index');
    }


    public function actionInfo()
    {
        if (!Yii::$app->request->isAjax) {
            $this->showMessage('非法请求，请联系管理员', '', self::__MSG_DANGER);
        }
        $model = CardOrderPayback::findOne(['pay_sn' => trim($_REQUEST['pay_sn'])]);
        if (!$model) {
            $this->showMessage('查无批次记录，请联系管理员', '', self::__MSG_DANGER);
        }
        return $this->renderPartial('_card_view', ['model' => $model]);
    }


    public function actionApplf()
    {
        $seller_info = $this->seller;
        $seller_id = $seller_info->seller_id;
        $p_seller_id = $seller_info->pid;
        $is_two = $p_seller_id ? true : false;
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('coverage');
            $total_price = 0;
            $total_num = 0;
            $sql = 'INSERT INTO ' . CardOrderItem::tableName() . ' (`pay_sn`,`coverage_code`,`number`,`price`,`add_time`) values  ';
            $bind_val = [];
            foreach ($data as $k => $val) {
                if (!$val['code']) {
                    unset($data[$k]);
                    continue;
                }
                $num = intval(trim($val['num']));
                if($num < 1 ){
                    continue;
                }
                $t_to = $num * $val['price'];
                $total_price += $t_to;
                $total_num += $num;
                // 组装card_item插入数据
                $key_code = ':coverage_code_' . $k;
                $key_num = ':number_' . $k;
                $key_price = ':price_' . $k;
                $key_time = ':time_' . $k;
                $sql .= '( :pay_sn, ' . $key_code . ' , ' . $key_num . ' , ' . $key_price . ', ' . $key_time . '),';
                $bind_val[$key_code] = $val['code'];
                $bind_val[$key_num] = $val['num'];
                $bind_val[$key_price] = $val['price'];
                $bind_val[$key_time] = time();
            }

            if(!$bind_val){
                return $this->getCheckNo('请输入申请数量');
            }
            $model = new CardOrderPayback();
            $transaction = $model->getDb()->beginTransaction();
            $model->add_time = time();
            $model->total_price = $total_price;
            $model->to_seller_id = $seller_id;
            $model->from_seller_id = $p_seller_id ? $p_seller_id : 1;
            $model->handle_type = 1;
            $model->apply_type = intval($_REQUEST['apply_type']);
            $model->pay_sn = helper::_makeOrderSn(Yii::$app->user->identity->id);
            $model->num = $total_num;
            try {

                if ($model->save()) {
                    $sql = rtrim($sql, ',');
                    $bind_val[':pay_sn'] = $model->pay_sn;
                    $ret = Yii::$app->getDb()->createCommand($sql, $bind_val)->execute();
                    if ($ret) {
                        $transaction->commit();
                        return $this->getCheckYes([], '申请成功！');
                    }
                }
                throw new \yii\base\Exception("操作失败");
            } catch (\yii\base\Exception $e) {
                $transaction->rollBack();
                return $this->getCheckNo('卡券申请失败！请重试。。。');
            }
        }
        $pid = $p_seller_id ? $p_seller_id : 1;
        $result = $this->_getCard($pid);
        Yii::$app->params['_menu'] = '_index';
        $this->render('applf', ['result' => $result, 'is_two' => $is_two]);
    }


    /**
     * 卡券发放列表
     * @return string
     */
    public function actionIssue()
    {
        if (Yii::$app->request->isAjax) {

            $respon = array();
            $query = CardOrderItem::find()->from(['a' => CardOrderItem::tableName()]);
            $query->leftJoin(['b' => CardOrderPayback::tableName()], 'a.pay_sn=b.pay_sn');
            $query->leftJoin(['c' => InsuranceCoverage::tableName()], 'a.coverage_code=c.coverage_code');
            $query->andWhere(['b.from_seller_id' => $this->seller->seller_id]);

            $pageSize = Yii::$app->request->post('length', 10);
            $start = Yii::$app->request->post('start', 0);//偏移量

            $status = trim(Yii::$app->request->post('status', ''));
            if ($status !== '') {
                $query->andWhere(['a.status' => intval($status)]);
            }
            if ($coverage_code = Yii::$app->request->post('coverage_code', '')) {
                $query->andWhere(['a.coverage_code' => $coverage_code]);
            }
            if ($pay_sn = Yii::$app->request->post('pay_sn', '')) {
                $query->andWhere(['a.pay_sn' => $pay_sn]);
            }
			if ($seller_id = Yii::$app->request->post('seller_id', '0')) {
				$query->andWhere(['b.to_seller_id' => $seller_id]);
			}
            $apply_type = trim(Yii::$app->request->post('apply_type', ''));
            if ($apply_type !== '') {
                $query->andWhere(['b.apply_type' => intval($apply_type)]);
            }
            $pay_status = trim(Yii::$app->request->post('pay_status', ''));
            if ($pay_status !== '') {
                $query->andWhere(['b.pay_status' => intval($pay_status)]);
            }

            $total = $query->count('a.order_id');
            $data = $query->select('a.*,b.apply_type,b.pay_status,b.handle_type,b.to_seller_id,c.company_name,c.type_name,c.coverage_name')->orderBy('a.order_id DESC')->limit($pageSize)->offset($start)->asArray()->all();

            if ($data) {
                foreach ($data as $item) {
                    $t_seller_name = Seller::getSellerInfo($item['to_seller_id'])->seller_name;
                    $btn = '<a class="btn green btn-xs  btn-default" data-target="#my-card-apply" data-toggle="modal"  href="' . $this->createUrl(['card/info', 'pay_sn' => $item['pay_sn']]) . '"><i class="fa fa-share"></i> 查看详细</a>';
                    //没有 确认和发放处理的可以发放和取消操作
                    if ($item['status'] == CardOrderItem::_CD_STATE_TO_DO || $item['status'] == CardOrderItem::_CD_STATE_TO_WAIT) {
                        $btn .= '<a class="btn red btn-xs btn-default apply_cancel" data-content="您确定要取消 [' . $t_seller_name . '] 的险种 [' . $item['coverage_code'] . '] 发放？" rel="' . $this->createUrl(['card/cancel', 'order_id' => $item['order_id']]) . '" href="javascript:;"><i class="fa fa-trash-o"></i> 取消 </a>';
                        $btn .= '<a class="btn default btn-default btn-xs" data-target="#my-card-apply" data-toggle="modal"    href="' . $this->createUrl(['card/issuemod', 'order_id' => $item['order_id']]) . '"> 发放 </a>';
                    }
                    $respon[] = [
                        $t_seller_name,
                        $item['company_name'] . ' ' . $item['type_name'] . ' ' . $item['coverage_name'],
                        $item['coverage_code'],
                        $item['number'],
                        $item['price'],
                        $item['pay_sn'],
//                        !$item['handle_type']?'':CardOrderPayback::getMsg($item['apply_type']),
//                        CardOrderPayback::getTypeMsg($item['pay_status']),
                        CardOrderItem::itemStateData()[$item['status']],
                        $btn
                    ];
                }
            }

            return json_encode(array('data' => $respon, 'recordsTotal' => $total, 'recordsFiltered' => $total));
        }
		$d = [$this->seller->seller_id => $this->seller->seller_name];
        if(!$this->seller->isRankTwo){
			$seller_data = Seller::find()->where(['pid' => $this->seller->seller_id])->select('seller_id,seller_name')->asArray()->all();
			if ($seller_data) {
				$d = ArrayHelper::map($seller_data, 'seller_id', 'seller_name');
			}
		}

        return $this->render('issue',['coverage_data' => InsuranceCoverage::getCoverageDataCodeAll(),'seller_data' => $d]);
    }


    /**
     * 被动发放卡券   1级与2级商家之间
     * @return array
     */
    public function actionIssuemod()
    {
        if (Yii::$app->request->isPost) {
            /**
             * [card_number_str] => fgh
             * [service_note] => fh
             * [order_id] => 59
             * [_csrf-maintainer] => T2VIY1NkLjcoCQ0sAytXfSgyBSIHPHxCJhUqO2MBF0Y7PXsEEFxpfQ==
             */
            set_time_limit(0);
            $order = CardOrderItem::findOne(['order_id' => intval($_REQUEST['order_id'])]);
            if (!$order) {
                return $this->getCheckNo('查无申领记录');
            }
            $card_pay = CardOrderPayback::findOne(['pay_sn' => $order->pay_sn, 'from_seller_id' => $this->seller->seller_id]);
            if (!$card_pay) {
                return $this->getCheckNo('查无申领记录.');
            }

            //check card
            $cards = explode(',', trim($_REQUEST['card_number_str']));
            if (count($cards) != $order->number) {
                return $this->getCheckNo('申领卡券数量与发放数据不符合.');
            }
            $service_note = trim($_REQUEST['service_note']);

            $card_data = CardCouponsGrant::find()->where(['seller_id' => $card_pay['from_seller_id'], 'status' => [CardCouponsGrant::__STATUS_DEFAULT, CardCouponsGrant::__STATUS_FROZE], 'coverage_code' => $order['coverage_code'], 'card_number' => $cards])->asArray()->all();
            if (count($card_data) != count($cards)) {
                return $this->getCheckNo('商家可发放卡券数量不足');
            }


            if (!CardGrantRelation::checkIssue($cards, $card_pay['from_seller_id'])) {
                return $this->getCheckNo('当前发放的卡券之中包含已经发放过的卡券，请修改后重新发放！');
            }
            $time = time();
            $insert_data = array();
            foreach ($card_data as $card) {
                $insert_data[] = [
                    $card['id'],
                    $card['card_number'],
                    $order['order_id'],
                    $order['pay_sn'],
                    $card_pay['from_seller_id'],
                    $card_pay['to_seller_id'],
                    $time
                ];
            }
            $transtion = Yii::$app->db->beginTransaction();
            try {
                //添加关系
                $f1 = Yii::$app->db->createCommand()->batchInsert(CardGrantRelation::tableName(), ['card_id', 'card_number', 'order_id', 'pay_sn', 'from_seller_id', 'to_seller_id', 'add_time'], $insert_data)->execute();
                //添加卡券日志
                $c_log = [
                    'hand_type' => CardCouponsLog::__TYPE_GRANT,
                    'from_seller_id' => $card_pay['from_seller_id'],
                    'to_seller_id' => $card_pay['to_seller_id'],
                    'message' => '发放险种(' . $order['coverage_code'] . ') (' . $order['number'] . ')从[' . $card_pay['from_seller_id'] . '] 到商家[' . $card_pay['to_seller_id'] . ']',
                    'created' => date('Y-m-d H:i:s', $time)
                ];
                CardCouponsLog::addLog($c_log);
                $order->status = CardOrderItem::_CD_STATE_SUCCESS;
                $order->add_time = $time;
                $f2 = $order->update(false, ['status', 'add_time']);
                $card_pay->send_total_price += $order->price * $order->number;
                $card_pay->add_time = $time;
                $f3 = $card_pay->update(false, ['send_total_price', 'add_time']);

                CardOrderItemLog::addLog(['order_id' => $order->order_id, 'content' => $service_note]);
                if ($f1 && $f2 && $f3) {
                    $transtion->commit();
                    return $this->getCheckYes('卡券发放成功！');
                } else {
                    $transtion->rollBack();
                    return $this->getCheckNo('卡券发放失败');
                }

            } catch (Exception $e) {
                $transtion->rollBack();
                return $this->getCheckNo('卡券发放异常#' . $e->getMessage());
            }
        }

        //check order
        $order = CardOrderItem::findOne(['order_id' => intval($_REQUEST['order_id'])]);
        if (!$order) {
            $this->showMessage('查无申领险种记录', '', self::__MSG_DANGER);
        }
        //check pay
        $card_pay = CardOrderPayback::findOne(['pay_sn' => $order->pay_sn, 'from_seller_id' => $this->seller->seller_id]);
        if (!$card_pay) {
            $this->showMessage('查无申领险种记录', '', self::__MSG_DANGER);
        }
        $seller = Seller::findOne(['seller_id' => $card_pay['to_seller_id']]);

        return $this->renderPartial('_card_send', ['order' => $order, 'card_pay' => $card_pay, 'seller' => $seller]);

    }

    /**
     * 取消险种发放
     * @return array
     */
    public function actionCancel()
    {
        if (!Yii::$app->request->isAjax) {
            return $this->getCheckNo('非法请求');
        }

        //check order
        $order = CardOrderItem::findOne(['order_id' => intval($_REQUEST['order_id'])]);
        if (!$order) {
            return $this->getCheckNo('查无申领记录');
        }
        //check pay

        $card_pay = CardOrderPayback::findOne(['pay_sn' => $order->pay_sn, 'from_seller_id' => $this->seller->seller_id]);

        if (!$card_pay) {
            return $this->getCheckNo('查无申领险种记录');
        }
        if ($order->cancelApply($card_pay)) {
            return $this->getCheckYes([], '取消成功');
        }
        return $this->getCheckNo('取消失败');
    }


    /**
     * 主动发放卡券
     * @param array $condition
     * @return \yii\db\ActiveQuery
     */
    public function actionAccrod()
    {
        $seller_info = $this->seller;
        $seller_id = $seller_info->seller_id;
        $insurance_list = Seller::find()->where(['pid' => $seller_id, 'status' => 1])->all();
        $list = CardCouponsGrant::getCoverageCodeList($seller_id);
        Yii::$app->params['_menu'] = '_issue';
        return $this->render('accrod', [
            'insurance_list' => $insurance_list,
            'code_list' => $list
        ]);
    }

    /**
     * 主动发放卡券
     * @return array
     * @throws Exception
     */
    public function actionGrant()
    {
        if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
            $this->showMessage('非法访问');
        }
        $post = Yii::$app->request->post();
        if (!$post['to_seller_id'] || !$post['d_coverage'] || !$post['card_number_str'] || !$post['card_num']) {
            return $this->getCheckNo(' 参数错误');
        }
        $transaction = Yii::$app->getDb()->beginTransaction();
        $coverage = InsuranceCoverage::findOne(['coverage_code' => $post['d_coverage']]);
        if (!$coverage) return $this->getCheckNo('当前险种不存在！');
        $seller_info = Yii::$app->user->identity->getSellerInfo();
        $seller_id = $seller_info->seller_id;
        try {
            $data = [
                'apply_type' => 3,
                'from_seller_id' => $seller_id,
                'to_seller_id' => $post['to_seller_id'],
                'num' => $post['card_num'],
                'total_price' => floatval($coverage->wholesale_price * $post['card_num']),
                'coverage_code' => $post['d_coverage'],
                'price' => $coverage->wholesale_price
            ];
            set_time_limit(0);
            //生成卡号 支持以逗号分割单个与 | 分割连续的卡号
            $cards = helper::creadCard($post['card_number_str']);
            if (!$cards) {
                throw new Exception('卡券号输入错误');
            }
            $b = CardOrderPayback::create($data,Yii::$app->user->identity->id);
            if (!$b) {
                throw new Exception('订单创建失败！');
            }
            $t = $post['card_deadline'] ? $post['card_deadline'] : 0;
            if (!CardGrantRelation::checkIssue($cards, $seller_id)) {
                throw new Exception('当前发放的卡券之中包含已经发放过的卡券，请修改后重新发放！');
            }
            //验证卡券号是否与申请数相等
            $check_number = CardOrderItem::checkNumber($b, $post['d_coverage'], $cards);
            if (!$check_number['status']) {
                throw new Exception($check_number['msg']);
            }
            $check_number['data']['content'] = $post['card_remark'];
            $check_number['data']['t'] = $t;
            $tj = ['seller_id' => $seller_id, 'coverage_code' => $post['d_coverage'], 'status' => 0, 'card_number' => $cards];
            $model_create = new CardGrantRelation();
            $list = CardCouponsGrant::find()->where($tj)->asArray()->all();
            $bstop = $model_create->createCard($list, $check_number['data']);
            if ($bstop) {
                $transaction->commit();
                return $this->getCheckYes([], '卡券发放成功！');
            }
            throw new Exception('卡券发放失败');
        } catch (Exception $e) {
            $transaction->rollBack();
            return $this->getCheckNo($e->getMessage());
        }
    }

    /**
     * AJAX获取商家列表
     * seller_name 商家名称
     * type 商家类型
     */
    public function actionGetseller()
    {
        $seller_info = Yii::$app->user->identity->getSellerInfo();
        $seller_id = $seller_info->seller_id;
        $seller_name = Yii::$app->request->post('seller_name');
        $map = [
            'status' => 1,
            'pid' => $seller_id
        ];
        $model = Seller::find()->where($map);
        if ($seller_name) {
            $model->andWhere(['like', 'seller_name', $seller_name]);
        }
        $list = $model->asArray()->all();
        return $this->getCheckYes($list);
    }


    /**
     * 我的卡券
     */
    public function actionMe()
    {
        $seller_info = Yii::$app->user->identity->getSellerInfo();
        $seller_id = $seller_info->pid;
        $view = $seller_id ? 'child' : 'me';
        $this->render($view);
    }

    /**
     * 一级商家我的卡券
     */
    public function actionGetme()
    {
    	$isRankTwo = $this->seller->getIsRankTwo();
        $pageSize = Yii::$app->request->post('length', 10);
        $start = Yii::$app->request->post('start', 0);//偏移量
        $model = $this->_meCondition();
        $count = $model->count('*');
        $dataProvider = new ActiveDataProvider([
            'query' => $model->limit($pageSize)->offset($start)->asArray(),
            'pagination' => [
                'pageSize' => $pageSize,
                'page' => intval($start / $pageSize),
                'totalCount' => $count
            ]
        ]);
        $data = [
            'draw' => intval($_REQUEST['draw']),
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => []
        ];
        $result = $dataProvider->getModels();
        $seller_info = Yii::$app->user->identity->getSellerInfo();
        $seller_id = $seller_info->seller_id;
        $to_seller_ids = array_column($result, 'to_seller_id');
        $seller = Seller::getIdKeySeller(['seller_id' => $to_seller_ids]);
        foreach ($result as $val) {
            if ($val['to_seller_id'] != $seller_id) {
                $s = '<span class="font-red-thunderbird"> 卖出 至 【 ' . $seller[$val['to_seller_id']]['seller_name'] . ' 】 </span>';
            } else {
                $s = '<span class="font-blue-chambray">购入</span>';
            }

            if($isRankTwo){
				$data['data'][] = [
					$val['id'],
					$val['card_number'],
					$val['coverage_code'],
					$val['order_id'],
					'<span class="' . (!$val['status'] ? 'font-purple-seance' : 'font-grey-mint') . '">' . CardCouponsGrant::statusData()[$val['status']] . '</span>',
					$s,
					date('Y-m-d H:i:s', $val['add_time'])
				];
			}else{
				$data['data'][] = [
					Html::checkbox('check_id[' . $val['id'] . ']'),
					$val['id'],
					$val['card_number'],
					$val['coverage_code'],
					$val['order_id'],
					'<span class="' . (!$val['status'] ? 'font-purple-seance' : 'font-grey-mint') . '">' . CardCouponsGrant::statusData()[$val['status']] . '</span>',
					$s,
					date('Y-m-d H:i:s', $val['add_time'])
				];

			}
        }
        echo json_encode($data);
        die();
    }

    /**
     * 二级商家我的卡券
     */
    public function actionGetchild()
    {
        $pageSize = Yii::$app->request->post('length', 10);
        $start = Yii::$app->request->post('start', 0);//偏移量
        $model = $this->_meCondition();
        $count = $model->count('*');
        $dataProvider = new ActiveDataProvider([
            'query' => $model->limit($pageSize)->offset($start)->asArray(),
            'pagination' => [
                'pageSize' => $pageSize,
                'page' => intval($start / $pageSize),
                'totalCount' => $count
            ]
        ]);
        $data = [
            'draw' => intval($_REQUEST['draw']),
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => []
        ];
        $result = $dataProvider->getModels();
        $seller_info = Yii::$app->user->identity->getSellerInfo();
        $seller_id = $seller_info->pid ? $seller_info->pid : 1;
        $seller = Seller::getSellerInfo($seller_id);
        foreach ($result as $val) {
            if ($val['to_seller_id'] != $seller_info->seller_id) {
                $s = '<span class="font-red-thunderbird"> 退卡 至 【 ' . $seller->seller_name . ' 】 </span>';
            } else {
                $s = '<span class="font-blue-chambray">购入</span>';
            }
            $data['data'][] = [
                $val['id'],
                $val['card_number'],
                $val['coverage_code'],
                $val['order_id'],
                '<span class="' . (!$val['status'] ? 'font-purple-seance' : 'font-grey-mint') . '">' . CardCouponsGrant::statusData()[$val['status']] . '</span>',
                $s,
                date('Y-m-d H:i:s', $val['add_time'])
            ];
        }
        echo json_encode($data);
        die();
    }

    public function actionDownload(){
        $model = $this->_meCondition();
        set_time_limit(0);
        $data=$model->limit(3000)->asArray()->all();
        $temp=[];
        $seller_info = Yii::$app->user->identity->getSellerInfo();
        $seller_id = $seller_info->seller_id;
        $to_seller_ids = array_column($data, 'to_seller_id');
        $seller = Seller::getIdKeySeller(['seller_id' => $to_seller_ids]);
        foreach($data as $k=>$val){
            if ($val['to_seller_id'] != $seller_id) {
                $s = '卖出至【 ' . $seller[$val['to_seller_id']]['seller_name'] . ' 】';
            } else {
                $s = '购入';
            }
            $sn='';
            if(!$val['pay_sn']){
                $r=CardGrantRelation::find()->where(['to_seller_id'=>$seller_id])->andWhere('order_id > 0')->orderBy('id desc')->one();
                if($r && $r->pay_sn){
                    $sn=$r->pay_sn;
                }
            }
            $temp[$k]=[
                '="'.$val['card_number'].'"',
                $val['coverage_code'],
                '="'.($val['pay_sn']?$val['pay_sn']:$sn).'"',
                CardCouponsGrant::statusData()[$val['status']],
                $s,
                date('Y-m-d', $val['add_time'])
            ];
        }
        theCsv::export([
            'data' => $temp,
            'name' => "card_list_".date('Y_m_d_H', time()).".csv",    // 自定义导出文件名称
            'header' => ['卡券号','险种','批次号','卡券状态','卡券流转','时间'],
        ]);
    }

    public function actionCdownload(){
        $model = $this->_meCondition();
        set_time_limit(0);
        $data=$model->limit(3000)->asArray()->all();
        $temp=[];
        $seller_info = Yii::$app->user->identity->getSellerInfo();
        foreach($data as $k=>$val){
            if ($val['to_seller_id'] != $seller_info->seller_id) {
                continue;
            }
            $temp[$k]=[
                '="'.$val['card_number'].'"',
                $val['coverage_code'],
                '="'.$val['pay_sn'].'"',
                CardCouponsGrant::statusData()[$val['status']],
                date('Y-m-d', $val['add_time'])
            ];
        }
        theCsv::export([
            'data' => $temp,
            'name' => "card_list_".date('Y_m_d_H', time()).".csv",    // 自定义导出文件名称
            'header' => ['卡券号','险种','批次号','卡券状态','时间'],
        ]);
    }

    /**
     * 商家我的卡券条件
     * @return $this
     */
    private function _meCondition($where=[])
    {
        $seller_info = Yii::$app->user->identity->getSellerInfo();
        $seller_id = $seller_info->seller_id;
        $pid = $seller_info->pid ? $seller_info->pid : $seller_id;
        $model = CardGrantRelation::find();
        $tb = CardGrantRelation::tableName();
        $card_tb = CardCouponsGrant::tableName();
        $query = new Query();
        $get =Yii::$app->request->get();
        $post = Yii::$app->request->post();
        $post=array_merge($get,$post);
        $search = [];
        $filter = $query->select('*')->from(['u' => $tb])->where($where);
        if (isset($post['type']) && $post['type'] && $post['keyword'] !== '') {
            $post['keyword'] =trim($post['keyword']);
            if ($post['type'] == 1) {
                $child = Seller::find()->select('seller_id')->where(['like', 'seller_name', $post['keyword']])->asArray()->all();
                if ($child) {
                    $to_seller_id = array_column($child, 'seller_id');
                    $tj['to_seller_id'] = $to_seller_id;
                } else {
                    $tj['to_seller_id'] = -1;
                }
                $filter->where(['from_seller_id' => $seller_id])->andWhere($tj);
            } else if ($post['type'] == 2) {
                $search[$card_tb . '.coverage_code'] = $post['keyword'];
            }else if($post['type'] == 3){
                $search[$card_tb . '.card_number'] = $post['keyword'];
            }
        } else {
            $filter->where(['or', 'from_seller_id = ' . $seller_id, 'to_seller_id = ' . $seller_id]);
        }
        if (isset($post['status']) && $post['status'] !== '') {
            $search[$card_tb . '.status'] = intval($post['status']);
        }
		if ($coverage_code = trim(Yii::$app->request->post('coverage_code', ''))) {
			$search[$card_tb . '.coverage_code'] =$coverage_code;
		}
        $filter->orderBy('id desc');
        $field = $card_tb . '.status ,' . $card_tb . '.card_secret,' . $card_tb . '.coverage_code';
        $model->select('c.*,' . $field)->from(['c' => $filter])->innerJoin($card_tb, 'c.card_id = ' . $card_tb . '.id');
        $model->where([$card_tb . '.seller_id' => $pid]);
        $model->andWhere($search)->groupBy('c.card_id')->orderBy('c.id desc,c.from_seller_id asc,' . $card_tb . '.status asc,c.id desc,c.add_time desc');
        return $model;
    }


    /**
     * 卡券发放条件
     * @param array $condition
     * @return \yii\db\ActiveQuery
     */
    private function _issueCondition($condition = [])
    {
        $model = CardOrderPayback::find();
        $seller_info = Yii::$app->user->identity->getSellerInfo();
        $seller_id = $seller_info->seller_id;
        $where = ['from_seller_id' => $seller_id];
        $post = Yii::$app->request->post();
        if (isset($post['status']) && $post['status'] !== '') {
            $where['pay_status'] = (int)$post['status'];
        }
        if (isset($post['keyword']) && $post['keyword']) {
            $child = Seller::find()->select('seller_id')->where(['like', 'seller_name', $post['keyword']])->asArray()->all();
            if ($child) {
                $to_seller_id = array_column($child, 'seller_id');
                $where['to_seller_id'] = $to_seller_id;
            } else {
                $where['to_seller_id'] = -1;
            }
        }
        $condition = array_merge($where, $condition);
        $model->where($condition);
        return $model;
    }

    /**
     * 获取一级商家卡券详细信息
     * @param int $seller_id
     * @return array
     */
    private function _getCard($seller_id = 1)
    {
        $model = new Query();
        $t_b_card = CardCouponsGrant::tableName();
        $t_b_cover = InsuranceCoverage::tableName();
        $field = 'cover.*,count(card.coverage_code) as c';
        $result = $model->from(['card' => $t_b_card, 'cover' => $t_b_cover])->select($field)
            ->where('card.coverage_code = cover.coverage_code and card.seller_id = :seller_id and card.status = :status and cover.status = :c_status')
            ->addParams([':seller_id' => $seller_id, ':status' => 0, ':c_status' => 1])
            ->groupBy('cover.coverage_code')->orderBy('card.coverage_id DESC')->all();
        return $result;
    }


	/**
	 * 选择发放卡券处理
	 */
	public function actionSelsendcard()
	{

		return $this->renderPartial('_card_select_send', []);

	}



}
