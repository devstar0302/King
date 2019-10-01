<?php $__env->startSection('content'); ?>
<?php
    $user_temp = json_decode($user);
    $user_id = $user_temp->id;
    $user_role = strtolower($user_temp->title);
    $user_name = $user_temp->name; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card malfunctions <?php echo e($user_role); ?>" id="pdf-container">
                <?php if(session('status')): ?>
                    <div class="card-body">
                        <div class="alert alert-success" role="alert">
                            <?php echo e(session('status')); ?>

                        </div>
                    </div>
                <?php endif; ?>
                <div class="card-header">
                    <?php if((!isset($malfunction->data['status']) || !isset($malfunction->data['status']['stage']) || $malfunction->data['status']['stage'] != 'publish') && ($user_role == 'admin' || $user_role == 'employee')): ?>
                        <div class="btn-group <?php echo e(app()->getLocale() == 'he' ? 'right' : 'left'); ?>">
                            <a class='btn-action hide-in-pdf' onclick="deleteMalfunction();" style="color:black;" href="javascript:void(0);">
                                <i class="fa fa-trash"></i>
                            </a>
                            <a class='btn-action hide-in-pdf' style="color:black;" href="<?php echo e(action('MalfunctionController@edit', $malfunction->id)); ?>">
                                <i class="fa fa-edit"></i>
                            </a>
                        </div>
                    <?php endif; ?>

                    <h3 class="text-center text-primary">
                        <?php echo e(__('Food risk report(malfunction)')); ?>

                    </h3>
                    <h5 class="text-center">#<?php echo e($malfunction->nameCode); ?></h5>

                    <div class="btn-group <?php echo e(app()->getLocale() == 'he' ? 'left' : 'right'); ?>">
                        <?php if($user_role == 'admin' || $user_role == 'employee'): ?>
                            <a href="javascript:void(0);" class="btn-action hide-in-pdf" id="duplicate_malfunction" title="duplicate malfunction"><img src="<?php echo e(url('/')); ?>/img/frontend/action/copy.png" width=25 height=25></a>
                        <?php endif; ?>
                        <a href="javascript:void(0);" class="btn-action hide-in-pdf" id="download_pdf" title="download chart"><img src="<?php echo e(url('/')); ?>/img/frontend/action/download.png" width=25 height=25></a>
                        <a href="javascript:void(0);" class="btn-action hide-in-pdf" id="share_pdf" title="share pdf"><img src="<?php echo e(url('/')); ?>/img/frontend/action/share.png" width=25 height=25></a>
                        <a href="javascript:void(0);" class="btn-action hide-in-pdf" id="print_pdf" title="print pdf"><img src="<?php echo e(url('/')); ?>/img/frontend/action/print.png" width=25 height=25></a>
                        <div class="show-in-pdf" style="display: none;">
                            <img src="\img\logo.png" alt="">
                        </div>
                    </div>
                </div>
                <h5 style="padding-right: 60px;"><?php echo e(__('To')); ?></h5>
                <div class="card-body row total-summary">
                    <div class="col-md-4">
                        <ul class="company">
                            <li><span><?php echo e((isset($malfunction->data['site']) ? $malfunction->data['site'] : '') . (isset($malfunction->data['subsite']) ? (" / " . $malfunction->data['subsite']) : '')); ?></span></li>
                            <li><span><?php echo e(__('Company representative')); ?>: <?php echo e(isset($malfunction->data['company_representative']) ? $malfunction->data['company_representative'] : ''); ?></span></li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <ul class="employe">
                            <li><?php echo e(__('Employee name')); ?>: <span><?php echo e(isset($malfunction->data['employee_name']) ? $malfunction->data['employee_name'] : ""); ?></span></li>
                            <li><?php echo e(__('Admin name')); ?>: <span><?php echo e(isset($malfunction->data['status']) && isset($malfunction->data['status']['stage']) && $malfunction->data['status']['stage'] == 'publish' ? $malfunction->data['admin_name'] : ''); ?></span></li>
                            <li><?php echo e(__('Date')); ?>: <?php echo e(isset($malfunction->data['date']) ? $malfunction->data['date'] : '--/--/----'); ?></li>
                            <li><?php echo e(__('Time')); ?>: <?php echo e(isset($malfunction->data['time_']) ? $malfunction->data['time_'] : '--:--'); ?></li>
                            <li><?php echo e(__('Contract representative')); ?>: <?php echo e(isset($malfunction->data['contractor_representative']) ? $malfunction->data['contractor_representative'] : ''); ?></li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <ul class="totals">
                            <li><?php echo e(__('Total score')); ?>: <?php echo e(isset($malfunction->data['calculate']['total']) && strpos($malfunction->data['calculate']['total'], '%') !== false ? $malfunction->data['calculate']['total'] : "---"); ?> </li>
                            <li><?php echo e(__('Risk level')); ?>:
                                <?php if(isset($malfunction->data['risk_level']) && in_array($malfunction->data['risk_level'], [0,1,2])): ?>
                                    <?php if($malfunction->data['risk_level'] == 'Low'): ?> <?php echo e(__('Low')); ?>

                                    <?php elseif($malfunction->data['risk_level'] == 'Medium'): ?> <?php echo e(__('Medium')); ?>

                                    <?php elseif($malfunction->data['risk_level'] == 'High'): ?> <?php echo e(__('High')); ?>

                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php echo e('---'); ?>

                                <?php endif; ?>
                            </li>
                            <li><?php echo e(__('Gastronomy score')); ?>: <span id="gastronome"><?php echo e(isset($malfunction->data['gastronomy_score']) && strpos($malfunction->data['gastronomy_score'], '%') !== false ? $malfunction->data['gastronomy_score'] : '---'); ?></span></li>
                            <li><?php echo e(__('Service level')); ?>:
                                <?php if(isset($malfunction->data['service_level']) && in_array($malfunction->data['service_level'], [0,1,2,3])): ?>
                                    <?php if($malfunction->data['service_level'] == 'Very good'): ?> <?php echo e(__('Very good')); ?>

                                    <?php elseif($malfunction->data['service_level'] == 'Good'): ?> <?php echo e(__('Good')); ?>

                                    <?php elseif($malfunction->data['service_level'] == 'Bad'): ?> <?php echo e(__('Bad')); ?>

                                    <?php elseif($malfunction->data['service_level'] == 'N/A'): ?> <?php echo e(__('N/A')); ?>

                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php echo e('---'); ?>

                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card-body details">
                    <div class="malfunctions-item scoring">
                        <h5><b>1. <?php echo e(__('Scoring')); ?></b></h5>
                        <div id="chartContainer" style="height:370px; width:90%; margin: 20px auto; direction:ltr;"></div>
                        <table class="table table-hover" border="1">
                            <thead style="background-color:#0074D9;color:white;">
                                <tr>
                                    <td width="33.3%"><?php echo e(__('Category')); ?></td>
                                    <td width="33.3%"><?php echo e(__('Weight')); ?></td>
                                    <td width="33.4%"><?php echo e(__('CP Score')); ?></td>
                                </tr>
                            </thead>
                            <tbody style="background-color: #AAAAAA;">
                                <?php if(isset($malfunction->data['categories'])): ?>
                                    <?php $__currentLoopData = $malfunction->data['categories']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category_key => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="category">
                                            <td><b><?php echo e($category_key+1); ?>. <?php echo e($category['name']); ?></b></td>
                                            <td><b><?php echo e($category['score']); ?>%</b></td>
                                            <td><b><?php echo e(isset($malfunction->data['calculate'][$category['id']]['value']) && strpos($malfunction->data['calculate'][$category['id']]['value'], '%') !== false ? $malfunction->data['calculate'][$category['id']]['value'] : '0%'); ?></b></td>
                                        </tr>
                                        <tr id="category-paragraph-tr-<?php echo e($category['id']); ?>" style="display: none;">
                                            <td colspan="3" style="padding: 0px;">
                                                <table class="table table-striped" border="1" style="margin-bottom: 0px;">
                                                    <tr>
                                                        <td width="33.3%"><?php echo e(__('Paragraph')); ?></td>
                                                        <td width="33.3%"><?php echo e(__('F/S/B/N/C')); ?></td>
                                                        <td width="33.4%"><?php echo e(__('CP Score')); ?></td>
                                                    </tr>
                                                            <?php
                                                                $relatively = ['total' => 0, 'count' => 0];
                                                            ?>

                                                            <?php if(isset($malfunction->data['paragraphs']) && isset($malfunction->data['paragraphs'][$category['id']])): ?>
                                                                <?php $__currentLoopData = $malfunction->data['paragraphs'][$category['id']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph_key => $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                                            <tr>
                                                                <td><?php echo e(($category_key+1) .'.'. ($paragraph_key+1)); ?> <?php echo e($paragraph['name'] ?? \App\Models\Paragraph::find($paragraph['id'])->name); ?></td>
                                                                <td style="padding: 0px; text-align: center;">
                                                                    <table class="table" style="background: transparent; margin-bottom: 0px;">
                                                                        <tr style="background: transparent;">
                                                                            <?php $__currentLoopData = ['F', 'S', 'B', 'N', 'C']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                <td style="border-top: 0px; <?php if(!$loop->last): ?> <?php echo e(App::getLocale() == 'he' ? 'border-left: 1px solid grey;' : 'border-right: 1px solid grey;'); ?> <?php endif; ?>" width="20%">
                                                                                    <?php if(isset($malfunction->data['malfunction_type'][$category['id']][$paragraph['id']]) && $malfunction->data['malfunction_type'][$category['id']][$paragraph['id']] == $type): ?>
                                                                                        X
                                                                                    <?php endif; ?>
                                                                                </td>
                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                                <td style="padding: 0px;">
                                                                    <table class="table" style="background: transparent; margin-bottom: 0px;">
                                                                        <tr style="background: transparent;">
                                                                            <td style="border-top: 0px; <?php echo e(App::getLocale() == 'he' ? 'border-left: 1px solid black;' : 'border-right: 1px solid black;'); ?>" width="65%">
                                                                                <?php
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
                                                                                ?>
                                                                            </td>
                                                                            <td style="border-top: 0px;" width="35%"><?php echo e(isset($malfunction->data['calculate'][$category['id']][$paragraph['id']]) && strpos($malfunction->data['calculate'][$category['id']][$paragraph['id']], '%') !== false ? $malfunction->data['calculate'][$category['id']][$paragraph['id']] : '0%'); ?></td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php endif; ?>
                                                    <tr>
                                                        <td><?php echo e(__('Total')); ?></td>
                                                        <td></td>
                                                        <td style="padding: 0px;">
                                                            <table class="table" style="background: transparent;margin-bottom: 0px;">
                                                                <tr style="background: transparent;">
                                                                    <td style="border-top: 0px; <?php echo e(App::getLocale() == 'he' ? 'border-left: 1px solid black;' : 'border-right: 1px solid black;'); ?>" width="65%">
                                                                        <?php
                                                                            $score = $relatively['total'] / ($relatively['count'] ? $relatively['count'] : 1);
                                                                            $score = round($score, 1);
                                                                        ?>
                                                                        <?php echo e($score); ?> %
                                                                    </td>
                                                                    <td style="border-top: 0px;" width="35%"><?php echo e(isset($paragraphs_total[$category['id']]) && strpos($paragraphs_total[$category['id']], '%') !== 'false' ? $paragraphs_total[$category['id']] : '0'); ?>%</td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><b><?php echo e(__('Total')); ?></b></td>
                                        <td></td>
                                        <td><b><?php echo e($categories_total); ?>%</b></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="malfunctions-item other">
                        <h5><b>2. <?php echo e(__('General')); ?></b></h5>
                        <?php echo isset( $malfunction->data['malfunction-general'] ) ?  $malfunction->data['malfunction-general'] : ""; ?>

                    </div>
                    <div class="malfunctions-item other">
                        <h5><b>3. <?php echo e(__('Repaired malfunctions')); ?></b></h5>
                        <?php echo (isset( $malfunction->data['malfunction-repaired'] ) ?  $malfunction->data['malfunction-repaired'] : "") ?>
                    </div>
                    <div class="malfunctions-item other">
                        <h5><b>4. <?php echo e(__('Principal malfunctions')); ?></b></h5>
                        <?php if(isset($malfunction->data['malfunction_principal_detail'])): ?>
                            <ul class="malfunctions-principal">
                                <?php $__currentLoopData = $malfunction->data['malfunction_principal_detail']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $principal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="malfunctions-item" data-malfunction-id="<?php echo $malfunction_id ?>" data-category-id="<?php echo $principal["category_id"] ?>" data-principal-id="<?php echo $principal["id"] ?>">
                                      <?php echo (isset($principal["value"]) ? str_replace(',', '', $principal["value"]) : "") ?>
                                        <?php if(isset($principal["category_id"]) && isset($principal["id"]) && isset($malfunction->data['photo'][$principal["category_id"]][$principal["id"]])): ?>
                                            <div class="malfunction-scoring__photos show">
                                                <?php $__currentLoopData = $malfunction->data['photo'][$principal["category_id"]][$principal["id"]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                                                                <img src="<?php echo e($path.$photo); ?> " class="malfunction-principal__photo-item" alt="<?php echo e($photo); ?>"/>
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

                                            <?php if(isset($malfunction->data['comments']) && isset($malfunction->data['comments'][$principal["id"]])): ?>
                                                <?php $__currentLoopData = $malfunction->data['comments'][$principal["id"]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                    <div class="malfunctions-item other">
                        <?php if(isset($malfunction->data["malfunction-culinary"]) && isset($malfunction->data["malfunction-culinary"]["checked"])): ?>
                            <h5><b>5. <?php echo e(__('Culinary')); ?></b></h5>
                            <?php echo $malfunction->data["malfunction-culinary"]["text"]; ?>

                        <?php else: ?>
                            <h5><b>5.</b></h5>
                        <?php endif; ?>
                    </div>
                    <div class="malfunctions-item other">
                        <h5><b>6. <?php echo e(__('Malfunction list')); ?></b></h5>
                        <?php if(isset($malfunction->data['malfunction-list'])): ?>
                            <?php echo $malfunction->data['malfunction-list']; ?>

                        <?php endif; ?>
                    </div>

                    <div class="malfunctions-item other">
                        <h5><b>7. <?php echo e(__('Summary & Recommendations')); ?></b></h5>
                        <?php if(isset($malfunction->data['malfunction-list'])): ?>
                            <?php echo $malfunction->data['malfunction-summary']; ?>

                        <?php endif; ?>
                    </div>

                    <div class="malfunctions-item other">
                        <h5><b>8. <?php echo e(__('Uploaded documents')); ?></b></h5>
                        <?php if(isset($malfunction->data['malfunction-uploads'])): ?>
                            <?php $__currentLoopData = $malfunction->data['malfunction-uploads']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $upload): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="malfunction-uploads__item">
                                    <a href="<?php echo e(url('/')); ?>/uploads/<?php echo e($upload); ?>" target="_blank" class="malfunction-uploads__text">
                                        <?php if(isImage($upload)): ?>
                                            <img class="malfunction-principal__photo-item" src="<?php echo e(url('/')); ?>/uploads/<?php echo e($upload); ?>" alt="<?php echo e($upload); ?>"/>
                                        <?php else: ?>
                                            <?php echo e(strlen($upload) > 20 ? mb_substr($upload, 0, 20, 'utf-8') : $upload); ?>

                                        <?php endif; ?>
                                    </a>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <div class="clear-both"></div>
                        <?php endif; ?>
                    </div>
                    <div class="malfunctions-item other">
                        <h5><?php echo e(__('Sincerely')); ?>,</h5>
                        <h5><?php echo e(isset($malfunction->data['employee_name']) ? $malfunction->data['employee_name'] : ""); ?></h5>
                    </div>
                </div>
                <div id='printing-holder'></div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script src="<?php echo e(url('/')); ?>/js/canvasjs.min.js"></script>
    <script src="<?php echo e(url('/')); ?>/js/pdf/jspdf.min.js"></script>
    <script src="<?php echo e(url('/')); ?>/js/pdf/html2canvas.min.js"></script>
    <script src="<?php echo e(url('/')); ?>/js/pdf/html2pdf.js"></script>
    <script src="<?php echo e(asset('js/malfunctions/show.js')); ?>"></script>

    <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/malfunctions/main.css" media="all">
    <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/malfunctions/show.css" media="all">
    <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/malfunctions/print.css" media="print">

    <?php if(app()->getLocale() == 'he'): ?>
        <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/malfunctions/main_rtl.css" media="all">
    <?php endif; ?>

    <script type="text/javascript">
        var GET_STATISTICS_URL = "<?php echo e(route('get-statistics')); ?>",
            SEND_PDF_URL = "<?php echo e(route('malfunctionSharePdf')); ?>",
            DUPLICATE_MALFUCNTION_URL = "<?php echo e(route('duplicateMalfunction')); ?>",
            SAVE_COMMENTS_URL = "<?php echo e(route('malfunctionSaveComments', $malfunction_id)); ?>",
            DELETE_MALFUNCTION_URL = "<?php echo e(action('MalfunctionController@destroy', $malfunction->id)); ?>",
            TOKEN = "<?php echo e(csrf_token()); ?>",
            BASE_URL = "<?php echo e(url('/')); ?>";
            DATE = "<?php echo $date ?>",
            REPORT_DATE = "<?php echo e(isset($malfunction->data['date']) ? $malfunction->data['date'] : ''); ?>",
            MALFUNCTION_ID = <?php echo $malfunction_id ?>,
            SITE = "<?php echo $malfunction->data['site'] ?>",
            SUBSITE =  "<?php echo $malfunction->data['subsite'] ?>",
            user = <?php echo $user ?>,
            LOCALE = "<?php echo e(app()->getLocale()); ?>";

        var Lang = {
            "Edit" : "<?php echo e(__('Edit')); ?>",
            "Delete" : "<?php echo e(__('Delete')); ?>",
            "Score" : "<?php echo e(__('CP Score')); ?>",
            "Date" : "<?php echo e(__('Date')); ?>",
            "Print" : "<?php echo e(__('Print')); ?>",
            "Message sent successfully" : "<?php echo e(__('Message sent successfully')); ?>",
            "Email was not sent" : "<?php echo e(__('Email was not sent')); ?>",
            "Repeating" : "<?php echo e(__('Repeating malfunction')); ?>",
            "Are you sure you want to delete it?" : "<?php echo e(__('Are you sure you want to delete it?')); ?>",
            "Yes" : "<?php echo e(__('Yes')); ?>",
            "Cancel(no)" : "<?php echo e(__('Cancel(no)')); ?>"
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>