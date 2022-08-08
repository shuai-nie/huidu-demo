<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Ad extends Base
{
    public $page = ['0' => '首页顶部', '1' => '资讯右侧', 2=>'首页尾部',3=>'首页底部悬浮',4=>'专区页', 5=> '搜索页'];
    public $category = ['1' => '一栏', '2' => '两栏', '3' => '三栏'];
    public $type = ['1' => '1200x80', '2' => '1200x160', '3' => '1200x240', '4' => '1200x60', '5' => '1200x70', 6 => '1200*64'];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $map = ['status'=>1];
        $name = \request()->param('name');
        $page = \request()->param('page');
        $category = \request()->param('category');
        if(!empty($name)) {
            $map['name'] = ['like', "%{$name}%"];
        }
        if(is_numeric($page)) {
            $map['page'] = $page;
        }
        if(!empty($category)) {
            $map['category'] = $category;
        }
        $data = model("Ad")->where($map)->field('id,name,sort,page,category,load1,pic1,url1,begin1,end1')->order('end1 desc,id desc')->select();
        if($data) {
            $data = collection($data)->toArray();
        }
        foreach($data as $k => $d){
            $Map['id'] = $d['id'];
            if($d['category'] == 2){
                $load2 = model("Ad")->where($Map)->field('id,name,sort,page,category,pic2 as pic1,url2 as url1,load2 as load1,begin2 as begin1,end2 as end1')->order('end1 desc,id desc')->select();
                $load = [];
                if($load2) {
                    $load = collection($load2)->toArray();
                }
                array_splice($data, $k, 0, $load);

            }elseif ($d['category'] == 3){
                $load2 = model("Ad")->where($Map)->field('id,name,sort,page,category,pic2 as pic1,url2 as url1,load2 as load1,begin2 as begin1,end2 as end1')->order('end1 desc,id desc')->select();
                $load = [];
                if($load2) {
                    $load = collection($load2)->toArray();
                }
                array_splice($data, $k, 0, $load);


                $load3 = model("Ad")->where($Map)->field('id,name,sort,page,category,pic3 as pic1,url3 as url1,load2 as load1,begin3 as begin1,end3 as end1')->order('end1 desc,id desc')->select();
                $load3s = [];
                if($load3) {
                    $load3s = collection($load3)->toArray();
                }
                array_splice($data, $k, 0, $load3s);
            }
        }

        foreach ( $data as $key => $row )
        {
            $data[$key]['begin1'] = $row['begin1'] >10000 ? date('Y-m-d H:i:s', $row['begin1']) : '';
            $data[$key]['end1'] = $row['end1'] >10000 ? date('Y-m-d H:i:s', $row['end1']) : '';
            $data[$key]['pages'] = $this->page[$row['page']] . '_' . $row['id'];
            $data[$key]['categorys'] = $this->category[$row['category']] . '_' .$row['id'];

            $num1[$key] = $row['id'];
            $num2[$key] = $row['category'];
        }
        if($data) {
            array_multisort( $num1, SORT_DESC, $num2, SORT_DESC, $data);
        }
        return view('', [
            'data'            => $data,
            'page'            => $this->page,
            'category'        => $this->category,
            'search_name'     => $name,
            'search_page'     => $page,
            'search_category' => $category,
        ]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        if(Request()->isPost()) {
            $_post = Request()->param();
            $_post['begin1'] = !empty($_post['begin1']) ? strtotime($_post['begin1']) : 0;
            $_post['end1'] = !empty($_post['end1']) ? strtotime($_post['end1']) : 0;
            $_post['begin2'] = !empty($_post['begin2']) ? strtotime($_post['begin2']) : 0;
            $_post['end2'] = !empty($_post['end2']) ? strtotime($_post['end2']) : 0;
            $_post['begin3'] = !empty($_post['begin3']) ? strtotime($_post['begin3']) : 0;
            $_post['end3'] = !empty($_post['end3']) ? strtotime($_post['end3']) : 0;
            $state = model("Ad")->save($_post);
            if($state !== false){
                return success_json();
            }
            return error_json();
        }
        return view('create', [
            'page'     => $this->page,
            'category' => $this->category,
        ]);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        if(Request()->isPost()) {
            $_post = Request()->param();
            $_post['begin1'] = !empty($_post['begin1']) ? strtotime($_post['begin1']) : 0;
            $_post['end1'] = !empty($_post['end1']) ? strtotime($_post['end1']) : 0;
            $_post['begin2'] = !empty($_post['begin2']) ? strtotime($_post['begin2']) : 0;
            $_post['end2'] = !empty($_post['end2']) ? strtotime($_post['end2']) : 0;
            $_post['begin3'] = !empty($_post['begin3']) ? strtotime($_post['begin3']) : 0;
            $_post['end3'] = !empty($_post['end3']) ? strtotime($_post['end3']) : 0;
            $state = model("Ad")->save($_post, ['id'=>$_post['id']]);
            if($state !== false){
                return success_json();
            }
            return error_json();
        }
        $data = model("Ad")->find($id);
        $data['begin1'] = $data['begin1'] >10000 ? date('Y-m-d H:i:s', $data['begin1']) : '';
        $data['end1'] = $data['end1'] >10000 ? date('Y-m-d H:i:s', $data['end1']) : '';
        $data['begin2'] = $data['begin2'] >10000 ? date('Y-m-d H:i:s', $data['begin2']) : '';
        $data['end2'] = $data['end2'] >10000 ? date('Y-m-d H:i:s', $data['end2']) : '';
        $data['begin3'] = $data['begin3'] >10000 ? date('Y-m-d H:i:s', $data['begin3']) : '';
        $data['end3'] = $data['end3'] >10000 ? date('Y-m-d H:i:s', $data['end3']) : '';
        return view('', [
            'data' => $data,
            'page' => $this->page,
            'category' => $this->category,
        ]);
    }



    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $state = model("Ad")->save(['status'=>0], ['id'=>$id]);
        if($state !== false){
            return success_json();
        }
        return error_json();
    }


}
