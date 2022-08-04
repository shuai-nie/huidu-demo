<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use util\AliyunOssClient;
use util\UploadDownload;

class Content extends Controller
{
    protected $model;
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = model('Content');
        $this->assign('meta_title', "文章管理");
    }

    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {
        $ContentCategory = model('ContentCategory');
        if(\request()->isPost()){
            $map = ['A.status'=>1];
            $title = \request()->post('title');
            $category_id = \request()->post('category_id');
            $home_top = \request()->post('home_top');
            $category_top = \request()->post('category_top');
            if(!empty($title)) {
                $map['A.title'] = ['like', "%{$title}%"];
            }
            if(is_numeric($category_id) ){
                $map['A.category_id'] = $category_id;
            }

            if(is_numeric($home_top) ){
                $map['A.home_top'] = $home_top;
            }

            if(is_numeric($category_top) ){
                $map['A.category_top'] = $category_top;
            }

            $limit = \request()->post('limit');
            $page = \request()->post('page');
            $offset = ($page - 1) * $limit;

            $data = $this->model->alias('A')
                ->join($ContentCategory->getTable(). " B", "A.category_id=B.id", "left")
                ->field("A.*,B.name as category_name")
                ->where($map)->limit($offset, $limit)->order('A.id desc')->select();
            $count = $this->model->alias('A')
                ->join($ContentCategory->getTable(). " B", "A.category_id=B.id", "left")
                ->where($map)->count();
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        $category = $ContentCategory->where(['is_del'=>0,'type'=>1])->field('id,name')->order("sort desc")->select();
        $category2 = $ContentCategory->where(['is_del'=>0,'type'=>2])->field('id,name')->order("sort desc")->select();
        return view('', [
            'category'=>$category,
            'category2' => $category2
        ]);
    }

    /**
     * 显示创建资源表单页.
     * @return \think\Response
     */
    public function create()
    {
        $ContentCategory = model('ContentCategory');
        if(Request()->isPost()) {
            $data = Request()->post();

            Db::startTrans();
            try {
                $data['home_sort'] = 0;
                $state = $this->model->save($data);
                $cid = $this->model->id;
                $ContentDetail = model('ContentDetail');
                $UploadDownload = new UploadDownload();
                $data['content'] = $UploadDownload->replaceImg($data['content']);
                $ContentDetail->save([
                    'cid' => $cid,
                    'content' => htmlspecialchars_decode($data['content'])
                ]);
                Db::commit();
                return success_json(lang('CreateSuccess', [lang('Content')]));
            } catch (\Exception $e) {
                Db::rollback();
                return error_json(lang('CreateFail', [lang('Content')]));
            }
        }
        $category = $ContentCategory->where(['is_del'=>0,'type'=>1])->field('id,name')->order("sort desc")->select();
        $category2 = $ContentCategory->where(['is_del'=>0,'type'=>2])->field('id,name')->order("sort desc")->select();
        return view('', [
            'category'=>$category,
            'category2'=>$category2
        ]);
    }

    /**
     * 显示编辑资源表单页.
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $ContentCategory = model('ContentCategory');
        $ContentDetail = model('ContentDetail');
        if(Request()->isPost()) {
            $data = Request()->post();
            Db::startTrans();
            try {
                $state = $this->model->save($data, ['id'=>$id]);
                $ContentDetail = model('ContentDetail');
                $count = $ContentDetail->where(['cid'=>$id])->count();
                $UploadDownload = new UploadDownload();
                $data['content'] = $UploadDownload->replaceImg($data['content']);
                if($count > 0){
                    $ContentDetail->save([
                        'content' => htmlspecialchars_decode($data['content'])
                    ], ['cid' => $id,]);
                } else {
                    $ContentDetail->save([
                        'content' => htmlspecialchars_decode($data['content']),
                        'cid' => $id
                    ]);
                }
                Db::commit();
                return success_json(lang('EditSuccess', [lang('Content')]));
            } catch (\Exception $e) {
                Db::rollback();
                return error_json(lang('EditFail', [lang('Content')]));
            }
        }
        $data = $this->model->find($id);
        $detail = $ContentDetail->where(['cid'=>$id])->find();
        $data['content'] = $detail['content'];

        $category = $ContentCategory->where(['is_del'=>0,'type'=>1])->field('id,name')->order("sort desc")->select();
        $category2 = $ContentCategory->where(['is_del'=>0,'type'=>2])->field('id,name')->order("sort desc")->select();
        return view('edit', [
            'data' => $data,
            'category' => $category,
            'category2' => $category2,
        ]);
    }

    /**
     * 删除指定资源
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $id = \request()->param('id');
        $state = $this->model->save(['status'=>0], ['id'=>$id]);
        if($state !== false){
            return success_json(lang('DeleteSuccess', [lang('Content')]) );
        }
        return error_json(lang('DeleteFail', [lang('Content')]));
    }

    public function hometop($id)
    {
        if(\request()->isPost()){
            $_post = \request()->post();
            $state = $this->model->save([
                'home_top' => $_post['home_top'],
                'home_sort' => $_post['home_sort'],
            ], ['id'=>$id]);
            if($state !== false){
                return success_json(lang('TopSuccess', [lang('Content')]) );
            }
            return error_json(lang('TopFail', [lang('Content')]));
        }
        $data = $this->model->find($id);
        return view('', ['data'=>$data]);
    }

    public function categorytop($id)
    {
        if(\request()->isPost()){
            $_post = \request()->post();
            $state = $this->model->save([
                'category_top' => $_post['category_top'],
                'category_sort' => $_post['category_sort'],
            ], ['id'=>$id]);
            if($state !== false){
                return success_json(lang('TopSuccess', [lang('Content')]) );
            }
            return error_json(lang('TopFail', [lang('Content')]));
        }
        $data = $this->model->find($id);
        return view('', ['data'=>$data]);
    }


}
