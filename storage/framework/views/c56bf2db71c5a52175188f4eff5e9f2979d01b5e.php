<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center text-primary">
                            <?php echo e(isset($paragraph->name) ? (isset($paragraphNumber) ? $paragraphNumber . ' ' : '') .  $paragraph->name : 'Unnamed paragraph'); ?></h3>
                    </div>

                    <div class="card-body">
                        <?php if(session('status')): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo e(session('status')); ?>

                            </div>
                        <?php endif; ?>

                        <form method="POST" id="frr-form" action="" style="direction:ltr">
                            <?php echo e(csrf_field()); ?>

                            <table class="table table-striped" border="1">
                                <thead style="background-color:#0074D9;color:white;">
                                <tr>
                                    <td><span><?php echo e(__('Actions')); ?></span></td>
                                    
                                    <td><span><?php echo e(__('Repair')); ?></span></td>
                                    <td><span><?php echo e(__('Risk')); ?></span></td>
                                    <td><span><?php echo e(__('Finding')); ?></span></td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $paragraph->frr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $frr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <a class="ffr-edit" data-id="<?php echo e($frr->id); ?>" href=""><i class="fa fa-edit"></i></a>
                                            <a class="delete-item" data-action="delete[<?php echo e($frr->id); ?>]" href=""><i class="fa fa-trash"></i></a>
                                        </td>
                                        
                                        <td><span class="repair"><?php echo e($frr->repair); ?></span></td>
                                        <td><span class="risk"><?php echo e($frr->risk); ?></span></td>
                                        <td><span class="finding"><?php echo e($frr->finding); ?></span></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><a href="" style="color: black;" class="ffr-add"><i
                                                class="fa fa-plus-circle"></i></a></td>
                                    
                                    <td><span class="repair">---</span></td>
                                    <td><span class="risk">---</span></td>
                                    <td><span class="finding">---</span></td>
                                </tr>
                                </tbody>
                            </table>
                            <button type="submit" style="margin-top: 15px;" class="btn btn-success btn-block">
                                <?php echo e(__('Save')); ?>

                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <style>

        .data-row .item-add-score:after,
        .item-score:after {
            content: '%';
            font-weight: bold;
        }

        .data-row-active .item-add-score:after,
        .data-row-active .item-score:after {
            content: '';
        }

        .item-name {
            font-weight: bold;
        }

        .table {
            margin-bottom: 0px;
        }

        .data-row p {
            margin: 0;
        }

        .data-row input,
        .data-row-active p,
        .data-row-active b,
        .data-row-active span,
        .data-row select,
        .data-row-active .item-name {
            display: none;
        }

        .data-row-active input,
        .data-row-active select {
            display: block;
        }
    </style>

    <script>
        $(document).ready(function () {
            var frr_counter = 0;
            $(document).on('click', '.ffr-edit', function (e) {
                e.preventDefault();
                var icon = $(this).children(),
                    tr = $(this).parent().parent(),
                    // type = tr.find('.type'),
                    repair = tr.find('.repair'),
                    risk = tr.find('.risk'),
                    finding = tr.find('.finding'),
                    frr_id = $(this).data('id');

                if (!tr.hasClass('data-row')) {
                    tr.addClass('data-row');
/*
                    type.after(`<select name="edit[${frr_id}][type]">
                                <option value="normal">normal</option>
                                <option ${type.html() == 'severe' ? 'selected' : ''} value="severe">severe</option>
                            </select>`);
                            */
                    repair.after(`<input type="text" name="edit[${frr_id}][repair]" value="${repair.html()}">`);
                    risk.after(`<input type="text" name="edit[${frr_id}][risk]" value="${risk.html()}">`);
                    finding.after(`<input type="text" name="edit[${frr_id}][finding]" value="${finding.html()}">`);

                    // type.next().on('change', function () {
                    //     $(this).prev().html($(this).val());
                    // })
                    repair.next().on('keyup', function () {
                        $(this).prev().html($(this).val());
                    })
                    risk.next().on('keyup', function () {
                        $(this).prev().html($(this).val());
                    })
                    finding.next().on('keyup', function () {
                        $(this).prev().html($(this).val());
                    })
                }

                tr.addClass('data-row-active');
                icon.remove();
            })

            $(document).on('click', '.ffr-add', function (e) {

                e.preventDefault();
                var icon = $(this).children(),
                    tr = $(this).parent().parent(),
                    // type = tr.find('.type'),
                    repair = tr.find('.repair'),
                    risk = tr.find('.risk'),
                    finding = tr.find('.finding');

                if (!tr.hasClass('data-row')) {
                    frr_counter++;
                    tr.addClass('data-row');
                    tr.attr('data-counter', frr_counter);

                    // type.html(`<span>normal</span><select name="new[${frr_counter}][type]">
                    //             <option value="normal">normal</option>
                    //             <option value="severe">severe</option>
                    //         </select>`);
                    repair.after(`<input type="text" name="new[${frr_counter}][repair]">`);
                    risk.after(`<input type="text" name="new[${frr_counter}][risk]">`);
                    finding.after(`<input type="text" name="new[${frr_counter}][finding]">`);

                    // type.find('select').on('change', function () {
                    //     $(this).prev().html($(this).val());
                    // })
                    repair.next().on('keyup', function () {
                        $(this).prev().html($(this).val());
                    })
                    risk.next().on('keyup', function () {
                        $(this).prev().html($(this).val());
                    })
                    finding.next().on('keyup', function () {
                        $(this).prev().html($(this).val());
                    })
                }

                tr.removeClass('data-row-active');
                $(this).parent().html(`
                    <a style="color:black; cursor: pointer;" onclick="deleteParagraph(${frr_counter})"><i class="fa fa-trash"></i></a>
                `);

                tr.after(`<tr>
                            <td><a href="" style="color: black;" class="ffr-add"><i class="fa fa-plus-circle"></i></a></td>
                            <!--<td class="type"></td>-->
                            <td><span class="repair">---</span></td>
                            <td><span class="risk">---</span></td>
                            <td><span class="finding">---</span></td>
                        </tr>`)
                tr.addClass('data-row-active')
            });

            $('.delete-item').on('click', function (e) {
                e.preventDefault();
                var action = $(this).data('action');
                swal({
                    title: "<?php echo e(__('Are you sure?')); ?>",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "<?php echo e(__('Yes, delete it')); ?>",
                    cancelButtonText: "<?php echo e(__('No,cancel')); ?>",
                }).then((result) => {
                    if (result.value) {
                        $(this).parent().parent().remove();
                    }
                });

                $('#frr-form').append(`<input type="hidden" name="${action}" value="true">`);
            })
        });

        function deleteParagraph(index) {
            swal({
                title: "<?php echo e(__('Are you sure?')); ?>",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "<?php echo e(__('Yes, delete it')); ?>",
                cancelButtonText: "<?php echo e(__('No,cancel')); ?>",
            }).then((result) => {
                if (result.value) {
                    $('#frr-form tbody tr[data-counter='+index+']').remove();
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>