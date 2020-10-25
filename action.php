<?php
/*
 * $Header: /cvsroot/xoopsbrasil/xoops2/modules/webmail/action.php,v 1.1 2006/03/27 08:18:47 mikhail Exp $
 *
 * Copyright 2001 Nicolas Chalanset <nicocha@free.fr>
 * Copyright 2001 Olivier Cahagne <cahagn_o@epita.fr>
 *
 * See the enclosed file COPYING for license information (GPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * This file is the main file of NOCC each function starts from here
 */

//require_once ('./conf.php');
//require_once ('./check_lang.php');
//require_once ('./functions.php');
//require_once ('./prefs.php');

if (!session_is_registered('loggedin'))
	$action = '';
require_once __DIR__ . '/html/header.php';

$user   = safestrip($user);
$passwd = safestrip($passwd);

if (setlocale(LC_TIME, $lang_locale) != $lang_locale) {
    $default_date_format = $no_locale_date_format;
}
$current_date = strftime($default_date_format, time());

// Get preferences
$prefs_full_name     = getPref('full_name');
$prefs_email_address = getPref('email_address');
$prefs_signature     = getSig();

// Default e-mail address on send form
if ($prefs_email_address != "") {
    $mail_from = $prefs_email_address;
} else {
    $mail_from = $user . "@" . $domain;
}
if ($prefs_full_name != "") {
    $mail_from = $prefs_full_name . " <" . $mail_from . ">";
}

if (!isset($action)) {
    $action = '';
}

switch (trim($action)) {
    case 'aff_mail':
        // Here we display the message
        require __DIR__ . '/html/menu_mail.php';
        require_once __DIR__ . '/html/html_mail_top.php';
        $content = aff_mail($servr, $user, $passwd, $folder, $mail, $verbose, $lang, $sort, $sortdir);
        require_once __DIR__ . '/html/html_mail_header.php';
        while ($tmp = array_shift($attach_tab)) {
            // $attach_tab is the array of attachments
            // If it's a text/plain, display it
            if ((!preg_match('ATTACHMENT', $tmp['disposition'])) && $display_text_attach && (preg_match('text/plain', $tmp['mime']))) {
                echo '<hr>' . view_part($servr, $user, $passwd, $folder, $mail, $tmp['number'], $tmp['transfer'], $tmp['charset'], $charset);
            }
            if ($display_img_attach && (preg_match('image', $tmp['mime']) && ($tmp['number'] != ''))) {
                // if it's an image, display it
                $img_type = array_pop(explode('/', $tmp['mime']));
                if (preg_match('JPEG', $img_type) || preg_match('JPG', $img_type) || preg_match('GIF', $img_type) || preg_match('PNG', $img_type)) {
                    echo '<hr>';
                    echo '<center>';
                    echo '<p>' . $html_loading_image . ' ' . $tmp['name'] . "...</p>";
                    echo '<img src="get_img.php?' . $php_session . '=' . $$php_session . '&amp;mail=' . $mail . '&amp;folder=' . $folder . '&amp;num=' . $tmp['number'] . '&amp;mime=' . $img_type . '&amp;transfer=' . $tmp['transfer'] . '">';
                    echo '</center>';
                }
            }
        }
        require_once __DIR__ . '/html/html_mail_bottom.php';
        require __DIR__ . '/html/menu_mail.php';
        break;

    case 'logout':
        header("Location: " . $base_url . "logout.php?lang=$lang&$php_session=" . $$php_session);
        break;

    case 'write':
        // Add signature
        $mail_body = "\r\n" . $prefs_signature;

        $num_attach = 0;
        require __DIR__ . '/html/menu_inbox.php';
        require_once __DIR__ . '/html/send.php';
        require __DIR__ . '/html/menu_inbox.php';
        break;

    case 'reply':
        $content = aff_mail($servr, $user, $passwd, $folder, $mail, 0, $lang, $sort, $sortdir);
        $mail_to = !empty($content['reply_to']) ? $content['reply_to'] : $content['from'];
        // Test for Re: in subject, should not be added twice !
        if (!strcasecmp(substr($content['subject'], 0, 2), $html_reply_short)) {
            $mail_subject = $content['subject'];
        } else {
            $mail_subject = $html_reply_short . ': ' . $content['subject'];
        }

        // Set body
        $outlook_quoting = getPref('outlook_quoting');
        if ($outlook_quoting) {
            $mail_body = $original_msg . "\n" . $html_from . ': ' . $content['from'] . "\n" . $html_to . ': ' . $content['to'] . "\n" . $html_sent . ': ' . $content['complete_date'] . "\n" . $html_subject . ': ' . $content['subject'] . "\n\n" . strip_tags($content['body'], '');
        } else {
            $mail_body = mailquote(strip_tags($content['body'], ''), $content['from'], $html_wrote);
        }

        // Add signature
        $mail_body .= "\r\n\r\n" . $prefs_signature;

        // We add the attachments of the original message
        //list($num_attach, $attach_array) = save_attachment($servr, $user, $passwd, $folder, $mail, $tmpdir);
        // Registering the attachments array into the session
        //session_register('num_attach', 'attach_array');
        require __DIR__ . '/html/menu_inbox.php';
        require_once __DIR__ . '/html/send.php';
        require __DIR__ . '/html/menu_inbox.php';
        break;

    case 'reply_all':
        $content = aff_mail($servr, $user, $passwd, $folder, $mail, 0, $lang, $sort, $sortdir);
        $mail_to = get_reply_all($user, $domain, $content['from'], $content['to'], $content['cc']);
        if (!strcasecmp(substr($content['subject'], 0, 2), $html_reply_short)) {
            $mail_subject = $content['subject'];
        } else {
            $mail_subject = $html_reply_short . ': ' . $content['subject'];
        }
        // Set body
        $outlook_quoting = getPref('outlook_quoting');
        if ($outlook_quoting) {
            $mail_body = $original_msg . "\n" . $html_from . ': ' . $content['from'] . "\n" . $html_to . ': ' . $content['to'] . "\n" . $html_sent . ': ' . $content['complete_date'] . "\n" . $html_subject . ': ' . $content['subject'] . "\n\n" . strip_tags($content['body'], '');
        } else {
            $mail_body = mailquote(strip_tags($content['body'], ''), $content['from'], $html_wrote);
        }

        // Add signature
        $mail_body .= "\r\n" . $prefs_signature;

        // We add the attachments of the original message
        //list($num_attach, $attach_array) = save_attachment($servr, $user, $passwd, $folder, $mail, $tmpdir);
        // Registering the attachments array into the session
        //session_register('num_attach', 'attach_array');
        require __DIR__ . '/html/menu_inbox.php';
        require_once __DIR__ . '/html/send.php';
        require __DIR__ . '/html/menu_inbox.php';
        break;

    case 'forward':
        $content      = aff_mail($servr, $user, $passwd, $folder, $mail, 0, $lang, $sort, $sortdir);
        $mail_subject = $html_forward_short . ': ' . $content['subject'];
        $mail_body    = $original_msg . "\n" . $html_from . ': ' . $content['from'] . "\n" . $html_to . ': ' . $content['to'] . "\n" . $html_sent . ': ' . $content['complete_date'] . "\n" . $html_subject . ': ' . $content['subject'] . "\n\n" . strip_tags($content['body'], '');
        // Add signature
        $mail_body .= "\r\n" . $prefs_signature;

        // We add the attachments of the original message
        [$num_attach, $attach_array] = save_attachment($servr, $user, $passwd, $folder, $mail, $tmpdir);
        // Registering the attachments array into the session
        session_register('num_attach', 'attach_array');
        require __DIR__ . '/html/menu_inbox.php';
        require_once __DIR__ . '/html/send.php';
        require __DIR__ . '/html/menu_inbox.php';
        break;

    case 'setprefs':
        if (isset($submit_prefs)) {
            $lastev = '';

            // Full name
            if (!$lastev && isset($full_name)) {
                $ev = setPref('full_name', $full_name);
                if (Exception::isException($ev)) {
                    $lastev = $ev;
                }
            }

            // Email address
            if (!$lastev && isset($email_address)) {
                $ev = setPref('email_address', $email_address);
                if (Exception::isException($ev)) {
                    $lastev = $ev;
                }
            }

            // CC Self
            if (!$lastev) {
                if (isset($cc_self) && $cc_self == 'on') {
                    $ev = setPref('cc_self', $cc_self);
                    if (Exception::isException($ev)) {
                        $lastev = $ev;
                    }
                } else {
                    $ev = setPref('cc_self', '');
                    if (Exception::isException($ev)) {
                        $lastev = $ev;
                    }
                }
            }

            // Hide Addresses
            if (!$lastev) {
                if (isset($hide_addresses) && $hide_addresses == 'on') {
                    $ev = setPref('hide_addresses', $hide_addresses);
                    if (Exception::isException($ev)) {
                        $lastev = $ev;
                    }
                } else {
                    $ev = setPref('hide_addresses', '');
                    if (Exception::isException($ev)) {
                        $lastev = $ev;
                    }
                }
            }

            // Outlook-style quoting
            if (!$lastev) {
                if (isset($outlook_quoting) && $outlook_quoting == 'on') {
                    $ev = setPref('outlook_quoting', $outlook_quoting);
                    if (Exception::isException($ev)) {
                        $lastev = $ev;
                    }
                } else {
                    $ev = setPref('outlook_quoting', '');
                    if (Exception::isException($ev)) {
                        $lastev = $ev;
                    }
                }
            }

            if (!$lastev && $signature != "") {
                $ev = setSig($signature);
                if (Exception::isException($ev)) {
                    $lastev = $ev;
                }
            }

            // Handle an errors that occurred
            if (Exception::isException($lastev)) {
                $ev = $lastev;
                require __DIR__ . '/html/prefs_error.php';
                break;
            }
        }
        $full_name       = getPref('full_name');
        $email_address   = getPref('email_address');
        $cc_self         = getPref('cc_self');
        $hide_addresses  = getPref('hide_addresses');
        $outlook_quoting = getPref('outlook_quoting');
        $signature       = getSig();
        require __DIR__ . '/html/menu_prefs.php';
        require_once __DIR__ . '/html/prefs.php';
        require __DIR__ . '/html/menu_prefs.php';
        break;

    default:
        // Default we display the mailbox
        if (!isset($servr) || !isset($passwd)) {
            require_once __DIR__ . '/wrong.php';
            break;
        }
        $tab_mail = inbox($servr, $user, $passwd, $folder, $sort, $sortdir, $lang, $theme);
        switch ($tab_mail) {
            case -1:
                // -1 either the login and/or the password are wrong or the server is down
                require_once __DIR__ . '/wrong.php';
                break;
            case 0:
                $loggedin = 1;
                session_register('loggedin');
                // the mailbox is empty
                $num_msg = 0;
                require __DIR__ . '/html/menu_inbox.php';
                require_once __DIR__ . '/html/html_top_table.php';
                require_once __DIR__ . '/html/no_mail.php';
                require_once __DIR__ . '/html/html_bottom_table.php';
                require __DIR__ . '/html/menu_inbox.php';
                break;
            default:
                if (!isset($attach_array)) {
                    $attach_array = null;
                }
                go_back_index($attach_array, $tmpdir, $php_session, $sort, $sortdir, $lang, false);
                $loggedin = 1;
                session_register('loggedin');
                // there are messages, we display
                $num_msg = count($tab_mail);
                require __DIR__ . '/html/menu_inbox.php';
                require_once __DIR__ . '/html/html_top_table.php';
                require __DIR__ . '/html/menu_inbox_opts.php';
                while ($tmp = array_shift($tab_mail)) {
                    require __DIR__ . '/html/html_inbox.php';
                }
                require __DIR__ . '/html/menu_inbox_opts.php';
                require_once __DIR__ . '/html/html_bottom_table.php';
                require __DIR__ . '/html/menu_inbox.php';
                break;
        }
        break;
}

require_once __DIR__ . '/html/footer.php';
?>
