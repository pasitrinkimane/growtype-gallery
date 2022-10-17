<a <?php echo $link_to === 'lightbox' ? 'data-fancybox="' . $group_name . '"' : '' ?>
    class="<?php echo $link_to === 'lightbox' ? 'fancybox' : '' ?> wp-block-image-inner"
    href="<?php echo $src; ?>"
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
</a>
