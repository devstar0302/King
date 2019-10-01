@extends('layouts.app')

@section('content')
    <?php
    $user_temp = json_decode($user);
    $user_id = $user_temp->id;
    $user_role = strtolower($user_temp->title);
    $user_name = $user_temp->name; ?>

    <div id="app">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card malfunctions {{$user_role}}">
                        <div class="card-header">
                            <input type="file" id="file" name="uploads[]" multiple onchange="uploadFiles(this)" hidden>
                            <div class="dropdown-wrapper">
                                <div class="dropdown">
                                    <label class="dropdown-toggle" data-toggle="dropdown"><i
                                                class="fa fa-upload"></i></label>
                                    <ul class="dropdown-menu">
                                        <li><a href="javascript:void(0)"
                                               id='upload_from_computer'>{{__('Upload from computer')}}</a></li>
                                        <li><a href="javascript:void(0);"
                                               onclick="uploadFromSignsForms()">{{__('Upload from Signs&forms')}}</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <h3 class="text-center text-primary">{{__('Food risk report(malfunction)')}}</h3>
                            <h5 class="text-center">#{{$nameCode}}</h5>
                        </div>

                        <div class="card-body">
                            <form action="{{ action('MalfunctionController@update', $malfunction_id) }}" method="POST" id="malfunction-form" style="width:100%;">
                                {{method_field('PUT')}}

                                <input type="hidden" id="calc-input-total" name="data[calculate][total]"
                                       value="{{ isset($malfunction['calculate']['total']) && strpos($malfunction['calculate']['total'], '%') !== false ? $malfunction['calculate']['total'] : '' }}">
                                <input type="hidden" name="data[site]"
                                       value="{{ isset($malfunction['site']) ? $malfunction['site'] : '' }}"/>
                                <input type="hidden" name="data[subsite]"
                                       value="{{ isset($malfunction['subsite']) ? $malfunction['subsite'] : '' }}"/>
                                <input type="hidden" name="data[date]"
                                       value="{{ isset($malfunction['date']) ? $malfunction['date'] : '' }}"/>
                                <input type="hidden" name="data[time_]"
                                       value="{{ isset($malfunction['time_']) ? $malfunction['time_'] : '' }}"/>
                                <input type="hidden" name="data[service_level]"
                                       value="{{ isset($malfunction['service_level']) ? $malfunction['service_level'] : '' }}"/>
                                <input type="hidden" name="data[risk_level]"
                                       value="{{ isset($risk['level']) ? $risk['level'] : '' }}"/>
                                <input type="hidden" name="data[employee_id]"
                                       @if ($user_role == 'admin' || $user_role == 'employee') value="{{$user_id}}"
                                       @else value="{{isset($malfunction['employee_id']) ? $malfunction['employee_id'] : ''}}" @endif/>
                                <input type="hidden" name="data[employee_name]"
                                       @if (($user_role == 'admin' || $user_role == 'employee') && !isset($malfunction['employee_name'])) value="{{$user_name}}"
                                       @else value="{{isset($malfunction['employee_name']) ? $malfunction['employee_name'] : ''}}" @endif/>
                                <input type="hidden" name="data[admin_name]"
                                       value="{{isset($malfunction['status']) && isset($malfunction['status']['stage']) && $malfunction['status']['stage'] == 'publish' ? $malfunction['admin_name'] : ''}}"/>
                                <input type="hidden" name="data[gastronomy_score]"
                                       value="{{ isset($malfunction['gastronomy_score']) && strpos($malfunction['gastronomy_score'], '%') !== false ? $malfunction['gastronomy_score'] : '' }}"/>
                                <input type="hidden" name="data[status][stage]"
                                       @if (($user_role == 'admin' || $user_role == 'employee') && (!isset($malfunction['status']) || !isset($malfunction['status']['stage']) || $malfunction['status']['stage'] == 'draft')) value="draft"
                                       @else value="{{isset($malfunction['status']) && isset($malfunction['status']['stage']) ? $malfunction['status']['stage'] : ''}}" @endif/>
                                <input type="hidden" name="data[status][admin_changed_date]" value="unchanged"/>
                                <div class="row malfunction-total-summary">
                                    <div class="col-md-4">
                                        <?php $site_id = ""; ?>
                                        <select name="site_" class="ml-2" id="site" onchange="updateButtonColor()">
                                            <option {{ (isset($malfunction['site']) && $malfunction['site']) ? '' : 'selected' }} value="site">{{__('Site')}}</option>
                                            @foreach($sites as $site)
                                                <option value="{{$site->id}}"
                                                        @if(isset($malfunction['site']) && $malfunction['site'] == $site->title)
                                                        selected
                                                <?php $site_id = $site->id; ?>
                                                        @endif
                                                >{{$site->title}}</option>
                                            @endforeach
                                        </select>
                                        <select {{ (isset($malfunction['site']) && $malfunction['site']) ? '' : 'disabled' }} name="subsite_"
                                                id="subsite" onchange="updateButtonColor()">
                                            <option {{ (isset($malfunction['site']) && $malfunction['site']) ? '' : 'selected' }} value="subsite">{{__('Sub-site')}}</option>
                                            @foreach($subsites as $subsite)
                                                @if((isset($malfunction['site']) && $malfunction['site'] && $subsite->site_id == $site_id))
                                                    <option value="{{$subsite->id}}" {{ (isset($malfunction['subsite']) && $malfunction['subsite'] == $subsite->title) ? 'selected' : '' }}>{{$subsite->title}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <div class="col-md-12 mt-4">
                                            <ul style="{{App::getLocale() == 'he' ? 'padding-right: 10px !important' : 'padding-left: 10px !important'}}">
                                                <li>
                                                    <label for="resp">{{__('Company representative')}}: </label>
                                                    <input type="text" id="resp" class="edit-field" readonly
                                                           placeholder="" size="15" name="data[company_representative]"
                                                           value="{{ isset($malfunction['company_representative']) ? $malfunction['company_representative'] : '' }}">
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="employe">
                                            <li>{{__('Employee name')}}:
                                                <span>@if (($user_role == 'admin' || $user_role == 'employee') && !isset($malfunction['employee_name'])) {{ $user_name }} @else {{isset($malfunction['employee_name']) ? $malfunction['employee_name'] : ""}} @endif</span>
                                            </li>
                                            <li>{{__('Admin name')}}:
                                                <span>{{isset($malfunction['admin_name']) ? $malfunction['admin_name'] : ""}}</span>
                                            </li>
                                            <li>{{__('Date')}}: <input id="datepicker" class="edit-field"
                                                                       data-date-format="mm/dd/yyyy"
                                                                       value="{{isset($malfunction['date']) ? $malfunction['date'] : ""}}">
                                            </li>
                                            <li>{{__('Time')}}: <input type="text" id="time" class="edit-field" size="4"
                                               maxlength="5"
                                               pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]"
                                               onchange="updateTime(this);"
                                               value="{{isset($malfunction['time_']) ? $malfunction['time_'] : ""}}">
                                            </li>
                                            <li>{{__('Contract representative')}}: <input type="text" size="15"
                                                  class="contractor_representative edit-field"
                                                  name="data[contractor_representative]"
                                                  placeholder=""
                                                  value="{{ isset($malfunction['contractor_representative']) ? $malfunction['contractor_representative'] : '' }}"/>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="totals">
                                            <li>{{__('Total score')}}:
                                                <span class="total">
                                                @if (isset($malfunction['calculate']['total']) && strpos($malfunction['calculate']['total'], '%') !== false)
                                                        {{ $malfunction['calculate']['total'] }}
                                                    @else
                                                        ---
                                                    @endif
                                            </span>
                                            </li>

                                            {{--@TODO here--}}
                                            <li>{{__('Risk level')}}: <span id="risk-value-span" class="risk">
                                                @if (isset($risk) && in_array($risk['level'], [0,1,2]))
                                                        @if ($risk['level'] == 'Low') {{__('Low')}}
                                                        @elseif ($risk['level'] == 'Medium') {{__('Medium')}}
                                                        @elseif ($risk['level'] == 'High') {{__('High')}}
                                                        @endif
                                                    @else
                                                        {{ '---' }}
                                                    @endif
                                            </span></li>
                                            <li>{{__('Gastronomy score')}}: <span
                                                        id="gastronome">{{ isset($malfunction['gastronomy_score']) && strpos($malfunction['gastronomy_score'], '%') !== false ? $malfunction['gastronomy_score'] : '---' }}</span>
                                            </li>
                                            <li>{{__('Service level')}}:`
                                                <select id="service_level" onchange="updateButtonColor()">
                                                    <option value="-1" {{ (!isset($malfunction['service_level']) || $malfunction['service_level'] == '-1') ? 'selected' : '' }}>{{__('Type')}}</option>
                                                    <option value="Very good" {{ (isset($malfunction['service_level']) && $malfunction['service_level'] == 'Very good') ? 'selected' : '' }}>{{__('Very good')}}</option>
                                                    <option value="Good" {{ (isset($malfunction['service_level']) && $malfunction['service_level'] == 'Good') ? 'selected' : '' }}>{{__('Good')}}</option>
                                                    <option value="Bad" {{ (isset($malfunction['service_level']) && $malfunction['service_level'] == 'Bad') ? 'selected' : '' }}>{{__('Bad')}}</option>
                                                    <option value="N/A" {{ (isset($malfunction['service_level']) && $malfunction['service_level'] == 'N/A') ? 'selected' : '' }}>{{__('N/A')}}</option>
                                                </select>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-md-12 malfunction-scoring">
                                    <div><h4 class="malfunction-heading">1. {{__('Scoring')}}</h4></div>
                                    <div id="chartContainer"
                                         style="height:370px; width:90%; margin: 20px auto; direction:ltr;"></div>
                                    @foreach ($categories as $key_category => $category)
                                        <input type="hidden" name="data[categories][{{ $key_category }}][id]"
                                               value="{{ $category->id }}">
                                        <input type="hidden" name="data[categories][{{ $key_category }}][name]"
                                               value="{{ $category->name }}">
                                        <input type="hidden" name="data[categories][{{ $key_category }}][score]"
                                               value="{{ $category->score }}">
                                        @if (count($category->paragraphs))
                                            <input type="hidden" id="calc-input-category-{{ $category->id }}"
                                                   name="data[calculate][{{ $category->id }}][value]"
                                                   value="{{ isset($malfunction['calculate'][$category->id]['value']) ? $malfunction['calculate'][$category->id]['value'] : 'false' }}">
                                            <div><h5>{{ $key_category+1 }}. {{ $category->name }}</h5></div>
                                            <table class="malfunction-scoring__table"
                                                   data-category-id="{{ $category->id }}">
                                                <thead>
                                                <tr>
                                                    <td class="malfunction-scoring__paragraph">{{__('#Paragraph')}}</td>
                                                    <td class="malfunction-scoring__type">{{__('Malfunction type')}}</td>
                                                    <td class="malfunction-scoring__finding">{{__('Finding')}}</td>
                                                    <td class="malfunction-scoring__risk">{{__('Risk + Repair')}}</td>
                                                    <td class="malfunction-scoring__principal">{{__('Principal malfunction')}}</td>
                                                    {{--<td class="malfunction-scoring__photos">{{__('Photos')}}</td>--}}
                                                    <td class="malfunction-scoring__score">{{__('Scoring')}}</td>
                                                </tr>
                                                </thead>

                                                <tbody class="malfunction-scoring__items">
                                                @foreach ($category->paragraphs as $key_paragraph => $paragraph)
                                                    <input type="hidden"
                                                           id="calc-input-paragraph-{{ $category->id }}-{{ $paragraph->id }}"
                                                           name="data[calculate][{{ $category->id }}][{{ $paragraph->id }}]"
                                                           value="{{ isset($malfunction['calculate'][$category->id][$paragraph->id]) ? $malfunction['calculate'][$category->id][$paragraph->id] : 'false' }}">

                                                    <input type="hidden"
                                                           name="data[paragraphs][{{$category->id}}][{{$key_paragraph}}][id]"
                                                           value="{{ $paragraph->id }}">
                                                    <input type="hidden"
                                                           name="data[paragraphs][{{$category->id}}][{{$key_paragraph}}][name]"
                                                           value="{{ $paragraph->name }}">
                                                    <input type="hidden"
                                                           name="data[paragraphs][{{$category->id}}][{{$key_paragraph}}][score]"
                                                           value="{{ $paragraph->score }}">

                                                    <tr class="malfunction-scoring__item"
                                                        data-category-name="{{ $category->name }}"
                                                        data-category-id="{{ $category->id }}"
                                                        data-id="{{ $paragraph->id }}"
                                                        data-value="{{ $paragraph->score }}"
                                                        data-type="{{ $paragraph->type }}"
                                                        data-frr="{{ json_encode($paragraph->frr) }}">

                                                        <td class="malfunction-scoring__paragraph">
                                                            <div style="display: flex; justify-content: flex-start">
                                                                <div class="malfunction-scoring__paragraph-number">{{ $key_category+1 }}.{{ $key_paragraph + 1 }}</div>
                                                                &nbsp;
                                                                <div class="malfunction-scoring__paragraph-name">{{ $paragraph->name }}</div>
                                                            </div>
                                                        </td>

                                                        <td class="malfunction-scoring__type"
                                                            onchange="updateButtonColor()">
                                                            <input id="paragraph-type-hidden-input" type="hidden"
                                                                   value="{{ \App\Models\Paragraph::find($paragraph->id)->type }}">
                                                            <select name="data[malfunction_type][{{ $category->id }}][{{ $paragraph->id }}]"
                                                                    id="change-risk-select"
                                                                    class="malfunction-type-select">
                                                                <option
                                                                        {{ isset($malfunction['malfunction_type'][$category->id][$paragraph->id]) && $malfunction['malfunction_type'][$category->id][$paragraph->id] == 'F' ? 'selected' : '' }} value="F">
                                                                    {{__('TYPE_F')}}
                                                                </option>
                                                                <option
                                                                        {{ isset($malfunction['malfunction_type'][$category->id][$paragraph->id]) && $malfunction['malfunction_type'][$category->id][$paragraph->id] == 'S' ? 'selected' : '' }} value="S">
                                                                    {{__('TYPE_S')}}
                                                                </option>
                                                                {{--@TODO here--}}
                                                                <option
                                                                        {{ isset($malfunction['malfunction_type'][$category->id][$paragraph->id]) && $malfunction['malfunction_type'][$category->id][$paragraph->id] == 'B' ? 'selected' : '' }} value="B">
                                                                    {{__('TYPE_B')}}
                                                                </option>
                                                                <option
                                                                        {{ isset($malfunction['malfunction_type'][$category->id][$paragraph->id]) && $malfunction['malfunction_type'][$category->id][$paragraph->id] == 'N' ? 'selected' : '' }} value="N">
                                                                    {{__('TYPE_N')}}
                                                                </option>
                                                                <option
                                                                        {{ isset($malfunction['malfunction_type'][$category->id][$paragraph->id]) && $malfunction['malfunction_type'][$category->id][$paragraph->id] == 'C' ? 'selected' : '' }} value="C">
                                                                    {{__('TYPE_C')}}
                                                                </option>
                                                            </select>
                                                        </td>

                                                        <td class="malfunction-scoring__finding"
                                                            onchange="updateButtonColor()">
                                                            <select style="display: none;"
                                                                    multiple
                                                                    data-placeholder="בחר ממצא"
                                                                    summernote-textarea
                                                                    name="data[malfunction_finding][{{ $category->id }}][{{ $paragraph->id }}][]"
                                                                    class="malfunction-finding-select"
                                                                    {{ isset($malfunction['malfunction_type'][$category->id][$paragraph->id]) && in_array($malfunction['malfunction_type'][$category->id][$paragraph->id], ['S', 'B']) ? '' : 'disabled' }}>
                                                                @foreach ($paragraph->frr as $frr)
                                                                    <option value="{{ $frr->id }}" {{
                                                        isset($malfunction['malfunction_finding'][$category->id][$paragraph->id])
                                                        && in_array($frr->id, $malfunction['malfunction_finding'][$category->id][$paragraph->id])
                                                        ? 'selected'
                                                        : ''
                                                    }}>{{ $frr->finding }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td class="malfunction-scoring__risk {{ !isset($malfunction['malfunction_finding'][$category->id][$paragraph->id]) ? 'hasnt_active' : ''}}">
                                                            @foreach ($paragraph->frr as $frr)
                                                                {{--TODO refactor this--}}
                                                                <textarea style="display: none; float: right;"
                                                                          spellcheck="true"
                                                                          name="data[malfunction_rr][{{ $category->id }}][{{ $paragraph->id }}][{{$frr->id}}]"
                                                                          class="summernote-textarea summernote-paragraph-{{ $paragraph->id }} {{
                                                                        isset($malfunction['malfunction_finding'][$category->id][$paragraph->id])
                                                                        && in_array($frr->id, $malfunction['malfunction_finding'][$category->id][$paragraph->id])
                                                                        ? ''
                                                                        : 'disabled' }}"
                                                                          id="summernote-paragraph-{{ $paragraph->id }}-{{ $frr->id }}">
                                                                @if (isset($malfunction['malfunction_rr'][$category->id][$paragraph->id][$frr->id]))
                                                                        {{ $malfunction['malfunction_rr'][$category->id][$paragraph->id][$frr->id] }}
                                                                    @else
                                                                        <p>{{ $frr->finding }} {{ $frr->risk }} {{ $frr->repair }}</p>
                                                                    @endif
                                                            </textarea>
                                                            @endforeach
                                                        </td>

                                                        <td class="malfunction-scoring__principal {{ !isset($malfunction['malfunction_finding'][$category->id][$paragraph->id]) ? 'hasnt_active' : ''}}">
                                                            <div style="display: inline-block;">
                                                                @foreach ($paragraph->frr as $frr)
                                                                    <input
                                                                            {{
                                                                               (isset($malfunction['malfunction_finding'][$category->id][$paragraph->id])
                                                                               && in_array($frr->id, $malfunction['malfunction_finding'][$category->id][$paragraph->id]))
                                                                               &&
                                                                               (isset($malfunction['malfunction_type'][$category->id][$paragraph->id])
                                                                            && in_array($malfunction['malfunction_type'][$category->id][$paragraph->id], ['S', 'B']))
                                                                               ? ''
                                                                               : 'disabled'
                                                                           }}
                                                                            name="data[malfunction_principal][{{ $category->id }}][{{ $paragraph->id }}][{{$frr->id}}]"
                                                                            type="hidden"
                                                                            value="off"
                                                                    />
                                                                    <input {{ isset($malfunction['malfunction_principal'][$category->id][$paragraph->id][$frr->id])
                                                                    && $malfunction['malfunction_principal'][$category->id][$paragraph->id][$frr->id] == 'on'
                                                                    ? 'checked'
                                                                    : '' }}
                                                                           {{
                                                                               (isset($malfunction['malfunction_finding'][$category->id][$paragraph->id])
                                                                               && in_array($frr->id, $malfunction['malfunction_finding'][$category->id][$paragraph->id]))
                                                                               &&
                                                                               (isset($malfunction['malfunction_type'][$category->id][$paragraph->id])
                                                                            && in_array($malfunction['malfunction_type'][$category->id][$paragraph->id], ['S', 'B']))
                                                                               ? ''
                                                                               : 'disabled'
                                                                           }}
                                                                           name="data[malfunction_principal][{{ $category->id }}][{{ $paragraph->id }}][{{$frr->id}}]"
                                                                           class="malfunction-scoring__principal-checkbox"
                                                                           type="checkbox">
                                                                @endforeach
                                                            </div>
                                                        </td>
                                                        <td class="malfunction-scoring__score">
                                                            @if (isset($malfunction['calculate'][$category->id][$paragraph->id]) && strpos($malfunction['calculate'][$category->id][$paragraph->id], '%') !== false)
                                                                {{ $malfunction['calculate'][$category->id][$paragraph->id] }}
                                                            @else
                                                                0%
                                                            @endif

                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="7"
                                                        style="{{app()->getLocale() == 'he'? 'text-align: left;' : 'text-align: right;'}}">
                                                        <b>{{__('Category score')}}:</b>
                                                        <span id="category-score-{{ $category->id }}"
                                                              class="category-id"
                                                              data-category-value="{{ $category->score/100 }}">
                                                        @if (isset($malfunction['calculate'][$category->id]['value']) && strpos($malfunction['calculate'][$category->id]['value'], '%') !== false)
                                                                {{ (array_sum(array_except($malfunction['calculate'][$category->id], 'value'))) ."%" }}
                                                        @else
                                                                ---
                                                        @endif
                                                    </span>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>

                                        @endif
                                    @endforeach

                                    <div class="malfunction-scoring__total-score">
                                        <div class="malfunction-scoring__total-info">
                                            <b>{{__('Total score')}}</b>
                                            <span style="width: auto; {{app()->getLocale() == 'he'? 'text-align: left;' : 'text-align: right;'}}" class="fork-number" fork="{{ $fork }}">
                                        @if (isset($malfunction['calculate']['total']))
                                                    {{ $malfunction['calculate']['total'] }}
                                                @else
                                                    ---
                                                @endif
                                    </span>
                                        </div>
                                        <div style="{{app()->getLocale() == 'he'? 'text-align: left;' : 'text-align: right;'}}">
                                            <p id="malfunction-calc-error-msg" style="margin-bottom: 0;
                                        color: red;"></p>
                                            <a href="" class="malfunction-scoring__calculate"
                                               id="calculate">{{__('Calculate')}}</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 malfunction-general malfunction-other hidden">
                                    <div><h4 class="malfunction-heading">2. {{__('General')}}</h4></div>
                                    <textarea name="data[malfunction-general]" id="malfunction-general-textarea"
                                              class="summernote-textarea" style="display: none" spellcheck="true">
                                    @isset($malfunction['malfunction-general'])
                                            {{ $malfunction['malfunction-general'] }}
                                        @endif
                                </textarea>
                                </div>
                                <div class="col-md-12 malfunction-repaired malfunction-other hidden">
                                    <div><h4 class="malfunction-heading">3. {{__('Repaired malfunctions')}}</h4></div>
                                    <textarea name="data[malfunction-repaired]" id="malfunction-repaired-textarea"
                                          class="summernote-textarea" style="display: none" spellcheck="true">
                                        @isset($malfunction['malfunction-repaired'])
                                            {{ $malfunction['malfunction-repaired'] }}
                                        @endif
                                    </textarea>
                                </div>
                                <div class="col-md-12 malfunction-principal malfunction-other hidden">
                                    <div><h4 class="malfunction-heading">4. {{__('Principal malfunctions')}}</h4></div>
                                    <div id="malfunction-principal__items">
                                        @if (isset($malfunction['malfunction_principal_detail']))
                                            @foreach ($malfunction['malfunction_principal_detail'] as $key => $principal)
                                                @if (isset($malfunction['malfunction_principal'][$principal["category_id"]][$principal['id']]) &&
                                                 str_contains(implode(',', $malfunction['malfunction_principal'][$principal["category_id"]][$principal['id']]), 'on'))
                                                    <div class="malfunction-principal__item"
                                                         data-category-id="{{$principal["category_id"]}}"
                                                         data-id="{{$principal["id"]}}">
                                                        <div class="malfunction-sort"></div>
                                                        <textarea name="data[malfunction_principal_detail][{{$key}}][value]"
                                                                class="summernote-textarea" style="display: none" spellcheck="true">
                                                            {{ str_replace(',', '', $principal["value"]) }}
                                                        </textarea>

                                                        <input type="hidden"
                                                               name="data[malfunction_principal_detail][{{$key}}][category_id]"
                                                               value="{{$principal["category_id"]}}"/>
                                                        <input type="hidden"
                                                               name="data[malfunction_principal_detail][{{$key}}][id]"
                                                               value="{{$principal["id"]}}"/>

                                                        <div class="malfunction-scoring__photos {{ isset($malfunction['malfunction_type'][$principal["category_id"]][$principal["id"]]) && in_array($malfunction['malfunction_type'][$principal["category_id"]][$principal["id"]], ['S', 'B']) ? '' : 'disabled' }}">
                                                            @if (isset($malfunction['photo'][$principal["category_id"]][$principal["id"]]))
                                                                <input type="hidden"
                                                                       name="data[photo][{{$principal["category_id"]}}][{{$principal["id"]}}][]"
                                                                       value=""/>
                                                                @foreach ($malfunction['photo'][$principal["category_id"]][$principal["id"]] as $photo)
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
                                                                                <img src="{{$path.$photo}} "
                                                                                     class="malfunction-principal__photo-item"
                                                                                     alt="{{ $photo }}"/>
                                                                            @else
                                                                                {{ strlen($photo) > 20 ? mb_substr($photo, 0, 20, 'utf-8') : $photo }}
                                                                            @endif
                                                                        </a>
                                                                        <i class="malfunction-principal__photo-remove"></i>
                                                                        <input type="hidden"
                                                                               name="data[photo][{{$principal["category_id"]}}][{{$principal["id"]}}][]"
                                                                               value="{{$photo}}"/>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                            <div class='upload-photo-wrapper'>
                                                                <label class="malfunction-scoring__add-photo"
                                                                       for="add-image-{{ $principal["category_id"] }}-{{ $principal["id"] }}">
                                                                    <i class="fa fa-upload"></i>
                                                                    <input id="add-image-{{$principal["category_id"]}}-{{ $principal["id"] }}"
                                                                           class="malfunction-upload-image"
                                                                           type="file" {{ isset($malfunction['malfunction_type'][$principal["category_id"]][$principal["id"]]) && in_array($malfunction['malfunction_type'][$principal["category_id"]][$principal["id"]], ['S', 'B']) ? '' : 'disabled' }}>
                                                                </label>
                                                            </div>
                                                            <div class='cb'></div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-12 malfunction-repaired malfunction-other hidden">
                                    <div>
                                        <?php $checked = (isset($malfunction["malfunction-culinary"]) && isset($malfunction["malfunction-culinary"]["checked"])) ? $malfunction["malfunction-culinary"]["checked"] : false; ?>

                                        <h4 class="malfunction-heading">
                                            <input type="checkbox" name="data[malfunction-culinary][checked]"
                                                   {{$checked ? "checked" : ""}} id="malfunction-culinary-check"/>
                                            5. <span
                                                    class="{{ $checked ? "" : "hidden"}} malfunction-culinary-heading"> {{__('Culinary')}} </span>
                                        </h4>
                                    </div>
                                    <textarea name="data[malfunction-culinary][text]" id="malfunction-culinary-textarea"
                                          class="{{ $checked ?  "summernote-textarea" : "hidden" }}" spellcheck="true">
                                        @if(isset($malfunction["malfunction-culinary"]) && isset($malfunction["malfunction-culinary"]["text"]))
                                            {{ $malfunction["malfunction-culinary"]["text"] }}
                                        @else
                                            <p>5.1 {{__('General')}}</p>
                                            <p>5.2 {{__('Main dish')}}</p>
                                            <p>5.3 {{__('Toppings')}}</p>
                                            <p>5.4 {{__('Soups')}}</p>
                                            <p>5.5 {{__('Salads')}}</p>
                                            <p>5.6 {{__('Deserts')}}</p>
                                        @endif
                                    </textarea>
                                </div>


                                <div class="col-md-12 malfunction-list malfunction-other hidden">
                                    <div><h4 class="malfunction-heading">6. {{__('Malfunction list')}}</h4></div>
                                    <textarea name="data[malfunction-list]" id="malfunction-list-textarea"
                                            class="summernote-textarea" style="display: none" spellcheck="true">
                                        @isset($malfunction['malfunction-list'])
                                            {{ $malfunction['malfunction-list'] }}
                                        @endisset
                                     </textarea>
                                </div>
                                <div class="col-md-12 malfunction-summary malfunction-other hidden">
                                    <div><h4 class="malfunction-heading">7. {{App::getLocale() == 'en' ? 'Summary & Recommendations' : 'מסקנות והמלצות'}}</h4>
                                    </div>
                                    <textarea name="data[malfunction-summary]" id="malfunction-summary-textarea"
                                          class="summernote-textarea" style="display: none" spellcheck="true">
                                        @isset($malfunction['malfunction-summary'])
                                            {{ $malfunction['malfunction-summary'] }}
                                        @endisset
                                    </textarea>
                                </div>

                                <div class="col-md-12 malfunction-uploaded-documents malfunction-other hidden">
                                    <div><h4 class="malfunction-heading">8. {{__('Uploaded documents')}}</h4></div>
                                    <div id="malfunction-uploaded-documents">
                                        @isset($malfunction['malfunction-uploads'])
                                            <input type="hidden" name="data[malfunction-uploads][]" value=""/>
                                            @foreach($malfunction['malfunction-uploads'] as $key => $upload)
                                                <div class="malfunction-uploads__item">
                                                    <a href="{{ url('/') }}/uploads/{{$upload}}" target="_blank"
                                                       class="malfunction-uploads__text">
                                                        @if(isImage($upload))
                                                            <img class="malfunction-principal__photo-item"
                                                                 src="{{ url('/') }}/uploads/{{$upload}}"
                                                                 alt="{{$upload}}"/>
                                                        @else
                                                            {{ strlen($upload) > 20 ? mb_substr($upload, 0, 20, 'utf-8') : $upload }}
                                                        @endif
                                                    </a>
                                                    <i class="malfunction-uploads__item-remove"></i>
                                                    <input type="hidden" name="data[malfunction-uploads][]"
                                                           value="{{$upload}}"/>
                                                </div>
                                            @endforeach
                                        @endisset
                                    </div>
                                </div>
                                <div class='cb'></div>
                                <div style='display: flex; justify-content: center; align-items: center; margin-top: 10px;'>
                                    <a href="javascript:void(0);" id="send_to_admin"
                                       class="btn-blue">{{__('Send to admin')}}</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div id='printing-holder'></div>
                </div>
            </div>
        </div>
    </div>

    <div class='subview-container loader-container' style="display: none; right: 0">
        @include('frontend._part._modals')
    </div>

    <div class='subview-container'>
        @include('frontend._part._signs_forms')
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
        <link rel="stylesheet" href="{{ url('/') }}/css/malfunctions/main_rtl.css">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.js"></script>
    <script src="{{url('/')}}/js/summernote-list-styles.js"></script>
    <script src="{{asset('js/malfunctions/edit.js')}}"></script>

    @if (app()->getLocale() == 'he')
        <script src="{{url('/')}}/js/i18n/bootstrap-datepicker.he.js" charset="UTF-8"></script>
    @endif

    <script type="text/javascript">
        var user = <?php echo $user ?>,
            FILTER_COMPANY_URL = '{{ action('MalfunctionController@filterCompany') }}',
            FILTER_SITE_URL = '{{ action('MalfunctionController@filterSite') }}',
            FILTER_SUBSITE_URL = '{{ action('MalfunctionController@filterSubsite') }}',
            FORM_LIST_URL = '{{ action('MalfunctionController@index') }}',
            UPDATE_URL = '{{ action('MalfunctionController@update', $malfunction_id) }}',
            FIND_URL = '{{ action('MalfunctionController@find') }}',
            UPLOAD_FILE_URL = '{{ url('/') }}/upload-file',
            UPLOAD_FILES_URL = '{{ url('/') }}/upload-files',
            GET_STATISTICS_URL = "<?php echo e(route('get-statistics')); ?>",
            SEND_PDF_URL = "<?php echo e(route('malfunctionSharePdf')); ?>",
            DUPLICATE_MALFUCNTION_URL = "<?php echo e(route('duplicateMalfunction')); ?>",
            TOKEN = "{{ csrf_token() }}",
            BASE_URL = "{{ url('/') }}";
        LEVEL_URL = "{{action('MalfunctionController@level')}}",
            IS_SUBMITTED = {{ isset($malfunction['calculate']['total']) ? 'true' : 'false' }},
            MALFUNCTION_ID = "{{$malfunction_id}}",
            FORK = {{$fork}},
            LOCALE = "{{app()->getLocale()}}",
            categories = <?php echo $categories ?>,
            lastMalfunctionType = JSON.parse('{!! $lastMalfunctionType !!}'),
            lastMalfunctionFinding = <?php echo $lastMalfunctionFinding ?>,
            initialMalfunctionType = JSON.parse('{!! json_encode((isset($malfunction["malfunction_type"]) && (is_array($malfunction["malfunction_type"])) ? $malfunction["malfunction_type"] : [])) !!}');

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
            "date_desc": "{{__('Upload date DESC')}}",
            "date_asc": "{{__('Upload date ASC')}}",
            "type_asc": "{{__('Type A-Z')}}",
            "type_desc": "{{__('Type Z-A')}}",
            "name_asc": "{{__('Name A-Z')}}",
            "name_desc": "{{__('Name Z-A')}}"
        }

        var Lang = {
            "Are you sure?(send to admin)": "{{__('Are you sure?(send to admin)')}}",
            "Yes": "{{__('Yes')}}",
            "Cancel(no)": "{{__('Cancel(no)')}}",
            "Message sent successfully": "{{__('Message sent successfully')}}",
            "Email was not sent": "{{__('Email was not sent')}}",
            "Sub-site": "{{__('Sub-site')}}",
            "Score": "{{__('CP Score')}}",
            "Date": "{{__('Date')}}",
            "Print": "{{__('Print')}}",
            "SelectAll": "{{__('Select all')}}",
            "AllSelected": "{{__('All selected')}}",
            "NoMatchesFound": "{{__('No matches found')}}",
            "SomeFieldsAreMissiing": "{{__('Some fields are missing')}}",
            "Low": "{{__('Low')}}", "Medium": "{{__('Medium')}}", "High": "{{__('High')}}",
            "Repeating": "{{__('Repeating malfunction')}}",
            "Inspection_made": "{!!__('On dd/mm/yy an inspection was made', ['date' => '<span class=\'report-date\'></span>'])!!}",
            "Inspection_done": "{!!__('The inspection done together with:', ['rep' => '<span class=\'report-rep\'></span>'])!!}",
            "TwoPointThreeValue": "{!!__('Two Point three value:', ['rep' => '<span class=\'report-rep\'></span>'])!!}"
        }

        $('select[multiple]').multipleSelect({
            selectAllText: Lang['SelectAll'],
            allSelected: Lang['AllSelected'],
            noMatchesFound: Lang['NoMatchesFound']
        });

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
