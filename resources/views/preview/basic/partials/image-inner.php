<div class="wp-block-image-inner"
     alt="<?php echo $alt; ?>"
     title="<?php echo $caption; ?>"
     style="background: url(<?php echo $preview_src; ?>);
         background-position: center;
         background-size: cover;
         padding-top: <?php echo $image_height; ?>;
         border-radius: <?php echo $image_border_radius; ?>;
         "
>
    <?php include('image-inner-content.php') ?>
</div>
