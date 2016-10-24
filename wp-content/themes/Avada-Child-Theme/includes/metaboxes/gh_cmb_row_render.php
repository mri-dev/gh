<?php

function gh_checbox_render_row_cb( $field_args, $field ) {
	$classes     = $field->row_classes();
	$id          = $field->args( 'id' );
	$label       = $field->args( 'name' );
	$name        = $field->args( '_name' );
	$value       = $field->escaped_value();
	$description = $field->args( 'description' );
	?>
  <div class="gh-user-meta-checkbox <?php echo $classes; ?>">
    <input type="checkbox" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo $value; ?>"><label for="<?php echo $id; ?>"><?php echo $label; ?></label>
    <p class="description"><?php echo $description; ?></p>
  </div>
	<?php
}

?>
