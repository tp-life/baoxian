<?php
/**
 * Created by PhpStorm.
 * User: tp
 * Date: 16/9/22
 * Time: 下午2:32
 */

namespace maintainer\controllers;


use common\models\BrandModel;
use common\models\BrandOffer;
use common\models\MaintenanceOffer;
use common\models\Seller;
use maintainer\components\LoginedController;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;

class OfferController extends LoginedController
{

    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionCreate()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $id = Yii::$app->request->post('id');
            if (!$id) {
                return $this->getCheckNo('参数错误!');
            }
            $seller_id = Seller::getSeller(Yii::$app->user->identity->id)->seller_id;
            $ids = explode(',', $id);
            $model = new MaintenanceOffer();
            $result = $model->find()->where(['seller_id' => $seller_id])->andWhere(['in', 'offer_id', $ids])->asArray()->all();
            $diff_id = $result ? array_column($result, 'offer_id') : [];
            $new_ids = array_diff($ids, $diff_id);
            $sql = 'INSERT INTO ' . MaintenanceOffer::tableName() . ' (`offer_id`,`seller_id`,`update_time`)  VALUES ';
            foreach ($new_ids as $k => $v) {
                $key = ':offer_id_' . $k;
                $sql .= " ( {$key} ,:seller_id ,  :update_time),";
                $value[$key] = $v;
            }
            $value[':update_time'] = date('Y-m-d H:i:s');
            $value[':seller_id'] = $seller_id;
            $sql = trim($sql, ',');
            $ret = Yii::$app->getDb()->createCommand($sql, $value)->execute();

            if ($ret) {
                return $this->getCheckYes([], '操作成功!');
            }
            return $this->getCheckNo('操作失败!');
        }

        $url = $this->createUrl(['offer/getdata', 'type' => 'brand']);
        $this->render('create', ['url' => $url]);
    }


    public function actionGetdata()
    {
        $pageSize = Yii::$app->request->post('length', 10);
        $start = Yii::$app->request->post('start', 0);//偏移量
        $type = Yii::$app->request->get('type', null);
        $main_offer_id = [];
        if ($type == 'brand') {
            $seller = Seller::getSeller(Yii::$app->user->identity->id)->seller_id;
            $main = MaintenanceOffer::find()->where(['seller_id' => $seller])->asArray()->all();
            $main_offer_id = array_column($main, 'offer_id');
            $model = $this->_conditionBrand(['not in', 'offer_id', $main_offer_id]);

        } else {
            $model = $this->_condition();
        }

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
        $data['data'] = $type == 'brand' ? $this->_handleBrandData($brand_offer, $main_offer_id) : $this->_handleData($brand_offer);

        return json_encode($data);
    }

    /**
     * 处理报价设置数据
     * @param array $brand_offer
     */
    private function _handleBrandData($brand_offer = [], $main_offer_id = [])
    {
        $brand = $this->getBrand();
        $data = [];

        foreach ($brand_offer as $key => $val) {
            $dis = '';
            if (!in_array($val['offer_id'], $main_offer_id)) {
                $btn = '<a class="btn green btn-xs btn-default" onClick="handleStatus(' . $val['offer_id'] . ',' . 1 . ')" href="javascript:;"> 加入维修 </a>';
            } else {
                $btn = '<a class="btn disabled btn-xs btn-default">已加入</a>';
                $dis = 'disabled="disabled"';
            }
            $data[] = array(
                '<input type="checkbox" ' . $dis . ' name="id[]" class="offer_checkbox" value="' . $val['offer_id'] . '">',
                $val['offer_id'],
                $val['name'],
                $brand[$val['brand_id']] ? $brand[$val['brand_id']]['model_name'] : '',
                $brand[$val['model_id']] ? $brand[$val['model_id']]['model_name'] : '',
//                $brand[$val['color_id']] ? $brand[$val['color_id']]['model_name'] : '',
//                $val['inner_screen'],
//                $val['outer_screen'],
//                $val['commission'] . ' %',
                $btn
            );
        }
        return $data;
    }

    /**
     * 处理查看报价数据
     * @param array $brand_offer
     * @return array
     */
    private function _handleData($brand_offer = [])
    {
        $brand = $this->getBrand();
        $data = [];
        foreach ($brand_offer as $key => $val) {

            $btn = $val['status'] == 1 ?
                '<a class="btn btn-xs default btn-editable" onClick="handleStatus(' . $val['id'] . ',' . 0 . ')"  href="javascript:;"> <i class="fa">暂停</i></a>'
                : '<a class="btn green btn-xs btn-default" onClick="handleStatus(' . $val['id'] . ',' . 1 . ')" href="javascript:;"> 重启 </a>';
            $btn .= '<a class="btn red btn-xs btn-default " href="javascript:void(0)" onclick="handleDelete(' . $val['id'] . ')"><i class="fa fa-trash-o"></i>删除 </a>';
            $data[] = array(
//                '<input type="checkbox" name="id[]" value="'.$val->id.'">',
                $val['id'],
                $val['name'],
                $brand[$val['brand_id']] ? $brand[$val['brand_id']]['model_name'] : '',
                $brand[$val['model_id']] ? $brand[$val['model_id']]['model_name'] : '',
//                $brand[$val['color_id']] ? $brand[$val['color_id']]['model_name'] : '',
//                $val['inner_screen'],
//                $val['outer_screen'],
//                $val['commission'] . ' %',
                $val['status'] == 1 ? '<span class="font-green-sharp">正常</span>' : '<span class="">暂停</span>',
                $btn
            );
        }
        return $data;
    }

    /**
     * 查看报价条件
     * @param array $condition
     * @return Query
     */
    private function _condition($condition = array())
    {

        $model = new Query();
        $seller = Seller::getSeller(Yii::$app->user->identity->id);
        if (!$seller) {
            $seller_id = 0;
        } else {
            $seller_id = $seller->seller_id;
        }
        $field = 'b_r.name,b_r.brand_id,b_r.model_id,b_r.color_id,b_r.inner_screen,b_r.outer_screen,b_r.commission,m_r.id,m_r.status,m_r.offer_id';
        $model->select($field)->from(['b_r' => BrandOffer::tableName(), 'm_r' => MaintenanceOffer::tableName()])->where(
            'm_r.offer_id = b_r.offer_id AND m_r.seller_id =:seller_id ', [':seller_id' => $seller_id]
        );
        $get = Yii::$app->request->get();
        $post = Yii::$app->request->post();
        $tj = array_merge($get, $post);
        $where = [];
        if (isset($tj['name']) && !empty($tj['name'])) {
            $model->andwhere(['like', 'b_r.name', $tj['name']]);
        }
        $condition = array_merge($where, $condition);
        $model->andWhere($condition);
        return $model;
    }

    /**
     * 新增报价条件
     * @param array $condition
     * @return \yii\db\ActiveQuery
     */
    private function _conditionBrand($condition = array())
    {
        $get = Yii::$app->request->get();
        $post = Yii::$app->request->post();
        $tj = array_merge($get, $post);
        $where = [];
        $model = BrandOffer::find();
        $model->where(['status' => 1]);
        if (isset($tj['name']) && !empty($tj['name'])) {
            $model->andwhere(['like', 'name', $tj['name']]);
        }
        $condition = array_merge($where, $condition);
        $model->andWhere($condition);
        return $model;
    }


    /**
     * 更改状态
     * @return array
     */
    public function actionChange()
    {
        if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
            return $this->getCheckNo('非法访问!');
        }
        $id = Yii::$app->request->post('id');
        $status = Yii::$app->request->post('status', null);
        if (!$id || is_null($status)) {
            return $this->getCheckNo('参数错误!');
        }
        $seller = MaintenanceOffer::findOne(['id' => $id]);
        if ($seller) {
            $seller->status = (int)$status;
            $seller->update_time = date('Y-m-d H:i:s', time());
            if ($seller->status) {
                $seller->offer_change_log_id = 0;
            }
            if ($seller->save()) {
                return $this->getCheckYes([], '操作成功!');
            }
        }
        return $this->getCheckNo('操作失败!');
    }

    /**
     * 更改状态
     * @return array
     */
    public function action()
    {

    }

    /**
     * 删除
     * @return array
     */
    public function actionDeleted()
    {
        if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
            return $this->getCheckNo('非法访问!');
        }
        $id = Yii::$app->request->post('id');
        if (!$id) {
            return $this->getCheckNo('参数错误!');
        }
        $seller = MaintenanceOffer::findOne(['id' => $id]);
        if ($seller->delete()) {
            return $this->getCheckYes([], '删除成功!');
        }
        return $this->getCheckNo('删除失败!');
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


    public function actionShowlog()
    {
        $seller_id = $this->seller->seller_id;
        $data = MaintenanceOffer::getChangeLog($seller_id);
        //$data = '';
        if (empty($data)) {
            return '';
        }
        return $this->renderPartial('_change_log', ['data' => $data]);

    }

}