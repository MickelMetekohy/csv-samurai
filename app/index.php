<?php
  $up_error_mes = '';
  $up_succes_mes = '';

  /**
   * recursive delete files and directeries untill $src is reached
   */
  function rrmdir($src) {
      $dir = opendir($src);
      while(false !== ( $file = readdir($dir)) ) {
          if (( $file != '.' ) && ( $file != '..' )) {
              $full = $src . '/' . $file;
              if ( is_dir($full) ) {
                  rrmdir($full);
              }
              else {
                  unlink($full);
              }
          }
      }
      closedir($dir);
      rmdir($src);
  }

  // echo '<pre>';
  // print_r($GLOBALS);
  // echo '</pre>';

  if(isset($_FILES['csvfile'])) {
    // The file
    $up_file      = $_FILES['csvfile'];
    // The file upload properties
    $up_name      = $up_file['name'];
    $up_tmp_name  = $up_file['tmp_name'];
    $up_size      = $up_file['size'];
    $up_error     = $up_file['error'];

    // check and model the file extention
    $up_ext       = explode('.', $up_name);
    $up_ext       = strtolower(end($up_ext));
    $up_allowed_ext = array('csv');

    // checks
    // Set Max upload size
    $up_max_size  = 128*1024*1024;
    $up_error_mes .= $up_error !== 0 ? 'An error occurred' : false;
    $up_error_mes .= $up_size > $up_max_size ? 'The file you are trying to upload is to big' : false;
    $up_error_mes .= !in_array($up_ext, $up_allowed_ext) ? 'The file you are trying to upload does not have the right extention' : false;

    if($up_error_mes === '' ) {
      // create unique id with random int and time
      $file_stamp = time() . uniqid('_', true);
      // set the name and location for the uploaded
      $file_name_up = $file_stamp . '.' . $up_ext;
      $file_destination = '../uploads/' . $file_name_up;
      // slice after count
      $slice_count = 1000;

      if(move_uploaded_file($up_tmp_name, $file_destination)) {
        // a file was uploaded from the temp location to the uploads location , do something with it
        $up_succes_mes .= 'Your file upload was succesfull';

        // keep track in the loop
        $line_count = 0;
        $file_slice_count = 1;
        $header_line;

        // set the src file
        $srcFile = new SplFileObject($file_destination);
        $count_rowes = count($srcFile);
        // create a zip file
        $zip = new ZipArchive();
        $zip_name = '../slices/'. $file_stamp .'.zip';
        if ($zip->open($zip_name, ZipArchive::CREATE)!==TRUE) {
            exit("cannot open <$filename>\n");
        }

        // foreach line in the src file, create slices
        foreach ($srcFile as $key => $line) {
          // if this is the first loop save the header
          if($line_count == 0 && $file_slice_count == 1) {
            $header_line = $line;
          }
          // if the line count is less than max rows per slice
          if($line_count < $slice_count && $line_count > 0 ) {
            $destFile->fwrite($line);
            $line_count++;
          }
          // if line count is 0, create new slice and collect the slices in a folder with the unique stamp
          if($line_count == 0) {
            if (!file_exists('../slices/' . $file_stamp)) {
                mkdir('../slices/' . $file_stamp, 0777, true);
            }
            $destFile = new SplFileObject('../slices/'. $file_stamp. '/' . $up_name .'_'. $file_slice_count .'.csv', 'w+');
            // $destFile = new SplFileObject('../slices/' . $up_name .'_'. $file_slice_count .'.csv', 'w+');
            // if it is the first slice the header is grabbed from the uploaded file so no need to prit it here
            if($file_slice_count !== 1) {
              $destFile->fwrite($header_line);
            }
            $destFile->fwrite($line);
            $line_count++;
          }
          // if max rows per slice is reached, add the file to the zip
          if($line_count >= $slice_count) {
            $zip->addFile('../slices/'. $file_stamp. '/' . $up_name .'_'. $file_slice_count .'.csv');
            $line_count = 0;
            $file_slice_count++;
          }
          if($key === $count_rowes-1) {
            $zip->addFile('../slices/'. $file_stamp. '/' . $up_name .'_'. $file_slice_count .'.csv');
          }
        }
      }
      // close objects and the remove slices
      fclose($destFile);
      $zip->close();
      unlink($file_destination);
      // rrmdir('../slices/' . $file_stamp);

      // TEST if zip exists
      header('Content-disposition: attachment; filename='. $up_name .'.zip');
      header('Content-type: application/zip');
      readfile($zip_name);
    }
  }
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">


    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" type="text/css" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="styles.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body id="page-top">


    <header class="bg-primary" id="csv">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 text-center">
                    <form enctype="multipart/form-data" action="/" method="POST">
                        <div class="form-group">
                            <label for="csvfile">Upload CSV</label>
                            <input accept=".csv" type="file" name="csvfile" class="form-control" id="csvfile" placeholder="*.csv">
                        </div>
                        <button type="submit" class="btn btn-default">Slice</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <section class="bg-primary" id="file">

                    <pre>
                    <?php
                    print_r($_FILES);
                    echo $up_error_mes; ?>
                    </pre>

    </section>

    <!-- scripts -->
    <script type="text/javascript" src="scripts.js"></script>




</body>

</html>
