@extends('layouts.app')

@section('content')
    <?php
    $user_temp = json_decode($user);
    $user_id = $user_temp->id;
    $user_role = strtolower($user_temp->title);
    $user_name = $user_temp->name; ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card malfunctions {{$user_role}}" id="pdf-container">
                    @if (session('status'))
                        <div class="card-body">
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        </div>
                    @endif
                    <div class="card-header">
                        @if ((!isset($malfunction->data['status']) || !isset($malfunction->data['status']['stage']) || $malfunction->data['status']['stage'] != 'publish') && ($user_role == 'admin' || $user_role == 'employee'))
                            <div class="btn-group {{app()->getLocale() == 'he' ? 'right' : 'left'}}">
                                <a class='btn-action hide-in-pdf' onclick="deleteMalfunction();" style="color:black;" href="javascript:void(0);">
                                    <i class="fa fa-trash"></i>
                                </a>
                                <a class='btn-action hide-in-pdf' style="color:black;" href="{{ action('MalfunctionController@edit', $malfunction->id) }}">
                                    <i class="fa fa-edit"></i>
                                </a>
                            </div>
                        @endif

                        <h3 class="text-center text-primary" id="print_title">
                            {{__('Food risk report(malfunction)')}}
                        </h3>
                        <h5 class="text-center">#{{$malfunction->nameCode}}</h5>

                        <div class="btn-group {{app()->getLocale() == 'he' ? 'left' : 'right'}}">
                            @if ($user_role == 'admin' || $user_role == 'employee')
                                <a href="javascript:void(0);" class="btn-action hide-in-pdf" id="duplicate_malfunction" title="duplicate malfunction"><img src="{{url('/')}}/img/frontend/action/copy.png" width=25 height=25></a>
                            @endif
                            <a href="javascript:void(0);" class="btn-action hide-in-pdf" id="download_pdf" title="download chart"><img src="{{url('/')}}/img/frontend/action/download.png" width=25 height=25></a>
                            <a href="javascript:void(0);" class="btn-action hide-in-pdf" id="share_pdf" title="share pdf"><img src="{{url('/')}}/img/frontend/action/share.png" width=25 height=25></a>
                            <a href="javascript:void(0);" class="btn-action hide-in-pdf" id="print_pdf" title="print pdf"><img src="{{url('/')}}/img/frontend/action/print.png" width=25 height=25></a>
                            <div class="show-in-pdf" style="display: none;">
                                <img src="{{url('/')}}/img/logo.png" alt="">
                            </div>
                        </div>
                    </div>
                    <h5 style="padding-right: 43px; padding-left: 43px; margin-bottom: -21px; font-size: 0.9rem;">{{__('To')}}</h5>
                    <div class="card-body row total-summary">
                        <div class="col-md-4">
                            <ul class="company">
                                <li>
                                <span>
                                    {{ isset($malfunction->data['company_representative']) ?
                                        $malfunction->data['company_representative'] : '' }}
                                </span>
                                </li>
                                <li id="print_sub">
                                <span>{{ (isset($malfunction->data['site']) ? $malfunction->data['site'] : '') . (isset($malfunction->data['subsite']) ? (" / " . $malfunction->data['subsite']) : '') }}
                                </span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <ul class="employe">
                                <li>{{__('Employee name')}}: <span>{{isset($malfunction->data['employee_name']) ? $malfunction->data['employee_name'] : ""}}</span></li>
                                <li>{{__('Admin name')}}: <span>{{isset($malfunction->data['status']) && isset($malfunction->data['status']['stage']) && $malfunction->data['status']['stage'] == 'publish' ? $malfunction->data['admin_name'] : ''}}</span></li>
                                <li id="print_date">{{__('Date')}}: {{ isset($malfunction->data['date']) ? $malfunction->data['date'] : '--/--/----' }}</li>
                                <li>{{__('Time')}}: {{ isset($malfunction->data['time_']) ? $malfunction->data['time_'] : '--:--' }}</li>
                                <li>{{__('Contract representative')}}: {{ isset($malfunction->data['contractor_representative']) ? $malfunction->data['contractor_representative'] : '' }}</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <ul class="totals">
                                <li>{{__('Total score')}}: {{ isset($malfunction->data['calculate']['total']) && strpos($malfunction->data['calculate']['total'], '%') !== false ? $malfunction->data['calculate']['total'] : "---" }} </li>
                                <li>{{__('Risk level')}}:
                                    @if (isset($malfunction->data['risk_level']) && in_array($malfunction->data['risk_level'], [0,1,2]))
                                        @if ($malfunction->data['risk_level'] == 'Low') {{__('Low')}}
                                        @elseif ($malfunction->data['risk_level'] == 'Medium') {{__('Medium')}}
                                        @elseif ($malfunction->data['risk_level'] == 'High') {{__('High')}}
                                        @endif
                                    @else
                                        {{ '---' }}
                                    @endif
                                </li>
                                <li>{{__('Gastronomy score')}}: <span id="gastronome">{{ isset($malfunction->data['gastronomy_score']) && strpos($malfunction->data['gastronomy_score'], '%') !== false ? $malfunction->data['gastronomy_score'] : '---' }}</span></li>
                                <li>{{__('Service level')}}:
                                    @if (isset($malfunction->data['service_level']) && in_array($malfunction->data['service_level'], [0,1,2,3]))
                                        @if ($malfunction->data['service_level'] == 'Very good') {{__('Very good')}}
                                        @elseif ($malfunction->data['service_level'] == 'Good') {{__('Good')}}
                                        @elseif ($malfunction->data['service_level'] == 'Bad') {{__('Bad')}}
                                        @elseif ($malfunction->data['service_level'] == 'N/A') {{__('N/A')}}
                                        @endif
                                    @else
                                        {{ '---' }}
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-body details">
                        <div class="malfunctions-item scoring">
                            <h5><b>1. {{__('Scoring')}}</b></h5>
                            <div id="chartContainer" style="height:370px; width:100%; margin: 20px auto; direction:ltr;"></div>
                            <table class="table table-hover" border="1">
                                <thead style="background-color:#0074D9;color:white;">
                                <tr>
                                    <td width="33.3%">{{__('Category')}}</td>
                                    <td width="33.3%">{{__('Weight')}}</td>
                                    <td width="33.4%">{{__('CP Score')}}</td>
                                </tr>
                                </thead>
                                <tbody style="background-color: #AAAAAA;">
                                @if (isset($malfunction->data['categories']))
                                    @foreach($malfunction->data['categories'] as $category_key => $category)
                                        <tr class="category">
                                            <td><b>{{ $category_key+1 }}. {{ $category['name'] }}</b></td>
                                            <td><b>{{ $category['score']}}%</b></td>
                                            <td><b>{{ isset($malfunction->data['calculate'][$category['id']]['value']) && strpos($malfunction->data['calculate'][$category['id']]['value'], '%') !== false ? $malfunction->data['calculate'][$category['id']]['value'] : '0%'}}</b></td>
                                        </tr>
                                        <tr id="category-paragraph-tr-{{$category['id']}}" style="display: none;">
                                            <td colspan="3" style="padding: 0px;">
                                                <table class="table table-striped" border="1" style="margin-bottom: 0px;">
                                                    <tr>
                                                        <td width="33.3%">{{__('Paragraph')}}</td>
                                                        <td width="33.3%">{{__('F/S/B/N/C')}}</td>
                                                        <td width="33.4%">{{__('CP Score')}}</td>
                                                    </tr>
                                                    @php
                                                        $relatively = ['total' => 0, 'count' => 0];
                                                    @endphp

                                                    @if (isset($malfunction->data['paragraphs']) && isset($malfunction->data['paragraphs'][$category['id']]))
                                                        @foreach($malfunction->data['paragraphs'][$category['id']] as $paragraph_key => $paragraph)

                                                            <tr>
                                                                <td>{{ ($category_key+1) .'.'. ($paragraph_key+1) }} {{$paragraph['name'] ?? \App\Models\Paragraph::find($paragraph['id'])->name}}</td>
                                                                <td style="padding: 0px; text-align: center;">
                                                                    <table class="table" style="background: transparent; margin-bottom: 0px;">
                                                                        <tr style="background: transparent;">
                                                                            @foreach(['F', 'S', 'B', 'N', 'C'] as $type)
                                                                                <td style="border-top: 0px; @if (!$loop->last) {{App::getLocale() == 'he' ? 'border-left: 1px solid grey;' : 'border-right: 1px solid grey;'}} @endif" width="20%">
                                                                                    @if(isset($malfunction->data['malfunction_type'][$category['id']][$paragraph['id']]) && $malfunction->data['malfunction_type'][$category['id']][$paragraph['id']] == $type)
                                                                                        X
                                                                                    @endif
                                                                                </td>
                                                                            @endforeach
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                                <td style="padding: 0px;">
                                                                    <table class="table" style="background: transparent; margin-bottom: 0px;">
                                                                        <tr style="background: transparent;">
                                                                            <td style="border-top: 0px; {{App::getLocale() == 'he' ? 'border-left: 1px solid black;' : 'border-right: 1px solid black;'}}" width="65%">
                                                                                @php
                                                                                    if (isset($malfunction->data['malfunction_type'][$category['id']][$paragraph['id']])) {
                                                                                        $relatively['count']++;

                                                                                        switch ($malfunction->data['malfunction_type'][$category['id']][$paragraph['id']]) {
                                                                                            case 'S':
                                                                                                echo '50%';
                                                                                                $relatively['total'] += 50;
                                                                                                break;
                                                                                            case 'B':
                                                                                                echo '0%';
                                                                                                break;
                                                                                            default:
                                                                                                echo '100%';
                                                                                                $relatively['total'] += 100;
                                                                                        }
                                                                                    }
                                                                                @endphp
                                                                            </td>
                                                                            <td style="border-top: 0px;" width="35%">{{ isset($malfunction->data['calculate'][$category['id']][$paragraph['id']]) && strpos($malfunction->data['calculate'][$category['id']][$paragraph['id']], '%') !== false ? $malfunction->data['calculate'][$category['id']][$paragraph['id']] : '0%' }}</td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                    <tr>
                                                        <td>{{__('Total')}}</td>
                                                        <td></td>
                                                        <td style="padding: 0px;">
                                                            <table class="table" style="background: transparent;margin-bottom: 0px;">
                                                                <tr style="background: transparent;">
                                                                    <td style="border-top: 0px; {{App::getLocale() == 'he' ? 'border-left: 1px solid black;' : 'border-right: 1px solid black;'}}" width="65%">
                                                                        @php
                                                                            $score = $relatively['total'] / ($relatively['count'] ? $relatively['count'] : 1);
                                                                            $score = round($score, 1);
                                                                        @endphp
                                                                        {{ $score }} %
                                                                    </td>
                                                                    <td style="border-top: 0px;" width="35%">{{ isset($paragraphs_total[$category['id']]) && strpos($paragraphs_total[$category['id']], '%') !== 'false' ? $paragraphs_total[$category['id']] : '0' }}%</td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td><b>{{__('Total')}}</b></td>
                                        <td></td>
                                        <!-- <td><b>{{ $categories_total }}%</b></td> -->
                                        <td><b>{{ isset($malfunction->data['calculate']['total']) && strpos($malfunction->data['calculate']['total'], '%') !== false ? $malfunction->data['calculate']['total'] : "---" }}</b></td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="malfunctions-item other">
                            <h5><b>2. {{__('General')}}</b></h5>
                            {!! isset( $malfunction->data['malfunction-general'] ) ?  $malfunction->data['malfunction-general'] : "" !!}
                        </div>
                        <div class="malfunctions-item other">
                            <h5><b>3. {{__('Repaired malfunctions')}}</b></h5>
                            <?php echo (isset( $malfunction->data['malfunction-repaired'] ) ?  $malfunction->data['malfunction-repaired'] : "") ?>
                        </div>
                        <div class="malfunctions-item other">
                            <h5><b>4. {{__('Principal malfunctions')}}</b></h5>
                            @if (isset($malfunction->data['malfunction_principal_detail']))
                                <ul class="malfunctions-principal">
                                    @foreach($malfunction->data['malfunction_principal_detail'] as $principal)
                                        <li class="malfunctions-item" data-malfunction-id="<?php echo $malfunction_id ?>" data-category-id="<?php echo $principal["category_id"] ?>" data-principal-id="<?php echo $principal["id"] ?>">
                                            <?php echo (isset($principal["value"]) ? str_replace(',', '', $principal["value"]) : "") ?>
                                            @if (isset($principal["category_id"]) && isset($principal["id"]) && isset($malfunction->data['photo'][$principal["category_id"]][$principal["id"]]))
                                                <div class="malfunction-scoring__photos show">
                                                    @foreach ($malfunction->data['photo'][$principal["category_id"]][$principal["id"]] as $photo)
                                                        @php
                                                            $name = explode('.', $photo);
                                                            $extention = $name[count($name) - 1];
                                                            $extention = strtolower($extention);
                                                            $is_image = in_array($extention, ["jpg", "jpeg", "bmp", "gif", "png"]);
                                                            $path = url('/').($is_image ? '/images/' : '/uploads/');
                                                        @endphp
                                                        <div class="malfunction-principal__photo-item">
                                                            <a href="{{ $path.$photo }}" target="_blank">
                                                                @if ($is_image)
                                                                    <img src="{{$path.$photo}} " class="malfunction-principal__photo-item" alt="{{ $photo }}"/>
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
                                                @if (isset($malfunction->data['comments']) && isset($malfunction->data['comments'][$principal["id"]]))
                                                    @foreach($malfunction->data['comments'][$principal["id"]] as $key => $comment)
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
                        <div class="malfunctions-item other">
                            @if(isset($malfunction->data["malfunction-culinary"]) && isset($malfunction->data["malfunction-culinary"]["checked"]))
                                <h5><b>5. {{__('Culinary')}}</b></h5>
                                {!! $malfunction->data["malfunction-culinary"]["text"] !!}
                            @else
                                <h5><b>5.</b></h5>
                            @endif
                        </div>
                        <div class="malfunctions-item other">
                            <h5><b>6. {{__('Malfunction list')}}</b></h5>
                            @if(isset($malfunction->data['malfunction-list']))
                                {!! $malfunction->data['malfunction-list'] !!}
                            @endif
                        </div>

                        <div class="malfunctions-item other">
                            <h5><b>7. {{App::getLocale() == 'en' ? 'Summary & Recommendations' : 'מסקנות והמלצות'}}</b></h5>
                            @if(isset($malfunction->data['malfunction-list']))
                                {!! $malfunction->data['malfunction-summary'] !!}
                            @endif
                        </div>

                        <div class="malfunctions-item other">
                            <h5><b>8. {{__('Uploaded documents')}}</b></h5>
                            @isset($malfunction->data['malfunction-uploads'])
                                @foreach($malfunction->data['malfunction-uploads'] as $upload)
                                    <div class="malfunction-uploads__item">
                                        <a href="{{ url('/') }}/uploads/{{$upload}}" target="_blank" class="malfunction-uploads__text">
                                            @if(isImage($upload))
                                                <img class="malfunction-principal__photo-item" src="{{ url('/') }}/uploads/{{$upload}}" alt="{{$upload}}"/>
                                            @else
                                                {{ strlen($upload) > 20 ? mb_substr($upload, 0, 20, 'utf-8') : $upload }}
                                            @endif
                                        </a>
                                    </div>
                                @endforeach
                                <div class="clear-both"></div>
                            @endisset
                        </div>
                        <div class="malfunctions-item other">
                            <h5 style="font-size: 0.9rem;">{{__('Sincerely')}},</h5>
                            <h5 style="font-size: 0.9rem;">{{isset($malfunction->data['employee_name']) ? $malfunction->data['employee_name'] : ""}}</h5>
                        </div>
                    </div>
                    <div id='printing-holder'></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{url('/')}}/js/canvasjs.min.js"></script>
    <script src="{{url('/')}}/js/pdf/jspdf.min.js"></script>
    <script src="{{url('/')}}/js/pdf/html2canvas.min.js"></script>
    <script src="{{url('/')}}/js/pdf/html2pdf.js"></script>
    <script src="{{asset('js/malfunctions/show.js')}}"></script>

    <link rel="stylesheet" href="{{ url('/') }}/css/malfunctions/main.css" media="all">
    <link rel="stylesheet" href="{{ url('/') }}/css/malfunctions/show.css" media="all">
    <link rel="stylesheet" href="{{ url('/') }}/css/malfunctions/print.css" media="print">

    @if (app()->getLocale() == 'he')
        <link rel="stylesheet" href="{{ url('/') }}/css/malfunctions/main_rtl.css" media="all">
    @endif

    <script type="text/javascript">
        var GET_STATISTICS_URL = "<?php echo e(route('get-statistics')); ?>",
            SEND_PDF_URL = "<?php echo e(route('malfunctionSharePdf')); ?>",
            DUPLICATE_MALFUCNTION_URL = "<?php echo e(route('duplicateMalfunction')); ?>",
            SAVE_COMMENTS_URL = "<?php echo e(route('malfunctionSaveComments', $malfunction_id)); ?>",
            DELETE_MALFUNCTION_URL = "{{ action('MalfunctionController@destroy', $malfunction->id) }}",
            TOKEN = "{{ csrf_token() }}",
            BASE_URL = "{{ url('/') }}";
        DATE = "<?php echo $date ?>",
            REPORT_DATE = "{{ isset($malfunction->data['date']) ? $malfunction->data['date'] : '' }}",
            MALFUNCTION_ID = <?php echo $malfunction_id ?>,
            SITE = "<?php echo $malfunction->data['site'] ?>",
            SUBSITE =  "<?php echo $malfunction->data['subsite'] ?>",
            user = <?php echo $user ?>,
            LOCALE = "{{app()->getLocale()}}";

        var Lang = {
            "Edit" : "{{__('Edit')}}",
            "Delete" : "{{__('Delete')}}",
            "Score" : "{{__('CP Score')}}",
            "Date" : "{{__('Date')}}",
            "Print" : "{{__('Print')}}",
            "Message sent successfully" : "{{__('Message sent successfully')}}",
            "Email was not sent" : "{{__('Email was not sent')}}",
            "Repeating" : "{{__('Repeating malfunction')}}",
            "Are you sure you want to delete it?" : "{{__('Are you sure you want to delete it?')}}",
            "Yes" : "{{__('Yes')}}",
            "Cancel(no)" : "{{__('Cancel(no)')}}"
        }
    </script>
@stop
