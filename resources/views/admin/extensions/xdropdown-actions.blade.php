<div class="grid-dropdown-actions dropdown">
    <div class="btn-group">
        @foreach($default as $action)
            <button type="button" class="btn btn-default btn-xs">{!! $action->render() !!}</button>
        @endforeach
            @if(!empty($custom) && $custom)
        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle btn-sm" data-toggle="dropdown">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" style="min-width: 70px !important;box-shadow: 0 2px 3px 0 rgba(0,0,0,.2);border-radius:0;left: -65px;top: 5px;">

                    @foreach($custom as $action)
                        <li>{!! $action->render() !!}</li>
                    @endforeach
            </ul>
        </div>
            @endif
    </div>
</div>