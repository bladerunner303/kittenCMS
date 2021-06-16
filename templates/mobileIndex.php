<div id="div-menu" style="background-color:black;height: 32px" >
    <?php print $menuHtml; ?>
</div>
<table style="margin-left: auto; margin-right:auto">
  <tr>
    <td>
      <?php print $headerHtml; ?>
    </td>
  </tr>
  <tr>
    <td style="vertical-align: top">
      <div id="div-content" style="width:<?php print $contentLength; ?>;"/>
    </td>
  </tr>
  <tr>
    <td style="text-align:right;font-size: 6px;">
      <div id="div-last-modified"/>
    </td>
  </tr>
  <tr>
    <td >
      <?php print $footerHtml; ?>
    </td>
  </tr>
</table>
