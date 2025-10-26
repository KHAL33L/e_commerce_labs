# Server Deployment Checklist

## Files to Upload to Server

### 1. Fixed Upload Script
- `actions/upload_product_image_action.php` (UPDATED)

### 2. Fixed Config File  
- `config/init.php` (UPDATED - now detects server automatically)

### 3. Uploads Directory & Security Files
- `uploads/.htaccess` (allows images, blocks PHP)
- `uploads/index.php` (blocks directory listing)

### 4. Optional Diagnostic Tools
- `check_images.php` (to check current image status)
- `migrate_images.php` (to fix old image paths if needed)

## What Changed

### config/init.php
The BASE_URL is now dynamically detected:
- **Server**: `http://169.239.251.102:442/~ibrahim.dasuki`
- **Local**: `http://localhost/e_commerce_labs`
- Automatically switches based on the server environment

### uploads/ Directory
- Created in project root
- Includes security files (`.htaccess` and `index.php`)
- Where all product images will be stored

## Testing on Server

1. **Upload all files above to your server**

2. **Verify uploads directory exists:**
   ```
   http://169.239.251.102:442/~ibrahim.dasuki/uploads/
   ```
   Should show a blank page (not directory listing) thanks to index.php

3. **Test image upload:**
   - Go to admin panel
   - Add or edit a product
   - Upload an image
   - Check if it saves

4. **Verify image displays:**
   - Go to `all_products.php`
   - Product image should display
   - Go to `single_product.php`
   - Product image should display

## Troubleshooting

### If images don't display after upload:
1. Check if file was created in `uploads/` folder
2. Check browser console for 404 errors
3. Verify the `image_path` in the database matches actual files
4. Run `check_images.php` to diagnose

### If upload fails:
1. Check server error logs
2. Verify `uploads/` folder permissions (chmod 755)
3. Verify `uploads/` folder is writable
4. Check PHP upload limits in php.ini

