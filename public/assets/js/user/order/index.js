define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/index/index' + location.search,
                    //add_url: 'order/index/add',
                    //edit_url: 'order/index/edit',
                    //del_url: 'order/index/del',
                    multi_url: 'order/index/multi',
                    table: 'order',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                sortOrder: 'asc',
                columns: [
                    [
                        //{checkbox: true},
                        //{field: 'id', title: __('Id'), operate:false},
                        //{field: 'user_id', title: __('User_id')},
                        //{field: 'user.username', title: __('User_id'), operate:false},
                        {field: 'out_trade_no', title: __('Out_trade_no')},
                        //{field: 'bank_user', title: __('Bank_user'), events: Controller.api.xiaobao, formatter: Controller.api.formatter.ip},
                        //{field: 'bank_number', title: __('Bank_number'), events: Controller.api.xiaobao, formatter: Controller.api.formatter.ip},
                        //{field: 'bank_type', title: __('Bank_type'), events: Controller.api.xiaobao, formatter: Controller.api.formatter.ip, operate:false},
                        //{field: 'bank_from', title: __('Bank_from'), events: Controller.api.xiaobao, formatter: Controller.api.formatter.ip, operate:false},
                        //{field: 'amount', title: __('Amount'), operate:false, events: Controller.api.xiaobao, formatter: Controller.api.formatter.ip},

                        {field: 'bank_user', title: __('Bank_user'), events: Controller.api.xiaobao, formatter:function(value,row,index){
                                if(row.status === '2'){
                                    return '<a href="javascript:;"  data-clipboard-text="'+value+'"class="btn btn-xs btn-fuzhi btn-success" data-toggle="tooltip" title="" data-table-id="table" data-field-index="13" data-row-index="0" data-button-index="1" data-original-title="点击复制">'+value+'</a>';
                                }else{
                                    return value;
                                }
                            }

                        },
                        {field: 'bank_number', title: __('Bank_number'), events: Controller.api.xiaobao, formatter:function(value,row,index){
                                if(row.status === '2'){
                                    return '<a href="javascript:;"  data-clipboard-text="'+value+'"class="btn btn-xs btn-fuzhi btn-success" data-toggle="tooltip" title="" data-table-id="table" data-field-index="13" data-row-index="0" data-button-index="1" data-original-title="点击复制">'+value+'</a>';
                                }else{
                                    return value;
                                }
                            }

                        },
                        {field: 'bank_type', title: __('Bank_type'), operate:false, events: Controller.api.xiaobao, formatter:function(value,row,index){
                                if(row.status === '2'){
                                    return '<a href="javascript:;"  data-clipboard-text="'+value+'"class="btn btn-xs btn-fuzhi btn-success" data-toggle="tooltip" title="" data-table-id="table" data-field-index="13" data-row-index="0" data-button-index="1" data-original-title="点击复制">'+value+'</a>';
                                }else{
                                    return value;
                                }
                            }

                        },
                        /*{field: 'bank_from', title: __('Bank_from'), operate:false, events: Controller.api.xiaobao, formatter:function(value,row,index){
                                if(row.status === '2'){
                                    return '<a href="javascript:;"  data-clipboard-text="'+value+'"class="btn btn-xs btn-fuzhi btn-success" data-toggle="tooltip" title="" data-table-id="table" data-field-index="13" data-row-index="0" data-button-index="1" data-original-title="点击复制">'+value+'</a>';
                                }else{
                                    return value;
                                }
                            }

                        },*/
                        {field: 'amount', title: __('Amount'), operate:false, events: Controller.api.xiaobao, formatter:function(value,row,index){
                                if(row.status === '2'){
                                    return '<a href="javascript:;"  data-clipboard-text="'+value+'"class="btn btn-xs btn-fuzhi btn-success" data-toggle="tooltip" title="" data-table-id="table" data-field-index="13" data-row-index="0" data-button-index="1" data-original-title="点击复制">'+value+'</a>';
                                }else{
                                    return value;
                                }
                            }

                        },

                        //{field: 'fees', title: __('手续费'), operate:'BETWEEN', events: Controller.api.xiaobao, formatter: Controller.api.formatter.ip, operate:false},
                        {field: 'status', title: __('Status'), searchList: {"1":__('Status 1'),"2":__('Status 2'),"3":__('Status 3'),"4":__('Status 4'),"5":__('Status 5')}, formatter: Table.api.formatter.status,defaultValue:2},
                        //{field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,defaultValue:this.today(0)+' 00:00:00 - '+this.today(0)+ ' 23:59:59' },
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,},
                        //{field: 'ordertime', title: __('Ordertime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'acount', title: __('Acount'), operate:false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'ajax',
                                    text: '下发',
                                    title: __('下发'),
                                    classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                    icon: 'fa fa-check',
                                    url: 'order.index/xiafa',
                                    hidden:function(row){
                                        if(row.status == 1 || row.status == 4){
                                            return true;
                                        }

                                    },
                                    success: function (data, ret) {
                                        table.bootstrapTable('refresh');

                                        //Toastr.info(ret.msg);
                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                    },
                                    error: function (data, ret) {
                                        //Toastr.error(ret.msg);
                                        return false;
                                    }
                                },
                                {
                                    name: 'ajax',
                                    text: '异常',
                                    title: __('异常'),
                                    classname: 'btn btn-xs btn-danger btn-magic btn-ajax',
                                    icon: 'fa fa-close',
                                    url: 'order.index/yichang',
                                    hidden:function(row){
                                        if(row.status == 1 || row.status == 4){
                                            return true;
                                        }

                                    },
                                    success: function (data, ret) {
                                        table.bootstrapTable('refresh');

                                        //Toastr.info(ret.msg);
                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                    },
                                    error: function (data, ret) {
                                        //Toastr.error(ret.msg);

                                        return false;
                                    }
                                },
                                {
                                    name: 'ajax',
                                    text: '回充',
                                    title: __('回充'),
                                    classname: 'btn btn-xs btn-info btn-magic btn-ajax',
                                    icon: 'fa fa-exclamation',
                                    url: 'order.index/huichong',

                                    success: function (data, ret) {
                                        table.bootstrapTable('refresh');

                                        //Toastr.info(ret.msg);
                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                    },
                                    error: function (data, ret) {
                                        //Toastr.error(ret.msg);

                                        return false;
                                    }
                                },
                            ],

                        }
                    ]
                ],
            	search:false,
                showSearch: false,
                searchFormVisible: true,

            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            table.off('dbl-click-row.bs.table');

            //当表格数据加载完成时
            table.on('load-success.bs.table', function (e, data) {
                //这里可以获取从服务端获取的JSON数据
                //这里我们手动设置底部的值
               //这里可以获取从服务端获取的JSON数据

                var myAuto2 = document.getElementById("myaudio2");
                if(data.extend.findapply === 1){
                    Toastr.warning('您有新的充值订单');
                    myAuto2.play();
                }else{
                    myAuto2.pause();
                }

                var myAuto = document.getElementById("myaudio");
                if(data.total > 0){
                    myAuto.play();

                }else{
                    myAuto.pause();

                }

            });

            var clipboard = new ClipboardJS('.btn-fuzhi');
            clipboard.on('success', function(e) {
                Toastr.info('复制成功');
            });

            clipboard.on('error', function(e) {
                alert('复制失败');
            });

            // 启动和暂停按钮
            $(document).on("click", ".btn-start", function () {
                startTimer();
            });
            $(document).on("click", ".btn-pause", function () {
                stopTimer();
            });
            var timer = null;

            $(function(){
                startTimer();
            });


            function startTimer(){
               timer = setInterval(function(){
                   $(".btn-refresh").trigger("click");
                   /*Fast.api.ajax({
                       url:'order/index/queryOrder',
                       data:{id:Config.uid}
                   }, function(data, ret){
                       //成功的回调
                       if(ret.code == 1){
                           //stopTimer();
                           $(".btn-refresh").trigger("click");
                       }
                       return false;
                   }, function(data, ret){
                       //失败的回调
                       //alert(ret.msg);
                       return false;
                   });*/

               }, 15000);
            }

            function stopTimer(){
               clearInterval(timer);
           }

            // 下发和异常按钮
            $(document).on("click", "#xiafa", function () {
                var that = this;

                var ids = Table.api.selectedids(table);
                ids = ids.join(",");
                Layer.confirm(
                    __('确定下发?', ids.length),
                    {icon: 3, title: __('Warning'), offset: 0, shadeClose: true},
                    function (index) {
                        Backend.api.ajax({
                            url: "acc/bdd/delexp?ids="+ids,
                            data: $(that).closest("form").serialize()
                        });
                        table.bootstrapTable('refresh');
                        Layer.close(index);
                    }
                );
            });
            $(document).on("click", "#yichang", function () {
                var that = this;
                var ids = Table.api.selectedids(table);
                Layer.confirm(
                    __('确定异常?', ids.length),
                    {icon: 3, title: __('Warning'), offset: 0, shadeClose: true},
                    function (index) {
                        Backend.api.ajax({
                            url: "acc/bdd/delexp",
                            data: $(that).closest("form").serialize()
                        });
                        table.bootstrapTable('refresh');
                        Layer.close(index);
                    }
                );
            });


        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {//渲染的方法
                ip: function (value, row, index) {
                    return '<a href="javascript:;"  data-clipboard-text="'+value+'"class="btn btn-xs btn-fuzhi btn-success" data-toggle="tooltip" title="" data-table-id="table" data-field-index="13" data-row-index="0" data-button-index="1" data-original-title="点击复制">'+value+'</a>';
                },
            },
            xiaobao:{
                'click .btn-fuzhi': function (e, value, row, index) {

                }
            }
        },
        today:function(AddDayCount){
        	var dd = new Date();
        	dd.setDate(dd.getDate() + AddDayCount);
        	var y = dd.getFullYear();
        	var m = dd.getMonth()+1;
        	var d = dd.getDate();

        	//判断月
        	if(m <10){
        		m = "0" + m;
        	}else{
        		m = m;
        	}

        	//判断日
        	if(d<10){ //如果天数小于10
        		d = "0" + d;
        	}else{
        		d = d;
        	}
        	return y+"-"+m+"-"+d;
        }
    };
    return Controller;
});
