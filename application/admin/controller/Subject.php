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

        if(request()->isPost()){
            $_post = request()->post();
            $id = request()->post('id', 0);
            if($id > 0){
                $state = $plate->allowField(true)->isUpdate(true)->save($_post, ['id'=>$id]);
            }else{
                $state = $plate->allowField(true)->data($_post)->save();
            }

            if($state !== false){
                return success_json('提交成功');
            }
            return error_json("提交失败");
        }
        $sid = request()->param('sid');
        $count = $plate->where(['subject_id'=>$sid])->count();
        if($count > 0){
            $info = $plate->where(['subject_id'=>$sid])->find();
            return view('/plate/edit', [
                'sid' => $sid,
                'info' => $info,
            ]);
        }
        return view('/plate/create', [
            'sid' => $sid
        ]);
    }

    public function banner()
    {
        $subjectBanner = model('subjectBanner');
        $sid = request()->param('sid');
        if(request()->isPost()){
            $page = request()->post('page', 1);
            $limit = request()->post('limit', 10);
            $offset = ($page - 1) * $limit;
            $map = ['status' => 1, 'subject_id' => $sid];
            $data = $subjectBanner->where($map)->order('id desc')->limit($offset, $limit)->select();
            $count = $subjectBanner->where($map)->count();
            foreach ($data as $k => $v) {
                $v['key'] = $k+ ($page-1)*$limit+1;
                $data[$k] = $v;
            }
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        return view('/subject_banner/index', [
            'sid' => $sid,
            'type' => $subjectBanner->type
        ]);
    }

    public function create_banner()
    {
        $subjectBanner = model('subjectBanner');
        $sid = request()->param('sid');
        if(request()->isPost()){
            $_post = request()->post();
            $state = $subjectBanner->data($_post)->save();

            if($state !== false){
                return success_json('提交成功');
            }
            return error_json("提交失败");
        }
        return view('/subject_banner/create', [
            'sid' => $sid,
            'type' => $subjectBanner->type
        ]);
    }

    public function edit_banner()
    {
        $subjectBanner = model('subjectBanner');
        $id = request()->param('id');
        if(request()->isPost()){
            $_post = request()->post();
            $state = $subjectBanner->allowField(true)->isUpdate(true)->save($_post, ['id'=>$id]);
            if($state !== false) {
                return success_json("提交成功");
            }
            return error_json("提交失败");
        }
        $info = $subjectBanner->where(['id'=>$id])->find();
        return view('/subject_banner/edit', [
            'info' => $info,
            'type' => $subjectBanner->type
        ]);
    }

    public function del_banner()
    {
        $subjectBanner = model('subjectBanner');
        if(request()->isPost()){
            $id = request()->param('id');
            $state = $subjectBanner->isUpdate(true)->save(['status'=>0], ['id'=>$id]);
            if($state !== false) {
                return success_json("删除成功");
            }
            return error_json("删除失败");
        }
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
        $questionAnswerGroup = model('questionAnswerGroup');
        $sid = request()->param('sid');
        $count = $subjectContent->where(['subject_id'=>$sid,'status'=>2])->count();
        if($count > 0){
            return view('/subject_question_answer/edit', []);
        }
        $groupAll = $questionAnswerGroup->where(['status'=>1])->field('id,title')->select();
        return view('/subject_question_answer/create', [
            'groupAll' => $groupAll
        ]);
    }

}