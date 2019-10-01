<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center text-primary"><?php echo e(__('Paragraphs')); ?></h3>
                    </div>

                    <div class="card-body">
                        <?php if(session('status')): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo e(session('status')); ?>

                            </div>
                        <?php endif; ?>
                        <?php if($paragraphs->count()): ?>
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <td><?php echo e(__('Name')); ?></td>
                                    <td><?php echo e(__('Score')); ?></td>
                                    <td><?php echo e(__('Finding')); ?></td>
                                    <td><?php echo e(__('Risk')); ?></td>
                                    <td><?php echo e(__('Repair')); ?></td>
                                    <td><?php echo e(__('Type')); ?></td>
                                    <td><?php echo e(__('Actions')); ?></td>
                                </tr>

                                <?php $__currentLoopData = $paragraphs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($paragraph->name); ?></td>
                                        <td><?php echo e($paragraph->score); ?></td>
                                        <td><?php echo e(implode(';',$paragraph->finding)); ?></td>
                                        <td><?php echo e($paragraph->risk); ?></td>
                                        <td><?php echo e($paragraph->repair); ?></td>
                                        <td><?php echo e($paragraph->type); ?></td>
                                        <td>
                                            <a href="<?php echo e(action('ParagraphController@edit', $paragraph->id)); ?>"><i class="fa fa-edit"></i> </a>
                                            <a  href="<?php echo e(action('ParagraphController@destroy', $paragraph->id)); ?>"><i class="fa fa-trash text-danger"></i> </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </thead>
                            </table>
                                <form action="<?php echo e(action('ParagraphController@store')); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                <div class="row">
                                        <div class="col-md-2">
                                            <input type="text" placeholder="Name" name="name" class="form-control" />
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" placeholder="Score" name="score" class="form-control" />
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" placeholder="Finding" name="finding" class="form-control" />
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" placeholder="Risk" name="risk" class="form-control" />
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" placeholder="Repair" name="repair"
                                                   class="form-control" />
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-control" name="type">
                                                <option value="normal"><?php echo e(__('normal')); ?></option>
                                                <option value="principal"><?php echo e(__('principal')); ?></option>
                                                <option value="severe"><?php echo e(__('severe')); ?></option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 offset-md-10" style="margin-top:15px;">
                                            <button type="submit" class="btn btn-primary btn-block"><i class="fa
                                            fa-save"></i> </button>
                                        </div>
                                </div>
                            </form>
                        <?php else: ?>
                            <h3 class="text-center text-danger"><?php echo e(__('No data in paragraphs table')); ?></h3>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>