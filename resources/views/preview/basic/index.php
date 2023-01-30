<figure style="padding: <?php echo $image_padding; ?>;" class="<?php echo $parent_class; ?>" data-index="<?php echo $index; ?>" data-img-preview-format="<?php echo $image_preview_format; ?>">
    <?php if ($link_to === 'none') { ?>
        <?php include('partials/image-inner.php') ?>
    <?php } else { ?>
        <?php include('partials/image-inner-link.php') ?>
    <?php } ?>
</figure>
