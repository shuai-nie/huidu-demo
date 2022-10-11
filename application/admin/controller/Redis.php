<?php
namespace app\admin\controller;

class Redis extends Base
{
    public $redisData = [
        ['title' => '首页banner', 'key' => 'bannerList:getBannerList'],
        ['title' => '广告列表', 'key' => 'ad_all_list'],
        ['title' => 'config配置', 'key' => 'configList:getConfigList'],
        ['title' => '合作动态', 'key' => 'CooperationList:getCooperationList'],
        ['title' => '首页级联菜单', 'key' => 'HomeTypeAll:getTypeAll'],
        ['title' => '字典缓存', 'key' => 'dataDicTypeList:*'],
        ['title' => '资源字典缓存', 'key' => 'listByDataTypeNos:listByDataTypeNos'],
        ['title' => '首页滚动资源', 'key' => 'homeResource:*'],
        ['title' => '首页推荐资源', 'key' => 'HomeDemandList:*'],
        ['title' => '区号列表', 'key' => 'CountryMobilePrefixList:getList'],
    ];

    /*
     * 接口： /api/removerCache
        请求方式：Post
        参数：cacheName
        缓存列表：
        首页banner：bannerList:getBannerList
        广告列表：ad_all_list
        config配置：configList:getConfigList
        合作动态：CooperationList:getCooperationList
        首页级联菜单：HomeTypeAll:getTypeAll
        字典缓存：dataDicTypeList:*
        资源字典缓存：listByDataTypeNos:listByDataTypeNos
        首页滚动资源：homeResource:*
        首页推荐资源：HomeDemandList:*
        区号列表：CountryMobilePrefixList:getList
     * */

    public function index()
    {
        if(request()->isPost()){
            $_post = request()->post();
            foreach ($_post['key'] as $val){
                request_post(config('CacheHost') . config('CacheUrlApi')['1'], ['cacheName' => $val]);
            }
            return success_json('删除成功');
        }
        return view('', [
            'meta_title' => '缓存',
            'redisData' => $this->redisData
        ]);
    }

}