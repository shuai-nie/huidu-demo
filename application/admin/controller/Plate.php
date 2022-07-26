<?php
namespace app\admin\controller;

class Plate extends Base
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        if(request()->isPost()){
            $page = request()->post('page', 1);
            $limit = request()->post('limit', 10);
            $offset = ($page - 1) * $limit;
            $map = [];
            $plate = model('plate');
            $plateResource = model('plateResource');
            $data = $plate->alias('A')
                ->join($plateResource->getTable().' B', "A.id=B.plate_id", "left")
                ->field("A.*,B.type,B.key,B.sort")
                ->where($map)->limit($offset, $limit)->select();
            $count = $plate->alias('A')
                ->join($plateResource->getTable().' B', "A.id=B.plate_id", "left")
                ->where($map)->count();
            foreach ($data as $k => $v) {
                $v['key'] = $k+ ($page-1)*$limit+1;
                $data[$k] = $v;
            }
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        return view('', [
            'meta_title' => '板块列表',
        ]);
    }

    public function create()
    {
        return view('', []);
    }

}