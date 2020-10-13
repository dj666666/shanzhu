<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:93:"E:\phpstudy_pro\WWW\daifuagent\public/../application/merchant\view\order\index\batch_add.html";i:1602487876;s:76:"E:\phpstudy_pro\WWW\daifuagent\application\merchant\view\layout\default.html";i:1588765312;s:73:"E:\phpstudy_pro\WWW\daifuagent\application\merchant\view\common\meta.html";i:1588765312;s:75:"E:\phpstudy_pro\WWW\daifuagent\application\merchant\view\common\script.html";i:1588765312;}*/ ?>
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


    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">批量添加:</label>
        <div class="col-xs-12 col-sm-10">
            <div id="type1">
                <div></div>
                <dl class="fieldlist" data-template="basictpl2" data-name="row[orderjson]">
                    <dd>
                        <ins><?php echo __('Bank_user'); ?></ins>
                        <ins><?php echo __('Bank_number'); ?></ins>
                        <ins><?php echo __('bank_type'); ?></ins>

                        <ins><?php echo __('Amount'); ?></ins>
                    </dd>
                    <dd><a href="javascript:;" class="btn btn-sm btn-success btn-append"><i class="fa fa-plus"></i>
                        <?php echo __('Append'); ?></a></dd>
                    <textarea name="row[orderjson]" class="form-control hide" cols="30" rows="5"></textarea>
                </dl>
                <script id="basictpl2" type="text/html">
                    <dd class="form-inline">
                        <ins><input type="text" name="<%=name%>[<%=index%>][bank_user]" class="form-control" value=""
                                    placeholder="开户人" size="8"/></ins>
                        <ins><input type="text" name="<%=name%>[<%=index%>][bank_number]" class="form-control" value=""
                                    placeholder="银行账户"/></ins>
                        <ins><input type="text" name="<%=name%>[<%=index%>][bank_type]" class="form-control" value=""
                                    placeholder="开户行"/></ins>

                        <ins><input type="text" name="<%=name%>[<%=index%>][amount]" class="form-control" value=""
                                    placeholder="金额"/></ins>
                        <!--下面的两个按钮务必保留-->
                        <span class="btn btn-sm btn-danger btn-remove"><i class="fa fa-times"></i></span>
                    </dd>
                </script>
            </div>

        </div>

    </div>


    <?php if(\think\Config::get('fastadmin.order_checkmerchantgoogle')): ?>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('谷歌验证码'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-google_captcha" data-rule="required" class="form-control" name="row[google_captcha]" type="text" value="">
        </div>
    </div>
    <?php endif; ?>
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