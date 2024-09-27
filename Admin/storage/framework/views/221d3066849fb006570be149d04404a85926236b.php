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
    <title><?php echo e(__('Installing App - Server Requirement')); ?></title>
    
  </head>
  <body>
    <?php echo $__env->make('admin.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
      <div class="preL display-none">
        <div class="preloader3 display-none"></div>
      </div>

   		<div class="container">
   			<div class="card">
          <div class="card-header">
              <h3 class="m-3 text-center text-dark ">
                  <?php echo e(__('Server Requirement')); ?>

              </h3>
          </div>
   				<div class="card-body" id="stepbox">
               <form autocomplete="off" action="<?php echo e(route('server.check')); ?>" id="step1form" method="POST" class="needs-validation" novalidate>
                  <?php echo csrf_field(); ?>
                  <?php
                    $servercheck= array();
                  ?>
                  <div class="form-row">
                      <br>
                     <div class="col-md-12">
                        <table class="table table-bordered table-striped">
                          <thead>
                            <tr>
                              <th><?php echo e(__('php extension')); ?></th>
                              <th><?php echo e(__('Status')); ?></th>
                            </tr>
                          </thead>

                          <tbody>

                             <tr>
                                <?php
                                  $v = phpversion();
                                ?>
                              <td><?php echo e(__('php version')); ?> (<b><?php echo e($v); ?></b>)</td>
                              <td>
                               
                               <?php if($v > 8.1): ?>
                                 
                                 <i class="text-success fa fa-check-circle"></i>
                                 <?php
                                   array_push($servercheck, 1);
                                 ?>
                               <?php else: ?>
                                <i class="text-danger fa fa-times-circle"></i>
                                 <i class="text-success fa fa-times-circle"></i> <?php echo e(__('php version required greater than 8.1')); ?>

                                 <?php
                                   array_push($servercheck, 0);
                                 ?>
                               <?php endif; ?>
                              </td>
                            </tr>

                             <tr>
                              <td><?php echo e(__('pdo')); ?></td>
                              <td>
                               
                                  <?php if(extension_loaded('pdo')): ?>
                                       
                                    <i class="text-success fa fa-check-circle"></i> 
                                    <?php
                                      array_push($servercheck, 1);
                                    ?>
                                  <?php else: ?>
                                     <i class="text-danger fa fa-times-circle"></i>
                                     <?php
                                      array_push($servercheck, 0);
                                     ?>
                                  <?php endif; ?>
                              </td>
                            </tr>

                             <tr>
                              <td><?php echo e(__('BCMath')); ?></td>
                              <td>
                               
                                  <?php if(extension_loaded('BCMath')): ?>
                                       
                                    <i class="text-success fa fa-check-circle"></i> 
                                    <?php
                                      array_push($servercheck, 1);
                                    ?>
                                  <?php else: ?>
                                     <i class="text-danger fa fa-times-circle"></i>
                                     <?php
                                      array_push($servercheck, 0);
                                     ?>
                                  <?php endif; ?>
                              </td>
                            </tr>

                             <tr>
                              <td><?php echo e(__('openssl')); ?></td>
                              <td>
                               
                                  <?php if(extension_loaded('openssl')): ?>
                                       
                                    <i class="text-success fa fa-check-circle"></i> 
                                     <?php
                                      array_push($servercheck, 1);
                                    ?>
                                  <?php else: ?>
                                     <i class="text-danger fa fa-times-circle"></i>
                                     <?php
                                      array_push($servercheck, 0);
                                     ?>
                                  <?php endif; ?>
                              </td>
                            </tr>

                            <tr>
                              <td><?php echo e(__('json')); ?></td>
                              <td>
                               
                                  <?php if(extension_loaded('json')): ?>
                                       
                                    <i class="text-success fa fa-check-circle"></i> 
                                    <?php
                                      array_push($servercheck, 1);
                                    ?>
                                  <?php else: ?>
                                     <i class="text-danger fa fa-times-circle"></i>
                                     <?php
                                      array_push($servercheck, 0);
                                     ?>
                                  <?php endif; ?>
                              </td>
                            </tr>

                            <tr>
                              <td><?php echo e(__('session')); ?></td>
                              <td>
                               
                                  <?php if(extension_loaded('session')): ?>
                                       
                                    <i class="text-success fa fa-check-circle"></i> 
                                     <?php
                                      array_push($servercheck, 1);
                                    ?>
                                  <?php else: ?>
                                     <i class="text-danger fa fa-times-circle"></i>
                                     <?php
                                      array_push($servercheck, 0);
                                    ?>
                                  <?php endif; ?>
                              </td>
                            </tr>

                             <tr>
                              <td><?php echo e(__('gd')); ?></td>
                              <td>
                               
                                  <?php if(extension_loaded('gd')): ?>
                                       
                                    <i class="text-success fa fa-check-circle"></i> 
                                    <?php
                                      array_push($servercheck, 1);
                                    ?>
                                  <?php else: ?>
                                     <i class="text-danger fa fa-times-circle"></i>
                                     <?php
                                      array_push($servercheck, 0);
                                     ?>
                                  <?php endif; ?>
                              </td>
                            </tr>

                            
                            <tr>
                              <td><?php echo e(__('allow_url_fopen')); ?></td>
                              <td>
                               
                                  <?php if(ini_get('allow_url_fopen')): ?>
                                       
                                    <i class="text-success fa fa-check-circle"></i> 
                                     <?php
                                      array_push($servercheck, 1);
                                    ?>
                                  <?php else: ?>
                                     <i class="text-danger fa fa-times-circle"></i>
                                      <?php
                                      array_push($servercheck, 0);
                                     ?>
                                  <?php endif; ?>
                              </td>
                            </tr>
                            

                             <tr>
                              <td><?php echo e(__('xml')); ?></td>
                              <td>
                               
                                  <?php if(extension_loaded('xml')): ?>
                                       
                                    <i class="text-success fa fa-check-circle"></i> 
                                     <?php
                                      array_push($servercheck, 1);
                                    ?>
                                  <?php else: ?>
                                     <i class="text-danger fa fa-times-circle"></i>
                                     <?php
                                      array_push($servercheck, 0);
                                     ?>
                                  <?php endif; ?>
                              </td>
                            </tr>

                             <tr>
                              <td><?php echo e(__('tokenizer')); ?></td>
                              <td>
                               
                                  <?php if(extension_loaded('tokenizer')): ?>
                                       
                                    <i class="text-success fa fa-check-circle"></i> 
                                     <?php
                                      array_push($servercheck, 1);
                                    ?>
                                  <?php else: ?>
                                     <i class="text-danger fa fa-times-circle"></i>
                                     <?php
                                      array_push($servercheck, 0);
                                    ?>
                                  <?php endif; ?>
                              </td>
                            </tr>
                             <tr>
                              <td><?php echo e(__('standard')); ?></td>
                              <td>
                               
                                  <?php if(extension_loaded('standard')): ?>
                                       
                                    <i class="text-success fa fa-check-circle"></i> 
                                     <?php
                                      array_push($servercheck, 1);
                                    ?>
                                  <?php else: ?>
                                     <i class="text-danger fa fa-times-circle"></i>
                                     <?php
                                      array_push($servercheck, 0);
                                    ?>
                                  <?php endif; ?>
                              </td>
                            </tr>

                            <tr>
                              <td><?php echo e(__('mysqli')); ?></td>
                              <td>
                               
                                  <?php if(extension_loaded('mysqli')): ?>
                                       
                                    <i class="text-success fa fa-check-circle"></i> 
                                     <?php
                                      array_push($servercheck, 1);
                                    ?>
                                  <?php else: ?>
                                     <i class="text-danger fa fa-times-circle"></i>
                                     <?php
                                      array_push($servercheck, 0);
                                    ?>
                                  <?php endif; ?>
                              </td>
                            </tr>

                            <tr>
                              <td><?php echo e(__('mbstring')); ?></td>
                              <td>
                               
                                  <?php if(extension_loaded('mbstring')): ?>
                                       
                                    <i class="text-success fa fa-check-circle"></i> 
                                     <?php
                                      array_push($servercheck, 1);
                                    ?>
                                  <?php else: ?>
                                     <i class="text-danger fa fa-times-circle"></i>
                                     <?php
                                      array_push($servercheck, 0);
                                    ?>
                                  <?php endif; ?>
                              </td>
                            </tr>

                             <tr>
                              <td><?php echo e(__('ctype')); ?></td>
                              <td>
                               
                                  <?php if(extension_loaded('ctype')): ?>
                                       
                                    <i class="text-success fa fa-check-circle"></i> 
                                     <?php
                                      array_push($servercheck, 1);
                                    ?>
                                  <?php else: ?>
                                     <i class="text-danger fa fa-times-circle"></i>
                                     <?php
                                      array_push($servercheck, 0);
                                    ?>
                                  <?php endif; ?>
                              </td>
                            </tr>

                          <tr>
                            <td><?php echo e(__('fileinfo')); ?></td>
                              <td>
                               
                                  <?php if(extension_loaded('fileinfo')): ?>
                                       
                                    <i class="text-success fa fa-check-circle"></i> 
                                     <?php
                                      array_push($servercheck, 1);
                                    ?>
                                  <?php else: ?>
                                     <i class="text-danger fa fa-times-circle"></i>
                                     <?php
                                      array_push($servercheck, 0);
                                     ?>
                                  <?php endif; ?>
                              </td>
                          </tr>

                          

                          <tr>
                            <td><b><?php echo e(storage_path()); ?></b><?php echo e(__(' is writable?')); ?></td>
                            <td>
                              <?php
                                $path = storage_path();
                              ?>
                              <?php if(is_writable($path)): ?>
                               <i class="text-success fa fa-check-circle"></i> 
                              <?php else: ?>
                                <i class="text-danger fa fa-times-circle"></i>
                              <?php endif; ?>
                            </td>
                          </tr>

                          <tr>
                            <td><b><?php echo e(base_path('bootstrap/cache')); ?></b> <?php echo e(__('is writable?')); ?></td>
                            <td>
                              <?php
                                $path = base_path('bootstrap/cache');
                              ?>
                              <?php if(is_writable($path)): ?>
                                <i class="text-success fa fa-check-circle"></i> 
                              <?php else: ?>
                                <i class="text-danger fa fa-times-circle"></i>
                              <?php endif; ?>
                            </td>
                          </tr>



                          </tbody>
                        </table>
                     </div>
                     
                  </div>
                  <?php if(!in_array(0, $servercheck)): ?>
                    <button class="float-right step1btn btn btn-primary" type="submit"><?php echo e(__('Next')); ?></button>
                  <?php else: ?>
                    <p class="pull-right text-danger"><b><?php echo e(__('Some extension are missing. Contact your host provider for enable it.')); ?></b></p>
                  <?php endif; ?>
              </form>
   				</div>
   			</div>
        <p class="text-center m-3 text-white">&copy;<?php echo e(date('Y')); ?> <?php echo e(__('| eClass - Learning Management System | Installer v1.1 |')); ?> <a class="text-white" href="http://mediacity.co.in"><?php echo e(__('Media City')); ?></a></p>
   		</div>
      
    <div class="corner-ribbon bottom-right sticky green shadow"><?php echo e(__('Server Check')); ?> </div>
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
          var forms = document.getElementsByClassName('needs-validation');
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
<?php /**PATH D:\xampp8.1\htdocs\Personal\theteacher.pk\Admin\resources\views/server.blade.php ENDPATH**/ ?>