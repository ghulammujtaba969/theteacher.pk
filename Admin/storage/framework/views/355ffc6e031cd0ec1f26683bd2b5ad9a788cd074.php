<div class="ai-generate-image scroll-down">
    <?php if(isset($imageUrl)): ?>
    <img src="<?php echo e($imageUrl ?? ''); ?>" class="img-fluid" alt="">
    <?php endif; ?>
    <div class="img-output-icon">
        <ul>
            <li><a href="<?php echo e($imageUrl ?? ''); ?>" title="Download" download><i class="feather icon-download"></i></a></li>
            <li><a href="<?php echo e($imageUrl ?? ''); ?>" data-lightbox="homePortfolio" title="View" target="_blank"><i class="feather icon-eye"></i></a></li>
        </ul>
    </div>
</div><?php /**PATH C:\laragon\www\eclass_purchasecopy\resources\views/admin/openai/image.blade.php ENDPATH**/ ?>