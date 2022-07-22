<?php
namespace app\admin\controller;

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
            $count = $QuestionAnswerGroup->where($map)->count();
            $data = $QuestionAnswerGroup->where($map)->limit($offset, $limit)->select();
            return json(['data' => [ 'count' => $count, 'list' => $data]], 200);
        }
        return view('', []);
    }

    public function create()
    {
        if(request()->isPost()){

        }
        return view('', []);
    }



}