# ImageHelper Quick Start Guide

## TL;DR

All product images now use the `ImageHelper` class. Images are automatically optimized via Cloudinary (if configured) or served from local storage.

---

## Basic Usage

### Import the Helper
```php
use App\Helpers\ImageHelper;
```

### In Blade Templates (without namespace)
```blade
{{ \App\Helpers\ImageHelper::getProductImage($product->foto_produk) }}
```

---

## Common Use Cases

### 1. Product Image (Full Size)
```blade
<img src="{{ \App\Helpers\ImageHelper::getProductImage($product->foto_produk) }}" 
     alt="{{ $product->nama_produk }}">
```

### 2. Product Thumbnail (Admin List)
```blade
<img src="{{ \App\Helpers\ImageHelper::getProductThumbnail($product->foto_produk, 60, 60) }}" 
     alt="{{ $product->nama_produk }}" 
     class="img-thumbnail">
```

### 3. Product Thumbnail (Product List)
```blade
<img src="{{ \App\Helpers\ImageHelper::getProductThumbnail($product->foto_produk, 200, 200) }}" 
     alt="{{ $product->nama_produk }}">
```

### 4. Conditional with Fallback
```blade
<img src="{{ $product->foto_produk ? \App\Helpers\ImageHelper::getProductImage($product->foto_produk) : 'https://via.placeholder.com/400' }}" 
     alt="{{ $product->nama_produk }}">
```

### 5. In JavaScript
```blade
<img onclick="changeImage('{{ \App\Helpers\ImageHelper::getProductImage($product->foto_produk) }}', this)">
```

---

## API Reference

### getProductImage()
```php
ImageHelper::getProductImage($filename, $width = 600, $height = 600)
```
- **Purpose**: Full-size product image
- **Returns**: Cloudinary URL or local storage path
- **Example**: `{{ ImageHelper::getProductImage('produk/photo.jpg') }}`

### getProductThumbnail()
```php
ImageHelper::getProductThumbnail($filename, $width = 200, $height = 200)
```
- **Purpose**: Thumbnail image with custom dimensions
- **Returns**: Optimized thumbnail URL
- **Example**: `{{ ImageHelper::getProductThumbnail('produk/photo.jpg', 150, 150) }}`

### getCarouselImage()
```php
ImageHelper::getCarouselImage($filename, $width = 800, $height = 500)
```
- **Purpose**: Wide carousel/banner image
- **Returns**: Carousel-sized image URL
- **Example**: `{{ ImageHelper::getCarouselImage('banner/slide1.jpg') }}`

### getCustomImage()
```php
ImageHelper::getCustomImage($filename, $width, $height, $quality = 80)
```
- **Purpose**: Custom sized image with quality control
- **Returns**: Custom image URL
- **Example**: `{{ ImageHelper::getCustomImage('produk/photo.jpg', 400, 400, 90) }}`

---

## How to Add It to New Views

1. **Locate the image code**:
```blade
<!-- Old code -->
<img src="{{ asset('storage/' . $product->foto_produk) }}">
```

2. **Replace with ImageHelper**:
```blade
<!-- New code -->
<img src="{{ \App\Helpers\ImageHelper::getProductImage($product->foto_produk) }}">
```

3. **Or use getProductThumbnail for smaller images**:
```blade
<!-- New code with custom size -->
<img src="{{ \App\Helpers\ImageHelper::getProductThumbnail($product->foto_produk, 300, 300) }}">
```

---

## Configuration

### Enable Cloudinary
Set in `.env`:
```env
CLOUDINARY_URL=cloudinary://api_key:api_secret@cloud_name
```

### Fallback (No Cloudinary)
If `CLOUDINARY_URL` is not set, images automatically fallback to:
```
storage/app/public/{filename}
```

No code changes needed!

---

## Database Schema

Product images stored in:
- **Table**: `db_integrasi_ayu_mart.tb_produk`
- **Column**: `foto_produk`
- **Example**: `produk/abc123.jpg`

---

## Common Issues & Solutions

### ❌ Images not showing?
1. Check if `foto_produk` column has data
2. Verify file exists in `storage/app/public/` or Cloudinary
3. Check DevTools > Network for 404 errors

### ❌ CLOUDINARY_URL not working?
1. Verify URL format: `cloudinary://key:secret@cloud_name`
2. Test with `php artisan tinker`: `config('cloudinary.url')`
3. Fallback to local storage should still work

### ❌ Images too small?
1. Use `getProductImage()` instead of `getProductThumbnail()`
2. Or specify larger dimensions: `getProductThumbnail($file, 600, 600)`

### ❌ Images too large (file size)?
1. Cloudinary automatically optimizes
2. If using local storage, file size will be larger
3. Consider implementing image compression

---

## Performance Tips

### For Admin (Performance-Critical)
Use smaller thumbnails:
```blade
{{ ImageHelper::getProductThumbnail($file, 60, 60) }}    <!-- Admin list -->
{{ ImageHelper::getProductThumbnail($file, 100, 100) }}  <!-- Modal preview -->
```

### For Public Views (User-Facing)
Use appropriate sizes:
```blade
{{ ImageHelper::getProductThumbnail($file, 200, 200) }}  <!-- Product grid -->
{{ ImageHelper::getProductImage($file) }}                <!-- Product detail -->
```

### Mobile Optimization
Current implementation handles mobile well through Cloudinary auto-sizing.

---

## Cloudinary Features (Automatic)

The helper automatically applies:
- ✅ Format conversion (WebP for modern browsers)
- ✅ Quality optimization (auto-adjusting based on format)
- ✅ Responsive sizing (exact dimensions specified)
- ✅ Smart crop (centered on important elements)
- ✅ CDN caching (fast delivery worldwide)

---

## Testing Checklist

- [ ] Image loads in admin section
- [ ] Image loads in public section
- [ ] Thumbnail correct size in list views
- [ ] Full image displays in detail view
- [ ] Fallback works if CLOUDINARY_URL disabled
- [ ] No console errors in DevTools
- [ ] Images load from Cloudinary CDN (if configured)

---

## File Locations

- **Helper Class**: `app/Helpers/ImageHelper.php`
- **Updated Views**: `resources/views/**/*.blade.php`
- **Documentation**: 
  - `PRODUCT_IMAGE_REFACTOR.md` (overview)
  - `IMAGE_REFACTOR_DETAILED.md` (detailed guide)
  - `COMPLETION_REPORT.md` (implementation report)

---

## Quick Copy-Paste Snippets

### Admin Thumbnail
```blade
{{ \App\Helpers\ImageHelper::getProductThumbnail($product->foto_produk, 60, 60) }}
```

### Product List
```blade
{{ \App\Helpers\ImageHelper::getProductThumbnail($product->foto_produk, 200, 200) }}
```

### Product Detail
```blade
{{ \App\Helpers\ImageHelper::getProductImage($product->foto_produk) }}
```

### With Fallback
```blade
{{ $product->foto_produk ? \App\Helpers\ImageHelper::getProductImage($product->foto_produk) : 'https://via.placeholder.com/400' }}
```

---

## Support

For issues or questions:
1. Check this Quick Start Guide
2. Review `COMPLETION_REPORT.md` for detailed info
3. Check `app/Helpers/ImageHelper.php` for method signatures
4. Run verification script: `bash verify-image-refactor.sh`

---

**Last Updated**: 4 Mei 2026  
**Status**: ✅ Production Ready
