<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <!-- <div class="card-header"><?php echo e(__('Login')); ?></div> -->
                <div class="card-header">
                    <h3 class="text-center text-primary"><?php echo e(__('Login to your account')); ?></h3>
                </div>

                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('login')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="form-group row">
                            <!-- <label for="email" class="col-sm-4 col-form-label <?php echo e(app()->getLocale() == 'he' ? 'text-md-left' : 'text-md-right'); ?>"><?php echo e(__('Email address')); ?></label> -->

                            <div class="col-md-12">
                                <input id="email" class="form-control<?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" name="email" value="<?php echo e(old('email')); ?>" placeholder="<?php echo e(__('Email')); ?>" autofocus>

                                <!-- <?php if($errors->has('email')): ?>
                                    <span class="invalid-feedback" role="alert">
                                        <strong><?php echo e($errors->first('email')); ?></strong>
                                    </span>
                                <?php endif; ?> -->
                            </div>
                        </div>

                        <div class="form-group row">
                            <!-- <label for="password" class="col-md-4 col-form-label <?php echo e(app()->getLocale() == 'he' ? 'text-md-left' : 'text-md-right'); ?>"><?php echo e(__('Password')); ?></label> -->

                            <div class="col-md-12">
                                <input id="password" type="password" class="form-control<?php echo e($errors->has('password') ? ' is-invalid' : ''); ?>" placeholder="<?php echo e(__('Password')); ?>" name="password">

                                <!-- <?php if($errors->has('password')): ?>
                                    <span class="invalid-feedback" role="alert">
                                        <strong><?php echo e($errors->first('password')); ?></strong>
                                    </span>
                                <?php endif; ?> -->
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>

                                    <label class="form-check-label" for="remember">
                                        <?php echo e(__('Keep me logged in')); ?>

                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <a class="btn btn-link <?php echo e(app()->getLocale() == 'he' ? 'text-left float-left' : 'text-right float-right'); ?> p-0" href="<?php echo e(route('password.request')); ?>">
                                    <?php echo e(__('Forgot password')); ?>

                                </a>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary w-100 mt-4">
                                    <?php echo e(__('Login')); ?>

                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>