@extends('layouts.app')

@section('content')
    <?php
    $user_temp = json_decode($user);
    $user_id = $user_temp->id;
    $user_role = strtolower($user_temp->title);
    $user_name = $user_temp->name; ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card guidances {{$user_role}}" >
                    <div class="card-header">
                        <div class="dropdown-wrapper">
                            <input type="file" id="file" name="uploads[]" multiple onchange="uploadFiles(this)" hidden>
                            <div class="dropdown">
                                <label class="dropdown-toggle" data-toggle="dropdown"><i
                                            class="fa fa-upload"></i></label>
                                <ul class="dropdown-menu">
                                    <li><a href="javascript:void(0)"
                                           id='upload_from_computer'>{{__('Upload from computer')}}</a></li>
                                    <li><a href="javascript:void(0);"
                                           onclick="uploadFromSignsForms()">{{__('Upload from Signs&forms')}}</a></li>
                                </ul>
                            </div>
                        </div>
                        <h3 class="text-center text-primary">{{__('Food risk report(guidance)')}}</h3>
                        <h5 class="text-center">#{{$nameCode}}</h5>

                    <!-- <div class="btn-group">
                        <a href="javascript:void(0);" class="btn-action" id="duplicate_guidance" title="duplicate guidance"><img src="{{url('/')}}/img/frontend/action/copy.png" width=25 height=25></a>
                        <a href="javascript:void(0);" class="btn-action" id="download_pdf" title="download chart"><img src="{{url('/')}}/img/frontend/action/download.png" width=25 height=25></a>
                        <a href="javascript:void(0);" class="btn-action" id="share_pdf" title="share pdf"><img src="{{url('/')}}/img/frontend/action/share.png" width=25 height=25></a>
                        <a href="javascript:void(0);" class="btn-action" id="print_pdf" title="print pdf"><img src="{{url('/')}}/img/frontend/action/print.png" width=25 height=25></a>
                    </div> -->
                    </div>
                    <div class="card-body">
                        <form id="guidance-form" style="width:100%">
                            {{method_field('PUT')}}

                            <input type="hidden" name="data[site]"
                                   value="{{ isset($guidance['site']) ? $guidance['site'] : '' }}"/>
                            <input type="hidden" name="data[subsite]"
                                   value="{{ isset($guidance['subsite']) ? $guidance['subsite'] : '' }}"/>
                            <input type="hidden" name="data[date]"
                                   value="{{ isset($guidance['date']) ? $guidance['date'] : '' }}"/>
                            <input type="hidden" name="data[time_]"
                                   value="{{ isset($guidance['time_']) ? $guidance['time_'] : '' }}"/>
                            <input type="hidden" name="data[employee_id]"
                                   @if ($user_role == 'admin' || $user_role == 'employee') value="{{$user_id}}"
                                   @else value="{{isset($guidance['employee_id']) ? $guidance['employee_id'] : ''}}" @endif/>
                            <input type="hidden" name="data[employee_name]"
                                   @if (($user_role == 'admin' || $user_role == 'employee') && !isset($guidance['employee_name'])) value="{{$user_name}}"
                                   @else value="{{isset($guidance['employee_name']) ? $guidance['employee_name'] : ''}}" @endif/>
                            <input type="hidden" name="data[admin_name]"
                                   value="{{isset($guidance['status']) && isset($guidance['status']['stage']) && $guidance['status']['stage'] == 'publish' ? $guidance['admin_name'] : ''}}"/>
                            <input type="hidden" name="data[status][stage]"
                                   @if (($user_role == 'admin' || $user_role == 'employee') && (!isset($guidance['status']) || !isset($guidance['status']['stage']) || $guidance['status']['stage'] == 'draft')) value="draft"
                                   @else value="{{isset($guidance['status']) && isset($guidance['status']['stage']) ? $guidance['status']['stage'] : ''}}" @endif/>
                            <input type="hidden" name="data[status][admin_changed_date]" value="unchanged"/>

                            <div class="row guidance-total-summary">
                                <div class="col-md-7">
                                    <?php $site_id = ""; ?>
                                    <select name="site_" class="ml-2" id="site">
                                        <option {{ (isset($guidance['site']) && $guidance['site']) ? '' : 'selected' }} value="site">{{__('Site')}}</option>
                                        @foreach($sites as $site)
                                            <option value="{{$site->id}}"
                                                    @if(isset($guidance['site']) && $guidance['site'] == $site->title)
                                                    selected
                                            <?php $site_id = $site->id; ?>
                                                    @endif
                                            >{{$site->title}}</option>
                                        @endforeach
                                    </select>
                                    <select {{ (isset($guidance['site']) && $guidance['site']) ? '' : 'disabled' }} name="subsite_"
                                            id="subsite">
                                        <option {{ (isset($guidance['site']) && $guidance['site']) ? '' : 'selected' }} value="subsite">{{__('Sub-site')}}</option>
                                        @foreach($subsites as $subsite)
                                            @if((isset($guidance['site']) && $guidance['site'] && $subsite->site_id == $site_id))
                                                <option value="{{$subsite->id}}" {{ (isset($guidance['subsite']) && $guidance['subsite'] == $subsite->title) ? 'selected' : '' }}>{{$subsite->title}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <div class="col-md-12 mt-4">
                                        <ul style="{{App::getLocale() == 'he' ? 'padding-right: 10px !important' : 'padding-left: 10px !important'}}">
                                            <li>
                                                <label for="resp">{{__('Company representative')}}: </label>
                                                <input type="text" id="resp" class="edit-field" readonly placeholder=""
                                                       size="15" name="data[company_representative]"
                                                       value="{{ isset($guidance['company_representative']) ? $guidance['company_representative'] : '' }}">
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <ul class="employe">
                                        <li>{{__('Employee name')}}:
                                            <span>@if (($user_role == 'admin' || $user_role == 'employee') && !isset($guidance['employee_name'])) {{ $user_name }} @else {{isset($guidance['employee_name']) ? $guidance['employee_name'] : ""}} @endif</span>
                                        </li>
                                        <li>{{__('Admin name')}}:
                                            <span>{{isset($guidance['admin_name']) ? $guidance['admin_name'] : ""}}</span>
                                        </li>
                                        <li>{{__('Date')}}: <input id="datepicker" class="edit-field"
                                                                   data-date-format="mm/dd/yyyy"
                                                                   value="{{isset($guidance['date']) ? $guidance['date'] : ""}}">
                                        </li>
                                        <li>{{__('Time')}}: <input type="text" id="time" class="edit-field" size="4"
                                                                   maxlength="5"
                                                                   pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]"
                                                                   onchange="updateTime(this);"
                                                                   value="{{isset($guidance['time_']) ? $guidance['time_'] : ""}}">
                                        </li>
                                        <li>{{__('Contract representative')}}: <input type="text" size="15"
                                                                                      class="contractor_representative edit-field"
                                                                                      name="data[contractor_representative]"
                                                                                      placeholder=""
                                                                                      value="{{ isset($guidance['contractor_representative']) ? $guidance['contractor_representative'] : '' }}"/>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-12 guidance-general">
                                <div><h4 class="guidance-heading">1. {{__('General')}}</h4></div>
                                <textarea name="data[guidance-general]" id="guidance-general-textarea"
                                          class="summernote-textarea" style="display: none">
                                @if (isset($guidance['guidance-general']))
                                        {{ $guidance['guidance-general'] }}
                                    @else
                                        <p>1. {{__('On dd/mm/yy there was a guidance day')}}</p>
                                        <p>2. {{__('The guidance done together with:')}} </p>
                                        <p>3. {{__('The length of demo day was:')}} </p>
                                    @endif
                            </textarea>
                            </div>
                            <div class="col-md-12 guidance-themes">
                                <div><h4 class="guidance-heading">2. {{__('Guidance themes')}}</h4></div>
                                <textarea name="data[guidance-themes]" id="guidance-themes-textarea"
                                          class="summernote-textarea" style="display: none">
                                @if (isset($guidance['guidance-themes']))
                                        {{ $guidance['guidance-themes'] }}
                                    @else
                                        <p><b>2.1 {{__('Dividing the kitchen for working zones:')}}</b></p>
                                        <p><b>2.2 {{__('Main work process in the kitchen:')}}</b></p>
                                        <p><b>2.3 {{__('Product expiration labeling:')}}</b></p>
                                        <p><b>2.4 {{__('Commence of Inspections and documentation')}}</b></p>
                                    @endif
                            </textarea>
                            </div>
                            <div class="col-md-12 guidance-principal">
                                <div><h4 class="guidance-heading">3. {{__('Principal malfunctions')}}</h4></div>
                                <div id="guidance-principal-items">
                                    @if (isset($guidance['guidance_principal_detail']))
                                        @foreach ($guidance['guidance_principal_detail'] as $key => $principal)
                                            @if (isset($principal["id"]) && isset($principal["value"]))
                                                <div class="guidance-principal-item" data-id="{{$principal["id"]}}">
                                                    <div class="guidance-sort"></div>
                                                    <textarea
                                                            name="data[guidance_principal_detail][{{$principal["id"]}}][value]"
                                                            class="summernote-textarea" style="display: none">
                                                {{ $principal["value"] }}
                                            </textarea>

                                                    <input type="hidden"
                                                           name="data[guidance_principal_detail][{{$principal["id"]}}][id]"
                                                           value="{{$principal["id"]}}"/>

                                                    <div class="guidance-photos">
                                                        @if (isset($guidance['photo'][$principal["id"]]))
                                                            <input type="hidden"
                                                                   name="data[photo][{{$principal["id"]}}][]" value=""/>
                                                            @foreach ($guidance['photo'][$principal["id"]] as $photo)
                                                                @php
                                                                    $name = explode('.', $photo);
                                                                    $extention = $name[count($name) - 1];
                                                                    $extention = strtolower($extention);
                                                                    $is_image = in_array($extention, ["jpg", "jpeg", "bmp", "gif", "png"]);
                                                                    $path = url('/').($is_image ? '/images/' : '/uploads/');
                                                                @endphp

                                                                <div class="guidance-principal-photo-item">
                                                                    <a href="{{ $path.$photo }}" target="_blank">
                                                                        @if ($is_image)
                                                                            <img src="{{$path.$photo}} "
                                                                                 class="guidance-principal-photo-item"
                                                                                 alt="{{ $photo }}"/>
                                                                        @else
                                                                            {{ strlen($photo) > 20 ? mb_substr($photo, 0, 20, 'utf-8') : $photo }}
                                                                        @endif
                                                                    </a>
                                                                    <i class="guidance-principal-photo-remove"></i>
                                                                    <input type="hidden"
                                                                           name="data[photo][{{$principal["id"]}}][]"
                                                                           value="{{$photo}}"/>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                        <div class='upload-photo-wrapper'>
                                                            <label class="guidance-add-photo "
                                                                   for="add-image-{{ $principal["id"] }}">
                                                                <i class="fa fa-upload" ></i>
                                                                <input id="add-image-{{ $principal["id"] }}"
                                                                       class="guidance-upload-image" type="file">
                                                            </label>
                                                        </div>
                                                        <div class='cb'></div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                                <a href='javascript:void(0);' id='add-principal-guidance'
                                   class='btn-blue'>{{__('Add more')}}</a>
                            </div>

                            <div class="col-md-12 guidance-summary">
                                <div><h4 class="guidance-heading">4. {{__('Summary & Recommendations')}}</h4></div>
                                <textarea name="data[guidance-summary]" id="guidance-summary-textarea"
                                          class="summernote-textarea" style="display: none">
                                @isset($guidance['guidance-summary'])
                                        {{ $guidance['guidance-summary'] }}
                                    @endisset
                            </textarea>
                            </div>

                            <div class="col-md-12 guidance-uploaded-documents">
                                <div><h4 class="guidance-heading">5. {{__('Uploaded documents')}}</h4></div>
                                <div id="guidance-uploaded-documents">
                                    @isset($guidance['guidance-uploads'])
                                        <input type="hidden" name="data[guidance-uploads][]" value=""/>
                                        @foreach($guidance['guidance-uploads'] as $key => $upload)
                                            <div class="guidance-uploads-item">
                                                <a href="{{ url('/') }}/uploads/{{$upload}}" target="_blank"
                                                   class="guidance-uploads-text">
                                                    @if(isImage($upload))
                                                        <img class="guidance-principal-photo-item"
                                                             src="{{ url('/') }}/uploads/{{$upload}}"
                                                             alt="{{$upload}}"/>
                                                    @else
                                                        {{ strlen($upload) > 20 ? mb_substr($upload, 0, 20, 'utf-8') : $upload }}
                                                    @endif
                                                </a>
                                                <i class="guidance-uploads-item-remove"></i>
                                                <input type="hidden" name="data[guidance-uploads][]"
                                                       value="{{$upload}}"/>
                                            </div>
                                        @endforeach
                                    @endisset
                                </div>
                                <div class='cb'></div>
                            </div>
                            <div style='display:flex; justify-content:center; align-items:center; margin-top:20px; flex-direction:column;'>
                                <p id="guidance-error-msg" style="margin-bottom: 0px; color: red;"></p>
                                <a href="javascript:void(0);" id="send_to_admin"
                                   class="btn-blue">{{__('Send to admin')}}</a>
                            </div>
                        </form>
                    </div>
                </div>
                <div id='printing-holder'></div>
            </div>
        </div>

        <div class='subview-container'>
            @include('frontend._part._signs_forms')
            @include('frontend._part._modals')
        </div>

    </div>

@endsection

@section('scripts')
    <link rel="stylesheet" href="{{asset('css/datepicker.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('/') }}/css/summernote-list-styles.css">
    <link rel="stylesheet" href="{{url('/')}}/js/frontend/uploadiFive/uploadifive.css">
    <link rel="stylesheet" href="{{ url('/') }}/css/multiselect.css">
    <link rel="stylesheet" href="{{ url('/') }}/css/malfunctions/main.css">
    <link rel="stylesheet" href="{{ url('/') }}/css/malfunctions/edit.css">

    @if (app()->getLocale() == 'he')
        <link rel="stylesheet" href="{{ url('/') }}/css/malfunctions/main_rtl.css" media="all">
    @endif

    <script src="{{url('/')}}/js/multiselect.js"></script>
    <script src="{{asset('js/bootstrap-datepicker.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.22.2/moment.js"></script>
    <script src="{{asset('js/datetime.js')}}"></script>
    <script src="{{asset('js/jquery.form.js')}}"></script>
    <script src="{{url('/')}}/js/canvasjs.min.js"></script>
    <script src="{{url('/')}}/js/pdf/jspdf.min.js"></script>
    <script src="{{url('/')}}/js/pdf/html2canvas.min.js"></script>
    <script src="{{url('/')}}/js/pdf/html2pdf.js"></script>
    <script src="{{asset('js/htmldiff.js')}}"></script>

    @if (app()->getLocale() == 'he')
        <script src="{{url('/')}}/js/i18n/bootstrap-datepicker.he.js" charset="UTF-8"></script>
    @endif

    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.js"></script>
    <script src="{{url('/')}}/js/summernote-list-styles.js"></script>
    <script src="{{asset('js/malfunctions/edit-guidance.js')}}"></script>

    <script type="text/javascript">
        var user = <?php echo $user ?>,
            FILTER_SITE_URL = '{{ action('MalfunctionController@filterSite') }}',
            FILTER_SUBSITE_URL = '{{ action('MalfunctionController@filterSubsite') }}',
            UPDATE_URL = '{{ action('MalfunctionController@updateGuidance', $guidance_id) }}',
            FORM_LIST_URL = '{{ action('MalfunctionController@index') }}',
            UPLOAD_FILE_URL = '{{ url('/') }}/upload-file',
            UPLOAD_FILES_URL = '{{ url('/') }}/upload-files',
            SEND_PDF_URL = "<?php echo e(route('malfunctionSharePdf')); ?>",
            DUPLICATE_GUIDANCE_URL = "<?php echo e(route('duplicateGuidance')); ?>",
            TOKEN = "{{ csrf_token() }}",
            BASE_URL = "{{ url('/') }}",
            LOCALE = "{{app()->getLocale()}}",
            SITE = "<?php echo $guidance['site'] ?>",
            SUBSITE = "<?php echo $guidance['subsite'] ?>"
        GUIDANCE_ID = "{{$guidance_id}}";

        var SF_TOKEN = "{{ csrf_token() }}",
            SF_TIMESTAMP = "<? echo time();?>",
            SF_CURRENT_URL = "{{ $_SERVER['REQUEST_URI'] }}",
            SF_UPLOAD_ITEM_URL = "{{url("/")}}/nik/upload",
            SF_RESORTING_URL = "{{url('/')}}/nik/newsort",
            SF_UPDATE_CONTENT_URL = "{{url('/')}}/nik/newArea",
            SF_DELET_ITEM_URL = "{{url('/')}}/nik/delete",
            SF_SEND_EMAIL_URL = "{{url('/')}}/nik/sendmail",
            SF_UPLOAD_FILES_URL = '{{ url('/') }}/upload-sf-files';

        var sortTextArray = {
            "date_desc": "Upload date DESC",
            "date_asc": "Upload date ASC",
            "type_asc": "Type A-Z",
            "type_desc": "Type Z-A",
            "name_asc": "Name A-Z",
            "name_desc": "Name Z-A"
        };

        var Lang = {
            "Are you sure?(send to admin)": "{{__('Are you sure?(send to admin)')}}",
            "Yes": "{{__('Yes')}}",
            "No": "{{__('No')}}",
            "Message sent successfully": "{{__('Message sent successfully')}}",
            "Email was not sent": "{{__('Email was not sent')}}",
            "Sub-site": "{{__('Sub-site')}}",
            "SomeFieldsAreMissiing": "{{__('Some fields are missing')}}"
        }

        @if(Session::has('sort'))
        $(document).ready(function () {
            $('.item.sortBlock').find('span').text(sortTextArray["{{ Session::get('sort') }}"])
        });
        @endif

        @if(Session::has('success'))
        $(document).ready(function () {
            alert('{{ Session::get("success") }}');
        });
        @endif

    </script>

    <script src="{{url('/')}}/js/frontend/uploadiFive/jquery.uploadifive.min.js"></script>
    <script src="{{url('/')}}/js/frontend/signs-forms.js"></script>

@stop
