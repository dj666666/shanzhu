define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'statistics/statistics/index' + location.search,
                    //add_url: 'statistics/statistics/add',
                    //edit_url: 'statistics/statistics/edit',
                    //del_url: 'statistics/statistics/del',
                    //multi_url: 'statistics/statistics/multi',
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
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'username', title: __('用户名'),operate:false},
                        {field: 'createtime', title: __('时间'),visible: false, operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'all_money', title: __('总金额'),operate:false},
                        {field: 'all_success_money', title: __('成功金额'),operate:false},
                        {field: 'all_faile_money', title: __('异常金额'),operate:false},
                        {field: 'all_order', title: __('订单数量'),operate:false},
                        {field: 'all_success_order', title: __('成功订单数量'),operate:false},
                        {field: 'all_faile_order', title: __('异常订单数量'),operate:false},
                    ]
                ],
                earch:false,
                search:false,
                showSearch: false,
                searchFormVisible: true,
                showToggle: false,
                showColumns: false,
                showExport: false,
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            table.off('dbl-click-row.bs.table');

            //当表格数据加载完成时
            table.on('load-success.bs.table', function (e, data) {
                //这里可以获取从服务端获取的JSON数据
                //这里我们手动设置底部的值
                $("#allmoney").text(data.extend.allmoney);
                $("#allsuccessmoney").text(data.extend.allsuccessmoney);
                $("#allfailemoney").text(data.extend.allfailemoney);
                $("#allorder").text(data.extend.allorder);
                $("#allsuccessorder").text(data.extend.allsuccessorder);
                $("#allfaileorder").text(data.extend.allfaileorder);
                $("#allfees").text(data.extend.allfees);
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
