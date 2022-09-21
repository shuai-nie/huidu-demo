<?php
namespace app\admin\model;

class Adsense extends Base
{
    protected $connection = [
        'prefix' => 'mk_',
    ];

    /**
     *
     * 11首页·顶部横幅+左右三联
     * 12首页·弹窗
     * 13首页·底部悬浮
     * 14首页·上横幅大
     * 15首页·上横幅中
     * 16首页·上横幅小 \r\n
     * 17首页·中横幅大
     * 18首页·中横幅中
     * 19首页·中横幅小 \r\n
     * 21合作页·上横幅大
     * 22合作页·上横幅中
     * 23合作页·上横幅小
     * 24合作页·右侧栏一
     * 25合作页·右侧栏二
     * 26合作页·右侧栏三\r\n
     * 31搜索页·右侧栏一）
     */


    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $insert = ['status' => 1, 'create_id', 'update_id'];
    protected $update = ['update_id'];
    public $type  = [
        11 => ['id'=>11, 'title'=>'首页·顶部横幅+左右三联'],
        12 => ['id'=>12, 'title'=>'首页·弹窗'],
        13 => ['id'=>13, 'title'=>'首页·底部悬浮'],
        14 => ['id'=>14, 'title'=>'首页·上横幅大'],
        15 => ['id'=>15, 'title'=>'首页·上横幅中'],
        16 => ['id'=>16, 'title'=>'首页·上横幅小'],
        17 => ['id'=>17, 'title'=>'首页·中横幅大'],
        18 => ['id'=>18, 'title'=>'首页·中横幅中'],
        19 => ['id'=>19, 'title'=>'首页·中横幅小'],
        21 => ['id'=>21, 'title'=>'合作页·上横幅大'],
        22 => ['id'=>22, 'title'=>'合作页·上横幅中'],
        23 => ['id'=>23, 'title'=>'合作页·上横幅小'],
        24 => ['id'=>24, 'title'=>'合作页·右侧栏一'],
        25 => ['id'=>25, 'title'=>'合作页·右侧栏二'],
        26 => ['id'=>26, 'title'=>'合作页·右侧栏三'],
        31 => ['id'=>31, 'title'=>'搜索页·右侧栏一'],
        41 => ['id'=>41, 'title'=>'联系我们'],
    ];

    protected function setCreateIdAttr()
    {
        return getLoginUserId();
    }

    protected function setUpdateIdAttr()
    {
        return getLoginUserId();
    }

    public function allselect()
    {
        $data = $this->where(['status' => 1])->field(['site,id'])->order('site desc')->select();
        foreach ($data as $key => $value) {
            $value['title'] = $this->type[$value['site']]['title'];
            $data[$key] = $value;
        }
        return $data;
    }

    public function allFind($id)
    {
        $data = $this->where(['status' => 1, 'id'=>$id])->field(['site,id'])->find();
        $data['title'] = $this->type[$data['site']]['title'];
        return $data;
    }


}