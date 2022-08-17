<?php
namespace app\admin\controller;

class Advert extends Base
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $Advert = model('Advert');
        if(request()->isPost()){
            $limit = request()->post('limit');
            $page = request()->post('page', 1);
            $title = request()->post('title');
            $adsense_id = request()->post('adsense_id');
            $start_time = request()->post('start_time');
            $end_time = request()->post('end_time');
            $offset = ($page - 1) * $limit;
            $map = ['status'=>1];

            if(!empty($title)) {
                $map['title'] = ['like', "%{$title}%"];
            }

            if(!empty($adsense_id)) {
                $map['adsense_id'] = $adsense_id;
            }

            if(!empty($start_time)) {
                $map['start_time'] = ['>=', strtotime($start_time)];
            }

            if(!empty($end_time)) {
                $map['end_time'] = ['<=', strtotime($end_time)];
            }

            $count = $Advert->where($map)->count();
            $list = $Advert->where($map)->order('id desc')->limit($offset, $limit)->select();
            foreach ($list as $k=>$v){
                $v['key'] = $k+ ($page-1)*$limit+1;
                $v['adsense_title'] = allAdventFind($v['adsense_id']);
                $list[$k] = $v;
            }
            return json(['data'=>['count'=>$count, 'list'=>$list]], 200);
        }
        $AdsenseAll = model('Adsense')->allselect();
        return view('', [
            'AdsenseAll' => $AdsenseAll,
        ]);
    }

    public function create()
    {
        $Advert = model('Advert');
        if(request()->isPost()) {
            $_post = request()->post();
            if(!empty($_post['start_time'])){
                $_post['start_time'] = strtotime($_post['start_time']);
            }
            if(!empty($_post['end_time'])){
                $_post['end_time'] = strtotime($_post['end_time']);
            }
            $state = $Advert->allowField(true)->isUpdate(false)->data($_post)->save();
            if($state != false) {
                return success_json("提交成功");
            }
            return error_json("提交失败");
        }


        $adsenseAll = model('adsense')->allselect();
        return view('', [
            'adsenseAll' => $adsenseAll,
        ]);
    }

    public function edit()
    {
        $Advert = model('Advert');
        $id = request()->param('id');
        if(request()->isPost()) {
            $_post = request()->post();
            if(!empty($_post['start_time'])){
                $_post['start_time'] = strtotime($_post['start_time']);
            }
            if(!empty($_post['end_time'])){
                $_post['end_time'] = strtotime($_post['end_time']);
            }
            $state = $Advert->allowField(true)->isUpdate(true)->save($_post, ['id'=>$id]);
            if($state != false) {
                return success_json("提交成功");
            }
            return error_json("提交失败");
        }

        $info = $Advert->where(['id'=>$id])->find();
        $adsenseAll = model('adsense')->allselect();
        return view('', [
            'info' => $info,
            'adsenseAll' => $adsenseAll,
        ]);
    }

    public function delete()
    {
        $Advert = model('Advert');
        $id = request()->param('id');
        $state = $Advert->isUpdate(true)->save(['status'=>0], ['id'=>$id]);
        if ($state != false) {
            return success_json("刪除成功");
        }
        return error_json("删除失败");
    }

}