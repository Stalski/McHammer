 <?php
/**
 * @file
 * Example html.tpl.php for a mailtemplate: Modern template from campaign monitor.
 */
global $base_url;
$image_path = $base_url . '/' . drupal_get_path('module', 'mchammer_sample') . '/plugins/layouts/modern/images';
?>

<html lang="en">
  <head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <title>
      Modern
    </title>
    <style type="text/css">
      a:hover { text-decoration: none !important; }
      .header h1 {color: #47c8db !important; font: bold 32px Helvetica, Arial, sans-serif; margin: 0; padding: 0; line-height: 40px;}
      .header p {color: #c6c6c6; font: normal 12px Helvetica, Arial, sans-serif; margin: 0; padding: 0; line-height: 18px;}
      .sidebar table.toc-table  { color: #767676; margin: 0; padding: 0; font-size: 12px;font-family: Helvetica, Arial, sans-serif; }
      .sidebar table.toc-table td {padding: 0 0 5px; margin: 0;}
      .sidebar h4{color:#eb8484 !important; font-size: 11px;line-height: 16px;font-family: Helvetica, Arial, sans-serif; margin: 0; padding: 0;}
      .sidebar p {color: #989898; font-size: 11px;line-height: 16px;font-family: Helvetica, Arial, sans-serif; margin: 0; padding: 0;}
      .sidebar p a{color: #0eb6ce; text-decoration: none;}
      .content h2 {color:#646464 !important; font-weight: bold; margin: 0; padding: 0; line-height: 26px; font-size: 18px; font-family: Helvetica, Arial, sans-serif;  }
      .content p {color:#767676; font-weight: normal; margin: 0; padding: 0; line-height: 20px; font-size: 12px;font-family: Helvetica, Arial, sans-serif;}
      .content a {color: #0eb6ce; text-decoration: none;}
      .footer p {font-size: 11px; color:#7d7a7a; margin: 0; padding: 0; font-family: Helvetica, Arial, sans-serif;}
      .footer a {color: #0eb6ce; text-decoration: none;}
    </style>
  </head>
  <body style="margin: 0; padding: 0; background: #4b4b4b url('<?php print $image_path ?>/bg_email.png');" bgcolor="#4b4b4b">
    <?php print $page; ?>
  </body>
</html>