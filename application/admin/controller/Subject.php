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
        $subjectQuestionAnswer = model('subjectQuestionAnswer');
        $sid = request()->param('sid');

        if(request()->isPost()){
            $_post = request()->post();
            $arrAll = [];
            $updateAll = [];
            $updateIdAll = [];
            if(isset($_post['question'])){
            foreach ($_post['question'] as $key => $value) {
                if($_post['id'][$key] > 0) {
                    array_push($updateAll, array(
                        'id' => $_post['id'][$key],
                        'subject_id' => $sid,
                        'question_answer_group_id' => $_post['question_answer_group_id'][$key],
                        'question_answer' => $_post['question_answer'][$key],
                        'question' => $_post['question'][$key],
                        'answer' => $_post['answer'][$key],
                        'sort' => $_post['sort'][$key],
                        'status' => 1,
                    ));
                    array_push($updateIdAll, $_post['id'][$key]);
                } else {
                    array_push($arrAll, array(
                        'subject_id' => $sid,
                        'question_answer_group_id' => $_post['question_answer_group_id'][$key],
                        'question_answer' => $_post['question_answer'][$key],
                        'question' => $_post['question'][$key],
                        'answer' => $_post['answer'][$key],
                        'sort' => $_post['sort'][$key],
                    ));
                }
            }
            }
            $state = false;
            Db::startTrans();
            try {
                $subjectQuestionAnswer->isUpdate(true)->save(['status'=>0], ['subject_id'=>$sid]);

                if(!empty($updateAll)){
                    $subjectQuestionAnswer->isUpdate(true)->saveAll($updateAll);
                }

                if(!empty($arrAll)){
                    $subjectQuestionAnswer->isUpdate(false)->saveAll($arrAll);
                }

                Db::commit();
                $state = true;
            }catch (Exception $e) {
                $state = false;
                Db::rollback();
            }
            if($state !== false) {
                return success_json("提交成功");
            }
            return error_json("提交失败");
        }
        $count = $subjectQuestionAnswer->where(['subject_id'=>$sid,'status'=>1])->count();
        $groupAll = $questionAnswerGroup->where(['status'=>1])->field('id,title')->select();
        if($count > 0){
            $answerAll = $subjectQuestionAnswer->where(['subject_id'=>$sid])->select();
            return view('/subject_question_answer/edit', [
                'answerAll' => $answerAll,
                'groupAll' => $groupAll,
                'sid' => $sid
            ]);
        }

        return view('/subject_question_answer/create', [
            'groupAll' => $groupAll,
            'sid' => $sid
        ]);
    }

    public function question_answer()
    {
        if(request()->isPost()){
            $gid = request()->param('gid');
            $questionAnswer = model('questionAnswer');
            $data = $questionAnswer->where(['status'=>1,'question_answer_template_id'=>$gid])->field('id,question_answer_template_id,question,answer,sort')->order('sort desc')->select();
            $count = $questionAnswer->where(['status'=>1,'question_answer_template_id'=>$gid])->count();

            return json([
                'code' => 0,
                'data' => $data,
                'gid' => $gid,
                'count' => $count
            ]);
        }

    }

    public function subject_question_answer()
    {
        if(request()->isPost()){
            $sid = request()->param('sid');
            $subjectQuestionAnswer = model('subjectQuestionAnswer');
            $data = $subjectQuestionAnswer->where(['status'=>1,'subject_id'=>$sid])->field('question,answer,sort,id,question_answer_group_id,question_answer')->order('sort desc')->select();
            $count = $subjectQuestionAnswer->where(['status'=>1,'subject_id'=>$sid])->count();
            return json([
                'code' => 0,
                'data' => $data,
                'sid' => $sid,
                'count' => $count
            ]);
        }
    }

    public function subject_advertisement()
    {
        $sid = request()->param('sid');
        $subjectAdvertisement = model('subjectAdvertisement');

        if(request()->isPost()){
            $page = request()->post('page', 1);
            $limit = request()->post('limit', 10);
            $offset = ($page - 1) * $limit;
            $map = ['status' => 1, 'subject_id' => $sid];
            $data = $subjectAdvertisement->where($map)->order('id desc')->limit($offset, $limit)->select();
            $count = $subjectAdvertisement->where($map)->count();
            foreach ($data as $k => $v) {
                $v['key'] = $k+ ($page-1)*$limit+1;
                $data[$k] = $v;
            }
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }

        return view('', [
            'sid' => $sid,
        ]);
    }

    public function create_advertisement()
    {
        $sid = request()->param('sid');
        $subjectAdvertisement = model('subjectAdvertisement');
        if(request()->isPost()){
            $_post = request()->post();
            $state = $subjectAdvertisement->allowField(true)->data($_post)->save();
            if($state !== false) {
                return success_json('提交成功');
            }
            return error_json('提交失败');
        }
        return view('', [
            'sid' => $sid,
        ]);
    }

    public function edit_advertisement()
    {
        $id = request()->param('id');
        $subjectAdvertisement = model('subjectAdvertisement');
        $info = $subjectAdvertisement->where(['id' => $id])->find();
        if(request()->isPost()){
            $_post = request()->post();
            $state = $subjectAdvertisement->allowField(true)->isUpdate(true)->save($_post, ['id'=>$id]);
            if($state !== false) {
                return success_json('提交成功');
            }
            return error_json('提交失败');
        }
        return view('', [
            'info' => $info,
        ]);
    }

    public function del_advertisement()
    {
        if(request()->isPost()){
            $id = request()->param('id');
            $subjectAdvertisement = model('subjectAdvertisement');
            $state = $subjectAdvertisement->allowField(true)->isUpdate(true)->save(['status'=>0], ['id'=>$id]);
            if($state !== false) {
                return success_json(lang('DeleteSuccess', ["广告"]));
            }
            return error_json(lang('DeleteFail', ["广告"]));
        }
    }

    public function subject_category()
    {
        $sid = request()->param('sid');
        $subjectCategory = model('subjectCategory');

        if(request()->isPost()){
            $page = request()->post('page', 1);
            $limit = request()->post('limit', 10);
            $offset = ($page - 1) * $limit;
            $map = ['status' => 1, 'subject_id' => $sid];
            $data = $subjectCategory->where($map)->order('id desc')->limit($offset, $limit)->select();
            $count = $subjectCategory->where($map)->count();
            foreach ($data as $k => $v) {
                $v['key'] = $k+ ($page-1)*$limit+1;
                $data[$k] = $v;
            }
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }

        return view('', [
            'sid' => $sid,
        ]);
    }

    public function create_subject_category()
    {
        $sid = request()->param('sid');
        $subjectCategory = model('subjectCategory');
        if(request()->isPost()){
            $_post = request()->post();
            $state = $subjectCategory->allowField(true)->data($_post)->save();
            if($state !== false) {
                return success_json(lang('CreateSuccess', ["优选分类"]));
            }
            return error_json(lang('CreateFail', ["优选分类"]));
        }
        return view('', ['sid' => $sid]);
    }

    public function edit_subject_category()
    {
        $subjectCategory = model('subjectCategory');
        $id = request()->param('id');
        if(request()->isPost()){
            $_post = request()->post();
            $state = $subjectCategory->allowField(true)->isUpdate(true)->save($_post, ['id' => $id]);
            if($state !== false) {
                return success_json(lang('CreateSuccess', ["优选分类"]));
            }
            return error_json(lang('CreateFail', ["优选分类"]));
        }
        $info = $subjectCategory->where(['id'=>$id])->find();
        return view('', ['info' => $info]);
    }

    public function del_subject_category()
    {
        if(request()->isPost()){
            $id = request()->param('id');
            $subjectCategory = model('subjectCategory');
            $state = $subjectCategory->allowField(true)->isUpdate(true)->save(['status'=>0], ['id'=>$id]);
            if($state !== false) {
                return success_json(lang('DeleteSuccess', ["优选分类"]));
            }
            return error_json(lang('DeleteFail', ["优选分类"]));
        }
    }

}