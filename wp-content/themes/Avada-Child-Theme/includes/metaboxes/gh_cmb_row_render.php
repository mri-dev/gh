<?php

function gh_checbox_render_row_cb( $field_args, $field ) {
	$classes     = $field->row_classes();
	$id          = $field->args( 'id' );
	$label       = $field->args( 'name' );
	$name        = $field->args( '_name' );
	$value       = $field->escaped_value();
	$description = $field->args( 'description' );
	$permission_name = str_replace("gh_user_permission_", "", $name);

	$current_user = get_userdata($_GET['user_id']);
	$caps = $current_user->caps;
	?>

  <div class="gh-user-meta-checkbox <?php echo $classes; ?>">
    <input type="checkbox" <?php if(array_key_exists($permission_name, $caps)){ echo "checked='checked'"; }?> name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="1"><label for="<?php echo $id; ?>"><?php echo $label; ?></label>
    <p class="description"><?php echo $description; ?></p>
  </div>
	<?php
}

function gh_regio_select_render_row_cb( $field_args, $field )
{
	$classes     = $field->row_classes();
	$id          = $field->args( 'id' );
	$label       = $field->args( 'name' );
	$name        = $field->args( '_name' );
	$value       = $field->escaped_value();
?>
<?php wp_dropdown_categories(array(
		'class' => $classes,
		'id' => $id,
		'name' => $name,
		'taxonomy' => 'locations',
		'hide_empty' => false,
		'hierarchical' => 1,
		'selected' => $value,
		'show_option_none' => __( '-- válaszon régiót --', 'gh' )
	)); ?>
	<?php
}

?>
