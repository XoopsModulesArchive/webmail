<?php
/*
 * $Header: /cvsroot/xoopsbrasil/xoops2/modules/webmail/get_img.php,v 1.1 2006/03/27 08:18:47 mikhail Exp $
 *
 * Copyright 2001 Nicolas Chalanset <nicocha@free.fr>
 * Copyright 2001 Olivier Cahagne <cahagn_o@epita.fr>
 *
 * See the enclosed file COPYING for license information (GPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 */

//session_register ('user', 'passwd');
//require_once ('./conf.php');
//require_once ('./functions.php');
//$passwd = safestrip($passwd);
session_start();
//echo "serveur ".$servr." user ".$user." mail ".$mail." pass ".$passwd."<br>";
$pop = @imap_open('{' . $servr . '}INBOX', $user, $passwd);
$img = imap_fetchbody($pop, $mail, $num);
imap_close($pop);
if ('BASE64' == $transfer) {
    $img = base64_decode($img, true);
} elseif ('QUOTED-PRINTABLE' == $transfer) {
    $img = imap_qprint($img);
}

header('Content-type: image/' . $mime);
echo $img;
