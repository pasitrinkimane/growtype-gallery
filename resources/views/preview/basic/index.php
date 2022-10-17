<figure class="<?php echo $parent_class; ?>">
    <?php if ($link_to === 'none') { ?>
        <?php include('partials/image-inner.php') ?>
    <?php } else { ?>
        <?php include('partials/image-inner-link.php') ?>
    <?php } ?>
</figure>
