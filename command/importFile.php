<?php
//error_reporting(0);
require_once "medoo.php";
class ImportFile{
    private $company=[];
    private $seller=[];
    private $type=[];
    private $coverage=[];
    private $mysql;
    private $path='file/';
    private $config = [
        'database_type' => 'mysql',
        'database_name' => 'baoxian_new',
        'server' => '192.168.0.231',
        'username' => 'root',
        'password' => 'CDjd_pw231*',
        'charset' => 'utf8',
        'port' => 3306,
        'prefix' => 'fj_',];
	private $coverage_info=[];
    public function __construct()
    {
        set_time_limit(0);
        $this->mysql=new  medoo($this->config);
//         $this->getSeller();
//         $this->checkCard();
//         $this->getCard();
    }

    public function run(){
        $this ->insertCompany();
        $this->insertType();
        $this->insertCoverage();
        $this->insertCard();
        $this->getCard();
    }

    private function insertCompany(){
        $file = $this->path.'company.csv';
        $content = $this->readFile($file);
        foreach ($content as $key=>$val){
            $val = mb_convert_encoding($val, "UTF-8", "GBK");
            $temp = explode(',',$val);
            if(!$temp[0]) continue;
            $insert_data=[
                'name'=>$temp[1],
                'logo'=>$temp[2],
                'sp'    =>$temp[3],
                'contact_name'=>$temp[4],
                'contact_phone'=>$temp[5],
                'p_id'=>$temp[6],
                'c_id'=>$temp[7],
                'a_id'=>$temp[8],
                'address_detail'=>$temp[9],
                'note'=>$temp[10],
                'insurance_number'=>0,
                'status'=>1,
                'created'=>time()
            ];
            $id=$this->mysql->insert('insurance_company',$insert_data);
            $this->company[$temp[0]]=['name'=>$temp[1],'id'=>$id,'name'=>$temp[1]];
        }
        $this->log($this->company,'company_id.log');
    }

    private function insertType(){
        $file = $this->path.'type.csv';
        $content = $this->readFile($file);
        foreach ($content as $key=>$val){
            $val = mb_convert_encoding($val, "UTF-8", "GBK");
            $temp = explode(',',$val);
            if(!$temp[0]) continue;
            $insert_data=[
                'type_name'=>$temp[1],
                'type_code'=>$temp[2],
                'note'    =>$temp[3],
                'insurance_number'=>0,
                'status'=>1,
                'created'=>time()
            ];
            $id=$this->mysql->insert('insurance_type',$insert_data);
            $this->type[$temp[0]]=['name'=>$temp[1],'id'=>$id];
        }
        $this->log($this->type,'type_id.log');
    }

    private function insertCoverage(){
        $file = $this->path.'coverage.csv';
        $content = $this->readFile($file);
        $type_number=[];
        $company_number=[];
//        $this->setData();
        foreach ($content as $key=>$val){
            $val = mb_convert_encoding($val, "UTF-8", "GBK");
            $temp = explode(',',$val);

            if(!$temp[0]) continue;
            if(isset($type_number[$this->type[$temp[3]]['id']])){
                $type_number[$this->type[$temp[3]]['id']] ++;
            }else{
                $type_number[$this->type[$temp[3]]['id']] = 1;
            }
            if(isset($company_number[$this->company[$temp[1]]['id']])){
                $company_number[$this->company[$temp[1]]['id']] ++;
            }else{
                $company_number[$this->company[$temp[1]]['id']] = 1;
            }
            $insert_data=[
                'company_id'=>$this->company[$temp[1]]['id'],
                'company_name'=>$this->company[$temp[1]]['name'],
                'type_id'    =>$this->type[$temp[3]]['id'],
                'type_name'=>$this->type[$temp[3]]['name'],
                'coverage_name'=>$temp[5],
                'period'=>$temp[6],
                'cost_price'=>$temp[7],
                'official_price'=>$temp[8],
                'wholesale_price'=>$temp[14],
                'max_payment'=>$temp[9],
                'coverage_code'=>$temp[10],
                'status'=>1,
                'is_more'=>0,
                'image'=>'',
                'add_time'=>time(),
                'note'=>$temp[12]
            ];
            $id=$this->mysql->insert('insurance_coverage',$insert_data);

            foreach ($company_number as $key=>$val){
                $this->mysql->update('insurance_company',['insurance_number'=>$val],['id'=>$key]);
            }
            foreach ($type_number as $key=>$val){
                $this->mysql->update('insurance_type',['insurance_number'=>$val],['id'=>$key]);
            }
            $this->coverage[$temp[10]]=['name'=>$temp[5],'id'=>$id,'old_id'=>$temp[0],'coverage_code'=>$temp[10],'type_id'=>$this->type[$temp[3]]['id'],'company_id'=>$this->company[$temp[1]]['id']];

        }
        $this->log($this->coverage,'coverage_id.log');
    }

    private function insertCard(){
        $file = $this->path.'card_1.csv';
        $file2= $this->path.'card_2.csv';
        $content = $this->readFile($file);
        $content_2 = $this->readFile($file2);
        unset($content[0]);
        unset($content_2[0]);
        $content = array_merge($content,$content_2);
//        $this->setData();
        $this->getSeller();
        foreach ($content as $val){
            $val = mb_convert_encoding($val, "UTF-8", "GBK");
            $temp = explode(',',$val);
            if(!$temp[0]) continue;
            $seller_id = $this->getSellerId($temp[4]);
            $card_num = trim($temp[0]);
            $len = 7 -strlen($card_num);
            for($i=0;$i < $len;$i++){
                 $card_num ='0'.$card_num;
            }
            $insert_data=[
                'card_number'=>$card_num,
                'card_secret'=>trim($temp[1]),
                'seller_id'  =>$seller_id,
                'status'=>0,
                'active_time'=>0,
                'ymd'=>0,
                'order_id'=>0,
                'coverage_id'=>$this->coverage[$temp[2]]['id'],
                'coverage_code'=>$temp[2],
                'type_id'=>$this->coverage[$temp[2]]['type_id'],
                'company_id'=>$this->coverage[$temp[2]]['company_id'],
                'created'=>$temp[3]
            ];
            $id=$this->mysql->insert('card_coupons_grant',$insert_data);
//            var_dump($insert_data);
            usleep(5);
        }

    }

    private function getCard(){
        $result =$this->getCardByShopnc();
//         $this->setData();
        $error=[];
        $seller_info = [];
        $card_info=[];
       foreach ($result as $key=>$val){
           $len = 7 - strlen($val['card_number']);
           for($i=0;$i < $len;$i++){
               $card_num ='0'.$val['card_number'];
           }
           $seller_id = $this->getSellerId($val['company_name']);
           $coverage = $this->getCoverage($val['coverage_code']);
           if(!$coverage) continue;
           $seller_info[$seller_id]['name']=$val['company_name'];
           $seller_info[$seller_id]['time']=$val['ymd'];
           $seller_info[$seller_id]['coverage'][$coverage['id']]=$coverage;

           if(isset($seller_info[$seller_id]['total'])){
	           	$seller_info[$seller_id]['total']++;
           }else{
           		$seller_info[$seller_id]['total'] = 0;
           }

           
           if(isset($seller_info[$seller_id]['num'][$coverage['id']])){
           		$seller_info[$seller_id]['num'][$coverage['id']] ++;
           }else{
           		$seller_info[$seller_id]['num'][$coverage['id']] = 0;
           }
           $seller_info[$seller_id]['total'] += $coverage['wholesale_price'];
           	$insert_data=[
               'card_number'=>$card_num,
               'card_secret'=>$val['card_secret'],
               'seller_id'  =>$seller_id,
               'status'=>0,
               'active_time'=>0,
               'ymd'=>0,
               'order_id'=>0,
               'coverage_id'=>$coverage['id'],
               'coverage_code'=>$val['coverage_code'],
               'type_id'=>$coverage['type_id'],
               'company_id'=>$coverage['company_id'],
               'created'=>date('Y-m-d H:i:s',$val['ymd'])
           ];
           	$id=$this->mysql->insert('card_coupons_grant',$insert_data);
           	$card_info[$id]=['card_number'=>$card_num,'coverage_code'=>$val['coverage_code'],'seller_id'=>$seller_id];
           	
            if(!$id){
                $error[]=['id'=>$val['id'],'card_number'=>$val['card_number'],'error'=>$this->mysql->error()];
            }
           usleep(5);
       }
       
       $this->orderPay($seller_info,$card_info);
        $this->log($error,'error_card.log');
    }
    
//     private function 
    
    private  function orderPay($seller=[],$result=[]){
    	if(!$seller) return false;
    	$data=[];
    	foreach ($seller as $key=>$val){
    		$order_sn = $this->_makeOrderSn(1);
    		$insert_data=[
    				'pay_sn'=>$order_sn,
    				'pay_status'=>0,
    				'apply_type'  =>3,
    				'handle_type'=>0,
    				'from_seller_id'=>1,
    				'to_seller_id'=>$key,
    				'num'=>array_sum($val['num']),
    				'total_price'=>$val['total'],
    				'send_total_price'=>$val['total'],
    				'received_price'=>'',
    				'back_price'=>'',
    				'real_back_price'=>'',
    				'add_time'=>$val['time']
    		];
    		$id=$this->mysql->insert('card_order_payback',$insert_data);
    		foreach ($val['coverage'] as $v){
    			$item=['pay_sn'=>$order_sn,'coverage_code'=>$v['coverage_code'],'number'=>$val['num'][$v['id']],'price'=>$v['wholesale_price'],
    					'status'=>2,'add_time'=>$val['time']
    			];
    			$order_id = $this->mysql->insert('card_order_item',$item);
    			if($order_id){
    				$data[$v['coverage_code']] = ['order_id'=>$order_id,'sn'=>$order_sn,'seller_id'=>$key,'time'=>$val['time']];
    			}
    		}
    		usleep(5);
    	}
    	foreach($result as $k=>$value){
    		if(!isset($data[$value['coverage_code']])) continue;
    		$rs = ['card_id'=>$k,'card_number'=>$value['card_number'],'order_id'=>$data[$value['coverage_code']]['order_id']
    				,'pay_sn'=>$data[$value['coverage_code']]['sn'],'from_seller_id'=>1,'to_seller_id'=>$value['seller_id']
    				,'add_time'=>$data[$value['coverage_code']]['time'],'deadline'=>0
    		];
    		$_id = $this->mysql->insert('card_grant_relation',$rs);
    	}
    }

    
    
    private function checkCard(){
        $result =$this->getCardByShopnc();
        $card_id = array_column($result,'card_secret');
        $card_now = $this->getCardByNow();
        $card_id_now = array_column($card_now,'card_secret');
        array_diff($card_id,$card_id_now);
    }

    private function  getCardByShopnc(){
        $mysql= $this -> getModel();
//        $old_seller_ids = [21,30,36,40,41,42,43,44,46,47, 48, 50,52,53,54, 55,56, 57, 58, 63, 64, 65, 66, 67,
//            68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 89, 92, 93, 94, 97, 98, 99, 101,
//            102, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116,117, 118, 119, 120, 121, 122, 124, 125, 126, 127];
//        $result=$mysql->select('card_coupons_grant(c)',['[><]maintenance(m)'=>['c.m_id'=>'m_id']],['c.card_number',
//            'c.card_secret','c.m_id','c.status','c.coverage_id','c.coverage_code','m.company_name','c.ymd','c.id'],['AND'=>['c.m_id'=>$old_seller_ids,
//            'c.coverage_id[>]'=>0,'c.status'=>0,'c.id[<]'=>49100,'c.coveage_id'=>array(5,8,9,10,11,12,13,14,15,21,22)
//        ]]);
//        var_dump($mysql->last_query());
		$sql = "select c.card_number,c.card_secret,c.m_id,c.status,c.coverage_id,c.coverage_code,m.company_name,c.ymd,c.id 
				from shopnc_card_coupons_grant as c INNER JOIN shopnc_maintenance as m ON c.m_id = m.m_id 
				where  c.coverage_id in(5,8,9,10,11,12,13,14,15,21,22) 
				and c.m_id  in (21,30,36,40,41,42,43,44,46,47,48,50,52,53,54,55,56,57,58,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,89,92,93,94,97,98,99,101,102,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,124,125,126,127) 
				and c.id < 49100 and LENGTH(c.card_number)=6  
				order by c.id desc";
        $sql ="select c.card_number,c.card_secret,c.m_id,c.status,c.coverage_id,c.coverage_code,m.company_name,c.ymd,c.id 
				from shopnc_card_coupons_grant as c INNER JOIN shopnc_maintenance as m ON c.m_id = m.m_id 
        		where c.m_id IN(37,45) and c.order_id = 0 and c.active_time=2";
		$result = $mysql->query($sql)->fetchAll();
        
       return $result;
    }

    private function getCardByNow(){
        return $this->mysql->select('card_coupons_grant','*');
    }

    private function getModel(){
        $config = [
            'database_type' => 'mysql',
            'database_name' => 'from68_eagle',
            'server' => '120.26.42.68',
            'username' => 'jdrx_yfgg',
            'password' => 'JD_pt123wx*',
            'charset' => 'utf8',
            'port' => 32206,
            'prefix' => 'shopnc_'];
        $mysql =new medoo($config);
        return $mysql;
    }

    private function setData(){
        $company_str='$company = '.file_get_contents($this->path.'company_id.log').';';
        $type_str ='$type = '.file_get_contents($this->path.'type_id.log').';';
        $c= trim(file_get_contents($this->path.'coverage_id.log'));
        $coverage_str ='$coverage = '.$c.';';
        eval($company_str);
        eval($type_str);
        $this->company =$company;
        $this->type = $type;
        if($c){
            eval($coverage_str);
            $this->coverage=$coverage;
        }
    }

    private function readFile($file=''){
        if(!$file) return false;
        $handle = fopen($file,'rb');
        $content=[];
        while (!feof($handle)) {
            $line = fgets($handle);
            $line = trim($line);
            if(!preg_match("/^[\w,\/-]*$/",$line)) continue;
            if(!$line) continue;
            $content[]=$line;
        }
        return $content;
    }

    private function getSeller(){
        $data = $this->mysql->select('seller','*');
        $this->seller = $data;
    }

    private function  getSellerId($seller_name=''){
        foreach ($this->seller as $val){
            if($seller_name == $val['seller_name']){
                return $val['seller_id'];
            }
        }
        return 1;
    }

    private function log($data=[],$name='log.log'){
        if(is_array($data))
            $data = var_export($data,true);
        file_put_contents($this->path.$name,$data);
    }

    private function getCoverage($coverage_code=''){
    	if(!$this->coverage_info){
    		$this->coverage_info = $this->mysql->select('insurance_coverage','*');
    	}
    	foreach ($this->coverage_info as $val){
    		if($coverage_code == $val['coverage_code']){
    			return $val;
    		}
    	}
    	return false;
    }
    
    private function  _makeOrderSn($member_id=0) {
    	return mt_rand(10,99)
    	. sprintf('%010d',time() - 946656000)
    	. sprintf('%03d', (float) microtime() * 1000)
    	. sprintf('%03d', (int) $member_id % 1000);
}

    public function test(){
        $reg ="/^[\w,\/-]*$/";
        $str ="290535462600291000,822022016510922000019,2016-12-20,201d7-12-19";
        $c=!preg_match($reg, $str,$a);
    }
    
    public  function getSql(){
           $this->getSqlSucc();
           $this->getSqlEndTime();
           $this->getSqlCancel();
    }
    
    private function getSqlSucc(){
        $file = $this->path.'success.csv';
        $data = $this->readFile($file);
        $model = $this->getModel();
        $sql='';
        foreach ($data as $val){
            $map = explode(',', $val);
            $code = trim($map[0]);
            
            $sn = trim($map[1]);
            $result = $model->get('vr_order_safe_common','*',['imei_code'=>$code]);
            if(!$result['order_id']) continue;
            $sql .="UPDATE shopnc_vr_order_safe_common SET state = 1,start_time = ".strtotime($map[2]).",end_time = ".strtotime($map[3])." WHERE imei_code = '{$code}';\r\n";
            $sql .="UPDATE shopnc_vr_order SET order_state = 30,plyno = '{$sn}' WHERE order_id = {$result['order_id']};\r\n";
//             var_dump($result['order_id']);
        }
        $this->log($sql,'succ.sql');
    }
    
    private function getSqlEndTime(){
        $file = $this->path.'endtime.csv';
        $data = $this->readFile($file);
        $model = $this->getModel();
        $sql='';
        foreach ($data as $val){
            $map = explode(',', $val);
            $code = trim($map[0]);
            $result = $model->get('vr_order_safe_common','*',['imei_code'=>$code]);
            if(!$result['order_id']) continue;
            $sql .="UPDATE shopnc_vr_order_safe_common SET state = 1,end_time = ".strtotime($map[1])." WHERE imei_code = '{$code}';\r\n";
            $sql .="UPDATE shopnc_vr_order SET order_state = 30 WHERE order_id={$result['order_id']};\r\n";
        }
        $this->log($sql,'endtime.sql');
    }
    
    private function getSqlCancel(){
        $file = $this->path.'cancel.csv';
        $data = $this->readFile($file);
        $model = $this->getModel();
        $sql='';
        foreach ($data as $val){
            $map = explode(',', $val);
            $code = trim($map[0]);
            $result = $model->get('vr_order_safe_common','*',['imei_code'=>$code]);
            if(!$result['order_id']) continue;
            $sql .="UPDATE shopnc_vr_order SET order_state = 0 WHERE order_id = {$result['order_id']};\r\n";
        }
        $this->log($sql,'cancel.sql');
    }
}
$import = new ImportFile();
//$import->run();
$import->getSql();


