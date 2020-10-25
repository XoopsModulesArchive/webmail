<?php
/*
 * $Header: /cvsroot/xoopsbrasil/xoops2/modules/webmail/check_lang.php,v 1.1 2006/03/27 08:18:47 mikhail Exp $
 *
 * Copyright 2001 Nicolas Chalanset <nicocha@free.fr>
 * Copyright 2001 Olivier Cahagne <cahagn_o@epita.fr>
 *
 * See the enclosed file COPYING for license information (GPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * Sets the language (default is browser language if it exists else
 * it's $default_lang)
 */

if (!isset($lang)) {
    $ar_lang = explode(',', $HTTP_ACCEPT_LANGUAGE);

    while ($accept_lang = array_shift($ar_lang)) {
        $tmp = explode(';', $accept_lang);

        $tmp[0] = mb_strtolower($tmp[0]);

        if (file_exists('./lang/' . $tmp[0] . '.php')) {
            $lang = $tmp[0];

            break;
        }
    }

    if ('' == $lang) {
        $lang = $default_lang;
    }
}
//  Fix for faulty PHP install (RH7, see bug #24933)
$lang = trim($lang);
require './lang/' . $lang . '.php';
