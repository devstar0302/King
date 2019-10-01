<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                      <h3 class="text-center text-primary"><?php echo e(__('Add new user')); ?></h3>
                    </div>

                    <div class="card-body">
                        <?php if(session('status')): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo e(session('status')); ?>

                            </div>
                        <?php endif; ?>

                        <form method="POST" action="<?php echo e(route('users.store')); ?>">
                            <?php echo csrf_field(); ?>

                            <div class="form-group row">
                                <label for="role" class="col-md-4 col-form-label <?php echo e(app()->getLocale() == 'he' ? 'text-md-left' : 'text-md-right'); ?>"><?php echo e(__('Role')); ?></label>

                                <div class="col-md-6">
                                    <select name="role" id="role" class="form-control<?php echo e($errors->has('role') ? ' is-invalid' : ''); ?>">
                                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($role->id); ?>"><?php echo e(__($role->title)); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <!-- <?php if($errors->has('role')): ?>
                                        <span class="invalid-feedback" role="alert">
                                            <strong><?php echo e($errors->first('role')); ?></strong>
                                        </span>
                                    <?php endif; ?> -->
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label <?php echo e(app()->getLocale() == 'he' ? 'text-md-left' : 'text-md-right'); ?>"><?php echo e(__('Name')); ?></label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control<?php echo e($errors->has('name') ? ' is-invalid' : ''); ?>" name="name" value="<?php echo e(old('name')); ?>" autofocus>
                                    <!-- <?php if($errors->has('name')): ?>
                                        <span class="invalid-feedback" role="alert">
                                            <strong><?php echo e($errors->first('name')); ?></strong>
                                        </span>
                                    <?php endif; ?> -->
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label <?php echo e(app()->getLocale() == 'he' ? 'text-md-left' : 'text-md-right'); ?>"><?php echo e(__('Email address')); ?></label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control<?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" name="email" value="<?php echo e(old('email')); ?>">
                                    <!-- <?php if($errors->has('email')): ?>
                                        <span class="invalid-feedback" role="alert">
                                            <strong><?php echo e($errors->first('email')); ?></strong>
                                        </span>
                                    <?php endif; ?> -->
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label <?php echo e(app()->getLocale() == 'he' ? 'text-md-left' : 'text-md-right'); ?>"><?php echo e(__('Password')); ?></label>
                                <div class="col-md-6">
                                    <input id="password" type="text" class="form-control<?php echo e($errors->has('password') ? ' is-invalid' : ''); ?>" name="password">
                                    <a href='javascript:void(0)' id='gen_password' class="btn-blue"><?php echo e(__('Generate password')); ?></a>
                                </div>
                                <!-- <?php if($errors->has('password')): ?>
                                    <span class="invalid-feedback" role="alert">
                                        <strong><?php echo e($errors->first('password')); ?></strong>
                                    </span>
                                <?php endif; ?> -->
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary"><?php echo e(__('Save')); ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>

<script>
    $('#gen_password').click(function() {
        $.ajax({
            type: 'get',
            url: "<?php echo e(route('gen-password')); ?>",
            data: {_token: "<?php echo e(csrf_token()); ?>" },
            success: function (data) {
                $('#password').val(data.password);
            }
        });
    });
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>