<?php
  $phone = '06 72 222 404';
  $email = get_option('admin_email', true);
  $sitename = get_option('blogname', false);

  $url = get_option('siteurl', '');
  $url_kereso = $url.'/ingatlanok';
  $url_rolunk = $url.'/rolunk';
  $url_kapcsolat = $url.'/kapcsolat';

?>
<!DOCTYPE html>
<html>
<head>
<title>Email</title>
<style>
  body {
    background: #f5f5f5;
    padding: 0;
    margin: 0;
    font-size: 13px;
    font-family: 'Arial';
  }
  .content{
    width:640px;
    margin: 0 auto;
  }
  .textc {
    padding: 15px;
  }
  h1, h2, h3{
    color: #222222;
    margin: 10px 0 0 0;
  }
  a:link, a:visited {
    color: #ce5b5b;
    text-decoration: none;
  }
  header {
    background: #333b3b;
    color: #dedede;
  }
  header .textc {
    text-align: center;
  }
  .ins-content{
    background: #ffffff;
    color: #6f6f6f;
  }
  header .foot{
    background: #c33a3a;
  }
  header .foot a{
    color: white;
  }
  header .foot td{
    width: 33.333%;
    text-align: center;
    font-size: 14px;
    padding: 2px;
    border-right: 1px solid #f54d4d;
  }
  header .foot{
    padding: 5px 0;
  }
  header .foot td:last-child{
    border-right: none;
  }
  header .foot td a{
    color: white;
    font-size: 10px;
    font-weight: bold;
    text-transform: uppercase;
    text-decoration: none;
  }
  footer {
    background: #404040;
    color: #e8e8e8 !important;
  }
  table {
    width: 100%;
  }
  table, td, tr, th {
    border: none;
    margin: 0;
    padding: 0;
  }
  footer table td{
    font-size: 12px;
    width: 33.3333%;
    text-align: center;
  }
  footer table td.logo {
    text-align: left;
  }
  footer a{
    color: #d44343;
  }
  footer .info .h {
    line-height: 24px;
    font-size: 12px;
    color: #848484;
  }
  footer .info .t {
    color: #e8e8e8 !important;
  }


</style>
</head>
<body>
  <div class="content">
    <header>
      <div class="textc">
        <img src="<?=IMG?>/logo_mail.png" alt="<?=$sitename?>">
      </div>
      <div class="foot">
        <table>
          <tr>
            <td><a href="<?=$url_kereso?>"><?=__('Ingatlan keresés', 'gh')?></a></td>
            <td><a href="<?=$url_rolunk?>"><?=__('Rólunk', 'gh')?></td>
            <td><a href="<?=$url_kapcsolat?>"><?=__('Kapcsolat', 'gh')?></td>
          </tr>
        </table>
      </div>
    </header>
    <div class="ins-content">
      <div class="textc">
        <?php echo $content; ?>
      </div>
    </div>
    <footer>
      <div class="textc">
        <table>
          <tr>
            <td class="logo">
              <img src="<?=IMG?>/logo_mail.png" height="45" alt="<?=$sitename?>">
            </td>
            <td>
              <div class="info">
                <div class="h"><?=__('Telefon', 'gh')?></div>
                <div class="t"><?=$phone?></div>
              </div>
            </td>
            <td>
              <div class="info">
                <div class="h"><?=__('Email', 'gh')?></div>
                <div class="t"><a href="mailto:<?=$email?>"><?=$email?></a></div>
              </div>
            </td>
          </tr>
        </table>
      </div>
    </footer>
  </div>
</body>
</html>
