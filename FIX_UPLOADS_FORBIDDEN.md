# Fix: Forbidden Error on Uploads Directory

## Issue
Accessing `http://169.239.251.102:442/~ibrahim.dasuki/uploads/` shows "Forbidden" error.

## Solutions

### Option 1: Upload the Updated Files
Upload these files to your server:
- `uploads/.htaccess` (NEW - simplified version)
- `uploads/index.php` (already created)

### Option 2: Fix Directory Permissions on Server

Connect to your server via SSH or use your hosting control panel, then run:

```bash
cd ~/public_html  # or wherever your files are
chmod 755 uploads
chmod 644 uploads/.htaccess
chmod 644 uploads/index.php
```

Or if you prefer, set the permissions through your hosting control panel's file manager.

### Option 3: Create .htaccess on Server Directly

If you can't upload files, create a new `.htaccess` file in the `uploads/` directory on your server with this content:

```apache
# Allow image files to be accessed directly
<FilesMatch "\.(jpg|jpeg|png|gif|webp|svg|bmp|ico)$">
    Allow from all
</FilesMatch>

# Use index.php when accessing the directory
DirectoryIndex index.php

# Disable directory browsing
Options -Indexes
```

### Option 4: Check Apache Configuration

The issue might be that your server has Apache's `AllowOverride` set to `None`. Contact your hosting provider and ask them to ensure `AllowOverride All` is set for your directory.

## Test

After applying the fix:

1. **Access the directory:** `http://169.239.251.102:442/~ibrahim.dasuki/uploads/`
   - Should show a blank page (index.php) instead of directory listing
   
2. **Upload an image via admin panel**
   - Go to admin/product.php
   - Add or edit a product
   - Upload an image
   
3. **Check if image displays:**
   - Go to all_products.php
   - Product image should display
   - Check browser console for any errors

## Expected Result

- **Direct directory access:** Shows blank page (not "Forbidden" and not directory listing)
- **Image file access:** Images should load properly (e.g., `uploads/product_123.jpg` should display)

## Quick Fix via FTP/File Manager

1. Navigate to `uploads/` folder on your server
2. Delete the current `.htaccess` file
3. Create a new `.htaccess` file with the simplified content above
4. Make sure `index.php` exists in the uploads folder
5. Set permissions: `uploads/` folder = 755, files inside = 644

## Files That Should Be in uploads/ Directory

```
uploads/
├── .htaccess      (simplified version)
├── index.php      (blocks directory listing)
└── (uploaded images will be here)
```

