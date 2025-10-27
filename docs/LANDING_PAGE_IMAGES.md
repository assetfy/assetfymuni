# AssetFy Landing Page - Image Upload Instructions

This document provides instructions for uploading images for the AssetFy landing page.

## Required Images

### 1. Carousel Images (Hero Section)

Upload three images for the hero carousel to the following locations:

- **public/images/carousel/slide1.jpg** - Gestión de Inventario
  - Recommended size: 800x600px
  - Format: JPG or PNG
  - Content: Should showcase inventory management features

- **public/images/carousel/slide2.jpg** - Control de Proveedores
  - Recommended size: 800x600px
  - Format: JPG or PNG
  - Content: Should showcase supplier management features

- **public/images/carousel/slide3.jpg** - Trazabilidad de Fabricación
  - Recommended size: 800x600px
  - Format: JPG or PNG
  - Content: Should showcase manufacturing traceability features

### 2. Integration Diagram

Upload the integration diagram to:

- **public/images/integration-diagram.png** - Cómo se Integran
  - Recommended size: 1000x600px
  - Format: PNG (for transparency support)
  - Content: Should show how Inventory, Supplier, and Manufacturer modules connect

## Steps to Upload Images

### Option 1: Direct Upload (Recommended)

1. Create the directories if they don't exist:
   ```bash
   mkdir -p public/images/carousel
   ```

2. Upload your images to the appropriate directories using FTP, SSH, or your hosting panel's file manager.

3. Ensure the images have the correct names as listed above.

4. Check file permissions (should be readable by the web server, typically 644 for files).

### Option 2: Using Laravel Storage

If you prefer to use Laravel's storage system:

1. Upload images to `storage/app/public/images/carousel/`

2. Create a symbolic link from `public/storage` to `storage/app/public`:
   ```bash
   php artisan storage:link
   ```

3. Update the image paths in `resources/views/landing.blade.php`:
   - Change `{{ asset('images/carousel/slide1.jpg') }}` to `{{ asset('storage/images/carousel/slide1.jpg') }}`
   - Apply this change to all image references

## Image Optimization Tips

1. **Compress images** before uploading to reduce load times
   - Use tools like TinyPNG, ImageOptim, or Squoosh
   - Target: Under 200KB per image

2. **Use appropriate formats**:
   - JPG for photographs
   - PNG for graphics with transparency
   - WebP for better compression (with fallbacks)

3. **Provide alt text** - Already included in the HTML for accessibility

4. **Responsive images** - The current implementation uses CSS to make images responsive

## Fallback Behavior

If images are not uploaded, the landing page will display placeholder icons with text labels:
- Carousel slides will show Font Awesome icons representing each feature
- Integration diagram will show a simplified visual representation

This ensures the page remains functional and professional even before images are uploaded.

## Testing After Upload

1. Visit the landing page at your domain root: `https://yourdomain.com/`
2. Check that all images load correctly
3. Verify the carousel autoplay functionality (should change every 3 seconds)
4. Test on mobile devices for responsive behavior
5. Check browser console for any 404 errors

## Need Help?

If you encounter issues with image uploads or the landing page:
1. Check file permissions
2. Verify file paths are correct
3. Clear Laravel cache: `php artisan cache:clear`
4. Clear browser cache

For technical support, contact: info@assetfy.com
