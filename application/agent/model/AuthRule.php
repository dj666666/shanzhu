<?php

namespace app\agent\model;

use think\Cache;
use think\Model;

class AuthRule extends Model
{
    protected $name = 'agent_auth_rule';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    protected static function init()
    {
        self::afterWrite(function ($row) {
            Cache::rm('__agentmenu__');
        });
    }

    public function getTitleAttr($value, $data)
    {
        return __($value);
    }

}
