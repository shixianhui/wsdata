<style>
      .layui-form-item .layui-input-company {width: auto;padding-right: 10px;line-height: 38px;}
</style>
<div class="layuimini-container">
    <div class="layuimini-main">

        <div class="layui-form layuimini-form" lay-filter="saveForm">
            <div class="layui-form-item">
                <label class="layui-form-label required">网站名称</label>
                <div class="layui-input-block">
                    <input type="text" name="site_name" lay-verify="required" lay-reqtext="网站名称不能为空" placeholder="请输入网站名称"  value="" class="layui-input">
                    <tip>填写自己部署网站的名称。</tip>
                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label">缓存时间</label>
                <div class="layui-input-inline" style="width: 80px;">
                    <input type="text" name="cache_time" lay-verify="number" value="" class="layui-input">
                </div>
                <div class="layui-input-inline layui-input-company">分钟</div>
                <div class="layui-form-mid layui-word-aux">本地开发一般推荐设置为 0，线上环境建议设置为 10。</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">最大文件上传</label>
                <div class="layui-input-inline" style="width: 80px;">
                    <input type="text" name="upload_file_size" lay-verify="number" value="" class="layui-input">
                </div>
                <div class="layui-input-inline layui-input-company">KB</div>
                <div class="layui-form-mid layui-word-aux">提示：1 M = 1024 KB</div>
            </div>

            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label required">首页标题</label>
                <div class="layui-input-block">
                  <input type="text" name="index_site_name" value="" placeholder="请输入首页标题" class="layui-input">
                </div>
            </div>
            
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">META关键词</label>
                <div class="layui-input-block">
                    <textarea name="site_keywords" class="layui-textarea" placeholder="多个关键词用英文状态 , 号分割"></textarea>
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">META描述</label>
                <div class="layui-input-block">
                    <textarea name="site_description" class="layui-textarea"></textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label required">网站备案号</label>
                <div class="layui-input-block">
                    <input type="text" name="icp_code" value="" placeholder="请输入网站备案号" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label required">版权信息</label>
                <div class="layui-input-block">
                    <textarea name="site_copyright" class="layui-textarea"></textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn layui-btn-normal" lay-submit lay-filter="setting">确认保存</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="js/admin/lib/layui-v2.6.3/layui.js" charset="utf-8"></script>
<script src="js/admin/lay-config.js?v=1.0.4" charset="utf-8"></script>
<script>
    var item_info = <?=$item_info_json?>;
    layui.use(['form', 'common'], function () {
        var form = layui.form,
            layer = layui.layer,
            common = layui.common;


        //表单赋值
        form.val("saveForm", {
            "site_name": item_info.site_name,
            "index_site_name": item_info.index_site_name,
            "client_index": item_info.client_index,
            "site_copyright": item_info.site_copyright,
            "site_keywords": item_info.site_keywords,
            "site_description": item_info.site_description,
            "icp_code": item_info.icp_code,
            "upload_file_size": item_info.upload_file_size,
            "cache_time": item_info.cache_time,
        });

        //监听提交
        form.on('submit(setting)', function (data) {
            var url= base_url+'admincp.php/system/save';
            common.asyncDoRequest(url, data.field);
            return false;
        });

    });
</script>