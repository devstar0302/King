<div class="container signs_forms">
    <div class="card">
        <div class="card-header">


            <div class="subview-container cars-close add-to-form ">
                <a href="javascript:void(0);"
                   onclick="uploadFromSignsForms()" class="close"></a>
            </div>
            <div class='btn-group {{app()->getLocale() == "he" ? "right" : "left"}}'>
                @if ($user_role == 'admin')
                    <div class="item uploadBlock btn-action" title="Upload files">
                        <i class="fa fa-upload"></i>
                        <form>
                            <div id="queue"></div>
                            <input id="file_upload" name="file_upload" type="file" multiple hidden>
                        </form>
                    </div>
                @endif
            </div>


            <div class="icons">
                @if(count($files))
                    <div class="icon">
                        <div class="option-block">
                            <a href="javascript:void(0);" title="Share " class="share disabled-action" onclick="sf_share(this)"></a>
                        </div>
                        <div class="option-block">
                            <a href="javascript:void(0);" class="delete disabled-action"
                               style="display: @if (!strcmp($user_role, 'admin')) inline-block @else none @endif"
                               onclick="sf_delete(target)"></a>
                        </div>
                        <div class="option-block">
                            <a href="javascript:void(0);" title="Download" class="download disabled-action" download onclick="sf_download(target)"></a>
                        </div>
                        <div class="option-block">
                            <a href="javascript:void(0);" title="Print" class="print disabled-action" onclick="sf_print(this)"></a>
                        </div>
                    </div>
                @endif
            </div>

            <h3 class="text-center text-primary">{{__('Signs&Forms')}}</h3>
            <div class="btn-group {{app()->getLocale() == 'he' ? 'left' : 'right'}}">
                <div data-view="1" title="List view" class="item viewBlock btn-action">
                <!-- <span>{{__('view')}}</span> -->
                </div>
                <div class="item sortBlock @if(Session::has('sort') && strpos(Session::get('sort'), 'asc')) asc @endif  btn-action">
                <!-- <span>{{__('Sort')}}</span> -->
                </div>
            </div>

        </div>

        <div class="card-body">
            <div style="display:flex; justify-content:space-between; margin-top: 1px; margin-bottom:20px;">
                <a href="javascript:void(0);" class="btn-blue add-to-form" style="margin: 0;"
                   onclick="uploadCheckedFiles()">{{__('Add')}}</a>
                <input type="text" name="search" id="search" placeholder="{{__('Search')}}" style="height: 33px;">
            </div>

            <div class="fileContent">
                @if(count($files))
                    @foreach($files as $file)
                        <div class="item">
                            <input type='checkbox' class='mentioned-item item-check' data-id="{{ $file->id }}" data-type="{{ $file->extension }}" data-path="{{url('/')}}/uploads/frontend/{{$file->filename}}">
                            <div class="image">
                                <img src="{{url('/')}}/uploads/frontend/{{ $file->image }}" alt="{{ $file->name }}">
                            </div>

                            <div class="content">
                                <div class="name">{{ $file->name }}</div>
                                <div class="type">{{ !empty($file->type->name) ? $file->type->name :pathinfo($file->filename, PATHINFO_EXTENSION) }}</div>
                                <div class="size">{{ $file->size }}</div>
                            </div>

{{--                            <div class="optionBlock">--}}
{{--                                <a href="javascript:void(0);" data-id="{{ $file->id }}" data-type="{{ $file->extension }}" data-path="{{url('/')}}/uploads/frontend/{{$file->filename}}" title="Print" class="print" onclick="sf_print(this)"></a>--}}
{{--                                <a href="{{url('/')}}/uploads/frontend/{{ $file->filename }}" title="Download" class="download" download></a>--}}
{{--                                <a href="javascript:void(0);" title="Delete" data-id="{{ $file->id }}" class="delete" style="display: @if (!strcmp($user_role, 'admin')) inline-block @else none @endif" onclick="sf_delete(this)"></a>--}}
{{--                                <a href="javascript:void(0);" data-id="{{ $file->id }}" title="Share" class="share" onclick="sf_share(this)"></a>--}}
{{--                            </div>--}}
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
<script src="{{asset('js/malfunctions/edit-guidance.js')}}"></script>
<input type="hidden" name="fileId" id="fileShareId">
