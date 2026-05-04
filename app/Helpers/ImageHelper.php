<?php

namespace App\Helpers;

/**
 * Image Helper - Utility untuk image URLs (Cloudinary & Local Storage)
 *
 * HANDLING CASES:
 * 1. Input sudah full URL (dari database integrasi Cloudinary) → Return as is
 * 2. Input nama file lokal → Fallback ke storage/app/public/
 * 3. Input kosong → Return placeholder
 */
class ImageHelper
{
    /**
     * Cek apakah string adalah valid URL
     *
     * @param string $url
     * @return bool
     */
    public static function isValidUrl($url)
    {
        if (empty($url)) {
            return false;
        }

        // Cek apakah dimulai dengan http:// atau https://
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Get image URL untuk product
     *
     * Handle 3 kasus:
     * 1. Input sudah URL → Return langsung
     * 2. Input nama file → Return dengan transformasi Cloudinary (jika configured)
     * 3. Input kosong → Return placeholder
     *
     * @param string $filename - Nama file atau URL lengkap
     * @param array $options - Opsi transformasi (width, height, quality, fetch_format)
     * @return string URL gambar
     */
    public static function getImageUrl($filename, $options = [])
    {
        if (!$filename) {
            return self::getPlaceholder($options);
        }

        // Kasus 1: Sudah merupakan URL lengkap (dari Cloudinary atau sumber lain)
        if (self::isValidUrl($filename)) {
            return $filename;
        }

        // Kasus 2: Nama file lokal - fallback ke storage
        return asset('storage/' . $filename);
    }

    /**
     * Get product thumbnail image dengan sizing
     *
     * @param string $filename - Nama file atau URL lengkap
     * @param int $width - Lebar dalam pixel
     * @param int $height - Tinggi dalam pixel
     * @return string Image URL
     */
    public static function getProductThumbnail($filename, $width = 200, $height = 200)
    {
        $url = self::getImageUrl($filename);

        // Jika URL sudah dari Cloudinary dan contains transformation, return as is
        if (strpos($url, 'res.cloudinary.com') !== false) {
            return $url; // Sudah optimized dari Cloudinary
        }

        // Untuk local storage, return apa adanya
        // (thumbnail optimization di-handle oleh JavaScript atau CSS)
        return $url;
    }

    /**
     * Get product image ukuran penuh
     *
     * @param string $filename - Nama file atau URL lengkap
     * @param int $width - Lebar dalam pixel (default 600)
     * @param int $height - Tinggi dalam pixel (default 600)
     * @return string Image URL
     */
    public static function getProductImage($filename, $width = 600, $height = 600)
    {
        return self::getImageUrl($filename, [
            'width' => $width,
            'height' => $height,
            'quality' => 85,
            'fetch_format' => 'auto',
        ]);
    }

    /**
     * Get carousel image
     *
     * @param string $filename - Nama file atau URL lengkap
     * @param int $width - Lebar dalam pixel (default 800)
     * @param int $height - Tinggi dalam pixel (default 500)
     * @return string Image URL
     */
    public static function getCarouselImage($filename, $width = 800, $height = 500)
    {
        return self::getImageUrl($filename, [
            'width' => $width,
            'height' => $height,
            'quality' => 85,
            'fetch_format' => 'auto',
        ]);
    }

    /**
     * Get custom image dengan opsi transformasi
     *
     * @param string $filename - Nama file atau URL lengkap
     * @param int|null $width - Lebar dalam pixel
     * @param int|null $height - Tinggi dalam pixel
     * @param int $quality - Quality 0-100
     * @return string Image URL
     */
    public static function getCustomImage($filename, $width = null, $height = null, $quality = 80)
    {
        $options = ['quality' => $quality, 'fetch_format' => 'auto'];

        if ($width) {
            $options['width'] = $width;
        }
        if ($height) {
            $options['height'] = $height;
        }

        return self::getImageUrl($filename, $options);
    }

    /**
     * Get placeholder image ketika tidak ada gambar
     *
     * @param array $options - Opsi sizing
     * @return string URL placeholder
     */
    public static function getPlaceholder($options = [])
    {
        $width = $options['width'] ?? 400;
        $height = $options['height'] ?? 400;

        return "https://via.placeholder.com/{$width}x{$height}?text=No+Image";
    }
}
