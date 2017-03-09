<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/6
 * Time: 9:34
 */
require_once "medoo.php";
require_once "config.php";

class statistics
{
    public $mysql, $config;
    const ORDER_STATUS = 40; //订单完成状态，以此判定是否理赔完成
    //卡券状态 3 冻结，2失效 1 激活 0 可用
    const ACTIVE_STOP = 3,
        ACTIVE_ERROR = 2,
        ACTIVE_SUCC = 1,
        ACTIVE_WAIT = 0;
    const LEHUANXIN = [1];


    public function __construct($config)
    {
        $this->config = $config->db;
        $this->mysql = new medoo($this->config);
    }


    public function run()
    {
        $this->handleInsert();
    }

    private function handleInsert()
    {
        $insert = $this->getInsertDate();
        $this->mysql->insert('seller_statistics', $insert);
    }

    private function getInsertDate()
    {
        $result = $this->mysql->select('seller', '*', ['AND' => ['is_insurance' => 1,'seller_id[!]' => self::LEHUANXIN]]);
        $card = $this->getData();
        $insert = [];
        foreach ($result as $val) {
            $seller_info = [
                'seller_id' => $val['seller_id'],
                'add_time' => time()
            ];
            $total = 0;
            foreach ($card as $v) {
                if ($v['seller_id'] == $val['seller_id']) {
                    if ($v['active_status'] == self::ACTIVE_STOP) {
                        $seller_info['frost_num'] = $v['total'];
                    }
                    if ($v['active_status'] == self::ACTIVE_ERROR) {
                        $seller_info['lsoe_num'] = $v['total'];
                    }
                    if ($v['active_status'] == self::ACTIVE_WAIT) {
                        $seller_info['wait_num'] = $v['total'];
                    }
                    if ($v['active_status'] == self::ACTIVE_SUCC && $v['order_state'] == self::ORDER_STATUS) {
                        $seller_info['settle_num'] = $v['total'];
                    }
                    if ($v['active_status'] == self::ACTIVE_SUCC && !$v['order_state']) {
                        $seller_info['active_num'] = $v['total'];
                    }
                    $total += $v['total'];
                }

            }
            $seller_info['card_num'] = $total;
            $insert[] = $seller_info;
        }
        return $insert;
    }


    /**
     * 通过发放关系查询商家所拥有的卡券信息
     * @return array
     */
    private function getData()
    {
        $status = self::ORDER_STATUS;
        $sql = <<<STS
SELECT
	c.`status` as active_status,
	o.`order_state`,
	COUNT(r.id) AS total,
    r.seller_id,
    r.pid,
    r.province_id,
    r.city_id,
    r.area_id,
    r.area_info,
    r.detail_address
FROM
	`fj_card_coupons_grant` AS c  
INNER JOIN (
	SELECT
			t.*
	FROM
		(
			SELECT
				re.`id`,
				`card_number`,
				`to_seller_id`,
				se.*
			FROM
				`fj_card_grant_relation` as  re
			LEFT JOIN fj_seller as se  ON re.to_seller_id = se.seller_id
			WHERE
				(
					`to_seller_id` = se.seller_id
					OR `from_seller_id` = se.seller_id
				) 		
				    AND se.is_insurance = 1
			ORDER BY
				re.`id` DESC
		) AS t
	GROUP BY
		t.`card_number`
	ORDER BY
		t.`id` DESC
) AS r ON r.`card_number` = c.`card_number`
LEFT JOIN `fj_order` AS o ON o.`order_id` = c.`order_id`
AND o.`order_state` = {$status}
GROUP BY
	r.seller_id,
	c.`status`,
	o.`order_state`
STS;
        $result = $this->mysql->query($sql)->fetchAll();
        return $result;
    }
}

$config = new Config();
$statistics = new statistics($config);
$statistics->run();