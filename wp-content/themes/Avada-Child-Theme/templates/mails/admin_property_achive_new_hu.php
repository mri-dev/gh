<?php
  $url = get_option('siteurl', '');
?>
<h1>Új archiválási kérelmet indítottak.</h1>
<p>
  <strong><?=$post->Title()?></strong> (<?=$post->Azonosito()?>) ingatlanhirdetésnél archiválási kérelmet ingított el <strong><?=$who->Name()?></strong> (<?=$who->Email()?>).
</p>
<h3>Archiválás indoklása</h3>
<p style="color: #869b3a; font-size: 15px; margin-bottom: 10px;">
  <em><?=$comment?></em>
</p>
<h3>Műveletek</h3>
<p>
  <div>
    <strong>Archiválási kérelmek listája:</strong> <a href="<?=$url?>/control/archive_requests"><?=$url?>/control/archive_requests</a>
  </div>
  <div>
    <strong>Ingatlan adatlapja:</strong> <a href="<?=$post->URL()?>"><?=$post->URL()?></a>
  </div>
  <div>
    <strong>Ingatlan szerkesztése:</strong> <a href="<?=$url?>/control/property_edit/?id=<?=$post->ID()?>"><?=$url?>/control/property_edit/?id=<?=$post->ID()?></a>
  </div>
</p>
