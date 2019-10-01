<?php $__env->startSection('content'); ?>
<?php
    $user_temp = json_decode($user);
    $user_id = $user_temp->id;
    $user_role = strtolower($user_temp->title);
    $user_name = $user_temp->name; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card guidances <?php echo e($user_role); ?>" id="pdf-container">
                <?php if(session('status')): ?>
                    <div class="card-body">
                        <div class="alert alert-success" role="alert">
                            <?php echo e(session('status')); ?>

                        </div>
                    </div>
                <?php endif; ?>

                <div class="card-header">
                    <?php if((!isset($guidance->data['status']) || !isset($guidance->data['status']['stage']) || $guidance->data['status']['stage'] != 'publish') && ($user_role == 'admin' || $user_role == 'employee')): ?>
                        <div class="btn-group <?php echo e(app()->getLocale() == 'he' ? 'right' : 'left'); ?>">
                            <a class='btn-action' onclick="deleteGuidance();" style="color:black;" href="javascript:void(0);">
                                <i class="fa fa-trash"></i>
                            </a>
                            <a class='btn-action' style="color:black;" href="<?php echo e(action('MalfunctionController@editGuidance', $guidance->id)); ?>">
                                <i class="fa fa-edit"></i>
                            </a>
                        </div>
                    <?php endif; ?>

                    <h3 class="text-center text-primary">
                        <?php echo e(__('Food risk report(guidance)')); ?>

                    </h3>
                    <h5 class="text-center">#<?php echo e($guidance->nameCode); ?></h5>

                    <div class="btn-group <?php echo e(app()->getLocale() == 'he' ? 'left' : 'right'); ?>">
                        <?php if($user_role == 'admin' || $user_role == 'employee'): ?>
                            <a href="javascript:void(0);" class="btn-action" id="duplicate_guidance" title="duplicate guidance"><img src="<?php echo e(url('/')); ?>/img/frontend/action/copy.png" width=25 height=25></a>
                        <?php endif; ?>
                        <a href="javascript:void(0);" class="btn-action" id="download_pdf" title="download chart"><img src="<?php echo e(url('/')); ?>/img/frontend/action/download.png" width=25 height=25></a>
                        <a href="javascript:void(0);" class="btn-action" id="share_pdf" title="share pdf"><img src="<?php echo e(url('/')); ?>/img/frontend/action/share.png" width=25 height=25></a>
                        <a href="javascript:void(0);" class="btn-action" id="print_pdf" title="print pdf"><img src="<?php echo e(url('/')); ?>/img/frontend/action/print.png" width=25 height=25></a>
                    </div>
                </div>
                <h5 style="padding-right: 60px;"><?php echo e(__('To')); ?></h5>
                <div class="card-body row total-summary">
                    <div class="col-md-7">
                        <ul class="company">
                            <li><span><?php echo e((isset($guidance->data['site']) ? $guidance->data['site'] : '') . (isset($guidance->data['subsite']) ? (" / " . $guidance->data['subsite']) : '')); ?></span></li>
                            <li><span><?php echo e(__('Company representative')); ?>: <?php echo e(isset($guidance->data['company_representative']) ? $guidance->data['company_representative'] : ''); ?></span></li>
                        </ul>
                    </div>
                    <div class="col-md-5">
                        <ul class="employe">
                            <li><?php echo e(__('Employee name')); ?>: <span><?php echo e(isset($guidance->data['employee_name']) ? $guidance->data['employee_name'] : ""); ?></span></li>
                            <li><?php echo e(__('Admin name')); ?>: <span><?php echo e(isset($guidance->data['status']) && isset($guidance->data['status']['stage']) && $guidance->data['status']['stage'] == 'publish' ? $guidance->data['admin_name'] : ''); ?></span></li>
                            <li><?php echo e(__('Date')); ?>: <?php echo e(isset($guidance->data['date']) ? $guidance->data['date'] : '--/--/--'); ?></li>
                            <li><?php echo e(__('Time')); ?>: <?php echo e(isset($guidance->data['time_']) ? $guidance->data['time_'] : '--:--'); ?></li>
                            <li><?php echo e(__('Contract representative')); ?>: <?php echo e(isset($guidance->data['contractor_representative']) ? $guidance->data['contractor_representative'] : ''); ?></li>
                        </ul>
                    </div>
                </div>

                <div class="card-body details">
                    <div class="guidances-item">
                        <h5><b>1. <?php echo e(__('General')); ?></b></h5>
                        <?php echo isset( $guidance->data['guidance-general'] ) ?  $guidance->data['guidance-general'] : ""; ?>

                    </div>

                    <div class="guidances-item">
                        <h5><b>2. <?php echo e(__('Guidance themes')); ?></b></h5>
                        <?php echo isset( $guidance->data['guidance-themes'] ) ?  $guidance->data['guidance-themes'] : ""; ?>

                    </div>

                    <div class="guidances-item">
                        <h5><b>3. <?php echo e(__('Principal malfunctions')); ?></b></h5>
                        <?php if(isset($guidance->data['guidance_principal_detail'])): ?>
                            <ul class="guidances-principal">
                                <?php $__currentLoopData = $guidance->data['guidance_principal_detail']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $principal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="guidances-item" data-guidance-id="<?php echo $guidance_id ?>" data-principal-id="<?php echo $principal["id"] ?>"><?php echo isset($principal["value"]) ? $principal["value"] : ""; ?>

                                        <?php if(isset($principal["id"]) && isset($guidance->data['photo'][$principal["id"]])): ?>
                                            <div class="guidance-photos show">
                                                <?php $__currentLoopData = $guidance->data['photo'][$principal["id"]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                                                                <img src="<?php echo e($path.$photo); ?> " class="guidance-principal-photo-item" alt="<?php echo e($photo); ?>"/>
                                                            <?php else: ?>
                                                                <?php echo e(strlen($photo) > 20 ? mb_substr($photo, 0, 20, 'utf-8') : $photo); ?>

                                                            <?php endif; ?>
                                                        </a>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                            <div class="clear-both"></div>
                                        <?php endif; ?>
                                        <form class="comments" data-principal-id="<?php echo $principal["id"] ?>">
                                            <?php echo e(method_field('POST')); ?>

                                            <?php echo e(csrf_field()); ?>

                                            <?php if(isset($guidance->data['comments']) && isset($guidance->data['comments'][$principal["id"]])): ?>
                                                <?php $__currentLoopData = $guidance->data['comments'][$principal["id"]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="comment-item" data-comment-id="<?php echo $key ?>">
                                                        <input type="hidden" name="data[comments][<?php echo e($principal["id"]); ?>][<?php echo e($key); ?>][user_id]" value="<?php echo e($comment['user_id']); ?>">
                                                        <input type="hidden" name="data[comments][<?php echo e($principal["id"]); ?>][<?php echo e($key); ?>][user_role]" value="<?php echo e($comment['user_role']); ?>" readonly>
                                                        <input type="hidden" name="data[comments][<?php echo e($principal["id"]); ?>][<?php echo e($key); ?>][user_name]" value="<?php echo e($comment['user_name']); ?>" readonly>
                                                        <span class="user-name <?php echo strtolower($comment['user_role']) ?>"><?php echo e($comment['user_name']); ?></span>
                                                        <input class="comment-value" name="data[comments][<?php echo e($principal["id"]); ?>][<?php echo e($key); ?>][value]" value="<?php echo e($comment['value']); ?>" readonly>

                                                    <?php if($user_id == $comment['user_id']): ?>
                                                        <div class="dropdown">
                                                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">...
                                                            <span class="caret"></span></button>
                                                            <ul class="dropdown-menu">
                                                                <li><a href="javascript:void(0);" class="btn_edit" onclick="editComment('<?php echo $principal["id"] ?>', '<?php echo $key ?>')"><?php echo e(__('Edit')); ?></a></li>
                                                                <li><a href="javascript:void(0);" class="btn_delete" onclick="deleteComment('<?php echo $principal["id"] ?>', '<?php echo $key ?>')"><?php echo e(__('Delete')); ?></a></li>
                                                            </ul>
                                                        </div>
                                                    <?php endif; ?>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </form>
                                        <input class="comment-editor" data-principal-id="<?php echo $principal["id"] ?>"></input>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php endif; ?>
                    </div>

                    <div class="guidances-item">
                        <h5><b>4. <?php echo e(__('Summary & Recommendations')); ?></b></h5>
                        <?php echo $guidance->data['guidance-summary']; ?>

                    </div>

                    <div class="guidances-item">
                        <h5><b>5. <?php echo e(__('Uploaded documents')); ?></b></h5>
                        <?php if(isset($guidance->data['guidance-uploads'])): ?>
                            <?php $__currentLoopData = $guidance->data['guidance-uploads']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $upload): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="guidance-uploads-item">
                                    <a href="<?php echo e(url('/')); ?>/uploads/<?php echo e($upload); ?>" target="_blank" class="guidance-uploads-text">
                                        <?php if(isImage($upload)): ?>
                                            <img class="guidance-principal-photo-item" src="<?php echo e(url('/')); ?>/uploads/<?php echo e($upload); ?>" alt="<?php echo e($upload); ?>"/>
                                        <?php else: ?>
                                            <?php echo e(strlen($upload) > 20 ? mb_substr($upload, 0, 20, 'utf-8') : $upload); ?>

                                        <?php endif; ?>
                                    </a>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <div class="clear-both"></div>
                        <?php endif; ?>
                    </div>
                    <div class="guidances-item">
                        <h5><?php echo e(__('Sincerely')); ?>,</h5>
                        <h5><?php echo e(isset($guidance->data['employee_name']) ? $guidance->data['employee_name'] : ""); ?></h5>
                    </div>
                </div>
                <div id='printing-holder'></div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/malfunctions/main.css" media="all">
    <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/malfunctions/show.css" media="all">
    <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/malfunctions/print.css" media="print">

    <?php if(app()->getLocale() == 'he'): ?>
        <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/malfunctions/main_rtl.css" media="all">
    <?php endif; ?>

    <script src="<?php echo e(url('/')); ?>/js/canvasjs.min.js"></script>
    <script src="<?php echo e(url('/')); ?>/js/pdf/jspdf.min.js"></script>
    <script src="<?php echo e(url('/')); ?>/js/pdf/html2canvas.min.js"></script>
    <script src="<?php echo e(url('/')); ?>/js/pdf/html2pdf.js"></script>
    <script src="<?php echo e(asset('js/malfunctions/show-guidance.js')); ?>"></script>

    <script type="text/javascript">
        var GET_STATISTICS_URL = "<?php echo e(route('get-statistics')); ?>",
            SEND_PDF_URL = "<?php echo e(route('malfunctionSharePdf')); ?>",
            DUPLICATE_GUIDANCE_URL = "<?php echo e(route('duplicateGuidance')); ?>",
            SAVE_COMMENTS_URL = "<?php echo e(route('guidanceSaveComments', $guidance_id)); ?>",
            DELETE_GUIDANCE_URL = "<?php echo e(action('MalfunctionController@destroyGuidance', $guidance->id)); ?>",
            TOKEN = "<?php echo e(csrf_token()); ?>",
            BASE_URL = "<?php echo e(url('/')); ?>";
            REPORT_DATE = "<?php echo e(isset($guidance->data['date']) ? $guidance->data['date'] : ''); ?>",
            GUIDANCE_ID = <?php echo $guidance_id ?>,
            SITE = "<?php echo $guidance->data['site'] ?>",
            SUBSITE =  "<?php echo $guidance->data['subsite'] ?>",
            user = <?php echo $user ?>;

        var Lang = {
            "Edit" : "<?php echo e(__('Edit')); ?>",
            "Delete" : "<?php echo e(__('Delete')); ?>",
            "Are you sure you want to delete it?" : "<?php echo e(__('Are you sure you want to delete it?')); ?>",
            "Yes" : "<?php echo e(__('Yes')); ?>",
            "Cancel(no)" : "<?php echo e(__('Cancel(no)')); ?>",
            "Message sent successfully" : "<?php echo e(__('Message sent successfully')); ?>",
            "Email was not sent" : "<?php echo e(__('Email was not sent')); ?>",
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>