<?php
namespace app\admin\controller;

use think\Db;
use think\Exception;
use util\Num;

class QuestionAnswerGroup extends Base
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        if(request()->isPost()){
            $QuestionAnswerGroup = model('QuestionAnswerGroup');
            $page = request()->post('page', 1);
            $limit = request()->post('limit', 10);
            $offset = ($page - 1) * $limit;
            $map = [];
            $count = $QuestionAnswerGroup->alias('A')
                ->join("(select question_answer_template_id,count(*) as cou FROM sj_question_answer where status=1 Group By question_answer_template_id ) as B", 'A.id=B.question_answer_template_id', 'left')
                ->where($map)->count();
            $data = $QuestionAnswerGroup->alias('A')
                ->join("(select question_answer_template_id,count(*) as cou FROM sj_question_answer where status=1 Group By question_answer_template_id ) as B", 'A.id=B.question_answer_template_id', 'left')->where($map)->order('id desc')
                ->field('A.*,B.cou')->limit($offset, $limit)->select();

            foreach ($data as $k => $v) {
                $v['key'] = $k + ($page - 1) * $limit + 1;
                $v['cou'] = $v['cou'] > 0 ? $v['cou'] : 0;
                $data[$k] = $v;
            }
            return json(['data' => [ 'count' => $count, 'list' => $data]], 200);
        }
        return view('', ['meta_title'=>'专题列表']);
    }

    public function create()
    {
        $questionAnswerGroup = model('questionAnswerGroup');
        $questionAnswer = model('questionAnswer');
        if(request()->isPost()){
            $_post = request()->post('');
            Db::startTrans();
            try {
                $questionAnswerGroup->data(['title'=>$_post['group_title']])->save();
                $plate_id = $questionAnswerGroup->id;
                $dataAll = [];
                foreach ($_post['question'] as $key => $value) {
                    array_push($dataAll, [
                        'question_answer_template_id' => $plate_id,
                        'question' => $value,
                        'answer' => $_post['answer'][$key],
                        'sort' => $_post['sort'][$key],
                    ]);
                }
                $questionAnswer->saveAll($dataAll, false);
                $state = true;
                Db::commit();
            } catch (Exception $e) {
                $state = false;
                Db::rollback();
            }
            if($state !== false) {
                return success_json('编辑成功');
            }
            return error_json('编辑失败');
        }

        $count = $questionAnswerGroup->where(['status'=>1])->count();
        return view('', ['count' => Num::numToWord($count+1)]);
    }

    public function edit()
    {
        $questionAnswerGroup = model('questionAnswerGroup');
        $questionAnswer = model('questionAnswer');
        $id = request()->param('id');
        if(request()->isPost()){
            $_post = request()->post('');
            Db::startTrans();

            try {
                $questionAnswerGroup->isUpdate(true)->save(['title' => $_post['group_title']], ['id' => $id]);

                $insertAll = [];
                $updateAll = [];
                $qids = [];
                foreach ($_post['question'] as $k => $v) {
                    if($_post['qid'][$k] > 0){
                        array_push($updateAll, ['id' => $_post['qid'][$k], 'question' => $v, 'answer' => $_post['answer'][$k], 'sort' => $_post['sort'][$k],]);
                        array_push($qids, $_post['qid'][$k]);
                    }else {
                        array_push($insertAll, ['question_answer_template_id' => $id, 'question' => $v, 'answer' => $_post['answer'][$k], 'sort' => $_post['sort'][$k],]);
                    }
                }
                if(!empty($insertAll)){
                    $questionAnswer->saveAll($insertAll, false);
                }

                if(!empty($updateAll)){
                    $questionAnswer->saveAll($updateAll, true);
                }
                $questionAnswer->where(["id" => ["not in", $qids], 'question_answer_template_id' => $id])->update(['status' => 0]);
                Db::commit();
                $state = true;
            }catch (Exception $e) {
                $state = false;
                Db::rollback();
            }
            if($state !== false) {
                return success_json('编辑成功');
            }
            return error_json('编辑失败');
        }
        $info = $questionAnswerGroup->where(['id'=>$id])->find();
        $data = $questionAnswer->where(['question_answer_template_id'=>$id,'status'=>1])->order('sort desc')->select();
        return view('', [
            'info' => $info,
            'data' => $data,
        ]);
    }


}