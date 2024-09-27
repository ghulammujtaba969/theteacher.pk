<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo e(url('installer/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(url('installer/css/shards.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(url('installer/css/custom.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(url('css/bower_components/font-awesome/css/font-awesome.min.css')); ?>">
    <title><?php echo e(__('Installing App - Step 2 - Database Details')); ?></title>
    
  </head>
  <body>
    <?php echo $__env->make('admin.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('sweetalert::alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

      <div class="preL display-none">
        <div class="preloader3 display-none"></div>
      </div>

   		<div class="container">
   			<div class="card">
          <div class="card-header">
              <h3 class="m-3 text-center text-dark ">
                 <?php echo e(__(' Welcome To Setup Wizard - Setting Up Database')); ?>

              </h3>
          </div>
   				<div class="card-body" id="stepbox">
            <form autocomplete="off" action="<?php echo e(route('db.next')); ?>" id="step2form" method="POST" class="database-validation" novalidate>
              <?php echo csrf_field(); ?>

              <h3><?php echo e(__('Database Details')); ?></h3>
              <hr>

              <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="DB_HOST"><?php echo e(__('Database Host: ex. 127.0.0.1/localhost')); ?></label>
                    <input name="DB_HOST" type="text" class="form-control" id="DB_HOST" placeholder="localhost" value="<?php echo e(env('DB_HOST')); ?>" required>
                    
                    <div class="invalid-feedback">
                       <?php echo e(__(' Please enter a database host name.')); ?>

                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="DB_PORT"><?php echo e(__('Database Port')); ?>:</label>
                    <input name="DB_PORT" type="text" class="form-control" id="DB_PORT" placeholder="3306" value="<?php echo e(env('DB_PORT')); ?>" required>
                    
                    <div class="invalid-feedback">
                       <?php echo e(__(' Please enter a database port.')); ?>

                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="DB_DATABASE"><?php echo e(__('Database Name')); ?>:</label>
                    <input name="DB_DATABASE" type="text" class="form-control" id="DB_DATABASE" placeholder="db_name" value="<?php echo e(env('DB_DATABASE')); ?>" required>
                    
                    <div class="invalid-feedback">
                        <?php echo e(__('Please enter a database name.')); ?>

                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="DB_USERNAME"><?php echo e(__('Database Username')); ?>:</label>
                    <input name="DB_USERNAME" type="text" class="form-control" id="DB_USERNAME" placeholder="root" value="<?php echo e(env('DB_USERNAME')); ?>" required>
                    <div class="invalid-feedback">
                        <?php echo e(__('Please enter a database username.')); ?>

                    </div>
                </div>

                <div class="eyeCy col-md-6 mb-3">
                    <label for="DB_PASSWORD"><?php echo e(__('Database Password')); ?>:</label>
                    <input name="DB_PASSWORD" type="password" class="form-control" id="validationCustom05" placeholder="*****" value="<?php echo e(env('DB_PASSWORD')); ?>">

                    <span toggle="#validationCustom05" class="eye fa fa-fw fa-eye field-icon toggle-password"></span>
                    
                    <div class="valid-feedback">
                          <?php echo e(__('Password can be blank if you testing it on localhost !')); ?>

                    </div>
                </div>
                
                <button class="float-right step1btn btn btn-primary" type="submit"><?php echo e(__('Continue')); ?></button>
              </div>
            </form>
              
   				</div>
   			</div>
        <p class="text-center m-3 text-white">&copy;<?php echo e(date('Y')); ?> | <?php echo e(__('eClass - Learning Management System | Installer v1.1 |')); ?> <a class="text-white" href="http://mediacity.co.in"><?php echo e(__('Media City')); ?></a></p>
   		</div>
      
      <div class="corner-ribbon bottom-right sticky green shadow"><?php echo e(__('Step 2')); ?> </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="<?php echo e(url('installer/js/jquery-3.4.1.min.js')); ?>"></script>
    <script src="<?php echo e(url('installer/js/jquery.validate.min.js')); ?>"></script>
    <script src="<?php echo e(url('installer/js/additional-methods.min.js')); ?>"></script>
    <script src="<?php echo e(url('installer/js/ej.web.all.min.js')); ?>"></script>
    <script src="<?php echo e(url('installer/js/popper.min.js')); ?>"></script>
    <script src="<?php echo e(url('installer/js/bootstrap.min.js')); ?>"></script>
    <script src="<?php echo e(url('installer/js/shards.min.js')); ?>"></script>
    <?php echo $__env->yieldContent('script-zone'); ?>
    <script>

      (function() {
          'use strict';
          window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('database-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
              form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                  event.preventDefault();
                  event.stopPropagation();
                }
                form.classList.add('was-validated');
              }, false);
            });
          }, false);
        })();

        $(".toggle-password").on('click', function() {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if(input.attr("type") == "password") {
          input.attr("type", "text");
        } else {
          input.attr("type", "password");
        }
      });  
    </script>

    <script>
    (function() {
      'use strict';
        $(function() 
        { 
          $("form").submit(function () {
            if($(this).valid()){
                $('.preL').fadeIn('fast');
                $('.preloader3').fadeIn('fast');
                $('.container').css({ '-webkit-filter':'blur(5px)'});
                $('body').attr('scroll','no');
                $('body').css('overflow','hidden');
              }
          });
        });
      })();
  </script>

</body>
</html>
<?php /**PATH D:\xampp8.1\htdocs\Personal\theteacher.pk\Admin\resources\views/db.blade.php ENDPATH**/ ?>