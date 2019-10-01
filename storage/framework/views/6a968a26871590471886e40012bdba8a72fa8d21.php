<div class="modal fade sharing"role="dialog">
    <div class="modal-dialog" style="max-width:500px;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h1><?php echo e(__('Sharing pdf')); ?></h1>
            </div>
            <div class="modal-body">
                <div class='form-group'>
                    <!-- <label for="to"><?php echo e(__('Send To')); ?>: </label> -->
                    <input type="email" name="to" id="to_address" value="" placeholder="<?php echo e(__('Please input email address')); ?>" class="fl">
                </div>
                <div class='form-group'>
                    <!-- <label for="subject"><?php echo e(__('Subject')); ?>: </label> -->
                    <input type="text" name="subject" id="message_subject" value="" placeholder="<?php echo e(__('Please input subject')); ?>" class="fl">
                </div>
                <div class='form-group'>
                    <!-- <label for="body"><?php echo e(__('Message')); ?>: </label> -->
                    <textarea name="body" id="message_body" value="" placeholder="<?php echo e(__('Please input message')); ?>" class="fl"></textarea>
                </div>
            </div>
            <div class="cb"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="sendFile();"><?php echo e(__('Send')); ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="$('.modal.sharing').removeClass('show');"><?php echo e(__('Cancel')); ?></button>
            </div>
        </div>
    </div>
</div>
