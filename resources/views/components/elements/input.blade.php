<fieldset class="fieldset">
    <legend class="fieldset-legend text-base">{{ $label }}
        @if ($isRequired)
            <span class="star-required text-red-400 mr-5">*</span>
        @endif
    </legend>
    <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" class="input w-full {{$class}}" value="{{ $value }}"
           placeholder="{{ $placeholder }}" {{ $isRequired ? 'required' : '' }} min="{{$min}}" max="{{$max}}"/>
    @if($optional)
        <p class="label">{{$optional}}</p>
    @endif
</fieldset>
