<?php

include 'status.inc';
require 'PHPMailerAutoload.php';

$allowedExts = array("jpeg", "jpg", "gif", "png");
$temp = explode(".", $_FILES["file"]["name"]);
$ext = end($temp);
$type = $_FILES["file"]["type"];
$fileInfo = getimagesize($_FILES["file"]["tmp_name"]);
$mimeType = $fileInfo["mime"];
$size = $_FILES["file"]["size"];

// to do upper case ext

if (($mimeType == "image/jpeg") || ($mimeType == "image/jpg") || ($mimeType == "image/gif") || ($mimeType == "image/png") && in_array($ext, $allowedExts))
{
	if ($_FILES["file"]["error"] > 0)
	{
		// TO DO: Web service error.
        sendResponse(400, 'File error');
	}
    else
    {
        if ($size > 2048000)
        {
            // TO DO: File to large.
            sendResponse(400, 'File too large');
        }
        else
        {
            //$filePath = "uploads/" . $_FILES["file"]["name"];

            //if (file_exists($filePath))
            //{
                //unlink($filePath);
            //}

            //move_uploaded_file($_FILES["file"]["tmp_name"], $filePath);
            //echo "Stored in: upload/" . $_FILES["file"]["name"];
            
            $author = strip_tags( $_POST['author'] );
            $age = strip_tags( $_POST['age'] );
            $city = strip_tags( $_POST['city'] );
            
            $author = preg_replace("/\r|\n/", "", $author);
            $age = preg_replace("/\r|\n/", "", $age);
            $city = preg_replace("/\r|\n/", "", $city);
            
            $email = "donotreply@ohbrotherapps.com";
			$subject = "Oh Brother Share Request";
			$message = "Dear Oh, Brother! Comic Apps, you've received a share request from ".$author." (age ".$age.") from ".$city.".";

			//$result = mail("ohbrothercomicapps@gmail.com", "$subject", $message, "From:" . $email);
            //$result = mail("allen@allensherrod.com", "$subject", $message, "From:" . $email);
            
            $result = array(
                "message" => success,
                "file_name" => $_FILES["file"]["name"],
                "file_type" => $type,
                "file_size" => $size,
                "file_temp" => $_FILES["file"]["tmp_name"],
                "author" => $author,
                "age" => $age,
                "city" => $city
            );
            
            $mailer = new PHPMailer();
            //$mailer->isSendmail();
            $mailer->From = $email;
            $mailer->FromName = 'Oh, Brother! Comic Apps';
            $mailer->Subject   = $subject;
            $mailer->Body      = $message;
            $mailer->AddAddress('ohbrothercomicapps@gmail.com');

            $file_to_attach = $_FILES["file"]["tmp_name"];

            $mailer->AddAttachment($file_to_attach , "share_".$author."_".$age."_".$city."_image.".$ext);

			if (!$mailer->send())
				sendResponse(400, 'Mail did not send');
			else
				sendResponse(200, json_encode($result));
        }
    }
}
else
{
	// TO DO: Error
    sendResponse(400, 'Invalid content type');
}

?>
