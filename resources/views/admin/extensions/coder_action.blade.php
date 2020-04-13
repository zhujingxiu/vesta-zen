<div class="form-group">
    <label>{{ $label }}</label>
    <div>
        <textarea class="form-control" id="{{$id}}" name="{{$name}}" placeholder="{{ trans('admin::lang.input') }} {{$label}}" {!! $attributes !!} >{{ old($column, $value) }}</textarea>
    </div>
    @include('admin::actions.form.help-block')
</div>
