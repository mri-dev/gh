<label for="post_author"><?=__('Válassza ki az új referenst a listából, akit szeretne beállítani a kiválasztott ingatlanokhoz', 'gh')?>:</label>
<br>
<div class="row">
  <div class="col-md-12">
    <?php
      wp_dropdown_users(array(
        'class' => 'form-control',
        'name' => 'post_author'
      ));
    ?>
  </div>
</div>
