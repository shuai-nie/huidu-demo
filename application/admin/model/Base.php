<?php

namespace app\admin\model;

use think\Log;
use think\Model;

class Base extends Model
{
    protected static function init()
    {
        AuthMenu::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '编辑菜单 ID:' . request()->param('id') ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });

        AuthMenu::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '新建菜单  ID:' . \model('AuthMenu')->getLastInsID() ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });

        Admin::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '编辑 管理人员  ID:' . request()->param('id') ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });
        Admin::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '新建 管理人员  ID:' . \model('Admin')->getLastInsID() ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });

        Advertisement::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '编辑 广告  ID:' . request()->param('id') ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });
        Advertisement::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '新建 广告  ID:' . \model('Advertisement')->getLastInsID() ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });

        Banner::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '编辑 Banner  ID:' . request()->param('id') ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });
        Banner::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '新建 Banner  ID:' . \model('Banner')->getLastInsID() ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ])->save();
        });

        Card::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '编辑 用户名片  ID:' . request()->param('id') ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });
        Card::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '新建 Banner  ID:' . \model('Card')->getLastInsID() ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });

        CardContact::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->save([
                'uid' => getLoginUserId(),
                'text' => '编辑 用户名片·联系方式表  ID:' . request()->param('id') ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ]);
        });
        CardContact::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->save([
                'uid' => getLoginUserId(),
                'text' => '新建 用户名片·联系方式表  ID:' . \model('CardContact')->getLastInsID() ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ]);
        });

        Config::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->save([
                'uid' => getLoginUserId(),
                'text' => '编辑 全局配置  ID:' . request()->param('id') ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ]);
        });
        Config::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->save([
                'uid' => getLoginUserId(),
                'text' => '新建 全局配置  ID:' . \model('Config')->getLastInsID() ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ]);
        });

        Cooperation::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->save([
                'uid' => getLoginUserId(),
                'text' => '编辑 合作动态  ID:' . request()->param('id') ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ]);
        });
        Cooperation::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->save([
                'uid' => getLoginUserId(),
                'text' => '新建 合作动态  ID:' . \model('Cooperation')->getLastInsID() ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ]);
        });

        Counselor::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->save([
                'uid' => getLoginUserId(),
                'text' => '编辑 顾问表  ID:' . request()->param('id') ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ]);
        });
        Counselor::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->save([
                'uid' => getLoginUserId(),
                'text' => '新建 顾问表  ID:' . \model('Counselor')->getLastInsID() ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ]);
        });

        DataDic::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->save([
                'uid' => getLoginUserId(),
                'text' => '编辑 字典  ID:' . request()->param('id') ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ]);
        });
        DataDic::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->save([
                'uid' => getLoginUserId(),
                'text' => '新建 字典  ID:' . \model('DataDic')->getLastInsID() ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ]);
        });

        Group::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->save([
                'uid' => getLoginUserId(),
                'text' => '编辑 角色  ID:' . request()->param('id') ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ]);
        });
        Group::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->save([
                'uid' => getLoginUserId(),
                'text' => '新建 角色  ID:' . \model('Group')->getLastInsID() ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ]);
        });

        Package::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->save([
                'uid' => getLoginUserId(),
                'text' => '编辑 VIP套餐  ID:' . request()->param('id') ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ]);
        });
        Package::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->save([
                'uid' => getLoginUserId(),
                'text' => '新建 VIP套餐  ID:' . \model('Package')->getLastInsID() ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ]);
        });

        Resource::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->save([
                'uid' => getLoginUserId(),
                'text' => '编辑 资源  ID:' . request()->param('id') ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ]);
        });
        Resource::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->save([
                'uid' => getLoginUserId(),
                'text' => '新建 资源  ID:' . \model('Resource')->getLastInsID() ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ]);
        });

        ResourceCard::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->save([
                'uid' => getLoginUserId(),
                'text' => '编辑 资源·名片投递记录  ID:' . request()->param('id') ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ]);
        });
        ResourceCard::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->save([
                'uid' => getLoginUserId(),
                'text' => '新建 资源·名片投递记录  ID:' . \model('ResourceCard')->getLastInsID() ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ]);
        });

        ResourceContact::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->save([
                'uid' => getLoginUserId(),
                'text' => '编辑 资源·联系信息表 ID:' . request()->param('id') ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ]);
        });
        ResourceContact::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->save([
                'uid' => getLoginUserId(),
                'text' => '新建 资源·联系信息表  ID:' . \model('ResourceContact')->getLastInsID() ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ]);
        });

        ResourceStats::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->save([
                'uid' => getLoginUserId(),
                'text' => '编辑 资源·统计表 ID:' . request()->param('id') ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ]);
        });
        ResourceStats::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->save([
                'uid' => getLoginUserId(),
                'text' => '新建 资源·统计表  ID:' . \model('ResourceStats')->getLastInsID() ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ]);
        });

        User::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '编辑 用户基础表 ID:' . request()->param('id') ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });
        User::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '新建 用户基础表  ID:' . \model('User')->getLastInsID() ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });

        UserInfo::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '编辑 用户信息 ID:' . request()->param('id') ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });
        UserInfo::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '新建 用户信息  ID:' . \model('UserInfo')->getLastInsID() ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });

        UserRecharge::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '编辑 套餐充值记录 ID:' . request()->param('id') ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });
        UserRecharge::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '新建 套餐充值记录  ID:' . \model('UserRecharge')->getLastInsID() ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });

        Channel::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '编辑 推广渠道 ID:' . request()->param('id') ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });

        Channel::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '新建 推广渠道 ID:' . model('Channel')->getLastInsID(),
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });

        Adsense::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '编辑 广告位表 ID:'. request()->param('id'),
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });

        Adsense::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '新建 广告位表 ID:' . model('Adsense')->getLastInsID(),
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });

        Advert::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '编辑 广告 ID:'. request()->param('id'),
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });

        Advert::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '新建 广告 ID:' . model('Advert')->getLastInsID(),
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });

        Content::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '新建 文章 ID:' . model('Content')->getLastInsID(),
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });

        Content::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '编辑 文章 ID:'. request()->param('id'),
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });

        ContentCategory::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '新建 文章分类 ID:' . model('ContentCategory')->getLastInsID(),
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });

        ContentCategory::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '编辑 文章分类 ID:'. request()->param('id'),
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });

        ContentHot::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '新建 文章置顶 ID:' . model('ContentHot')->getLastInsID() . '='.$data['type'],
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });

        ContentHot::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '编辑 文章置顶 ID:'. request()->param('id'). '='.$data['type'],
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });

        Firm::afterInsert(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '新建 企业 ID:' . model('Firm')->getLastInsID(),
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });

        Firm::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '编辑 文章置顶 ID:'. request()->param('id'),
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });

        FirmRelevance::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '用户关联 审核 '. request()->param('id'),
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });

        FirmRelevanceDatum::afterUpdate(function($data){
            model('AdminLog')->isUpdate(false)->data([
                'uid' => getLoginUserId(),
                'text' => '用户关联企业·审核记录·资料表 '  ,
                'url' => (string)url(),
                'ip' => request()->ip(),
            ], true)->save();
        });
    }


    public function saveId($save)
    {
        $create = $this->allowField(true)->isUpdate(false)->save($save);
        if($create){
            return $this->id;
        }else {
            return 0;
        }
    }



}
