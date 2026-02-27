<?php

use Sotbit\B2bCabinet\Helper\Config;

global $USER, $APPLICATION;


if (defined("NEED_AUTH") && NEED_AUTH === true) {
    include_once "auth_footer.php";
    return;
}

if ($_GET['IFRAME']) {
    return;
}

include "footer/content_footer.php";
?>

</div>
<!-- /page content -->
</body>

</html>