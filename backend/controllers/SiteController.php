<?php
namespace backend\controllers;

use backend\components\BaseController;
use common\models\AdminLog;
use common\models\Article;
use common\models\Bank;
use common\models\BrandModel;
use common\models\Member;
use common\models\MemberExtend;
use common\models\Msg;
use common\models\Seller;
use Yii;
use backend\models\LoginForm;
use yii\data\ActiveDataProvider;
use yii\helpers\FileHelper;

/**
 * Site controller
 */
class SiteController extends BaseController
{
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [];
	}

	/**
	 * @inheritdoc
	 */
	public function actions()
	{
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
		];
	}

	/**
	 * Displays homepage.
	 *
	 * @return string
	 */
	public function actionIndex()
	{
		if (Yii::$app->user->isGuest) {
			return $this->redirect(['site/login']);
		}
		$model = Msg::find();
		$model->where(['seller_id'=>0]);
		$count=$model->count();
		$pageSize=15;
		$page=Yii::$app->request->get('page',0);
		if($page < 1 ){
			$page =0;
		}
		$dataProvider = new ActiveDataProvider([
				'query' =>$model->orderBy('status asc')->limit($pageSize)->offset($pageSize*$page)->asArray(),
				'pagination' => [
						'pageSize' => $pageSize,
						'page' => $page,
						'totalCount' => $count
				]
		]);
		$result=$dataProvider->getModels();

		return $this->render('index',['result'=>$result,'page'=>$page,'total_page'=>ceil($count/$pageSize)]);
	}


	public function actionHandlemsg(){
		if (Yii::$app->user->isGuest) {
			return $this->redirect(['site/login']);
		}

		if(Yii::$app->request->isAjax && Yii::$app->request->isPost){
			$id = Yii::$app->request->post('id','');
			if(!$id){
				return $this->getCheckNo('参数错误!');
			}
			$member_id = Yii::$app->user->identity->id;
			$model = Msg::findOne(['id'=>$id]);
			$model->status = 1 ;
			$model->read_id = $member_id;
			$model->read_time =time();
			if($model->save()){
				return $this->getCheckYes([],'操作成功!');
			}
			return $this->getCheckNo('操作失败!');
		}
	}

	/**
	 * Login action.
	 *
	 * @return string
	 */
	public function actionLogin()
	{
		$model = new LoginForm();
		if ($model->load(Yii::$app->request->post()) && $model->login()) {
			return $this->redirect(['site/index']);
		} else {
			$this->layout = 'login_layout';//未登录 布局
			return $this->render('login', [
				'model' => $model,
			]);
		}
	}

	public function actionLock()
	{
		if (Yii::$app->user->isGuest) {
			return $this->redirect(['site/login']);
		}
		AdminLog::LogOutLog();
		$this->layout = 'empty_layout';//已登录 空布局
		return $this->render('user_lock');
	}

	/**
	 * Logout action.
	 *
	 * @return string
	 */
	public function actionLogout()
	{	AdminLog::LogOutLog();
		Yii::$app->user->logout(true);
		return $this->goHome();
	}

	protected function parseFile($file)
	{
		$data = array();
		$handle = fopen($file, "rb");
		while (!feof($handle)) {
			usleep(100);
			$line = fgets($handle);
			$line = iconv('gbk', 'utf-8', $line);
			$line = explode(',',$line);
			$data[] = $line;
			unset($_temp);
			$_temp = null;
			usleep(20);
		}
		return $data;
	}
	protected function fileLog($name,$data)
	{
		FileHelper::createDirectory(Yii::getAlias('@runtime').'/import_log',0775);
		$data = is_array($data)?var_export($data,true):$data;
		file_put_contents(Yii::getAlias('@runtime').'/import_log/'.$name.'_'.date("Y-m-d").'.log',$data.PHP_EOL,FILE_APPEND);
	}

	/**
	 * 导入商家信息
	 *
	 * [0] => Array
	(
	商家编号[0] => 21
	公司[1] => 四川欢欣网络技术有限公司
	联系人[2] => 蒋军保险
	联系电话[3] => 18140043611
	用户编号[4] => 10134
	是否保险商户[5] => 1
	是否维修商户[6] => 1
	商家等级[7] => 10
	父级商家[8] => 0
	商家添加时间[9] => 1460426438
	商家名称[10] => 乐换新
	省[11] => 23
	市[12] => 385
	区[13] => 45056
	地址[14] => 四川 成都市 高新区
	详细[15] => 府城大道399号天府新谷5-1306
	银行[16] =>
	账户[17] =>
	卡号[18] =>
	用户名 member[19] => 蒋军保险
	密码[20] => e10adc3949ba59abbe56e057f20f883e
	登录电话[21] => 18140043611
	真名[22] =>
	注册时间[23] => 1460426438

	)
	**/
	private function actionImportsellerinfo()
	{
		set_time_limit(0);
		echo 'start';
		//echo Yii::getAlias('@runtime');
		$path = Yii::getAlias('@runtime');
		$file_name = 'seller_import_info.csv';
		$d = $this->parseFile($path.'/'.$file_name);
		return ;die;
		//print_r($d);
		foreach($d as $k=>$v){

			if(!$v || !$v[0]){
				continue;
			}

			//添加用户
			$t_member = new Member();
			$t_member->isNewRecord = true;
			$t_member->attributes = [
				'name'=>$v[22]?$v[22]:$v[19],
				'phone'=>$v[21],
				'passwd'=>$v[20],
			];
			//print_r($t_member->attributes);die;
			if($t_member->save(false)){
				// 用户扩展
				$member_extend=new MemberExtend();
				$member_extend->member_id=$t_member->member_id;
				$member_extend->register_time=$v[23];
				$member_extend->save(false);

				//商家信息

				$t_seller = new Seller();
				$t_seller->isNewRecord = true;
				$t_seller->attributes = [
					'seller_name'=> $v[10],
					'member_id'=>$t_member->member_id,
					'is_insurance'=>$v[5],
					'is_repair'=>$v[6],
					'pid'=>0,
					'province_id'=>$v[11],
					'city_id'=>$v[12],
					'area_id'=>$v[13],
					'area_info'=>$v[14],
					'detail_address'=>$v[15],
					'concat'=>$v[2],
					'concat_tel'=>$v[3],
					'status'=>1,
					'add_time'=>$v[9],
					'is_agreement'=>1
				];
				if($t_seller->save(false)){
					if($v[16] && $v[17] && $v[18]){
						$bank = new Bank();
						$bank->isNewRecord = true;
						$bank->attributes = [
							'member_id'=>$t_member->member_id,
							'brank_name'=>$v[16],
							'brank_account'=>$v[18],
							'account_holder'=>$v[17],
							'is_default'=>1,
							'add_time'=>$v[9]
						];
						$bank->save(false);
					}
				}else{
					$this->fileLog('fail_seller_insert',implode(',',$v));
				}


			}else{
				$this->fileLog('fail_user_insert',implode(',',$v));
			}

		}

		echo '#end';

	}

	/**
	 * 维修报价处理
	**/

	private function actionBaojia()
	{
		set_time_limit(0);
		echo 'start';
       return ;die;
		$path = Yii::getAlias('@runtime');
		$file_name = 'baojia.csv';
		$d = $this->parseFile($path.'/'.$file_name);
		foreach($d as $k=>$v){

			if(!$v || !$v[0]){
				continue;
			}
			$brand = trim($v[0]);//品牌
			$b_model = trim($v[1]);//型号
			$price = trim($v[2]);//报价
			$obj_brand = BrandModel::findOne(['model_name'=>$brand]);
			if(!$obj_brand){
				$this->fileLog('fail_baojia',implode(',',$v));
				continue;
			}
			$obj_model = BrandModel::findOne(['model_name'=>$b_model,'parent_id'=>$obj_brand['id']]);
			if(!$obj_model){
				$this->fileLog('fail_baojia_model',implode(',',$v));
				continue;
			}

			$sql = "INSERT INTO `fj_brand_offer` VALUES (null, '{$obj_brand['id']}', '{$obj_model['id']}', '0', '{$brand} {$b_model}', '{$price}', '{$price}', '5', '1', '2016-12-19 14:33:07');";

			$this->fileLog('ok_baojia_sql',$sql);

		}

		echo '#end';
	}

}
