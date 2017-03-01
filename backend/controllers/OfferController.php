<?php
/**
 * Created by PhpStorm.
 * User: tp
 * Date: 16/9/9
 * Time: 上午10:56
 */

namespace backend\controllers;


use backend\components\LoginedController;
use common\library\helper;
use common\library\UploadFile;
use common\models\BrandModel;
use common\models\BrandOffer;
use common\models\BrandOfferUpdateLog;
use common\models\MaintenanceOffer;
use m35\thecsv\theCsv;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;

class OfferController extends LoginedController
{
    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionGetdata()
    {
        $pageSize = Yii::$app->request->post('length', 10);
        $start = Yii::$app->request->post('start', 0);//偏移量
        $brand = $this->getBrand();
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
        foreach ($brand_offer as $key => $val) {
            $btn = '<a class="btn green btn-xs btn-default " href="' . $this->createUrl(['offer/create', 'id' => $val->offer_id]) . '"><i class="fa fa-edit"></i> 修改报价 </a>';
            $btn .= $val->status == 1 ?
                '<a class="btn red btn-xs btn-default " onClick="handleStatus(' . $val->offer_id . ',' . 0 . ')"  href="javascript:;"><i class="fa fa-trash-o"></i> 终止使用 </a>'
                : '<a class="btn blue btn-xs btn-default" onClick="handleStatus(' . $val->offer_id . ',' . 1 . ')" href="javascript:;"><i class="fa fa-check"></i> 重启使用 </a>';
            $num =$this->getMainCount($val->offer_id);
            $data['data'][] = array(
//                '<input type="checkbox" name="id[]" value="'.$val->id.'">',
                $val->offer_id,
                $val->name,
                $brand[$val->brand_id] ? $brand[$val->brand_id]['model_name'] : '',
                $brand[$val->model_id] ? $brand[$val->model_id]['model_name'] : '',
                $brand[$val->color_id] ? $brand[$val->color_id]['model_name'] : '',
                $val->inner_screen,
                $val->outer_screen,
                $val->commission . ' %',
                $val->status == 1 ? '<label class="font-green-sharp">正常</label>' : '<label class="">暂停</label>',
                $num >0?'<a title="点击查看报价商户"  class="btn  btn-outline btn-xs font-red-thunderbird" style="text-decoration:underline;" href="'.$this->createUrl(['offermain/index','offer_id'=>$val->offer_id]).'">'.$num.'</a>':$num,
                $btn
            );
        }

        return json_encode($data);
    }

    private function _condition($condition = array())
    {
        $model = BrandOffer::find();
        $get=Yii::$app->request->get();
        $post=Yii::$app->request->post();
        $tj=array_merge($get,$post);
        $where=[];
        if(isset($tj['name']) && !empty($tj['name'])){
            $model->where(['like','name',$tj['name']]);
        }
        $condition=array_merge($where,$condition);
        $model->andWhere($condition);
        return $model;
    }

    public function actionCreate()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            if(!$post['brand_id']){
				return $this->getCheckNo('请选择品牌');
			}
			if(!$post['model_id']){
				return $this->getCheckNo('请选择品牌型号');
			}
			if(!$post['outer_screen']){
				return $this->getCheckNo('请填写外屏报价');
			}

            list($brand_id, $brand) = explode(',', $post['brand_id']);
            list($model_id, $m) = explode(',', $post['model_id']);
            list($color_id, $color) = explode(',', $post['color_id']);
            if (isset($post['id']) && $post['id']) {
                $model = BrandOffer::findOne(['offer_id' => $post['id']]);
            } else {
                $tj = [
                    'brand_id' => $brand_id,
                    'model_id' => $model_id
                ];
                if ($color_id) {
                    $tj['color_id'] = $color_id;
                }
                $model = BrandOffer::findOne($tj);
                $model = $model ? $model : new BrandOffer();
            }

			$model->brand_id = $brand_id;
			$model->model_id = $model_id;
			$model->color_id = (int)$color_id;
			$model->name = $brand . ' ' . $m . ' ' . $color;

			//日志添加 start
			$log_data = array();
			if (!$model->isNewRecord) {
				$before_inner_screen = $model->inner_screen;
				$before_outer_screen = $model->outer_screen;
				$after_inner_screen = $post['inner_screen'];
				$after_outer_screen = $post['outer_screen'];

				//价格异动
				if ($before_inner_screen != $after_inner_screen || $before_outer_screen != $after_outer_screen) {
					$log_data['name'] = trim($model->name);
					$log_data['before'] = [
						'inner_screen' => $before_inner_screen,
						'outer_screen' => $before_outer_screen
					];
					$log_data['after'] = [
						'inner_screen' => $after_inner_screen,
						'outer_screen' => $after_outer_screen
					];
				}
			}
			//日志添加 end

            $model->inner_screen = $post['inner_screen'];
            $model->outer_screen = $post['outer_screen'];
            $model->commission = trim($post['commission']) === '' ? 5 : (int)$post['commission'];
            $model->update_time = date('Y-m-d H:i:s');

            if ($model->save()) {

				if($log_data){
					BrandOfferUpdateLog::addOfferLog($model->offer_id,$log_data);
				}

                return $this->getCheckYes([], '操作成功');
            }
            return $this->getCheckNo('操作失败');

        }
        $id = Yii::$app->request->get('id', '');
        $info = BrandOffer::find()->where(['offer_id' => $id])->asArray()->one();
        $model_html = $color_html = '';
        if ($id) {
            $model_html = helper::getBrandModel($info['brand_id'], $info['model_id']);
            $color_html = helper::getBrandModel($info['model_id'], $info['color_id']);
        }
        $brand = BrandModel::findAll(['parent_id' => 0]);
        $this->render('create', ['brand' => $brand, 'model_html' => $model_html, 'color_html' => $color_html, 'info' => $this->viewData($info)]);
    }


    public function  actionGetbrand()
    {
        if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
            $this->showMessage('非法访问!');
        }
        $id = Yii::$app->request->post('id', '');
        if (!$id) {
            return $this->getCheckNo('参数错误');
        }
        $brand = BrandModel::find()->where(['parent_id' => $id])->asArray()->all();
        return $this->getCheckYes($brand);
    }

    public function actionChange()
    {
        if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
            return $this->getCheckNo('非法访问!');
        }
        $seller_id = Yii::$app->request->post('offer_id');
        $status = Yii::$app->request->post('status', null);
        if (!$seller_id || is_null($status)) {
            return $this->getCheckNo('参数错误!');
        }
        $seller = BrandOffer::findOne(['offer_id' => $seller_id]);
        if ($seller) {
            $seller->status = (int)$status;
			$seller->update_time = date('Y-m-d H:i:s',time());

            if ($seller->save()) {

				if ($seller->status === 0) {
					//add log
					BrandOfferUpdateLog::addOfferLog($seller['offer_id'], ['name'=>$seller['name']], BrandOfferUpdateLog::__HD__STOP);
				}

                return $this->getCheckYes([], '操作成功!');
            }
        }
        return $this->getCheckNo('操作失败!');
    }

    /**
     * 导出报价
     */
    public function actionDownload(){
        $model = $this->_condition();
        $data = $model->limit(2000)->orderBy('brand_id DESC,model_id DESC')->all();
        $temp=[];
        $brand = $this->getBrand();
        foreach ($data as $val){
            $temp[]=[
                $val->offer_id,
                $val->name,
                $brand[$val->brand_id] ? $brand[$val->brand_id]['model_name'] : '',
                $brand[$val->model_id] ? $brand[$val->model_id]['model_name'] : '',
                $brand[$val->color_id] ? $brand[$val->color_id]['model_name'] : '',
                $val->inner_screen,
                $val->outer_screen,
                $val->commission . ' %',
                $val->status == 1 ? '正常' : '暂停',
            ];
        }
        theCsv::export([
            'data' => $temp,
            'name' => "offer_list_".date('Y_m_d_H', time()).".csv",    // 自定义导出文件名称
            'header' => ['ID','手机名称','品牌','型号','颜色','内屏报价','外屏报价','平台服务费','状态'],
        ]);
    }

    /**
     * 导入报价
     */
    public function actionImport(){

        if($_FILES['offer']['name']) {
            $maxSize= 3145728;
            $allowExts = array('csv');
            $path=pathinfo($_FILES['offer']['name']);
            if(!in_array($path['extension'],$allowExts) || $_FILES['offer']['size'] > $maxSize){
                $err='文件大小超出限制或文件后缀不合法';
                goto err;
            }
            $data=$this->handData($_FILES['offer']['tmp_name']);
            if(!$data){
                $err='文件内容读取错误';
                goto err;
            }
            $ids = array_column($data,'id');
            $result = BrandOffer::find()->where(['offer_id'=>$ids])->all();
            $tran = Yii::$app->db->beginTransaction();
            $succ=false;
            try {
                foreach ($result as $val) {
                    if (array_key_exists($val->offer_id, $data) && ($data[$val->offer_id]['n_p'] != $val->inner_screen || $data[$val->offer_id]['w_p'] != $val->outer_screen)) {
                        $inner_screen = $val->inner_screen;
                        $outer_screen =$val->outer_screen;
                        $val->inner_screen = $data[$val->offer_id]['n_p'];
                        $val->outer_screen = $data[$val->offer_id]['w_p'];
                        if ($val->save()) {
                            $log_data['name'] = trim($val->name);
                            $log_data['before'] = [
                                'inner_screen' => $inner_screen,
                                'outer_screen' => $outer_screen
                            ];
                            $log_data['after'] = [
                                'inner_screen' => $data[$val->offer_id]['n_p'],
                                'outer_screen' => $data[$val->offer_id]['w_p']
                            ];
                            BrandOfferUpdateLog::addOfferLog($val->offer_id, $log_data);
                            $succ=true;
                        } else {
                            throw  new Exception($val->name .' 报价变动修改失败。');
                        }
                    }
                }
                if($succ){
                    $tran->commit();
                    return json_encode(array('status' => 1, 'msg' =>'报价导入成功'));
                }else{
                    throw  new Exception('没有要变动的报价');
                }
            }catch (Exception $e){
                $tran ->rollBack();
                $err = $e->getMessage();
                goto err;
            }
        }
        err:{
            return json_encode(array('status' => 0, 'msg' =>$err));
        }
    }


    private function handData($file=''){
        $data = array();
        $handle = fopen($file, "rb");
        $i=0;
        while (!feof($handle)) {
            usleep(50);
            $line = fgets($handle);
            $line = trim($line);
            if($i && $line){
                list($id,,,,,$n_p,$w_p)=explode(',',$line);
                if(!is_numeric($id)){
                    return false;
                }
                $data[$id]=['id'=>$id,'n_p'=>$n_p,'w_p'=>$w_p];
            }
            $i++;
            usleep(20);
        }
        return $data;
    }



    private function viewData($data)
    {
        $seller = is_array($data) ? $data : [];
        $temp = ['brand_id' => '', 'model_id' => '', 'color_id' => '', 'inner_screen' => '', 'outer_screen' => '', 'commission' => ''];
        return array_merge($temp, $seller);
    }

    private function getBrand()
    {
        $brand = BrandModel::find()->asArray()->all();
        $data = [];
        foreach ($brand as $val) {
            $data[$val['id']] = $val;
        }
        return $data;
    }

    private function getMainCount($offer_id=''){
        $model= MaintenanceOffer::find();
        return $model->where(['offer_id'=>$offer_id,'status'=>1])->count();
    }

}