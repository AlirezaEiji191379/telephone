<?php
require 'PHPMailer-5.2.16/PHPMailerAutoload.php';
$mail = new PHPMailer;
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'alirezaeiji191379@gmail.com';
$mail->Password = 'aqvootqbfziukjce';
$mail->SMTPSecure = 'tls';
$mail->From = 'alirezaeiji191379@gmail.com';
$mail->FromName = 'alireza eiji';
$mail->addAddress('alirezaeiji151379@gmail.com', 'Raj Amal W');
$mail->WordWrap = 50;
$mail->isHTML(true);
$mail->Port = 587;
$mail->Subject = 'Using PHPMailer';
$mail->Body    = 'Hi Iam using PHPMailer library to sent SMTP mail from localhost';
if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
    exit;
}
echo 'Message has been sent';


