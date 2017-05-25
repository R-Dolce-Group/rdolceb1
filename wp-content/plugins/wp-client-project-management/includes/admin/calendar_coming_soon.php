<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<div class='wrap'>
    <?php echo $wpc_client->get_plugin_logo_block(); ?>
    <div class="clear"></div>
    <div class="icon32" id="icon-themes"><br /></div>

    <?php $this->get_breadcrumbs() ?>

    <div class="clear"></div>
    <?php echo $this->gen_project_tabs_menu(); ?>
    <div class="clear"></div>
    <div id="wpc_pm_scheduler" class="wpc_pm_main_block dhx_cal_container" style="text-align: center; padding-top: 100px;">
        <img alt="" src="<?php echo $this->extension_url; ?>/images/Postit-ComingSoon.png" />
    </div>
</div>