<?php $__env->startSection('content'); ?>
    <?php
    $user_temp = json_decode($user);
    $user_id = $user_temp->id;
    $user_role = strtolower($user_temp->title);
    $user_name = $user_temp->name; ?>

    <div id="app">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card malfunctions <?php echo e($user_role); ?>">
                        <div class="card-header">
                            <input type="file" id="file" name="uploads[]" multiple onchange="uploadFiles(this)" hidden>
                            <div class="dropdown-wrapper">
                                <div class="dropdown">
                                    <label class="dropdown-toggle" data-toggle="dropdown"><i
                                                class="fa fa-upload"></i></label>
                                    <ul class="dropdown-menu">
                                        <li><a href="javascript:void(0)"
                                               id='upload_from_computer'><?php echo e(__('Upload from computer')); ?></a></li>
                                        <li><a href="javascript:void(0);"
                                               onclick="uploadFromSignsForms()"><?php echo e(__('Upload from Signs&forms')); ?></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <h3 class="text-center text-primary"><?php echo e(__('Food risk report(malfunction)')); ?></h3>
                            <h5 class="text-center">#<?php echo e($nameCode); ?></h5>
                        </div>

                        <div class="card-body">
                            <form id="malfunction-form" style="width:100%;">
                                <?php echo e(method_field('PUT')); ?>


                                <input type="hidden" id="calc-input-total" name="data[calculate][total]"
                                       value="<?php echo e(isset($malfunction['calculate']['total']) && strpos($malfunction['calculate']['total'], '%') !== false ? $malfunction['calculate']['total'] : ''); ?>">
                                <input type="hidden" name="data[site]"
                                       value="<?php echo e(isset($malfunction['site']) ? $malfunction['site'] : ''); ?>"/>
                                <input type="hidden" name="data[subsite]"
                                       value="<?php echo e(isset($malfunction['subsite']) ? $malfunction['subsite'] : ''); ?>"/>
                                <input type="hidden" name="data[date]"
                                       value="<?php echo e(isset($malfunction['date']) ? $malfunction['date'] : ''); ?>"/>
                                <input type="hidden" name="data[time_]"
                                       value="<?php echo e(isset($malfunction['time_']) ? $malfunction['time_'] : ''); ?>"/>
                                <input type="hidden" name="data[service_level]"
                                       value="<?php echo e(isset($malfunction['service_level']) ? $malfunction['service_level'] : ''); ?>"/>
                                <input type="hidden" name="data[risk_level]"
                                       value="<?php echo e(isset($risk['level']) ? $risk['level'] : ''); ?>"/>
                                <input type="hidden" name="data[employee_id]"
                                       <?php if($user_role == 'admin' || $user_role == 'employee'): ?> value="<?php echo e($user_id); ?>"
                                       <?php else: ?> value="<?php echo e(isset($malfunction['employee_id']) ? $malfunction['employee_id'] : ''); ?>" <?php endif; ?>/>
                                <input type="hidden" name="data[employee_name]"
                                       <?php if(($user_role == 'admin' || $user_role == 'employee') && !isset($malfunction['employee_name'])): ?> value="<?php echo e($user_name); ?>"
                                       <?php else: ?> value="<?php echo e(isset($malfunction['employee_name']) ? $malfunction['employee_name'] : ''); ?>" <?php endif; ?>/>
                                <input type="hidden" name="data[admin_name]"
                                       value="<?php echo e(isset($malfunction['status']) && isset($malfunction['status']['stage']) && $malfunction['status']['stage'] == 'publish' ? $malfunction['admin_name'] : ''); ?>"/>
                                <input type="hidden" name="data[gastronomy_score]"
                                       value="<?php echo e(isset($malfunction['gastronomy_score']) && strpos($malfunction['gastronomy_score'], '%') !== false ? $malfunction['gastronomy_score'] : ''); ?>"/>
                                <input type="hidden" name="data[status][stage]"
                                       <?php if(($user_role == 'admin' || $user_role == 'employee') && (!isset($malfunction['status']) || !isset($malfunction['status']['stage']) || $malfunction['status']['stage'] == 'draft')): ?> value="draft"
                                       <?php else: ?> value="<?php echo e(isset($malfunction['status']) && isset($malfunction['status']['stage']) ? $malfunction['status']['stage'] : ''); ?>" <?php endif; ?>/>
                                <input type="hidden" name="data[status][admin_changed_date]" value="unchanged"/>
                                <div class="row malfunction-total-summary">
                                    <div class="col-md-4">
                                        <?php $site_id = ""; ?>
                                        <select name="site_" class="ml-2" id="site" onchange="updateButtonColor()">
                                            <option <?php echo e((isset($malfunction['site']) && $malfunction['site']) ? '' : 'selected'); ?> value="site"><?php echo e(__('Site')); ?></option>
                                            <?php $__currentLoopData = $sites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($site->id); ?>"
                                                        <?php if(isset($malfunction['site']) && $malfunction['site'] == $site->title): ?>
                                                        selected
                                                <?php $site_id = $site->id; ?>
                                                        <?php endif; ?>
                                                ><?php echo e($site->title); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <select <?php echo e((isset($malfunction['site']) && $malfunction['site']) ? '' : 'disabled'); ?> name="subsite_"
                                                id="subsite" onchange="updateButtonColor()">
                                            <option <?php echo e((isset($malfunction['site']) && $malfunction['site']) ? '' : 'selected'); ?> value="subsite"><?php echo e(__('Sub-site')); ?></option>
                                            <?php $__currentLoopData = $subsites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subsite): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if((isset($malfunction['site']) && $malfunction['site'] && $subsite->site_id == $site_id)): ?>
                                                    <option value="<?php echo e($subsite->id); ?>" <?php echo e((isset($malfunction['subsite']) && $malfunction['subsite'] == $subsite->title) ? 'selected' : ''); ?>><?php echo e($subsite->title); ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <div class="col-md-12 mt-4">
                                            <ul style="<?php echo e(App::getLocale() == 'he' ? 'padding-right: 10px !important' : 'padding-left: 10px !important'); ?>">
                                                <li>
                                                    <label for="resp"><?php echo e(__('Company representative')); ?>: </label>
                                                    <input type="text" id="resp" class="edit-field" readonly
                                                           placeholder="" size="15" name="data[company_representative]"
                                                           value="<?php echo e(isset($malfunction['company_representative']) ? $malfunction['company_representative'] : ''); ?>">
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="employe">
                                            <li><?php echo e(__('Employee name')); ?>:
                                                <span><?php if(($user_role == 'admin' || $user_role == 'employee') && !isset($malfunction['employee_name'])): ?> <?php echo e($user_name); ?> <?php else: ?> <?php echo e(isset($malfunction['employee_name']) ? $malfunction['employee_name'] : ""); ?> <?php endif; ?></span>
                                            </li>
                                            <li><?php echo e(__('Admin name')); ?>:
                                                <span><?php echo e(isset($malfunction['admin_name']) ? $malfunction['admin_name'] : ""); ?></span>
                                            </li>
                                            <li><?php echo e(__('Date')); ?>: <input id="datepicker" class="edit-field"
                                                                       data-date-format="mm/dd/yyyy"
                                                                       value="<?php echo e(isset($malfunction['date']) ? $malfunction['date'] : ""); ?>">
                                            </li>
                                            <li><?php echo e(__('Time')); ?>: <input type="text" id="time" class="edit-field" size="4"
                                               maxlength="5"
                                               pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]"
                                               onchange="updateTime(this);"
                                               value="<?php echo e(isset($malfunction['time_']) ? $malfunction['time_'] : ""); ?>">
                                            </li>
                                            <li><?php echo e(__('Contract representative')); ?>: <input type="text" size="15"
                                                  class="contractor_representative edit-field"
                                                  name="data[contractor_representative]"
                                                  placeholder=""
                                                  value="<?php echo e(isset($malfunction['contractor_representative']) ? $malfunction['contractor_representative'] : ''); ?>"/>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="totals">
                                            <li><?php echo e(__('Total score')); ?>:
                                                <span class="total">
                                                <?php if(isset($malfunction['calculate']['total']) && strpos($malfunction['calculate']['total'], '%') !== false): ?>
                                                        <?php echo e($malfunction['calculate']['total']); ?>

                                                    <?php else: ?>
                                                        ---
                                                    <?php endif; ?>
                                            </span>
                                            </li>

                                            
                                            <li><?php echo e(__('Risk level')); ?>: <span id="risk-value-span" class="risk">
                                                <?php if(isset($risk) && in_array($risk['level'], [0,1,2])): ?>
                                                        <?php if($risk['level'] == 'Low'): ?> <?php echo e(__('Low')); ?>

                                                        <?php elseif($risk['level'] == 'Medium'): ?> <?php echo e(__('Medium')); ?>

                                                        <?php elseif($risk['level'] == 'High'): ?> <?php echo e(__('High')); ?>

                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <?php echo e('---'); ?>

                                                    <?php endif; ?>
                                            </span></li>
                                            <li><?php echo e(__('Gastronomy score')); ?>: <span
                                                        id="gastronome"><?php echo e(isset($malfunction['gastronomy_score']) && strpos($malfunction['gastronomy_score'], '%') !== false ? $malfunction['gastronomy_score'] : '---'); ?></span>
                                            </li>
                                            <li><?php echo e(__('Service level')); ?>:`
                                                <select id="service_level" onchange="updateButtonColor()">
                                                    <option value="-1" <?php echo e((!isset($malfunction['service_level']) || $malfunction['service_level'] == '-1') ? 'selected' : ''); ?>><?php echo e(__('Type')); ?></option>
                                                    <option value="Very good" <?php echo e((isset($malfunction['service_level']) && $malfunction['service_level'] == 'Very good') ? 'selected' : ''); ?>><?php echo e(__('Very good')); ?></option>
                                                    <option value="Good" <?php echo e((isset($malfunction['service_level']) && $malfunction['service_level'] == 'Good') ? 'selected' : ''); ?>><?php echo e(__('Good')); ?></option>
                                                    <option value="Bad" <?php echo e((isset($malfunction['service_level']) && $malfunction['service_level'] == 'Bad') ? 'selected' : ''); ?>><?php echo e(__('Bad')); ?></option>
                                                    <option value="N/A" <?php echo e((isset($malfunction['service_level']) && $malfunction['service_level'] == 'N/A') ? 'selected' : ''); ?>><?php echo e(__('N/A')); ?></option>
                                                </select>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-md-12 malfunction-scoring">
                                    <div><h4 class="malfunction-heading">1. <?php echo e(__('Scoring')); ?></h4></div>
                                    <div id="chartContainer"
                                         style="height:370px; width:90%; margin: 20px auto; direction:ltr;"></div>
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key_category => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <input type="hidden" name="data[categories][<?php echo e($key_category); ?>][id]"
                                               value="<?php echo e($category->id); ?>">
                                        <input type="hidden" name="data[categories][<?php echo e($key_category); ?>][name]"
                                               value="<?php echo e($category->name); ?>">
                                        <input type="hidden" name="data[categories][<?php echo e($key_category); ?>][score]"
                                               value="<?php echo e($category->score); ?>">
                                        <?php if(count($category->paragraphs)): ?>
                                            <input type="hidden" id="calc-input-category-<?php echo e($category->id); ?>"
                                                   name="data[calculate][<?php echo e($category->id); ?>][value]"
                                                   value="<?php echo e(isset($malfunction['calculate'][$category->id]['value']) ? $malfunction['calculate'][$category->id]['value'] : 'false'); ?>">
                                            <div><h5><?php echo e($key_category+1); ?>. <?php echo e($category->name); ?></h5></div>
                                            <table class="malfunction-scoring__table"
                                                   data-category-id="<?php echo e($category->id); ?>">
                                                <thead>
                                                <tr>
                                                    <td class="malfunction-scoring__paragraph"><?php echo e(__('#Paragraph')); ?></td>
                                                    <td class="malfunction-scoring__type"><?php echo e(__('Malfunction type')); ?></td>
                                                    <td class="malfunction-scoring__finding"><?php echo e(__('Finding')); ?></td>
                                                    <td class="malfunction-scoring__risk"><?php echo e(__('Risk + Repair')); ?></td>
                                                    <td class="malfunction-scoring__principal"><?php echo e(__('Principal malfunction')); ?></td>
                                                    
                                                    <td class="malfunction-scoring__score"><?php echo e(__('Scoring')); ?></td>
                                                </tr>
                                                </thead>

                                                <tbody class="malfunction-scoring__items">
                                                <?php $__currentLoopData = $category->paragraphs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key_paragraph => $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <input type="hidden"
                                                           id="calc-input-paragraph-<?php echo e($category->id); ?>-<?php echo e($paragraph->id); ?>"
                                                           name="data[calculate][<?php echo e($category->id); ?>][<?php echo e($paragraph->id); ?>]"
                                                           value="<?php echo e(isset($malfunction['calculate'][$category->id][$paragraph->id]) ? $malfunction['calculate'][$category->id][$paragraph->id] : 'false'); ?>">

                                                    <input type="hidden"
                                                           name="data[paragraphs][<?php echo e($category->id); ?>][<?php echo e($key_paragraph); ?>][id]"
                                                           value="<?php echo e($paragraph->id); ?>">
                                                    <input type="hidden"
                                                           name="data[paragraphs][<?php echo e($category->id); ?>][<?php echo e($key_paragraph); ?>][name]"
                                                           value="<?php echo e($paragraph->name); ?>">
                                                    <input type="hidden"
                                                           name="data[paragraphs][<?php echo e($category->id); ?>][<?php echo e($key_paragraph); ?>][score]"
                                                           value="<?php echo e($paragraph->score); ?>">

                                                    <tr class="malfunction-scoring__item"
                                                        data-category-name="<?php echo e($category->name); ?>"
                                                        data-category-id="<?php echo e($category->id); ?>"
                                                        data-id="<?php echo e($paragraph->id); ?>"
                                                        data-value="<?php echo e($paragraph->score); ?>"
                                                        data-type="<?php echo e($paragraph->type); ?>"
                                                        data-frr="<?php echo e(json_encode($paragraph->frr)); ?>">

                                                        <td class="malfunction-scoring__paragraph">
                                                            <div style="display: flex; justify-content: flex-start">
                                                                <div class="malfunction-scoring__paragraph-number"><?php echo e($key_category+1); ?>.<?php echo e($key_paragraph + 1); ?></div>
                                                                &nbsp;
                                                                <div class="malfunction-scoring__paragraph-name"><?php echo e($paragraph->name); ?></div>
                                                            </div>
                                                        </td>

                                                        <td class="malfunction-scoring__type"
                                                            onchange="updateButtonColor()">
                                                            <input id="paragraph-type-hidden-input" type="hidden"
                                                                   value="<?php echo e(\App\Models\Paragraph::find($paragraph->id)->type); ?>">
                                                            <select name="data[malfunction_type][<?php echo e($category->id); ?>][<?php echo e($paragraph->id); ?>]"
                                                                    id="change-risk-select"
                                                                    class="malfunction-type-select">
                                                                <option
                                                                        <?php echo e(isset($malfunction['malfunction_type'][$category->id][$paragraph->id]) && $malfunction['malfunction_type'][$category->id][$paragraph->id] == 'F' ? 'selected' : ''); ?> value="F">
                                                                    <?php echo e(__('TYPE_F')); ?>

                                                                </option>
                                                                <option
                                                                        <?php echo e(isset($malfunction['malfunction_type'][$category->id][$paragraph->id]) && $malfunction['malfunction_type'][$category->id][$paragraph->id] == 'S' ? 'selected' : ''); ?> value="S">
                                                                    <?php echo e(__('TYPE_S')); ?>

                                                                </option>
                                                                
                                                                <option
                                                                        <?php echo e(isset($malfunction['malfunction_type'][$category->id][$paragraph->id]) && $malfunction['malfunction_type'][$category->id][$paragraph->id] == 'B' ? 'selected' : ''); ?> value="B">
                                                                    <?php echo e(__('TYPE_B')); ?>

                                                                </option>
                                                                <option
                                                                        <?php echo e(isset($malfunction['malfunction_type'][$category->id][$paragraph->id]) && $malfunction['malfunction_type'][$category->id][$paragraph->id] == 'N' ? 'selected' : ''); ?> value="N">
                                                                    <?php echo e(__('TYPE_N')); ?>

                                                                </option>
                                                                <option
                                                                        <?php echo e(isset($malfunction['malfunction_type'][$category->id][$paragraph->id]) && $malfunction['malfunction_type'][$category->id][$paragraph->id] == 'C' ? 'selected' : ''); ?> value="C">
                                                                    <?php echo e(__('TYPE_C')); ?>

                                                                </option>
                                                            </select>
                                                        </td>

                                                        <td class="malfunction-scoring__finding"
                                                            onchange="updateButtonColor()">
                                                            <select style="display: none;"
                                                                    multiple
                                                                    data-placeholder="בחר ממצא"
                                                                    summernote-textarea
                                                                    name="data[malfunction_finding][<?php echo e($category->id); ?>][<?php echo e($paragraph->id); ?>][]"
                                                                    class="malfunction-finding-select"
                                                                    <?php echo e(isset($malfunction['malfunction_type'][$category->id][$paragraph->id]) && in_array($malfunction['malfunction_type'][$category->id][$paragraph->id], ['S', 'B']) ? '' : 'disabled'); ?>>
                                                                <?php $__currentLoopData = $paragraph->frr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $frr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <option value="<?php echo e($frr->id); ?>" <?php echo e(isset($malfunction['malfunction_finding'][$category->id][$paragraph->id])
                                                        && in_array($frr->id, $malfunction['malfunction_finding'][$category->id][$paragraph->id])
                                                        ? 'selected'
                                                        : ''); ?>><?php echo e($frr->finding); ?></option>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </select>
                                                        </td>
                                                        <td class="malfunction-scoring__risk <?php echo e(!isset($malfunction['malfunction_finding'][$category->id][$paragraph->id]) ? 'hasnt_active' : ''); ?>">
                                                            <?php $__currentLoopData = $paragraph->frr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $frr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                
                                                                <textarea style="display: none; float: right;"
                                                                          spellcheck="true"
                                                                          name="data[malfunction_rr][<?php echo e($category->id); ?>][<?php echo e($paragraph->id); ?>][<?php echo e($frr->id); ?>]"
                                                                          class="summernote-textarea summernote-paragraph-<?php echo e($paragraph->id); ?> <?php echo e(isset($malfunction['malfunction_finding'][$category->id][$paragraph->id])
                                                                        && in_array($frr->id, $malfunction['malfunction_finding'][$category->id][$paragraph->id])
                                                                        ? ''
                                                                        : 'disabled'); ?>"
                                                                          id="summernote-paragraph-<?php echo e($paragraph->id); ?>-<?php echo e($frr->id); ?>">
                                                                <?php if(isset($malfunction['malfunction_rr'][$category->id][$paragraph->id][$frr->id])): ?>
                                                                        <?php echo e($malfunction['malfunction_rr'][$category->id][$paragraph->id][$frr->id]); ?>

                                                                    <?php else: ?>
                                                                        <p><?php echo e($frr->finding); ?> <?php echo e($frr->risk); ?> <?php echo e($frr->repair); ?></p>
                                                                    <?php endif; ?>
                                                            </textarea>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </td>

                                                        <td class="malfunction-scoring__principal <?php echo e(!isset($malfunction['malfunction_finding'][$category->id][$paragraph->id]) ? 'hasnt_active' : ''); ?>">
                                                            <div style="display: inline-block;">
                                                                <?php $__currentLoopData = $paragraph->frr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $frr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <input
                                                                            <?php echo e((isset($malfunction['malfunction_finding'][$category->id][$paragraph->id])
                                                                               && in_array($frr->id, $malfunction['malfunction_finding'][$category->id][$paragraph->id]))
                                                                               &&
                                                                               (isset($malfunction['malfunction_type'][$category->id][$paragraph->id])
                                                                            && in_array($malfunction['malfunction_type'][$category->id][$paragraph->id], ['S', 'B']))
                                                                               ? ''
                                                                               : 'disabled'); ?>

                                                                            name="data[malfunction_principal][<?php echo e($category->id); ?>][<?php echo e($paragraph->id); ?>][<?php echo e($frr->id); ?>]"
                                                                            type="hidden"
                                                                            value="off"
                                                                    />
                                                                    <input <?php echo e(isset($malfunction['malfunction_principal'][$category->id][$paragraph->id][$frr->id])
                                                                    && $malfunction['malfunction_principal'][$category->id][$paragraph->id][$frr->id] == 'on'
                                                                    ? 'checked'
                                                                    : ''); ?>

                                                                           <?php echo e((isset($malfunction['malfunction_finding'][$category->id][$paragraph->id])
                                                                               && in_array($frr->id, $malfunction['malfunction_finding'][$category->id][$paragraph->id]))
                                                                               &&
                                                                               (isset($malfunction['malfunction_type'][$category->id][$paragraph->id])
                                                                            && in_array($malfunction['malfunction_type'][$category->id][$paragraph->id], ['S', 'B']))
                                                                               ? ''
                                                                               : 'disabled'); ?>

                                                                           name="data[malfunction_principal][<?php echo e($category->id); ?>][<?php echo e($paragraph->id); ?>][<?php echo e($frr->id); ?>]"
                                                                           class="malfunction-scoring__principal-checkbox"
                                                                           type="checkbox">
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </div>
                                                        </td>
                                                        <td class="malfunction-scoring__score">
                                                            <?php if(isset($malfunction['calculate'][$category->id][$paragraph->id]) && strpos($malfunction['calculate'][$category->id][$paragraph->id], '%') !== false): ?>
                                                                <?php echo e($malfunction['calculate'][$category->id][$paragraph->id]); ?>

                                                            <?php else: ?>
                                                                0%
                                                            <?php endif; ?>

                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td colspan="7"
                                                        style="<?php echo e(app()->getLocale() == 'he'? 'text-align: left;' : 'text-align: right;'); ?>">
                                                        <b><?php echo e(__('Category score')); ?>:</b>
                                                        <span id="category-score-<?php echo e($category->id); ?>"
                                                              class="category-id"
                                                              data-category-value="<?php echo e($category->score/100); ?>">
                                                        <?php if(isset($malfunction['calculate'][$category->id]['value']) && strpos($malfunction['calculate'][$category->id]['value'], '%') !== false): ?>
                                                                <?php echo e((array_sum(array_except($malfunction['calculate'][$category->id], 'value'))) ."%"); ?>

                                                        <?php else: ?>
                                                                ---
                                                        <?php endif; ?>
                                                    </span>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>

                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    <div class="malfunction-scoring__total-score">
                                        <div class="malfunction-scoring__total-info">
                                            <b><?php echo e(__('Total score')); ?></b>
                                            <span style="width: auto; <?php echo e(app()->getLocale() == 'he'? 'text-align: left;' : 'text-align: right;'); ?>" class="fork-number" fork="<?php echo e($fork); ?>">
                                        <?php if(isset($malfunction['calculate']['total'])): ?>
                                                    <?php echo e($malfunction['calculate']['total']); ?>

                                                <?php else: ?>
                                                    ---
                                                <?php endif; ?>
                                    </span>
                                        </div>
                                        <div style="<?php echo e(app()->getLocale() == 'he'? 'text-align: left;' : 'text-align: right;'); ?>">
                                            <p id="malfunction-calc-error-msg" style="margin-bottom: 0;
                                        color: red;"></p>
                                            <a href="" class="malfunction-scoring__calculate"
                                               id="calculate"><?php echo e(__('Calculate')); ?></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 malfunction-general malfunction-other hidden">
                                    <div><h4 class="malfunction-heading">2. <?php echo e(__('General')); ?></h4></div>
                                    <textarea name="data[malfunction-general]" id="malfunction-general-textarea"
                                              class="summernote-textarea" style="display: none" spellcheck="true">
                                    <?php if(isset($malfunction['malfunction-general'])): ?>
                                            <?php echo e($malfunction['malfunction-general']); ?>

                                        <?php endif; ?>
                                </textarea>
                                </div>
                                <div class="col-md-12 malfunction-repaired malfunction-other hidden">
                                    <div><h4 class="malfunction-heading">3. <?php echo e(__('Repaired malfunctions')); ?></h4></div>
                                    <textarea name="data[malfunction-repaired]" id="malfunction-repaired-textarea"
                                          class="summernote-textarea" style="display: none" spellcheck="true">
                                        <?php if(isset($malfunction['malfunction-repaired'])): ?>
                                            <?php echo e($malfunction['malfunction-repaired']); ?>

                                        <?php endif; ?>
                                    </textarea>
                                </div>
                                <div class="col-md-12 malfunction-principal malfunction-other hidden">
                                    <div><h4 class="malfunction-heading">4. <?php echo e(__('Principal malfunctions')); ?></h4></div>
                                    <div id="malfunction-principal__items">
                                        <?php if(isset($malfunction['malfunction_principal_detail'])): ?>
                                            <?php $__currentLoopData = $malfunction['malfunction_principal_detail']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $principal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(isset($malfunction['malfunction_principal'][$principal["category_id"]][$principal['id']]) &&
                                                 str_contains(implode(',', $malfunction['malfunction_principal'][$principal["category_id"]][$principal['id']]), 'on')): ?>
                                                    <div class="malfunction-principal__item"
                                                         data-category-id="<?php echo e($principal["category_id"]); ?>"
                                                         data-id="<?php echo e($principal["id"]); ?>">
                                                        <div class="malfunction-sort"></div>
                                                        <textarea name="data[malfunction_principal_detail][<?php echo e($key); ?>][value]"
                                                                class="summernote-textarea" style="display: none" spellcheck="true">
                                                            <?php echo e(str_replace(',', '', $principal["value"])); ?>

                                                        </textarea>

                                                        <input type="hidden"
                                                               name="data[malfunction_principal_detail][<?php echo e($key); ?>][category_id]"
                                                               value="<?php echo e($principal["category_id"]); ?>"/>
                                                        <input type="hidden"
                                                               name="data[malfunction_principal_detail][<?php echo e($key); ?>][id]"
                                                               value="<?php echo e($principal["id"]); ?>"/>

                                                        <div class="malfunction-scoring__photos <?php echo e(isset($malfunction['malfunction_type'][$principal["category_id"]][$principal["id"]]) && in_array($malfunction['malfunction_type'][$principal["category_id"]][$principal["id"]], ['S', 'B']) ? '' : 'disabled'); ?>">
                                                            <?php if(isset($malfunction['photo'][$principal["category_id"]][$principal["id"]])): ?>
                                                                <input type="hidden"
                                                                       name="data[photo][<?php echo e($principal["category_id"]); ?>][<?php echo e($principal["id"]); ?>][]"
                                                                       value=""/>
                                                                <?php $__currentLoopData = $malfunction['photo'][$principal["category_id"]][$principal["id"]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php
                                                                        $name = explode('.', $photo);
                                                                        $extention = $name[count($name) - 1];
                                                                        $extention = strtolower($extention);
                                                                        $is_image = in_array($extention, ["jpg", "jpeg", "bmp", "gif", "png"]);
                                                                        $path = url('/').($is_image ? '/images/' : '/uploads/');
                                                                    ?>

                                                                    <div class="malfunction-principal__photo-item">
                                                                        <a href="<?php echo e($path.$photo); ?>" target="_blank">
                                                                            <?php if($is_image): ?>
                                                                                <img src="<?php echo e($path.$photo); ?> "
                                                                                     class="malfunction-principal__photo-item"
                                                                                     alt="<?php echo e($photo); ?>"/>
                                                                            <?php else: ?>
                                                                                <?php echo e(strlen($photo) > 20 ? mb_substr($photo, 0, 20, 'utf-8') : $photo); ?>

                                                                            <?php endif; ?>
                                                                        </a>
                                                                        <i class="malfunction-principal__photo-remove"></i>
                                                                        <input type="hidden"
                                                                               name="data[photo][<?php echo e($principal["category_id"]); ?>][<?php echo e($principal["id"]); ?>][]"
                                                                               value="<?php echo e($photo); ?>"/>
                                                                    </div>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            <?php endif; ?>
                                                            <div class='upload-photo-wrapper'>
                                                                <label class="malfunction-scoring__add-photo"
                                                                       for="add-image-<?php echo e($principal["category_id"]); ?>-<?php echo e($principal["id"]); ?>">
                                                                    <i class="fa fa-upload"></i>
                                                                    <input id="add-image-<?php echo e($principal["category_id"]); ?>-<?php echo e($principal["id"]); ?>"
                                                                           class="malfunction-upload-image"
                                                                           type="file" <?php echo e(isset($malfunction['malfunction_type'][$principal["category_id"]][$principal["id"]]) && in_array($malfunction['malfunction_type'][$principal["category_id"]][$principal["id"]], ['S', 'B']) ? '' : 'disabled'); ?>>
                                                                </label>
                                                            </div>
                                                            <div class='cb'></div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-12 malfunction-repaired malfunction-other hidden">
                                    <div>
                                        <?php $checked = (isset($malfunction["malfunction-culinary"]) && isset($malfunction["malfunction-culinary"]["checked"])) ? $malfunction["malfunction-culinary"]["checked"] : false; ?>

                                        <h4 class="malfunction-heading">
                                            <input type="checkbox" name="data[malfunction-culinary][checked]"
                                                   <?php echo e($checked ? "checked" : ""); ?> id="malfunction-culinary-check"/>
                                            5. <span
                                                    class="<?php echo e($checked ? "" : "hidden"); ?> malfunction-culinary-heading"> <?php echo e(__('Culinary')); ?> </span>
                                        </h4>
                                    </div>
                                    <textarea name="data[malfunction-culinary][text]" id="malfunction-culinary-textarea"
                                          class="<?php echo e($checked ?  "summernote-textarea" : "hidden"); ?>" spellcheck="true">
                                        <?php if(isset($malfunction["malfunction-culinary"]) && isset($malfunction["malfunction-culinary"]["text"])): ?>
                                            <?php echo e($malfunction["malfunction-culinary"]["text"]); ?>

                                        <?php else: ?>
                                            <p>5.1 <?php echo e(__('General')); ?></p>
                                            <p>5.2 <?php echo e(__('Main dish')); ?></p>
                                            <p>5.3 <?php echo e(__('Toppings')); ?></p>
                                            <p>5.4 <?php echo e(__('Soups')); ?></p>
                                            <p>5.5 <?php echo e(__('Salads')); ?></p>
                                            <p>5.6 <?php echo e(__('Deserts')); ?></p>
                                        <?php endif; ?>
                                    </textarea>
                                </div>


                                <div class="col-md-12 malfunction-list malfunction-other hidden">
                                    <div><h4 class="malfunction-heading">6. <?php echo e(__('Malfunction list')); ?></h4></div>
                                    <textarea name="data[malfunction-list]" id="malfunction-list-textarea"
                                            class="summernote-textarea" style="display: none" spellcheck="true">
                                        <?php if(isset($malfunction['malfunction-list'])): ?>
                                            <?php echo e($malfunction['malfunction-list']); ?>

                                        <?php endif; ?>
                                     </textarea>
                                </div>
                                <div class="col-md-12 malfunction-summary malfunction-other hidden">
                                    <div><h4 class="malfunction-heading">7. <?php echo e(__('Summary & Recommendations')); ?></h4>
                                    </div>
                                    <textarea name="data[malfunction-summary]" id="malfunction-summary-textarea"
                                          class="summernote-textarea" style="display: none" spellcheck="true">
                                        <?php if(isset($malfunction['malfunction-summary'])): ?>
                                            <?php echo e($malfunction['malfunction-summary']); ?>

                                        <?php endif; ?>
                                    </textarea>
                                </div>

                                <div class="col-md-12 malfunction-uploaded-documents malfunction-other hidden">
                                    <div><h4 class="malfunction-heading">8. <?php echo e(__('Uploaded documents')); ?></h4></div>
                                    <div id="malfunction-uploaded-documents">
                                        <?php if(isset($malfunction['malfunction-uploads'])): ?>
                                            <input type="hidden" name="data[malfunction-uploads][]" value=""/>
                                            <?php $__currentLoopData = $malfunction['malfunction-uploads']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $upload): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="malfunction-uploads__item">
                                                    <a href="<?php echo e(url('/')); ?>/uploads/<?php echo e($upload); ?>" target="_blank"
                                                       class="malfunction-uploads__text">
                                                        <?php if(isImage($upload)): ?>
                                                            <img class="malfunction-principal__photo-item"
                                                                 src="<?php echo e(url('/')); ?>/uploads/<?php echo e($upload); ?>"
                                                                 alt="<?php echo e($upload); ?>"/>
                                                        <?php else: ?>
                                                            <?php echo e(strlen($upload) > 20 ? mb_substr($upload, 0, 20, 'utf-8') : $upload); ?>

                                                        <?php endif; ?>
                                                    </a>
                                                    <i class="malfunction-uploads__item-remove"></i>
                                                    <input type="hidden" name="data[malfunction-uploads][]"
                                                           value="<?php echo e($upload); ?>"/>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class='cb'></div>
                                <div style='display: flex; justify-content: center; align-items: center; margin-top: 10px;'>
                                    <a href="javascript:void(0);" id="send_to_admin"
                                       class="btn-blue"><?php echo e(__('Send to admin')); ?></a>
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
        <?php echo $__env->make('frontend._part._modals', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>

    <div class='subview-container'>
        <?php echo $__env->make('frontend._part._signs_forms', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/datepicker.css')); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/summernote-list-styles.css">
    <link rel="stylesheet" href="<?php echo e(url('/')); ?>/js/frontend/uploadiFive/uploadifive.css">
    <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/multiselect.css">
    <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/malfunctions/main.css">
    <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/malfunctions/edit.css">

    <?php if(app()->getLocale() == 'he'): ?>
        <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/malfunctions/main_rtl.css">
    <?php endif; ?>

    <script src="<?php echo e(url('/')); ?>/js/multiselect.js"></script>
    <script src="<?php echo e(asset('js/bootstrap-datepicker.js')); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.22.2/moment.js"></script>
    <script src="<?php echo e(asset('js/datetime.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jquery.form.js')); ?>"></script>
    <script src="<?php echo e(url('/')); ?>/js/canvasjs.min.js"></script>
    <script src="<?php echo e(url('/')); ?>/js/pdf/jspdf.min.js"></script>
    <script src="<?php echo e(url('/')); ?>/js/pdf/html2canvas.min.js"></script>
    <script src="<?php echo e(url('/')); ?>/js/pdf/html2pdf.js"></script>
    <script src="<?php echo e(asset('js/htmldiff.js')); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.js"></script>
    <script src="<?php echo e(url('/')); ?>/js/summernote-list-styles.js"></script>
    <script src="<?php echo e(asset('js/malfunctions/edit.js')); ?>"></script>

    <?php if(app()->getLocale() == 'he'): ?>
        <script src="<?php echo e(url('/')); ?>/js/i18n/bootstrap-datepicker.he.js" charset="UTF-8"></script>
    <?php endif; ?>

    <script type="text/javascript">
        var user = <?php echo $user ?>,
            FILTER_COMPANY_URL = '<?php echo e(action('MalfunctionController@filterCompany')); ?>',
            FILTER_SITE_URL = '<?php echo e(action('MalfunctionController@filterSite')); ?>',
            FILTER_SUBSITE_URL = '<?php echo e(action('MalfunctionController@filterSubsite')); ?>',
            FORM_LIST_URL = '<?php echo e(action('MalfunctionController@index')); ?>',
            UPDATE_URL = '<?php echo e(action('MalfunctionController@update', $malfunction_id)); ?>',
            FIND_URL = '<?php echo e(action('MalfunctionController@find')); ?>',
            UPLOAD_FILE_URL = '<?php echo e(url('/')); ?>/upload-file',
            UPLOAD_FILES_URL = '<?php echo e(url('/')); ?>/upload-files',
            GET_STATISTICS_URL = "<?php echo e(route('get-statistics')); ?>",
            SEND_PDF_URL = "<?php echo e(route('malfunctionSharePdf')); ?>",
            DUPLICATE_MALFUCNTION_URL = "<?php echo e(route('duplicateMalfunction')); ?>",
            TOKEN = "<?php echo e(csrf_token()); ?>",
            BASE_URL = "<?php echo e(url('/')); ?>";
        LEVEL_URL = "<?php echo e(action('MalfunctionController@level')); ?>",
            IS_SUBMITTED = <?php echo e(isset($malfunction['calculate']['total']) ? 'true' : 'false'); ?>,
            MALFUNCTION_ID = "<?php echo e($malfunction_id); ?>",
            FORK = <?php echo e($fork); ?>,
            LOCALE = "<?php echo e(app()->getLocale()); ?>",
            categories = <?php echo $categories ?>,
            lastMalfunctionType = JSON.parse('<?php echo $lastMalfunctionType; ?>'),
            lastMalfunctionFinding = <?php echo $lastMalfunctionFinding ?>,
            initialMalfunctionType = JSON.parse('<?php echo json_encode((isset($malfunction["malfunction_type"]) && (is_array($malfunction["malfunction_type"])) ? $malfunction["malfunction_type"] : [])); ?>');

        var SF_TOKEN = "<?php echo e(csrf_token()); ?>",
            SF_TIMESTAMP = "<? echo time();?>",
            SF_CURRENT_URL = "<?php echo e($_SERVER['REQUEST_URI']); ?>",
            SF_UPLOAD_ITEM_URL = "<?php echo e(url("/")); ?>/nik/upload",
            SF_RESORTING_URL = "<?php echo e(url('/')); ?>/nik/newsort",
            SF_UPDATE_CONTENT_URL = "<?php echo e(url('/')); ?>/nik/newArea",
            SF_DELET_ITEM_URL = "<?php echo e(url('/')); ?>/nik/delete",
            SF_SEND_EMAIL_URL = "<?php echo e(url('/')); ?>/nik/sendmail",
            SF_UPLOAD_FILES_URL = '<?php echo e(url('/')); ?>/upload-sf-files';

        var sortTextArray = {
            "date_desc": "<?php echo e(__('Upload date DESC')); ?>",
            "date_asc": "<?php echo e(__('Upload date ASC')); ?>",
            "type_asc": "<?php echo e(__('Type A-Z')); ?>",
            "type_desc": "<?php echo e(__('Type Z-A')); ?>",
            "name_asc": "<?php echo e(__('Name A-Z')); ?>",
            "name_desc": "<?php echo e(__('Name Z-A')); ?>"
        }

        var Lang = {
            "Are you sure?(send to admin)": "<?php echo e(__('Are you sure?(send to admin)')); ?>",
            "Yes": "<?php echo e(__('Yes')); ?>",
            "Cancel(no)": "<?php echo e(__('Cancel(no)')); ?>",
            "Message sent successfully": "<?php echo e(__('Message sent successfully')); ?>",
            "Email was not sent": "<?php echo e(__('Email was not sent')); ?>",
            "Sub-site": "<?php echo e(__('Sub-site')); ?>",
            "Score": "<?php echo e(__('CP Score')); ?>",
            "Date": "<?php echo e(__('Date')); ?>",
            "Print": "<?php echo e(__('Print')); ?>",
            "SelectAll": "<?php echo e(__('Select all')); ?>",
            "AllSelected": "<?php echo e(__('All selected')); ?>",
            "NoMatchesFound": "<?php echo e(__('No matches found')); ?>",
            "SomeFieldsAreMissiing": "<?php echo e(__('Some fields are missing')); ?>",
            "Low": "<?php echo e(__('Low')); ?>", "Medium": "<?php echo e(__('Medium')); ?>", "High": "<?php echo e(__('High')); ?>",
            "Repeating": "<?php echo e(__('Repeating malfunction')); ?>",
            "Inspection_made": "<?php echo __('On dd/mm/yy an inspection was made', ['date' => '<span class=\'report-date\'></span>']); ?>",
            "Inspection_done": "<?php echo __('The inspection done together with:', ['rep' => '<span class=\'report-rep\'></span>']); ?>",
            "TwoPointThreeValue": "<?php echo __('Two Point three value:', ['rep' => '<span class=\'report-rep\'></span>']); ?>"
        }

        $('select[multiple]').multipleSelect({
            selectAllText: Lang['SelectAll'],
            allSelected: Lang['AllSelected'],
            noMatchesFound: Lang['NoMatchesFound']
        });

        <?php if(Session::has('sort')): ?>
        $(document).ready(function () {
            $('.item.sortBlock').find('span').text(sortTextArray["<?php echo e(Session::get('sort')); ?>"])
        });
        <?php endif; ?>

        <?php if(Session::has('success')): ?>
        $(document).ready(function () {
            alert('<?php echo e(Session::get("success")); ?>');
        });
        <?php endif; ?>
    </script>

    <script src="<?php echo e(url('/')); ?>/js/frontend/uploadiFive/jquery.uploadifive.min.js"></script>
    <script src="<?php echo e(url('/')); ?>/js/frontend/signs-forms.js"></script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>