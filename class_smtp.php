<?php
/*
 * $Header: /cvsroot/xoopsbrasil/xoops2/modules/webmail/class_smtp.php,v 1.1 2006/03/27 08:18:47 mikhail Exp $
 *
 * Copyright 2001 Nicolas Chalanset <nicocha@free.fr>
 * Copyright 2001 Olivier Cahagne <cahagn_o@epita.fr>
 *
 * See the enclosed file COPYING for license information (GPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * Class based on a work from Unk <rgroesb_garbage@triple-it_garbage.nl>
 */

require_once __DIR__ . '/exception.php';

class smtp
{
    public $smtp_server;

    public $port;

    public $from;

    public $to;

    public $cc;

    public $bcc;

    public $subject;

    public $data;

    public $sessionlog = '';

    // This function is the constructor don't forget this one

    public function __construct()
    {
        $this->smtp_server = '';

        $this->port = '';

        $this->from = '';

        $this->to = [];

        $this->cc = [];

        $this->bcc = [];

        $this->subject = '';

        $this->data = '';
    }

    public function smtp_open()
    {
        global $SMTP_GLOBAL_STATUS;

        $smtp = fsockopen($this->smtp_server, $this->port, $errno, $errstr);

        if (!$smtp) {
            return new Exception($html_smtp_no_con . ': ' . $errstr);
        }

        $line = fgets($smtp, 1024);

        $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULT'] = mb_substr($line, 0, 1);

        $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULTTXT'] = mb_substr($line, 0, 1024);

        if ('2' != $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULT']) {
            return new Exception($html_smtp_error_unexpected . ' ' . $line);
        }

        return $smtp;
    }

    public function smtp_helo($smtp)
    {
        global $SMTP_GLOBAL_STATUS;

        /* 'localhost' always works [Unk] */

        fwrite($smtp, "helo localhost\r\n");

        $this->sessionlog .= 'Sent: helo localhost';

        $line = fgets($smtp, 1024);

        $this->sessionlog .= "Rcvd: $line";

        $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULT'] = mb_substr($line, 0, 1);

        $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULTTXT'] = mb_substr($line, 0, 1024);

        if ('2' != $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULT']) {
            return new Exception($html_smtp_error_unexpected . ' ' . $line);
        }

        return (true);
    }

    public function smtp_ehlo($smtp)
    {
        global $SMTP_GLOBAL_STATUS;

        /* Well, let's use "helo" for now.. Until we need the
          extra func's   [Unk]
        */

        fwrite($smtp, "ehlo localhost\r\n");

        $this->sessionlog .= 'Sent: ehlo localhost';

        $line = fgets($smtp, 1024);

        $this->sessionlog .= "Rcvd: $line";

        $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULT'] = mb_substr($line, 0, 1);

        $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULTTXT'] = mb_substr($line, 0, 1024);

        if ('2' != $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULT']) {
            return new Exception($html_smtp_error_unexpected . ' ' . $line);
        }

        return (true);
    }

    public function smtp_mail_from($smtp)
    {
        global $SMTP_GLOBAL_STATUS;

        fwrite($smtp, "MAIL FROM:$this->from\r\n");

        $this->sessionlog .= "Sent: MAIL FROM:$this->from";

        $line = fgets($smtp, 1024);

        $this->sessionlog .= "Rcvd: $line";

        $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULT'] = mb_substr($line, 0, 1);

        $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULTTXT'] = mb_substr($line, 0, 1024);

        if ('2' != $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULT']) {
            return new Exception($html_smtp_error_unexpected . ' ' . $line);
        }

        return (true);
    }

    public function smtp_rcpt_to($smtp)
    {
        global $SMTP_GLOBAL_STATUS;

        // Modified by nicocha to use to, cc and bcc field

        while ($tmp = array_shift($this->to)) {
            if ('' == $tmp || '<>' == $tmp) {
                continue;
            }

            fwrite($smtp, "RCPT TO:$tmp\r\n");

            $this->sessionlog .= "Sent: RCPT TO:$tmp";

            $line = fgets($smtp, 1024);

            $this->sessionlog .= "Rcvd: $line";

            $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULT'] = mb_substr($line, 0, 1);

            $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULTTXT'] = mb_substr($line, 0, 1024);

            if ('2' != $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULT']) {
                return new Exception($html_smtp_error_unexpected . ' ' . $line);
            }
        }

        while ($tmp = array_shift($this->cc)) {
            if ('' == $tmp || '<>' == $tmp) {
                continue;
            }

            fwrite($smtp, "RCPT TO:$tmp\r\n");

            $this->sessionlog .= "Sent: RCPT TO:$tmp";

            $line = fgets($smtp, 1024);

            $this->sessionlog .= "Rcvd: $line";

            $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULT'] = mb_substr($line, 0, 1);

            $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULTTXT'] = mb_substr($line, 0, 1024);

            if ('2' != $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULT']) {
                return new Exception($html_smtp_error_unexpected . ' ' . $line);
            }
        }

        while ($tmp = array_shift($this->bcc)) {
            if ('' == $tmp || '<>' == $tmp) {
                continue;
            }

            fwrite($smtp, "RCPT TO:$tmp\r\n");

            $this->sessionlog .= "Sent: RCPT TO:$tmp";

            $line = fgets($smtp, 1024);

            $this->sessionlog .= "Rcvd: $line";

            $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULT'] = mb_substr($line, 0, 1);

            $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULTTXT'] = mb_substr($line, 0, 1024);

            if ('2' != $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULT']) {
                return new Exception($html_smtp_error_unexpected . ' ' . $line);
            }
        }

        return (true);
    }

    public function smtp_data($smtp)
    {
        global $SMTP_GLOBAL_STATUS;

        fwrite($smtp, "DATA\r\n");

        $this->sessionlog .= 'Sent: DATA';

        $line = fgets($smtp, 1024);

        $this->sessionlog .= "Rcvd: $line";

        $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULT'] = mb_substr($line, 0, 1);

        $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULTTXT'] = mb_substr($line, 0, 1024);

        if ('3' != $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULT']) {
            return new Exception($html_smtp_error_unexpected . ' ' . $line);
        }

        fwrite($smtp, (string)$this->data);

        fwrite($smtp, "\r\n.\r\n");

        $line = fgets($smtp, 1024);

        $this->sessionlog .= "Rcvd: $line";

        if ('2' != mb_substr($line, 0, 1)) {
            return new Exception($html_smtp_error_unexpected . ' ' . $line);
        }

        return (true);
    }

    public function smtp_quit($smtp)
    {
        global $SMTP_GLOBAL_STATUS;

        fwrite($smtp, "QUIT\r\n");

        $this->sessionlog .= 'Sent: QUIT';

        $line = fgets($smtp, 1024);

        $this->sessionlog .= "Rcvd: $line";

        $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULT'] = mb_substr($line, 0, 1);

        $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULTTXT'] = mb_substr($line, 0, 1024);

        if ('2' != $SMTP_GLOBAL_STATUS[$smtp]['LASTRESULT']) {
            return new Exception($html_smtp_error_unexpected . ' ' . $line);
        }

        return (true);
    }

    public function send()
    {
        $smtp = $this->smtp_open();

        if (\Throwable::isException($smtp)) {
            return $smtp;
        }

        $ev = $this->smtp_helo($smtp);

        if (\Throwable::isException($ev)) {
            return $ev;
        }

        $ev = $this->smtp_mail_from($smtp);

        if (\Throwable::isException($ev)) {
            return $ev;
        }

        $ev = $this->smtp_rcpt_to($smtp);

        if (\Throwable::isException($ev)) {
            return $ev;
        }

        $ev = $this->smtp_data($smtp);

        if (\Throwable::isException($ev)) {
            return $ev;
        }

        $ev = $this->smtp_quit($smtp);

        if (\Throwable::isException($ev)) {
            return $ev;
        }

        return (true);
    }
}
