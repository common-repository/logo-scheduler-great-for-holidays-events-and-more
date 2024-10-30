<?php

/**
 * The plugin area to process the table bulk actions.
 */
?>

<?php if ( current_user_can('edit_users' ) ): ?>
	<h2> <?php echo __('Process bulk operations for the selected entries: <br />', $this->plugin_text_domain ); ?> </h2>
	<h4>
		<ul>
		<?php
			foreach( $bulk_entry_ids as $entry_id ) {
				$user = get_user_by( 'id', $entry_id );
				echo '<li>' . $user->display_name . ' (' . $user->user_login . ')' . '</li>';
			}
		?>
		</ul>
	</h4>
	<div class="card">
		<h4> This where you would perform the operations. </h4>
	</div>
	<br>
	<a href="<?php echo esc_url( add_query_arg( array( 'page' => wp_unslash( $_REQUEST['page'] ) ) , admin_url( 'users.php' ) ) ); ?>"><?php _e( 'Back', $this->plugin_text_domain ) ?></a>

<?php else: ?>
	<p> <?php echo __( 'You are not authorized to perform this operation.', $this->plugin_text_domain ) ?> </p>
<?php endif; ?>
