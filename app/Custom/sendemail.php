<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;

require 'vendor/autoload.php';
require "../sanitize.php";
require "editevent.php";

$action = $_POST["action"];
$tutor = sanitize($_POST["tutor"]["username"]);
$student = sanitize($_POST["student"]["username"]);
$tutoremail = sanitize($_POST["tutor"]["email"]);
$studentemail = sanitize($_POST["student"]["email"]);
$datetime = sanitize($_POST["datetime"]);
$subject = sanitize($_POST["subject"]);
$message = sanitize($_POST["message"]);
$eventid = $_POST["eventid"];

if ($action == "delete") {
    $offtutor = sanitize(getTimezone($tutor));
    $dttutor = date("Y-m-d h:i A", strtotime($datetime . "$offtutor hours"));
    if ($student != "n/a") {
        $offstudent = sanitize(getTimezone($student));
        $dtstudent = date("Y-m-d H:i", strtotime($datetime . "$offstudent hours"));
        $input = array("tutor" => array("subject" => "canceled: session at $dttutor with $student", "body" => "<h1 style='border-radius: .25rem;color: #f8f9fa!important;padding-left: .5rem!important;padding-right: .5rem!important;padding-top: .3125rem; padding-bottom: .3125rem;font-size: 1.25rem;background-color: #6c757d!important;display:inline-block;'> tfa-calendar</h1> <br> <i> *note that all times are in your local timezone.</i> <br> <br> you canceled the session at $dttutor with $student. please remember to avoid doing so after a student claims the slot. <br><br> <b>cancellation reasons:</b> <br> $message", "email" => $tutoremail, "username" => $tutor), "student" => array("subject" => "canceled: session at $dtstudent with $tutor", "body" => "<h1 style='border-radius: .25rem;color: #f8f9fa!important;padding-left: .5rem!important;padding-right: .5rem!important;padding-top: .3125rem; padding-bottom: .3125rem;font-size: 1.25rem;background-color: #6c757d!important;display:inline-block;'> tfa-calendar</h1> <br> <i> *note that all times are in your local timezone.</i> <br> <br> your tutor $tutor canceled the session at $dtstudent with you. <br><br> <b>cancellation reasons:</b> <br> $message", "email" => $studentemail, "username" => $student));
    }
    editEvent('delete', $datetime, [$tutor, $student], false, false, $eventid);
} else if ($action == "unclaim") {
    $offtutor = sanitize(getTimezone($tutor));
    $dttutor = date("Y-m-d h:i A", strtotime($datetime . "$offtutor hours"));
    $offstudent = sanitize(getTimezone($student));
    $dtstudent = date("Y-m-d h:i A", strtotime($datetime . "$offstudent hours"));
    $input = array("tutor" => array("subject" => "canceled: session at $dttutor with $student", "body" => "<h1 style='border-radius: .25rem;color: #f8f9fa!important;padding-left: .5rem!important;padding-right: .5rem!important;padding-top: .3125rem; padding-bottom: .3125rem;font-size: 1.25rem;background-color: #6c757d!important;display:inline-block;'> tfa-calendar</h1> <br> <i> *note that all times are in your local timezone.</i> <br> <br> your student $student canceled the session at $dttutor with you. <br><br> <b>cancellation reasons:</b> <br> $message", "email" => $tutoremail, "username" => $tutor), "student" => array("subject" => "canceled: session at $dtstudent with $tutor", "body" => "<h1 style='border-radius: .25rem;color: #f8f9fa!important;padding-left: .5rem!important;padding-right: .5rem!important;padding-top: .3125rem; padding-bottom: .3125rem;font-size: 1.25rem;background-color: #6c757d!important;display:inline-block;'> tfa-calendar</h1> <br> <i> *note that all times are in your local timezone.</i> <br> <br> you canceled the session at $dtstudent with $tutor. please remember to avoid doing so after you claim the slot. <br><br> <b>cancellation reasons:</b> <br> $message", "email" => $studentemail, "username" => $student));
    editEvent('unclaim', $datetime, [$tutor, $student], false, false, $eventid);
} else if ($action == "claim") {
    $zoom = getZoom($tutor);
    $offtutor = sanitize(getTimezone($tutor));
    $dttutor = date("Y-m-d h:i A", strtotime($datetime . "$offtutor hours"));
    $offstudent = sanitize(getTimezone($student));
    $dtstudent = date("Y-m-d h:i A", strtotime($datetime . "$offstudent hours"));
    $emailstudent = getEmail($student);
    $emailtutor = getEmail($tutor);
    $input = array("tutor" => array("subject" => "claimed: session at $dttutor with $student", "body" => "<h1 style='border-radius: .25rem;color: #f8f9fa!important;padding-left: .5rem!important;padding-right: .5rem!important;padding-top: .3125rem; padding-bottom: .3125rem;font-size: 1.25rem;background-color: #6c757d!important;display:inline-block;'> tfa-calendar</h1> <br> <i> *note that all times are in your local timezone.</i> <br> <br> the student $student claimed the session at $dttutor with you. please remember to avoid canceling. <br> please go to the following zoom link at least 5 minutes before $dttutor. if this is not your preferred meeting link, please change it on your profile on the tfa-calendar website. <br><br> <a style='display: inline-block;text-decoration:none; font-weight: 400; color: #212529; text-align: center; vertical-align: middle; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; background-color: transparent; border: 1px solid transparent; padding: 2px 5px; font-size: 1rem; line-height: 1.5; border-radius: .25rem;color: #fff; background-color: #007bff; border-color: #007bff;' target='_blank' href='$zoom'>zoom link (opens in new page)</a> <br> <br> <b>session's topic needs/help:</b> <br> $message <br> <br> <b>contact the student if you have any problems:</b> <br> $emailstudent", "email" => $tutoremail), "student" => array("subject" => "claimed: session at $dtstudent with $tutor", "body" => "<h1 style='border-radius: .25rem;color: #f8f9fa!important;padding-left: .5rem!important;padding-right: .5rem!important;padding-top: .3125rem; padding-bottom: .3125rem;font-size: 1.25rem;background-color: #6c757d!important;display:inline-block;'> tfa-calendar</h1> <br> <i> *note that all times are in your local timezone.</i> <br> <br> you claimed the session at $dtstudent with the tutor $tutor. please remember to avoid canceling. <br> please go to the following zoom link at least 5 minutes before $dtstudent. <br><br> <a style='display: inline-block;text-decoration:none; font-weight: 400; color: #212529; text-align: center; vertical-align: middle; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; background-color: transparent; border: 1px solid transparent; padding: 2px 5px; font-size: 1rem; line-height: 1.5; border-radius: .25rem;color: #fff; background-color: #007bff; border-color: #007bff;' target='_blank' href='$zoom'>zoom link (opens in new page)</a> <br> <br> <b>session's topic needs/help:</b> <br> $message <br> <br> <b>contact the tutor if you have any problems:</b> <br> $emailtutor", "email" => $studentemail, "username" => $student));
    editEvent('claim', $datetime, [$tutor, $student], $message, $zoom, $eventid);
}

// init phpmailer
$mail = new PHPMailer();
$mail->isSMTP();

// debugging
// SMTP::DEBUG_OFF = off (for production use)
// SMTP::DEBUG_CLIENT = client messages
// SMTP::DEBUG_SERVER = client and server messages
$mail->SMTPDebug = SMTP::DEBUG_OFF;

// smtp auth
$mail->Host = 'smtp.gmail.com';
$mail->Port = 587;
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->SMTPAuth = true;
$mail->AuthType = 'XOAUTH2';
$email = 'scheduler@tutoringforall.org';
$clientId = '813639001936-0nefnbeqr0ech4tiusjioh5e77em228m.apps.googleusercontent.com';
$clientSecret = 'v-w27AgTcIUM7JMNF4Fsz_a1';
$refreshToken = '1//04x6jOyrrdvgOCgYIARAAGAQSNwF-L9IrmWiyjzjTuSbcPN4bjKf2qd5gl5QfeqWwhFQaJeIiU6h-CZRWAWu_1zPceiW6kFeti1I';

// coauth2 provider
$provider = new Google(
    [
        'clientId' => $clientId,
        'clientSecret' => $clientSecret,
    ]
);
$mail->setOAuth(
    new OAuth(
        [
            'provider' => $provider,
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'refreshToken' => $refreshToken,
            'userName' => $email,
        ]
    )
);

// setup + send email
$mail->setFrom($email, 'tfa-calendar');
$mail->isHTML(true);
foreach ($input as $value) {
    $mail->ClearAllRecipients();
    $mail->addAddress(sanitize($value["email"]), sanitize($value["username"]));
    $mail->Subject = sanitize($value["subject"]);
    $mail->Body = $value["body"];
    $mail->AltBody = "If you want to view this message, please use a html supported browser and email client. We apologize for the inconvenience.";
    $mail->send();
}

function getTimezone($username)
{
    require "../config.php";
    $sql = "SELECT timezone FROM users WHERE username=?";
    if ($stmt = $link->prepare($sql)) {
        $stmt->execute([$username]);
        if ($row = $stmt->fetch()) {
            $output = $row[0];
        }
    }
    return $output;
}

function getZoom($username)
{
    require "../config.php";
    $sql = "SELECT zoom FROM users WHERE username=?";
    if ($stmt = $link->prepare($sql)) {
        $stmt->execute([$username]);
        if ($row = $stmt->fetch()) {
            $output = $row[0];
        }
    }
    return $output;
}

function getEmail($input)
{
    require "../config.php";
    require "../sanitize.php";

    $username = sanitize($input);

    // process
    $sql = "SELECT email FROM users WHERE username=?";
    if ($stmt = $link->prepare($sql)) {
        $stmt->execute([$username]);
        if ($row = $stmt->fetch()) {
            $output = $row[0];
        }
    }

    return $output;
}
