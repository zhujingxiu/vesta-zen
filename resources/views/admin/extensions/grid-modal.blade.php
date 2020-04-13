<div class="btn-group pull-right" style="margin-right: 10px;margin-left: 5px;" id="grid-modal-{!! $modal_name !!}">
    <a href="" class="btn btn-sm btn-default" data-toggle="modal" data-target="#{!! $modal_name !!}">
        {!! $label !!}
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
            <div class="modal-body">
            {!! $form_body !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary grid-modal-submit">提交</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        var modal_name = '{!! $modal_name !!}';
        var selected = [];
        $('#grid-modal-'+modal_name).click(function () {

            $('.grid-row-checkbox:checked').each(function(){
                selected.push($(this).data('id'));
            });
            if (selected.length <= 0) {
                $.admin.swal({type: 'error', title: '请先选择一行!'});
                $(this).next('div').removeAttr('id', modal_name);
                return;
            } else if (selected.length == 1) {
                $(this).next('div').attr('id', modal_name);
                $('#'+modal_name).find('form input[name="selected"]').val(selected.join())
            } else {
                $.admin.swal({type: 'error', title: '该操作只允许选择一行!'});
                $(this).next('div').removeAttr('id', modal_name);
                return;
            }
        });
        {!! $script !!}
    });
</script>