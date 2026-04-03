<?php
/**
 * Helper function to generate product image URLs from Unsplash
 * Falls back to database image if available, otherwise generates URL based on category
 */
function getProductImageUrl($productName, $category, $dbImage = null) {
    // If product has a valid/custom image URL, use it
    if (!empty($dbImage) && $dbImage !== 'https://via.placeholder.com/300x220?text=Product') {
        return htmlspecialchars($dbImage);
    }
    
    // Map categories to Unsplash keywords
    $categoryImages = [
        'grocery' => 'fresh-groceries',
        'fruits' => 'fresh-fruits',
        'vegetables' => 'fresh-vegetables',
        'dairy' => 'milk-dairy',
        'bread' => 'fresh-bread',
        'snacks' => 'snacks-chips',
        'beverages' => 'drinks-beverages',
        'bakery' => 'bakery-items',
        'household' => 'household-cleaning',
        'personal-care' => 'personal-hygiene',
        'cleaning' => 'cleaning-products',
        'spices' => 'spices-seasonings',
        'oil' => 'cooking-oil',
        'rice' => 'rice-grains',
        'flour' => 'flour-wheat',
        'sugar' => 'sugar-sweetener',
        'salt' => 'salt',
        'eggs' => 'eggs',
        'meat' => 'meat-fresh',
        'fish' => 'seafood-fish',
        'frozen' => 'frozen-foods',
        'canned' => 'canned-goods',
        'pasta' => 'pasta-noodles',
        'sauce' => 'cooking-sauce',
        'juice' => 'fruit-juice',
        'coffee' => 'coffee-beans',
        'tea' => 'tea-leaves',
        'water' => 'mineral-water',
        'health' => 'health-vitamins'
    ];
    
    $categoryLower = strtolower($category);
    $keyword = isset($categoryImages[$categoryLower]) ? $categoryImages[$categoryLower] : str_replace(' ', '-', $categoryLower);
    
    // Use Unsplash source with category keyword and product name hash for consistency
    return 'https://source.unsplash.com/300x220/?'.$keyword.'&sig='.md5($productName);
}

/**
 * Generate larger image URL for product detail pages
 */
function getProductDetailImageUrl($productName, $category, $dbImage = null) {
    // If product has a valid/custom image URL, use it
    if (!empty($dbImage) && $dbImage !== 'https://via.placeholder.com/300x220?text=Product') {
        return htmlspecialchars($dbImage);
    }
    
    $categoryImages = [
        'grocery' => 'fresh-groceries',
        'fruits' => 'fresh-fruits',
        'vegetables' => 'fresh-vegetables',
        'dairy' => 'milk-dairy',
        'bread' => 'fresh-bread',
        'snacks' => 'snacks-chips',
        'beverages' => 'drinks-beverages',
        'bakery' => 'bakery-items',
        'household' => 'household-cleaning',
        'personal-care' => 'personal-hygiene',
        'cleaning' => 'cleaning-products',
        'spices' => 'spices-seasonings',
        'oil' => 'cooking-oil',
        'rice' => 'rice-grains',
        'flour' => 'flour-wheat',
        'sugar' => 'sugar-sweetener',
        'salt' => 'salt',
        'eggs' => 'eggs',
        'meat' => 'meat-fresh',
        'fish' => 'seafood-fish',
        'frozen' => 'frozen-foods',
        'canned' => 'canned-goods',
        'pasta' => 'pasta-noodles',
        'sauce' => 'cooking-sauce',
        'juice' => 'fruit-juice',
        'coffee' => 'coffee-beans',
        'tea' => 'tea-leaves',
        'water' => 'mineral-water',
        'health' => 'health-vitamins'
    ];
    
    $categoryLower = strtolower($category);
    $keyword = isset($categoryImages[$categoryLower]) ? $categoryImages[$categoryLower] : str_replace(' ', '-', $categoryLower);
    
    // Use larger size for detail pages (600x600)
    return 'https://source.unsplash.com/600x600/?'.$keyword.'&sig='.md5($productName);
}
?>
