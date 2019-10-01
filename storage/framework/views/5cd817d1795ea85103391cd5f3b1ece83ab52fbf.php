<?php $__env->startSection('content'); ?>


<?php echo $__env->make('frontend._part._signs_forms_with_upload_button', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->make('frontend._part._modals', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        var SF_TOKEN = "<?php echo e(csrf_token()); ?>",
            SF_TIMESTAMP = "<? echo time();?>",
            SF_CURRENT_URL = "<?php echo e($_SERVER['REQUEST_URI']); ?>",
            SF_UPLOAD_ITEM_URL = "<?php echo e(url("/")); ?>/nik/upload",
            SF_RESORTING_URL = "<?php echo e(url('/')); ?>/nik/newsort",
            SF_UPDATE_CONTENT_URL = "<?php echo e(url('/')); ?>/nik/newArea",
            SF_DELET_ITEM_URL = "<?php echo e(url('/')); ?>/nik/delete",
            SF_SEND_EMAIL_URL = "<?php echo e(url('/')); ?>/nik/sendmail";
    </script>

    <script>
        var sortTextArray = {
            "date_desc": "Upload date DESC",
            "date_asc" : "Upload date ASC",
            "type_asc" : "Type A-Z",
            "type_desc": "Type Z-A",
            "name_asc" : "Name A-Z",
            "name_desc": "Name Z-A"
        };

        var Lang = {
            "Are you sure you want to delete it?" : "<?php echo e(__('Are you sure you want to delete it?')); ?>",
            "Yes" : "<?php echo e(__('Yes')); ?>",
            "Cancel(no)" : "<?php echo e(__('Cancel(no)')); ?>",
            "Message sent successfully" : "<?php echo e(__('Message sent successfully')); ?>",
            "Email was not sent" : "<?php echo e(__('Email was not sent')); ?>",
        }

        <?php if(Session::has('sort')): ?>
            $(document).ready(function () {
                $('.item.sortBlock').find('span').text(sortTextArray["<?php echo e(Session::get('sort')); ?>"]);
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

<?php $__env->startSection('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(url('/')); ?>/js/frontend/uploadiFive/uploadifive.css">
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>