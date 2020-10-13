<?php

namespace app\user\model\yhk;

use think\Model;


class Apply extends Model
{

    

    

    // 表名
    protected $name = 'applys';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text'
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




    public function yhk()
    {
        return $this->belongsTo('app\user\model\Yhk', 'yhk_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function merchant()
    {
        return $this->belongsTo('app\user\model\Merchant', 'mer_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
