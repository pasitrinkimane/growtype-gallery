<?php
if (isset($image_url) && !empty($image_url)) {
    echo '<a href="' . $image_url . '" class="wp-block-image-wrapper" ' . ($link_to === 'popup' && !empty($post_id) ? 'data-bs-toggle="modal" data-bs-target="#' . $img_unique_id . '"' : '') . '>';
}
?>

<?php if (isset($image_preview_format) && $image_preview_format === 'background_img') { ?>
    <div class="wp-block-image-inner"
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
    </div>
<?php } else { ?>
    <div class="wp-block-image-inner"
         alt="<?php echo $alt ?? ''; ?>"
         title="<?php echo $caption ?? ''; ?>"
         style="border-radius: <?php echo $image_border_radius ?? 0; ?>;"
    >
        <img style="border-radius: <?php echo $image_border_radius ?? 0; ?>;" src="<?php echo $img_preview_url; ?>" alt="<?php echo $alt ?? ''; ?>" title="<?php echo $caption ?? ''; ?>" width="<?php echo $img_width ?? '' ?>" height="<?php echo $img_height ?? ''; ?>">
        <?php include('image-inner-content.php') ?>
    </div>
<?php } ?>

<?php
if (isset($image_url) && !empty($image_url)) {
    echo '</a>';
}
?>
