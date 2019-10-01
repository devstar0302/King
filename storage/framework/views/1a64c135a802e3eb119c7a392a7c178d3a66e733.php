<?php $__env->startSection('content'); ?>
    <?php ?>

    <style>
        .tutorial-video-action-icon {
            cursor: pointer;
        }
        .user-type-select {
            width: 110px !important;
            margin-right: 10px;
        }
    </style>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center text-primary"><?php echo e(__('Tutorial videos')); ?></h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped" border="1">
                            <thead style="background-color:#0074D9;color:white;">
                            <tr>
                                <th>#</th>
                                <th><?php echo e(__('Name')); ?></th>
                                <?php if($is_admin): ?>
                                    <th><?php echo e(__('Link')); ?></th>
                                    <th><?php echo e(__('User types')); ?></th>
                                    <th><?php echo e(__('Actions')); ?></th>
                                <?php endif; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $tutorials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr id="<?php echo e($index); ?>-fixed">
                                    <td <?php if(!$is_admin): ?> width="3%" <?php endif; ?>><?php echo e($index + 1); ?></td>
                                    <td width="50%"><a href="<?php echo e($row['link']); ?>" target="_blank"><?php echo e($row['name']); ?></a></td>
                                    <?php if($is_admin): ?>
                                        <td style="max-width: 150px;">
                                            <div style="text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                                <?php echo e($row['link']); ?>

                                            </div>
                                        </td>
                                        <td><?php echo e(implode(', ', $row['user_types'])); ?></td>
                                        <td>
                                            <input type="hidden" class="clicked_tutorial_id">
                                            <i class="fa fa-share-alt share-tutorial tutorial-video-action-icon"
                                               onclick="showSharePopup('<?php echo e($row['id']); ?>', '<?php echo e($row['name']); ?>', '<?php echo e($row['link']); ?>')"></i>
                                            <i class='fa fa-pencil-alt tutorial-video-action-icon'
                                               onclick="setEditableRow('<?php echo e($index); ?>')"></i>
                                            <i class='fa fa-trash-alt tutorial-video-action-icon'
                                               onclick="deleteTutorial('<?php echo e($row['id']); ?>')"></i>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                                <tr id="<?php echo e($index); ?>-editing" style="display: none">
                                    <td><?php echo e($index + 1); ?></td>
                                    <td><input id="<?php echo e($row['id']); ?>-name" type="text" value="<?php echo e($row['name']); ?>"></td>
                                    <?php if($is_admin): ?>
                                        <td><input id="<?php echo e($row['id']); ?>-link" type="text" value="<?php echo e($row['link']); ?>">
                                        </td>
                                        <td>
                                            <select
                                                    id="<?php echo e($row['id']); ?>-user-types"
                                                    multiple
                                                    class="user-type-select">
                                                <?php $__currentLoopData = [__('Admin'), __('Client'), __('Contractor'), __('Employee')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($type); ?>" <?php if(in_array($type, $row['user_types'])): ?> selected <?php endif; ?>
                                                        <?php if($type === __('Admin')): ?> selected disabled <?php endif; ?>>
                                                        <?php echo e($type); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </td>
                                        <td>
                                            <i class="fa fa-check tutorial-video-action-icon"
                                               onclick="updateTutorial('<?php echo e($row['id']); ?>')"></i>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if($is_admin): ?>
                                <tr id="<?php echo e(sizeof($tutorials)); ?>-fixed">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <i class="fa fa-plus-circle tutorial-video-action-icon"
                                           onclick="setEditableRow('<?php echo e(sizeof($tutorials)); ?>')"></i>
                                    </td>
                                </tr>
                                <tr id="<?php echo e(sizeof($tutorials)); ?>-editing" style="display: none">
                                    <td><?php echo e(sizeof($tutorials) + 1); ?></td>
                                    <td><input id="add-name" type="text"></td>
                                    <?php if($is_admin): ?>
                                        <td><input id="add-link" type="text"></td>
                                        <td>
                                            <select
                                                    id="add-user-types"
                                                    multiple
                                                    class="user-type-select">
                                                <?php $__currentLoopData = [__('Admin'), __('Client'), __('Contractor'), __('Employee')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($type); ?>"
                                                    <?php if($type === __('Admin')): ?> selected disabled <?php endif; ?>>
                                                        <?php echo e($type); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </td>
                                        <td>
                                            <i class="fa fa-check tutorial-video-action-icon"
                                               onclick="addTutorial()"></i>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <link rel="stylesheet" href="<?php echo e(url('/')); ?>/css/multiselect.css">
    <script src="<?php echo e(url('/')); ?>/js/multiselect.js"></script>


    <script>
        const Lang = {
            "SelectAll": "<?php echo e(__('Select all')); ?>",
            "AllSelected": "<?php echo e(__('All selected')); ?>",
            "NoMatchesFound": "<?php echo e(__('No matches found')); ?>",
            "Message sent successfully": "<?php echo e(__('Message sent successfully')); ?>"
        };

        $('select[multiple]').multipleSelect({
            selectAllText: Lang['SelectAll'],
            allSelected: Lang['AllSelected'],
            noMatchesFound: Lang['NoMatchesFound']
        });

        var op = document.querySelectorAll('[data-name]');
        for (var i = 0; i < op.length; i++) {
            if (op[i].getAttribute('data-name') === 'selectAll') {
                op[i].parentElement.remove();
            }
        }

        function showSharePopup(id, name, link) {
            $(".clicked_tutorial_id").val(id.toString());
            $("#message_subject").val(name.toString());
            $("#message_body").val(link.toString());

            $('.modal.sharing').addClass('show');
        }

        function sendFile() {
            var tutorial_id = $(".clicked_tutorial_id").val();
            var to_address = $("#to_address").val();
            var message_subject = $("#message_subject").val();
            var message_body = $("#message_body").val();

            if (to_address === '' || message_subject === '' || message_body === '')
                return;

            $('.modal.sharing').removeClass('show');

            let data = '';
            data += 'to_address=' + to_address + '&' + 'subject=' + message_subject + '&' + 'message=' + message_body;

            const onSuccess = () => {
                showAlert('success', Lang['Message sent successfully']);
            };

            sendRequest("POST", "tutorial/send-email", data, onSuccess);
        }

        function setEditableRow(index) {
            $('#' + index + '-fixed').css('display', 'none')
            $('#' + index + '-editing').css('display', 'table-row')
        }

        function addTutorial() {
            const data = dataMaker('add');
            sendRequest("POST", 'tutorial', data);
        }

        function updateTutorial(id) {
            const data = dataMaker(id);
            sendRequest("PUT", "tutorial/" + id, data)
        }

        function dataMaker(id) {
            let data = '';
            const name = $('#' + id + '-name').val();
            if (name) {
                data += 'name=' + name;
            }
            const link = $('#' + id + '-link').val();
            if (link) {
                if (data) {
                    data += '&';
                }
                data += 'link=' + link;
            }
            let types = $('#' + id + '-user-types').val();

            if (types.length > 0) {
                types = types + ',מנהל';
            } else {
                types = 'מנהל';
            }

            if (data) {
                data += '&';
            }
            data += 'user_types=' + types;
            return data;
        }

        function deleteTutorial(id) {
            swal({
                title: "<?php echo e(__('Are you sure you want to delete it?')); ?>",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "<?php echo e(__('Yes, delete it(site linking)')); ?>",
                cancelButtonText: "<?php echo e(__('No, cancel(site linking)')); ?>"
            }).then((result) => {
                if (result.value) {
                    sendRequest("DELETE", "tutorial/" + id);
                }
            })
        }

        function sendRequest(method, url, data, onSuccess) {
            let finalData = "_token=<?php echo e(csrf_token()); ?>";
            if (data) {
                finalData += '&' + data;
            }

            $('#loadingBlock').fadeIn(100);
            $.ajax({
                type: method,
                url: url,
                dataType: "html",
                data: finalData,
                success: function (response) {
                    $('#loadingBlock').fadeOut();
                    onSuccess ? onSuccess() : window.location.reload();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#loadingBlock').fadeOut();
                    window.location.reload();
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>