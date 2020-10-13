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
                        {field: 'bank_user', title: __('Bank_user')},
                        {field: 'bank_name', title: __('Bank_name')},
                        {field: 'bank_number', title: __('Bank_number')},
                        {field: 'bank_type', title: __('Bank_type')},
                        {field: 'remainmoney', title: __('已收金额')},
                        {field: 'money', title: __('剩余额度')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'remark', title: __('Remark')},
                        {field: 'is_state', title: __('Is_state'), searchList: {"0":__('Is_state 0'),"1":__('Is_state 1')}, formatter: Table.api.formatter.normal},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                        	buttons: [
                                {
                                    name: 'ajax',
                                    text: '重置额度',
                                    title: __('重置额度'),
                                    classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                    icon: 'fa fa-check',
                                    url: 'yhk.index/resetMoney',
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