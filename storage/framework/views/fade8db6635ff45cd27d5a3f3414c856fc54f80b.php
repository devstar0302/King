<style>
    .hide-save {
        display: none !important;
    }
</style>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center text-primary"><?php echo e(__('Users management')); ?></h3>
                    </div>

                    <div class="card-body">
                        <?php if(session('status')): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo e(session('status')); ?>

                            </div>
                        <?php endif; ?>
                        <a href="<?php echo e(route('users.create')); ?>" class="btn-blue"><?php echo e(__('Add new user')); ?></a>
                        <table id="users-table" class="display responsive no-wrap" style="width:100%">
                            <thead>
                            <tr>
                                <th><?php echo e(__('Actions')); ?></th>
                                <th><?php echo e(__('Name')); ?></th>
                                <th><?php echo e(__('Password')); ?></th>
                                <th><?php echo e(__('Email')); ?></th>
                                <th><?php echo e(__('User type')); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr id="<?php echo e($user->id); ?>">
                                    <td style="display: flex; align-items: center;">
                                        <button class="btn btn-default mail-btn" onclick="mailUser(<?php echo e($user->id); ?>)"><i
                                                    class="far fa-envelope"></i></button>
                                        <button class="btn btn-default edit-btn"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-danger" onclick="deleteUser(<?php echo e($user->id); ?>)"><i
                                                    class="fas fa-trash-alt"></i></button>
                                        <button class="btn btn-success update-btn hide-save"><?php echo e(__('Save')); ?></button>
                                    </td>
                                    <td data-title="name"><?php echo e($user->name); ?></td>
                                    <td data-title="pwd"><?php echo e($user->orig); ?></td>
                                    <td data-title="email"><?php echo e($user->email); ?></td>
                                    <td data-title="role"
                                        data-roleid="<?php echo e($user->role->id); ?>"><?php echo e(__($user->role->title)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script type="text/javascript" charset="utf8"
            src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function () {
            $('#users-table').DataTable({
                responsive: true,
                "oLanguage": {
                    "oAria": {
                        "sSortAscending": "<?php echo e(__('sorting ascending')); ?>",
                        "sSortDescending": "<?php echo e(__('sorting descending')); ?>"
                    },
                    "oPaginate": {
                        "sFirst": "<?php echo e(__('First')); ?>",
                        "sLast": "<?php echo e(__('Last')); ?>",
                        "sNext": "<?php echo e(__('Next')); ?>",
                        "sPrevious": "<?php echo e(__('Previous')); ?>"
                    },
                    "sEmptyTable": "<?php echo e(__('No data available in table')); ?>",
                    "sLengthMenu": "<?php echo e(__('show _MENU_ entries')); ?>",
                    "sLoadingRecords": "<?php echo e(__('Loading...')); ?>",
                    "sProcessing": "<?php echo e(__('Processing...')); ?>",
                    "sSearch": "<?php echo e(__('Search')); ?>",
                    "sZeroRecords": "<?php echo e(__('No matching records found')); ?>",
                    "sInfo": "<?php echo e(__('Showing _START_ to _END_ of _TOTAL_ entries')); ?>",
                    "sInfoEmpty": "<?php echo e(__('No entries to show')); ?>",
                    "sInfoFiltered": "<?php echo e(__('filtered from _MAX_ total records')); ?>"
                }
            });
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(".update-btn").on('click', function () {
            let id = $(this).parent().parent().attr("id");
            let pwdEl = $("#" + id + " > *[data-title='pwd']");
            let emailEl = $("#" + id + " > *[data-title='email']");
            let nameEl = $("#" + id + " > *[data-title='name']");
            let roleEl = $("#" + id + " > *[data-title='role']");
            let pwd = $("#" + id + " > *[data-title='pwd'] > input[name='pwd']").val();
            let email = $("#" + id + " > *[data-title='email'] > input[name='email']").val();
            let name = $("#" + id + " > *[data-title='name'] > input[name='name']").val();
            let role = $("#" + id + " > *[data-title='role'] > select[name='role']").val();
            let data = {
                'id': id,
                'pwd': pwd,
                'email': email,
                'name': name,
                'role': role
            };

            $.ajax({
                type: 'PUT',
                url: "<?php echo route('users.update', ['id' => '']); ?>" + "/" + id,
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
                    "_token": "<?php echo e(csrf_token()); ?>",
                    "pwd": data.pwd,
                    "email": data.email,
                    "name": data.name,
                    "role": data.role
                },

                success: function (data) {
                    swal({
                        position: 'top-end',
                        type: 'success',
                        title: "<?php echo e(__('Updated!(data saved)')); ?>",
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function (data) {
                    swal({
                        title: "<?php echo e(__('Error!')); ?>",
                        type: 'error',
                        confirmButtonText: "<?php echo e(__('Ok')); ?>"
                    })
                },
                finally: function () {

                }
            });

            pwdEl.html(data.pwd);
            emailEl.html(data.email);
            nameEl.html(data.name);
            roleEl.html(data.role);
            $(this).addClass('hide-save')
        });

        $(".edit-btn").on('click', function () {
            let id = $(this).parent().parent().attr("id");
            let pwdEl = $("#" + id + " > *[data-title='pwd']");
            let emailEl = $("#" + id + " > *[data-title='email']");
            let nameEl = $("#" + id + " > *[data-title='name']");
            let roleEl = $("#" + id + " > *[data-title='role']");
            let data = {
                'id': id,
                'pwd': pwdEl.html(),
                'email': emailEl.html(),
                'name': nameEl.html(),
                'role': roleEl.html(),
            };
            pwdEl.html("<input type='text' name='pwd' value='" + data.pwd + "'>");
            emailEl.html("<input type='email' name='email' value='" + data.email + "'>");
            nameEl.html("<input type='text' name='name' value='" + data.name + "'>");

            //Create array of options to be added
            let roles = ["מנהל", "לקוח", "קבלן", "עובד"];

            //Create and append select list
            var selectList = document.createElement("select");
            selectList.setAttribute("name", "role");
            roleEl.html(selectList);

            //Create and append the options
            for (let i = 0; i < roles.length; i++) {
                let option = document.createElement("option");
                option.setAttribute("value", roles[i]);
                if (data.role === roles[i]) {
                    option.setAttribute("selected", "selected");
                }
                option.text = roles[i];
                selectList.appendChild(option);
            }

            $(this).parent().find('.update-btn').removeClass('hide-save')
        });

        function deleteUser(id) {
            swal({
                title: "<?php echo e(__('Are you sure?')); ?>",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "<?php echo e(__('Yes')); ?>",
                cancelButtonText: "<?php echo e(__('Cancel(no)')); ?>"
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type: 'DELETE',
                        url: "<?php echo route('users.destroy', ['id' => '']); ?>" + "/" + id,
                        dataType: 'json',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: {"_token": "<?php echo e(csrf_token()); ?>"},

                        success: function (data) {
                            $("#" + id).remove();
                            swal({
                                title: "<?php echo e(__('Deleted!')); ?>",
                                type: 'success',
                                confirmButtonText: "<?php echo e(__('Ok')); ?>"
                            })
                        },
                        error: function (data) {
                            swal({
                                title: "<?php echo e(__('Error!')); ?>",
                                type: 'error',
                                confirmButtonText: "<?php echo e(__('Ok')); ?>"
                            })
                        }
                    });
                }
            })
        }

        function mailUser(id) {
            $(".mail-btn").attr("disabled", true);
            $.ajax({
                type: 'GET',
                url: "<?php echo route('send-cred', ['id' => '']); ?>" + "/" + id,
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {"_token": "<?php echo e(csrf_token()); ?>"},

                success: function (data) {
                    swal({
                        position: 'top-end',
                        type: 'success',
                        title: "<?php echo e(__('Mail was sent')); ?>",
                        showConfirmButton: false,
                        timer: 1500
                    })
                    $('.mail-btn').removeAttr('disabled');
                },
                error: function (data) {
                    swal({
                        title: "<?php echo e(__('Error!')); ?>",
                        type: 'error',
                        confirmButtonText: "<?php echo e(__('Ok')); ?>"
                    })
                    $('.mail-btn').removeAttr('disabled');
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>