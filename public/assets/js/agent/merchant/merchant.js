define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'merchant/merchant/index' + location.search,
                    add_url: 'merchant/merchant/add',
                    edit_url: 'merchant/merchant/edit',
                    del_url: 'merchant/merchant/del',
                    multi_url: 'merchant/merchant/multi',
                    table: 'merchant',
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
                        //{field: 'id', title: __('Id')},
                        //{field: 'group_id', title: __('Group_id')},
                        //{field: 'agent_id', title: __('Agent_id')},
                        {field: 'merchantgroup.name', title: __('Merchantgroup.name'),operate: false},
                        //{field: 'agent.username', title: __('所属代理')},
                        //{field: 'number', title: __('商户编号')},
                        {field: 'username', title: __('Username')},
                        {field: 'nickname', title: __('Nickname'),operate: false},
                        {field: 'money', title: __('Money'),operate: false},
                        {field: 'rate', title: __('Rate'),operate: false},
                        {field: 'add_money', title: __('Add_Money'),operate: false},
                        {field: 'logintime', title: __('Logintime'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'loginip', title: __('Loginip'),operate: false},
                        {field: 'jointime', title: __('Jointime'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'),operate: false, formatter: Table.api.formatter.status},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑 定事件
            Table.api.bindevent(table);
            table.off('dbl-click-row.bs.table');

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