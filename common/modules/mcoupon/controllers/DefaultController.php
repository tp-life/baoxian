<?php

namespace common\modules\mcoupon\controllers;

use backend\components\BaseController;
use common\models\CardCouponsGrant;
use common\models\Seller;
use Yii;

/**
 * Default controller for the `mcoupon` module
 */
class DefaultController extends BaseController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
		die('no access!');
        return $this->render('index');
    }
	/**
	 *后台 卡券 统计 卡券总览显示
	*/
	public function actionOverview()
	{
		if(!Yii::$app->request->isAjax){
			return  $this->getCheckNo('非法请求');
		}
		//return  $this->getCheckNo('暂无卡券统计数据');
		$num_total= CardCouponsGrant::find()->select('id')->count();
		$num_default = CardCouponsGrant::find()->where(['status'=>CardCouponsGrant::__STATUS_DEFAULT])->count();
		$num_active = CardCouponsGrant::find()->where(['status'=>CardCouponsGrant::__STATUS_ACTIVE])->count();
		$num_fail = CardCouponsGrant::find()->where(['status'=>CardCouponsGrant::__STATUS_FAIL])->count();
		$num_froze = CardCouponsGrant::find()->where(['status'=>CardCouponsGrant::__STATUS_FROZE])->count();
		$text = $this->renderPartial('overview', [
			'num_total' => $num_total,
			'num_default' => $num_default,
			'num_active' => $num_active,
			'num_fail' => $num_fail,
			'num_froze' => $num_froze,
			'seller'=>null
		]);
		$list = [
					[
						"name"=>"已激活",
						"value"=> $num_active
					],
			[
				"name"=>"未激活",
				"value"=> $num_default
			],[
				"name"=>"已失效",
				"value"=> $num_fail
			],[
				"name"=>"冻结中",
				"value"=> $num_froze
			],
				];
		return  $this->getCheckYes(['text'=>$text,'data'=>$list]);
	}

	/**
	 *商家 卡券 统计 卡券总览显示
	 */
	public function actionOverviewseller()
	{
		if(!Yii::$app->request->isAjax){
			return  $this->getCheckNo('非法请求');
		}
		$seller_id = intval(Yii::$app->request->get('seller_id',0));
		$seller = Seller::findOne(['seller_id'=>$seller_id]);
		if(empty($seller)){
			return  $this->getCheckNo('查无商家信息');
		}
		$num_total= CardCouponsGrant::find()->select('id')->where(['seller_id'=>$seller_id])->count();
		$num_default = CardCouponsGrant::find()->where(['status'=>CardCouponsGrant::__STATUS_DEFAULT,'seller_id'=>$seller_id])->count();
		$num_active = CardCouponsGrant::find()->where(['status'=>CardCouponsGrant::__STATUS_ACTIVE,'seller_id'=>$seller_id])->count();
		$num_fail = CardCouponsGrant::find()->where(['status'=>CardCouponsGrant::__STATUS_FAIL,'seller_id'=>$seller_id])->count();
		$num_froze = CardCouponsGrant::find()->where(['status'=>CardCouponsGrant::__STATUS_FROZE,'seller_id'=>$seller_id])->count();
		$text = $this->renderPartial('overview', [
			'num_total' => $num_total,
			'num_default' => $num_default,
			'num_active' => $num_active,
			'num_fail' => $num_fail,
			'num_froze' => $num_froze,
			'seller'=>$seller
		]);
		$list = [
			[
				"name"=>"已激活",
				"value"=> $num_active
			],
			[
				"name"=>"未激活",
				"value"=> $num_default
			],[
				"name"=>"已失效",
				"value"=> $num_fail
			],[
				"name"=>"冻结中",
				"value"=> $num_froze
			],
		];
		return  $this->getCheckYes(['text'=>$text,'data'=>$list]);
	}
}
