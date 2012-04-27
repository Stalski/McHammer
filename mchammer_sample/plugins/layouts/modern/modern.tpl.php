 <?php
/**
 * @file
 * Example mailtemplate: Modern template from campaign monitor.
 */
global $base_url;
$image_path = $base_url . '/' . drupal_get_path('module', 'mchammer_sample') . '/plugins/layouts/modern/images';
?>
    <table cellpadding="0" cellspacing="0" border="0" align="center" width="100%" style="padding: 35px 0; background: #4b4b4b url('<?php print $image_path ?>/bg_email.png');" bgcolor="#4b4b4b">
      <tr>
        <td align="center" style="margin: 0; padding: 0; background: url('<?php print $image_path ?>/bg_email.png');" >
          <table cellpadding="0" cellspacing="0" border="0" align="center" width="600" style="font-family: Helvetica, Arial, sans-serif;background:#2a2a2a;" class="header">
            <tr>
              <td width="600" align="left" style="padding: font-size: 0; line-height: 0; height: 7px;" height="7" colspan="2"><img src="<?php print $image_path ?>/bg_header.png" alt="header bg"></td>
            </tr>
            <tr>
              <td width="20"style="font-size: 0px;">&nbsp;</td>
              <td width="580" align="left" style="padding: 18px 0 10px;">
                <h1 style="color: #47c8db; font: bold 32px Helvetica, Arial, sans-serif; margin: 0; padding: 0; line-height: 40px;">McHammer</h1>
                <p style="color: #c6c6c6; font: normal 12px Helvetica, Arial, sans-serif; margin: 0; padding: 0; line-height: 18px;">Drupals Most Awesome Newsletter Composer</p>
              </td>
            </tr>
          </table><!-- header-->
          <table cellpadding="0" cellspacing="0" border="0" align="center" width="600" style="font-family: Helvetica, Arial, sans-serif; background: #fff url('<?php print $image_path ?>/bg_table.png') repeat-y;" bgcolor="#fff">
            <tr>
              <td width="186" valign="top" align="left" style="font-family: Helvetica, Arial, sans-serif; background: #fff url('<?php print $image_path ?>/bg_table.png') repeat-y;" bgcolor="#fff" class="sidebar">
                <?php print $content['left']; ?>
              </td>
              <td width="414" valign="top" align="left" style="font-family: Helvetica, Arial, sans-serif; padding: 25px 0 0;" class="content">
                <?php print $content['middle']; ?>
              </td>
            </tr>
            <tr>
              <td width="600" align="left" style="padding: font-size: 0; line-height: 0; height: 3px;" height="3" colspan="2"><img src="<?php print $image_path ?>/bg_bottom.png" alt="header bg"></td>
            </tr>
          </table><!-- body -->
          <table cellpadding="0" cellspacing="0" border="0" align="center" width="600" style="font-family: Helvetica, Arial, sans-serif; line-height: 10px;" class="footer">
            <tr>
              <td align="center" style="padding: 5px 0 10px; font-size: 11px; color:#7d7a7a; margin: 0; line-height: 1.2;font-family: Helvetica, Arial, sans-serif;" valign="top">
                <p style="font-size: 11px; color:#7d7a7a; margin: 0; padding: 0; font-family: Helvetica, Arial, sans-serif;">Having trouble reading this? <webversion style="color: #0eb6ce; text-decoration: none;">View it in your browser</webversion>. Not interested? <unsubscribe style="color: #0eb6ce; text-decoration: none;">Unsubscribe</unsubscribe> instantly.</p>
              </td>
            </tr>
          </table><!-- footer-->
        </td>
      </tr>
    </table>