<?php
class CsvSamurai {
    public $notification = [];
    public $up_file = false;
    public $up_name = false;
    public $up_tmp_name = false;
    public $up_size = false;
    public $up_ext = false;
    public $up_allowed_ext = array('csv'); 
    public $up_max_size   = 128*1024*1024;

    public function __construct() {
        // The file
        $this->up_file        = $_FILES['csvfile'];
        // The file upload properties
        $this->up_name        = $this->up_file['name'];
        $this->up_tmp_name    = $this->up_file['tmp_name'];
        $this->up_size        = $this->up_file['size'];
        $this->notification          = $this->addToNotification($this->up_file['error'], 'danger');
        // check and model the file extention
        $this->up_ext         = explode('.', $this->up_name);
        $this->up_ext         = strtolower(end($this->up_ext));
        // checks
        count($this->notification) > 0 ? $this->addToNotification('An error occurred', 'danger') : false;
        $this->up_size > $this->up_max_size ? $this->addToNotification('The file you are trying to upload is to big', 'danger') : false;
        !in_array($this->up_ext, $this->up_allowed_ext) ? $this->addToNotification('The file you are trying to upload does not have the right extention', 'danger') : false;
        if(count($this->notification) === 0) {
            $this->addToNotification('Got the file details', 'success');
            $this->deal();
        }
    }

    public function deal() {
        // create unique id with random int and time
        $file_stamp = time() . uniqid('_', true);
        // set the name and location for the uploaded
        $file_name_up = $file_stamp . '.' . $this->up_ext;
        $file_upload_destination = '../data-collection/uploads/' . $file_name_up;
        // slice after count
        $rows_per_slice = ($_POST['rows-per-slice'] && is_numeric($_POST['rows-per-slice']))
            ? $_POST['rows-per-slice'] 
            : 1000;
        $slice_count = $rows_per_slice;
        $this->addToNotification('Got the slice details', 'success');
        if(move_uploaded_file($this->up_tmp_name, $file_upload_destination)) {
            // a file was uploaded from the temp location to the uploads location , do something with it
            // keep track in the loop
            $line_count = 0;
            $file_slice_count = 1;
            $header_line;
            
            // set the src file
            $srcFile = new SplFileObject($file_upload_destination);
            // find the number of rows in the uploaded file
            $srcFile->seek(PHP_INT_MAX);
            $count_rows = $srcFile->key() + 1;
            
            // create a zip file
            $zip = new ZipArchive();
            $zip_name = '../data-collection/slices/'. $file_stamp .'.zip';
            if ($zip->open($zip_name, ZipArchive::CREATE) !== TRUE) {
                exit("cannot open <$zip_name>\n");
            }
            // foreach line in the src file, create slices (order matters)
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
                    if (!file_exists('../data-collection/slices/' . $file_stamp)) {
                        mkdir('../data-collection/slices/' . $file_stamp, 0777, true);
                    }
                    $dest_dir = '../data-collection/slices/' . $file_stamp;
                    $destFile = new SplFileObject('../data-collection/slices/'. $file_stamp. '/' . $this->up_name .'_'. $file_slice_count .'.csv', 'w+');
                    // if it is the first slice the header is grabbed from the uploaded file so no need to print it here
                    if($file_slice_count !== 1) {
                        $destFile->fwrite($header_line);
                    }
                    $destFile->fwrite($line);
                    $line_count++;
                }
                // if max rows per slice is reached, add the file to the zip
                if($line_count >= $slice_count) {
                    $zip->addFile('../data-collection/slices/'. $file_stamp. '/' . $this->up_name .'_'. $file_slice_count .'.csv');
                    $line_count = 0;
                    $file_slice_count++;
                }
                //
                if($key === $count_rows-1) {
                    $zip->addFile('../data-collection/slices/'. $file_stamp. '/' . $this->up_name .'_'. $file_slice_count .'.csv');
                }
            }
        }
        // close objects and the remove slices
        fclose($destFile);
        $zip->close();
        $this->addToNotification('Sliced and Zipped', 'success');
        // TEST if zip exists, download zip, remove zip
        // should probably download the file differently
        // maybe ajax or store in session?
        // when the headers are changed I'm loosing this CsvSamurai object
        if (file_exists($zip_name)) {
            // header('Content-type: application/force-download'); 
            // header('Content-Transfer-Encoding: Binary'); 
            // header('Content-length: ' . filesize($zip_name)); 
            // header('Content-disposition: attachment; filename='. $this->up_name .'.zip');
            // readfile($zip_name);
            // unlink($zip_name);
        }
        // Delete the uploaded file
        if (file_exists($file_upload_destination)) {
            unlink($file_upload_destination);
        }
        // Delete the slices
        if (file_exists($dest_dir)) {
            $this->rrmdir($dest_dir);
        }
        $this->addToNotification('Clean Up', 'success');
        $this->addToNotification('Done', 'success');
    }

    /**
     * Add To Notification property
     *
     * @param Array $message
     * @param String $style bootstrap4[primary, secondary, success, danger, warning, info, light, dark]
     * @return void
     */
    public function addToNotification($message, $style = 'primary') {
        $this->notification[] = [$message, $style];
    }

    /**
     * Get Notifications
     *
     * @return void
     */
    public function getNotifications() {
        $output = '';
        foreach($this->notification as $note) {
            $output .= '<div class="csvsamurai-notifications">';
            $output .= '<div class="alert alert-'.$note['1'].' alert-dismissible fade show" role="alert"><span>'.$note['0'].'</span>';
            $output .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
            $output .= '<span aria-hidden="true">&times;</span>';
            $output .= '</button>';
            $output .= '</div>';
            $output .= '</div>';
        }
        return $output;
    }

    /**
     * recursive delete files and directeries untill $src is reached
     */
    public function rrmdir($src) {
        $dir = opendir($src);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                $full = $src . '/' . $file;
                if ( is_dir($full) ) {
                    $this->rrmdir($full);
                }
                else {
                    unlink($full);
                }
            }
        }
        closedir($dir);
        rmdir($src);
    }
} // END CsvSamurai


if(isset($_FILES['csvfile'])) {
    $mkr = new CsvSamurai();
}
?>