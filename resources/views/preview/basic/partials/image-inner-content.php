<?php if ($loader_active) { ?>
    <div class="loader">
        <div class="loader-inner" data-progress="0"></div>
    </div>
<?php } ?>
<?php if ($overlay) { ?>
    <div class="overlay" style="background-color:<?php echo $overlay_color; ?>;">
        <?php if ($overlay_icon) { ?>
            <div class="icon">
                <span class="dashicons dashicons-search"></span>
            </div>
        <?php } ?>
    </div>
<?php } ?>
<?php if ($watermark) { ?>
    <p class="watermark"><?php echo $watermark ?></p>
<?php } ?>
