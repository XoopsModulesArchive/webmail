<?php
/*
 * $Header: /cvsroot/xoopsbrasil/xoops2/modules/webmail/download.php,v 1.1 2006/03/27 08:18:47 mikhail Exp $
 *
 * Copyright 2001 Nicolas Chalanset <nicocha@free.fr>
 * Copyright 2001 Olivier Cahagne <cahagn_o@epita.fr>
 *
 * See the enclosed file COPYING for license information (GPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * File for downloading the attachments
 */

if (eregi('MSIE', $HTTP_USER_AGENT) || eregi('Internet Explorer', $HTTP_USER_AGENT)) {
    session_cache_limiter('public');
}

header('Content-Type: application/x-unknown-' . $mime);
// IE 5.5 is weird, the line is not correct but it works
if (eregi('MSIE', $HTTP_USER_AGENT) && eregi('5.5', $HTTP_USER_AGENT)) {
    header('Content-Disposition: filename=' . urldecode($filename));
} else {
    header('Content-Disposition: attachment; filename=' . urldecode($filename));
}

session_start();

echo 'serveur ' . $servr . ' user ' . $user . ' mail ' . $mail . ' pass ' . $passwd . '<br>';
$pop = imap_open('{' . $servr . '}' . $folder, $user, $passwd);
$file = imap_fetchbody($pop, $mail, $part);
imap_close($pop);
if ('BASE64' == $transfer) {
    $file = imap_base64($file);
} elseif ('QUOTED-PRINTABLE' == $transfer) {
    $file = imap_qprint($file);
}

header('Content-Length: ' . mb_strlen($file));
echo($file);
