<form action="cart.php" method="POST">
    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
    <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
    <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
</form>