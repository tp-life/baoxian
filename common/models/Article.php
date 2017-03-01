<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%_article}}".
 *
 * @property string $id
 * @property string $category_id
 * @property string $coverage_type_id
 * @property string $tag_id
 * @property string $title
 * @property string $author
 * @property string $content
 * @property integer $status
 * @property integer $sort
 * @property integer $hit
 * @property string $add_time
 */
class Article extends ActiveRecord
{

    /**
     * 保险系列类型值，1 保险详情，2 投保须知  3 理赔须知
     */
    const COVEAGE_INFO = 1;
    const COVEAGE_INSUE =2;
    const COVEAGE_CLAIMS = 3;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_article}}';
    }

    public function behaviors(){
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['add_time'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id','coverage_type_id', 'tag_id', 'sort', 'hit','status'], 'integer'],
            [['content', 'category_id', 'title','status'], 'required'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 50],
            [['author'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'category_id' => '分类',
            'coverage_type_id' => '保险类型',
            'tag_id' => '文章标签',
            'title' => '标题',
            'author' => '上传者',
            'content' => '文章内容',
            'sort' => '排序',
            'hit' => '访问量',
            'status' => '状态',
            'add_time' => '添加时间',
        ];
    }

    /**
     * 获取所在分类
     */
    public function getArticleCategory($id){
        //得到所在分类
        $list = ArticleCategory::parentList($id);
        $list = array_reverse($list);
        $title_arr = [];
        foreach ($list as $item) {
            $title_arr[] = $item['title'];
        }
        return implode('->', $title_arr);
    }
}
