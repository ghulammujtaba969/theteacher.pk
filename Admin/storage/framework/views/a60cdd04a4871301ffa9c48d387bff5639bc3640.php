<?php if(session('status') == 'error'): ?>
    <div class="alert alert-danger">
        <?php echo e(session('message')); ?>

    </div>
<?php endif; ?>

<div class="offcanvas-menu">
    <div class="row">
        <div class="col-lg-8">
            <h4 class="ai-title"><img src="<?php echo e(url('admin_assets/assets/images/svg-icon/ai_2.svg')); ?>" class="img-fluid" alt="<?php echo e(__('Ai Tool')); ?>">Ai Tool</h4>
        </div>
        <div class="col-lg-4">
            <span class="menu-close"><i class="feather icon-x"></i></span>
        </div>
    </div>
    <hr>
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pills-text-tab" data-toggle="pill" data-target="#pills-text" type="button" role="tab" aria-controls="pills-text" aria-selected="true">Text</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-image-tab" data-toggle="pill" data-target="#pills-image" type="button" role="tab" aria-controls="pills-image" aria-selected="false">Image</button>
        </li>
    </ul>
    <?php
        $services = App\Service::where('status','1')->get();
    ?>
    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-text" role="tabpanel" aria-labelledby="pills-text-tab">
            <form id="mytext" class="openai_generator_form">
                <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="ai_service"><?php echo e(__('Service')); ?></label>
                            <select name="service" class="form-control" id="service" required>
                                <option value=""><?php echo e(__('Select Service')); ?></option>
                                <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($service->id); ?>"><?php echo e($service->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                                                  
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="language"><?php echo e(__('Enter Language')); ?></label>
                            <select class="form-control" id="language">
                                <option value="English">English</option>
                                <option value="Arabic">Arabic</option>
                                <option value="French">French</option>
                                <option value="Hindi">Hindi</option>
                                <option value="Spanish">Spanish</option>
                            </select>
                        </div>
                    </div>
                </div>                                    
                <div class="form-group mb-3">
                    <label for="ai_keyword"><?php echo e(__('Enter your keyword')); ?></label>
                    <input type="text" class="form-control" id="keyword" placeholder="">
                </div>                                    
                <div class="ai-generate-btn">
                    <button type="submit" class="btn btn-primary service_btn">Generate</button>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="generator_sidebar_table">
                            <?php echo $__env->make('admin.openai.output', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>
                    </div>
                </div> 
            </form>
        </div>
        <div class="tab-pane fade" id="pills-image" role="tabpanel" aria-labelledby="pills-image-tab">
            <form id="openai_generator_form2" onsubmit="return generatorFormImage();">
                <div class="form-group mb-3">
                    <label for="ai_keyword_img"><?php echo e(__('Enter your keyword')); ?></label>
                    <input type="text" class="form-control" id="description" placeholder="" required>
                </div>  
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="ai_service_img"><?php echo e(__('No of Images')); ?></label>
                            <select name="image_number_of_images" class="form-control" id="image_number_of_images">
                                <option value=1>1</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="ai_image"><?php echo e(__('Enter Image Size')); ?></label>
                            <select class="form-control" id="size">
                                <option value=256x256>256x256</option>
                                <option value=512x512>512x512</option>
                                <option value=1024x1024>1024x1024</option>
                            </select>
                        </div>
                    </div>
                </div>                                                                      
                <div class="ai-generate-btn">
                    <button id="image-generator" type="submit" class="btn btn-primary generate-btn-text">Generate</button>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ai-text-result">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="image-output">
                                        <?php echo $__env->make('admin.openai.image', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="offcanvas-overly"></div>
<?php /**PATH C:\laragon\www\eclass_purchasecopy\resources\views/admin/openai/topbar.blade.php ENDPATH**/ ?>