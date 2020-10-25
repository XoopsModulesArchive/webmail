<?php

$custom_footer = "./themes/$theme/footer.php";
if (file_exists($custom_footer)) {
    include $custom_footer;
} else {
    ?>
    </td>
    </tr>
    <tr>
        <td align="center" colspan="2"><a href="http://nocc.sourceforge.net" target="_blank"><img src="themes/<?php echo $theme ?>/img/button.png" border="0" height="31" width="88" alt="Turbinado pelo NOCC"></a></td>
    </tr>
    </table>

    </body>
    </html>
    <?php
}
?>
