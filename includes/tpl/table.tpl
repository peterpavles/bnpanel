<div class="table" style="{$PROPS}">
    <div class="cat">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>
           <span class="cat_title">{$HEADER}</span>
          </td>
          <td align="right">
          	<a href="Javascript: tblshowhide('{$ID}', 'img{$ID}', '{$url}')" class="expand">
          		<img border="0" id="img{$ID}" src="{$url}themes/icons/bullet_toggle_minus.png" />
          	</a>
          </td>
        </tr>
      </table>
    </div>
    <div class="text" id="{$ID}">{$CONTENT}</div>
    <div class="catend"><!-- no content here --></div>
</div>