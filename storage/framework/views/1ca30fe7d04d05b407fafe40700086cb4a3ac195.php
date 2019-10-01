<?php $__env->startSection('content'); ?>
    <?php
        $user_role = strtolower(auth()->user()->role->title);
    ?>
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
                        <?php
                            $mPaginateClass = 'pull-right';

                            if (app()->getLocale() == 'he') {
                                $mPaginateClass = 'pull-left';
                            }
                        ?>
                        <div class="row" style="padding: 0 15px 10px 15px; text-align: left;height: 45px;">
                            <div class="" style="position: absolute;left: 20px;">
                                <input type="text" class="form-control" name="date-range" value="" placeholder="<?php echo e(__('Select date range')); ?>" style="height: 33px;">
                            </div>
                            <div>
                                <?php if( $user_role == 'admin' || $user_role == 'employee'): ?>
                                    <a href="<?php echo e(action('MalfunctionController@create')); ?>" class="btn-blue"><i class="fa fa-plus-circle"></i> <?php echo e(__('New form')); ?></a>
                                <?php endif; ?>
                            </div>
                            <div>
                                <?php if( $user_role == 'admin' || $user_role == 'employee'): ?>
                                    <a href="<?php echo e(action('MalfunctionController@createGuidance')); ?>" class="btn-blue"><i class="fa fa-plus-circle"></i> <?php echo e(__('New guidance day')); ?></a>
                                <?php endif; ?>
                            </div>




                        </div>

                        <div id="form_grid">

                        </div>
                        <div class="preloader-wrapper">
                            <div class="preloader">
                                <img src="<?php echo e(url('/')); ?>/css/malfunctions/images/preloader.gif" alt="NILA">
                            </div>
                        </div>
                            <input type="hidden" id="namecode" value="<?php echo e(__('Form')); ?>">
                            <input type="hidden" id="status" value="<?php echo e(__('Status')); ?>">
                            <input type="hidden" id="site" value="<?php echo e(__('Site')); ?>">
                            <input type="hidden" id="subsite" value="<?php echo e(__('Sub-site')); ?>">
                            <input type="hidden" id="employee" value="<?php echo e(__('Employee')); ?>">
                            <input type="hidden" id="score" value="<?php echo e(__('Score')); ?>">
                            <input type="hidden" id="date" value="<?php echo e(__('Date')); ?>">
                            <input type="hidden" id="lang" value="<?php echo e(app()->getLocale()); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/malfunctions/main.css">
    <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/malfunctions/jqx.base.css">

    <?php if(app()->getLocale() == 'he'): ?>
        <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/malfunctions/main_rtl.css" media="all">
    <?php endif; ?>

    <script type="text/javascript" src="<?php echo e(url('/')); ?>/js/malfunctions/jquery-1.11.1.min.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="<?php echo e(url('/')); ?>/js/stupidtable.min.js"></script>

    <script type="text/javascript" src="<?php echo e(url('/')); ?>/js/malfunctions/jqxcore.js"></script>
    <script type="text/javascript" src="<?php echo e(url('/')); ?>/js/malfunctions/jqxdata.js"></script>
    <script type="text/javascript" src="<?php echo e(url('/')); ?>/js/malfunctions/jqxbuttons.js"></script>
    <script type="text/javascript" src="<?php echo e(url('/')); ?>/js/malfunctions/jqxscrollbar.js"></script>
    <script type="text/javascript" src="<?php echo e(url('/')); ?>/js/malfunctions/jqxmenu.js"></script>
    <script type="text/javascript" src="<?php echo e(url('/')); ?>/js/malfunctions/jqxcheckbox.js"></script>
    <script type="text/javascript" src="<?php echo e(url('/')); ?>/js/malfunctions/jqxlistbox.js"></script>
    <script type="text/javascript" src="<?php echo e(url('/')); ?>/js/malfunctions/jqxdropdownlist.js"></script>
    <script type="text/javascript" src="<?php echo e(url('/')); ?>/js/malfunctions/jqxgrid.js"></script>
    <script type="text/javascript" src="<?php echo e(url('/')); ?>/js/malfunctions/jqxgrid.sort.js"></script>
    <script type="text/javascript" src="<?php echo e(url('/')); ?>/js/malfunctions/jqxgrid.pager.js"></script>
    <script type="text/javascript" src="<?php echo e(url('/')); ?>/js/malfunctions/jqxgrid.selection.js"></script>
    <script type="text/javascript" src="<?php echo e(url('/')); ?>/js/malfunctions/jqxgrid.edit.js"></script>
    <script type="text/javascript" src="<?php echo e(url('/')); ?>/js/malfunctions/jqxpanel.js"></script>
    <script type="text/javascript" src="<?php echo e(url('/')); ?>/js/malfunctions/jqxgrid.filter.js"></script>

    <script type="text/javascript">
        $('[name="date-range"]').daterangepicker({
            opens: 'left',
            language: 'he',
            autoUpdateInput: false,
            locale: {
                applyLabel: "<?php echo e(__('Apply')); ?>",
                cancelLabel: "<?php echo e(__('Clear')); ?>",
                daysOfWeek: ["<?php echo e(__('Su')); ?>", "<?php echo e(__('Mo')); ?>", "<?php echo e(__('Tu')); ?>", "<?php echo e(__('We')); ?>", "<?php echo e(__('Th')); ?>", "<?php echo e(__('Fr')); ?>", "<?php echo e(__('Sa')); ?>"],
                monthNames: [ "<?php echo e(__('January')); ?>", "<?php echo e(__('February')); ?>", "<?php echo e(__('March')); ?>", "<?php echo e(__('April')); ?>", "<?php echo e(__('May')); ?>", "<?php echo e(__('June')); ?>", "<?php echo e(__('July')); ?>", "<?php echo e(__('August')); ?>", "<?php echo e(__('September')); ?>", "<?php echo e(__('October')); ?>", "<?php echo e(__('November')); ?>", "<?php echo e(__('December')); ?>"],
                firstDay: 1
            },
            onSelect: function(dateText) {
                $('[name="date-range"]').val(start.format('YYYY-MM-DD') + ' / ' + end.format('YYYY-MM-DD'));
            }
        }, function(start, end, label) {
            $('[name="date-range"]').val(start.format('YYYY-MM-DD') + ' / ' + end.format('YYYY-MM-DD'));
            getData(start.format('YYYY-MM-DD') + '/' + end.format('YYYY-MM-DD'));
        });


    </script>
    <script src="<?php echo e(url('/')); ?>/js/malfunctions/index.js"></script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>