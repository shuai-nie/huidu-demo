<?php

namespace app\admin\model;

use think\Db;
use think\Model;

class Resource extends Base
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public function selectCount($map)
    {
        $map['status'] = 1;
        $map['auth'] = 1;
        $data=  $this->where($map)->count();
        //echo $this->getLastSql() . "\n\t";
        return $data;
    }

    // 合作区域
    public function selectRegionCount($id)
    {
        // SELECT * from (select id,REPLACE(region, '|', ',') as region FROM hc_resource  ) as t  where FIND_IN_SET(12, region )
        //return $this->where($map)->count();
        return Db::query("SELECT count(*) AS count from (select id,status,auth,REPLACE(region, '|', ',') as region FROM hc_resource  ) as t where status=1 and auth = 1 and  FIND_IN_SET(?, region )", [$id]);
    }

    // 行业细分
    public function selectSubdivideCount($id)
    {
        // SELECT * from (select id,REPLACE(region, '|', ',') as region FROM hc_resource  ) as t  where FIND_IN_SET(12, region )
        //return $this->where($map)->count();
        return Db::query("SELECT count(*) AS count_as from (select id,status,auth,REPLACE(industry_subdivide, '|', ',') as industry_subdivide FROM hc_resource  ) as t where status=1 and auth = 1 and  FIND_IN_SET(?, industry_subdivide )", [$id]);
    }
}
