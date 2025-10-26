# Fix: Images Not Loading on Website

## Quick Diagnosis

### Step 1: Run the Diagnostic
Upload `check_images.php` to your server and access it:
```
http://169.239.251.102:442/~ibrahim.dasuki/check_images.php
```

This will show you:
- Which products have images
- Which images exist on the server
- Which image paths are stored in the database
- Direct links to test images

### Step 2: Most Common Issues & Fixes

#### Issue 1: Images Never Uploaded
**Symptom:** "No image available" everywhere

**Fix:**
1. Go to admin/product.php
2. Edit each product that needs an image
3. Click "Choose File" and select an image
4. Save the product
5. The image will upload to `uploads/` folder

#### Issue 2: Images Uploaded to Wrong Location
**Symptom:** Database has paths like `uploads/u1/p1/image.jpg` but files don't exist

**Fix:**
1. Run the migration tool: `http://169.239.251.102:442/~ibrahim.dasuki/migrate_images.php`
2. Or manually upload the images to the correct location

#### Issue 3: Uploads Folder Not Accessible
**Symptom:** Can't access `http://169.239.251.102:442/~ibrahim.dasuki/uploads/`

**Fix:**
1. Upload `uploads/.htaccess` and `uploads/index.php` to your server
2. Set folder permissions to 755
3. Set file permissions to 644

#### Issue 4: Wrong BASE_URL
**Symptom:** Images not loading because paths resolve incorrectly

**Fix:**
Already fixed in `config/init.php` - make sure you uploaded the updated version

## Testing the Upload System

### 1. Test Image Upload
1. Go to `http://169.239.251.102:442/~ibrahim.dasuki/admin/product.php`
2. Add a new product or edit existing one
3. Upload an image
4. Save

### 2. Check if Image Uploaded
1. Access: `http://169.239.251.102:442/~ibrahim.dasuki/uploads/`
2. Should see files listed (or blank page if index.php works)

### 3. Check Browser Console
1. Open your website
2. Press F12
3. Go to "Console" tab
4. Look for errors like:
   - `Failed to load resource: the server responded with a status of 404`
   - `GET http://.../uploads/... 404 (Not Found)`

### 4. Check Actual Image URLs
1. Run the diagnostic: `check_images.php`
2. Click "Test Link" on any product
3. This will try to load the image directly
4. If you get 404, the file doesn't exist at that path

## Expected Behavior

### When Everything Works:

1. **Upload flow:**
   - Admin uploads image → saves to `uploads/product_123_1698364800_a1b2c3.jpg`
   - Database stores: `uploads/product_123_1698364800_a1b2c3.jpg`

2. **Display flow:**
   - Frontend reads: `uploads/product_123_1698364800_a1b2c3.jpg`
   - Browser requests: `http://169.239.251.102:442/~ibrahim.dasuki/uploads/product_123_1698364800_a1b2c3.jpg`
   - Server serves: the actual image file
   - User sees: the product image ✅

### When It Doesn't Work:

1. **Check database:**
   - Is `image_path` column populated?
   - What value is stored there?

2. **Check server files:**
   - Does `uploads/` folder exist?
   - Are there any files in it?
   - Are file permissions correct (755 for folder, 644 for files)?

3. **Check .htaccess:**
   - Does `.htaccess` exist in `uploads/`?
   - Does it allow image access?

## Quick Fix Checklist

- [ ] Upload `check_images.php` to server
- [ ] Upload `config/init.php` (updated) to server
- [ ] Upload `uploads/.htaccess` to server
- [ ] Upload `uploads/index.php` to server
- [ ] Set `uploads/` folder permissions to 755
- [ ] Run `check_images.php` to diagnose
- [ ] Upload a test image via admin panel
- [ ] Check if image appears on all_products.php

## After Running Diagnostics

The `check_images.php` page will show you exactly:
- Which products have image paths in the database
- Which of those images actually exist on the server
- What the current paths are
- Click-to-test preview links

Use this information to fix the specific issue!

