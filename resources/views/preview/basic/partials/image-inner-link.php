<?php if ($image_preview_format === 'background_img') { ?>
    <a <?php echo $link_to === 'lightbox' ? 'data-fancybox="' . $group_name . '"' : '' ?>
        class="<?php echo $link_to === 'lightbox' ? 'fancybox' : '' ?> wp-block-image-inner"
        href="<?php echo $src; ?>"
        alt="<?php echo $alt; ?>"
        title="<?php echo $caption; ?>"
        style="background: url(<?php echo $img_preview_url; ?>);
            background-position: center;
            background-size: cover;
            padding-top: <?php echo $image_height; ?>;
            border-radius: <?php echo $image_border_radius; ?>;
            "
    >
        <?php include('image-inner-content.php') ?>
    </a>
    <?php
} else {
    ?>
    <a <?php echo $link_to === 'lightbox' ? 'data-fancybox="' . $group_name . '"' : '' ?>
        class="<?php echo $link_to === 'lightbox' ? 'fancybox' : '' ?> wp-block-image-inner"
        href="<?php echo $src; ?>"
        alt="<?php echo $alt; ?>"
        title="<?php echo $caption; ?>"
    >
        <img style="border-radius: <?php echo $image_border_radius; ?>;" src="<?php echo $img_preview_url; ?>" alt="<?php echo $alt; ?>" title="<?php echo $caption; ?>" width="<?php echo $img_width ?>" height="<?php echo $img_height; ?>">
        <?php include('image-inner-content.php') ?>
    </a>
    <?php
}
?>
