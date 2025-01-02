<?php
    $currentDirectory = getcwd();
    $uploadDirectory = "/Get-Stuff-Here/";

    $errors = []; // Errors go here

    $fileName = $_FILES['the_file']['name'];
    $fileNameArray = explode('.',$fileName);
    $fileSize = $_FILES['the_file']['size'];
    $fileTmpName  = $_FILES['the_file']['tmp_name'];
    $fileType = $_FILES['the_file']['type'];
    $fileExtension = strtolower(end($fileNameArray));

    $uploadPath = $currentDirectory . $uploadDirectory . basename($fileName); 

    if (isset($_POST['submit'])) {

      if ($fileSize > 1000000000) {
        $errors[] = "File exceeds maximum size (1GB)";
      }

      if (empty($errors)) {
        $didUpload = move_uploaded_file($fileTmpName, $uploadPath);

        if ($didUpload) {
          echo "The file " . basename($fileName) . " has been uploaded";
        } else {
          echo "An error occurred. Think harder.";
        }
      } else {
        foreach ($errors as $error) {
          echo $error . "These are the errors" . "\n";
        }
      }

    }
?>