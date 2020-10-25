<?php

echo "\n<!--deb menu_inbox_opts -->\n";
?>

<tr>
    <td colspan="7">
        <table border="0" align="center" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td align="left">
                    <input type="button" value="<?php echo _WBM_HTML_SELECT_ALL; ?>" onselect="SelectAll()" onClick="SelectAll()">
                </td>
                <td align="right">
                    <?php if ($delete_button_icon) { ?>
                        <input type="image" src="images/delete.gif" alt="<?php echo _WBM_ALT_DELETE; ?> ">
                    <?php } else { ?>
                        <input type="submit" value="<?php echo _WBM_HTML_DELETE ?>">
                    <?php } ?>
                </td>
            </tr>
        </table>
    </td>
</tr>
<!--fin menu_inbox_opts -->
