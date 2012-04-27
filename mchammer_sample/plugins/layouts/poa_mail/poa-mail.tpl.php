 <?php
/**
 * @file
 * Example mailtemplate: Modern template from campaign monitor.
 */
?>
<table cellpadding="0" cellspacing="0" border="0" align="center" width="100%">

  <tr>
    <td colspan="2" valign="top">
      <?php print $content['top'] ?>
    </td>
  </tr>

  <tr>
    <td width="80%" valign="top"><?php print $content['left']; ?></td>
    <td width="20%" valign="top"><?php print $content['right']; ?></td>
  </tr>

</table>