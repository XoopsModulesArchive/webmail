<?php
/*
 * $Header: /cvsroot/xoopsbrasil/xoops2/modules/webmail/prefs.php,v 1.1 2006/03/27 08:18:47 mikhail Exp $
 *
 * Copyright 2001 Nicolas Chalanset <nicocha@free.fr>
 * Copyright 2001 Olivier Cahagne <cahagn_o@epita.fr>
 *
 * See the enclosed file COPYING for license information (GPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 */

require_once __DIR__ . '/exception.php';

if (!isset(\ < ? php
/*
 * $Header: /cvsroot/xoopsbrasil/xoops2/modules/webmail/prefs.php,v 1.1 2006/03/27 08:18:47 mikhail Exp $
 *
 * Copyright 2001 Nicolas Chalanset <nicocha@free.fr>
 * Copyright 2001 Olivier Cahagne <cahagn_o@epita.fr>
 *
 * See the enclosed file COPYING for license information (GPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 */

require_once __DIR__ . '/exception.php';

if (!session_is_registered('prefs_are_cached')) {
    $prefs_are_cached = false;
    $prefs_cache      = [];
    session_register('prefs_cache');
    session_register('prefs_are_cached');
}

function cachePrefValues($username)
{
    global $prefs_dir, $prefs_are_cached, $prefs_cache;

    if (!$prefs_dir) {
        return;
    }
    if ($prefs_are_cached) {
        return;
    }

    $filename = $prefs_dir . $username . '.pref';
    if (file_exists($filename)) {
        $file = fopen($filename, 'r');
        if (!$file) {
            error_log("Could not open $filename for reading.");
            return;
        }

        /** read in all the preferences **/
        $highlight_num = 0;
        while (!feof($file)) {
            $pref     = trim(fgets($file, 1024));
            $equalsAt = strpos($pref, '=');
            if ($equalsAt > 0) {
                $Key   = substr($pref, 0, $equalsAt);
                $Value = substr($pref, $equalsAt + 1);
                if (substr($Key, 0, 9) == 'highlight') {
                    $Key = 'highlight' . $highlight_num;
                    $highlight_num++;
                }
                if ($Value != '') {
                    $prefs_cache[$Key] = $Value;
                }
            }
        }
        fclose($file);
    }

    $prefs_are_cached = true;
}

/** returns the value for $string *
 * @param $string
 * @return mixed|string
 */
function getPref($string)
{
    global $prefs_dir, $user, $domain, $prefs_cache;

    $username = $user . '@' . $domain;
    cachePrefValues($username);

    if (isset($prefs_cache[$string])) {
        return ($prefs_cache[$string]);
    }
    return ('');
}

function savePrefValues($username)
{
    global $prefs_dir, $prefs_cache, $html_prefs_file_error;

    $filename = $prefs_dir . $username . '.pref';
    if (file_exists($filename) && !is_writable($filename)) {
        return (new Exception($html_prefs_file_error));
    }
    $file = fopen($filename, 'w');
    if (!$file) {
        return (new Exception($html_prefs_file_error));
    }
    foreach ($prefs_cache as $Key => $Value) {
        if (isset($Value)) {
            fwrite($file, $Key . '=' . $Value . "\n");
        }
    }
    fclose($file);
}

function removePref($username, $string)
{
    global $prefs_cache;

    cachePrefValues($username);
    if (isset($prefs_cache[$string])) {
        unset($prefs_cache[$string]);
    }
    return (savePrefValues($username));
}

/** sets the pref, $string, to $set_to *
 * @param $string
 * @param $set_to
 * @return \Exception|void
 */
function setPref($string, $set_to)
{
    global $prefs_dir, $user, $domain, $prefs_cache;

    $username = $user . '@' . $domain;
    cachePrefValues($username);
    if (isset($prefs_cache[$string]) && $prefs_cache[$string] == $set_to) {
        return;
    }
    if ($set_to === '') {
        removePref($username, $string);
        return;
    }
    $prefs_cache[$string] = $set_to;
    return (savePrefValues($username));
}

/** Writes the Signature *
 * @param $string
 * @return \Exception
 */
function setSig($string)
{
    global $prefs_dir, $user, $domain, $html_sig_file_error;

    $username = $user . '@' . $domain;
    $filename = $prefs_dir . $username . '.sig';
    if (file_exists($filename) && !is_writable($filename)) {
        return new Exception($html_sig_file_error);
    }
    $file = fopen($filename, 'w');
    if (!$file) {
        return new Exception($html_sig_file_error);
    }
    fwrite($file, $string);
    fclose($file);
}

/** Gets the signature **/
function getSig()
{
    global $prefs_dir, $user, $domain;

    $username = $user . '@' . $domain;
    $filename = $prefs_dir . $username . '.sig';
    $sig      = '';
    if (file_exists($filename)) {
        $file = fopen($filename, 'r');
        if (!$file) {
            error_log("Could not open $filename for reading.");
            return ('');
        }
        while (!feof($file)) {
            $sig .= fgets($file, 1024);
        }
        fclose($file);
    }
    return ($sig);
}

?>
SESSION['prefs_are_cached'])) {
    $prefs_are_cached = false;
    $prefs_cache      = [];
    session_register('prefs_cache');
    session_register('prefs_are_cached');
}

function cachePrefValues($username)
{
    global $prefs_dir, $prefs_are_cached, $prefs_cache;

    if (!$prefs_dir) {
        return;
    }
    if ($prefs_are_cached) {
        return;
    }

    $filename = $prefs_dir . $username . '.pref';
    if (file_exists($filename)) {
        $file = fopen($filename, 'r');
        if (!$file) {
            error_log("Could not open $filename for reading.");
            return;
        }

        /** read in all the preferences **/
        $highlight_num = 0;
        while (!feof($file)) {
            $pref     = trim(fgets($file, 1024));
            $equalsAt = strpos($pref, '=');
            if ($equalsAt > 0) {
                $Key   = substr($pref, 0, $equalsAt);
                $Value = substr($pref, $equalsAt + 1);
                if (substr($Key, 0, 9) == 'highlight') {
                    $Key = 'highlight' . $highlight_num;
                    $highlight_num++;
                }
                if ($Value != '') {
                    $prefs_cache[$Key] = $Value;
                }
            }
        }
        fclose($file);
    }

    $prefs_are_cached = true;
}

/** returns the value for $string **/
function getPref($string)
{
    global $prefs_dir, $user, $domain, $prefs_cache;

    $username = $user . '@' . $domain;
    cachePrefValues($username);

    if (isset($prefs_cache[$string])) {
        return ($prefs_cache[$string]);
    }
    return ('');
}

function savePrefValues($username)
{
    global $prefs_dir, $prefs_cache, $html_prefs_file_error;

    $filename = $prefs_dir . $username . '.pref';
    if (file_exists($filename) && !is_writable($filename)) {
        return (new Exception($html_prefs_file_error));
    }
    $file = fopen($filename, 'w');
    if (!$file) {
        return (new Exception($html_prefs_file_error));
    }
    foreach ($prefs_cache as $Key => $Value) {
        if (isset($Value)) {
            fwrite($file, $Key . '=' . $Value . "\n");
        }
    }
    fclose($file);
}

function removePref($username, $string)
{
    global $prefs_cache;

    cachePrefValues($username);
    if (isset($prefs_cache[$string])) {
        unset($prefs_cache[$string]);
    }
    return (savePrefValues($username));
}

/** sets the pref, $string, to $set_to **/
function setPref($string, $set_to)
{
    global $prefs_dir, $user, $domain, $prefs_cache;

    $username = $user . '@' . $domain;
    cachePrefValues($username);
    if (isset($prefs_cache[$string]) && $prefs_cache[$string] == $set_to) {
        return;
    }
    if ($set_to === '') {
        removePref($username, $string);
        return;
    }
    $prefs_cache[$string] = $set_to;
    return (savePrefValues($username));
}

/** Writes the Signature **/
function setSig($string)
{
    global $prefs_dir, $user, $domain, $html_sig_file_error;

    $username = $user . '@' . $domain;
    $filename = $prefs_dir . $username . '.sig';
    if (file_exists($filename) && !is_writable($filename)) {
        return new Exception($html_sig_file_error);
    }
    $file = fopen($filename, 'w');
    if (!$file) {
        return new Exception($html_sig_file_error);
    }
    fwrite($file, $string);
    fclose($file);
}

/** Gets the signature **/
function getSig()
{
    global $prefs_dir, $user, $domain;

    $username = $user . '@' . $domain;
    $filename = $prefs_dir . $username . '.sig';
    $sig      = '';
    if (file_exists($filename)) {
        $file = fopen($filename, 'r');
        if (!$file) {
            error_log("Could not open $filename for reading.");
            return ('');
        }
        while (!feof($file)) {
            $sig .= fgets($file, 1024);
        }
        fclose($file);
    }
    return ($sig);
}

?>
