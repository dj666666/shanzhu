<?php

namespace app\user\model\order;

use think\Model;


class Index extends Model
{

    

    

    // 表名
    protected $name = 'order';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text',
        'ordertime_text'
    ];
    

    
    public function getStatusList()
    {
        return ['1' => __('Status 1'), '2' => __('Status 2'), '3' => __('Status 3'), '4' => __('Status 4'), '5' => __('Status 5')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getOrdertimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['ordertime']) ? $data['ordertime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setOrdertimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function user()
    {
        return $this->belongsTo('app\user\model\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
