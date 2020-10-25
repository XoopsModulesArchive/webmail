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
// **************************************************************************//
// * Function: b_freecontent_show									 *//
// * Output  : Returns the links to FC content with hide=0				 *//
// **************************************************************************//

function b_webmail_show($options)
{
    global $xoopsDB, $xoopsConfig, $xoopsUser;

    $block = [];

    $block['title'] = _WBM_BLOCK_TITLE;

    $block['content'] = '<small>';

    if ($xoopsUser) {
        $sqlstr = 'SELECT wbmid,login, password, serveur, type, compte FROM ' . $xoopsConfig['prefix'] . '_webmail  WHERE uid= ' . $xoopsUser->uid() . ' ';

        $res = $xoopsDB->query($sqlstr);

        while (list($loc_wbmid, $login, $password, $serveur, $type, $compte) = $xoopsDB->fetchRow($res)) {
            $i++;

            $bg = 'bg' . (2 + ($i % 2));

            $block['content'] .= '<a href=' . $xoopsConfig['xoops_url'] . "/modules/webmail/index.php?wbmid=$loc_wbmid>$compte</a><br>\n";
        }

        if (0 == $i) {
            $block['content'] .= '<a href=' . $xoopsConfig['xoops_url'] . "/modules/webmail/index.php?op=ajouter_compte>Configurer</a><br>\n";
        }
    } else {
        $block['content'] .= '<a href=' . $xoopsConfig['xoops_url'] . '/modules/webmail>' . _WBM_MOD_NAME . "</a>\n";

        $block['content'] .= '<br>';
    }

    $block['content'] .= '</small>';

    return $block;
}
