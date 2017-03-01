<?php

namespace common\modules\mimport\controllers;
use backend\components\BaseController;
use common\models\CardRefund;
use m35\thecsv\theCsv;
use maintainer\models\UploadForm;
use Yii;
use yii\base\Exception;
use yii\web\UploadedFile;

/**
 * Default controller for the `mimport` module
 */
class RexportController extends BaseController
{
    /**
     * Renders the index view for the module
     * @return string
     */
	public function actionIndex()
	{
		die('No access!');
	}

    /**
     * 导出退卡卡券号
     */
	public function actionRefund(){
        $id = Yii::$app->request->get();
        $model =CardRefund::findOne(['id'=>$id]);
        $data=$model->getCardInfo(2000);
        $temp=[];
        foreach ($data as $item){
            $c_info = $item->getCoverageInfo();
            $temp[]=[
                $item->id,
                $item->card_number,
                '="'.$item->coverage_code.'"',
                $item->getStatusText(),
                $c_info->coverage_name,
                $c_info->type_name,
                $c_info->company_name
            ];
        }
        theCsv::export([
            'data' => $temp,
            'name' => "refund_card_list_".date('Y_m_d_H', time()).".csv",    // 自定义导出文件名称
            'header' => ['ID','卡券编号','险种编码','卡券状态','保险名称','所属保险类型','所属保险公司'],
        ]);
    }
}
