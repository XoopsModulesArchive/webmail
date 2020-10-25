<!-- start of $Id: html_top_table.php,v 1.1 2006/03/27 08:18:49 mikhail Exp $ -->
<?php
$arrow = (0 == $sortdir) ? 'up' : 'down';
$new_sortdir = (0 == $sortdir) ? 1 : 0;
$is_Imap = is_Imap($servr);
?>
<table border="0" align="center" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td>
            <form method="post" action="index.php?op=delete" name="delete_form">
                <input type="hidden" name="wbmid" value="<?php echo $wbmid; ?>">
                <table width="100%" cellpadding="2" cellspacing="1" border="0">
                    <tr>
                        <td <?php if (($is_Imap) || ($have_ucb_pop_server)) {
    echo 'colspan="5"';
} else {
    echo 'colspan="4"';
} ?>align="left" class="titlew">
                            <b><?php echo $compte . ' : ' . $folder ?></b>
                        </td>
                        <td class="titlew">
                            <?php echo $current_date ?>
                        </td>
                        <td align="right" class="titlew">
                            <?php echo $num_msg ?><?php if (1 == $num_msg) {
    echo _WBM_HTML_MSG;
} else {
    echo _WBM_HTML_MSGS;
} ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" class="inbox" width="40">
                            <?php echo _WBM_HTML_SELECT ?>
                        </td>
                        <?php if (($is_Imap) || ($have_ucb_pop_server)) { ?>
                            <td align="center" class="inbox">
                                <?php echo _WBM_HTML_NEW ?>
                            </td>
                        <?php } ?>
                        <td align="center" class="inbox">
                            &nbsp; <?php // echo $html_att?>
                        </td>
                        <td align="center" class="inbox">
                            <a href="<?php echo $PHP_SELF ?>?wbmid=<?php echo $wbmid ?>&sort=2&amp;sortdir=<?php echo $new_sortdir ?>&amp;">
                                <img src="images/<?php echo $arrow; ?>.gif" border="0" width="12" height="12" alt="<?php echo _WBM_HTML_SORT_BY . ' ' . _WBM_HTML_FROM; ?>"></a>
                            &nbsp;
                            <a href="<?php echo $PHP_SELF ?>?wbmid=<?php echo $wbmid ?>&sort=2&amp;sortdir=<?php echo $new_sortdir ?>&amp;">
                                <?php echo _WBM_HTML_FROM ?></a>
                        </td>
                        <td align="center" class="inbox">
                            <a href="<?php echo $PHP_SELF ?>?wbmid=<?php echo $wbmid ?>&sort=3&amp;sortdir=<?php echo $new_sortdir ?>&amp;">
                                <img src="images/<?php echo $arrow ?>.gif" border="0" width="12" height="12" alt="<?php echo _WBM_HTML_SORT_BY . ' ' . _WBM_HTML_SUBJECT; ?>"></a>
                            &nbsp;
                            <a href="<?php echo $PHP_SELF ?>?wbmid=<?php echo $wbmid ?>&sort=3&amp;sortdir=<?php echo $new_sortdir ?>&amp;">
                                <?php echo _WBM_HTML_SUBJECT ?></a>
                        </td>
                        <td align="center" class="inbox">
                            <a href="<?php echo $PHP_SELF ?>?wbmid=<?php echo $wbmid ?>&sort=1&amp;sortdir=<?php echo $new_sortdir ?>&amp;">
                                <img src="images/<?php echo $arrow ?>.gif" border="0" width="12" height="12" alt="<?php echo _WBM_HTML_SORT_BY . ' ' . _WBM_HTML_DATE; ?>"></a>
                            &nbsp;
                            <a href="<?php echo $PHP_SELF ?>?wbmid=<?php echo $wbmid ?>&sort=1&amp;sortdir=<?php echo $new_sortdir ?>&amp;">
                                <?php echo _WBM_HTML_DATE ?></a>
                        </td>
                        <td align="right" class="inbox">
                            <a href="<?php echo $PHP_SELF ?>?wbmid=<?php echo $wbmid ?>&sort=6&amp;sortdir=<?php echo $new_sortdir ?>&amp;">
                                <img src="images/<?php echo $arrow ?>.gif" border="0" width="12" height="12" alt="<?php echo _WBM_HTML_SORT_BY . ' ' . _WBM_HTML_SIZE; ?>"></a>
                            &nbsp;
                            <a href="<?php echo $PHP_SELF ?>?wbmid=<?php echo $wbmid ?>&sort=6&amp;sortdir=<?php echo $new_sortdir ?>&amp;">
                                <?php echo _WBM_HTML_SIZE ?></a>
                        </td>
                    </tr>
