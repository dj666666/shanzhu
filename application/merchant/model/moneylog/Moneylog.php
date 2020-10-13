<?php

namespace app\merchant\model\moneylog;

use think\Model;


class Moneylog extends Model
{

    

    

    // 表名
    protected $name = 'money_log';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_text',
        'create_time_text',
        'update_time_text'
    ];
    

    
    public function getTypeList()
    {
        return ['0' => __('Type 0'), '1' => __('Type 1')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getCreateTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['create_time']) ? $data['create_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getUpdateTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['update_time']) ? $data['update_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setCreateTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setUpdateTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    /*public function agent()
    {
        return $this->belongsTo('app\merchant\model\Agent', 'agent_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }*/


    public function merchant()
    {
        return $this->belongsTo('app\merchant\model\Merchant', 'mer_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
