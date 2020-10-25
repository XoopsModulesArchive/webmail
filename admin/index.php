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

require_once 'admin_header.php';
require_once $xoopsConfig['root_path'] . 'class/module.textsanitizer.php';

/*********************************************************/
/*			   FreeContent - Admin				  */
/*********************************************************/
/*
include ($xoopsConfig['root_path']."header.php");
$myts = new MyTextSanitizer();
$xoopsModule->printAdminMenu();
echo "<br>";

switch($op) {
    case "edit":
        fc_admin_edit($id);
        break;

    case "editdb":
        $form_title = htmlspecialchars($form_title);
        $form_comment = htmlspecialchars($form_comment);

        if ($form_hide){
            $form_hide = 1;
        }
        else{
            $form_hide = 0;
        }

        $q = "UPDATE ".$xoopsDB->prefix()."_freecontent SET title='".$form_title."', adress='".$form_adress."', comment='".$form_comment."', hide='".$form_hide."', hits='".$form_hits."' WHERE id='".$form_id."'";

        if ($xoopsDB->queryF($q)){
            fc_admin_message(_FC_EDIT_DONE,0,"");
        }
        else{
            fc_admin_message(_FC_EDIT_DBERROR,1,"");
        }

        fc_admin_list();
        fc_admin_add();
        fc_footer();
        break;

    case "del":

        if($xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix()."_freecontent WHERE id=".$id."")){
            fc_admin_message(_FC_DEL_OK,0,"");
        }
        else{
            fc_admin_message(_FC_EDIT_DBERROR,1,"");
        }
        fc_admin_list();
        fc_admin_add();
        fc_footer();
        break;

    case "delconfirm":
        OpenTable();
        $result = $xoopsDB->queryF("SELECT id, title, comment FROM ".$xoopsDB->prefix()."_freecontent WHERE id='".$id."'",1);
        $fc_item = $xoopsDB->fetcharray($result);

        echo "<center><h4>"._FC_DEL_REALLY."</h4><br>".$fc_item['id']." <b>|</b> ".$fc_item['title']." <b>|</b> ".$fc_item['comment']."<br><a href=\"./index.php?op=del&id=".$id."\"><h4>"._FC_DELETE."</h4></a><center>";
        CloseTable();
        fc_footer();
        break;

    case "add":

        $form_title = htmlspecialchars($form_title);
        $form_comment = htmlspecialchars($form_comment);

        if ($form_hide){
            $form_hide = 1;
        }
        else{
            $form_hide = 0;
        }
        $q = "INSERT INTO ".$xoopsDB->prefix()."_freecontent (title, adress, comment, hide) VALUES ('".$form_title."', '".$form_adress."', '".$form_comment."', ".$form_hide.")";
        if ($xoopsDB->queryF($q)){
            fc_admin_message(_FC_EDIT_DONE,0,"");
        }
        else{
            fc_admin_message(_FC_EDIT_DBERROR,1, "");
        }

        fc_admin_list();
        fc_admin_add();
        fc_footer();
        break;

    case "createdb":
        fc_createdb();
        fc_footer();
        break;

    default:
        fc_admin_message(_FC_DB_MUST,0,_FC_DB_LINK);
        fc_admin_list();
        fc_admin_add();
        fc_footer();
        break;
}


include "admin_footer.php";

*/

//*****************************************************************************************
//*** Functions-declaration ***************************************************************
//*****************************************************************************************

function fc_admin_list()
{
    global $xoopsDB, $xoopsConfig;

    OpenTable();

    echo '<table border=0 cellpadding=2 cellspacing=2 width="95%"><tr><td colspan=8><div align="center">
		<b>' . _FC_LIST_HEADER . '</b></div></td></tr>
			<tr>
				<td><i>' . _FC_ID . '</i></td>
				<td><i>' . _FC_TITLE . '</i></td>
				<td><i>' . _FC_ADRESS . '</i></td>
				<td><i>' . _FC_COMMENT . '</i></td>
				<td><i>' . _FC_HIDE . '</i></td>
				<td><i>' . _FC_HITS . '</i></td>
				<td><i>' . _FC_EDIT . '</i></td>
				<td><i>' . _FC_DELETE . '</i></td>
			</tr>';

    // get all rows from db

    $result = $xoopsDB->queryF('SELECT id, title, adress, comment, hide, hits FROM ' . $xoopsDB->prefix() . '_freecontent');

    while (false !== ($fc_item = $xoopsDB->fetchArray($result))) {
        echo '<tr><td>' . $fc_item['id'] . '</td>
				<td>' . $fc_item['title'] . '</td>
					    <td>' . $fc_item['adress'] . '</td>
				<td>' . $fc_item['comment'] . '</td>
				<td>' . $fc_item['hide'] . '</td>
				<td>' . $fc_item['hits'] . '</td>
				<td><a href="./index.php?op=edit&id=' . $fc_item['id'] . '">' . _FC_EDIT . '</a></td>
				<td><a href="./index.php?op=delconfirm&id=' . $fc_item['id'] . '">' . _FC_DELETE . '</a></td></tr>';
    }

    echo '</table>';

    CloseTable();
}

function fc_admin_add()
{
    global $xoopsConfig;

    OpenTable();

    echo '<form name="Add Content" action="./index.php" method="post"><div align="center">
			 <h4>' . _FC_ADD_HEADER . '</h4>
		</div><table border="0" cellpadding="2" cellspacing="2" width="95%">
		<tr>
			<td align="right">' . _FC_TITLE . ':</td>
						<td><input type="text" name="form_title" size="50" tabindex="1"> </td>
		</tr>
		<tr>
						<td align="right">' . _FC_ADRESS . ':</td>
						<td>' . $xoopsConfig['root_path'] . ' <input type="text" name="form_adress" size="100" maxlength="255" value="modules/freecontent/content/example.php" tabindex="2"></td>
		</tr>
		<tr>
						<td align="right">' . _FC_COMMENT . ':</td>
						<td><input type="text" name="form_comment" size="100" tabindex="3"></td>
		</tr>
		<tr>
						<td align="right">' . _FC_HIDE . ':</td>
						<td><input type="checkbox" value="checkboxValue" name="form_hide" tabindex="4"> ' . _FC_ADD_HIDELONG . '</td>
		</tr>
			<tr height="10">
						<td align="right" height="10"></td>
						<td height="10"><input type="hidden" value="add" name="op"></td>
		</tr>
		<tr>
						<td align="right"></td>
						<td><input type="submit" name="add" tabindex="5" value="' . _FC_ADD_SUBMIT_ADD . '"> <input type="reset" tabindex="6" value="' . _FC_ADD_SUBMIT_RESET . '"></td>
		</tr></table></form>';

    CloseTable();
}

function fc_admin_edit($id)
{
    global $xoopsConfig, $xoopsDB;

    $result = $xoopsDB->queryF('SELECT title, adress, comment, hide, hits FROM ' . $xoopsDB->prefix() . "_freecontent WHERE id='" . $id . "'");

    $fc_item = $xoopsDB->fetchArray($result);

    if (0 == $fc_item['hide']) {
        $hide_checked = '';
    } else {
        $hide_checked = 'checked';
    }

    OpenTable();

    echo '<form name="Edit Content" action="./index.php" method="post"><div align="center">
			 <h4>' . _FC_EDIT_HEADER . '</h4>
		</div><table border="0" cellpadding="2" cellspacing="2" width="95%">
		<tr>
			<td align="right">' . _FC_ID . ':</td>
						<td><input type="text" value="' . $id . '" name="form_id" size="5" readonly> </td>
		</tr>
		<tr>
			<td align="right">' . _FC_TITLE . ':</td>
						<td><input type="text" value="' . $fc_item['title'] . '" name="form_title" size="50" tabindex="1"> </td>
		</tr>
		<tr>
						<td align="right">' . _FC_ADRESS . ':</td>
						<td>' . $xoopsConfig['root_path'] . ' <input type="text" name="form_adress" size="100" maxlength="255"  value="' . $fc_item['adress'] . '" tabindex="2"></td>
		</tr>
		<tr>
						<td align="right">' . _FC_COMMENT . ':</td>
						<td><input type="text" value="' . $fc_item['comment'] . '" name="form_comment" size="100" tabindex="3"></td>
		</tr>
		<tr>
						<td align="right">' . _FC_HIDE . ':</td>
						<td><input type="checkbox" value="1" name="form_hide" tabindex="4" ' . $hide_checked . '> ' . _FC_ADD_HIDELONG . '</td>
		</tr>
		<tr>
						<td align="right">' . _FC_HITS . ':</td>
						<td><input type="text" value="' . $fc_item['hits'] . '" name="form_hits" size="11" tabindex="5"></td>
		</tr>
			<tr height="10">
						<td align="right" height="10"></td>
						<td height="10"><input type="hidden" value="editdb" name="op"></td>
		</tr>
		<tr>
						<td align="right"></td>
						<td><input type="submit" name="add" tabindex="6" value="' . _FC_SUBMIT_UPD . '"> <input type="reset" tabindex="7" value="' . _FC_ADD_SUBMIT_RESET . '"></td>
		</tr></table></form>';

    CloseTable();
}

function fc_admin_message($message_text, $error_color, $additional_text)
{
    OpenTable();

    if (0 == $error_color) {
        //Good News

        echo '<center><br><h4>' . $message_text . '</h4><br>' . $additional_text . '</center>';
    } else {
        //Bad News

        echo '<center><br><font color="red"><h4>' . $message_text . '</h4><br></font>' . $additional_text . '</center>';
    }

    CloseTable();
}

function fc_footer()
{
    echo '<br><b>FreeContent v0.60 is a free software<br> ...written by <a href="mailto:tiger@sibserag.de">Stefan SIBSERAG Oeser</a><br>...and released under the <a target"_blank" href="http://www.gnu.org">GNU/GPL license.</a><br>Find updates and infos here: <a target"_blank" href="http://coding.sibserag.de">http://coding.sibserag.de</a><br>***';
}

function fc_createdb()
{
    global $xoopsDB;

    // create table and structure

    $q1 = $xoopsDB->queryF(
        'CREATE TABLE `' . $xoopsDB->prefix() . "_freecontent` (
  `id` int(5) unsigned NOT NULL auto_increment,
  `title` varchar(150) NOT NULL default '',
  `type` tinyint(10) unsigned NOT NULL default '0',
  `design` tinyint(4) unsigned NOT NULL default '0',
  `hide` tinyint(4) unsigned NOT NULL default '0',
  `adress` varchar(255) default NULL,
  `comment` varchar(255) default NULL,
  `image` varchar(255) default NULL,
  `hits` int(6) unsigned NOT NULL default '0',
  PRIMARY KEY	(`id`),
  UNIQUE KEY `index` (`id`),
  KEY `index_2` (`id`)
) ENGINE = ISAM COMMENT='FreeContent for Xoops - by SibSerag (tiger@sibserag.de)'"
    );

    // insert content

    $q2 = $xoopsDB->queryF('INSERT INTO `' . $xoopsDB->prefix() . "_freecontent` VALUES (1, 'FreeContent', 0, 0, 0, 'modules/freecontent/content/example.php', 'The example-file.', NULL, 0)");

    if ($q1 && $q2) {
        fc_admin_message(_FC_DB_CREATED, 0, '');
    } else {
        fc_admin_message(_FC_EDIT_DBERROR, 1, '');
    }
}
