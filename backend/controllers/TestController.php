<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/14
 * Time: 9:28
 */

namespace backend\controllers;


use backend\components\LoginedController;
use common\library\helper;
use common\models\CardCouponsGrant;
use common\models\CardGrantRelation;
use common\models\CardOrderItem;
use common\models\CardOrderPayback;
use common\models\InsuranceCoverage;
use common\models\OrderExtend;
use common\models\WxMember;
use yii\base\Exception;
use yii\web\Controller;
use Yii;
use yii\web\Cookie;

class TestController extends LoginedController
{

    public function actionIndex(){

    }








    public function actionCard(){
        return;
        set_time_limit(0);
//        $this->change();
        $model = CardCouponsGrant::find()->select('seller_id,coverage_code,coverage_id,count(*) as count')->where('seller_id > 1 and status = 0  ')->groupBy('coverage_code,seller_id')->asArray()->all();

        foreach ($model as $val ){
            $coverage= InsuranceCoverage::findOne(['coverage_code'=>$val['coverage_code']]);
            if(!$coverage) continue;
            $transaction = Yii::$app->getDb()->beginTransaction();
            try{
                set_time_limit(0);
                $find_cards = CardCouponsGrant::find()->where(['seller_id'=>$val['seller_id'],'coverage_code'=>$val['coverage_code'],'status'=>0])->asArray()->all();
                $cards=array_unique(array_column($find_cards,'card_number'));
                if(!$cards){
                    throw new Exception('卡券号输入错误');
                }
                $seller_id = $val['seller_id'];
                if(count($cards) > 300){
                    $i = ceil(count($cards) % 300);

                    for ($j=0;$j<$i;$j++){
                        $k = $j * 300;
                        $c=array_slice($cards,$k,300);
                        if(!$c) continue;
                        $this->hand($seller_id,$c,$val['coverage_code'],$val,$coverage);
                        usleep(20);
                    }
                    $transaction->commit();
                    continue;
                }else{
                    if($this->hand($seller_id,$cards,$val['coverage_code'],$val,$coverage)){
                        $transaction->commit();
                        continue;
                    }
                }

                throw new Exception('卡券发放失败');
            }catch (Exception $e){
                $this->fileLog($val);
                $transaction->rollBack();
                var_dump($e->getMessage());
            }
            usleep(30);
        }
    }


    private  function hand($seller_id,$cards,$coverage,$val,$coverage_info){
        $data=[
            'apply_type'=>3,
            'from_seller_id'=>1,
            'to_seller_id'=>$val['seller_id'],
            'num' =>count($cards),
            'total_price' => floatval($coverage_info->wholesale_price * $val['count']),
            'coverage_code'=>$val['coverage_code'],
            'price'=>$coverage_info->wholesale_price
        ];
        //创建领用订单
        $b=$this->create($data,Yii::$app->user->identity->id);
        if(!$b){
            throw new Exception('订单创建失败！');
        }
        //验证卡券号是否与申请数相等
        $check_number= $this->checkNumber($b,$coverage,$cards,$seller_id);
        if(!$check_number['status']){
            throw new Exception($check_number['msg']);
        }
        $check_number['data']['content'] ='主动发放导入卡券';
        $check_number['data']['t'] =0;
        $tj=['seller_id'=>$seller_id,'coverage_code'=>$coverage,'status' => 0,'card_number' =>$cards];
        $bstop=$this->_merage($tj,$check_number['data']);
        if($bstop){
            return true;
        }
    }

    /**
     * 主动创建订单
     * @param array $data
     * @return bool|string
     */
    public  function create($data = [],$uid=0)
    {
        try {
            $model = new CardOrderPayback;
            $model->pay_sn = helper::_makeOrderSn($uid);
            $model->pay_status = 1;
            $model->from_seller_id = $data['from_seller_id'];
            $model->to_seller_id = $data['to_seller_id'];
            $model->num = $data['num'];
            $model->total_price = $data['total_price'];
            $model->add_time = time();
            if ($model->save()) {
                $item = new CardOrderItem();
                $item->pay_sn = $model->pay_sn;
                $item->coverage_code = $data['coverage_code'];
                $item->number = $model->num;
                $item->price = $data['price'];
                $item->status = 0;
                $item->add_time = time();
                if ($item->save()) {
                    return $model->pay_sn;
                }
            }
            throw new Exception('错误');
        } catch (Exception $e) {
            return false;
        }
    }


    /**
     * 检查卡券数量与当前订单是否相匹
     * @param string $pay_sn
     * @param int $num
     * @return array
     */
    public   function checkNumber($pay_sn='',$code='',$card=[],$seller_id = 0){
        $table=CardOrderItem::tableName();
        $t_b = CardOrderPayback::tableName();
        $model_card_item=CardOrderPayback::find()->select('*')->innerJoin($table,$t_b.'.pay_sn = '.$table.'.pay_sn')
            ->where([
                $table.'.pay_sn'=>$pay_sn,
                $table.'.coverage_code'=>$code,
                $table.'.status'=>[0,1]
            ])->asArray()->one();
        if(!$model_card_item){
            return ['status'=>false,'msg'=>'当前卡券申请不存在！'];
        }
        $num= count($card);
        if($num != $model_card_item['number']){
            return ['status'=>false,'msg'=>'商家领用卡券'.$model_card_item['number'].'张，当前分配卡券 '.$num .'张'];
        }
        $where=[
            'seller_id' => $seller_id?$seller_id:$model_card_item['from_seller_id'],
            'status' => 0,
            'coverage_code' => $model_card_item ['coverage_code'],
            'card_number' =>$card
        ];
        $count = CardCouponsGrant::find()->where($where)->count('id');
//        if(!$count){
//            return ['status'=>false,'msg'=>'当前卡券不可用！'];
//        }else if($count < $num){
//            return ['status'=>false,'msg'=>'当前输入的卡券中可用卡券小于商家购买数量！请检查是否包含已经发放过的卡券'];
//        }

        return ['status'=>true,'data'=>$model_card_item];
    }

    private function change(){
        $result = CardCouponsGrant::find()->groupBy('coverage_code')->asArray()->all();
        foreach ($result as $val){
            $coverage = InsuranceCoverage::findOne(['coverage_code'=>$val['coverage_code']]);
            if(!$coverage) continue;
            $sql2 = 'update '.CardCouponsGrant::tableName().' set coverage_id = '.$coverage->id .' where coverage_code = \''.$coverage->coverage_code.'\'';
            $ret2 = Yii::$app->getDb()->createCommand($sql2)->execute();
        }
    }

    /**
     * 合并卡券
     * @param array $tj
     * @param array $data
     * @return bool
     * @throws \yii\db\Exception
     */
    public    function _merage($tj=[],$data=[]){
        $list = CardCouponsGrant::find()->where($tj)->asArray()->all();
        $to_seller_id = $data['to_seller_id'];
        $id_s = '';
        $transaction = Yii::$app->getDb()->beginTransaction();
        foreach($list as $v){
            $id_s .= $v['id'].',';
        }
        $id_s = trim($id_s,',');
        try{
            $sql2 = 'update '.CardCouponsGrant::tableName().' set seller_id = '.$to_seller_id .' where id in('.$id_s.')';
//            $ret2 = Yii::$app->getDb()->createCommand($sql2)->execute();
            $ret2 =true;
            $model_create = new CardGrantRelation();
            $ret3=$model_create ->createCard($list,$data);
            if($ret2 && $ret3){
                $transaction->commit();
                return true;
            }
            throw new Exception('error');
        }catch (Exception $e){
            $transaction->rollBack();
            return false;
        }
    }


    private function fileLog($data=[])
    {

        file_put_contents(Yii::getAlias('@runtime').'/logs/import_card.log',var_export($data,true)."\r\n",FILE_APPEND);
    }
}