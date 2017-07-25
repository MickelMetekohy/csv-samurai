# CSVslicer.com

Slice CSV's into manageable chunks.

## Dev on Localmachine

* need php7
* changes to php.ini for MAMP
 - memory_limit = 128M (or higher)
 - upload_max_filesize = 128M (or higher)
 - post_max_size = 128M (or higher)

## Use

* `npm install` (no scripts yet).
* Go through `app/index.php` to set the default values and database creds.
* Start uploading data.

* Save excel as CSV UTF-8 (comma delimited).
* Open CSV, delete blank rows, and save again.
* Upload to CSVslicer.com.
* Set Rows per slice.
* Set header option.
* Slice.
* Download zip.

## @TODO

* Setup build process with webpack or gulp.
* Why is one file not saved in the zip file.
* create only zip not folder on the server.
* Download link.
* GUI rows per slice input.
* GUI header on all slices / header on first slice / no header.
* file dorp area.
* test if UTF-8.
* GUI for line endings / convert line endings / crlf.
* trim the end of the file, remove empty rows (rows that only have commas).
* https.
* save uploads.
* structure/naming of the zip.
