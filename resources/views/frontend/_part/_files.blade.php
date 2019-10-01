@if(count($files))
    @foreach($files as $file)
        <div class="item">
            <input type='checkbox' class='mentioned-item item-check' data-id="{{ $file->id }}"
                   data-type="{{ $file->extension }}" data-path="{{url('/')}}/uploads/frontend/{{$file->filename}}">
            <div class="image">
                <img src="{{url('/')}}/uploads/frontend/{{ $file->image }}" alt="{{ $file->name }}">
            </div>

            <div class="content">
                <div class="name">{{ $file->name }}</div>
                <div class="type">{{ !empty($file->type->name) ? $file->type->name :pathinfo($file->filename, PATHINFO_EXTENSION) }}</div>
                <div class="size">{{ $file->size }}</div>
            </div>

        </div>
    @endforeach
@endif
