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
                        {field: 'id', title: __('Id')},
                        //{field: 'group_id', title: __('Group_id')},
                        //{field: 'agent_id', title: __('Agent_id')},
                        {field: 'merchantgroup.name', title: __('Merchantgroup.name')},
                        {field: 'agent.username', title: __('所属代理')},
                        //{field: 'number', title: __('商户编号')},
                        {field: 'username', title: __('Username')},
                        {field: 'nickname', title: __('Nickname')},
                        {field: 'rate', title: __('Rate')},
                        {field: 'add_money', title: __('加')},
                        {field: 'money', title: __('余额')},
                        {field: 'block_money', title: __('冻结金额')},
                        {field: 'rate_money', title: __('手续费余额')},
                        {field: 'logintime', title: __('Logintime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'loginip', title: __('Loginip')},
                        {field: 'jointime', title: __('Jointime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.status},

                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'yes',
                                    text: '重置谷歌',
                                    title: __('重置谷歌'),
                                    classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                    icon: 'fa fa-check',
                                    url: 'merchant.merchant/resetGoogleKey',
                                    confirm:'确定要重置谷歌密钥?',
                                    success: function (data,ret) {

                                    },
                                    error: function (data,ret) {

                                    }
                                },
                            ]
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
