<?php
// redirect to folder-based product page
$id = intval($_GET['id'] ?? 0);
if($id){
    header("Location: /hamropasal/product/?id=$id");
} else {
    header('Location: /hamropasal/products/');
}
exit;
