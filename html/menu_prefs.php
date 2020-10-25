<!-- start of $Id: menu_prefs.php,v 1.1 2006/03/27 08:18:49 mikhail Exp $ -->
<table border="0" align="center" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td bgcolor="<?php echo $glob_theme->inside_color ?>">
            <table border="0" cellpadding="2" cellspacing="1" bgcolor="<?php echo $glob_theme->inside_color ?>" width="100%">
                <tr>
                    <td class="menu" align="center" width="120" bgcolor="<?php echo $glob_theme->menu_color ?>">
                        <a href="<?php echo $PHP_SELF ?>?lang=<?php echo $lang ?>&amp;sort=<?php echo $sort ?>&amp;sortdir=<?php echo $sortdir ?>" class="menu"><?php echo $html_inbox ?></a>
                    </td>
                    <td class="menu" align="center" width="120" bgcolor="<?php echo $glob_theme->menu_color ?>">
                        <a href="<?php echo $PHP_SELF ?>?action=write&amp;lang=<?php echo $lang ?>&amp;sort=<?php echo $sort ?>&amp;sortdir=<?php echo $sortdir ?>" class="menu"><?php echo $html_new_msg ?></a>
                    </td>
                    <td width="*" bgcolor="<?php echo $glob_theme->menu_color ?>">
                        <img src="themes/<?php echo $theme ?>/img/spacer.gif" height="1" width="1" alt="">
                    </td>
                    <td class="menu" align="center" width="80" bgcolor="<?php echo $glob_theme->menu_color_on ?>">
                        <font color="<?php echo $glob_theme->link_color ?>"><?php echo $html_preferences ?></font>
                    </td>
                    <?php if ($enable_logout) { ?>
                        <td class="menu" align="center" width="80" bgcolor="<?php echo $glob_theme->menu_color ?>">
                            <a href="logout.php?lang=<?php echo $lang ?>" class="menu"><?php echo $html_logout ?></a>
                        </td>
                    <?php } ?>
                    <!--<td class="menu" align="center" width="80" bgcolor="<?php echo $glob_theme->menu_color ?>">
						<a href="javascript:void(null)" onMouseUp="OpenHelpWindow('help.php?action=<?php echo $action ?>&amp;lang=<?php echo $lang ?>&amp;sort=<?php echo $sort ?>&amp;sortdir=<?php echo $sortdir ?>','image','scrollbars=yes,resizable=yes,width=400,height=300')" class="menu"><?php echo $html_help ?></a>
					</td> -->
                </tr>
            </table>
        </td>
    </tr>
</table>
<!-- end of $Id: menu_prefs.php,v 1.1 2006/03/27 08:18:49 mikhail Exp $ -->
