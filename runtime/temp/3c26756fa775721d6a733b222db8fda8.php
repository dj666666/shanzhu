<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:85:"E:\phpstudy_pro\WWW\daifuagent\public/../application/admin\view\agent\agent\edit.html";i:1596621406;s:73:"E:\phpstudy_pro\WWW\daifuagent\application\admin\view\layout\default.html";i:1588765312;s:70:"E:\phpstudy_pro\WWW\daifuagent\application\admin\view\common\meta.html";i:1588765312;s:72:"E:\phpstudy_pro\WWW\daifuagent\application\admin\view\common\script.html";i:1588765312;}*/ ?>
<!DOCTYPE html>
<html lang="<?php echo $config['language']; ?>">
    <head>
        <meta charset="utf-8">
<title><?php echo (isset($title) && ($title !== '')?$title:''); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">

<link rel="shortcut icon" href="/assets/img/favicon.ico" />
<!-- Loading Bootstrap -->
<link href="/assets/css/backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">

<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
  <script src="/assets/js/html5shiv.js"></script>
  <script src="/assets/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
    var require = {
        config:  <?php echo json_encode($config); ?>
    };
</script>
    </head>

    <body class="inside-header inside-aside <?php echo defined('IS_DIALOG') && IS_DIALOG ? 'is-dialog' : ''; ?>">
        <div id="main" role="main">
            <div class="tab-content tab-addtabs">
                <div id="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <section class="content-header hide">
                                <h1>
                                    <?php echo __('Dashboard'); ?>
                                    <small><?php echo __('Control panel'); ?></small>
                                </h1>
                            </section>
                            <?php if(!IS_DIALOG && !\think\Config::get('fastadmin.multiplenav')): ?>
                            <!-- RIBBON -->
                            <div id="ribbon">
                                <ol class="breadcrumb pull-left">
                                    <li><a href="dashboard" class="addtabsit"><i class="fa fa-dashboard"></i> <?php echo __('Dashboard'); ?></a></li>
                                </ol>
                                <ol class="breadcrumb pull-right">
                                    <?php foreach($breadcrumb as $vo): ?>
                                    <li><a href="javascript:;" data-url="<?php echo $vo['url']; ?>"><?php echo $vo['title']; ?></a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                            <!-- END RIBBON -->
                            <?php endif; ?>
                            <div class="content">
                                <form id="edit-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">

    <!--<div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Group_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-group_id" data-rule="required" data-source="group/index" class="form-control selectpage" name="row[group_id]" type="text" value="<?php echo htmlentities($row['group_id']); ?>">
        </div>
    </div>-->
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Number'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-number" data-rule="required" class="form-control" readonly name="row[number]" type="text" value="<?php echo htmlentities($row['number']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Username'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-username" data-rule="required" class="form-control" readonly name="row[username]" type="text" value="<?php echo htmlentities($row['username']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Nickname'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-nickname" data-rule="required" class="form-control" name="row[nickname]" type="text" value="<?php echo htmlentities($row['nickname']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Password'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-password" data-rule="" placeholder="不修改密码请留空" class="form-control" name="row[password]" type="text" value="">
        </div>
    </div>

    <!--<div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Mobile'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-mobile" data-rule="required" class="form-control" name="row[mobile]" type="text" value="<?php echo htmlentities($row['mobile']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Avatar'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-avatar" data-rule="required" class="form-control" size="50" name="row[avatar]" type="text" value="<?php echo htmlentities($row['avatar']); ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-avatar" class="btn btn-danger plupload" data-input-id="c-avatar" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="false" data-preview-id="p-avatar"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-avatar" class="btn btn-primary fachoose" data-input-id="c-avatar" data-mimetype="image/*" data-multiple="false"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-avatar"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-avatar"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Level'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-level" data-rule="required" class="form-control" name="row[level]" type="number" value="<?php echo htmlentities($row['level']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Gender'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-gender" data-rule="required" class="form-control" name="row[gender]" type="number" value="<?php echo htmlentities($row['gender']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Money'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-money" data-rule="required" class="form-control" step="0.01" name="row[money]" type="number" value="<?php echo htmlentities($row['money']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Successions'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-successions" data-rule="required" class="form-control" name="row[successions]" type="number" value="<?php echo htmlentities($row['successions']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Maxsuccessions'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-maxsuccessions" data-rule="required" class="form-control" name="row[maxsuccessions]" type="number" value="<?php echo htmlentities($row['maxsuccessions']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Prevtime'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-prevtime" class="form-control datetimepicker" data-date-format="YYYY-MM-DD HH:mm:ss" data-use-current="true" name="row[prevtime]" type="text" value="<?php echo $row['prevtime']?datetime($row['prevtime']):''; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Logintime'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-logintime" class="form-control datetimepicker" data-date-format="YYYY-MM-DD HH:mm:ss" data-use-current="true" name="row[logintime]" type="text" value="<?php echo $row['logintime']?datetime($row['logintime']):''; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Loginip'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-loginip" data-rule="required" class="form-control" name="row[loginip]" type="text" value="<?php echo htmlentities($row['loginip']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Loginfailure'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-loginfailure" data-rule="required" class="form-control" name="row[loginfailure]" type="number" value="<?php echo htmlentities($row['loginfailure']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Joinip'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-joinip" data-rule="required" class="form-control" name="row[joinip]" type="text" value="<?php echo htmlentities($row['joinip']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Jointime'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-jointime" class="form-control datetimepicker" data-date-format="YYYY-MM-DD HH:mm:ss" data-use-current="true" name="row[jointime]" type="text" value="<?php echo $row['jointime']?datetime($row['jointime']):''; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Token'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-token" data-rule="required" class="form-control" name="row[token]" type="text" value="<?php echo htmlentities($row['token']); ?>">
        </div>
    </div>-->
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Status'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <?php if(is_array($statusList) || $statusList instanceof \think\Collection || $statusList instanceof \think\Paginator): if( count($statusList)==0 ) : echo "" ;else: foreach($statusList as $key=>$vo): ?>
            <label for="row[status]-<?php echo $key; ?>"><input id="row[status]-<?php echo $key; ?>" name="row[status]" type="radio" value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['status'])?$row['status']:explode(',',$row['status']))): ?>checked<?php endif; ?> /> <?php echo $vo; ?></label> 
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>

        </div>
    </div>
    <!--<div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Verification'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-verification" data-rule="required" class="form-control" name="row[verification]" type="text" value="<?php echo htmlentities($row['verification']); ?>">
        </div>
    </div>-->
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Google_code'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-google_code" class="form-control" readonly name="row[google_code]" type="text" value="<?php echo htmlentities($row['google_code']); ?>">
        </div>
    </div>
    <div class="form-group layer-footer">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8">
            <button type="submit" class="btn btn-success btn-embossed disabled"><?php echo __('OK'); ?></button>
            <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
        </div>
    </div>
</form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>