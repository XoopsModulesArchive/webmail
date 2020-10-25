<!-- start of $Id: html_mail_header.php,v 1.1 2006/03/27 08:18:49 mikhail Exp $ -->
<?php
if (1 == $verbose && true === $use_verbose) {
    echo "<tr><td class=\"mail\"><a href=\"$PHP_SELF?action=aff_mail&amp;mail=$mail&amp;verbose=0&amp;sort=" . $sort . '&amp;sortdir=' . $sortdir . '" class="mail">' . _WBM_HTML_REMOVE_HEADER . '</a></td>';
} elseif (true === $use_verbose) {
    echo "<tr><td class=\"mail\"><a href=\"$PHP_SELF?action=aff_mail&amp;mail=$mail&amp;verbose=1&amp;sort=" . $sort . '&amp;sortdir=' . $sortdir . '" class="mail">' . _WBM_HTML_VIEW_HEADER . '</a></td>';
} else {
    echo '<tr><td>&nbsp;</td>';
}

if (('' != $content['prev']) && (0 != $content['prev'])) {
    $prev = "<a href=\"$PHP_SELF?wbmid=" . $wbmid . '&op=aff_mail&amp;mail=' . $content['prev'] . '&amp;verbose=' . $verbose . '&amp;sort=' . $sort . '&amp;sortdir=' . $sortdir . "\"><img src=\"images/left_arrow.gif\" alt=\"$alt_prev\" title=\"$alt_prev\" border=\"0\"></a>";
}
if (('' != $content['next']) && (0 != $content['next'])) {
    $next = "<a href=\"$PHP_SELF?wbmid=" . $wbmid . '&op=aff_mail&amp;mail=' . $content['next'] . '&amp;verbose=' . $verbose . '&amp;sort=' . $sort . '&amp;sortdir=' . $sortdir . "\"><img src=\"images/right_arrow.gif\" alt=\"$alt_next\" title=\"$alt_next\" border=\"0\"></a>";
}
?>
<td align="right"><?php if (isset($prev)) {
    echo $prev;
} ?>&nbsp;<?php if (isset($next)) {
    echo $next;
} ?></td></tr>
<tr>
    <td align="right" class="mail"><?php echo _WBM_HTML_FROM ?></td>
    <td class="mail"><b><?php echo htmlspecialchars($content['from'], ENT_QUOTES | ENT_HTML5) ?></b></td>
</tr>

<tr>
    <td align="right" class="mail"><?php echo _WBM_HTML_TO ?></td>
    <td class="mail"><?php echo htmlspecialchars($content['to'], ENT_QUOTES | ENT_HTML5) ?></td>
</tr>

<?php
if ('' != $content['cc']) { ?>
    <tr>
        <td align="right" class="mail"><?php echo _WBM_HTML_CC ?></td>
        <td class="mail"><?php echo htmlspecialchars($content['cc'], ENT_QUOTES | ENT_HTML5) ?></td>
    </tr>
    <?php
}

if ('' == $content['subject']) {
    $content['subject'] = $html_nosubject;
}
?>

<tr>
    <td align="right" class="mail"><?php echo _WBM_HTML_SUBJECT ?></td>
    <td class="mail"><b><?php echo htmlspecialchars($content['subject'], ENT_QUOTES | ENT_HTML5) ?></b></td>
</tr>

<tr>
    <td align="right" class="mail"><?php echo _WBM_HTML_DATE ?></td>
    <td class="mail"><?php echo $content['date'] ?></td>
</tr>

<?php echo $content['att'] ?>

<tr>
    <td colspan="2" class="mail">
        <pre><?php echo $content['header'] ?></pre>
        <br>

        <?php echo $content['body'] ?>
        <!-- end of $Id: html_mail_header.php,v 1.1 2006/03/27 08:18:49 mikhail Exp $ -->
