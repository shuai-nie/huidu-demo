<?php
namespace app\admin\controller;

use think\Db;
use think\Exception;

class Subject extends Base
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
            $subject = model('subject');
            $subjectAdvertisement = model('SubjectAdvertisement');
            $data = $subject->alias('A')
                ->join($subjectAdvertisement->getTable().' B', "A.id=B.subject_id", "left")
                ->field("A.*,B.img_url,B.link_url")
                ->where($map)->limit($offset, $limit)->select();
            $count = $subject->alias('A')->where($map)->count();
            foreach ($data as $k => $v) {
                $v['key'] = $k+ ($page-1)*$limit+1;
                $data[$k] = $v;
            }
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        return view('', [
            'meta_title' => '专题列表',
        ]);
    }

    public function create()
    {
        $subject = model('subject');
        $subjectAdvertisement = model('SubjectAdvertisement');
        if(request()->isPost()){
            $_post = request()->post();
            $state = false;
            Db::startTrans();
            try {
                $subject->allowField(true)->isUpdate(false)->data($_post)->save();
                $subject_id = $subject->id;
                $subjectAdvertisement->allowField(true)->isUpdate(false)->data([
                    'subject_id' => $subject_id,
                    'img_url' => $_post['img_url'],
                    'link_url' => $_post['link_url'],
                ])->save();

                Db::commit();
                $state = true;
            } catch (Exception $e) {
                Db::rollback();
                $state = false;
            }
            if($state !== false) {
                return success_json('提交成功');
            }
            return error_json('提交失败');
        }
        return view('', []);
    }

    public function edit()
    {
        $subject = model('subject');
        $subjectAdvertisement = model('SubjectAdvertisement');
        $id = request()->param('id');
        if(request()->isPost()) {
            $_post = request()->post();
            $state = false;
            Db::startTrans();
            try {
                $subject->allowField(true)->isUpdate(true)->save($_post, ['id'=>$id]);
                $subjectAdvertisement->isUpdate(true)->save([
                    'img_url' => $_post['img_url'],
                    'link_url' => $_post['link_url'],
                ], ['subject_id'=>$id]);
                Db::commit();
                $state = true;
            }catch (Exception $e){
                Db::rollback();
                $state = false;
            }
            if($state !== false) {
                return success_json('提交成功');
            }
            return error_json('提交失败');
        }
        $map = ['A.id' => $id];
        $info = $subject->alias('A')
            ->join($subjectAdvertisement->getTable().' B', "A.id=B.subject_id", "left")->field('A.*,B.img_url,B.link_url')->where($map)->find();
        return view('', [
            'info' => $info
        ]);
    }

    public function del()
    {

    }

    public function plate()
    {
        $plate = model('plate');
        $sid = request()->param('sid');
        $count = $plate->where(['subject_id'=>$sid])->count();
        if($count > 0){
            return view('/plate/edit');
        }
        return view('/plate/create');
    }

    public function banner()
    {
        $subjectBanner = model('subjectBanner');
        $sid = request()->param('sid');
        $count = $subjectBanner->where(['subject_id'=>$sid])->count();
        if($count > 0){
            return view('/subject_banner/edit');
        }
        return view('/subject_banner/create');
    }

    public function zixun()
    {
        $subjectContent = model('subjectContent');
        $sid = request()->param('sid');
        $count = $subjectContent->where(['subject_id'=>$sid])->count();
        if($count > 0){
            return view('/subject_content/edit');
        }
        return view('/subject_content/create');
    }

    public function question()
    {
        $subjectContent = model('subjectContent');
        $sid = request()->param('sid');
        $count = $subjectContent->where(['subject_id'=>$sid])->count();
        if($count > 0){
            return view('/subject_question_answer/edit');
        }
        return view('/subject_question_answer/create');
    }

}