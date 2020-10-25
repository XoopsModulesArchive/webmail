<?php
// ------------------------------------------------------------------------- //
//						 FreeContent							  //
//				    Version:  0.61 beta (for RC2)					  //
//						  Module for							  //
//				XOOPS - PHP Content Management System				  //
//					<https://www.xoops.org>						  //
// ------------------------------------------------------------------------- //
// Author: Stefan "SibSerag" Oeser 								  //
// Purpose: Module to wrap html or php-content into nice Xoops design.	  //
// email: tiger@sibserag.de										  //
// Site: http://coding.sibserag.de 								  //
//---------------------------------------------------------------------------//
// Based on:													  //
// myPHPNUKE Web Portal System - http://myphpnuke.com/ 				  //
// PHP-NUKE Web Portal System - http://phpnuke.org/					  //
// Thatware - http://thatware.org/ 								  //
// ------------------------------------------------------------------------- //
//  This program is free software; you can redistribute it and/or modify	  //
//  it under the terms of the GNU General Public License as published by	  //
//  the Free Software Foundation; either version 2 of the License, or 	  //
//  (at your option) any later version. 							  //
//															  //
//  This program is distributed in the hope that it will be useful,		  //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of		  //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the		  //
//  GNU General Public License for more details.						  //
// ------------------------------------------------------------------------- //

include 'header.php';
include 'config.php';
//session_unregister('num_attach','attach_array','attach_tab');
//CheckDB();
session_register('num_attach', 'attach_array', 'attach_tab');

$php_session = ini_get('session.name');

set_magic_quotes_runtime(0);

if ('webmail' == $xoopsConfig['startpage']) {
    $xoopsOption['show_rblock'] = 1;

    include $xoopsConfig['root_path'] . 'header.php';

    make_cblock();

    echo '<br>';
} else {
    $xoopsOption['show_rblock'] = 0;

    include $xoopsConfig['root_path'] . 'header.php';
}
require_once $xoopsConfig['root_path'] . 'class/xoopshtmlform.php';
require_once $xoopsConfig['root_path'] . 'class/module.textsanitizer.php';

$charset = _WBM_CHARSET;
$default_date_format = _WBM_DEFAULT_DATE_FORMAT;
$no_locale_date_format = _WBM_NO_LOCALE_DATE_FORMAT;
$nocc_version = '0.9.5';
$nocc_name = 'NOCC adapted for Xoops';
$folder = 'INBOX';
$attach_tab = [];

$use_verbose = true;

// Whether or not to display attachment part number
$display_part_no = true;

// Whether or not to display the Message/RFC822 into the attachments
// (the attachments of that part are still available even if false is set
$display_rfc822 = true;

// If you don't want to display images (GIF, JPEG and PNG) sent as attachements
// set it to 'false'
$display_img_attach = true;

// If you don't want to display text/plain attachments set it to 'false'
$display_text_attach = true;

// By default the messages are sorted by date
$default_sort = '1';

// By default the most recent is in top ('1' --> sorting top to bottom,
// '0' --> bottom to top)
$default_sortdir = '1';

// For old UCB POP server, change this setting to 1 to enable
// new mail detection. Recommended: leave it to 0 for any other POP or
// IMAP server.
// See FAQ for more details.
$have_ucb_pop_server = false;
if (empty($sort)) {
    $sort = 1;
}
if (empty($sortdir)) {
    $sortdir = 1;
}

global $xoopsDB, $xoopsConfig, $xoopsTheme;
require_once __DIR__ . '/functions.php';
if ($xoopsUser) {
    $sqlstr = 'SELECT email  FROM ' . $xoopsDB->prefix('users') . ' WHERE uid= ' . $xoopsUser->uid() . ' ';

    $res = $xoopsDB->query($sqlstr);

    [$mail_from] = $xoopsDB->fetchRow($res);

    OpenTable();

    $sqlstr = 'SELECT wbmid,login, password, serveur, type, compte FROM ' . $xoopsConfig['prefix'] . '_webmail  WHERE uid= ' . $xoopsUser->uid() . ' ';

    $res = $xoopsDB->query($sqlstr);

    $i = 0;

    $bg = 'bg2';

    echo "<form name='delete_Compte' action='index.php' method='post'>\n";

    echo "<input type='hidden' name='op' value='delete'>\n";

    echo "<table class=bg1 width = 100%><tr><td>Tout selectioné</td><td>compte</td><td>Login</td><td>serveur</td><td>type</td></tr>\n";

    while (list($loc_wbmid, $login, $password, $serveur, $type, $compte) = $xoopsDB->fetchRow($res)) {
        $i++;

        $bg = 'bg' . (2 + ($i % 2));

        echo "<tr class=$bg><td><input type='checkbox' name='select_$loc_wbmid' value=0></td><td><a href=index.php?op=modifier_compte&wbmid=$loc_wbmid>$compte</a></td><td>$login</td><td>$serveur</td><td>$type</td></tr>\n";
    }

    echo "<tr class=bg1><td><a href=javascript:SelectAllCompte()>Tout selectioner</a></td><td><a href=index.php?op=ajouter_compte>ajouter un compte</a></td><td><a href=javascript:void(null) >Supprimer</a></td><td>&nbsp;</td><td>&nbsp;</td></tr>\n";

    echo "<input type='hidden' name='Nb_CheckBox' value='" . ($i - 1) . "'>";

    echo "</table>\n</form>\n";

    echo "
	<script type=\"text/javascript\">
	<!--
	function SelectAllCompte() 
	{
	  for (var i = 0; i < document.delete_Compte.elements.length; i++) 
	  {
	    if( document.delete_Compte.elements[i].name.substr( 0, 6 ) == 'select') 
	    {
	      	document.delete_Compte.elements[i].checked =!(document.delete_Compte.elements[i].checked);
	    }
	  }
	}
	//-->
	</script>
	";

    if (0 != $i || op != 'ajouter_compte') {
        $sqlstr = 'SELECT wbmid,login, password, serveur, type, compte FROM ' . $xoopsConfig['prefix'] . '_webmail  WHERE uid= ' . $xoopsUser->uid . ' ';

        if (!empty($wbmid) || '' != $wbmid) {
            $sqlstr .= " AND wbmid = $wbmid";
        }

        $res = $xoopsDB->queryF($sqlstr);

        [$wbmid, $login, $password, $serveur, $type, $compte] = $xoopsDB->fetchRow($res);

        CloseTable();

        echo "<table width= 100%><tr valign='top'><td width = 15%>";

        ListeDossier($wbmid, $op, $sort, $sortdir);

        echo '</td><td>';

        OpenTable();

        if ('pop' == $type) {
            $serveur .= '/' . $type . ':110';
        }

        if ('imap' == $type) {
            $serveur .= '/' . $type . ':143';
        }

        switch ($op) {
            case 'send_mail':
            {
                $mail_from = safestrip($mail_from);
                $mail_to = safestrip($mail_to);
                $mail_cc = safestrip($mail_cc);
                $mail_bcc = safestrip($mail_bcc);
                $mail_subject = safestrip($mail_subject);
                $mail_body = safestrip($mail_body);

                switch (trim($sendaction)) {
                    case 'add':
                    {
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
                            copy($mail_att, 'temp/' . $tmp_name);

                            $attach_array[$num_attach]->file_name = basename($mail_att_name);

                            $attach_array[$num_attach]->tmp_file = $tmp_name;

                            $attach_array[$num_attach]->file_size = $mail_att_size;

                            $attach_array[$num_attach]->file_mime = $mail_att_type;

                            //echo "$tmp_name  =  ".$attach_array[$num_attach]->tmp_file ."<br>";
                            //echo "$mail_att_name , $mail_att_size <br>";
                        }
                        // Registering the attachments array into the session
                        session_unregister('attach_array');
                        session_register('num_attach', 'attach_array');
                        // Displaying the sending form with the new attachments array
                        header("Content-type: text/html; Charset=$charset");
                        //require ('./html/header.php');
                        //require ('./html/menu_inbox.php');
                        require __DIR__ . '/html/send.php';
                        //require ('./html/menu_inbox.php');
                        break;
                    }
                    case 'send':
                    {
                        $ip = (getenv('HTTP_X_FORWARDED_FOR') ?: getenv('REMOTE_ADDR'));
                        $mail = new mime_mail();
                        $mail->crlf = get_crlf($smtp_server);
                        if ('' == $mail->crlf) {
                            $mail->crlf = "\n";
                        }
                        $mail->smtp_server = $smtp_server;
                        $mail->smtp_port = $smtp_port;
                        $mail->charset = $charset;

                        //$mail->from = cut_address(trim($mail_from), $charset);
                        $mail->from = trim($mail_from);
                        //$mail->from = $mail->from[0];
                        $mail->priority = $priority;
                        $mail->headers = 'X-Originating-Ip: [' . $ip . ']' . $mail->crlf . 'X-Mailer: ' . $nocc_name . ' v' . $nocc_version;
                        //$mail->to = cut_address(trim($mail_to), $charset);
                        $mail->to = trim($mail_to);
                        //$mail->cc = cut_address(trim($mail_cc), $charset);
                        $mail->cc = trim($mail_cc);
                        //$cc_self = getPref('cc_self');
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

                                    //echo "ATTACH ->temp/".$attach_array[$i]->tmp_file."<br>";

                                    $mail->add_attachment($file, imap_qprint($attach_array[$i]->file_name), $attach_array[$i]->file_mime, 'base64', '');

                                    // then we delete the temporary file

                                    unlink('temp/' . $attach_array[$i]->tmp_file);
                                }
                            }
                        }
                        // We need to unregister the attachments array and num_attach
                        session_unregister('num_attach');
                        session_unregister('attach_array');
                        $ev = $mail->send();
                        header("Content-type: text/html; Charset=$charset");
                        if (\Throwable::isException($ev)) {
                            // Error while sending the message, display an error message

                            //require ('./html/header.php');

                            //require ('./html/menu_inbox.php');

                            require __DIR__ . '/html/send_error.php';

                        //require ('./html/menu_inbox.php');
                        } else {
                            // Display a confirmation of success

                            //require ('./html/header.php');

                            //require ('./html/menu_inbox.php');

                            require __DIR__ . '/html/send_confirmed.php';

                            //require ('./html/menu_inbox.php');
                        }
                        break;
                    }
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
                                @unlink('temp/' . $attach_array[$i]->tmp_file);
                            }
                        }
                        $num_attach = ($j > 1 ? $j - 1 : 0);
                        // Removing the attachments array from the current session
                        session_unregister('num_attach');
                        session_unregister('attach_array');
                        $attach_array = $tmp_array;
                        // Registering the attachments array into the session
                        session_register('num_attach', 'attach_array');
                        // Displaying the sending form with the new attachment array
                        header("Content-type: text/html; Charset=$charset");
                        //require ('./html/header.php');
                        //require ('./html/menu_inbox.php');
                        require __DIR__ . '/html/send.php';
                        //require ('./html/menu_inbox.php');
                        break;
                    default:
                        // Nothing was set in the sendaction (e.g. no javascript enabled)
                        header("Content-type: text/html; Charset=$charset");
                        $ev = new Exception((string)$html_no_sendaction);
                        //require ('./html/header.php');
                        //require ('./html/menu_inbox.php');
                        require __DIR__ . '/html/send_error.php';
                        //require ('./html/menu_inbox.php');
                        break;
                }
                break;
            }
            case 'delete':
            {
                //session_register ('user', 'passwd');
                //require_once ('./conf.php');
                //require_once ('./functions.php');

                $pop = @imap_open('{' . $serveur . '}' . $folder, $login, $password);
                //$pop = @imap_open('{' . $servr . '}INBOX', $HTTP_SESSION_VARS['user'], safestrip($HTTP_SESSION_VARS['passwd']));
                $num_messages = imap_num_msg($pop);
                //$msg.="num message = ".$num_messages."<br>mail = ".$mail."<br>wbmid =".$wbmid."<br>";
                /*
                if (isset($only_one) && ($only_one == 1))
                {
                    imap_delete($pop, $mail, 0);
                    $msg .= "Message 0 efface<br>";
                }
                else
                */
                {
                    for ($i = 1; $i <= $num_messages; $i++) {
                        $delete_this_one = $_POST['msg-' . $i];

                        if ('Y' == $delete_this_one) {
                            imap_delete($pop, $i, 0);

                            $msg .= "Message $i efface<br>";
                        }
                    }
                }
                imap_close($pop, CL_EXPUNGE);
                redirect_header($xoopsConfig['xoops_url'] . "/modules/webmail/index.php?wbmid=$wbmid&sort=$sort&sortdir=$sortdir", 1, $msg);
                //header('Location: ' . $base_url . "action.php?sort=$sort&sortdir=$sortdir&lang=$lang&$php_session=" . $$php_session);
                break;
            }
            case 'update':
            {
                $sqlstr = 'UPDATE ' . $xoopsConfig['prefix'] . "_webmail SET login='$nv_login' , password='$nv_password' , serveur='$nv_serveur' , type='$nv_type' , compte='$nv_compte' WHERE wbmid= $nv_wbmid";

                $res = $xoopsDB->query($sqlstr);
                if (false !== $res) {
                    redirect_header($xoopsConfig['xoops_url'] . '/modules/webmail/index.php', 1, "Compte $compte mis a jour");
                } else {
                    redirect_header($xoopsConfig['xoops_url'] . '/modules/webmail/index.php', 1, "Erreur lors de la mise a jour<br>$sqlstr<br>" . $xoopsDB->error() . '');
                }
                break;
            }

            case 'ajouter_compte':
            {
                $hf = new XoopsHtmlForm();
                if ($suivant) {
                    $sqlstr = 'INSERT INTO ' . $xoopsConfig['prefix'] . "_webmail SET login='$nv_login', password='$nv_password', serveur='$nv_serveur', type='$nv_type', compte='$nv_compte' ,uid =" . $xoopsUser->uid() . '';

                    $res = $xoopsDB->query($sqlstr);

                    [$wbmid, $login, $password, $serveur, $type, $compte] = $xoopsDB->fetchRow($res);

                    if (false !== $res) {
                        redirect_header($xoopsConfig['xoops_url'] . '/modules/webmail/index.php', 1, "Compte $compte ajouté");
                    } else {
                        redirect_header($xoopsConfig['xoops_url'] . '/modules/webmail/index.php', 1, "Erreur lors de la mise a jour<br>$sqlstr<br>" . $xoopsDB->error() . '');
                    }

                    break;
                }  
                    OpenTable();
                    $array_type = ['pop' => 'pop', 'imap' => 'imap'];
                    echo "<form name='$op' action='index.php' method='post'>\n";
                    echo $hf->input_hidden('op', 'ajouter_compte') . "\n";
                    echo $hf->input_hidden('suivant', '1') . "\n";
                    echo '<tr><td width = 50>Compte:</td><td> ' . $hf->input_text('nv_compte') . "</td><td></td></tr>\n";
                    echo '<tr><td width = 50>Login :</td><td> ' . $hf->input_text('nv_login') . "</td><td></td></tr>\n";
                    echo '<tr><td width = 50>Password :</td><td> ' . $hf->input_password('nv_password') . "</td><td></td></tr>\n";
                    echo '<tr><td width = 50>Serveur :</td><td> ' . $hf->input_text('nv_serveur') . "</td><td></td></tr>\n";
                    echo '<tr><td width = 50>Type :</td><td> ' . $hf->select('nv_type', $array_type) . "</td><td></td></tr>\n";
                    echo '<tr><td width = 50></td><td> ' . $hf->input_submit('ok', 'submit') . "</td><td></td></tr>\n";
                    echo "</form>\n";
                    CloseTable();

                break;
            }
            case 'modifier_compte':
            {
                $hf = new XoopsHtmlForm();
                OpenTable();
                $sqlstr = 'SELECT wbmid, login, password, serveur, type, compte FROM ' . $xoopsConfig['prefix'] . "_webmail  WHERE wbmid= $wbmid ";
                $res = $xoopsDB->query($sqlstr);
                [$wbmid, $login, $password, $serveur, $type, $compte] = $xoopsDB->fetchRow($res);
                $array_type = ['pop' => 'pop', 'imap' => 'imap'];
                echo "<form name='$op' action='index.php' method='post'>\n";
                echo $hf->input_hidden('op', 'update') . "\n";
                echo $hf->input_hidden('nv_wbmid', $wbmid) . "\n";
                echo '<tr><td width = 50>Compte:</td><td> ' . $hf->input_text('nv_compte', $compte) . "</td><td></td></tr>\n";
                echo '<tr><td width = 50>Login :</td><td> ' . $hf->input_text('nv_login', $login) . "</td><td></td></tr>\n";
                echo '<tr><td width = 50>Password :</td><td> ' . $hf->input_password('nv_password', $password) . "</td><td></td></tr>\n";
                echo '<tr><td width = 50>Serveur :</td><td> ' . $hf->input_text('nv_serveur', $serveur) . "</td><td></td></tr>\n";
                echo '<tr><td width = 50>Type :</td><td> ' . $hf->select('nv_type', $array_type) . "</td><td></td></tr>\n";
                echo '<tr><td width = 50></td><td> ' . $hf->input_submit('ok', 'submit') . "</td><td></td></tr>\n";
                echo "</form>\n";
                CloseTable();
                break;
            }
            case 'write':
            {
                // Add signature
                $mail_body = "\r\n" . $prefs_signature;
                $num_attach = 0;
                //require ('./html/menu_inbox.php');
                require_once __DIR__ . '/html/send.php';
                //require ('./html/menu_inbox.php');
                break;
            }
            case 'reply_all':
            {
                $content = aff_mail($serveur, $login, $password, $folder, $mail, 0, $sort, $sortdir);
                //$mail_to = get_reply_all($user, $domain, $content['from'], $content['to'], $content['cc']);
                if (!strcasecmp(mb_substr($content['subject'], 0, 2), _WBM_HTML_REPLY_SHORT)) {
                    $mail_subject = $content['subject'];
                } else {
                    $mail_subject = $_WBM_HTML_REPLY_SHORT . ': ' . $content['subject'];
                }

                // Set body
                //$outlook_quoting = getPref('outlook_quoting');
                if ($outlook_quoting) {
                    $mail_body = $original_msg . "\n" . _WBM_HTML_FROM . ': ' . $content['from'] . "\n" . $html_to . ': ' . $content['to'] . "\n" . _WBM_HTML_SENT . ': ' . $content['complete_date'] . "\n" . _WBM_HTML_SUBJECT . ': ' . $content['subject'] . "\n\n" . strip_tags($content['body'], '');
                } else {
                    $mail_body = mailquote(strip_tags($content['body'], ''), $content['from'], _WBM_HTML_WROTE);
                }

                // We add the attachments of the original message
                //list($num_attach, $attach_array) = save_attachment($servr, $user, $passwd, $folder, $mail, $tmpdir);
                // Registering the attachments array into the session
                //session_register('num_attach', 'attach_array');
                //require ('./html/menu_inbox.php');
                require_once __DIR__ . '/html/send.php';
                //require ('./html/menu_inbox.php');
                break;
            }
            case 'reply':
            {
                $content = aff_mail($serveur, $login, $password, $folder, $mail, $verbose, $sort, $sortdir);
                $mail_to = !empty($content['reply_to']) ? $content['reply_to'] : $content['from'];
                // Test for Re: in subject, should not be added twice !
                if (!strcasecmp(mb_substr($content['subject'], 0, 2), _WBM_HTML_REPLY_SHORT)) {
                    $mail_subject = $content['subject'];
                } else {
                    $mail_subject = $_WBM_HTML_REPLY_SHORT . ': ' . $content['subject'];
                }

                // Set body
                //$outlook_quoting = getPref('outlook_quoting');
                if ($outlook_quoting) {
                    $mail_body = $original_msg . "\n" . _WBM_HTML_FROM . ': ' . $content['from'] . "\n" . $html_to . ': ' . $content['to'] . "\n" . _WBM_HTML_SENT . ': ' . $content['complete_date'] . "\n" . _WBM_HTML_SUBJECT . ': ' . $content['subject'] . "\n\n" . strip_tags($content['body'], '');
                } else {
                    $mail_body = mailquote(strip_tags($content['body'], ''), $content['from'], _WBM_HTML_WROTE);
                }

                // We add the attachments of the original message
                [$num_attach, $attach_array] = save_attachment($serveur, $login, $password, $folder, $mail, 'temp/');
                // Registering the attachments array into the session
                session_register('num_attach', 'attach_array');
                //require ('./html/menu_inbox.php');
                require_once __DIR__ . '/html/send.php';
                //require ('./html/menu_inbox.php');
                break;
            }
            case 'aff_mail':
            {
                // Here we display the message
                OpenTable();
                require __DIR__ . '/html/menu_mail.php';
                require_once __DIR__ . '/html/html_mail_top.php';
                $servr = $serveur;
                $user = $login;
                $passwd = $password;
                session_register('servr', 'user', 'passwd');
                $php_session = ini_get('session.name');

                $content = aff_mail($serveur, $login, $password, $folder, $mail, $verbose, $sort, $sortdir);
                require_once __DIR__ . '/html/html_mail_header.php';

                while ($tmp = array_shift($attach_tab)) {
                    //print_r ($tmp);

                    // $attach_tab is the array of attachments

                    // If it's a text/plain, display it

                    if ((!eregi('ATTACHMENT', $tmp['disposition'])) && $display_text_attach && (eregi('text/plain', $tmp['mime']))) {
                        echo '<hr>' . view_part($serveur, $login, $password, $folder, $mail, $tmp['number'], $tmp['transfer'], $tmp['charset'], $charset);
                    }

                    if ($display_img_attach && (eregi('image', $tmp['mime']) && ('' != $tmp['number']))) {
                        // if it's an image, display it

                        $img_type = array_pop(explode('/', $tmp['mime']));

                        if (eregi('JPEG', $img_type) || eregi('JPG', $img_type) || eregi('GIF', $img_type) || eregi('PNG', $img_type)) {
                            echo '<hr>';

                            echo '<center>';

                            echo '<p>' . _WBM_HTML_LOADING_IMAGE . ' ' . $tmp['name'] . '...</p>';

                            //echo 'get_img.php?'.$php_session.'='.$$php_session.'&amp;mail='.$mail.'&amp;folder='.$folder.'&amp;num='.$tmp['number'].'&amp;mime='.$img_type.'&amp;transfer='.$tmp['transfer'];

                            echo '<img src="get_img.php?' . $php_session . '=' . $$php_session . '&amp;mail=' . $mail . '&amp;folder=' . $folder . '&amp;num=' . $tmp['number'] . '&amp;mime=' . $img_type . '&amp;transfer=' . $tmp['transfer'] . '">';

                            echo '</center>';
                        }
                    }
                }
                session_unregister('servr', 'user', 'passwd');
                require_once __DIR__ . '/html/html_mail_bottom.php';
                require __DIR__ . '/html/menu_mail.php';
                CloseTable();
                break;
            }
            default:
                {
                    ListeMail($wbmid, $op, $compte, $serveur, $login, $password, 'INBOX', $sort, $sortdir);
                }

                break;
        }

        CloseTable();

        echo '</td></tr></table>';
    } else {
        echo "vous n'avez pas de compte configure<br>Veuillez en ajouter un";

        CloseTable();
    }

    CloseTable();
}
/*
OpenTable();
CloseTable();
*/

include $xoopsConfig['root_path'] . 'footer.php';
function ListeDossier($wbmid, $op, $sort, $sortid)
{
    global $xoopsDB, $xoopsConfig, $xoopsTheme, $xoopsUser;

    OpenTable();

    $sqlstr = 'SELECT wbmid, compte FROM ' . $xoopsConfig['prefix'] . '_webmail  WHERE uid= ' . $xoopsUser->uid() . ' AND wbmid != ' . $wbmid . ' ';

    $res = $xoopsDB->queryF($sqlstr);

    $i = 0;

    while (list($loc_wbmid, $compte) = $xoopsDB->fetchRow($res)) {
        echo '<tr><td class=bg' . (1 + (($i++) % 2)) . '><a href =?wbmid=' . $loc_wbmid . "><img src=\"images/right.gif\">$compte</a></td></tr>";
    }

    $sqlstr = 'SELECT  compte FROM ' . $xoopsConfig['prefix'] . '_webmail  WHERE  wbmid = ' . $wbmid . ' ';

    echo "<!-- SSS $sqlstr -->";

    $res = $xoopsDB->queryF($sqlstr);

    [$compte] = $xoopsDB->fetchRow($res);

    echo '<tr><td class=bg' . (1 + (($i++) % 2)) . '><img src="images/down.gif">' . $compte . '</td></tr>';

    $enable_logout = 1;

    require __DIR__ . '/html/menu_inbox.php';

    CloseTable();
}

function ListeMail($wbmid, $op, $compte, $serveur, $login, $passwd, $folder, $sort, $sortdir)
{
    $tab_mail = inbox($serveur, $login, $passwd, $folder, $sort, $sortdir);

    switch ($tab_mail) {
        case -1:
            // -1 either the login and/or the password are wrong or the server is down
            require __DIR__ . '/wrong.php';
            break;
        case 0:
            echo '0 mail';
            $loggedin = 1;
            //session_register('loggedin');
            // the mailbox is empty
            $num_msg = 0;
            //require ('./html/menu_inbox.php');
            require_once __DIR__ . '/html/html_top_table.php';
            require_once __DIR__ . '/html/no_mail.php';
            require_once __DIR__ . '/html/html_bottom_table.php';
            //require ('./html/menu_inbox.php');
            break;
        default:

            if (!isset($attach_array)) {
                $attach_array = null;
            }
            go_back_index($attach_array, 'temp', $php_session, $sort, $sortdir, $lang, false);
            //$loggedin = 1;
            //session_register('loggedin');
            // there are messages, we display
            $num_msg = count($tab_mail);
            //echo "$num_msg mails<br>";
            //Affich_Menu_Inbox();

            require_once __DIR__ . '/html/html_top_table.php';
            $delete_button_icon = 1;
            require __DIR__ . '/html/menu_inbox_opts.php';
            $i_inbox = 0;
            while ($tmp = array_shift($tab_mail)) {
                $i_inbox++;

                require __DIR__ . '/html/html_inbox.php';
            }
            require __DIR__ . '/html/menu_inbox_opts.php';
            require_once __DIR__ . '/html/html_bottom_table.php';
            //require ('./html/menu_inbox.php');
            break;
    }
}
