(function($){
  var check_fav_ids = [];
  check_property_favorites();

  $('*[favchecker]').click(function(){
    var pid = $(this).data('fav-pid');
    $(this).removeClass('fa-heart').addClass('fa-spinner fa-spin');
    $.post('/wp-admin/admin-ajax.php?action=property_fav_action', {
      id: pid
    }, function(d){
      var re = $.parseJSON(d);
      if (re.error == 0) {
        check_property_favorites(function(){
          $('*[favchecker][data-fav-pid='+re.id+']').removeClass('fa-spinner fa-spin').addClass('fa-heart');
        });
      }
      console.log(re);
    },"html");
  });

  function check_property_favorites( callback ) {
    $('*[favchecker]').each(function(i,e){
      var pid = $(this).data('fav-pid');
      if ( $.inArray(pid, check_fav_ids) === -1 ) {
        check_fav_ids.push(pid);
      }
    });

    $.post('/wp-admin/admin-ajax.php?action=check_property_fav',{
      ids : check_fav_ids
    }, function( d ){
      var re = $.parseJSON(d);
      //console.log(re);
      if(re.num != 0) {
        $('#notification-favorite').text(re.num).addClass('has');
        $('*[favchecker]').removeClass('hl');
        $(re.in_fav).each(function(i,e){
          $('*[favchecker][data-fav-pid='+e+']').addClass('hl');
        });
      } else {
        $('*[favchecker]').removeClass('hl');
        $('#notification-favorite').removeClass('has').text('');
      }

      if (typeof callback !== 'undefined') {
        callback();
      }
    }, "html");
  }
})(jQuery);
