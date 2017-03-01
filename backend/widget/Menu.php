<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/15
 * Time: 10:09
 */

namespace backend\widget;

use yii\bootstrap\Widget;
use Yii;
use common\models\RoleAccess;
use common\models\RoleNavGroup;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class Menu extends Widget
{

	private $_menu;
	private $_controller_id;
	private $_action_id;
	private $_role_id;
	const _CACHE_ROLE_DATA_PFIX = 'role_';
	const _CACHE_ACCESS_DATA_PFIX = 'data_';
	//const _CACHE_MENU_DATA_PFIX = 'menu_';
	const _CACHE_NAV_DATA_PFIX = 'nav_';
	const _CACHE_TIME = 1800;

	const _MENU_CACHE_OPEN = true;

	public function init()
	{
		parent::init();
		//$this->_menu = Yii::$app->params['menu'];//test
		$this->_controller_id = Yii::$app->controller->id;
		$this->_action_id = Yii::$app->controller->action->id;
		$this->_role_id = Yii::$app->user->identity->role_id;
		$this->_menu = $this->initMenuData();

	}

	public function run()
	{
		return $this->render('menu', ['data' => $this->_menu]);
	}

	/**
	 * @note 获取菜单项
	 **/
	protected function getNavData()
	{
		if (self::_MENU_CACHE_OPEN) {
			if ($dataMenu = Yii::$app->cache->get(self::_CACHE_NAV_DATA_PFIX)) {
				return $dataMenu;
			}
		}

		$dataMenu = $this->__getNavDataOptimize();
		if ($dataMenu && self::_MENU_CACHE_OPEN) {
			Yii::$app->cache->set(self::_CACHE_NAV_DATA_PFIX, $dataMenu, self::_CACHE_TIME);
		}

		return $dataMenu;
	}

	private function __getNavDataOptimize()
	{
		$navsql = <<<NAVSQL
SELECT
	a.id AS action_id,
	a.action,
	a. NAME AS action_name,
	b.module,
	b. NAME AS module_name,
	b.id AS module_id,
	c. NAME AS nav_name,
	c.id AS nav_id,
   c.icon AS nav_icon,
	d. NAME AS menu_name,
	d.id AS menu_id,
  d.icons AS menu_icon
FROM
	fj_role_action AS a
LEFT JOIN fj_role_module AS b ON b.id = a.module_id
LEFT JOIN fj_role_nav AS c ON c.id = a.group_id
LEFT JOIN fj_role_nav_group AS d ON d.id = c.nav_id
WHERE
	a.group_id != 0
	AND b.is_effect = 1
	AND c.is_effect = 1
	AND d.is_effect = 1
ORDER BY
	d.sort ASC,
	d.id ASC,
	c.sort ASC,
	c.id ASC
NAVSQL;
		return Yii::$app->db->createCommand($navsql)->queryAll();
	}


	protected function getRoleAccess()
	{
		$role_id = $this->_role_id;
		if (self::_MENU_CACHE_OPEN) {
			if ($cache_data_access = Yii::$app->cache->get(self::_CACHE_ACCESS_DATA_PFIX . $role_id)) {
				return $cache_data_access;
			}
		}
		$roleAccess = $this->__getRoleAccessOptimize($role_id);
		if (self::_MENU_CACHE_OPEN) {
			Yii::$app->cache->set(self::_CACHE_ACCESS_DATA_PFIX . $role_id, $roleAccess, self::_CACHE_TIME);
		}
		return $roleAccess;
	}

	private function __getRoleAccessOptimize($role_id = 0)
	{
		return Yii::$app->db->createCommand("select action_id,module_id from fj_role_access WHERE role_id={$role_id}")->queryAll();
	}


	/**
	 * @note 获取权限认证的菜单项
	 **/
	protected function getAccessNavData($navData, $navAccess)
	{
		if (self::_MENU_CACHE_OPEN) {
			if ($cache_data_menu = Yii::$app->cache->get(self::_CACHE_ROLE_DATA_PFIX.$this->_role_id)) {
				$dataMenu = $cache_data_menu;
				return $dataMenu;
			}
		}

		$temp = array();
		foreach ($navData as $value) {
			if (empty($navAccess)) {
				break;
			}
			foreach ($navAccess as $access_item) {

				if ($value['action_id'] == $access_item['action_id'] && $value['module_id'] == $access_item['module_id']) {

					//设置菜单栏
					if (!isset($temp[$value['menu_id']])) {
						$temp[$value['menu_id']] = [
							'id' => $value['menu_id'],
							'name' => $value['menu_name'],
							'active' => '',
							'group' => [],
						];
					}
					//设置子菜单
					if (!isset($temp[$value['menu_id']]['group'][$value['nav_id']])) {
						$temp[$value['menu_id']]['group'][$value['nav_id']] = [
							'id' => $value['nav_id'],
							'name' => $value['nav_name'],
							'active' => '',
							'icon' => $value['nav_icon'],
							'nodes' => [],
						];
					}
					//设置子菜单连接
					if (!isset($temp[$value['menu_id']]['group'][$value['nav_id']]['nodes'][$value['action_id']])) {
						$temp[$value['menu_id']]['group'][$value['nav_id']]['nodes'][$value['action_id']] = [
							'module' => $value['module'],
							'action' => $value['action'],
							'name' => $value['action_name'],
							'module_id' => $value['module_id'],
							'action_id' => $value['action_id'],
							'active' => '',
							'url' => Url::to([$value['module'] . '/' . $value['action']])
						];
					}

				}
			}
		}
		if ($temp && self::_MENU_CACHE_OPEN) {
			Yii::$app->cache->set(self::_CACHE_ROLE_DATA_PFIX.$this->_role_id, $temp, self::_CACHE_TIME);
		}
		return $temp;
	}

	protected function initMenuData()
	{
		$dataMenu = $this->getNavData();

		$roleAccess = $this->getRoleAccess();

		$accessMenu = $this->getAccessNavData($dataMenu, $roleAccess);

		foreach ($accessMenu as $k => $m) {
			if ($m['group']) {
				foreach ($m['group'] as $kg => $group) {

					if ($group['nodes']) {
						foreach ($group['nodes'] as $kn => $node) {

							if ($node['module'] == $this->_controller_id) {
								$accessMenu[$k]['active'] = true;
								$accessMenu[$k]['group'][$kg]['active'] = true;
							}
							if ($node['module'] == $this->_controller_id && $node['action'] == $this->_action_id) {
								$accessMenu[$k]['group'][$kg]['nodes'][$kn]['active'] = true;
							}
						}
					}
				}
			}
		}

		return $accessMenu;
	}


	/***
	 *  demo menu conf
	 * array (
	 * 0 =>
	 * array (
	 * 'id' => 15,
	 * 'name' => '系统管理',
	 * 'icon' => 'icon-settings',
	 * 'active' => true,
	 * 'group' =>
	 * array (
	 * 0 =>
	 * array (
	 * 'id' => '97',
	 * 'name' => '管理员列表',
	 * 'icon' => '',
	 * 'active' => true,
	 * 'nodes' =>
	 * array (
	 * 0 =>
	 * array (
	 * 'module' => 'admin',
	 * 'action' => 'index',
	 * 'name' => '管理员列表',
	 * 'module_id' => '6',
	 * 'action_id' => '4',
	 * 'active' => true,
	 * 'url' => '/admin/index',
	 * ),
	 * ),
	 * ),
	 * ),
	 * ),
	 * 1 =>
	 * array (
	 * 'id' => 16,
	 * 'name' => '会员管理',
	 * 'icon' => 'icon-user',
	 * 'active' => false,
	 * 'group' =>
	 * array (
	 * 0 =>
	 * array (
	 * 'id' => '99',
	 * 'name' => '维修商家',
	 * 'icon' => 'icon-user-follow ',
	 * 'active' => false,
	 * 'nodes' =>
	 * array (
	 * 0 =>
	 * array (
	 * 'module' => 'mainter',
	 * 'action' => 'index',
	 * 'name' => '维修商家列表',
	 * 'module_id' => '9',
	 * 'action_id' => '6',
	 * 'active' => false,
	 * 'url' => '/mainter/index',
	 * ),
	 * ),
	 * ),
	 * 1 =>
	 * array (
	 * 'id' => '100',
	 * 'name' => '保险商家',
	 * 'icon' => 'icon-user-following',
	 * 'active' => false,
	 * 'nodes' =>
	 * array (
	 * 0 =>
	 * array (
	 * 'module' => 'seller',
	 * 'action' => 'index',
	 * 'name' => '保险商家列表',
	 * 'module_id' => '8',
	 * 'action_id' => '7',
	 * 'active' => false,
	 * 'url' => '/seller/index',
	 * ),
	 * ),
	 * ),
	 * ),
	 * ),
	 * )
	 *
	 ***/


}