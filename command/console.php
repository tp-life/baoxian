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
        $limit = 200;
        $mysql =$this->getModel();
        $data = $this->getSms($i, $limit);
        if (!$data) {
            return false;
        }
        $succ = [];
        $insert = [];
        foreach ($data as $value) {
            if (sms::sendSMS($value['phone'], $value['content'], $value['type'], [], true)) {
                $succ[] = $value['id'];
                $value['send_time'] = date('Y-m-d H:i:s');
                unset($value['id']);
                $insert[] = $value;
            }
        }
        if ($insert) {
            $mysql->insert('sms_log', $insert);
            $mysql->delete('sms_queue', ['id' => $succ]);
        }
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
