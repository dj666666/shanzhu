define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'moneylog/moneylog/index' + location.search,
                    //add_url: 'moneylog/moneylog/add',
                    //edit_url: 'moneylog/moneylog/edit',
                    //del_url: 'moneylog/moneylog/del',
                    //multi_url: 'moneylog/moneylog/multi',
                    table: 'money_log',
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
                        {field: 'id', title: __('Id'),operate: false},
                        //{field: 'agent_id', title: __('Agent_id')},
                        //{field: 'mer_id', title: __('Mer_id')},
                        //{field: 'agent.username', title: __('所属代理')},
                        {field: 'merchant.username', title: __('所属商户')},
                        {field: 'out_trade_no', title: __('Out_trade_no')},
                        {field: 'type', title: __('Type'), searchList: {"0":__('Type 0'),"1":__('Type 1')}, formatter: Table.api.formatter.normal},
                        {field: 'amount', title: __('Amount'), operate:false},
                        {field: 'before_amount', title: __('Before_amount'), operate:false},
                        {field: 'after_amount', title: __('After_amount'), operate:false},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'update_time', title: __('Update_time'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'remark', title: __('Remark'), operate:false},

                        //{field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                search:false,
                showSearch: false,
                searchFormVisible: true,
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