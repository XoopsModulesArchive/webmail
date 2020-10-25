<?php
/*
 * $Header: /cvsroot/xoopsbrasil/xoops2/modules/webmail/delete.php,v 1.1 2006/03/27 08:18:47 mikhail Exp $
 *
 * Copyright 2001 Nicolas Chalanset <nicocha@free.fr>
 * Copyright 2001 Olivier Cahagne <cahagn_o@epita.fr>
 *
 * See the enclosed file COPYING for license information (GPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * this file just delete the selected message(s)
 */

session_register('user', 'passwd');
require_once __DIR__ . '/conf.php';
require_once __DIR__ . '/functions.php';

$pop = @imap_open('{' . $servr . '}INBOX', $HTTP_SESSION_VARS['user'], safestrip($HTTP_SESSION_VARS['passwd']));
$num_messages = imap_num_msg($pop);

if (isset($only_one) && (1 == $only_one)) {
    imap_delete($pop, $mail, 0);
} else {
    for ($i = 1; $i <= $num_messages; $i++) {
        $delete_this_one = $_POST['msg-' . $i];

        if ('Y' == $delete_this_one) {
            imap_delete($pop, $i, 0);
        }
    }
}
imap_close($pop, CL_EXPUNGE);

header('Location: ' . $base_url . "action.php?sort=$sort&sortdir=$sortdir&lang=$lang&$php_session=" . $$php_session);
