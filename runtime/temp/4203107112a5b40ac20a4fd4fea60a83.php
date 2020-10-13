<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:88:"/home/wwwroot/pt.jinniupaid.com/public/../application/merchant/view/order/index/add.html";i:1598716948;s:77:"/home/wwwroot/pt.jinniupaid.com/application/merchant/view/layout/default.html";i:1588765312;s:74:"/home/wwwroot/pt.jinniupaid.com/application/merchant/view/common/meta.html";i:1588765312;s:76:"/home/wwwroot/pt.jinniupaid.com/application/merchant/view/common/script.html";i:1588765312;}*/ ?>
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
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('User_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-user_id" data-rule="required" data-source="user/user/index" data-field="nickname" class="form-control selectpage" name="row[user_id]" type="text" value="">
        </div>
    </div>-->
    <!--<div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Out_trade_no'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-out_trade_no" data-rule="required" class="form-control" name="row[out_trade_no]" type="text" value="">
        </div>
    </div>-->
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Bank_user'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-bank_user" data-rule="required" class="form-control" autocomplete="off" name="row[bank_user]" type="text" value="">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Bank_number'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-bank_number" data-rule="required" class="form-control" autocomplete="off" name="row[bank_number]" type="text" value="">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Bank_type'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-bank_type" data-rule="required" class="form-control" autocomplete="off" name="row[bank_type]" type="text">
        </div>
    </div>
    <!--<div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Bank_from'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-bank_from" data-rule="required" class="form-control" autocomplete="off" name="row[bank_from]" type="text" value="">
        </div>
    </div>-->

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Amount'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-amount" data-rule="required;range(1~)" class="form-control"  name="row[amount]" type="number" value="">
        </div>
    </div>
    <!--<div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Status'); ?>:</label>
        <div class="col-xs-12 col-sm-8">

            <div class="radio">
            <?php if(is_array($statusList) || $statusList instanceof \think\Collection || $statusList instanceof \think\Paginator): if( count($statusList)==0 ) : echo "" ;else: foreach($statusList as $key=>$vo): ?>
            <label for="row[status]-<?php echo $key; ?>"><input id="row[status]-<?php echo $key; ?>" name="row[status]" type="radio" value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',"2"))): ?>checked<?php endif; ?> /> <?php echo $vo; ?></label>
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>

        </div>
    </div>-->

    <!--<div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('谷歌验证码'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-google_captcha" data-rule="required" class="form-control" name="row[google_captcha]" type="text" value="">
        </div>
    </div>-->
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('支付密码'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-pay_pwd" data-rule="required" class="form-control" name="row[pay_pwd]" type="password" value="">
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