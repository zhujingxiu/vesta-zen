<div class="form-group">
    <label>{{ $label }}</label>
    <div>
        @foreach($options as $option => $label)
            <span class="icheck">
            <div class="radio-inline">
                <input type="radio" name="{{$name}}" value="{{$option}}" class="minimal {{$class}}" {{ ($option == old($column, $value)) || ($value === null && in_array($label, $checked)) ?'checked':'' }} {!! $attributes !!} />&nbsp;{{$label}}&nbsp;&nbsp;
            </div>
        </span>
        @endforeach
    </div>
    @include('admin::actions.form.help-block')
</div>
