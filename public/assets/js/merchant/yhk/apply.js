define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'yhk/apply/index' + location.search,
                    add_url: 'yhk/apply/add',
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
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        //{field: 'user_id', title: __('User_id')},
                        //{field: 'mer_id', title: __('Mer_id')},
                        //{field: 'yhk_id', title: __('Yhk_id')},
                        //{field: 'merchant.username', title: __('所属商户')},
                        {field: 'user.username', title: __('所属用户')},
                        {field: 'out_trade_no', title: __('Out_trade_no')},
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
                        {field: 'updatetime', title: __('Updatetime'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'remark', title: __('Remark'),operate:false},
                        //{field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
                $("#money").text(data.extend.money);
                $("#rate_money").text(data.extend.rate_money);
                $("#block_money").text(data.extend.block_money);
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
