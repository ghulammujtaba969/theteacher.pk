
<?php $__env->startSection('title', 'Institute - Admin'); ?>
<?php $__env->startSection('maincontent'); ?>
<?php
$data['heading'] = 'Institutes';
$data['title'] = 'Institutes';
?>
<?php echo $__env->make('admin.layouts.topbar',$data, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div class="contentbar">
  <?php if($errors->any()): ?>
  <div class="alert alert-danger" role="alert">
    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <p><?php echo e($error); ?><button type="button" class="close" data-dismiss="alert" aria-label="Close" title="<?php echo e(__('Close')); ?>">
        <span aria-hidden="true" style="color:red;">&times;</span></button></p>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
  <?php endif; ?>
  <!-- Start row -->
  <div class="row">
    <div class="col-lg-12">
      <div class="card dashboard-card m-b-30">
        <div class="card-header">
          <h5 class="card-title"><?php echo e(__('Institutes')); ?></h5>
          <div>
            <div class="widgetbar">
              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('institute.create')): ?>
              <a href="<?php echo e(route('institute.create')); ?>" class="btn btn-primary-rgba" title="<?php echo e(__('Add Institute')); ?>"><i
                  class="feather icon-plus mr-2"></i><?php echo e(__("Add Institute")); ?></a>
              <?php endif; ?>
              <?php echo e(csrf_field()); ?>

              <a href="<?php echo e(route('institute.import')); ?>" class="btn btn-success-rgba" title="<?php echo e(__('Import')); ?>"><i
                  class="feather icon-plus mr-2"></i><?php echo e(__("Import")); ?></a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <?php if(Auth::User()->role == "admin"): ?>
            <table id="datatable-buttons" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th><?php echo e(__('Id')); ?></th>
                  <th><?php echo e(__('Image')); ?></th>
                  <th><?php echo e(__('Institute Name')); ?></th>
                  <th><?php echo e(__('Details')); ?></th>
                  <th><?php echo e(__('Skills')); ?></th>
                  <th><?php echo e(__('Status')); ?></th>
                  <th><?php echo e(__('Verify')); ?></th>
                  <th><?php echo e(__('Action')); ?></th>
                </tr>
              </thead>
              <?php $__currentLoopData = $institute; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

              <tbody>
                <tr>
                  <td><?php echo e(filter_var($key+1)); ?></td>
                  <td><img src="<?php echo e(asset('files/institute/'.filter_var($item->image))); ?>"
                      class="img-responsive img-circle"></td>
                  <td><?php echo e($item->title); ?></td>
                  <td><?php echo e($item->detail); ?></td>
                  <td><?php echo e($item->skill); ?></td>
                  <td>
                    <label class="switch">
                      <input class="status" type="checkbox" name="status" data-id="<?php echo e($item->id); ?>"
                        <?php echo e($item->status == '1' ? 'checked' : ''); ?>>
                      <span class="knob"></span>
                    </label>
                  </td>
                  <td>
                    <label class="switch">
                      <input class="verify" type="checkbox" name="verify" data-id="<?php echo e($item->id); ?>"
                        <?php echo e($item->verified == '1' ? 'checked' : ''); ?>>
                      <span class="knob"></span>
                    </label>
                  </td>
                  <td>
                    <div class="dropdown">
                      <button class="btn btn-round btn-primary-rgba" type="button" id="CustomdropdownMenuButton3"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                          class="feather icon-more-vertical-" title="<?php echo e(__('Settings')); ?>"></i></button>
                      <div class="dropdown-menu" aria-labelledby="CustomdropdownMenuButton3">
                        <a class="dropdown-item" href="<?php echo e(route('institute.edit',['id' => $item->id])); ?>" title="<?php echo e(__('Edit')); ?>"><i
                            class="feather icon-edit mr-2"></i><?php echo e(__("Edit")); ?></a>
                        <a class="dropdown-item" data-toggle="modal"  data-target="#delete<?php echo e($item->id); ?>" title="<?php echo e(__('Delete')); ?>"><i
                            class="feather icon-delete mr-2"></i><?php echo e(__("Delete")); ?></a>
                      </div>
                    </div>

                    <div class="modal fade bd-example-modal-sm" id="delete<?php echo e($item->id); ?>" tabindex="-1" role="dialog" aria-hidden="true">
                      <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleSmallModalLabel"><?php echo e(__('Delete')); ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" title="<?php echo e(__('Close')); ?>">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            <p class="text-muted">
                              <?php echo e(__("Do you really want to delete these records? This process cannot be undone.")); ?></p>
                          </div>
                          <div class="modal-footer">
                            <form method="post" action="<?php echo e(route('institute.delete',['id' => $item->id])); ?>

                                              " data-parsley-validate class="form-horizontal form-label-left">
                              <?php echo e(csrf_field()); ?>

                              <?php echo e(method_field('DELETE')); ?>

                              <button type="button" class="btn btn-secondary"
                                data-dismiss="modal"><?php echo e(__("No")); ?></button>
                              <button type="submit" class="btn btn-danger"><?php echo e(__("Yes")); ?></button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </table>
            <?php endif; ?>

            <?php if(Auth::User()->role == "instructor"): ?>
            <table id="datatable-buttons" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th><?php echo e(__('Id')); ?></th>
                  <th><?php echo e(__('Image')); ?></th>
                  <th><?php echo e(__('Institutes')); ?></th>
                  <th><?php echo e(__('Details')); ?></th>
                  <th><?php echo e(__('Skills')); ?></th>
                  <th><?php echo e(__('Action')); ?></th>
                </tr>
              </thead>
              <tbody>
                <?php
                $insti = App\Institute::where('user_id',Auth::User()->id)
                ->where('status',1)
                ->get();

                ?>
                <?php $__currentLoopData = $insti; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><?php echo e(filter_var($key+1)); ?></td>
                  <td><img src="<?php echo e(asset('files/institute/'.filter_var($value->image))); ?>"
                      class="img-responsive img-circle"></td>
                  <td><?php echo e($value->title); ?></td>
                  <td><?php echo e($value->detail); ?></td>
                  <td><?php echo e($value->skill); ?></td>
                  <td>
                    <div class="dropdown">
                      <button class="btn btn-round btn-primary-rgba" type="button" id="CustomdropdownMenuButton3"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                          class="feather icon-more-vertical-" title="<?php echo e(__('Settings')); ?>"></i></button>
                      <div class="dropdown-menu" aria-labelledby="CustomdropdownMenuButton3">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('institute.edit')): ?>
                        <a class="dropdown-item" href="<?php echo e(route('institute.edit',['id' => $value->id])); ?>" title="<?php echo e(__('Edit')); ?>"><i
                            class="feather icon-edit mr-2"></i><?php echo e(__("Edit")); ?></a>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('institute.delete')): ?>
                        <a class="dropdown-item" data-toggle="modal" data-target=".bd-example-modal-sm" title="<?php echo e(__('Delete')); ?>"><i
                            class="feather icon-delete mr-2"></i><?php echo e(__("Delete")); ?></a>
                        <?php endif; ?>
                      </div>
                    </div>
                  </td>
                  <div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-sm">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleSmallModalLabel"><?php echo e(__('Delete')); ?></h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close" title="<?php echo e(__('Close')); ?>">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <p class="text-muted">
                            <?php echo e(__("Do you really want to delete these records? This process cannot be undone.")); ?></p>
                        </div>
                        <div class="modal-footer">
                          <form method="post" action="<?php echo e(route('institute.delete',['id' => $value->id])); ?>

                                              " data-parsley-validate class="form-horizontal form-label-left">
                            <?php echo e(csrf_field()); ?>

                            <?php echo e(method_field('DELETE')); ?>

                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(__("No")); ?></button>
                            <button type="submit" class="btn btn-danger"><?php echo e(__("Yes")); ?></button>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    <!-- End col -->
  </div>
  <!-- End row -->
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<script>
  $(document).on("change", ".status", function () {
    $.ajax({
      type: "GET",
      dataType: "json",
      url: 'institute/status',
      data: {
        'status': $(this).is(':checked') ? 1 : 0,
        'id': $(this).data('id')
      },
      success: function (data) {
        console.log(id)
      }
    });
  });
</script>
<script>
  $(document).on("change", ".verify", function () {
    $.ajax({
      type: "GET",
      dataType: "json",
      url: 'institute/verify',
      data: {
        'verify': $(this).is(':checked') ? 1 : 0,
        'id': $(this).data('id')
      },
      success: function (data) {
        var warning = new PNotify({
          title: 'success',
          text: 'Status Update Successfully',
          type: 'success',
          desktop: {
            desktop: true,
            icon: 'feather icon-thumbs-down'
          }
        });
        warning.get().click(function () {
          warning.remove();
        });
      }
    });
  });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\eclass_purchasecopy\resources\views/admin/Institute/index.blade.php ENDPATH**/ ?>