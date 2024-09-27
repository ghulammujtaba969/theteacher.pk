
<?php $__env->startSection('title','All User'); ?>
<?php $__env->startSection('maincontent'); ?>
<?php
$data['heading'] = 'All Users';
$data['title'] = 'All Users';
?>
<?php echo $__env->make('admin.layouts.topbar',$data, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div class="contentbar">
    <div class="row">
        <div class="col-lg-12">
            <div class="card dashboard-card m-b-30">
                <div class="card-header all-user-card-header">
                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="false" title="<?php echo e(__('All Users')); ?>"><?php echo e(__('All Users')); ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false" title="<?php echo e(__('Students')); ?>"><?php echo e(__('Students')); ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="true" title="<?php echo e(__('Instructors')); ?>"><?php echo e(__('Instructors')); ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-admin-tab" data-toggle="pill" href="#pills-admin" role="tab" aria-controls="pills-admin" aria-selected="true" title="<?php echo e(__('Admins')); ?>"><?php echo e(__('Admins')); ?></a>
                        </li>
                    </ul>
                </div>
                <div class="mt-4">
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade active show" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                            <div class="all-user-menu">
                                <div class="row">
                                    <div class="col-lg-4 col-md-12 col-12">
                                        <h5 class="box-title"> <?php echo e(__('All Users')); ?></h5>
                                    </div>
                                    <div class="col-lg-8 col-md-12 col-12 text-right menus-button">
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('users.create')): ?>
                                        <a href="<?php echo e(route('user.add')); ?>" class="btn btn-primary-rgba mr-2" title="<?php echo e(__('Add User')); ?>"><i
                                                class="feather icon-plus mr-2"></i><?php echo e(__('Add User')); ?> </a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('users.delete')): ?>
                                        <button type="button" class="btn btn-danger-rgba mr-2 " data-toggle="modal"
                                            data-target="#bulk_delete" title="<?php echo e(__('Delete Selected')); ?>"><i class="feather icon-trash mr-2"></i> <?php echo e(__('Delete Selected')); ?> </button>
                                            <?php endif; ?>
                                                <a href="<?php echo e(route('user.import')); ?>" class="btn btn-success-rgba" title="<?php echo e(__('Import')); ?>"><i class="feather icon-plus mr-2"></i><?php echo e(__("Import")); ?></a>
                                    </div>
                                </div>
                            </div>
                            <div style="display:none" id="msg" class="alert alert-success">
                                <span id="res_message"></span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="test" class="table table-bordered data-table">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <input id="checkboxAll" type="checkbox" class="filled-in" name="checked[]"
                                                        value="all" />
                                                    <label for="checkboxAll" class="material-checkbox"></label> #
                                                </th>
                                                <th>#</th>
                                                <th><?php echo e(__('Profile Picture')); ?></th>
                                                <th><?php echo e(__('Users Details')); ?></th>
                                                <th><?php echo e(__('Role')); ?></th>
                                                <th><?php echo e(__('Login as User')); ?></th>
                                                <th><?php echo e(__('Status')); ?></th>
                                                <th><?php echo e(__('Action')); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                         <div id="bulk_delete" class="delete-modal modal fade" role="dialog">
                                            <div class="modal-dialog modal-sm">
                                                <!-- Modal content-->
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close"
                                                            data-dismiss="modal" title="<?php echo e(__('Close')); ?>">&times;</button>
                                                        <div class="delete-icon"></div>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <h4 class="modal-heading"><?php echo e(__('Are You Sure ?')); ?></h4>
                                                        <p><?php echo e(__('Do you really want to delete selected item names here? This
                                                            process
                                                            cannot be undone.')); ?></p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <form id="bulk_delete_form" method="post"
                                                            action="<?php echo e(route('user.bulk_delete')); ?>">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('POST'); ?>
                                                            <button type="reset" class="btn btn-gray translate-y-3"
                                                                data-dismiss="modal"><?php echo e(__('No')); ?></button>
                                                            <button type="submit" class="btn btn-danger"><?php echo e(__('Yes')); ?></button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                            <div class="all-user-menu">
                                <div class="row">

                                    <div class="col-lg-4 col-md-12">
                                        <h5 class="box-title"><?php echo e(__('All Students')); ?></h5>
                                    </div>
                                    
                                    <div class="col-lg-8 col-md-12 text-right menus-button">
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Alluser.delete')): ?>
                                            <a href="<?php echo e(route('alluser.add')); ?>" class="btn btn-primary-rgba mr-2" title="<?php echo e(__('Add Student')); ?>"><i
                                                class="feather icon-plus mr-2"></i><?php echo e(__('Add Student')); ?></a>
                                                <?php endif; ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Alluser.delete')): ?>
                                        <button type="button" class="btn btn-danger-rgba mr-2 " data-toggle="modal"
                                            data-target="#bulk_delete" title="<?php echo e(__('Delete Selected')); ?>"><i class="feather icon-trash mr-2"></i> <?php echo e(__('Delete Selected')); ?></button>
                                            <?php endif; ?>
                                            
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
            
                                <div class="table-responsive">
                                    <table id="allusertable" class="table table-striped table-bordered" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <input id="checkboxAll" type="checkbox" class="filled-in" name="checked[]"
                                                        value="all" />
                                                    <label for="checkboxAll" class="material-checkbox"></label> 
                                                </th>
                                                <th>#</th>
                                                <th><?php echo e(__('Profile Picture')); ?></th>
                                                <th><?php echo e(__('Student Detail')); ?></th>
                                                <th><?php echo e(__('Login As Student')); ?></th>
                                                <th><?php echo e(__('Status')); ?></th>
                                                <th><?php echo e(__('Action')); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <div id="bulk_delete" class="delete-modal modal fade" role="dialog">
                                            <div class="modal-dialog modal-sm">
                                                <!-- Modal content-->
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close"
                                                            data-dismiss="modal" title="<?php echo e(__('Close')); ?>">&times;</button>
                                                        <div class="delete-icon"></div>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <h4 class="modal-heading"><?php echo e(__('Are You Sure')); ?> ?</h4>
                                                        <p><?php echo e(__('Do you really want to delete selected item names here? This process
                                                                    cannot be undone')); ?>.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <form id="bulk_delete_form" method="post"
                                                            action="<?php echo e(route('user.bulk_delete')); ?>">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('POST'); ?>
                                                            <button type="reset" class="btn btn-gray translate-y-3"
                                                                data-dismiss="modal"><?php echo e(__('No')); ?></button>
                                                            <button type="submit"
                                                                class="btn btn-danger"><?php echo e(__('Yes')); ?></button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                            <div class="all-user-menu">
                                <div class="row">
                                    <div class="col-lg-4 col-md-12">
                                        <h5 class="box-title"><?php echo e(__('All Instructors')); ?></h5>
                                    </div>
                                    <div class="col-lg-8 col-md-12 text-right menus-button">
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Allinstructor.create')): ?>
                                        <a href="<?php echo e(route('allinstructor.add')); ?>" class="btn btn-primary-rgba mr-2"><i
                                            class="feather icon-plus mr-2" title="<?php echo e(__('Add Instructor')); ?>"></i><?php echo e(__('Add Instructor')); ?></a>
                                        <?php endif; ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Allinstructor.delete')): ?>
                                        <button type="button" class="btn btn-danger-rgba mr-2 " data-toggle="modal"
                                        data-target="#bulk_delete" title="<?php echo e(__('Delete Selected')); ?>"><i class="feather icon-trash mr-2"></i> <?php echo e(__('Delete Selected')); ?></button>
                                        <?php endif; ?>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="allinstructor" class="table table-striped table-bordered" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>
                                                    <input id="checkboxAll" type="checkbox" class="filled-in" name="checked[]"
                                                        value="all" />
                                                    <label for="checkboxAll" class="material-checkbox"></label> 
                                                </th>
                                                <th><?php echo e(__('Profile Picture')); ?></th>
                                                <th><?php echo e(__('Instructor Detail')); ?></th>
                                                <th><?php echo e(__('Login As Instructor')); ?></th>
                                                <th><?php echo e(__('Status')); ?></th>
                                                <th><?php echo e(__('Action')); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <div id="bulk_delete" class="delete-modal modal fade" role="dialog">
                                            <div class="modal-dialog modal-sm">
                                                <!-- Modal content-->
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close"
                                                            data-dismiss="modal" title="<?php echo e(__('Close')); ?>">&times;</button>
                                                        <div class="delete-icon"></div>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <h4 class="modal-heading"><?php echo e(__('Are You Sure')); ?> ?</h4>
                                                        <p><?php echo e(__('Do you really want to delete selected item names here? This process
                                                                    cannot be undone')); ?>.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <form id="bulk_delete_form" method="post"
                                                            action="<?php echo e(route('user.bulk_delete')); ?>">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('POST'); ?>
                                                            <button type="reset" class="btn btn-gray translate-y-3"
                                                                data-dismiss="modal"><?php echo e(__('No')); ?></button>
                                                            <button type="submit"
                                                                class="btn btn-danger"><?php echo e(__('Yes')); ?></button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-admin" role="tabpanel" aria-labelledby="pills-admin-tab">
                            <div class="all-user-menu">
                                <div class="row">

                                    <div class="col-lg-4 col-md-12">
                                        <h5 class="box-title"><?php echo e(__('All Admins')); ?></h5>
                                    </div>
                                    <div class="col-lg-8 col-md-12 text-right menus-button">
                                                <a href="<?php echo e(route('alladmin.add')); ?>" class="btn btn-primary-rgba mr-2" title="<?php echo e(__('Add Admin')); ?>"><i
                                                    class="feather icon-plus mr-2"></i><?php echo e(__('Add Admin')); ?></a> 
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="alladmin" class="table table-striped table-bordered" style="width: 100%";>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th><?php echo e(__('Profile Picture')); ?></th>
                                                <th><?php echo e(__('Admin Detail')); ?></th>
                                                <th><?php echo e(__('Login As Admin')); ?></th>
                                                <th><?php echo e(__('Status')); ?></th>
                                                <th><?php echo e(__('Action')); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
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
<!-- script for datatable end -->
<script type="text/javascript">
    $(function () {
      var table = $('#test').DataTable({
          processing: true,
          serverSide: true,
          searchDelay : 1000,
          stateSave : true,
          ajax: "<?php echo e(route('user.index')); ?>",
          columns: [
              {data: 'checkbox', name: 'checkbox'},
              {data: 'DT_RowIndex', name: 'users.id'},
              {data: 'image', name: 'image' , orderable: false, searchable: false},
              {data: 'name',name: 'users.fname'},
              {data: 'role', name: 'roles.name'},
              {data: 'loginasuser', name: 'loginasuser' , orderable: false, searchable: false},
              {data: 'status', name: 'status', orderable: false, searchable: false},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ]
      });
      
    });
</script>

<script>

    $(document).on("change", ".user", function () {

        $.ajax({
            type: "GET",
            dataType: "json",
            url: 'user/status',
            data: {
                'status': $(this).is(':checked') ? 1 : 0,
                'id': $(this).data('id')
            },
            success: function(data){
                var warning = new PNotify( {
                title: 'success', text:'Status Update Successfully', type: 'success', desktop: {
                desktop: true, icon: 'feather icon-thumbs-down'
                }
            });
                warning.get().click(function() {
                    warning.remove();
                });
            }
        });
    });

    $("#checkboxAll").on('click', function () {
        $('input.check').not(this).prop('checked', this.checked);
    });
</script>
<script type="text/javascript">
    $(function () {
      
      var table = $('#allusertable').DataTable({
          processing: true,
          serverSide: true,
          searchDelay : 1000,
          stateSave : true,
          ajax: "<?php echo e(route('allusers.index')); ?>",
          columns: [
              {data: 'checkbox', name: 'checkbox'},
              {data: 'DT_RowIndex', name: 'users.id'},
              {data: 'image', name: 'image', orderable: false, searchable: false},
              {data: 'name', name: 'users.fname'},
              {data: 'loginasuser', name: 'loginasuser' , orderable: false, searchable: false},
              {data: 'status', name: 'status', orderable: false, searchable: false},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ]
      });
      
    });
</script>
<!-- script for datatable start -->
<script>
    $(document).on("change", ".user", function () {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: 'user/status',
            data: {
                'status': $(this).is(':checked') ? 1 : 0,
                'id': $(this).data('id')
            },
            success: function(data){
                var warning = new PNotify( {
                title: 'success', text:'Status Update Successfully', type: 'primary', desktop: {
                desktop: true, icon: 'feather icon-thumbs-down'
                }
            });
                warning.get().click(function() {
                    warning.remove();
                });
            }
        });
    })
</script>
<script>
    $("#checkboxAll").on('click', function () {
        $('input.check').not(this).prop('checked', this.checked);
    });
</script>
<script type="text/javascript">
    $(function () {
      
      var table = $('#allinstructor').DataTable({
          processing: true,
          serverSide: true,
          responsive:true,
          searchDelay : 1000,
          stateSave : true,
          ajax: '<?php echo e(route('allinstructor.index')); ?>',
          columns: [
              {data: 'DT_RowIndex', name: 'users.id'},
              {data: 'checkbox', name: 'checkbox' , orderable: false ,searchable: false},
              {data: 'image', name: 'image', orderable: false ,searchable: false},
              {data: 'name' ,name: 'users.fname'},
              {data: 'loginasuser', name: 'loginasuser' , orderable: false, searchable: false},
              {data: 'status', name: 'status', orderable: false ,searchable: false},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ]
      });
      
    });
</script>
<script>
    $(document).on("change", ".user", function () {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: 'user/status',
            data: {
                'status': $(this).is(':checked') ? 1 : 0,
                'id': $(this).data('id')
            },
            success: function(data){
            var warning = new PNotify( {
                title: 'success', text:'Status Update Successfully', type: 'success', desktop: {
                desktop: true, icon: 'feather icon-thumbs-down'
                }
              });
              warning.get().click(function() {
                warning.remove();
              });
          }
        });
    })
</script>
<script>
    $("#checkboxAll").on('click', function () {
        $('input.check').not(this).prop('checked', this.checked);
    });
</script>
<script type="text/javascript">
    $(function () {
      
      var table = $('#alladmin').DataTable({
          processing: true,
          serverSide: true,
          responsive:true,
          searchDelay : 1000,
          stateSave : true,
          ajax: '<?php echo e(route('alladmin.index')); ?>',
          columns: [
              {data: 'DT_RowIndex', name: 'users.id'},
              {data: 'image', name: 'image', orderable: false ,searchable: false},
              {data: 'name' ,name: 'users.fname'},
              {data: 'loginasuser', name: 'loginasuser' , orderable: false, searchable: false},
              {data: 'status', name: 'status', orderable: false ,searchable: false},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ]
      });
      
    });
</script>
<script>
    $(document).on("change", ".user", function () {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: 'user/status',
            data: {
                'status': $(this).is(':checked') ? 1 : 0,
                'id': $(this).data('id')
            },
            success: function(data){
            var warning = new PNotify( {
                title: 'success', text:'Status Update Successfully', type: 'success', desktop: {
                desktop: true, icon: 'feather icon-thumbs-down'
                }
              });
              warning.get().click(function() {
                warning.remove();
              });
          }
        });
    })
</script>
<script>
    $("#checkboxAll").on('click', function () {
        $('input.check').not(this).prop('checked', this.checked);
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\eclass_purchasecopy\resources\views/admin/user/index.blade.php ENDPATH**/ ?>