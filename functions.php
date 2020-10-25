<?php

/*
 * $Header: /cvsroot/xoopsbrasil/xoops2/modules/webmail/functions.php,v 1.1 2006/03/27 08:18:47 mikhail Exp $
 *
 * Copyright 2001 Nicolas Chalanset <nicocha@free.fr>
 * Copyright 2001 Olivier Cahagne <cahagn_o@epita.fr>
 *
 * See the enclosed file COPYING for license information (GPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 */

/* ----------------------------------------------------- */
/*
function CheckDB ()
{

$sqlstr= "CREATE TABLE IF NOT EXISTS ".xoopsDB->prefix("webmail")." (
       wbmid int(5) NOT NULL auto_increment,
       uid int(5) DEFAULT '0' NOT NULL,
       login varchar(50) NOT NULL,
       password varchar(15) NOT NULL,
       serveur varchar(100) NOT NULL,
       type varchar(10) NOT NULL,
       compte varchar(50) NOT NULL,
       PRIMARY KEY (wbmid),
       KEY uid (uid)
    )";
echo $sqlstr;
*/

function inbox($servr, $user, $passwd, $folder, $sort, $sortdir)
{
    $mailhost = $servr;

    //require ('./conf.php');

    global $have_ucb_pop_server;

    $pop = @imap_open('{' . $mailhost . '}' . $folder, $user, $passwd);

    if (false === $pop) {
        return (-1);
    }  

    if (0 == ($num_messages = @imap_num_msg($pop))) {
        imap_close($pop);

        return (0);
    }  

    $sorted = imap_sort($pop, $sort, $sortdir, SE_UID);

    for ($i = 0; $i < $num_messages; $i++) {
        $subject = $from = '';

        $msgnum = $sorted[$i];

        $ref_contenu_message = imap_headerinfo($pop, imap_msgno($pop, $msgnum));

        $struct_msg = imap_fetchstructure($pop, imap_msgno($pop, $msgnum));

        $subject_array = imap_mime_header_decode($ref_contenu_message->subject);

        for ($j = 0, $jMax = count($subject_array); $j < $jMax; $j++) {
            $subject .= $subject_array[$j]->text;
        }

        $from_array = imap_mime_header_decode($ref_contenu_message->fromaddress);

        for ($j = 0, $jMax = count($from_array); $j < $jMax; $j++) {
            $from .= $from_array[$j]->text;
        }

        if (is_Imap($mailhost)) {
            $msg_size = get_mail_size($struct_msg);
        } else {
            $msg_size = ($struct_msg->bytes > 1000) ? ceil($struct_msg->bytes / 1000) : 1;
        }

        if (isset($struct_msg->type) && 1 == $struct_msg->type) {
            if ('ALTERNATIVE' == $struct_msg->subtype || 'RELATED' == $struct_msg->subtype) {
                $attach = '&nbsp;';
            } else {
                //$mail=$i;

                //$para='mail='.$i.'&user='.$user.'&servr='.$servr.'&passwd='.$passwd;

                //$attach = '<a href=download.php?'.$para.'><img src="images/attach.gif" alt=""></a>';

                $attach = '<img src="images/attach.gif" alt=""></a>';
            }
        } else {
            $attach = '&nbsp;';
        }

        // Check Status Line with UCB POP Server to

        // see if this is a new message. This is a

        // non-RFC standard line header.

        // Set this in conf.php

        if ($have_ucb_pop_server) {
            $header_msg = imap_fetchheader($pop, imap_msgno($pop, $msgnum));

            $header_lines = explode("\r\n", $header_msg);

            while (list($k, $v) = each($header_lines)) {
                [$header_field, $header_value] = explode(':', $v);

                if ('Status' == $header_field) {
                    $new_mail_from_header = $header_value;
                }
            }
        } else {
            if (('U' == $ref_contenu_message->Unseen) || ('N' == $ref_contenu_message->Recent)) {
                $new_mail_from_header = '';
            } else {
                $new_mail_from_header = '&nbsp;';
            }
        }

        if ('' == $new_mail_from_header) {
            $newmail = '<img src="images/new.gif" alt="" height="17" width="17">';
        } else {
            $newmail = '&nbsp;';
        }

        /*
        if ($num_messages > 1)
        {
            $next = ($i + 1 < $num_messages) ? imap_msgno($pop, $sorted[$i + 1]) : 0;
            $prev = ($i - 1 > 0) ? imap_msgno($pop, $sorted[$i - 1]) : 0;
        }
        else
        {
            $next = $prev = 0;
        }
        */

        [$date, $complete_date] = change_date(rtrim($ref_contenu_message->udate), $lang);

        $msg_list[$i] = [
                    'new' => $newmail,
'number' => imap_msgno($pop, $msgnum),
                    /*
                    'next' => $next,
                    'prev' => $prev,
                    */
'attach' => $attach,
'from' => $from,
'subject' => $subject,
'date' => $date,
'complete_date' => $complete_date,
'size' => $msg_size,
'sort' => $sort,
'sortdir' => $sortdir,
                ];
    }

    imap_close($pop);

    return ($msg_list);
}

/* ----------------------------------------------------- */

function aff_mail($servr, $user, $passwd, $folder, $mail, $verbose, $sort, $sortdir)
{
    $mailhost = $servr;

    //require ('./conf.php');

    //require ('./check_lang.php');

    global $attach_tab;

    global $PHP_SELF;

    global $display_rfc822;

    global $display_part_no;

    global $display_img_attach;

    global $display_img_attach;

    $glob_body = $subject = $from = $to = $cc = $reply_to = '';

    if (setlocale(LC_TIME, $lang_locale) != $lang_locale) {
        $default_date_format = $no_locale_date_format;
    }

    $pop = @imap_open('{' . $mailhost . '}' . $folder, $user, $passwd);

    // Finding the next and previous message number

    $sorted = imap_sort($pop, $sort, $sortdir);

    $prev_msg = $next_msg = 0;

    for ($i = 0, $iMax = count($sorted); $i < $iMax; $i++) {
        if ($mail == $sorted[$i]) {
            $prev_msg = ($i - 1 >= 0) ? $sorted[$i - 1] : 0;

            $next_msg = ($i + 1 < count($sorted)) ? $sorted[$i + 1] : 0;

            break;
        }
    }

    // END finding the next and previous message number

    $num_messages = @imap_num_msg($pop);

    $ref_contenu_message = @imap_headerinfo($pop, $mail);

    $struct_msg = @imap_fetchstructure($pop, $mail);

    //echo "struct_msg<br>";

    //print_r ($struct_msg);

    if (isset($struct_msg->parts) && (count($struct_msg->parts) > 0)) {
        GetPart($struct_msg, null, $display_rfc822);
    } else {
        GetSinglePart($struct_msg, imap_fetchheader($pop, $mail), @imap_body($pop, $mail));
    }

    if ((1 == $verbose) && (true === $use_verbose)) {
        $header = imap_fetchheader($pop, $mail);
    } else {
        $header = '';
    }

    $tmp = array_pop($attach_tab);

    if (eregi('text/html', $tmp['mime']) || eregi('text/plain', $tmp['mime'])) {
        if ('QUOTED-PRINTABLE' == $tmp['transfer']) {
            $glob_body = imap_qprint(imap_fetchbody($pop, $mail, $tmp['number']));
        } elseif ('BASE64' == $tmp['transfer']) {
            $glob_body = base64_decode(imap_fetchbody($pop, $mail, $tmp['number']), true);
        } else {
            $glob_body = imap_fetchbody($pop, $mail, $tmp['number']);
        }

        $glob_body = remove_stuff($glob_body, $lang, $tmp['mime']);
    } else {
        $attach_tab[] = $tmp;
    }

    @imap_close($pop);

    if ('ALTERNATIVE' != $struct_msg->subtype && 'RELATED' != $struct_msg->subtype) {
        switch (count($attach_tab)) {
            case 0:
                $link_att = '';
                break;
            case 1:
                $link_att = '<tr><td align="right" valign="top" class="mail">' . $html_att . '</td><td  class="mail">' . link_att($mailhost, $mail, $attach_tab, $display_part_no) . '</td></tr>';
                break;
            default:
                $link_att = '<tr><td align="right" valign="top" class="mail">' . $html_atts . '</td><td " class="mail">' . link_att($mailhost, $mail, $attach_tab, $display_part_no) . '</td></tr>';
                break;
        }
    }

    $subject_array = imap_mime_header_decode($ref_contenu_message->subject);

    for ($j = 0, $jMax = count($subject_array); $j < $jMax; $j++) {
        $subject .= $subject_array[$j]->text;
    }

    $from_array = imap_mime_header_decode($ref_contenu_message->fromaddress);

    for ($j = 0, $jMax = count($from_array); $j < $jMax; $j++) {
        $from .= $from_array[$j]->text;
    }

    $to_array = imap_mime_header_decode($ref_contenu_message->toaddress);

    for ($j = 0, $jMax = count($to_array); $j < $jMax; $j++) {
        $to .= $to_array[$j]->text;
    }

    $to = str_replace(',', ', ', $to);

    $cc_array = isset($ref_contenu_message->ccaddress) ? imap_mime_header_decode($ref_contenu_message->ccaddress) : 0;

    for ($j = 0, $jMax = count($cc_array); $j < $jMax; $j++) {
        $cc .= $cc_array[$j]->text;
    }

    $cc = str_replace(',', ', ', $cc);

    $reply_to_array = isset($ref_contenu_message->reply_toaddress) ? imap_mime_header_decode($ref_contenu_message->reply_toaddress) : 0;

    for ($j = 0, $jMax = count($reply_to_array); $j < $jMax; $j++) {
        $reply_to .= $reply_to_array[$j]->text;
    }

    [$date, $complete_date] = change_date(rtrim($ref_contenu_message->udate), $lang);

    $content = [
        'from' => $from,
'to' => $to,
'cc' => $cc,
'reply_to' => $reply_to,
'subject' => $subject,
'date' => $date,
'complete_date' => $complete_date,
'att' => $link_att,
'body' => $glob_body,
'body_mime' => $tmp['mime'],
'body_transfer' => $tmp['transfer'],
'header' => $header,
'verbose' => $verbose,
'prev' => $prev_msg,
'next' => $next_msg,
    ];

    return ($content);
}

/* ----------------------------------------------------- */

// based on a function from matt@bonneau.net
function GetPart($this_part, $part_no, $display_rfc822)
{
    global $attach_tab;

    $att_name = '[unknown]';

    if (true === $this_part->ifdescription) {
        $att_name = $this_part->description;
    }

    for ($i = 0, $iMax = count($this_part->parameters); $i < $iMax; $i++) {
        $param = $this_part->parameters[$i];

        if ((('NAME' == $param->attribute) || ('name' == $param->attribute)) && ('' != $param->value)) {
            $att_name = $param->value;

            break;
        }
    }

    if (isset($this_part->type)) {
        switch ($this_part->type) {
            case 0:
                $mime_type = 'text';
                break;
            case 1:
                $mime_type = 'multipart';
                for ($i = 0, $iMax = count($this_part->parts); $i < $iMax; $i++) {
                    if ('' != $part_no) {
                        $part_no .= '.';
                    }

                    for ($i = 0, $iMax = count($this_part->parts); $i < $iMax; $i++) {
                        // if it's an alternative, we skip the text part to only keep the HTML part
                        if ('ALTERNATIVE' == $this_part->subtype) {// && $read === true)
                            GetPart($this_part->parts[++$i], $part_no . ($i + 1), $display_rfc822);
                        } else {
                            GetPart($this_part->parts[$i], $part_no . ($i + 1), $display_rfc822);
                        }
                    }
                }
                break;
            case 2:
                $mime_type = 'message';
                // well it's a message we have to parse it to find attachments or text message
                if (isset($this_part->parts[0]->parts)) {
                    $num_parts = count($this_part->parts[0]->parts);

                    for ($i = 0; $i < $num_parts; $i++) {
                        GetPart($this_part->parts[0]->parts[$i], $part_no . '.' . ($i + 1), $display_rfc822);
                    }
                }
                break;
            // Maybe we can do something with the mime types later ??
            case 3:
                $mime_type = 'application';
                break;
            case 4:
                $mime_type = 'audio';
                break;
            case 5:
                $mime_type = 'image';
                break;
            case 6:
                $mime_type = 'video';
                break;
            case 7:
                $mime_type = 'other';
                break;
            default:
                $mime_type = 'unknown';
        }
    } else {
        $mime_type = 'text';
    }

    $full_mime_type = $mime_type . '/' . $this_part->subtype;

    if (isset($this_part->encoding)) {
        switch ($this_part->encoding) {
            case 0:
                $encoding = '7BIT';
                break;
            case 1:
                $encoding = '8BIT';
                break;
            case 2:
                $encoding = 'BINARY';
                break;
            case 3:
                $encoding = 'BASE64';
                break;
            case 4:
                $encoding = 'QUOTED-PRINTABLE';
                break;
            case 5:
                $encoding = 'OTHER';
                break;
            default:
                $encoding = 'none';
                break;
        }
    } else {
        $encoding = '7BIT';
    }

    if (('message/RFC822' == $full_mime_type && true === $display_rfc822) || ('multipart' != $mime_type && 'message/RFC822' != $full_mime_type)) {
        $charset = '';

        if ($this_part->ifparameters) {
            while ($obj = array_pop($this_part->parameters)) {
                if ('CHARSET' == $obj->attribute) {
                    $charset = $obj->value;

                    break;
                }
            }
        }

        $tmp = [
            'number' => ('' != $part_no ? $part_no : 1),
'id' => $this_part->ifid ? $this_part->id : 0,
'name' => $att_name,
'mime' => $full_mime_type,
'transfer' => $encoding,
'disposition' => $this_part->ifdisposition ? $this_part->disposition : '',
'charset' => $charset,
'size' => ($this_part->bytes > 1000) ? ceil($this_part->bytes / 1000) : 1,
        ];

        array_unshift($attach_tab, $tmp);
    }
}

/* ----------------------------------------------------- */

function GetSinglePart($this_part, $header, $body)
{
    global $attach_tab;

    if (eregi('text/html', $header)) {
        $full_mime_type = 'text/html';
    } else {
        $full_mime_type = 'text/plain';
    }

    if (isset($this_part->encoding)) {
        switch ($this_part->encoding) {
            case 0:
                $encoding = '7BIT';
                break;
            case 1:
                $encoding = '8BIT';
                break;
            case 2:
                $encoding = 'BINARY';
                break;
            case 3:
                $encoding = 'BASE64';
                break;
            case 4:
                $encoding = 'QUOTED-PRINTABLE';
                break;
            case 5:
                $encoding = 'OTHER';
                break;
            default:
                $encoding = 'none';
                break;
        }
    } else {
        $encoding = '7BIT';
    }

    $charset = '';

    if ($this_part->ifparameters) {
        while ($obj = array_pop($this_part->parameters)) {
            if ('CHARSET' == $obj->attribute) {
                $charset = $obj->value;

                break;
            }
        }
    }

    $tmp = [
        'number' => 1,
'id' => $this_part->ifid ? $this_part->id : 0,
'name' => '',
'mime' => $full_mime_type,
'transfer' => $encoding,
'disposition' => $this_part->ifdisposition ? $this_part->disposition : '',
'charset' => $charset,
'size' => ($this_part->bytes > 1000) ? ceil($this_part->bytes / 1000) : 1,
    ];

    array_unshift($attach_tab, $tmp);
}

/* ----------------------------------------------------- */

function remove_stuff($body, $lang, $mime)
{
    global $PHP_SELF;

    if (eregi('html', $mime)) {
        $to_removed_array = [
            "'<html>'si",
            "'</html>'si",
            "'<body[^>]*>'si",
            "'</body>'si",
            "'<head[^>]*>.*?</head>'si",
            "'<style[^>]*>.*?</style>'si",
            "'<script[^>]*>.*?</script>'si",
            "'<object[^>]*>.*?</object>'si",
            "'<embed[^>]*>.*?</embed>'si",
            "'<applet[^>]*>.*?</applet>'si",
            "'<mocha[^>]*>.*?</mocha>'si",
        ];

        $body = preg_replace($to_removed_array, '', $body);

        $body = preg_replace('|href="(.*)script:|i', 'href="nocc_removed_script:', $body);

        $body = preg_replace('|<([^>]*)java|i', '<nocc_removed_java_tag', $body);

        $body = preg_replace('|<([^>]*)&{.*}([^>]*)>|i', '<&{;}\\3>', $body);

        //$body = preg_replace("|<([^>]*)mocha:([^>]*)>|i", "<nocc_removed_mocha:\\2>",$body);

        $body = eregi_replace('href="mailto:([a-zA-Z0-9+-=%&:_.~?@]+[#a-zA-Z0-9+]*)"', "<A HREF=\"$PHP_SELF?action=write&amp;mail_to=\\1&amp;lang=$lang\"", $body);

        $body = eregi_replace('href=mailto:([a-zA-Z0-9+-=%&:_.~?@]+[#a-zA-Z0-9+]*)', "<A HREF=\"$PHP_SELF?action=write&amp;mail_to=\\1&amp;lang=$lang\"", $body);

        //$body = eregi_replace("target=\"([a-zA-Z0-9+-=%&:_.~?]+[#a-zA-Z0-9+]*)\"", "", $body);

        //$body = eregi_replace("target=([a-zA-Z0-9+-=%&:_.~?]+[#a-zA-Z0-9+]*)", "", $body);

        $body = eregi_replace('href="([a-zA-Z0-9+-=%&:_.~?]+[#a-zA-Z0-9+]*)"', '<a href="\\1" target="_blank"', $body);

        $body = eregi_replace('href=([a-zA-Z0-9+-=%&:_.~?]+[#a-zA-Z0-9+]*)', '<a href="\\1" target="_blank"', $body);
    } elseif (eregi('plain', $mime)) {
        $body = eregi_replace('(http|https|ftp)://([a-zA-Z0-9+-=%&:_.~?]+[#a-zA-Z0-9+]*)', '<a href="\\1://\\2" target="_blank">\\1://\\2</a>', $body);

        $body = eregi_replace("([#a-zA-Z0-9+-._]*)@([#a-zA-Z0-9+-_]*)\.([a-zA-Z0-9+-_.]+[#a-zA-Z0-9+]*)", "<a href=\"$PHP_SELF?action=write&amp;mail_to=\\1@\\2.\\3&amp;lang=$lang\">\\1@\\2.\\3</a>", $body);

        $body = nl2br($body);

        if (function_exists('wordwrap')) {
            $body = wordwrap($body, 80, "\n");
        }
    }

    return ($body);
}

/* ----------------------------------------------------- */

function link_att($servr, $mail, $tab, $display_part_no)
{
    sort($tab);

    $link = '<table border="0">';

    while ($tmp = array_shift($tab)) { //if ($tmp['id'] == '')
        if (!empty($tmp['name'])) {
            $mime = str_replace('/', '-', $tmp['mime']);

            $link .= '<tr>';

            if (true === $display_part_no) {
                $link .= '<td class="inbox">' . $tmp['number'] . '</td>';
            }

            $att_name = imap_mime_header_decode($tmp['name']);

            $link .= '<td class="inbox"><a href="download.php?mail='
                         . $mail
                         . '&amp;part='
                         . $tmp['number']
                         . '&amp;transfer='
                         . $tmp['transfer']
                         . '&amp;filename='
                         . urlencode($att_name[0]->text)
                         . '&amp;mime='
                         . $mime
                         . '">'
                     . htmlentities($att_name[0]->text, ENT_QUOTES | ENT_HTML5)
                         . '</a></td><td class="inbox">'
                         . $tmp['mime']
                         . '</td><td class="inbox">'
                         . $tmp['size']
                         . ' kb</td></tr>';
        }
    }

    $link .= '</table>';

    return ($link);
}

/* ----------------------------------------------------- */

function change_date($date, $lang)
{
    //require ('./check_lang.php');

    $default_date_format = _WBM_DEFAULT_DATE_FORMAT;

    $no_locale_date_format = _WBM_NO_LOCALE_DATE_FORMAT;

    if (empty($date)) {
        $msg_date = $complete_date = '&nbsp;';
    } else {
        if (setlocale(LC_TIME, $lang_locale) != $lang_locale) {
            $default_date_format = $no_locale_date_format;
        }

        $complete_date = strftime($default_date_format, $date);

        if ((date('Y', $date) != date('Y')) || (date('M') != date('M', $date)) || (date('d') != date('d', $date))) { // not today, use the date
            $msg_date = $complete_date;
        } else { // else it's today, use the time
            $msg_date = strftime($default_time_format, $date);
        }
    }

    return ([$msg_date, $complete_date]);
}

/* ----------------------------------------------------- */

// We have to figure out the entire mail size
function get_mail_size($this_part)
{
    $size = ($this_part->bytes ?? 0);

    if (isset($this_part->parts)) {
        for ($i = 0, $iMax = count($this_part->parts); $i < $iMax; $i++) {
            $size += ($this_part->parts[$i]->bytes ?? 0);
        }
    }

    $size = ($size > 1000) ? ceil($size / 1000) : 1;

    return ($size);
}

/* ----------------------------------------------------- */

// this function build an array with all the recipients of the message for later reply or reply all
function get_reply_all($user, $domain, $from, $to, $cc)
{
    if (!eregi($user . '@' . $domain, $from)) {
        $rcpt = $from . '; ';
    }

    $tab = explode(',', $to);

    while ($tmp = array_shift($tab)) {
        if (!eregi($user . '@' . $domain, $tmp)) {
            $rcpt .= $tmp . '; ';
        }
    }

    $tab = explode(',', $cc);

    while ($tmp = array_shift($tab)) {
        if (!eregi($user . '@' . $domain, $tmp)) {
            $rcpt .= $tmp . '; ';
        }
    }

    $rcpt = isset($rcpt) ? mb_substr($rcpt, 0, -2) : $from;

    return ($rcpt);
}

/* ----------------------------------------------------- */

// We need that to build a correct list of all the recipient when we send a message
function cut_address($addr, $charset)
{
    // Strip slashes from input

    $addr = safestrip($addr);

    // Break address line into individual addresses, taking

    // quoted addresses into account

    $addresses = [];

    $token = '';

    $quote_esc = false;

    for ($i = 0, $iMax = mb_strlen($addr); $i < $iMax; $i++) {
        $c = mb_substr($addr, $i, 1);

        // Are we entering/leaving escaped mode

        if ('"' == $c) {
            $quote_esc = !$quote_esc;
        }

        // Is this an address seperator (comma/semicolon)

        if (',' == $c || ';' == $c) {
            if (!$quote_esc) {
                $token = trim($token);

                if ('' != $token) {
                    $addresses[] = $token;
                }

                $token = '';

                continue;
            }
        }

        $token .= $c;
    }

    if (!$quote_esc) {
        $token = trim($token);

        if ('' != $token) {
            $addresses[] = $token;
        }
    }

    /* old way
    // Replace commas with semicolons as address seperator
    $addr = str_replace(',', ';', $addr);

    // Break address line into individual addresses
    $addresses = explode(';', $addr);
    */

    // Loop through addresses

    for ($i = 0, $iMax = count($addresses); $i < $iMax; $i++) {
        // Wrap address in brackets, if not already

        $pos = mb_strrpos($addresses[$i], '<');

        if (!is_int($pos)) {
            $addresses[$i] = '<' . $addresses[$i] . '>';
        }

        /* FIXME: encode_mime mangles addresses badly */
        /*else
        {
            $name = '';
            if ($pos != 0)
                $name = encode_mime(substr($addresses[$i], 0, $pos - 1), $charset).' ';
            $addr = substr($addresses[$i], $pos);
            $addresses[$i] = '"'.$name.' '.$addr.'"';
        } */
    }

    return ($addresses);
}

/* ----------------------------------------------------- */

// Function that save the attachment locally for reply, transfer...
// This function returns an array of all the attachment
function save_attachment($servr, $user, $passwd, $folder, $mail, $tmpdir)
{
    global $attach_tab;

    $i = 0;

    $attach_array = [];

    $pop = @imap_open('{' . $servr . '}' . $folder, $user, $passwd);

    while ($tmp = array_shift($attach_tab)) {
        $i++;

        $file = imap_fetchbody($pop, $mail, $tmp['number']);

        if ('QUOTED-PRINTABLE' == $tmp['transfer']) {
            $file = imap_qprint($file);
        } elseif ('BASE64' == $tmp['transfer']) {
            $file = base64_decode($file, true);
        }

        $filename = 'NOCC_TMP' . md5(uniqid(time()));

        $fp = fopen($tmpdir . '/' . $filename, 'wb');

        fwrite($fp, $file);

        fclose($fp);

        $attach_array[$i]->file_name = $tmp['name'];

        $attach_array[$i]->tmp_file = $filename;

        $attach_array[$i]->file_size = mb_strlen($file);

        $attach_array[$i]->file_mime = $tmp['mime'];
    }

    imap_close($pop);

    return ([$i, $attach_array]);
}

/* ----------------------------------------------------- */

function view_part($servr, $user, $passwd, $folder, $mail, $part_no, $transfer, $msg_charset, $charset)
{
    $pop = @imap_open('{' . $servr . '}' . $folder, $user, $passwd);

    $text = imap_fetchbody($pop, $mail, $part_no);

    if ('BASE64' == $transfer) {
        $str = nl2br(imap_base64($text));
    } elseif ('QUOTED-PRINTABLE' == $transfer) {
        $str = nl2br(quoted_printable_decode($text));
    } else {
        $str = nl2br($text);
    }

    //if (eregi('koi', $transfer) || eregi('windows-1251', $transfer))

    //	$str = @convert_cyr_string($str, $msg_charset, $charset);

    return ($str);
}

/* ----------------------------------------------------- */

function encode_mime($string, $charset)
{
    /*$text = '=?' . $charset . '?Q?';
    for($i = 0; $i < strlen($string); $i++ )
    {
        $val = ord($string[$i]);
        $val = dechex($val);
        $text .= '=' . $val;
    }
    $text .= '?=';
    return ($text);
    */

    $string = rawurlencode($string);

    $string = str_replace('%', '=', $string);

    $string = '=?' . $charset . '?Q?' . $string . '?=';

    return ($string);
}

/* ----------------------------------------------------- */

// This function is used when accessing a page without being logged in
// or accessing send.php via GET method
function go_back_index($attach_array, $tmpdir, $php_session, $sort, $sortdir, $lang, $redirect)
{
    global $$php_session;

    if (isset($attach_array) && is_array($attach_array)) {
        while ($tmp = array_shift($attach_array)) {
            @unlink($tmpdir . '/' . $tmp->tmp_file);
        }
    }

    session_unregister('num_attach');

    session_unregister('attach_array');

    if ($redirect) {
        header('Location: ' . $base_url . "action.php?sort=$sort&sortdir=$sortdir&lang=$lang&$php_session=" . $$php_session);
    }
}

/* ----------------------------------------------------- */

// This function returns TRUE if the mail server is an IMAP one
// and it returns FALSE otherwise
function is_Imap($servr)
{
    if (mb_stristr($servr, '/pop3')) {
        return (false);
    }

    if (mb_stristr($servr, '/nntp')) {
        return (false);
    }

    return (true);
}

/* ----------------------------------------------------- */

// This function returns the CRLF depending on the OS and
// the way to send messages
function get_crlf($smtp)
{
    global $OS;

    $crlf = mb_stristr($OS, 'Windows') ? "\r\n" : "\n";

    $crlf = $smtp ? "\r\n" : $crlf;

    return ($crlf);
}

/* ----------------------------------------------------- */

// This function chops the <mail@domain.com> bit from a
// full 'Blah Blah <mail@domain.com>' address, or not
// depending on the 'hide_addresses' preference.
function display_address($address)
{
    // Check for null

    if ('' == $address) {
        return $html_att_unknown;
    }

    // Get preference

    $hide_addresses = getPref('hide_addresses');

    // If not set, return full address.

    if ('on' != $hide_addresses) {
        return $address;
    }

    // If no '<', return full address.

    $bracketpos = mb_strpos($address, '<');

    if (false === $bracketpos) {
        return $address;
    }

    // Return up to the first '<', or end of string if not found

    return mb_substr($address, 0, $bracketpos - 1);
}

/* ----------------------------------------------------- */

function mailquote($body, $from, $html_wrote)
{
    $from = ucwords(trim(preg_replace('&lt;.*&gt;', '', str_replace('"', '', $from))));

    $body = '> ' . preg_replace("\n", "\n> ", trim($body));

    return ($from . ' ' . $html_wrote . " :\n\n" . $body);
}

/* ----------------------------------------------------- */

// If running with magic_quotes_gpc (get/post/cookie) set
// in php.ini, we will need to strip slashes from every
// field we receive from a get/post operation.
function safestrip($string)
{
    if (function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc()) {
        $string = stripslashes($string);
    }

    return $string;
}

/*
function is_uploaded_file($filename)
{
    if (!$tmp_file = ini_get('upload_tmp_dir'))
        $tmp_file = dirname(tempnam('', ''));
    $tmp_file .= '/' . basename($filename);
    return (preg_replace('/+', '/', $tmp_file) == $filename);
}
*/
