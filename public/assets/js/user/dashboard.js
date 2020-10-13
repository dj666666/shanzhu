define(['jquery', 'bootstrap', 'backend', 'addtabs', 'table', 'echarts', 'echarts-theme', 'template'], function ($, undefined, Backend, Datatable, Table, Echarts, undefined, Template) {

    var Controller = {
        index: function () {
            // 启动接单 和 停止按钮
            $(document).on("click", "#start", function () {
                console.log('11');
                Fast.api.ajax({
                    url:'dashboard/changeuser',
                    data:{id:Config.uid}
                }, function(data, ret){
                    if(ret.code == 1){
                        Toastr.info(ret.msg);
                        window.location.reload();
                    }
                    return false;
                }, function(data, ret){

                    //alert(ret.msg);
                    return false;
                });
            });

            // 启动和暂停按钮
            $(document).on("click", "#stop", function () {
                console.log('11');
                Fast.api.ajax({
                    url:'dashboard/changeuser',
                    data:{id:Config.uid}
                }, function(data, ret){
                    if(ret.code == 1){
                        Toastr.info(ret.msg);
                        window.location.reload();
                    }
                    return false;
                }, function(data, ret){
                    //alert(ret.msg);
                    return false;
                });
            });
        }
    };

    return Controller;
});