<div class="container signs_forms">
    <div class="card">
        <div class="card-header">


            <div class="subview-container cars-close add-to-form ">
                <a href="javascript:void(0);"
                   onclick="uploadFromSignsForms()" class="close"></a>
            </div>
            <div class='btn-group <?php echo e(app()->getLocale() == "he" ? "right" : "left"); ?>'>
                <?php if($user_role == 'admin'): ?>
                    <div class="item uploadBlock btn-action" title="Upload files">
                        <i class="fa fa-upload"></i>
                        <form>
                            <div id="queue"></div>
                            <input id="file_upload" name="file_upload" type="file" multiple hidden>
                        </form>
                    </div>
                <?php endif; ?>
            </div>


            <div class="icons">
                <?php if(count($files)): ?>
                    <div class="icon">
                        <div class="option-block">
                            <a href="javascript:void(0);" title="Share " class="share disabled-action" onclick="sf_share(this)"></a>
                        </div>
                        <div class="option-block">
                            <a href="javascript:void(0);" class="delete disabled-action"
                               style="display: <?php if(!strcmp($user_role, 'admin')): ?> inline-block <?php else: ?> none <?php endif; ?>"
                               onclick="sf_delete(target)"></a>
                        </div>
                        <div class="option-block">
                            <a href="javascript:void(0);" title="Download" class="download disabled-action" download onclick="sf_download(target)"></a>
                        </div>
                        <div class="option-block">
                            <a href="javascript:void(0);" title="Print" class="print disabled-action" onclick="sf_print(this)"></a>
                        </div>
                    </div>
                <?php endif; ?>
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
                <a href="javascript:void(0);" class="btn-blue add-to-form" style="margin: 0;"
                   onclick="uploadCheckedFiles()"><?php echo e(__('Add')); ?></a>
                <input type="text" name="search" id="search" placeholder="<?php echo e(__('Search')); ?>" style="height: 33px;">
            </div>

            <div class="fileContent">
                <?php if(count($files)): ?>
                    <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="item">
                            <input type='checkbox' class='mentioned-item item-check' data-id="<?php echo e($file->id); ?>" data-type="<?php echo e($file->extension); ?>" data-path="<?php echo e(url('/')); ?>/uploads/frontend/<?php echo e($file->filename); ?>">
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
