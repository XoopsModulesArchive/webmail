<?php

/*
 * $Header: /cvsroot/xoopsbrasil/xoops2/modules/webmail/send.php,v 1.1 2006/03/27 08:18:47 mikhail Exp $
 *
 * Copyright 2001 Nicolas Chalanset <nicocha@free.fr>
 * Copyright 2001 Olivier Cahagne <cahagn_o@epita.fr>
 *
 * See the enclosed file COPYING for license information (GPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 */
include 'header.php';
include 'config.php';
require_once $xoopsConfig['root_path'] . 'class/xoopshtmlform.php';

if ('webmail' == $xoopsConfig['startpage']) {
    $xoopsOption['show_rblock'] = 1;

    include $xoopsConfig['root_path'] . 'header.php';

    make_cblock();

    echo '<br>';
} else {
    $xoopsOption['show_rblock'] = 0;

    include $xoopsConfig['root_path'] . 'header.php';
}

require_once $xoopsConfig['root_path'] . 'class/module.textsanitizer.php';
require_once __DIR__ . '/functions.php';
global $xoopsDB, $xoopsConfig, $xoopsTheme;
if (xoopsUser) {
    require_once __DIR__ . '/class_send.php';

    require_once __DIR__ . '/class_smtp.php';

    $mail_from = safestrip($mail_from);

    $mail_to = safestrip($mail_to);

    $mail_cc = safestrip($mail_cc);

    $mail_bcc = safestrip($mail_bcc);

    $mail_subject = safestrip($mail_subject);

    $mail_body = safestrip($mail_body);

    switch (trim($sendaction)) {
        case 'add':
            // Counting the attachments number in the array
            if (!isset($attach_array)) {
                $attach_array = [];

                $num_attach = 1;
            } else {
                $num_attach++;
            }
            $tmp_name = basename($mail_att . '.att');
            // Adding the new file to the array
            if (is_uploaded_file($mail_att)) {
                copy($mail_att, $tmpdir . '/' . $tmp_name);

                $attach_array[$num_attach]->file_name = basename($mail_att_name);

                $attach_array[$num_attach]->tmp_file = $tmp_name;

                $attach_array[$num_attach]->file_size = $mail_att_size;

                $attach_array[$num_attach]->file_mime = $mail_att_type;
            }
            // Registering the attachments array into the session
            //session_unregister('attach_array');
            //session_register('num_attach', 'attach_array');
            // Displaying the sending form with the new attachments array
            header("Content-type: text/html; Charset=$charset");
            require __DIR__ . '/html/header.php';
            require __DIR__ . '/html/menu_inbox.php';
            require __DIR__ . '/html/send.php';
            require __DIR__ . '/html/menu_inbox.php';
            break;
        case 'send':
            $ip = (getenv('HTTP_X_FORWARDED_FOR') ?: getenv('REMOTE_ADDR'));

            $mail = new mime_mail();
            $mail->crlf = get_crlf($smtp_server);
            $mail->smtp_server = $smtp_server;
            $mail->smtp_port = $smtp_port;
            $mail->charset = $charset;
            $mail->from = cut_address(trim($mail_from), $charset);
            $mail->from = $mail->from[0];
            $mail->priority = $priority;
            $mail->headers = 'X-Originating-Ip: [' . $ip . ']' . $mail->crlf . 'X-Mailer: ' . $nocc_name . ' v' . $nocc_version;
            $mail->to = cut_address(trim($mail_to), $charset);
            $mail->cc = cut_address(trim($mail_cc), $charset);
            $cc_self = getPref('cc_self');
            if ('' != $cc_self) {
                array_unshift($mail->cc, $mail->from);
            }
            $mail->bcc = cut_address(trim($mail_bcc), $charset);
            if ('' != $mail_subject) {
                $mail->subject = trim($mail_subject);
            }

            // Append advertisement tag, if set
            if ('' != $mail_body) {
                $mail->body = $mail_body;
            }

            if (isset($ad)) {
                if ('' != $mail_body) {
                    $mail->body = $mail_body . $mail->crlf . $mail->crlf . $ad;
                } else {
                    $mail->body = $ad;
                }
            }

            // Getting the attachments
            if (isset($attach_array)) {
                for ($i = 1; $i <= $num_attach; $i++) {
                    // If the temporary file exists, attach it

                    if (file_exists('temp/' . $attach_array[$i]->tmp_file)) {
                        $fp = fopen('temp/' . $attach_array[$i]->tmp_file, 'rb');

                        $file = fread($fp, $attach_array[$i]->file_size);

                        fclose($fp);

                        // add it to the message, by default it is encoded in base64

                        $mail->add_attachment($file, imap_qprint($attach_array[$i]->file_name), $attach_array[$i]->file_mime, 'base64', '');

                        // then we delete the temporary file

                        unlink('temp/' . $attach_array[$i]->tmp_file);
                    }
                }
            }
            // We need to unregister the attachments array and num_attach
            //session_unregister('num_attach');
            //session_unregister('attach_array');
            $ev = $mail->send();
            header("Content-type: text/html; Charset=$charset");
            if (\Throwable::isException($ev)) {
                // Error while sending the message, display an error message

                require __DIR__ . '/html/header.php';

                require __DIR__ . '/html/menu_inbox.php';

                require __DIR__ . '/html/send_error.php';

                require __DIR__ . '/html/menu_inbox.php';
            } else {
                // Display a confirmation of success

                require __DIR__ . '/html/header.php';

                require __DIR__ . '/html/menu_inbox.php';

                require __DIR__ . '/html/send_confirmed.php';

                require __DIR__ . '/html/menu_inbox.php';
            }
            break;
        case 'delete':
            // Rebuilding the attachments array with only the files the user wants to keep
            $tmp_array = [];
            for ($i = $j = 1; $i <= $num_attach; $i++) {
                $thefile = 'file' . $i;

                if (empty($$thefile)) {
                    $tmp_array[$j]->file_name = $attach_array[$i]->file_name;

                    $tmp_array[$j]->tmp_file = $attach_array[$i]->tmp_file;

                    $tmp_array[$j]->file_size = $attach_array[$i]->file_size;

                    $tmp_array[$j]->file_mime = $attach_array[$i]->file_mime;

                    $j++;
                } else {
                    @unlink($tmpdir . '/' . $attach_array[$i]->tmp_file);
                }
            }
            $num_attach = ($j > 1 ? $j - 1 : 0);
            // Removing the attachments array from the current session
            //session_unregister('num_attach');
            //session_unregister('attach_array');
            $attach_array = $tmp_array;
            // Registering the attachments array into the session
            //session_register('num_attach', 'attach_array');
            // Displaying the sending form with the new attachment array
            header("Content-type: text/html; Charset=$charset");
            require __DIR__ . '/html/header.php';
            require __DIR__ . '/html/menu_inbox.php';
            require __DIR__ . '/html/send.php';
            require __DIR__ . '/html/menu_inbox.php';
            break;
        default:
            // Nothing was set in the sendaction (e.g. no javascript enabled)
            header("Content-type: text/html; Charset=$charset");
            $ev = new Exception((string)$html_no_sendaction);
            require __DIR__ . '/html/header.php';
            require __DIR__ . '/html/menu_inbox.php';
            require __DIR__ . '/html/send_error.php';
            require __DIR__ . '/html/menu_inbox.php';
            break;
    }

    //require_once ('./html/footer.php');
}
