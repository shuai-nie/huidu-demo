<?php
namespace app\admin\model;

class FirmRelevance extends Base
{
    protected $table = 'hc_firm_relevance';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

}