<?php
/**
 * Created by PhpStorm.
 * User: 39096
 * Date: 2016/9/5
 * Time: 21:51
 */

namespace App\Service;

use App\Factory;

class Mail
{
    /**
     * 发送一封邮件
     * @default enable
     */
    public static function sendMail()
    {
        $mail = new \PHPMailer;

        $mail->SMTPDebug = 3;                               // Enable verbose debug output

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.qq.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = '390961827@qq.com';                 // SMTP username
        $mail->Password = 'krdxmuavhvyvbigf';                 // SMTP password
        $mail->SMTPSecure = '';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 25;                                    // TCP port to connect to

        $mail->setFrom('390961827@qq.com', 'zhan');
        $mail->addAddress('lishengwens@sina.com', 'lsw');     // Add a recipient
        //$mail->addAddress('ellen@example.com');               // Name is optional
        //$mail->addReplyTo('info@example.com', 'Information');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');

        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'Here is the subject';
        $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
        $mail->AltBody = 'This is   the body in plain text for non-HTML mail clients';
        if(!$mail->send()) {
            Factory::logger('zhan')->addInfo(__FUNCTION__, [__LINE__, 'Message could not be sent.']);
            Factory::logger('zhan')->addInfo(__FUNCTION__, [__LINE__, $mail->ErrorInfo]);
        } else {
            Factory::logger('zhan')->addInfo(__FUNCTION__, [__LINE__, 'Message has been sent']);
        }
        return [
            'aa',
        ];
    }
}