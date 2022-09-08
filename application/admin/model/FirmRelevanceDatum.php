<?php
namespace app\admin\model;

class FirmRelevanceDatum extends Base
{
    protected $table = 'hc_firm_relevance_datum';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public $type = [
        1 => '企业网站验证',
        2 => '企业官方社区验证',
        3 => '企业官方邮政验证',
        4 => '企业工牌/名片验证',
        5 => '公司相关证件验证',
    ];

}