<?php $__env->startSection('content'); ?>
<div class="container">
    <div >
        <div class="col-md-8">
            <h1 class='home-title'><?php echo e(__('Main menu')); ?></h1>
            <div class="row">
                <div class="<?php if(!strcmp($user_role, 'admin')): ?> col-md-6 <?php else: ?> col-md-12 <?php endif; ?>">
                    <h3 class="home-sub-title"><?php echo e(__('Reports')); ?></h3>
                    <div class="home-link-item"><a href="malfunctions"><?php echo e(__('Form list')); ?></a></div>
                    <div class="home-link-item"><a href="statistics"><?php echo e(__('Statistics')); ?></a></div>
                    <?php if(!strcmp($user_role, 'admin')): ?>
                        <div class="home-link-item"><a href="categories"><?php echo e(__('Category management')); ?></a></div>
                    <?php endif; ?>
                    <div class="home-link-item"><a href="nik"><?php echo e(__('Signs&forms')); ?></a></div>
                    <div class="home-link-item"><a href="tutorial-videos"><?php echo e(__('Tutorial videos')); ?></a></div>
                </div>
                <?php if(!strcmp($user_role, 'admin')): ?>
                    <div class="col-md-6">
                        <h3 class="home-sub-title"><?php echo e(__('Users')); ?></h3>
                        <div class="home-link-item"><a href="users"><?php echo e(__('Users management')); ?></a></div>
                        <div class="home-link-item"><a href="companies"><?php echo e(__('Site linking')); ?></a></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>