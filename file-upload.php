<?php

// TODO - Consider changing the filename to be guaranteed unique.
// NOTE - This currently only (properly) supports uploading a single file.
//        If multiple files are uploaded, the name of the last file will be returned.

$uploads_dir_path = $_GET['upload_path'];	// This is where we *save* the file, eg, /home/www/wp-content/uploads/2013/12/pretty-flowers.jpg
$uploads_dir_url  = $_GET['upload_url'];	// This is where we *read* the file, eg, http://example.com/wp-content/uploads/2013/12/pretty-flowers.jpg
$file_name        = '';						// This is what we return to the user.

foreach ($_FILES as $key => $error) {
	$file = $_FILES[ $key ];
	$error = $file['error'];
    if ($error == UPLOAD_ERR_OK) {
        $tmp_name = $file["tmp_name"];
        $name = $file["name"];
        $file_name = "$uploads_dir_url/$name";
        move_uploaded_file($tmp_name, "$uploads_dir_path/$name");
    }
}

header('Content-type: application/json');
echo json_encode( array(
	"file_name" => $file_name
) );
die();
