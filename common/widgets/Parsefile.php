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

class Parsefile extends Widget
{

	public $id;

	public function init()
	{
		parent::init();

	}

	public function run()
	{
		return $this->render('pasefile');
	}



}