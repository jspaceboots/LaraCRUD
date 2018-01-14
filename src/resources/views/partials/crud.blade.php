@php
    $stringTypes = [];
    $relationTypes = [];
    $datetimeTypes = [];
    $numberTypes = [];
    $textTypes = [];

    foreach ($meta['fields'] as $field) {
        if ($field->getType()->getName() === 'string' && $field->getName() !== 'id' && strpos($field->getName(), '_id') === false) {
            $stringTypes[] = $field;
        }

        if ($field->getType()->getName() === 'string' && $field->getName() !== 'id' && strpos($field->getName(), '_id') !== false) {
            $relationTypes[] = $field;
        }

        if (($field->getName() !== 'created_at' && $field->getName() !== 'updated_at') && ($field->getType()->getName() === 'datetime' || $field->getType()->getName() === 'date')) {
            $datetimeTypes[] = $field;
        }

        if ($field->getType()->getName() === 'integer' && $field->getName() !== 'id' && strpos($field->getName(), '_id') === false) {
            $numberTypes[] = $field;
        }

        if ($field->getType()->getName() === 'text') {
            $textTypes[] = $field;
        }
    }
@endphp

<form action="/{{request()->route()->uri()}}" method="POST">
    {{ csrf_field() }}
    <div style="display: flex; flex-wrap: wrap;">
        @foreach($stringTypes as $field)
        <div style="margin-right: 1em;">
            <label style="display:block;">{{ucwords(str_replace('_', ' ', $field->getName()))}}</label>
            <input type="text" class="form-control" name="{{$field->getName()}}" value="{{$meta['editing'] ? $data[$field->getName()] : ''}}" />
        </div>
        @endforeach
        @foreach($numberTypes as $field)
        <div style="margin-right: 1em;">
            <label style="display:block;">{{ucwords(str_replace('_', ' ', $field->getName()))}}</label>
            <input type="number" class="form-control" name="{{$field->getName()}}" value="{{$meta['editing'] ? $data[$field->getName()] : ''}}" />
        </div>
        @endforeach
        @foreach($textTypes as $field)
            <div style="margin-right: 1em;">
                <label style="display:block;">{{ucwords(str_replace('_', ' ', $field->getName()))}}</label>
                <textarea class="form-control" name="{{$field->getName()}}">{{$meta['editing'] ? $data[$field->getName()] : ''}}</textarea>
            </div>
        @endforeach
        @foreach($datetimeTypes as $field)
            <div style="margin-right: 1em;">
                <label style="display:block;">{{ucwords(str_replace('_', ' ', $field->getName()))}}</label>
                <input class="datepicker form-control" name="{{$field->getName()}}" value="{{$meta['editing'] ? $data[$field->getName()] : ''}}" type="text" />
            </div>
        @endforeach
    </div>

    <div style="display: flex; flex-wrap: wrap; margin-top: 1em;">
        @foreach($meta['relations'] as $field => $fieldMeta)
        @if (array_key_exists('type', $fieldMeta) && $fieldMeta['type'] === 'checkbox')
        <div style="margin-right: 1em;">
            <label style="display: block;">{{$field}}</label>
            <br/>
            <div style="display: flex; flex-wrap: wrap;">
                @foreach($fieldMeta['values'] as $name => $id)
                <div style="margin-right: 1em;">
                    <label>{{$name}}</label>
                    <input type="checkbox" class="form-control" name="{{$field}}[]" value="{{$id}}">
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if(array_key_exists('type', $fieldMeta) && ($fieldMeta['type'] === 'dropdown') || $fieldMeta['type'] === 'multidropdown')
        <div style="margin-right: 1em; margin-bottom: 1em; {{$fieldMeta['type'] === 'multidropdown' ? 'max-width: 33%; min-width: 180px;' : ''}}">
            <label style="display: block;">{{ucwords(str_replace('_', ' ', str_replace('_id', '', $field)))}}</label>

            <select class="select-2" name="{{$field}}{{$fieldMeta['type'] === 'multidropdown' ? '[]' : ''}}" style="{{$fieldMeta['type'] === 'multidropdown' ? 'width: 100%;' : ''}}" {{$fieldMeta['type'] === 'multidropdown' ? 'multiple' : ''}}>

                @foreach($fieldMeta['values'] as $name => $id)
            <?php
            $isSelected = false;
            if (isset($data) && count($data)) {
                $isSelected =  $id && is_array($data[$field]) ? in_array($id, $data[$field]) : false;
                if (strpos($field, '_id') !== false) {
                    $isSelected = $id === $data[$field];
                }
            }
            ?>
            <option value="{{$id}}" {{$isSelected ? 'selected' : ''}}>{{$name}}</option>
            @endforeach
            </select>
        </div>
        @endif

        @endforeach
    </div>

    <div style="text-align: right;border-top: 1px solid rgba(0,0,0,.15);text-align: right;margin-top: 2em;padding-top: 1em;">
        <a style="margin-right: 1em;" href="{{route(str_replace('edit_', '', str_replace('new_', '', $meta['route'])))}}">Cancel</a>
        <button type="submit" class="btn btn-primary">{{$meta['editing'] ? 'Save' : 'Create'}}</button>
    </div>
</form>