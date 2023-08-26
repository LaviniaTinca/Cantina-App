<div class="news color">
    <?php if ($announcement) { ?>
        <span class="scrolling-text">
            <?php echo $announcement['description']; ?>
        </span>
    <?php } else { ?>
        <span class="scrolling-text">
            Nu sunt anunțuri disponibile.
        </span>
    <?php } ?>
</div>