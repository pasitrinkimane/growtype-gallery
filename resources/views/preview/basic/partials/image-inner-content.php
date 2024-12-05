<?php if (isset($caption) && $caption) { ?>
    <div class="caption">
        <?php echo empty($image_url ?? '') && !empty($caption_link) ? '<a href="' . $caption_link . '" target="_blank">' . $caption . '</a>' : $caption ?>
    </div>
<?php } ?>
<?php if (isset($loader_active) && $loader_active) { ?>
    <div class="loader">
        <div class="loader-inner" data-progress="0"></div>
    </div>
<?php } ?>
<?php if (isset($overlay) && $overlay) { ?>
    <div class="overlay" style="background-color:<?php echo $overlay_color; ?>;">
        <?php if ($overlay_icon) { ?>
            <div class="icon">
                <span class="dashicons dashicons-search"></span>
            </div>
        <?php } ?>
    </div>
<?php } ?>
<?php if (isset($watermark) && $watermark) { ?>
    <p class="watermark"><?php echo $watermark ?></p>
<?php } ?>
