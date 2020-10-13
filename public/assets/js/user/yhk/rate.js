define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'yhk/rate/index' + location.search,
                    //add_url: 'yhk/rate/add',
                    //edit_url: 'yhk/rate/edit',
                    //del_url: 'yhk/rate/del',
                    //multi_url: 'yhk/rate/multi',
                    table: 'rate_apply',
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
                        {checkbox: true},
                        {field: 'id', title: __('Id'), operate:false},
                        //{field: 'user_id', title: __('User_id')},
                        //{field: 'mer_id', title: __('Mer_id')},
                        //{field: 'agent_id', title: __('Agent_id')},
                        //{field: 'yhk_id', title: __('Yhk_id')},
                        {field: 'merchant.username', title: __('Mer_id'), operate:false},
                        //{field: 'user.username', title: __('User.username'), operate:false},
                        {field: 'out_trade_no', title: __('Out_trade_no')},
                        {field: 'bank_name', title: __('Bank_name'), operate:false},
                        {field: 'bank_user', title: __('Bank_user'), operate:false},
                        {field: 'bank_number', title: __('Bank_number'), operate:false},
                        {field: 'bank_type', title: __('Bank_type'), operate:false},

                        {field: 'amount', title: __('Amount'), operate:false},
                        {field: 'status', title: __('Status'), searchList: {"1":__('Status 1'),"2":__('Status 2'),"3":__('Status 3'),"4":__('Status 4'),"5":__('Status 5')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        //{field: 'updatetime', title: __('Updatetime'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'remark', title: __('Remark'), operate:false},



                        //{field: 'yhk.bank_user', title: __('Yhk.bank_user')},
                        //{field: 'yhk.bank_name', title: __('Yhk.bank_name')},
                        //{field: 'yhk.bank_number', title: __('Yhk.bank_number')},
                        //{field: 'yhk.bank_type', title: __('Yhk.bank_type')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'ajax',
                                    text: '收到',
                                    title: __('收到'),
                                    classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                    icon: 'fa fa-check',
                                    url: 'yhk.rate/received',
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
                                    url: 'yhk.rate/notreceived',
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
                                /*{
                                    name: 'ajax',
                                    text: '冻结',
                                    title: __('冻结'),
                                    classname: 'btn btn-xs btn-info btn-magic btn-ajax',
                                    icon: 'fa fa-exclamation',
                                    url: 'yhk.rate/dongjie',
                                    hidden:function(row){
                                        if(row.status == 1 || row.status == 2 || row.status == 3 || row.status == 5){
                                            return true;
                                        }

                                    },
                                    success: function (data, ret) {
                                        table.bootstrapTable('refresh');
                                        /!*if(ret.code == 1){
                                            table.bootstrapTable('refresh');
                                        }else{
                                            table.bootstrapTable('refresh');
                                        }*!/

                                        //Toastr.info(ret.msg);
                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                    },
                                    error: function (data, ret) {
                                        //Toastr.error(ret.msg);

                                        return false;
                                    }
                                }*/
                            ],
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
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