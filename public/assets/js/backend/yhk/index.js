define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'yhk/index/index' + location.search,
                    add_url: 'yhk/index/add',
                    edit_url: 'yhk/index/edit',
                    del_url: 'yhk/index/del',
                    multi_url: 'yhk/index/multi',
                    table: 'yhk',
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
                        //field: 'user_id', title: __('User_id')},
                        {field: 'user.username', title: __('所属用户')},
                        {field: 'bank_user', title: __('Bank_user')},
                        {field: 'bank_name', title: __('Bank_name')},
                        {field: 'bank_number', title: __('Bank_number')},
                        {field: 'bank_type', title: __('Bank_type')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'remark', title: __('Remark')},
                        {field: 'is_state', title: __('Is_state'), searchList: {"0":__('Is_state 0'),"1":__('Is_state 1')}, formatter: Table.api.formatter.normal},
                        {field: 'money', title: __('Money'), operate:'BETWEEN'},
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