define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'yhk/apply/index' + location.search,
                    //add_url: 'yhk/apply/add',
                    //edit_url: 'yhk/apply/edit',
                    //del_url: 'yhk/apply/del',
                    //multi_url: 'yhk/apply/multi',
                    table: 'applys',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        //{checkbox: true},
                        //{field: 'id', title: __('Id'),operate:false},
                        //{field: 'admin_id', title: __('Admin_id')},
                        //{field: 'yhk_id', title: __('Yhk_id')},
                        {field: 'out_trade_no', title: __('Out_trade_no')},
                        {field: 'merchant.username', title: __('所属商户')},
                        //{field: 'yhk.bank_user', title: __('Yhk.bank_user')},
                        {field: 'bank_user', title: __('Yhk.bank_user')},
                        //{field: 'yhk.bank_name', title: __('Yhk.bank_name')},
                        {field: 'bank_name', title: __('Yhk.bank_name')},
                        //{field: 'yhk.bank_number', title: __('Yhk.bank_number')},
                        {field: 'bank_number', title: __('Yhk.bank_number')},
                        //{field: 'yhk.bank_type', title: __('Yhk.bank_type')},
                        //{field: 'bank_type', title: __('Yhk.bank_type')},
                        {field: 'amount', title: __('Amount'), operate:false},
                        {field: 'status', title: __('Status'), searchList: {"1":__('Status 1'),"2":__('Status 2'),"3":__('Status 3'),"4":__('Status 4'),"5":__('Status 5')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        //{field: 'updatetime', title: __('Updatetime'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        //{field: 'remark', title: __('Remark'),operate:false},

                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'ajax',
                                    text: '收到',
                                    title: __('收到'),
                                    classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                    icon: 'fa fa-check',
                                    url: 'yhk.apply/received',
                                    hidden:function(row){
                                        if(row.status == 1 || row.status == 2 || row.status == 3 || row.status == 5){
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
                                    text: '未收到',
                                    title: __('未收到'),
                                    classname: 'btn btn-xs btn-danger btn-magic btn-ajax',
                                    icon: 'fa fa-close',
                                    url: 'yhk.apply/notreceived',
                                    hidden:function(row){
                                        if(row.status == 1 || row.status == 2 || row.status == 3 || row.status == 5){
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
                                    text: '冻结',
                                    title: __('冻结'),
                                    classname: 'btn btn-xs btn-info btn-magic btn-ajax',
                                    icon: 'fa fa-exclamation',
                                    url: 'yhk.apply/dongjie',
                                    hidden:function(row){
                                        if(row.status == 1 || row.status == 2 || row.status == 3 || row.status == 5){
                                            return true;
                                        }

                                    },
                                    success: function (data, ret) {
                                         table.bootstrapTable('refresh');
                                    	/*if(ret.code == 1){
                                    		table.bootstrapTable('refresh');
                                    	}else{
                                    		table.bootstrapTable('refresh');
                                    	}*/

                                        //Toastr.info(ret.msg);
                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                    },
                                    error: function (data, ret) {
                                        //Toastr.error(ret.msg);

                                        return false;
                                    }
                                }
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
                $("#apply_money").text(data.extend.apply_money);
                $("#yesterday_apply_money").text(data.extend.yesterday_apply_money);
                $("#all_apply_money").text(data.extend.all_apply_money);
                $("#severn_money").text(data.extend.severn_money);
                $("#all_f_apply_money").text(data.extend.all_f_apply_money);
                $("#sh_money").text(data.extend.sh_money);
                $("#rate_money").text(data.extend.rate_money);

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
            }
        }
    };
    return Controller;
});
