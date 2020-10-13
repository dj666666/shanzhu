define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'yhk/rate/index' + location.search,
                    add_url: 'yhk/rate/add',
                    edit_url: 'yhk/rate/edit',
                    del_url: 'yhk/rate/del',
                    multi_url: 'yhk/rate/multi',
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
                        {field: 'id', title: __('Id')},
                        //{field: 'user_id', title: __('User_id')},
                        //{field: 'mer_id', title: __('Mer_id')},
                        //{field: 'agent_id', title: __('Agent_id')},
                        //{field: 'yhk_id', title: __('Yhk_id')},
                        {field: 'merchant.username', title: __('Mer_id')},
                        {field: 'user.username', title: __('User_id')},
                        {field: 'out_trade_no', title: __('Out_trade_no')},
                        {field: 'bank_name', title: __('Bank_name')},
                        {field: 'bank_user', title: __('Bank_user')},
                        {field: 'bank_number', title: __('Bank_number')},
                        {field: 'bank_type', title: __('Bank_type')},
                        {field: 'amount', title: __('Amount'), operate:'BETWEEN'},
                        {field: 'status', title: __('Status'), searchList: {"1":__('Status 1'),"2":__('Status 2'),"3":__('Status 3'),"4":__('Status 4'),"5":__('Status 5')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'remark', title: __('Remark')},
                        
                        
                        //{field: 'yhk.bank_user', title: __('Yhk.bank_user')},
                        //{field: 'yhk.bank_name', title: __('Yhk.bank_name')},
                        //{field: 'yhk.bank_number', title: __('Yhk.bank_number')},
                        //{field: 'yhk.bank_type', title: __('Yhk.bank_type')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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