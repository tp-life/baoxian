<?php
/**
 * 卡券生成管理
 */

namespace maintainer\controllers;

use maintainer\components\LoginedController;

use common\models\Statistics;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use common\models\Seller;
use m35\thecsv\theCsv;

/**
 * CardController implements the CRUD actions for CardCouponsGrant model.
 */
class OverallController extends LoginedController
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
            $total_card = $model->sum('card_num');
            $total_active = $model->sum('active_num');
            $total_settle = $model->sum('settle_num');
            $total_ac = $total_card ? round(100 * $total_active / $total_card, 2) : 0;
            $total_se = $total_card ? round(100 * $total_settle / $total_card, 2) : 0;
            $s_text = '';
            $dataProvider = new ActiveDataProvider([
                'query' => $model->limit($pageSize)->offset($start),
                'pagination' => [
                    'pageSize' => $pageSize,
                    'page' => intval($start / $pageSize),
                    'totalCount' => $total
                ],
            ]);

            if ($data = $dataProvider->models) {

                foreach ($data as $item) {
                    $ac = $item['card_num'] > 0 ? round(100 * $item['active_num'] / $item['card_num'], 2) : 0;
                    $se = $item['card_num'] > 0 ? round(100 * $item['settle_num'] / $item['card_num'], 2) : 0;
                    $bstop = $item['wait_num'] > Statistics::DEFI_CARD_NUM ? true : false;
                    $respon[] = $this->setRed([
                        $item['seller_name'],
                        $item['concat'] . '【' . $item['concat_tel'] . '】',
                        $item['pid'] ? '二级商家' : '一级商家',
                        $item['card_num'],
                        $item['active_num'],
                        $item['wait_num'],
                        $item['wait_num'] > Statistics::DEFI_CARD_NUM ? '充足' : '不足',
                        $item['lsoe_num'],
                        $item['frost_num'],
                        $item['settle_num'],
                        $ac > 0 ? $ac . '%' : 0,
                        $se > 0 ? $se . '%' : 0,
                    ], $bstop);
                }
                if(!$this->seller->pid) {
                    $s_text = '合计发放数： <b class="font-red-mint">' . $total_card . '</b>；激活数： <b class="font-red-mint">' . $total_active . '</b>；激活率： ' .
                        '<b class="font-red-mint"> ' . ($total_ac > 0 ? $total_ac . '%' : 0) . '</b>；合计理赔数： <b class="font-red-mint">' . $total_settle . '</b>；理赔率： ' .
                        '<b class="font-red-mint"> ' . ($total_se > 0 ? $total_se . '%' : 0) . '</b>';
                }
            }
            return json_encode(array('data' => $respon, 'recordsTotal' => $total, 'recordsFiltered' => $total, 'statistics_text' => $s_text));
        }

        return $this->render('index',['level'=>$this->seller->pid]);

    }


    public function actionExport()
    {
        $get = Yii::$app->request->get();
        $model = $this->_condition([],$get);
        $data = $model->limit(5000)->all();

        foreach ($data as $item) {
            $ac = $item['card_num'] > 0 ? round(100 * $item['active_num'] / $item['card_num'], 2) : 0;
            $se = $item['card_num'] > 0 ? round(100 * $item['settle_num'] / $item['card_num'], 2) : 0;
            $respon[] = [
                $item['seller_name'],
                $item['concat'] . '【' . $item['concat_tel'] . '】',
                $item['pid'] ? '二级商家' : '一级商家',
                $item['card_num'],
                $item['active_num'],
                $item['wait_num'],
                $item['wait_num'] > Statistics::DEFI_CARD_NUM ? '充足' : '不足',
                $item['lsoe_num'],
                $item['frost_num'],
                $item['settle_num'],
                $ac > 0 ? $ac . '%' : 0,
                $se > 0 ? $se . '%' : 0,
            ];
        }
        set_time_limit(0);
        $this->_export($respon);
    }


    private function _export($respon)
    {
        theCsv::export([
            'data' => $respon,
            'name' => "seller_salfInsurance_statistics_" . date('Y_m_d_H', time()) . ".csv",    // 自定义导出文件名称
            'header' => ['商家名称', '联系人【电话】', '商家等级',  '卡券总数', '已激活', '未激活', '库存状态', '失效', '冻结', '理赔', '激活率', '理赔率'],
            //'header' => ['卡券序列号','生成时间','险种','卡券状态','所属商家'],
        ]);
    }

    private function setRed($array_text = [], $bstop = true)
    {
        if (!is_array($array_text) || !$array_text) {
            return [];
        }
        if ($bstop) {
            return $array_text;
        }
        foreach ($array_text as &$val) {
            $val = '<span class="font-red-mint">' . $val . '</span>';
        }
        return $array_text;
    }

    private function _condition($condition = [], $request = [])
    {
        $s_tb = Seller::tableName();
        $tj_tb = Statistics::tableName();
        $query = new Query();
        $where = ['s.is_insurance' => 1];
        $subQuery = (new Query())->from($tj_tb)->orderBy('id desc');
        $query->select([
            's.seller_id', 's.seller_name', 's.concat', 's.concat_tel', 's.status', 's.pid',
            't.add_time', 't.card_num', 't.active_num', 't.settle_num', 't.lsoe_num', 't.frost_num', 't.wait_num'

        ])->from(['s' => $s_tb])->leftJoin(['t' => $subQuery], 's.seller_id=t.seller_id');
        $post = Yii::$app->request->post();
        if ($request) {
            $post = array_merge($post, $request);
        }
        $query->where(['not in', 's.seller_id', Seller::$lehuanxin]);
        if (isset($post['diff']) && in_array($post['diff'], [1, 2])) {
            if ($post['diff'] == 1) {
                $query->andWhere(['>', 't.wait_num', Statistics::DEFI_CARD_NUM]);
            } else {
                $query->andWhere(['<=', 't.wait_num', Statistics::DEFI_CARD_NUM]);
            }
        }
        if (isset($post['level']) && in_array($post['level'], [1, 2])) {
            if ($post['level'] == 1) {
                $where['s.pid'] = 0;
            } else {
                $query->andWhere(['<>', 's.pid', 0]);
            }
        }

        if (isset($post['keyword']) && $post['keyword']) {
            $query->andWhere(['or', 's.seller_name LIKE \'%' . $post['keyword'] . '%\'', 's.concat LIKE \'%' . $post['keyword'] . '%\'', 's.concat_tel LIKE \'%' . $post['keyword'] . '%\'']);
        }
        if($this->seller->pid){
            $query->andWhere(['s.seller_id'=>intval($this->seller->seller_id)]);
        }else{
            $query->andWhere(['or','s.seller_id = '.intval($this->seller->seller_id),'s.pid = '.intval($this->seller->seller_id)]);
        }
        $condition = array_merge($where, $condition);
        $query->andWhere($condition)->groupBy('s.seller_id')->orderBy('t.wait_num DESC,s.seller_id DESC');
        return $query;
    }


}
