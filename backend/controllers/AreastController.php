<?php
/**
 * 卡券生成管理
 */

namespace backend\controllers;

use backend\components\LoginedController;

use common\models\Area;
use common\models\Statistics;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use common\models\Seller;
use m35\thecsv\theCsv;

/**
 * CardController implements the CRUD actions for CardCouponsGrant model.
 */
class AreastController extends LoginedController
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
            $post = Yii::$app->request->post();
            $pageSize = Yii::$app->request->post('length', 10);
            $start = Yii::$app->request->post('start', 0);//偏移量
            $model = $this->_condition();
            $total = $model->count();
            $dataProvider = new ActiveDataProvider([
                'query' => $model->limit($pageSize)->offset($start),
                'pagination' => [
                    'pageSize' => $pageSize,
                    'page' => intval($start / $pageSize),
                    'totalCount' => $total
                ],
            ]);

            if ($data = $dataProvider->models) {
                $respon=$this->handleData($data);
                if(isset($post['area_id']) && $post['area_id']){
                    $tj=['or','s.city_id = '.intval($post['area_id']),'s.province_id = '.intval($post['area_id'])];
                }else{
                    $tj=[];
                }
                $s_info =$this->getStatisticsInfo($tj);
                $s_text = '<div class="text-right"><p class="text-right">一级商家： <b class="font-red-mint">'.$s_info['level_one'].'</b>；二级商家：<b class="font-red-mint">'.$s_info['level_two'].'</b></p><p>合计发放数： <b class="font-red-mint">' . $s_info['total_card'] . '</b>；激活数： <b class="font-red-mint">' . $s_info['total_active'] . '</b>；激活率： ' .
                    '<b class="font-red-mint"> ' . $s_info['ac'] . '</b>；合计理赔数： <b class="font-red-mint">' . $s_info['total_settle'] . '</b>；理赔率： ' .
                    '<b class="font-red-mint"> ' . $s_info['sc'] . '</b></p></div>';
            }
            return json_encode(array('data' => $respon, 'recordsTotal' => $total, 'recordsFiltered' => $total, 'statistics_text' => $s_text));
        }
        $data = $this->getArea();
        $area=[
            ['id'=>0,'name'=>'全部省市','pId'=>0,'open'=>true]
        ];
        foreach ($data as $val){
            $temp=[
                'id'=>$val['area_id'],
                'name'=>$val['area_name'],
                'pId'=>$val['area_parent_id']
            ];
            if (!$temp['pId']) {
                $temp['open'] = true;
            }
            if(!isset($area[$val['area_id']])){
                $area[$val['area_id']]=$temp;
            }

        }
        return $this->render('index',['area'=>array_values($area)]);

    }


    public function actionExport()
    {
        $get = Yii::$app->request->get();
        $model = $this->_condition([], $get);
        $data = $model->limit(5000)->all();
        $respon=$this->handleData($data);
        set_time_limit(0);
        $this->_export($respon);
    }


    private function getArea(){
        $a_tb = Area::tableName();
        $s_tb = Seller::tableName();
        $query= new Query();
        $result= $query->select('a.*')->from(['a'=>$a_tb])->innerJoin(['s'=>$s_tb],'a.area_id = s.province_id OR a.area_id = s.city_id')
            ->where(['not in', 's.seller_id', Seller::$lehuanxin])->orderBy('area_parent_id asc ,convert(area_name using gbk) ASC')
            ->all();
        return $result;
    }


    private function sortArea($array=[]){

        foreach ($array as $key=>$value)
        {
            $new_array[$key] = iconv('UTF-8', 'GBK', $value);
        }

        sort($new_array);
        foreach ($new_array as $key=>$value)
        {
            $array[$key] = iconv('GBK', 'UTF-8', $value);
        }
        return $array;
    }

    private function _export($respon)
    {
        theCsv::export([
            'data' => $respon,
            'name' => "seller_Area_statistics_" . date('Y_m_d_H', time()) . ".csv",    // 自定义导出文件名称
            'header' => ['省份', '地区', '一级商家', '二级商家', '卡券总数', '已激活', '未激活',  '失效', '冻结', '理赔', '激活率', '理赔率'],
            //'header' => ['卡券序列号','生成时间','险种','卡券状态','所属商家'],
        ]);
    }

    private function handleData($data = [])
    {
        if (!$data || !is_array($data)) {
            return [];
        }
        $map=[];
        foreach ($data as $val) {
            $seller_s = Seller::find()->where(['city_id' => $val->city_id])->all();
            $seller_s = ArrayHelper::toArray($seller_s);
            $seller_ids = array_column($seller_s, 'seller_id');
            $s_info = $this->getStatisticsInfo(['in', 's.seller_id', $seller_ids]);
            $map[]=array_values($s_info);
        }
        return $map;
    }


    /**
     * 获取统计信息
     * @param array $tj
     * @return array
     */
    private function getStatisticsInfo($tj = [])
    {
        $s_tb = Seller::tableName();
        $tj_tb = Statistics::tableName();
        $query = new Query();
        $where = ['s.is_insurance' => 1];
        $subQuery = (new Query())->from($tj_tb)->orderBy('id desc');
        $query->select([
            's.area_info','s.seller_id',  's.status', 's.pid', 't.card_num', 't.active_num', 't.settle_num', 't.lsoe_num', 't.frost_num', 't.wait_num'
        ])->from(['s' => $s_tb])->leftJoin(['t' => $subQuery], 's.seller_id=t.seller_id')->where($where)->andwhere(['not in', 's.seller_id', Seller::$lehuanxin]);
        $s_info = $query->andWhere($tj)->groupBy('s.seller_id')->orderBy('t.wait_num DESC,s.seller_id DESC')->all();
        $level_one = 0;
        $level_two = 0;
        $total_card = $total_settle = $total_active = $total_lsoe = $total_frost = $total_wait = 0;
        foreach ($s_info as $val) {
            if ($val['pid'] > 0) {
                $level_two++;
            } else {
                $level_one++;
            }
            $total_card += $val['card_num'];
            $total_active += $val['active_num'];
            $total_frost += $val['frose_num'];
            $total_lsoe += $val['lsoe_num'];
            $total_wait += $val['wait_num'];
            $total_settle += $val['settle_num'];
        }
        $area_info = $s_info[0]['area_info'];
        list($p,$c)=explode(' ',$area_info);
        $ac = $total_card ? round(100 * $total_active / $total_card, 2) : 0;
        $sc = $total_card ? round(100 * $total_settle / $total_card, 2) : 0;
        $ac=$ac>0?$ac.'%':0;
        $sc=$sc>0?$sc.'%':0;
        return compact('p','c','level_one','level_two','total_card','total_active','total_wait','total_lsoe','total_frost','total_settle','ac','sc');
    }

    private function _condition($condition = [], $request = [])
    {

        $where = ['is_insurance' => 1];
        $post = Yii::$app->request->post();
        if ($request) {
            $post = array_merge($post, $request);
        }
        $condition = array_merge($where, $condition);
        $query = Seller::find()->where($condition);
        if(isset($post['area_id']) && !empty($post['area_id'])){
            $query->andWhere(['or','city_id = '.intval($post['area_id']),'province_id = '.intval($post['area_id'])]);
        }
        $query->groupBy('city_id');
        return $query->orderBy('city_id ASC ');
    }


}
