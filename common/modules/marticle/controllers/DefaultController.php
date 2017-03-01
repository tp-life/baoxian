<?php

namespace common\modules\marticle\controllers;

use backend\components\BaseController;
use common\models\Article;
//use yii\web\Controller;

/**
 * Default controller for the `marticle` module
 */
class DefaultController extends BaseController
{
	const __AGREEMENT__ = 4;
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
	 * 获取 商家合同
	*/
	public function actionAgreement()
	{
		$article = Article::findOne(['id'=>self::__AGREEMENT__]);
		if(empty($article)){
			$this->showMessage('无效协议文章，请联系管理员', '', self::__MSG_DANGER);
		}
		return $this->renderPartial('agreement', ['model'=>$article]);
	}
}
