<div style="direction: <?php echo ($name['locale']  == 'he' ? 'rtl' : 'ltr') ?>">
    <p><?php echo e(__('Your URL')); ?>: <?php echo e($name['login_url']); ?></p>
    <p><?php echo e(__('Your login')); ?>: <?php echo e($name['login']); ?></p>
    <p><?php echo e(__('Your password')); ?>: <?php echo e($name['password']); ?></p>
</div>

