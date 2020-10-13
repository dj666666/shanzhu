<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:82:"/home/wwwroot/pt.jinniupaid.com/public/../application/user/view/yhk/index/add.html";i:1596528640;s:73:"/home/wwwroot/pt.jinniupaid.com/application/user/view/layout/default.html";i:1588765312;s:70:"/home/wwwroot/pt.jinniupaid.com/application/user/view/common/meta.html";i:1588765312;s:72:"/home/wwwroot/pt.jinniupaid.com/application/user/view/common/script.html";i:1588765312;}*/ ?>
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
                                <form id="add-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">
	
	<!--<div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('所属商户'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-username" data-rule="required" data-source="auth.admin/getsh" class="form-control selectpage" data-field='username' name="row[username]"  type="text" value="">
        </div>
    </div>-->
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Bank_user'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-bank_user" data-rule="required" class="form-control" name="row[bank_user]" type="text" value="">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Bank_name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-bank_name" data-rule="required" class="form-control" name="row[bank_name]" type="text" value="">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Bank_number'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-bank_number" data-rule="required" class="form-control" name="row[bank_number]" type="text" value="">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Bank_type'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-bank_type" data-rule="required" class="form-control" name="row[bank_type]" type="text">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Remark'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-remark" class="form-control" name="row[remark]" type="text" value="">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Is_state'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <?php if(is_array($isStateList) || $isStateList instanceof \think\Collection || $isStateList instanceof \think\Paginator): if( count($isStateList)==0 ) : echo "" ;else: foreach($isStateList as $key=>$vo): ?>
            <label for="row[is_state]-<?php echo $key; ?>"><input id="row[is_state]-<?php echo $key; ?>" name="row[is_state]" type="radio" value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',"1"))): ?>checked<?php endif; ?> /> <?php echo $vo; ?></label> 
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>

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