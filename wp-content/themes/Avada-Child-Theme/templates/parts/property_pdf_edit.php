<h3><?=__('Dokumentumok (pdf)', 'gh')?></h3>
<div class="row">
  <div class="col-md-12">
    <button class="fusion-button button-green button-round" type="button" onclick="addPDFUploadRow();"><i class="fa fa-plus-circle"></i> <?=__('PDF hozzáadás')?></button>
    <div class="pdf-docs-holder" id="pdf-docs">
      <?php if(!$docs): ?>
        <em id="no-pdf"><?=__('Jelenleg nincs PDF-dokumentum az ingatlannál.', 'gh')?></em>
      <?php else: ?>
        <?php foreach ($docs as $doc): ?>
          <div class="pdf-row-add row-added" id="pdf-ur<?=$doc->ID?>">
            <div class="row">
              <div class="col-md-4">
                <a href="<?=$doc->guid?>" class="preview-link" target="_blank"><i class="fa fa-file-pdf-o"></i> <?=__('Dokumentum megtekintése', 'gh')?></a>
                <div class="up-date">
                  <?=$doc->post_date?>
                </div>
              </div>
              <div class="col-md-7"><input class="form-control" type="text" name="pdf_up_name[]" value="<?=$doc->post_title?>" placeholder="Dokumentum címe"></div>
              <div class="col-md-1"><input id="pdf_<?=$doc->ID?>" type="checkbox" name="extra[deleting_pdf][<?=$doc->ID?>]" value="1"><label class="del-lab" for="pdf_<?=$doc->ID?>"><?=__('töröl', 'gh')?></label></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
<script type="text/javascript">
  var added_pdf_elements = 0;
  function addPDFUploadRow()
  {
    var $ = jQuery;
    added_pdf_elements++;

    if (added_pdf_elements > 0) {
      $("#no-pdf").hide(0);
    } else {
      $("#no-pdf").show(0);
    }

    var elem =
    "<div class='pdf-row-add' id='pdf-r"+added_pdf_elements+"'>"+
      "<div class='row'>"+
        "<div class='col-md-4'><input class='form-control' type='file' name='pdf[]'/></div>"+
        "<div class='col-md-7'><input class='form-control' type='text' name='pdf_name[]' value='' placeholder='<?=__('Dokumentum címe','gh')?>'/></div>"+
        "<div class='col-md-1'><a href='javascript:void(0);' onclick='removePDFAdder("+added_pdf_elements+");' class='row-remove'><i class='fa fa-times'></i></a></div>"+
      "</div>"+
    "</div>";

    $(elem).appendTo("#pdf-docs");
  }

  function removePDFAdder(i) {
    var $ = jQuery;
    $('#pdf-r'+i).remove();
    added_pdf_elements--;
    if (added_pdf_elements > 0) {
      $("#no-pdf").hide(0);
    } else {
      $("#no-pdf").show(0);
    }
  }
</script>
