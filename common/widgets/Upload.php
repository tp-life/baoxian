<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/15
 * Time: 10:09
 *
 * 文件读取显示 处理
 */

namespace common\widgets;

use yii\bootstrap\Widget;
use Yii;

class Upload extends Widget
{


    public $id;   //隐藏域ID
    public $name; //要保存的目录
    public $img; //要显示图片 imgID
    public $button='上传图片';//上传按钮名称
    public $url;
    public $parms;
    public function init()
    {
        parent::init();

    }

    public function run()
    {
        $dir = $this->name?$this->name:'admin';
        $id  = $this-> id;
        return $this->render('upload',['id'=>$id,'dir'=>$dir,'img'=>$this->img,'button'=>$this->button,'url'=>$this->url,'parms'=>\GuzzleHttp\json_encode($this->parms)]);
    }



}