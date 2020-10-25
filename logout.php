<?php
/*
 * $Header: /cvsroot/xoopsbrasil/xoops2/modules/webmail/logout.php,v 1.1 2006/03/27 08:18:47 mikhail Exp $
 *
 * Copyright 2001 Nicolas Chalanset <nicocha@free.fr>
 * Copyright 2001 Olivier Cahagne <cahagn_o@epita.fr>
 *
 * See the enclosed file COPYING for license information (GPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 */

require_once __DIR__ . '/conf.php';

session_start();
$old_theme = $theme;
if (isset($attach_array) && is_array($attach_array)) {
    while ($tmp = array_shift($attach_array)) {
        @unlink($tmpdir . '/' . $tmp->tmp_file);
    }
}
session_destroy();
header('Location: ' . $base_url . "index.php?lang=$lang&theme=$old_theme");
