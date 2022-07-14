<?php
$mailbox = new PhpImap\Mailbox(
    '{imap.gmail.com:993/imap/ssl}Inbox',
    '',
    '',
    '../../storedMails',
    'US-ASCII'
);

try {
    $mails_ids = $mailbox->searchMailbox('SUBJECT "Order"');
} catch(PhpImap\Exceptions\ConnectionException $ex) {
    echo "IMAP connection failed: " . $ex;
    die();
}

$mailbox->setPathDelimiter('/');

$mailbox->setAttachmentsIgnore(true);

foreach($mails_ids as $mail_id) {
    echo "+------ P A R S I N G ------+\n";

    $email = $mailbox->getMail(
        $mail_id,
        false
    );

    echo "from-name: " . (isset($email->fromName)) ? $email->fromName : $email->fromAddress;
    echo "from-email: " . $email->fromAddress;
    echo "subject: " . $email->subject;
    echo "message_id: " . $email->messageId;

    echo "mail has attachments? ";
    if($email->hasAttachments()) {
        echo "Yes\n";
    } else {
        echo "No\n";
    }

    if(!empty($email->getAttachments())) {
        echo count($email->getAttachments()) . " attachements";
    }

    if(!empty($email->getAttachments())) {
        $emailAttachements = Storage::disk('s3')->get('email-attachements/');
        Storage::disk('s3')->put('email-attachements/', 'attachements');

    }



    if($email->textHtml) {
        echo "Message HTML:\n" . $email->textHtml;
    } else {
        echo "Message Plain:\n" . $email->textPlain;
    }

    if(!empty($email->autoSubmitted)) {
        $mailbox->markMailAsRead($mail_id);
        echo "+------ IGNORING: Auto-Reply ------+";
    }

    if(!empty($email->precedence)) {
        $mailbox->markMailAsRead($mail_id);
        echo "+------ IGNORING: Non-Delivery Report/Receipt ------+";
    }
}


$mailbox->disconnect();



?>