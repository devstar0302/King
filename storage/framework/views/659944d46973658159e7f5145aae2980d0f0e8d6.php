<div class="lds-css ng-scope" id="loadingBlock">
    <div style="width:100%;height:100%" class="lds-eclipse"><div></div></div>
</div>

<?php echo $__env->make('components.modal_sharing', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<div id="sortPopup" class="modal">
    <div class="modal-dialog" style="width: 300px; max-width:500px;">
        <div class="modal-content">
            <div class="modal-header">
                <h1><?php echo e(__('Sorting')); ?></h1>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" value="<?php echo e(csrf_token()); ?>" name="_token">
                    <label for="sortingSelect" style="<?php echo e(app()->getLocale() == 'he' ? 'width:90px;' : 'width:105px;'); ?>"><?php echo e(__('Sorting by')); ?>: </label>
                    <select name="sorting" id="sortingSelect">
                        <option <?php if(Session::has('sort') && Session::get('sort') == 'date_desc'): ?> selected <?php endif; ?> value="date_desc"><?php echo e(__('Upload date des')); ?></option>
                        <option <?php if(Session::has('sort') && Session::get('sort') == 'date_asc'): ?> selected <?php endif; ?> value="date_asc"><?php echo e(__('Upload date asc')); ?></option>
                        <option <?php if(Session::has('sort') && Session::get('sort') == 'type_asc'): ?> selected <?php endif; ?> value="type_asc"><?php echo e(__('Type A-Z')); ?></option>
                        <option <?php if(Session::has('sort') && Session::get('sort') == 'type_desc'): ?> selected <?php endif; ?> value="type_desc"><?php echo e(__('Type Z-A')); ?></option>
                        <option <?php if(Session::has('sort') && Session::get('sort') == 'name_asc'): ?> selected <?php endif; ?> value="name_asc"><?php echo e(__('Name A-Z')); ?></option>
                        <option <?php if(Session::has('sort') && Session::get('sort') == 'name_desc'): ?> selected <?php endif; ?> value="name_desc"><?php echo e(__('Name Z-A')); ?></option>
                    </select>
                </form>
            </div>
            <div class="cb"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default close-button"><?php echo e(__('Close')); ?></button>
            </div>
        </div>
    </div>
</div>

