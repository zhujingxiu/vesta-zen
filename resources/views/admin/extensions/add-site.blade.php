<div class="btn-group pull-right" style="margin-right: 10px" onclick="addSiteModal(this, '{!! $modal_name !!}')">
    <a href="" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#{!! $modal_name !!}">
        <i class="fa fa-save"></i>&nbsp;&nbsp;{!! $label !!}
    </a>
</div>
<div class="modal fade {!! $modal_name !!}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">关闭</span>
                </button>
                <h4 class="modal-title" id="">{!! $label !!}</h4>
            </div>
            <form action="{!! $form_action !!}" method="post" pjax-container>
                {{ csrf_field() }}
                <div class="modal-body">
                    <input type="hidden" name="server_id" class="server_id action">
                    <div class="form-group">
                        <label>服务器</label>
                        <input readonly="1" type="text" id="server_name" name="server_name"  class="form-control server_name action" placeholder="输入 服务器">
                    </div>
                    <input type="hidden" name="server_ip"  class="server_ip action">

                    <input type="hidden" name="server_user"  class="server_user action">

                    <input type="hidden" name="server_pwd"  class="server_pwd action">

                    <div class="form-group">
                        <label>选择IP</label>
                        <input readonly="1" type="text" id="server_ip" class="form-control server_ip action" placeholder="输入 选择IP">
                    </div>
                    <div class="form-group">
                        <label>DNS解析</label>
                        <div>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="parse_cf[]" value="1" class="parse_cf icheckbox" >
                                &nbsp;使用CloudFlare自动解析&nbsp;&nbsp;
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>域名</label>
                        <input type="text" id="domain" name="domain" class="form-control domain required" placeholder="输入 域名">
                        <span class="help-block">
                            <i class="fa fa-info-circle"></i>&nbsp;可以直接写二级域名
                        </span>
                    </div>
                    <div class="form-group">
                        <label>域名级别</label>
                        <div>
                            <label class="radio-inline">
                                <input type="radio" name="level" value="1" class="level icheckbox" checked="" >&nbsp;一级&nbsp;&nbsp;
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="level" value="2" class="level icheckbox">&nbsp;二级&nbsp;&nbsp;
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>选择站点语言</label>
                        <select class="form-control lang_id action" name="lang_id">
                            <option value="">请选择语言</option>
                            @foreach($data['languages'] as $option => $label)
                                <option value="{{$option}}">{{$label}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="display: none;">
                        <label>选择站点模板</label>
                        <select class="form-control tpl_id action" name="tpl_id" >
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="form-group" style="display: none;">
                        <label>站点预览</label>
                        <div id="preview">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary" id="add-site-submit">提交</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('.icheckbox').iCheck({
            checkboxClass:'icheckbox_minimal-blue',
            radioClass   : 'iradio_minimal-blue'
        });
        $("#domain").delay(300).bind('change,blur,input porpertychange',function(){
            var str = $(this).val()
            if(str.indexOf('.') != -1 ){
                $.getJSON('/admin/api/parse-domain',{_t:Math.random(),domain:str},function (json) {
                    if(json.code != 200){
                        $.admin.swal({type: 'error', title: json.msg});
                        return false;
                    }
                    var data = json.data;
                    if(data['level']!=1){
                        $('input[name="level"][value="2"]').iCheck('check');
                    }else{
                        $('input[name="level"][value="1"]').iCheck('check');
                    }
                })
            }
        });
        $('.lang_id').change(function () {
            var lang_id = $(this).val();
            $.getJSON('/admin/api/templates',{_t:Math.random(),q:lang_id},function (json) {
                if(json.code!=200){
                    $.admin.swal({type: 'error', title: json.msg});
                    return false;
                }
                var data = json.data;
                $('.tpl_id').find('option').remove();
                var options = '';
                for(i in data){
                    var tpl = data[i];
                    if(tpl['id']>0){
                        options += '<option value="'+tpl['id']+'" data-preview="'+tpl['preview']+'">'+tpl['text']+'</option>'
                    }
                }
                $('.tpl_id').html(options).parent().show();
                $('.tpl_id option:first').trigger('change');

            })
        });
        $('.tpl_id').change(function () {
            var preview = $(this).find('option[value="'+$(this).val()+'"]').data('preview');
            $('#preview').html('<img style="max-width:220px;max-height:180px;" class="thumbnail" src="'+preview+'">').parent().show();
        })
        $('#add-site-submit').click(function () {
            var isTrue = false;
            $('.required').each(function () {
                if ($(this).val() == '') {
                    toastr.error($(this).parent().siblings('label').text()+'必填');
                    isTrue = true;
                    return false;
                }
            });
            if (isTrue == true) {
                return;
            }
            if (!confirm('确定添加站点吗？')) {
                return ;
            }
            $.ajax({
                type: "POST",
                url:'{!! $form_action !!}',
                data:$(this).parents('form').serialize(),
                dataType:'json',
                beforeSend:function(){
                    console.log('start send');
                    //NProgress.start()
                },
                success: function(json) {
                    if (json.code == 200) {
                        $.admin.swal({title:'创建成功',text: json.msg,timer: 3000},function () {
                            if (json.data['redirect']) {
                                window.location.href = json.data['redirect'];
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        $.admin.swal("创建失败", json.msg, "error");
                    }
                },
                error: function(request) {
                    $.admin.swal("创建失败", "Connection error", "error");
                },
                complete:function () {
                    console.log('request finished');
                    //NProgress.done()
                }
            });
        })
    });
    function addSiteModal(obj, modal_name) {
        var selected = [];
        $('.grid-row-checkbox:checked').each(function(){
            selected.push($(this).data('id'));
        });
        if (selected.length <= 0) {
            $.admin.swal({type: 'error', title: '请先选择一行!'});
            $(obj).next('div').removeAttr('id', modal_name);
            return;
        } else if (selected.length == 1) {
            $(obj).next('div').attr('id', modal_name);
            var server_id = selected[0]
            $('.modal input[name="server_id"]').val(server_id);
            var server_name = $('#server-entity-'+server_id).data('name'),
                server_ip = $('#server-entity-'+server_id).data('ip'),
                server_user = $('#server-entity-'+server_id).data('user'),
                server_pwd = $('#server-entity-'+server_id).data('pwd');
            var server = server_user+':'+server_pwd+'@'+server_ip+'[#'+server_id+':'+server_name+']';
            $('.modal input[name="server_name"]').val(server);
            $('.modal input[name="server_user"]').val(server_user);
            $('.modal input[name="server_pwd"]').val(server_pwd);
            $('.modal input[name="server_ip"]').val(server_ip);
        } else {
            $.admin.swal({type: 'error', title: '该操作只允许选择一行!'});
            $(obj).next('div').removeAttr('id', modal_name);
            return;
        }
    }
</script>