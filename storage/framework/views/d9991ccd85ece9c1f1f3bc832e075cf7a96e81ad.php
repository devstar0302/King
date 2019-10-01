<?php if(count($files)): ?>
    <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="item">
            <input type='checkbox' class='mentioned-item item-check' data-id="<?php echo e($file->id); ?>"
                   data-type="<?php echo e($file->extension); ?>" data-path="<?php echo e(url('/')); ?>/uploads/frontend/<?php echo e($file->filename); ?>">
            <div class="image">
                <img src="<?php echo e(url('/')); ?>/uploads/frontend/<?php echo e($file->image); ?>" alt="<?php echo e($file->name); ?>">
            </div>

            <div class="content">
                <div class="name"><?php echo e($file->name); ?></div>
                <div class="type"><?php echo e(!empty($file->type->name) ? $file->type->name :pathinfo($file->filename, PATHINFO_EXTENSION)); ?></div>
                <div class="size"><?php echo e($file->size); ?></div>
            </div>

        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
