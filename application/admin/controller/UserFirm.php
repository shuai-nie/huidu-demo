<?php
namespace app\admin\controller;

class UserFirm extends Base
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        if(request()->isPost()) {
            $FirmRelevance = model('FirmRelevance');
            $map = [];
            $page = request()->post('page', 1);
            $limit = request()->post('limit');
            $offset = ($page - 1 ) * $limit;

            $count = $FirmRelevance->alias('A')
                ->where($map)->count();
            
            $data = $FirmRelevance->alias('A')
                ->where($map)->order('id desc')->limit($offset, $limit)->select();

            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        return view('', []);
    }

    public function examine()
    {
        return view('', []);
    }

}