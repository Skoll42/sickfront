<div class="sickfront-label"><?php _e('Choose Website', 'sickfront'); ?></div>
<div class="dropdown">
    <button class="btn btn-default dropdown-toggle form-control" type="button" data-toggle="dropdown">
        <span>Choose Website</span>
        <span class="caret"></span></button>
    <ul class="dropdown-menu">
        <?php foreach ($this->plugin->pages as $page): ?>
            <li><a href="<?php echo $page->getAdminURL(); ?>"><?php echo $page->getPageName()?></a></li>
        <?php endforeach; ?>
    </ul>
</div>