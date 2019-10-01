<?php $__env->startSection('content'); ?>
    <?php
    $user_temp = json_decode($user);
    $user_id = $user_temp->id;
    $user_role = strtolower($user_temp->title);
    $user_name = $user_temp->name; ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card guidances <?php echo e($user_role); ?>" >
                    <div class="card-header">
                        <div class="dropdown-wrapper">
                            <input type="file" id="file" name="uploads[]" multiple onchange="uploadFiles(this)" hidden>
                            <div class="dropdown">
                                <label class="dropdown-toggle" data-toggle="dropdown"><i
                                            class="fa fa-upload"></i></label>
                                <ul class="dropdown-menu">
                                    <li><a href="javascript:void(0)"
                                           id='upload_from_computer'><?php echo e(__('Upload from computer')); ?></a></li>
                                    <li><a href="javascript:void(0);"
                                           onclick="uploadFromSignsForms()"><?php echo e(__('Upload from Signs&forms')); ?></a></li>
                                </ul>
                            </div>
                        </div>
                        <h3 class="text-center text-primary"><?php echo e(__('Food risk report(guidance)')); ?></h3>
                        <h5 class="text-center">#<?php echo e($nameCode); ?></h5>

                    <!-- <div class="btn-group">
                        <a href="javascript:void(0);" class="btn-action" id="duplicate_guidance" title="duplicate guidance"><img src="<?php echo e(url('/')); ?>/img/frontend/action/copy.png" width=25 height=25></a>
                        <a href="javascript:void(0);" class="btn-action" id="download_pdf" title="download chart"><img src="<?php echo e(url('/')); ?>/img/frontend/action/download.png" width=25 height=25></a>
                        <a href="javascript:void(0);" class="btn-action" id="share_pdf" title="share pdf"><img src="<?php echo e(url('/')); ?>/img/frontend/action/share.png" width=25 height=25></a>
                        <a href="javascript:void(0);" class="btn-action" id="print_pdf" title="print pdf"><img src="<?php echo e(url('/')); ?>/img/frontend/action/print.png" width=25 height=25></a>
                    </div> -->
                    </div>
                    <div class="card-body">
                        <form id="guidance-form" style="width:100%">
                            <?php echo e(method_field('PUT')); ?>


                            <input type="hidden" name="data[site]"
                                   value="<?php echo e(isset($guidance['site']) ? $guidance['site'] : ''); ?>"/>
                            <input type="hidden" name="data[subsite]"
                                   value="<?php echo e(isset($guidance['subsite']) ? $guidance['subsite'] : ''); ?>"/>
                            <input type="hidden" name="data[date]"
                                   value="<?php echo e(isset($guidance['date']) ? $guidance['date'] : ''); ?>"/>
                            <input type="hidden" name="data[time_]"
                                   value="<?php echo e(isset($guidance['time_']) ? $guidance['time_'] : ''); ?>"/>
                            <input type="hidden" name="data[employee_id]"
                                   <?php if($user_role == 'admin' || $user_role == 'employee'): ?> value="<?php echo e($user_id); ?>"
                                   <?php else: ?> value="<?php echo e(isset($guidance['employee_id']) ? $guidance['employee_id'] : ''); ?>" <?php endif; ?>/>
                            <input type="hidden" name="data[employee_name]"
                                   <?php if(($user_role == 'admin' || $user_role == 'employee') && !isset($guidance['employee_name'])): ?> value="<?php echo e($user_name); ?>"
                                   <?php else: ?> value="<?php echo e(isset($guidance['employee_name']) ? $guidance['employee_name'] : ''); ?>" <?php endif; ?>/>
                            <input type="hidden" name="data[admin_name]"
                                   value="<?php echo e(isset($guidance['status']) && isset($guidance['status']['stage']) && $guidance['status']['stage'] == 'publish' ? $guidance['admin_name'] : ''); ?>"/>
                            <input type="hidden" name="data[status][stage]"
                                   <?php if(($user_role == 'admin' || $user_role == 'employee') && (!isset($guidance['status']) || !isset($guidance['status']['stage']) || $guidance['status']['stage'] == 'draft')): ?> value="draft"
                                   <?php else: ?> value="<?php echo e(isset($guidance['status']) && isset($guidance['status']['stage']) ? $guidance['status']['stage'] : ''); ?>" <?php endif; ?>/>
                            <input type="hidden" name="data[status][admin_changed_date]" value="unchanged"/>

                            <div class="row guidance-total-summary">
                                <div class="col-md-7">
                                    <?php $site_id = ""; ?>
                                    <select name="site_" class="ml-2" id="site">
                                        <option <?php echo e((isset($guidance['site']) && $guidance['site']) ? '' : 'selected'); ?> value="site"><?php echo e(__('Site')); ?></option>
                                        <?php $__currentLoopData = $sites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($site->id); ?>"
                                                    <?php if(isset($guidance['site']) && $guidance['site'] == $site->title): ?>
                                                    selected
                                            <?php $site_id = $site->id; ?>
                                                    <?php endif; ?>
                                            ><?php echo e($site->title); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <select <?php echo e((isset($guidance['site']) && $guidance['site']) ? '' : 'disabled'); ?> name="subsite_"
                                            id="subsite">
                                        <option <?php echo e((isset($guidance['site']) && $guidance['site']) ? '' : 'selected'); ?> value="subsite"><?php echo e(__('Sub-site')); ?></option>
                                        <?php $__currentLoopData = $subsites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subsite): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if((isset($guidance['site']) && $guidance['site'] && $subsite->site_id == $site_id)): ?>
                                                <option value="<?php echo e($subsite->id); ?>" <?php echo e((isset($guidance['subsite']) && $guidance['subsite'] == $subsite->title) ? 'selected' : ''); ?>><?php echo e($subsite->title); ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <div class="col-md-12 mt-4">
                                        <ul style="<?php echo e(App::getLocale() == 'he' ? 'padding-right: 10px !important' : 'padding-left: 10px !important'); ?>">
                                            <li>
                                                <label for="resp"><?php echo e(__('Company representative')); ?>: </label>
                                                <input type="text" id="resp" class="edit-field" readonly placeholder=""
                                                       size="15" name="data[company_representative]"
                                                       value="<?php echo e(isset($guidance['company_representative']) ? $guidance['company_representative'] : ''); ?>">
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <ul class="employe">
                                        <li><?php echo e(__('Employee name')); ?>:
                                            <span><?php if(($user_role == 'admin' || $user_role == 'employee') && !isset($guidance['employee_name'])): ?> <?php echo e($user_name); ?> <?php else: ?> <?php echo e(isset($guidance['employee_name']) ? $guidance['employee_name'] : ""); ?> <?php endif; ?></span>
                                        </li>
                                        <li><?php echo e(__('Admin name')); ?>:
                                            <span><?php echo e(isset($guidance['admin_name']) ? $guidance['admin_name'] : ""); ?></span>
                                        </li>
                                        <li><?php echo e(__('Date')); ?>: <input id="datepicker" class="edit-field"
                                                                   data-date-format="mm/dd/yyyy"
                                                                   value="<?php echo e(isset($guidance['date']) ? $guidance['date'] : ""); ?>">
                                        </li>
                                        <li><?php echo e(__('Time')); ?>: <input type="text" id="time" class="edit-field" size="4"
                                                                   maxlength="5"
                                                                   pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]"
                                                                   onchange="updateTime(this);"
                                                                   value="<?php echo e(isset($guidance['time_']) ? $guidance['time_'] : ""); ?>">
                                        </li>
                                        <li><?php echo e(__('Contract representative')); ?>: <input type="text" size="15"
                                                                                      class="contractor_representative edit-field"
                                                                                      name="data[contractor_representative]"
                                                                                      placeholder=""
                                                                                      value="<?php echo e(isset($guidance['contractor_representative']) ? $guidance['contractor_representative'] : ''); ?>"/>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-12 guidance-general">
                                <div><h4 class="guidance-heading">1. <?php echo e(__('General')); ?></h4></div>
                                <textarea name="data[guidance-general]" id="guidance-general-textarea"
                                          class="summernote-textarea" style="display: none">
                                <?php if(isset($guidance['guidance-general'])): ?>
                                        <?php echo e($guidance['guidance-general']); ?>

                                    <?php else: ?>
                                        <p>1. <?php echo e(__('On dd/mm/yy there was a guidance day')); ?></p>
                                        <p>2. <?php echo e(__('The guidance done together with:')); ?> </p>
                                        <p>3. <?php echo e(__('The length of demo day was:')); ?> </p>
                                    <?php endif; ?>
                            </textarea>
                            </div>
                            <div class="col-md-12 guidance-themes">
                                <div><h4 class="guidance-heading">2. <?php echo e(__('Guidance themes')); ?></h4></div>
                                <textarea name="data[guidance-themes]" id="guidance-themes-textarea"
                                          class="summernote-textarea" style="display: none">
                                <?php if(isset($guidance['guidance-themes'])): ?>
                                        <?php echo e($guidance['guidance-themes']); ?>

                                    <?php else: ?>
                                        <p><b>2.1 <?php echo e(__('Dividing the kitchen for working zones:')); ?></b></p>
                                        <p><b>2.2 <?php echo e(__('Main work process in the kitchen:')); ?></b></p>
                                        <p><b>2.3 <?php echo e(__('Product expiration labeling:')); ?></b></p>
                                        <p><b>2.4 <?php echo e(__('Commence of Inspections and documentation')); ?></b></p>
                                    <?php endif; ?>
                            </textarea>
                            </div>
                            <div class="col-md-12 guidance-principal">
                                <div><h4 class="guidance-heading">3. <?php echo e(__('Principal malfunctions')); ?></h4></div>
                                <div id="guidance-principal-items">
                                    <?php if(isset($guidance['guidance_principal_detail'])): ?>
                                        <?php $__currentLoopData = $guidance['guidance_principal_detail']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $principal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(isset($principal["id"]) && isset($principal["value"])): ?>
                                                <div class="guidance-principal-item" data-id="<?php echo e($principal["id"]); ?>">
                                                    <div class="guidance-sort"></div>
                                                    <textarea
                                                            name="data[guidance_principal_detail][<?php echo e($principal["id"]); ?>][value]"
                                                            class="summernote-textarea" style="display: none">
                                                <?php echo e($principal["value"]); ?>

                                            </textarea>

                                                    <input type="hidden"
                                                           name="data[guidance_principal_detail][<?php echo e($principal["id"]); ?>][id]"
                                                           value="<?php echo e($principal["id"]); ?>"/>

                                                    <div class="guidance-photos">
                                                        <?php if(isset($guidance['photo'][$principal["id"]])): ?>
                                                            <input type="hidden"
                                                                   name="data[photo][<?php echo e($principal["id"]); ?>][]" value=""/>
                                                            <?php $__currentLoopData = $guidance['photo'][$principal["id"]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <?php
                                                                    $name = explode('.', $photo);
                                                                    $extention = $name[count($name) - 1];
                                                                    $extention = strtolower($extention);
                                                                    $is_image = in_array($extention, ["jpg", "jpeg", "bmp", "gif", "png"]);
                                                                    $path = url('/').($is_image ? '/images/' : '/uploads/');
                                                                ?>

                                                                <div class="guidance-principal-photo-item">
                                                                    <a href="<?php echo e($path.$photo); ?>" target="_blank">
                                                                        <?php if($is_image): ?>
                                                                            <img src="<?php echo e($path.$photo); ?> "
                                                                                 class="guidance-principal-photo-item"
                                                                                 alt="<?php echo e($photo); ?>"/>
                                                                        <?php else: ?>
                                                                            <?php echo e(strlen($photo) > 20 ? mb_substr($photo, 0, 20, 'utf-8') : $photo); ?>

                                                                        <?php endif; ?>
                                                                    </a>
                                                                    <i class="guidance-principal-photo-remove"></i>
                                                                    <input type="hidden"
                                                                           name="data[photo][<?php echo e($principal["id"]); ?>][]"
                                                                           value="<?php echo e($photo); ?>"/>
                                                                </div>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php endif; ?>
                                                        <div class='upload-photo-wrapper'>
                                                            <label class="guidance-add-photo "
                                                                   for="add-image-<?php echo e($principal["id"]); ?>">
                                                                <i class="fa fa-upload" ></i>
                                                                <input id="add-image-<?php echo e($principal["id"]); ?>"
                                                                       class="guidance-upload-image" type="file">
                                                            </label>
                                                        </div>
                                                        <div class='cb'></div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </div>
                                <a href='javascript:void(0);' id='add-principal-guidance'
                                   class='btn-blue'><?php echo e(__('Add more')); ?></a>
                            </div>

                            <div class="col-md-12 guidance-summary">
                                <div><h4 class="guidance-heading">4. <?php echo e(__('Summary & Recommendations')); ?></h4></div>
                                <textarea name="data[guidance-summary]" id="guidance-summary-textarea"
                                          class="summernote-textarea" style="display: none">
                                <?php if(isset($guidance['guidance-summary'])): ?>
                                        <?php echo e($guidance['guidance-summary']); ?>

                                    <?php endif; ?>
                            </textarea>
                            </div>

                            <div class="col-md-12 guidance-uploaded-documents">
                                <div><h4 class="guidance-heading">5. <?php echo e(__('Uploaded documents')); ?></h4></div>
                                <div id="guidance-uploaded-documents">
                                    <?php if(isset($guidance['guidance-uploads'])): ?>
                                        <input type="hidden" name="data[guidance-uploads][]" value=""/>
                                        <?php $__currentLoopData = $guidance['guidance-uploads']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $upload): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="guidance-uploads-item">
                                                <a href="<?php echo e(url('/')); ?>/uploads/<?php echo e($upload); ?>" target="_blank"
                                                   class="guidance-uploads-text">
                                                    <?php if(isImage($upload)): ?>
                                                        <img class="guidance-principal-photo-item"
                                                             src="<?php echo e(url('/')); ?>/uploads/<?php echo e($upload); ?>"
                                                             alt="<?php echo e($upload); ?>"/>
                                                    <?php else: ?>
                                                        <?php echo e(strlen($upload) > 20 ? mb_substr($upload, 0, 20, 'utf-8') : $upload); ?>

                                                    <?php endif; ?>
                                                </a>
                                                <i class="guidance-uploads-item-remove"></i>
                                                <input type="hidden" name="data[guidance-uploads][]"
                                                       value="<?php echo e($upload); ?>"/>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </div>
                                <div class='cb'></div>
                            </div>
                            <div style='display:flex; justify-content:center; align-items:center; margin-top:20px; flex-direction:column;'>
                                <p id="guidance-error-msg" style="margin-bottom: 0px; color: red;"></p>
                                <a href="javascript:void(0);" id="send_to_admin"
                                   class="btn-blue"><?php echo e(__('Send to admin')); ?></a>
                            </div>
                        </form>
                    </div>
                </div>
                <div id='printing-holder'></div>
            </div>
        </div>

        <div class='subview-container'>
            <?php echo $__env->make('frontend._part._signs_forms', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('frontend._part._modals', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>

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
        <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/malfunctions/main_rtl.css" media="all">
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

    <?php if(app()->getLocale() == 'he'): ?>
        <script src="<?php echo e(url('/')); ?>/js/i18n/bootstrap-datepicker.he.js" charset="UTF-8"></script>
    <?php endif; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.js"></script>
    <script src="<?php echo e(url('/')); ?>/js/summernote-list-styles.js"></script>
    <script src="<?php echo e(asset('js/malfunctions/edit-guidance.js')); ?>"></script>

    <script type="text/javascript">
        var user = <?php echo $user ?>,
            FILTER_SITE_URL = '<?php echo e(action('MalfunctionController@filterSite')); ?>',
            FILTER_SUBSITE_URL = '<?php echo e(action('MalfunctionController@filterSubsite')); ?>',
            UPDATE_URL = '<?php echo e(action('MalfunctionController@updateGuidance', $guidance_id)); ?>',
            FORM_LIST_URL = '<?php echo e(action('MalfunctionController@index')); ?>',
            UPLOAD_FILE_URL = '<?php echo e(url('/')); ?>/upload-file',
            UPLOAD_FILES_URL = '<?php echo e(url('/')); ?>/upload-files',
            SEND_PDF_URL = "<?php echo e(route('malfunctionSharePdf')); ?>",
            DUPLICATE_GUIDANCE_URL = "<?php echo e(route('duplicateGuidance')); ?>",
            TOKEN = "<?php echo e(csrf_token()); ?>",
            BASE_URL = "<?php echo e(url('/')); ?>",
            LOCALE = "<?php echo e(app()->getLocale()); ?>",
            SITE = "<?php echo $guidance['site'] ?>",
            SUBSITE = "<?php echo $guidance['subsite'] ?>"
        GUIDANCE_ID = "<?php echo e($guidance_id); ?>";

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
            "date_desc": "Upload date DESC",
            "date_asc": "Upload date ASC",
            "type_asc": "Type A-Z",
            "type_desc": "Type Z-A",
            "name_asc": "Name A-Z",
            "name_desc": "Name Z-A"
        };

        var Lang = {
            "Are you sure?(send to admin)": "<?php echo e(__('Are you sure?(send to admin)')); ?>",
            "Yes": "<?php echo e(__('Yes')); ?>",
            "No": "<?php echo e(__('No')); ?>",
            "Message sent successfully": "<?php echo e(__('Message sent successfully')); ?>",
            "Email was not sent": "<?php echo e(__('Email was not sent')); ?>",
            "Sub-site": "<?php echo e(__('Sub-site')); ?>",
            "SomeFieldsAreMissiing": "<?php echo e(__('Some fields are missing')); ?>"
        }

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