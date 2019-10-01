<?php $__env->startSection('content'); ?>
<?php
    $user_temp = json_decode($user);
    $user_id = $user_temp->id;
    $user_role = strtolower(auth()->user()->role->title);
    $user_name = $user_temp->name; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center text-primary"><?php echo e(__('Malfunctions list')); ?></h3>                    
                </div>
                <div class="card-body">
                    <?php if(session('status')): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo e(session('status')); ?>

                    </div>
                    <?php endif; ?>
                    <?php if($user_role == 'admin' || $user_role == 'employee'): ?>
                        <a href="<?php echo e(action('MalfunctionController@create')); ?>" class="btn-blue"><i class="fa fa-plus-circle"></i> <?php echo e(__('New form')); ?></a>
                        <a href="<?php echo e(action('MalfunctionController@createGuidance')); ?>" class="btn-blue"><i class="fa fa-plus-circle"></i> <?php echo e(__('New guidance day')); ?></a>
                    <?php endif; ?>
                    <?php 
                        $mPaginateClass = 'pull-right';
                        
                        if (app()->getLocale() == 'he') {
                            $mPaginateClass = 'pull-left';
                        } 
                    ?>
                    
                    <div class="<?php echo e($mPaginateClass); ?>">
                        <?php if(! $mPagination->onFirstPage()): ?>
                            <a href="<?php echo e($mPagination->previousPageUrl()); ?>" class="btn-blue"><?php echo e(__('Previous')); ?></a>
                        <?php endif; ?>
                        <a href="<?php echo e($mPagination->nextPageUrl()); ?>" class="btn-blue"><?php echo e(__('Next')); ?></a>
                    </div>
                    
                    <form action="" style="float: left;">
                        <input type="text" name="date-range" value="" placeholder="<?php echo e(__('Select date range')); ?>">
                    </form>
                    <table class="table table-striped" border="1" id="malfunctions-table">
                        <thead style="background-color:#0074D9;color:white;">
                        <tr>
                            <th data-index='0' data-sort="alphanum" data-sort-default="desc" class="sortStyle"><?php echo e(__('Form')); ?> #</th>
                            <th data-index='1' data-sort="int" data-sort-onload="yes" data-sort-default="desc" class="sortStyle"><?php echo e(__('Date')); ?></th>
                            <th data-index='2' data-sort="string" class="sortStyle">
                                <span class="<?php echo e(app()->getLocale() == 'he' ? 'pull-right' : 'pull-left'); ?>"><?php echo e(__('Employee')); ?> </span><i class="fa fa-search search-toggle"></i>
                                <input type="text" class="<?php echo e((isset($employee)  && $employee) ? "" : "hidden"); ?> search-data" value="<?php echo e((isset($employee)  && $employee) ? $employee : ""); ?>" name="employee" size="15" placeholder="<?php echo e(__('Employee')); ?>" />

                            </th>
                            <th data-index='3' data-sort="string" class="sortStyle"><?php echo e(__('Score')); ?></th>
                            <th data-index='4' data-sort="string" class="sortStyle">
                                <span class="<?php echo e(app()->getLocale() == 'he' ? 'pull-right' : 'pull-left'); ?>"><?php echo e(__('Site')); ?> </span><i class="fa fa-search search-toggle"></i>
                                <input type="text" class="<?php echo e((isset($site)  && $site) ? "" : "hidden"); ?> search-data" value="<?php echo e((isset($site)  && $site) ? $site : ""); ?>" name="site" size="15" placeholder="<?php echo e(__('Site')); ?>" />
                            </th>
                            <th data-index='5' data-sort="string" class="sortStyle">
                                <span class="<?php echo e(app()->getLocale() == 'he' ? 'pull-right' : 'pull-left'); ?>"><?php echo e(__('Sub-site')); ?> </span><i class="fa fa-search search-toggle"></i>
                                <input type="text" class="<?php echo e((isset($subsite)  && $subsite) ? "" : "hidden"); ?> search-data" value="<?php echo e((isset($subsite)  && $subsite) ? $subsite : ""); ?>" name="subsite" size="15" placeholder="<?php echo e(__('Sub-Site')); ?>" />
                            </th>
                            <th data-index='6' data-sort="string" class="sortStyle"><?php echo e(__('Status')); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $malfunctions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $malfunction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($user_role != 'admin' && $user_role != 'employee' && isset($malfunction->data['status']) && isset($malfunction->data['status']['stage']) && $malfunction->data['status']['stage'] != 'publish'): ?>
                                    <?php continue; ?>;
                                <?php endif; ?>

                                <?php
                                    $date = '--/--/--';
                                    $date_sort_value = '';
                                    $date_pieces = explode('-', $malfunction->data['date']);
                                    if(count($date_pieces) == 3) {
                                        $date_sort_value = strtotime((2000 + (int)$date_pieces[2]).'-'.$date_pieces[1].'-'.$date_pieces[0]);
                                        $date = date("d/m/y", $date_sort_value);
                                    }

                                    $admin_changed = '';
                                    if($user_role == 'employee' && isset($malfunction->data['employee_name']) && $user_name == $malfunction->data['employee_name'] && isset($malfunction->data['status']) && isset($malfunction->data['status']['admin_changed_date']) &&
                                        (!isset($malfunction->data['status']['users']) || !isset($malfunction->data['status']['users'][$user_id]) || strtotime($malfunction->data['status']['users'][$user_id]['last_visit_date']) < strtotime($malfunction->data['status']['admin_changed_date']))) {
                                        $admin_changed  = 'changed';
                                    }

                                    $link_address = action('MalfunctionController@show', $malfunction->id);
                                    if(!isset($malfunction->data['status']) || !isset($malfunction->data['status']['stage']) || $malfunction->data['status']['stage'] == 'draft') {
                                        $link_address = action('MalfunctionController@edit', $malfunction->id);
                                    }
                                ?>
                                <tr data-id='<?php echo e($malfunction->id); ?>' class="<?php echo e($admin_changed); ?>">
                                    <td data-sort-value="<?php echo e('A9' . $malfunction->id); ?>"><a href="<?php echo e($link_address); ?>"><?php echo e($malfunction->nameCode); ?></a></td>
                                    <td data-sort-value='<?php echo e($date_sort_value); ?>'><?php echo e($date); ?></td>
                                    <td><?php echo e(isset($malfunction->data['employee_name']) ? $malfunction->data['employee_name'] : "------"); ?></td>
                                    <td><?php echo e(isset($malfunction->data['calculate']['total']) ? $malfunction->data['calculate']['total'] : "--.-%"); ?> </td>
                                    <td><?php echo e(isset($malfunction->data['site']) ? $malfunction->data['site'] : "------"); ?></td>
                                    <td><?php echo e(isset($malfunction->data['subsite']) ? $malfunction->data['subsite'] : "------"); ?></td>
                                    <td class='status'>
                                        <?php
                                            $employee_id = $malfunction->data['employee_id'];
                                            $status = $malfunction->data['status'];
                                            $hasStage = isset($status['stage']);
                                            $stage = $hasStage ? $status['stage'] : null;
                                        ?>
                                        <?php if(
                                                $hasStage &&
                                                (($stage != 'draft' && ($user_role == 'admin' || $user_role == 'employee')) ||
                                                $stage == 'publish' && ($user_role == 'client' || $user_role == 'contractor'))
                                            ): ?>
                                            <label class="toggle">
                                                <input type="checkbox" name="toggle-status" <?php if(
                                                    $hasStage && $stage == 'publish'
                                                ): ?> checked='checked' <?php endif; ?>
                                                <?php if($user_role != 'admin'): ?> disabled <?php endif; ?>>
                                                <i data-swchon-text="<?php echo e(__('ON')); ?>" data-swchoff-text="<?php echo e(__('OFF')); ?>"></i>
                                            </label>
                                        <?php endif; ?>
                                        <?php if(!$hasStage || $status['stage'] == 'draft'): ?>
                                            <img src="<?php echo e(url('/')); ?>/img/draft.png" width=40 style='margin-left:5px;'>
                                        <?php endif; ?>
                                        <?php if(
                                                $user_role == 'admin' &&
                                                isset($status['admin_changed_date']) &&
                                                isset($status['users']) &&
                                                isset($status['users'][$employee_id]) &&
                                                strtotime($status['users'][$employee_id]['last_visit_date']) > strtotime($status['admin_changed_date'])
                                            ): ?>
                                            <i class='fa fa-thumbs-up'></i>
                                        <?php endif; ?>
                                        <?php if(isset($status['stage'])): ?>
                                            <?php if(
                                                    isset($status['last_comment_date']) &&
                                                    (!isset($status['users'][$user_id]) ||
                                                    strtotime($status['users'][$user_id]['last_visit_date']) < strtotime($status['last_comment_date']))): ?>
                                                <img src="<?php echo e(url('/')); ?>/img/exclamation.png" width=30>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <?php $__currentLoopData = $guidances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guidance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($user_role != 'admin' && $user_role != 'employee' && isset($guidance->data['status']) && isset($guidance->data['status']['stage']) && $guidance->data['status']['stage'] != 'publish'): ?>
                                    <?php continue; ?>;
                                <?php endif; ?>

                                <?php
                                    $date = '--/--/--';
                                    $date_sort_value = '';
                                    $date_pieces = explode('-', $guidance->data['date']);
                                    if(count($date_pieces) == 3) {
                                        $date_sort_value = strtotime((2000 + (int)$date_pieces[2]).'-'.$date_pieces[1].'-'.$date_pieces[0]);
                                        $date = date("d/m/y", $date_sort_value);
                                    }

                                    $admin_changed = '';
                                    if($user_role == 'employee' && isset($guidance->data['employee_name']) && $user_name == $guidance->data['employee_name'] && isset($guidance->data['status']) && isset($guidance->data['status']['admin_changed_date']) &&
                                        (!isset($guidance->data['status']['users']) || !isset($guidance->data['status']['users'][$user_id]) || strtotime($guidance->data['status']['users'][$user_id]['last_visit_date']) < strtotime($guidance->data['status']['admin_changed_date']))) {
                                        $admin_changed  = 'changed';
                                    }

                                    $link_address = action('MalfunctionController@showGuidance', $guidance->id);
                                    if(!isset($guidance->data['status']) || !isset($guidance->data['status']['stage']) || $guidance->data['status']['stage'] == 'draft') {
                                        $link_address = action('MalfunctionController@editGuidance', $guidance->id);
                                    }
                                ?>
                                <tr data-id='<?php echo e($guidance->id); ?>' class="<?php echo e($admin_changed); ?>">
                                    <td data-sort-value="<?php echo e('B' . $guidance->id); ?>"><a href="<?php echo e($link_address); ?>"><?php echo e($guidance->nameCode); ?></a></td>
                                    <td data-sort-value='<?php echo e($date_sort_value); ?>'><?php echo e($date); ?></td>
                                    <td><?php echo e(isset($guidance->data['employee_name']) ? $guidance->data['employee_name'] : "------"); ?></td>
                                    <td> <?php echo e(__('Guidance day')); ?> </td>
                                    <td><?php echo e(isset($guidance->data['site']) ? $guidance->data['site'] : "------"); ?></td>
                                    <td><?php echo e(isset($guidance->data['subsite']) ? $guidance->data['subsite'] : "------"); ?></td>
                                    <td class='status'>
                                        <?php if(isset($guidance->data['status']) && isset($guidance->data['status']['stage']) &&
                                            ((($user_role == 'admin' || $user_role == 'employee') && $guidance->data['status']['stage'] != 'draft') || ($user_role == 'client' || $user_role == 'contractor') && $guidance->data['status']['stage'] == 'publish')): ?>
                                            <label class="toggle">
                                                <input type="checkbox" name="toggle-status" <?php if(isset($guidance->data['status']) && isset($guidance->data['status']['stage']) && $guidance->data['status']['stage'] == 'publish'): ?> checked='checked' <?php endif; ?> }} <?php if($user_role != 'admin'): ?> disabled <?php endif; ?> >
                                                <i data-swchon-text="<?php echo e(__('ON')); ?>" data-swchoff-text="<?php echo e(__('OFF')); ?>"></i>
                                            </label>
                                        <?php endif; ?>
                                        <?php if(!isset($guidance->data['status']) || !isset($guidance->data['status']['stage']) || $guidance->data['status']['stage'] == 'draft'): ?>
                                            <img src="<?php echo e(url('/')); ?>/img/draft.png" width=40 style='margin-left:5px;'>
                                        <?php endif; ?>
                                        <?php if($user_role == 'admin' && isset($guidance->data['employee_id']) && isset($guidance->data['status']) && isset($guidance->data['status']['admin_changed_date'])): ?>
                                            <?php $employee_id = $guidance->data['employee_id']; ?>
                                            <?php if(isset($guidance->data['status']['users']) && isset($guidance->data['status']['users'][$employee_id]) && strtotime($guidance->data['status']['users'][$employee_id]['last_visit_date']) > strtotime($guidance->data['status']['admin_changed_date'])): ?>
                                                <i class='fa fa-thumbs-up'></i>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if(isset($guidance->data['status']) && isset($guidance->data['status']['stage'])): ?>
                                            <?php if(isset($guidance->data['status']['last_comment_date']) && (!isset($guidance->data['status']['users']) || !isset($guidance->data['status']['users'][$user_id]) || strtotime($guidance->data['status']['users'][$user_id]['last_visit_date']) < strtotime($guidance->data['status']['last_comment_date']))): ?>
                                                <img src="<?php echo e(url('/')); ?>/img/exclamation.png" width=30>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    
                    <div class="pull-right">
                        <?php echo e($mPagination->links()); ?>

                    </div>   
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/malfunctions/main.css">

    <?php if(app()->getLocale() == 'he'): ?>
        <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/malfunctions/main_rtl.css" media="all">
    <?php endif; ?>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="<?php echo e(url('/')); ?>/js/stupidtable.min.js"></script>
    <script src="<?php echo e(url('/')); ?>/js/malfunctions/index.js"></script>

    <script type="text/javascript">
        var user = <?php echo $user ?>,
            FROM_DATE = "<?php echo e(isset($from) ? $from : ''); ?>",
            TO_DATE = "<?php echo e(isset($to) ? $to : ''); ?>",
            FORM_LIST_URL = "<?php echo e(action('MalfunctionController@index')); ?>",
            CHANGE_STATUS_URL = "<?php echo e(route('changeStatus')); ?>";


        $('[name="date-range"]').daterangepicker({
            opens: 'left',
            language: 'he',
            autoUpdateInput: false,
            startDate: FROM_DATE != "" ? new Date(FROM_DATE) : undefined,
            endDate: TO_DATE != "" ? new Date(TO_DATE) : undefined,
            locale: {
                applyLabel: "<?php echo e(__('Apply')); ?>",
                cancelLabel: "<?php echo e(__('Clear')); ?>",
                daysOfWeek: ["<?php echo e(__('Su')); ?>", "<?php echo e(__('Mo')); ?>", "<?php echo e(__('Tu')); ?>", "<?php echo e(__('We')); ?>", "<?php echo e(__('Th')); ?>", "<?php echo e(__('Fr')); ?>", "<?php echo e(__('Sa')); ?>"],
                monthNames: [ "<?php echo e(__('January')); ?>", "<?php echo e(__('February')); ?>", "<?php echo e(__('March')); ?>", "<?php echo e(__('April')); ?>", "<?php echo e(__('May')); ?>", "<?php echo e(__('June')); ?>", "<?php echo e(__('July')); ?>", "<?php echo e(__('August')); ?>", "<?php echo e(__('September')); ?>", "<?php echo e(__('October')); ?>", "<?php echo e(__('November')); ?>", "<?php echo e(__('December')); ?>"],
                firstDay: 1
            },
            onSelect: function(dateText) {
                $('[name="date-range"]').val(FROM_DATE + ' - ' + TO_DATE);
            }
        }, function(start, end, label) {
            location.href = FORM_LIST_URL + "?from=" + start.format('YYYY-MM-DD') + "&to=" + end.format('YYYY-MM-DD');
        });

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>