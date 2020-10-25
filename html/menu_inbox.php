<!-- start of $Id: menu_inbox.php,v 1.1 2006/03/27 08:18:49 mikhail Exp $ -->
<?php

//if ($op == '')
{
    $line = '<a href="' . $PHP_SELF . '?op=write&wbmid=' . $wbmid . 'sort=' . $sort . '&amp;sortdir=' . $sortdir . '" class="menu">' . _WBM_HTML_NEW_MSG . '</a>';
}

if ('write' == $op) {
    $line = '->' . _WBM_HTML_NEW_MSG;
}
if ('reply' == $op) {
    $line = '->' . _WBM_HTML_REPLY;
}
if ('reply_all' == $op) {
    $line = '->' . _WBM_HTML_REPLY_ALL;
}
if ('forward' == $op) {
    $line = '->' . _WBM_HTML_FORWARD;
}
?>

<tr>
    <td>
        <table border="0" cellpadding="2" cellspacing="1" width="100%">
            <tr>
                <td class="bg1">
                    <a href="<?php echo $PHP_SELF ?>?wbmid=<?php echo $wbmid ?>&sort=<?php echo $sort ?>&sortdir=<?php echo $sortdir ?>" class="menu"><?php echo _WBM_HTML_INBOX ?></a>
                </td>
            </tr>
            <tr>
                <td class="bg2">
                    <?php echo $line ?>
                </td>
            </tr>
            <tr>
                <td width="*" class=bg1>
                    <img src="images/spacer.gif" height="1" width="1" alt="">
                </td>
            </tr>
            <?php if ($prefs_dir) { ?>
                <tr>
                    <td class="bg2">
                        <a href="<?php echo $PHP_SELF ?>?op=setprefs" class="menu"><?php echo _WBM_HTML_PREFERENCES ?></a>
                    </td>
                </tr>
            <?php } ?>
            <?php if ($enable_logout) { ?>
            <tr>
                <td class="bg1">
                    <a href="logout.php?" class="menu"><?php echo _WBM_HTML_LOGOUT; ?></a>
                </td>
            </tr>
            <tr>
                <?php } ?>
                <!--
     <td class="bg2" >
     <a href="javascript:void(null)" onMouseUp="OpenHelpWindow('help.php?action=<?php echo $action ?>&amp;lang=<?php echo $lang ?>&amp;sort=<?php echo $sort ?>&amp;sortdir=<?php echo $sortdir ?>','image','scrollbars=yes,resizable=yes,width=400,height=300')" class="menu"><?php echo _WBM_HTML_HELP; ?></a>
     </td> 
     -->
            </tr>
        </table>
    </td>
</tr>

<!-- end of $Id: menu_inbox.php,v 1.1 2006/03/27 08:18:49 mikhail Exp $ -->
