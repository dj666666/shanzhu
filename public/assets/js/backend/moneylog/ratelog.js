define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'moneylog/ratelog/index' + location.search,
                    add_url: 'moneylog/ratelog/add',
                    //edit_url: 'moneylog/ratelog/edit',
                    del_url: 'moneylog/ratelog/del',
                    //multi_url: 'moneylog/ratelog/multi',
                    table: 'rate_money_log',
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
                        //{field: 'agent_id', title: __('Agent_id')},
                        //{field: 'mer_id', title: __('Mer_id')},
                        {field: 'agent.username', title: __('Agent_id')},
                        {field: 'merchant.username', title: __('Mer_id')},
                        {field: 'out_trade_no', title: __('Out_trade_no')},
                        {field: 'type', title: __('Type'), searchList: {"0":__('Type 0'),"1":__('Type 1')}, formatter: Table.api.formatter.normal},
                        {field: 'amount', title: __('Amount'), operate:'BETWEEN'},
                        {field: 'before_amount', title: __('Before_amount'), operate:'BETWEEN'},
                        {field: 'after_amount', title: __('After_amount'), operate:'BETWEEN'},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'update_time', title: __('Update_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'remark', title: __('Remark')},
                        
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