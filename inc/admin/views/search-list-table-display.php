<?php
/**
 * The admin area of the plugin to load the Search List Table
 */
?>

<div class="wrap">
  <h2><?php _e( 'Search Entries', $this->plugin_text_domain); ?></h2>
  <div id="amwnlogos-search-entries">
    <div id="nds-post-body">
			<form id="amwnlogos-search-list-form" method="get">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
				<?php
					$this->search_list_table->search_box( __( 'Find', $this->plugin_text_domain ), 'amwnlogos-entry-find');
					$this->search_list_table->display();
				?>
			</form>
    </div>
  </div>
</div>
