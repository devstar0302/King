@extends('layouts.app')

@section('content')
<?php
    $user_temp = json_decode($user);
    $user_id = $user_temp->id;
    $user_role = strtolower($user_temp->title);
    $user_name = $user_temp->name; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card guidances {{$user_role}}" id="pdf-container">
                @if (session('status'))
                    <div class="card-body">
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    </div>
                @endif

                <div class="card-header">
                    @if ((!isset($guidance->data['status']) || !isset($guidance->data['status']['stage']) || $guidance->data['status']['stage'] != 'publish') && ($user_role == 'admin' || $user_role == 'employee'))
                        <div class="btn-group {{app()->getLocale() == 'he' ? 'right' : 'left'}}">
                            <a class='btn-action' onclick="deleteGuidance();" style="color:black;" href="javascript:void(0);">
                                <i class="fa fa-trash"></i>
                            </a>
                            <a class='btn-action' style="color:black;" href="{{ action('MalfunctionController@editGuidance', $guidance->id) }}">
                                <i class="fa fa-edit"></i>
                            </a>
                        </div>
                    @endif

                    <h3 class="text-center text-primary">
                        {{__('Food risk report(guidance)')}}
                    </h3>
                    <h5 class="text-center">#{{$guidance->nameCode}}</h5>

                    <div class="btn-group {{app()->getLocale() == 'he' ? 'left' : 'right'}}">
                        @if ($user_role == 'admin' || $user_role == 'employee')
                            <a href="javascript:void(0);" class="btn-action" id="duplicate_guidance" title="duplicate guidance"><img src="{{url('/')}}/img/frontend/action/copy.png" width=25 height=25></a>
                        @endif
                        <a href="javascript:void(0);" class="btn-action" id="download_pdf" title="download chart"><img src="{{url('/')}}/img/frontend/action/download.png" width=25 height=25></a>
                        <a href="javascript:void(0);" class="btn-action" id="share_pdf" title="share pdf"><img src="{{url('/')}}/img/frontend/action/share.png" width=25 height=25></a>
                        <a href="javascript:void(0);" class="btn-action" id="print_pdf" title="print pdf"><img src="{{url('/')}}/img/frontend/action/print.png" width=25 height=25></a>
                    </div>
                </div>
                <h5 style="padding-right: 60px;">{{__('To')}}</h5>
                <div class="card-body row total-summary">
                    <div class="col-md-7">
                        <ul class="company">
                            <li><span>{{ (isset($guidance->data['site']) ? $guidance->data['site'] : '') . (isset($guidance->data['subsite']) ? (" / " . $guidance->data['subsite']) : '') }}</span></li>
                            <li><span>{{__('Company representative')}}: {{ isset($guidance->data['company_representative']) ? $guidance->data['company_representative'] : '' }}</span></li>
                        </ul>
                    </div>
                    <div class="col-md-5">
                        <ul class="employe">
                            <li>{{__('Employee name')}}: <span>{{isset($guidance->data['employee_name']) ? $guidance->data['employee_name'] : ""}}</span></li>
                            <li>{{__('Admin name')}}: <span>{{isset($guidance->data['status']) && isset($guidance->data['status']['stage']) && $guidance->data['status']['stage'] == 'publish' ? $guidance->data['admin_name'] : ''}}</span></li>
                            <li>{{__('Date')}}: {{ isset($guidance->data['date']) ? $guidance->data['date'] : '--/--/--' }}</li>
                            <li>{{__('Time')}}: {{ isset($guidance->data['time_']) ? $guidance->data['time_'] : '--:--' }}</li>
                            <li>{{__('Contract representative')}}: {{ isset($guidance->data['contractor_representative']) ? $guidance->data['contractor_representative'] : '' }}</li>
                        </ul>
                    </div>
                </div>

                <div class="card-body details">
                    <div class="guidances-item">
                        <h5><b>1. {{__('General')}}</b></h5>
                        {!! isset( $guidance->data['guidance-general'] ) ?  $guidance->data['guidance-general'] : "" !!}
                    </div>

                    <div class="guidances-item">
                        <h5><b>2. {{__('Guidance themes')}}</b></h5>
                        {!! isset( $guidance->data['guidance-themes'] ) ?  $guidance->data['guidance-themes'] : "" !!}
                    </div>

                    <div class="guidances-item">
                        <h5><b>3. {{__('Principal malfunctions')}}</b></h5>
                        @if (isset($guidance->data['guidance_principal_detail']))
                            <ul class="guidances-principal">
                                @foreach($guidance->data['guidance_principal_detail'] as $principal)
                                    <li class="guidances-item" data-guidance-id="<?php echo $guidance_id ?>" data-principal-id="<?php echo $principal["id"] ?>">{!! isset($principal["value"]) ? $principal["value"] : "" !!}
                                        @if (isset($principal["id"]) && isset($guidance->data['photo'][$principal["id"]]))
                                            <div class="guidance-photos show">
                                                @foreach ($guidance->data['photo'][$principal["id"]] as $photo)
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
                                                                <img src="{{$path.$photo}} " class="guidance-principal-photo-item" alt="{{ $photo }}"/>
                                                            @else
                                                                {{ strlen($photo) > 20 ? mb_substr($photo, 0, 20, 'utf-8') : $photo }}
                                                            @endif
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="clear-both"></div>
                                        @endif
                                        <form class="comments" data-principal-id="<?php echo $principal["id"] ?>">
                                            {{method_field('POST')}}
                                            {{ csrf_field() }}
                                            @if (isset($guidance->data['comments']) && isset($guidance->data['comments'][$principal["id"]]))
                                                @foreach($guidance->data['comments'][$principal["id"]] as $key => $comment)
                                                    <div class="comment-item" data-comment-id="<?php echo $key ?>">
                                                        <input type="hidden" name="data[comments][{{$principal["id"]}}][{{$key}}][user_id]" value="{{$comment['user_id']}}">
                                                        <input type="hidden" name="data[comments][{{$principal["id"]}}][{{$key}}][user_role]" value="{{$comment['user_role']}}" readonly>
                                                        <input type="hidden" name="data[comments][{{$principal["id"]}}][{{$key}}][user_name]" value="{{$comment['user_name']}}" readonly>
                                                        <span class="user-name <?php echo strtolower($comment['user_role']) ?>">{{$comment['user_name']}}</span>
                                                        <input class="comment-value" name="data[comments][{{$principal["id"]}}][{{$key}}][value]" value="{{$comment['value']}}" readonly>

                                                    @if($user_id == $comment['user_id'])
                                                        <div class="dropdown">
                                                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">...
                                                            <span class="caret"></span></button>
                                                            <ul class="dropdown-menu">
                                                                <li><a href="javascript:void(0);" class="btn_edit" onclick="editComment('<?php echo $principal["id"] ?>', '<?php echo $key ?>')">{{__('Edit')}}</a></li>
                                                                <li><a href="javascript:void(0);" class="btn_delete" onclick="deleteComment('<?php echo $principal["id"] ?>', '<?php echo $key ?>')">{{__('Delete')}}</a></li>
                                                            </ul>
                                                        </div>
                                                    @endif
                                                    </div>
                                                @endforeach
                                            @endif
                                        </form>
                                        <input class="comment-editor" data-principal-id="<?php echo $principal["id"] ?>"></input>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <div class="guidances-item">
                        <h5><b>4. {{__('Summary & Recommendations')}}</b></h5>
                        {!! $guidance->data['guidance-summary'] !!}
                    </div>

                    <div class="guidances-item">
                        <h5><b>5. {{__('Uploaded documents')}}</b></h5>
                        @isset($guidance->data['guidance-uploads'])
                            @foreach($guidance->data['guidance-uploads'] as $upload)
                                <div class="guidance-uploads-item">
                                    <a href="{{ url('/') }}/uploads/{{$upload}}" target="_blank" class="guidance-uploads-text">
                                        @if(isImage($upload))
                                            <img class="guidance-principal-photo-item" src="{{ url('/') }}/uploads/{{$upload}}" alt="{{$upload}}"/>
                                        @else
                                            {{ strlen($upload) > 20 ? mb_substr($upload, 0, 20, 'utf-8') : $upload }}
                                        @endif
                                    </a>
                                </div>
                            @endforeach
                            <div class="clear-both"></div>
                        @endisset
                    </div>
                    <div class="guidances-item">
                        <h5>{{__('Sincerely')}},</h5>
                        <h5>{{isset($guidance->data['employee_name']) ? $guidance->data['employee_name'] : ""}}</h5>
                    </div>
                </div>
                <div id='printing-holder'></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <link rel="stylesheet" href="{{ url('/') }}/css/malfunctions/main.css" media="all">
    <link rel="stylesheet" href="{{ url('/') }}/css/malfunctions/show.css" media="all">
    <link rel="stylesheet" href="{{ url('/') }}/css/malfunctions/print.css" media="print">

    @if (app()->getLocale() == 'he')
        <link rel="stylesheet" href="{{ url('/') }}/css/malfunctions/main_rtl.css" media="all">
    @endif

    <script src="{{url('/')}}/js/canvasjs.min.js"></script>
    <script src="{{url('/')}}/js/pdf/jspdf.min.js"></script>
    <script src="{{url('/')}}/js/pdf/html2canvas.min.js"></script>
    <script src="{{url('/')}}/js/pdf/html2pdf.js"></script>
    <script src="{{asset('js/malfunctions/show-guidance.js')}}"></script>

    <script type="text/javascript">
        var GET_STATISTICS_URL = "<?php echo e(route('get-statistics')); ?>",
            SEND_PDF_URL = "<?php echo e(route('malfunctionSharePdf')); ?>",
            DUPLICATE_GUIDANCE_URL = "<?php echo e(route('duplicateGuidance')); ?>",
            SAVE_COMMENTS_URL = "<?php echo e(route('guidanceSaveComments', $guidance_id)); ?>",
            DELETE_GUIDANCE_URL = "{{ action('MalfunctionController@destroyGuidance', $guidance->id) }}",
            TOKEN = "{{ csrf_token() }}",
            BASE_URL = "{{ url('/') }}";
            REPORT_DATE = "{{ isset($guidance->data['date']) ? $guidance->data['date'] : '' }}",
            GUIDANCE_ID = <?php echo $guidance_id ?>,
            SITE = "<?php echo $guidance->data['site'] ?>",
            SUBSITE =  "<?php echo $guidance->data['subsite'] ?>",
            user = <?php echo $user ?>;

        var Lang = {
            "Edit" : "{{__('Edit')}}",
            "Delete" : "{{__('Delete')}}",
            "Are you sure you want to delete it?" : "{{__('Are you sure you want to delete it?')}}",
            "Yes" : "{{__('Yes')}}",
            "Cancel(no)" : "{{__('Cancel(no)')}}",
            "Message sent successfully" : "{{__('Message sent successfully')}}",
            "Email was not sent" : "{{__('Email was not sent')}}",
        }
    </script>
@stop
