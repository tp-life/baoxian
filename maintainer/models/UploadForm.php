<?php


/**
 * Created by PhpStorm.
 * User: Leo.Yan
 * Date: 2016/9/26
 * Time: 15:56
 * Version:Lehuanxin@baoxian 1.0
 * Project:dev
 * File: UploadForm.php
 */

namespace maintainer\models;


use yii\base\Model;

class UploadForm extends Model {

	public $file;
	public function rules()
	{
		return [
			[['file'], 'file', 'extensions' => 'jpg, png', 'mimeTypes' => 'image/jpeg, image/png','maxSize'=>1024*1024*8,'tooBig'=>'图片尺寸超出限制'],
		];
	}
}