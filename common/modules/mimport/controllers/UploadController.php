<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/15
 * Time: 9:17
 */

namespace common\modules\mimport\controllers;
use backend\components\BaseController;
use maintainer\models\UploadForm;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use Yii;
class UploadController extends BaseController
{
    public function actionImg(){
        if (!Yii::$app->request->isPost && !Yii::$app->request->isAjax) {
            return $this->getCheckNo('非法请求');
        }
        $dir = Yii::$app->request->post('dir');
        $url = Yii::$app->request->post('url');
        $parms = Yii::$app->request->post('parms');
        if($parms){
            $parms =\GuzzleHttp\json_decode($parms,true);
        }
        $this->uploadFile($dir,$url,$parms);
    }


    protected function uploadFile($dir='admin',$url='',$parms=[])
    {

        $root = Yii::getAlias('@webroot');
        $path = '/uploads/'.$dir.'/'.date('Ymd');
        if (!FileHelper::createDirectory($root.$path)) {
            exit(json_encode(['code' => 400, 'message' => '文件上传权限不足', 'data' => []]));
        }
        $model = new UploadForm();
        $model->file = UploadedFile::getInstanceByName('upimg');
        if ($model->file && $model->validate()) {
            $baseName = $model->file->baseName;
            $extension = $model->file->extension;
            $file = strtolower($path.'/_'.sha1($baseName.time()).'.'.$extension);
            if ($model->file->saveAs($root.$file)) {
                if($url){
                    $parms['path']=$file;
                    $url = $url?Yii::$app->urlManager->createUrl([$url,'data'=>$parms]):'';
                   return Yii::$app->response->redirect([$url]);
                }
                exit(json_encode(['code' => 200, 'message' => 'Success', 'data' => ['path'=>$file]]));
            } else {
                $model->addError('file', '文件保存出错');
            }
        }
        exit(json_encode(['code' => 400, 'message' => var_export($model->getErrors(), true), 'data' => []]));

    }
}
