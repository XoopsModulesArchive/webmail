<?php
echo "\n<!-- HTML_INBOX-->\n"; ?>

    <tr class=bg<?php echo(($i_inbox % 2) + 1) ?>>
        <td align="center">
            <input type="checkbox" name="msg-<?php echo $tmp['number'] ?>" value="Y">
        </td>
        <?php if (($is_Imap) || ($have_ucb_pop_server)) { ?>
            <td align="center">
                <?php echo $tmp['new'] ?>
            </td>
        <?php } ?>
        <td align="center">
            <?php echo $tmp['attach'] ?>
        </td>
        <td class="inbox" align="left">
            <?php
            $buf = "<a href=$PHP_SELF?op=aff_mail&wbmid=" . $wbmid . '&mail=';
            $buf .= $tmp['number'] . '&sort=' . $sort . '&sortdir=' . $sortdir;
            $buf .= 'verbose=0&title=' . htmlspecialchars($tmp['from'], ENT_QUOTES | ENT_HTML5) . '>';
            //$buf=display_address ($tmp['from']);
            //echo $buf."\n";
            $buf .= htmlspecialchars($tmp['from'], ENT_QUOTES | ENT_HTML5) . '</a>';
            echo $buf . "\n";
            ?>
        </td>
        <td class="inbox" align="left">
            <a href="<?php echo $PHP_SELF ?>?op=aff_mail&wbmid=<?php echo $wbmid ?>&mail=<?php echo $tmp['number'] ?>&amp;sort=<?php echo $sort ?>&amp;sortdir=<?php echo $sortdir ?>&amp;verbose=0"><?php echo $tmp['subject'] ? htmlspecialchars($tmp['subject'], ENT_QUOTES | ENT_HTML5) : _WBM_HTML_NOSUBJECT; ?></a>
        </td>
        <td class="inbox" align="left">
            <?php echo $tmp['date'] ?>
        </td>
        <td class="inbox">
            <?php echo $tmp['size'] ?><?php echo _WBM_HTML_KB ?>
        </td>
    </tr>
<?php echo "\n<!-- ----------------- -->\n"; ?>
