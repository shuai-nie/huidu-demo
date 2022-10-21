<?php
namespace app\admin\model;

class FirmRelevance extends Base
{
    protected $table = 'hc_firm_relevance';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $insert = ['create_id', 'update_id', 'isweb' => 2];
    protected function setCreateIdAttr()
    {
        return getLoginUserId();
    }

    protected function setUpdateIdAttr()
    {
        return getLoginUserId();
    }

}