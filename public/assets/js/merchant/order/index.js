define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/index/index' + location.search,
                    add_url: 'order/index/add',
                    //edit_url: 'order/index/edit',
                    //del_url: 'order/index/del',
                    //multi_url: 'order/index/multi',
                    table: 'order',
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
                        //{field: 'user.id', title: __('User.id')},
                        {field: 'user.username', title: __('所属用户')},
                        {field: 'out_trade_no', title: __('Out_trade_no')},
                        {field: 'bank_user', title: __('Bank_user')},
                        {field: 'bank_number', title: __('Bank_number')},
                        {field: 'bank_type', title: __('Bank_type')},
                        //{field: 'bank_from', title: __('Bank_from'),operate:false},
                        {field: 'amount', title: __('Amount'), operate:false},
                        {field: 'fees', title: __('手续费'), operate:false},
                        {field: 'status', title: __('Status'), searchList: {"1":__('Status 1'),"2":__('Status 2'),"3":__('Status 3'),"4":__('Status 4'),"5":__('Status 5')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'ordertime', title: __('更新时间'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        //{field: 'acount', title: __('Acount'), operate:false},

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
            });
            
            // 监听事件
            $(document).on("click", ".btn-batchadd", function () {
            
                //发送给控制器
                Fast.api.open("order.index/batch_add", "批量提交代付单", {
                    callback: function (value) {
                
                    }
                });
            
            });
            
        },
        add: function () {
            Controller.api.bindevent();
        },
        batch_add: function () {
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
