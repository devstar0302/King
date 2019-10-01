<?php $__env->startSection('content'); ?>

    <div id="statistics_page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card statistics" id="pdf-container">
                        <div class="card-header">
                            <h3 class="text-center text-primary"><?php echo e(__('Statistics')); ?></h3>
                            <div class="btn-group <?php echo e(app()->getLocale() == 'he' ? 'left' : 'right'); ?>">
                                <a href="javascript:void(0);" class="btn-action" id="download_pdf" title="download chart"><img src="<?php echo e(url('/')); ?>/img/frontend/action/download.png" width=25 height=25></a>
                                <a href="javascript:void(0);" class="btn-action" id="share_pdf" title="share pdf"><img src="<?php echo e(url('/')); ?>/img/frontend/action/share.png" width=25 height=25></a>
                                <a href="javascript:void(0);" class="btn-action" title="print pdf" onclick="print();"><img src="<?php echo e(url('/')); ?>/img/frontend/action/print.png" width=25 height=25></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="statistics-chart">
                                <form id="statistics-form">
                                    <?php echo e(csrf_field()); ?>


                                    <div class="form-header">
                                        <select id="site" name="site" multiple>
                                            <?php $__currentLoopData = $sites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option class='site-item' value="<?php echo e($site->title); ?>" data-has-subsites=<?php echo e($site->has_subsites); ?>><?php echo e($site->title); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>

                                        <select id="subsite" name="subsite" multiple>
                                        </select>

                                        <a href="javascript:void(0)" class='<?php echo e(app()->getLocale() == "he" ? "float-left" : "float-right"); ?> btn-default btn-apply' id='apply'><?php echo e(__('Apply')); ?></a>

                                        <input id="selected_type" type="hidden">
                                        <input id="paragraph_id" type="hidden">
                                        <input id="category_id" type="hidden">
                                        <input id="paragraph_id" type="hidden">

                                        <input id="statistic_types" class='<?php echo e(app()->getLocale() == "he" ? "float-left" : "float-right"); ?> comboTreeWrapper' name="statistic_type" value='<?php echo e(__("Statistics type")); ?>' readonly>

                                        <input id='date-range' class='<?php echo e(app()->getLocale() == "he" ? "float-left" : "float-right"); ?> date-range' type="text" name="date-range" value="<?php echo e(isset($date_range) ? $date_range : ''); ?>" placeholder="<?php echo e(__('Select date range')); ?>" readonly>
                                    </div>
                                </form>

                                <p class='site-names'></p>
                                <div id="chartContainer" style="height:370px; width:90%; margin: 20px auto; direction:ltr;"></div>
                                <div class='site-linecolors'></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $__env->make('components.alert', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"  media="all">
    <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/multiselect.css" media="all">
    <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/comboTreePlugin.css" media="all">
    <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/statistics/index.css" media="all">
    <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/statistics/print.css" media="print">

    <?php if(app()->getLocale() == 'he'): ?>
        <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/statistics/index_rtl.css" media="all">
    <?php endif; ?>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="<?php echo e(url('/')); ?>/js/multiselect.js"></script>
    <script src="<?php echo e(url('/')); ?>/js/icontains.js"></script>
    <script src="<?php echo e(url('/')); ?>/js/comboTreePlugin.js"></script>
    <script src="<?php echo e(url('/')); ?>/js/canvasjs.min.js"></script>
    <script src="<?php echo e(url('/')); ?>/js/pdf/jspdf.min.js"></script>
    <script src="<?php echo e(url('/')); ?>/js/pdf/html2canvas.min.js"></script>
    <script src="<?php echo e(url('/')); ?>/js/pdf/html2pdf.js"></script>

    <script type="text/javascript">
        var g_SitesSubsites = <?php echo json_encode($sites_subsites); ?>,
            g_StatisticTypes = <?php echo json_encode($statistic_types); ?>,
            GET_STATISTICS_URL = "<?php echo e(route('get-statistics')); ?>",
            SEND_PDF_URL = "<?php echo e(route('statisticSharePdf')); ?>",
            TOKEN = "<?php echo e(csrf_token()); ?>";

        var Lang = {
            "Site" : "<?php echo e(__('Site')); ?>",
            "Sub-site" : "<?php echo e(__('Sub-site')); ?>",
            "Score" : "<?php echo e(__('CP Score')); ?>",
            "Number" : "<?php echo e(__('Number')); ?>",
            "Date" : "<?php echo e(__('Date')); ?>",
            "Print" : "<?php echo e(__('Print')); ?>",
            "Message sent successfully" : "<?php echo e(__('Message sent successfully')); ?>",
            "Email was not sent" : "<?php echo e(__('Email was not sent')); ?>",
            "SelectAll" : "<?php echo e(__('Select all')); ?>",
            "AllSelected" : "<?php echo e(__('All selected')); ?>",
            "NoMatchesFound" : "<?php echo e(__('No matches found')); ?>"
        };

        var risk_values = {0: '', 1: "<?php echo e(__('Low')); ?>", 2: "<?php echo e(__('Medium')); ?>", 3: "<?php echo e(__('High')); ?>", 4: ''};
        var service_values = {0: "<?php echo e(__('N/A')); ?>", 1: "<?php echo e(__('Bad')); ?>", 2: "<?php echo e(__('Good')); ?>", 3: "<?php echo e(__('Very good')); ?>", 4: ''};

        $('[name="date-range"]').daterangepicker({
            opens: 'left',
            autoUpdateInput: false,
            locale: {
                // direction: "<?php echo e(app()->getLocale() == 'he' ? "rtl" : "ltr"); ?>",
                applyLabel: "<?php echo e(__('Apply')); ?>",
                cancelLabel: "<?php echo e(__('Clear')); ?>",
                daysOfWeek: ["<?php echo e(__('Su')); ?>", "<?php echo e(__('Mo')); ?>", "<?php echo e(__('Tu')); ?>", "<?php echo e(__('We')); ?>", "<?php echo e(__('Th')); ?>", "<?php echo e(__('Fr')); ?>", "<?php echo e(__('Sa')); ?>"],
                monthNames: [ "<?php echo e(__('January')); ?>", "<?php echo e(__('February')); ?>", "<?php echo e(__('March')); ?>", "<?php echo e(__('April')); ?>", "<?php echo e(__('May')); ?>", "<?php echo e(__('June')); ?>", "<?php echo e(__('July')); ?>", "<?php echo e(__('August')); ?>", "<?php echo e(__('September')); ?>", "<?php echo e(__('October')); ?>", "<?php echo e(__('November')); ?>", "<?php echo e(__('December')); ?>"],
                firstDay: 1
            }
        }, function(start, end, label) {
            $('#date-range').val(end.format('YYYY-MM-DD') + '  ~  ' + start.format('YYYY-MM-DD'));
        });
    </script>

    <script src="<?php echo e(asset('js/statistics/index.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>