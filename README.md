# CSV Samurai

Slice CSV's into manageable chunks.

HTML PHP PUG SCSS JS WEBPACK GIT ESLINT YARN FILE-LOADING IMAGE-OPTIMIZATION

License: [MIT](https://choosealicense.com/licenses/mit/)

## Dev on Localmachine

* need php7
* changes to php.ini for MAMP
 - memory_limit = 128M (or higher)
 - upload_max_filesize = 128M (or higher)
 - post_max_size = 128M (or higher)

## Dev

* clone repo
* in root folder run `yarn install` to install from package.js
* in root folder run `yarn start` to run webpack
* _develop is the development working folder
* _distribute is the distribution folder
* HTML / PUG:
  * in the _develop/ folder
  * PUG files need to be registered in the webpack plugin section 
  ```JS
    new HtmlWebpackPlugin({
      filename: 'index.html',
      inject: false,
      template: './_develop/index.pug',
    }),
  ```
* IMAGES
  * in the _develop/images folder
  * images folder will be copied to _distribute
* SCSS:
  * in the _develop/scss folder
  * all components are imported to app.scss
  * app.scss is imported to app.js so webpack can bundle it
  * scss bundle output is in _distribute/css/app.bundle.css
  * file path must be from the _develop folder so webpack can deal with them
  * please use BEM [http://getbem.com/](http://getbem.com/)
* JS:
  * in the _develop/js folder
  * all libraries and components are imported to app.js
  * js bundle output is in _distribute/js/app.bundle.js
  * eslint airbnb preset eslint-config-airbnb with react (eslint-config-airbnb-base without react)
* FONTS
  * in the _develop/fonts folder
  * images folder will be copied to _distribute
* PACKAGES
  * jquery
  * bootstrap
  * slick-carousel

See package.json and webpack.config.js for more details on the build process.

## Use

* Save excel as CSV UTF-8 (comma delimited).
* Open CSV, delete blank rows, and save again.
* Upload to CSVslicer.com.
* Set Rows per slice.
* TODO Set header option.
* Slice.
* TODO Download zip.

## @TODO

* Download link.
* GUI header on all slices / header on first slice / no header.
* GUI for line endings / convert line endings / crlf.
* structure/naming of the zip.

## @NOTE

### CSV

https://www.drupal.org/node/622710

"Comma-separated values", a simple file format used to move tabular data between programs and databases. See also Comma-separated values on Wikipedia. The needed/correct line-ending-char(s) of a Feeds-imported CSV-file depends on the type of the operating system of the www-server:

If you are using a Linux-Server, use only "LF" at the line-end of the CSV-file.
If you are using a Windows-Server, use "CR+LF" at the line-end of the CSV-file.
If you are using a Mac-Server, use only "CR" at the line-end of the CSV-file.
The changing of the line-end of the CSV-file before importing is important, if the source of the CSV-file (e.g. your computer or the database of the CSV-file) has a different operating system!
For the meaning of "LF" (line feed) and "CR" (carriage return) see http://en.wikipedia.org/wiki/Newline#Representations.

If you have date fields in your input file:
The only allowed date formats in the input-file are:
"YYYY-MM-DD" or "MM/DD/YYYY" or "DD.MM.YYYY".
The delimiters are different for each format and have to be used properly!
This is only for the import!
Within e.g. a view, you can chose the format of the output.

When the Feeds-imported CSV-file is in "UTF8 with BOM"-format, then the import of special characters (letters like €, £, ß, ö ,ü, ä, Ö, Ä and Ü or other non-ASCII-signs) is without problems. See also UTF8 - Byte order mark on Wikipedia.
You can use a good editor like 'notepad++ on windows' or 'LibreOffice Calc', both when indicated: '... Portable', (or 'MS Excel') to change this.
Tip:
Use "Save as" and change the needed properties before and/or during saving the file ("before / during" is depending on the program used).
