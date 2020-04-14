<div class="modal" tabindex="-1" role="dialog" id="{{ $modal_id }}">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{ $title }}</h4>
            </div>
            <form>
                <div class="modal-body">
                    {!! $form_body !!}
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
$(function () {
    $('#{{$modal_id}} .modal-body .form-group').addClass('clearfix');
    $('#{{$modal_id}} .modal-body .CodeMirror-gutter.CodeMirror-linenumbers').css('width','29px');
    $('#{{$modal_id}} .modal-body .CodeMirror-gutter-wrapper').css('left','-30px');
    $('#{{$modal_id}} .modal-body .CodeMirror-sizer').css('margin-left','30px');
})
</script>