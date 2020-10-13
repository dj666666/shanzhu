define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/index/index' + location.search,
                    add_url: 'user/index/add',
                    edit_url: 'user/index/edit',
                    del_url: 'user/index/del',
                    multi_url: 'user/index/multi',
                    table: 'user',
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
                        {field: 'usergroup.name', title: __('Usergroup.name')},
                        {field: 'username', title: __('Username')},
                        {field: 'number', title: __('会员编号')},
                        {field: 'nickname', title: __('Nickname')},
                        {field: 'is_receive', title: __('在线接单'),formatter: Controller.api.formatter.custom},
                        //{field: 'mobile', title: __('Mobile')},
                        //{field: 'avatar', title: __('Avatar'), events: Table.api.events.image, formatter: Table.api.formatter.image},
                        //{field: 'level', title: __('Level')},
                        //{field: 'gender', title: __('Gender')},
                        //{field: 'money', title: __('Money'), operate:'BETWEEN'},
                        //{field: 'prevtime', title: __('Prevtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'logintime', title: __('Logintime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'loginip', title: __('Loginip')},
                        //{field: 'loginfailure', title: __('Loginfailure')},
                        //{field: 'joinip', title: __('Joinip')},
                        {field: 'jointime', title: __('Jointime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        //{field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        //{field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        //{field: 'token', title: __('Token')},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        //{field: 'verification', title: __('Verification')},
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
            },
            formatter: {//渲染的方法

                custom: function (value, row, index) {
                    //添加上btn-change可以自定义请求的URL进行数据处理
                    return '<a class="btn-change text-success" data-url="user/index/change" data-id="' + row.id + '"><i class="fa ' + (row.is_receive == '2' ? 'fa-toggle-on fa-flip-horizontal text-gray' : 'fa-toggle-on') + ' fa-2x"></i></a>';
                },
            },
        }
    };
    return Controller;
});