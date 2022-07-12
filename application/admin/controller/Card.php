<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Exception;
use think\Request;

class Card extends Base
{
    public function _initialize()
    {
        parent::_initialize();
        $this->assign('meta_title', lang('CARD'));
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if(Request()->isPost()) {
            $map = ['A.status'=>1];
            $page = Request()->post('page');
            $limit = Request()->post('limit');
            $name = \request()->post('name');
            if(!empty($name)) {
                $map['B.username'] = ['like', "%{$name}%"];
            }
            $nickname = \request()->post('nickname');
            if(!empty($nickname)) {
                $map['B.nickname'] = ['like', "%{$nickname}%"];
            }
            $number = \request()->post('number');
            if(!empty($number)) {
                $map['C.contact_number'] = ['like', '%'.$number.'%'];
            }
            $offset = ($page - 1) * $limit;
            $CardModel = model("Card");
            $UserModel = model("User");
            $CardContact = model('CardContact');
            $quality = \request()->post('quality');
            $isweb = \request()->post('isweb');
            if(is_numeric($quality)) {
                $map['A.quality'] = $quality;
            }
            if(!empty($isweb)){
                $map['A.isweb'] = $isweb;
            }
            $data = $CardModel->alias('A')
                ->join($UserModel->getTable().' B', "A.uid=B.id")
                ->join($CardContact->getTable().' C', '(A.id=C.card_id AND C.status=1 )')
                ->field('A.*,B.username,B.nickname,GROUP_CONCAT(C.contact_number) as number')
                ->where($map)->order('A.id desc')->group('A.id')->limit($offset, $limit)->select();
            $count = $CardModel->alias('A')
                ->join($UserModel->getTable().' B', "A.uid=B.id")
                ->join($CardContact->getTable().' C', '(A.id=C.card_id AND C.status=1 )')
                ->where($map)->group('A.id')->count();
            $dataDic = model('DataDic');

            foreach ($data as $k => $v) {
                $business_tag = explode('|', $v['business_tag']);
                $business_arr = [];
                foreach ($business_tag as $val_tag){
                    if(is_numeric($val_tag)){
                        $dataDicInfo = $dataDic->where(['data_type_no'=>'RESOURCES_TYPE','status'=>1,'data_no'=>$val_tag])->field('data_type_no,data_name')->find();
                        $business_arr[] = $dataDicInfo['data_name'];
                    }
                }
                $v['business_tag'] = implode('|', $business_arr);
                $data[$k] = $v;
            }
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        return view('');
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        if(Request()->isPost()) {
            $_post = Request()->post();
            $card = model('card');
            $userModel = model('user');
            $cardContact = model('cardContact');
            $number = GetRandStr(16);
            $pwd = md5(md5(config('userPwd')) . $number);
            $_post['business_tag'] = isset($_post['business_tag']) ? implode('|', $_post['business_tag']) : '';
            $contact = array();
            $state = false;
            $userInfo = model('user_info');
            $package = model('package');
            $userRecharge = model('userRecharge');
            Db::startTrans();
            $time = time();
            $head_url = !isset($_post['logo']) ? $_post['logo'] : 'http://file.huidu.io/avatar/5.png';
            try {
                $packageInfo = $package->find(1);
                $userModel->allowField(true)->isUpdate(false)->save([
                    'username' => $_post['username'],
                    'head_url' => $head_url,
                    'pwd' => $pwd,
                    'salt' => $number,
                    'nickname' => $_post['name'],
                ]);
                $_post['uid'] = $userModel->id;
                $userRecharge->isUpdate(false)->allowField(true)->save([
                    'uid' => $_post['uid'],
                    'package_id' => $packageInfo['id'],
                    'flush' => $packageInfo['flush'],
                    'publish' => $packageInfo['publish'],
                    'view_demand' => $packageInfo['view_demand'],
                    'view_provide' => $packageInfo['view_provide'],
                    'view_provide_give' => $packageInfo['view_provide_give'],
                    'used_flush' => 0,
                    'used_publish' => 0,
                    'used_view_demand' => 0,
                    'used_view_provide' => 0,
                    'start_time' => $time,
                    'end_time' => $time + 86400 *30,
                    'remarks' => '新建名片注册账号',
                ]);
                $userInfo->allowField(true)->isUpdate(false)->save([
                    'uid' => $_post['uid'],
                    'user_recharge_id' => $userRecharge->id
                ]);

                $card->allowField(true)->isUpdate(false)->save($_post);
                foreach ($_post['contact'] as $key => $val) {
                    array_push($contact, array(
                        'card_id' => $card->id,
                        'contact_type' => $val,
                        'contact_number' => $_post['tel'][$key]
                    ));
                }
                if($contact){
                    $cardContact->isUpdate(false)->saveAll($contact, false);
                }
                Db::commit();
                $state = true;
            }catch (\Exception $e) {
                Db::rollback();;
            }
            if($state !== false){
                return success_json('添加成功');
            }
            return error_json('添加失败');
        }
        $str_name = config('usernameRand') . date('y') . GetRandStr(6) . date('md');
        $this->getDataDicTypeNo();
        return view('', [
            'username' => $str_name,
        ]);
    }

    /**
     * 显示编辑资源表单页.
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $dataDic = model('DataDic');
        if(\request()->isPost()) {
            $_post = \request()->post();
            $contact = [];
            foreach ($_post['contact'] as $k=>$v) {
                array_push($contact, [
                    'card_id' => $id,
                    'contact_type' => $v,
                    'contact_number' => $_post['tel'][$k]
                ]);
            }
            $_post['business_tag'] = implode('|', $_post['business_tag']);
            $state = model('Card')->save($_post, ['id'=>$id]);
            model('CardContact')->where(['card_id'=>$id])->delete();
            model('CardContact')->saveAll($contact);
            if($state !== false) {
                return success_json(lang('EditSuccess', [lang('CARD')] ));
            }
            return error_json(lang('EditSuccess', [lang('CARD')]) );

        }
        $UserModel = model('User');
        $data = model('Card')->alias('A')
            ->join($UserModel->getTable().' B', 'A.uid=B.id')
            ->field('A.*,B.username,B.nickname')
            ->where(['A.id'=>$id])->find();
        $data['business_tag'] = explode('|', $data['business_tag']);

        $CardContact = model('CardContact')->where(['card_id'=>$data['id'],'status'=>1])->select();
        $this->getDataDicTypeNo();
        return view('', [
            'data'=>$data,
            'CardContact' => $CardContact,
        ]);
    }

    public function getDataDicTypeNo()
    {
        $contactType = model('DataDic')->selectType(['data_type_no'=>'CONTACT_TYPE','status'=>1]);
        $resources = model('DataDic')->selectType(['data_type_no'=>'RESOURCES_TYPE','status'=>1]);
        $this->assign('contactType' , $contactType);
        $this->assign('resources' , $resources);
    }

    public function quality()
    {
        if(\request()->isPost()){
            $id = \request()->post('id');
            $name = \request()->post('name');
            $val = \request()->post('val');
            $state = model('Card')->save([$name => $val], ['id' => $id]);
            if($state !== false) {
                return success_json(lang('EditSuccess', [lang('CARD')] ));
            }
            return error_json(lang('EditSuccess', [lang('CARD')]) );
        }
    }

    public function get_card_uid()
    {
        if(\request()->isPost()){
            $card = model('card');
            $uid = \request()->post('uid');
            $count = $card->where(['uid'=>$uid])->count();
            if( $count > 0 ) {
                return error_json('当前用户已创建名片', array(), 400);
            }
            return error_json('当前用户未创建名片', array(), 200);
        }
    }

    public function delete()
    {
        $id = \request()->param('id');
        if($id > 0) {
            $state = model('card')->isUpdate(true)->save(['status'=>0], ['id'=>$id]);
            if($state !== false ){
                return success_json('删除名片成功');
            }
            return error('删除名片失败');
        }

    }

}
