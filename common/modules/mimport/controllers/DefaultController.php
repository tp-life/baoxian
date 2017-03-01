<?php

namespace common\modules\mimport\controllers;
use backend\components\BaseController;
use maintainer\models\UploadForm;
use Yii;
use yii\base\Exception;
use yii\web\UploadedFile;

/**
 * Default controller for the `mimport` module
 */
class DefaultController extends BaseController
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
	 * 卡券导入转化器
	 *
	 * @return string
	 */
	public function actionUpload()
	{
		if(!Yii::$app->request->isPost){
			$this->responData['message'] = '非法请求,请联系管理员';
			exit(json_encode($this->responData));
		}
		$model = new UploadForm();
		$model->file = UploadedFile::getInstance($model, 'file');

		if ($file = $model->file) {
			try{
				$data = $this->parseFile($file->tempName);
				$this->responData['code'] = 'yes';
				$this->responData['message'] = 'Success';
				$this->responData['data'] = ['cards'=>implode(',',$data),'count'=>count($data)];
				exit(json_encode($this->responData));
			}catch (Exception $e){
				$model->addError('file', $e->getMessage());
			}
		}
		$this->responData['message'] = var_export($model->getErrors(), true);
		exit(json_encode($this->responData));
	}

	protected function parseFile($file)
	{
		$data = array();
		$handle = fopen($file, "rb");
		while (!feof($handle)) {
			usleep(100);
			$line = fgets($handle);
			//$line = iconv('gbk', 'utf-8', $line);

			//是否是多列
			if(stripos($line,',')!==false){
				$line = explode(',',$line)[0];
			}
			$line = str_replace(array('"',"'","=",'_'),array('','','',''),$line);
			$line = trim($line);
			if ($a=preg_match('/^\w{7}$/', $line)) {
				$data[] = $line;
			} elseif (preg_match('/\d{3,}/', $line)) {
				$data[] = str_pad($line, 7, 0, STR_PAD_LEFT);
			}
			$_temp = null;
			usleep(20);
		}
		//return implode(',',$data);
		return $data;
	}


	/**
	 * 微信应用统一上传处理 并返回路径     未启用
	**/
	private function actionUploadwxfile()
	{
		$token = $_REQUEST['token'];
		if(empty($token)){
			exit(json_encode(['code' => 400, 'message' => '参数缺失', 'data' => []]));
		}
		$root = Yii::getAlias('@webroot');
		$path = $root.'/uploads';
		$path = $path.'/weixin/'.date('Ymd');
		if (!FileHelper::createDirectory($path)) {
			exit(json_encode(['code' => 400, 'message' => '文件上传权限不足', 'data' => []]));
		}
		$model = new UploadForm();
		$model->file = UploadedFile::getInstance($model, 'file');
		if ($model->file && $model->validate()) {
			$baseName = $model->file->baseName;
			$extension = $model->file->extension;
			$file = strtolower($path.'/'.$token.'_'.sha1($baseName.time()).'.'.$extension);
			if ($model->file->saveAs($file)) {
				$file = '/'.ltrim($file, strtolower($root));
				exit(json_encode(['code' => 200, 'message' => 'Success', 'data' => ['path'=>$file,'token'=>$token]]));
			} else {
				$model->addError('file', '文件保存出错');
			}
		}
		exit(json_encode(['code' => 400, 'message' => var_export($model->getErrors(), true), 'data' => []]));

	}





}
