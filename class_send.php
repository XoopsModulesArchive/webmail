<?php
/*
 * $Header: /cvsroot/xoopsbrasil/xoops2/modules/webmail/class_send.php,v 1.1 2006/03/27 08:18:47 mikhail Exp $
 *
 * Copyright 2001 Nicolas Chalanset <nicocha@free.fr>
 * Copyright 2001 Olivier Cahagne <cahagn_o@epita.fr>
 *
 * See the enclosed file COPYING for license information (GPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 */

class mime_mail
{
    public $parts;

    public $to;

    public $cc;

    public $bcc;

    public $from;

    public $headers;

    public $subject;

    public $body;

    public $smtp_server;

    public $smtp_port;

    public $charset;

    public $crlf;

    public $priority;

    /*
    *     void mime_mail()
    *     class constructor
    */

    public function __construct()
    {
        $this->parts = [];

        $this->to = [];

        $this->cc = [];

        $this->bcc = [];

        $this->from = null;

        $this->headers = null;

        $this->subject = null;

        $this->body = null;

        $this->smtp_server = 'localhost';

        $this->smtp_port = 25;

        $this->charset = 'iso-8859-1';

        $this->crlf = null;

        $this->priority = '3 (Normal)';
    }

    /*
    *     void add_attachment(string message, [string name], [string ctype], [string encoding], [string charset])
    *     Add an attachment to the mail object
    */

    public function add_attachment($message, $name, $ctype, $encoding, $charset)
    {
        $this->parts[] = [
            'ctype' => $ctype,
'message' => $message,
'encoding' => $encoding,
'charset' => $charset,
'name' => $name,
        ];
    }

    /*
     *      void build_message(array part)
     *      Build message parts of a multipart mail
     */

    public function build_message($part)
    {
        $message = $part['message'];

        $encoding = $part['encoding'];

        $charset = $part['charset'];

        switch ($encoding) {
            case 'base64':
                $message = chunk_preg_split(base64_encode($message));
                break;
            case 'quoted-printable':
                $message = imap_8bit($message);
                break;
            default:
                break;
        }

        $val = 'Content-Type: ' . $part['ctype'] . ';';

        $val .= ($part['charset'] ? $this->crlf . "\tcharset=\"" . $part['charset'] . '"' : '');

        $val .= ($part['name'] ? $this->crlf . "\tname=\"" . $part['name'] . '"' : '');

        $val .= $this->crlf . 'Content-Transfer-Encoding: ' . $encoding;

        $val .= ($part['name'] ? $this->crlf . 'Content-Disposition: attachment;' . $this->crlf . "\tfilename=\"" . $part['name'] . '"' : '');

        $val .= $this->crlf . $this->crlf . $message . $this->crlf;

        return ($val);
    }

    /*
     *      void build_multipart()
     *      Build a multipart mail
     */

    public function build_multipart()
    {
        $boundary = 'NextPart' . md5(uniqid(time()));

        $multipart = 'Content-Type: multipart/mixed;' . $this->crlf . "\tboundary = $boundary" . $this->crlf . $this->crlf . 'This is a MIME encoded message.' . $this->crlf . $this->crlf . '--' . $boundary;

        for ($i = count($this->parts) - 1; $i >= 0; $i--) {
            $multipart .= $this->crlf . $this->build_message($this->parts[$i]) . '--' . $boundary;
        }

        return ($multipart .= '--' . $this->crlf);
    }

    /*
     *		void build_body()
     *		build a non multipart mail
    */

    public function build_body()
    {
        if (1 == count($this->parts)) {
            $part = $this->build_message($this->parts[0]);
        } else {
            $part = '';
        }

        return ($part . $this->crlf);
    }

    /*
     *      void send()
     *      Send the mail (last class-function to be called)
     */

    public function send()
    {
        $mime = '';

        if (('' != $this->smtp_server && '' != $this->smtp_port)) {
            if ('' != $this->to[0]) {
                $mime .= 'To: ' . implode(', ', $this->to) . $this->crlf;
            }

            if (!empty($this->subject)) {
                $mime .= 'Subject: ' . $this->subject . $this->crlf;
            }
        }

        if (!empty($this->from)) {
            $mime .= 'From: ' . $this->from . $this->crlf;
        }

        if ('' != $this->cc[0]) {
            $mime .= 'Cc: ' . implode(', ', $this->cc) . $this->crlf;
        }

        if ('' != $this->bcc[0]) {
            $mime .= 'Bcc: ' . implode(', ', $this->bcc) . $this->crlf;
        }

        if (preg_match("[4-9]\.[0-9]\.[4-9].*", phpversion())) {
            $mime .= 'Date: ' . date('r') . $this->crlf;
        } else {
            $mime .= 'Date: ' . date('D, j M Y H:i:s T') . $this->crlf;
        }

        if (!empty($this->from)) {
            $mime .= 'Reply-To: ' . $this->from . $this->crlf . 'Errors-To: ' . $this->from . $this->crlf;
        }

        $mime .= 'X-Priority: ' . $this->priority . $this->crlf;

        if (!empty($this->headers)) {
            $mime .= $this->headers . $this->crlf;
        }

        if (count($this->parts) >= 1) {
            $this->add_attachment($this->body, '', 'text/plain', 'quoted-printable', $this->charset);

            $mime .= 'MIME-Version: 1.0' . $this->crlf . $this->build_multipart();
        } else {
            $this->add_attachment($this->body, '', 'text/plain', '8bit', $this->charset);

            $mime .= $this->build_body();
        }

        // Whether or not to use SMTP or sendmail

        // depends on the config file (conf.php)

        if ('' == $this->smtp_server || '' == $this->smtp_port) {
            //$rcpt_to = join(', ', $this->to);

            $rcpt_to = $this->to;

            if (preg_match("[4-9]\.[0-9]\.[5-9].*", phpversion())) {
                return (mail($rcpt_to, $this->subject, '', $mime, '-f' . $this->from));
            }

            return (mail($rcpt_to, $this->subject, '', $mime));
        }  

        if (0 != ($smtp = new smtp())) {
            $smtp->smtp_server = $this->smtp_server;

            $smtp->port = $this->smtp_port;

            $smtp->from = $this->strip_comment($this->from);

            $smtp->to = $this->strip_comment_array($this->to);

            $smtp->cc = $this->strip_comment_array($this->cc);

            $smtp->bcc = $this->strip_comment_array($this->bcc);

            $smtp->subject = $this->subject;

            $smtp->data = $mime;

            return ($smtp->send());
        }

        return (0);
    }

    public function strip_comment_array($array)
    {
        for ($i = 0, $iMax = count($array); $i < $iMax; $i++) {
            $array[$i] = $this->strip_comment($array[$i]);
        }

        return $array;
    }

    public function strip_comment($address)
    {
        $pos = mb_strrpos($address, '<');

        if (false === $pos) {
            return '<' . $address . '>';
        }

        return mb_substr($address, $pos);
    }
}  // end of class
