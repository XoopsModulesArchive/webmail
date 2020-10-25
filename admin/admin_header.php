<?php

include '../../../mainfile.php';
require_once $xoopsConfig['root_path'] . 'class/xoopsmodule.php';
if ($xoopsUser) {
    $xoopsModule = XoopsModule::getByDirname('freecontent');

    if (!$xoopsUser->isAdmin($xoopsModule->mid())) {
        redirect_header($xoopsConfig['xoops_url'] . '/', 3, _NOPERM);

        exit();
    }
} else {
    redirect_header($xoopsConfig['xoops_url'] . '/', 3, _NOPERM);

    exit();
}
if (file_exists('../language/' . $xoopsConfig['language'] . '/admin.php')) {
    include '../language/' . $xoopsConfig['language'] . '/admin.php';
} else {
    include '../language/english/admin.php';
}
