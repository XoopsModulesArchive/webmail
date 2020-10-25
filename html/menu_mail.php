<!-- start of $Id: menu_mail.php,v 1.1 2006/03/27 08:18:49 mikhail Exp $ -->
<table border="0" align="center" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td>
            <table border="0" cellpadding="2" cellspacing="1" width="100%">
                <tr>
                    <td class="menu" align="center">
                        <a href="<?php echo $PHP_SELF ?>?wbmid=<?php echo $wbmid ?>&sort=<?php echo $sort ?>&amp;sortdir=<?php echo $sortdir ?>" class="menu"><?php echo _WBM_HTML_INBOX ?></a>
                    </td>
                    <td class="menu" align="center">
                        <a href="<?php echo $PHP_SELF ?>?wbmid=<?php echo $wbmid ?>&op=write&amp;sort=<?php echo $sort ?>&amp;sortdir=<?php echo $sortdir ?>" class="menu"><?php echo _WBM_HTML_NEW_MSG ?></a>
                    </td>
                    <td class="menu" align="center">
                        <a href="<?php echo $PHP_SELF ?>?wbmid=<?php echo $wbmid ?>&op=reply&amp;mail=<?php echo $mail ?>&amp;sort=<?php echo $sort ?>&amp;sortdir=<?php echo $sortdir ?>" class="menu"><?php echo _WBM_HTML_REPLY ?></a>
                    </td>
                    <td class="menu" align="center">
                        <a href="<?php echo $PHP_SELF ?>?wbmid=<?php echo $wbmid ?>&op=reply_all&amp;mail=<?php echo $mail ?>&amp;sort=<?php echo $sort ?>&amp;sortdir=<?php echo $sortdir ?>" class="menu"><?php echo _WBM_HTML_REPLY_ALL ?></a>
                    </td>
                    <td class="menu" align="center">
                        <a href="<?php echo $PHP_SELF ?>?wbmid=<?php echo $wbmid ?>&op=forward&amp;mail=<?php echo $mail ?>&amp;sort=<?php echo $sort ?>&amp;sortdir=<?php echo $sortdir ?>" class="menu"><?php echo _WBM_HTML_FORWARD ?></a>
                    </td>
                    <td class="menu" align="center">
                        <a href="<?php echo $PHP_SELF ?>?wbmid=<?php echo $wbmid ?>&op=delete&mail=<?php echo $mail ?>&amp;only_one=1&amp;sort=<?php echo $sort ?>&amp;sortdir=<?php echo $sortdir ?>" class="menu"><?php echo _WBM_HTML_DELETE ?></a>
                    </td>
                    <?php if ($enable_logout) { ?>
                        <td class="menu" align="center" width="80">
                            <a href="logout.php?lang=<?php echo $lang ?>" class="menu"><?php echo _WBM_HTML_LOGOUT ?></a>
                        </td>
                    <?php } ?>
                    <!--<td class="menu" align="center" >
      <a href="javascript:void(null)" onMouseUp="OpenHelpWindow('help.php?action=<?php echo $action ?>&amp;sort=<?php echo $sort ?>&amp;sortdir=<?php echo $sortdir ?>','image','scrollbars=yes,resizable=yes,width=400,height=300')" class="menu"><?php echo $html_help ?></a>
     </td>-->
                </tr>
            </table>
        </td>
    </tr>
</table>
<!-- end of $Id: menu_mail.php,v 1.1 2006/03/27 08:18:49 mikhail Exp $ -->
