function updateQty(productId, qty) {
    window.location = '/hamropasal/cart/?action=update&id=' + productId + '&qty=' + qty;
}

function removeItem(productId) {
    if (confirm('Remove this item?')) {
        window.location = '/hamropasal/cart/?action=remove&id=' + productId;
    }
}