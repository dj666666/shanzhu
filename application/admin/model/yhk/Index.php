<?php

namespace app\admin\model\yhk;

use think\Model;


class Index extends Model
{

    

    

    // 表名
    protected $name = 'yhk';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'is_state_text'
    ];
    

    
    public function getIsStateList()
    {
        return ['0' => __('Is_state 0'), '1' => __('Is_state 1')];
    }


    public function getIsStateTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_state']) ? $data['is_state'] : '');
        $list = $this->getIsStateList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function user()
    {
        return $this->belongsTo('app\admin\model\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
