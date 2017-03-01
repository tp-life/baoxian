<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/21
 * Time: 15:28
 */
require_once "medoo.php";
require_once "config.php";

class Insurance
{
    private $mysql = null;
    private $config;
    const APPLF = 22;
    const SUCC = 30;
    const WAIT = 10;
    const CANCEL = 0;
    const TIMEOUT = 3;

    public function __construct(Config $config)
    {
        $this->config = $config->db;
        $this->mysql = new medoo($this->config);
    }

    private function getData($l = 0, $limit = 2000)
    {
        $time = time();
        $result = $this->getModel()->select('order_extend(e)', ['[><]order(o)' => ['e.order_id' => 'order_id']], ['o.order_id',
            'o.order_state', 'e.end_time', 'e.policy_number'], ['AND' => ['o.order_state' => self::APPLF, 'e.start_time[<>]' => [10, $time]], 'LIMIT' => [$l, $limit]]);
        return $result;
    }


    private function getWaitPayData($l = 0, $limit = 2000)
    {
        $time = time() - self::TIMEOUT * 24 * 3600;
        $result = $this->getModel()->select('order', '*', ['AND' => ['order_state' => self::WAIT, 'add_time[<=]' => $time], 'LIMIT' => [$l, $limit]]);
        return $result;
    }

    public function run()
    {
        $this->checkInsurance();
    }

    private function getModel()
    {
        if (!$this->mysql) {
            $this->mysql = new medoo($this->config);
        }
        return $this->mysql;
    }

    private function checkInsurance()
    {
        $i = 0;
        $limit = 1000;
        $data = $this->getData($i, $limit);
        if ($data) {
            $order_id = array_column($data, 'order_id');
            $this->mysql->update('order', ['order_state' => self::SUCC], ['order_id' => $order_id]);
        }
        $cancel_data = $this->getWaitPayData($i, $limit);
        if ($cancel_data) {
            $order_id_cancel = array_column($cancel_data, 'order_id');
            $this->mysql->update('order', ['order_state' => self::CANCEL], ['order_id' => $order_id_cancel]);
        }
        unset($data);
        unset($cancel_data);
    }
}

$config = new Config();
$sms = new Insurance($config);
$sms->run();