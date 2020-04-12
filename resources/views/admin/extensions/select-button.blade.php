{{--<div class="btn">--}}
    {{--<a class="btn btn-sm btn-default  pull-right" href="{{$url}}"><i class="fa {{$icon}}"></i> {{$text}}</a>--}}
{{--</div>--}}


<!-- Split button -->
<div class="btn-group">
    <button type="button" class="btn btn-primary btn-sm">{{$text}}</button>
    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu">
        <li><a href="#" target="_blank">全部</a></li>
        <li><a href="#" target="_blank" id="hotcheckids">选择的行</a></li>
    </ul>
</div>
