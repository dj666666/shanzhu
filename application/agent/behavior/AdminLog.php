<?php

namespace app\agent\behavior;

class AdminLog
{
    public function run(&$params)
    {
        if (request()->isPost()) {
            \app\agent\model\AdminLog::record();
        }
    }
}
