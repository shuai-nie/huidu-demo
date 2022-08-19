<?php
namespace app\admin\controller;

use app\admin\model\Advert as AdvertModel;

class Adsense extends Base
{
    public function _initialize()
    {
        $this->assign('meta_title', '广告位列表');
        parent::_initialize();
    }

    public function index()
    {
        $Adsense = model('Adsense');
        if(request()->isPost()){
            $limit = request()->post('limit');
            $page = request()->post('page', 1);
            $offset = ($page - 1) * $limit;
            $map = ['status'=>1];
            $count = $Adsense->where($map)->count();
            $list = $Adsense->where($map)->order('id desc')->limit($offset, $limit)->select();
            foreach ($list as $k => $v) {
                $v['count'] = AdvertModel::where(['status' => 1, 'adsense_id' => $v['id']])->count();
                $list[$k] = $v;
            }
            return json(['data'=>['count'=>$count, 'list'=>$list]], 200);
        }
        return view('', [
            'type' => $Adsense->type,
        ]);
    }

    public function create()
    {
        $Adsense = model('Adsense');
        if(request()->isPost()) {
            $_post = request()->post();
            $state = $Adsense->allowField(true)->data($_post)->save();
            if($state != false) {
                GetHttp(config('CacheHost') . config('CacheUrlApi')['0']);
                return success_json("提交成功");
            }
            return error_json("提交失败");
        }

        return view('', [
            'type' => $Adsense->type,
        ]);
    }

    public function edit()
    {
        $Adsense = model('Adsense');
        $id = request()->param('id');
        if(request()->isPost()) {
            $_post = request()->post();
            $state = $Adsense->allowField(true)->isUpdate(true)->save($_post, ['id'=>$id]);
            if($state != false) {
                GetHttp(config('CacheHost') . config('CacheUrlApi')['0']);
                return success_json("提交成功");
            }
            return error_json("提交失败");
        }

        $info = $Adsense->where(['id'=>$id])->find();
        return view('', [
            'type' => $Adsense->type,
            'info' => $info,
        ]);
    }

    public function delete()
    {
        $Adsense = model('Adsense');
        $id = request()->param('id');
        $state = $Adsense->isUpdate(true)->save(['status'=>0], ['id'=>$id]);
        if ($state != false) {
            GetHttp(config('CacheHost') . config('CacheUrlApi')['0']);
            return success_json("刪除成功");
        }
        return error_json("删除失败");
    }

}