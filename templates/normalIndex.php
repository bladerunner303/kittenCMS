<table style="margin-left: auto; margin-right:auto">
  <tr>
    <td colspan="2">
      <?php print $headerHtml; ?>
    </td>
  </tr>
  <tr>
    <td style="vertical-align:top">
      <div id="div-menu" style="width:<?php print $menuLength; ?>; " >
        <?php print $menuHtml; ?>
      </div>
    </td>
    <td style="vertical-align: top">
      <div id="div-content" style="width:<?php print $contentLength; ?>; font-size: small;"/>
    </td>
  </tr>
  <tr>
    <td colspan="2" style="text-align:right;font-size: 10px;">
      <div id="div-last-modified"/>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <?php print $footerHtml; ?>
    </td>
  </tr>
</table>
