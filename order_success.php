<?php
$id = intval($_GET['id'] ?? 0);
if($id){
    header("Location: /hamropasal/order_success/?id=$id");
} else {
    header('Location: /hamropasal/');
}
exit;
