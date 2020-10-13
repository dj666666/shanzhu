<?php

namespace app\user\behavior;

class AdminLog
{
    public function run(&$params)
    {
        if (request()->isPost()) {
            \app\user\model\AdminLog::record();
        }
    }
}
