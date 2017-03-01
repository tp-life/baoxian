<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_article_category}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $brief
 * @property integer $pid
 * @property integer $is_effect
 * @property integer $sort
 */
class ArticleCategory extends \yii\db\ActiveRecord
{

	const TYPE_COMMON = 0;
	const TYPE_HELP = 1;
	const TYPE_NOTICE = 2;
	const TYPE_SYSTEM = 3;
	const TYPE_INSURANCE = 4;
    public $deep;

	public static $articleType = array(
		self::TYPE_COMMON => '普通文章',
		self::TYPE_HELP => '帮助文章',
		self::TYPE_NOTICE => '公告文章',
		self::TYPE_SYSTEM => '系统文章',
		self::TYPE_INSURANCE => '保险系列'
	);

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_article_category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'brief', 'pid', 'is_effect', 'sort'], 'required'],
            [['pid', 'is_effect', 'sort'], 'integer'],
            [['title', 'brief'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title' => '分类名称',
            'brief' => '分类简介',
            'pid' => '上级分类',
            'is_effect' => '状态',
            'sort' => '排序',
        ];
    }

    /**
     * 类别列表
     *
     * @param array $condition 检索条件
     * @return array 数组结构的返回结果
     */
    public function articleCategoryList($condition){
        return self::findALL($condition);
    }

    /**
     * @param $id
     * 根据节点ID获取所有父级节点
     */
    public static function parentList($id){
        $list[] = $item = self::find()->where(['id'=>$id])->asArray()->one();
        if ($item['pid']) {
            $tpls = self::parentList($item['pid']);
            foreach ($tpls as $tpl) {
                $list[] = $tpl;
            }
        }
        return $list;
    }

    public static function getChildrenIds($id){
        $idsArr[] = $id;
        $result = self::findALL(['pid'=>$id]);
        if ($result){
            foreach ($result as $key=>$val){
                $idsArr[] = $val['id'];
                $idsArr[] = self::getChildrenIds($val['id']);
            }
        }
        $ids = implode(",",$idsArr);
        $idsArr = explode(",",$ids);
        $ids = implode(",",array_unique($idsArr));
        return $ids;
    }

    /**
     * @param int $show_deep  数的深度
     * 获取分类数
     */
    public function treeArticleCategoryList($show_deep = 2) {
        $list = self::findALL(['is_effect'=>1]);
        $show_deep = intval($show_deep);
        $result = array();
        if(is_array($list) && !empty($list)) {
            $result = $this->_getTreeClassList($show_deep,$list);
        }
        return $result;
    }

    /**
     * 递归 整理分类
     *
     * @param int $show_deep 显示深度
     * @param array $class_list 类别内容集合
     * @param int $deep 深度
     * @param int $parent_id 父类编号
     * @param int $i 上次循环编号
     * @return array $show_class 返回数组形式的查询结果
     */
    private function _getTreeClassList($show_deep,$class_list,$deep=1,$parent_id=0,$i=0){
        static $show_class = array();//树状的平行数组
        if(is_array($class_list) && !empty($class_list)) {
            $size = count($class_list);
            if($i == 0) $show_class = array();//从0开始时清空数组，防止多次调用后出现重复
            for ($i;$i < $size;$i++) {//$i为上次循环到的分类编号，避免重新从第一条开始
                $val = $class_list[$i];
                $id = $val['id'];
                $pid   = $val['pid'];
                if($pid == $parent_id) {
                    $val['deep'] = $deep;
                    $show_class[] = $val;
                    if($deep < $show_deep && $deep < 3) {//本次深度小于显示深度时执行，避免取出的数据无用
                        $this->_getTreeClassList($show_deep,$class_list,$deep+1,$id,$i+1);
                    }
                }
//                if($pid > $parent_id) break;//当前分类的父编号大于本次递归的时退出循环
            }
        }
        return $show_class;
    }
}
