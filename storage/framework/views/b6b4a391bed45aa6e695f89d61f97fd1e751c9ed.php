<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<!--  -->

<!-- <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script> -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'HOME')); ?></title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo e(url('/')); ?>/img/favicon.ico" />

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/fontawesome.min.css">
    <link rel="stylesheet" href="<?php echo e(url('/')); ?>/fonts/style.css">

    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">

    <link href="<?php echo e(url('/')); ?>/css/app.css" rel="stylesheet" media="all">
    <link href="<?php echo e(url('/')); ?>/css/frontend/style.css" rel="stylesheet">
    <link href="<?php echo e(url('/')); ?>/css/custom.css" rel="stylesheet" media="all">

    <?php if(app()->getLocale() == 'he'): ?>
        <link href="<?php echo e(url('/')); ?>/css/frontend/style_rtl.css" rel="stylesheet">
        <link href="<?php echo e(url('/')); ?>/css/custom_rtl.css" rel="stylesheet" media="all">
    <?php endif; ?>
</head>

<?php
    $action = app('request')->route()->getAction();
    $controller = class_basename($action['controller']);
    list($controller, $action) = explode('@', $controller);

    $controller = str_replace('Controller', '', $controller);
    $controller = strtolower($controller);
?>
<body class="<?php echo e($controller); ?>">
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel header">
            <div aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>"><?php echo e(__('Main')); ?></a></li>
                    <?php if(isset($breadcrumbs)): ?>
                        <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="breadcrumb-item active" aria-current="page"><a href="<?php echo e($route['url']); ?>"><?php echo e($route['label']); ?></a></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <?php if(Request::segment(1) != null): ?>
                            <li class="breadcrumb-item active" aria-current="page"><a href="<?php echo e(isset($breadcrumbs_url)?$breadcrumbs_url:action('ParagraphController@index')); ?>"><?php echo e(ucfirst(Request::segment(1))); ?></a></li>
                        <?php endif; ?>
                        <?php if(Request::segment(2) != null): ?>
                            <li class="breadcrumb-item active" aria-current="page"><a href="<?php echo e(action('ParagraphController@index')); ?>"><?php echo e(ucfirst(Request::segment(2))); ?></a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ol>
            </div>

            <a href="<?php echo e(url('/')); ?>"><img src="<?php echo e(url('/')); ?>/img/logo.png" class="logo-img"></a>

            <?php if(env('APP_ENV', 'local') == 'local'): ?>
                <div class='lang-switch'>
                    <label class="toggle">
                        <input type="checkbox" name="toggle-status" <?php if(App::getLocale() == 'he'): ?> checked='checked' <?php endif; ?> onclick="$('#lang-form').submit()">
                        <i data-swchon-text="<?php echo e(__('he')); ?>" data-swchoff-text="<?php echo e(__('en')); ?>"></i>
                    </label>
                    <form id="lang-form" action="<?php echo e(route('lang.switch', ['lang' => (App::getLocale() == 'he' ? 'en' : 'he')])); ?>" method="POST" style="display: none;"> <?php echo csrf_field(); ?> </form>
                </div>
            <?php endif; ?>

            <div class="logout-wrapper">
                <a href="<?php echo e(route('logout')); ?>" title="<?php echo e(__('Log out')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> </a>
                <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;"> <?php echo csrf_field(); ?> </form>
            </div>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="<?php echo e(__('Toggle navigation')); ?>">
                <span class="navbar-toggler-icon"></span>
            </button>
        </nav>

        <main class="py-4">
            <?php if(count($errors)): ?>
                <div class="alert alert-danger" id="error_message">
                    <ul style="list-style: none;"
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>
            <div class="no-print">
                <?php echo $__env->make('flash::message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>

            <?php echo $__env->yieldContent('content'); ?>
        </main>

        <nav class="navbar navbar-expand-md navbar-light navbar-laravel footer">
            <div class="footer-wrapper">
                <a href="mailto:a@pampuni.com"><?php echo e(__('Created by Pampuni')); ?></a>
            </div>
        </nav>

        <?php echo $__env->make('components.alert', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php echo $__env->make('frontend._part._modals', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
</body>

<script src="<?php echo e(url('/')); ?>/js/jquery-3.3.1.min.js"></script>
<script src="<?php echo e(url('/')); ?>/js/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.28.4/dist/sweetalert2.all.min.js"></script>
<script src="<?php echo e(url('/')); ?>/js/jquery.cookie.js"></script>

<script src="<?php echo e(url('/')); ?>/js/custom.js"></script>

<?php echo $__env->yieldContent('scripts'); ?>
<script>
    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
</script>

</html>
