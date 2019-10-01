<div class="container signs_forms">
    <div class="card">
        <div class="card-header">


            <div class="subview-container cars-close add-to-form "  >
                <a href="javascript:void(0);"
                   onclick="uploadFromSignsForms()" class="closeButton" >&#10006</a>
            </div>













            <h3 class="text-center text-primary"><?php echo e(__('Signs&Forms')); ?></h3>
            <div class="btn-group <?php echo e(app()->getLocale() == 'he' ? 'left' : 'right'); ?>">
                <div data-view="1" title="List view" class="item viewBlock btn-action">
                    <!-- <span><?php echo e(__('view')); ?></span> -->
                </div>
                <div class="item sortBlock <?php if(Session::has('sort') && strpos(Session::get('sort'), 'asc')): ?> asc <?php endif; ?>  btn-action">
                    <!-- <span><?php echo e(__('Sort')); ?></span> -->
                </div>
            </div>

        </div>

        <div class="card-body">
            <div style="display:flex; justify-content:space-between; margin-top: 1px; margin-bottom:20px;">
                <a href="javascript:void(0);" class="btn-blue add-to-form" style="margin: 0;" onclick="uploadCheckedFiles()"><?php echo e(__('Add')); ?></a>
                <input type="text" name="search" id="search" placeholder="<?php echo e(__('Search')); ?>" style="height: 33px;">
            </div>

            <div class="fileContent">
                <?php if(count($files)): ?>
                    <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="item">
                            <input type='checkbox' class='item-check' data-id="<?php echo e($file->id); ?>">
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
            </div>
        </div>
    </div>
</div>
<script src="<?php echo e(asset('js/malfunctions/edit-guidance.js')); ?>"></script>
<input type="hidden" name="fileId" id="fileShareId">
