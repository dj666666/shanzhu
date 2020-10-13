<?php

namespace app\merchant\behavior;

class AdminLog
{
    public function run(&$params)
    {
        if (request()->isPost()) {
            \app\merchant\model\AdminLog::record();
        }
    }
}
