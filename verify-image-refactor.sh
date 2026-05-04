#!/bin/bash

# Product Image Refactor - Verification Script
# Run this script to verify all changes were applied correctly

echo "=========================================="
echo "Product Image Refactor - Verification"
echo "=========================================="
echo ""

cd /Users/macbook/Documents/Indah/CRM-terintegrasi

# 1. Check if ImageHelper exists
echo "1. Checking ImageHelper class..."
if [ -f "app/Helpers/ImageHelper.php" ]; then
    echo "   ✓ ImageHelper.php exists"
    LINES=$(wc -l < app/Helpers/ImageHelper.php)
    echo "   ✓ File size: $LINES lines"
else
    echo "   ✗ ImageHelper.php NOT FOUND!"
fi
echo ""

# 2. Check for old asset patterns in views
echo "2. Checking for old asset patterns in Blade views..."
OLD_PATTERN_COUNT=$(grep -r "asset('storage/' . \$product->foto_produk)" resources/views/ 2>/dev/null | wc -l)
if [ $OLD_PATTERN_COUNT -eq 0 ]; then
    echo "   ✓ No old asset('storage/' . \$product->foto_produk) patterns found"
else
    echo "   ✗ Found $OLD_PATTERN_COUNT old patterns still in views!"
fi
echo ""

# 3. Check for new ImageHelper usage
echo "3. Checking for ImageHelper usage in Blade views..."
NEW_PATTERN_COUNT=$(grep -r "ImageHelper::get" resources/views/ 2>/dev/null | wc -l)
echo "   ✓ Found $NEW_PATTERN_COUNT ImageHelper method calls"
echo ""

# 4. List all updated files
echo "4. Files updated with ImageHelper:"
echo ""
grep -l "ImageHelper::get" resources/views/**/*.blade.php 2>/dev/null | sort | while read file; do
    COUNT=$(grep -c "ImageHelper::get" "$file")
    echo "   ✓ $(basename $file) - $COUNT instance(s)"
done
echo ""

# 5. Check CLOUDINARY_URL in .env
echo "5. Checking environment configuration..."
if grep -q "CLOUDINARY_URL" .env 2>/dev/null; then
    echo "   ✓ CLOUDINARY_URL found in .env"
    # Don't expose the actual URL, just confirm it exists
    URL_LENGTH=$(grep "CLOUDINARY_URL" .env | grep -o "=.*" | wc -c)
    if [ $URL_LENGTH -gt 10 ]; then
        echo "   ✓ CLOUDINARY_URL has a value"
    else
        echo "   ⚠ CLOUDINARY_URL might be empty"
    fi
else
    echo "   ⚠ CLOUDINARY_URL not found in .env (will fallback to local storage)"
fi
echo ""

# 6. Summary statistics
echo "6. Summary Statistics:"
echo ""
TOTAL_VIEWS=$(find resources/views -name "*.blade.php" | wc -l)
UPDATED_VIEWS=$(grep -l "ImageHelper::get" resources/views/**/*.blade.php 2>/dev/null | wc -l)
echo "   • Total Blade views: $TOTAL_VIEWS"
echo "   • Views with ImageHelper: $UPDATED_VIEWS"
echo ""

echo "=========================================="
echo "Verification Complete!"
echo "=========================================="
echo ""
echo "Next Steps:"
echo "1. Start the Laravel development server: php artisan serve"
echo "2. Visit: http://localhost:8000/product (or any product page)"
echo "3. Check DevTools > Network to verify images load correctly"
echo "4. If CLOUDINARY_URL is set, images should come from res.cloudinary.com"
echo ""
