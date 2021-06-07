/**
  扩展一个 common 模块
**/      
 
layui.define(function(exports){ //提示：模块也可以依赖其它模块，如：layui.define('mod1', callback);
    var $ = layui.$;
    var layer = layui.layer;
    var obj = {
        doRequestDeferred: function (url, data, type = 'POST') {
            // $.when(common.doRequest(url, {'ids': data.id})).done(function (data) {
            //     // 获取异步方法的返回值
            // });
            var defer = $.Deferred();
            $.ajax({
                url: url,
                data: data,
                dataType: 'json',
                error: function (xhr,status,error) {
                    console.log(error);
                },
                success: function (result,status,xhr) {
                    if (result.success) {
                        if (result.message) {
                            layer.msg(result.message, {
                                icon: 1,
                                time: 1000
                            }, function(){
                                // var iframeIndex = parent.layer.getFrameIndex(window.name);
                                // parent.layer.close(iframeIndex);
                                if (result.url) {
                                    if (result.url == 'reload') {
                                        window.location.reload();
                                    } else if (result.url == 'close_layer') {
                                        parent.location.reload();
                                    } else {
                                        window.location.href = result.url;
                                    }
                                }
                            });
                        } else {
                            if (result.url) {
                                if (result.url == 'reload') {
                                    window.location.reload();
                                } else if (result.url == 'close_layer') {
                                    parent.location.reload();
                                } else {
                                    window.location.href = result.url;
                                }
                            }
                        }
                    } else {
                        layer.msg(result.message);
                    }
                    // 返回值调用resolve方法
                    defer.resolve(result);
                },
                type: type
            });
            return defer;
        },
        doRequest: function (url, data, type = 'POST') {
            return new Promise(function (resolve, reject) {
                $.ajax({
                    url: url,
                    data: data,
                    dataType: 'json',
                    error: function (xhr,status,error) {
                        console.log(error);
                    },
                    success: function (result,status,xhr) {
                        if (result.success) {
                            if (result.message) {
                                layer.msg(result.message, {
                                    icon: 1,
                                    time: 1000
                                }, function(){

                                    if (result.url) {
                                        if (result.url == 'reload') {
                                            window.location.reload();
                                        } else if (result.url == 'close_layer') {
                                            // var iframeIndex = parent.layer.getFrameIndex(window.name);
                                            // parent.layer.close(iframeIndex);
                                            parent.location.reload();
                                        } else {
                                            window.location.href = result.url;
                                        }
                                    }
                                });
                            } else {
                                if (result.url) {
                                    if (result.url == 'reload') {
                                        window.location.reload();
                                    } else if (result.url == 'close_layer') {
                                        // var iframeIndex = parent.layer.getFrameIndex(window.name);
                                        // parent.layer.close(iframeIndex);
                                        parent.location.reload();
                                    } else {
                                        window.location.href = result.url;
                                    }
                                }
                            }
                        } else {
                            layer.msg(result.message);
                        }
                        // 返回值调用resolve方法
                        resolve(result);
                    },
                    type: type
                });
            })
        },
        asyncDoRequest: async function (url, data = {}, options = {}){
            let fn = () => {};
            let method = options.method || 'POST'
            let success = options.success || fn // 成功
			let fail = options.fail || fn // 失败
            var res = await this.doRequest(url, data, method);
            if (res.success) {
                success(res)
            } else {
                fail(res)
            }
        },
        jumpToPage: function (page_uri, per_page) {
            var page = $('#jump_page').val();
            if (page > 0 && page_uri) {
                page = (page - 1) * per_page;
                window.location.href = page_uri + page + '.html';
            }
        }
    };
    
    //输出 common 接口
    exports('common', obj);
  }); 