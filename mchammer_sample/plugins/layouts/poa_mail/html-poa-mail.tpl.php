 <?php
/**
 * @file
 * Example html.tpl.php for a mailtemplate: Modern template from campaign monitor.
 */
global $base_url;
$image_path = $base_url . '/' . drupal_get_path('module', 'mchammer_sample') . '/plugins/layouts/poa_mail/images';
?>

<html lang="en">
  <head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <title>
      Port of Antwerp
    </title>
    <style type="text/css">

      p, div {
        font-family:Arial; font-size:13px; line-height:1.15em;
      }

      .view-mode-blokvariant_1 .group-left {
        float: left;
        width: 20%;
      }

      .view-mode-blokvariant_1 .group-right {
        float: right;
        width: 80%;
      }

      .view-mode-blokvariant_2 .group-left {
        float: left;
        width: 80%;
      }

      .view-mode-blokvariant_2 .group-right {
        float: right;
        width: 20%;
      }

      .group-footer {
        clear: both;
      }

      .clearfix {
        clear: both;
      }

      .view-mode-blokvariant_3 {
        width: 250px;
        float: left;
        clear: none;
        padding-right: 20px;
      }
      .

    </style>
  </head>
  <body style="margin: 0; padding: 0; background: #F2F2F2;">
    <table cellpadding="0" cellspacing="0" border="0" align="center" width="800" style="background:#fff; padding:30px 30px; padding-bottom:10px;">
      <tr>
        <td align="left"><?php print theme('image', array('path' => $image_path . '/poa-logo.gif')) ?></td>
        <td align="right"><h1>Radar</h1></td>
      </tr>
      <tr>
        <td colspan="2">
          <?php print $page; ?>
        </td>
      </tr>
    </table>
  </body>
</html>