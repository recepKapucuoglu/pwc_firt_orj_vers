<?php
session_start();
session_destroy();
echo '<script language=\'javascript\'>location.href=\'/\'</script>';
exit();
?>