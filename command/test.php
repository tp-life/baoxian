<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/7
 * Time: 17:05
 */
require_once "medoo.php";
require_once "sms.php";
require_once "config.php";

class Console
{


    private $mysql;
    private $config;

    public function __construct(Config $config)
    {
        set_time_limit(0);
        $this->config = $config->db;
    }

    private function getSms($l = 0, $limit = 2000)
    {
        return $this->getModel()->select('sms_queue', '*', ['LIMIT' => [$l, $limit]]);
    }

    private function getModel()
    {
        if (!$this->mysql) {
            $this->mysql = new medoo($this->config);
        }
        return $this->mysql;
    }

    public function sendSms()
    {
        $i = 0;
        $limit = 2;
        $mysql =$this->getModel();
        $data = $this->getSms($i, $limit);
        if (!$data) {
            return false;
        }
        $succ = [];
        $insert = [];
        foreach ($data as $value) {
            if (sms::sendSMS('18080093730', $value['content'], $value['type'], [], true)) {
                $succ[] = $value['id'];
                $value['send_time'] = date('Y-m-d H:i:s');
                unset($value['id']);
                $insert[] = $value;
            }
        }
        $string = var_export($insert,true);
        echo "成功了,发送了的信息是：".$string."\r\n要删除的ID是：".var_export($succ,true);
        unset($data);
        unset($succ);
        unset($insert);
        unset($mysql);
        $this->mysql = null;

    }
}

$config = new Config();
$sms = new Console($config);
$sms->sendSms();
