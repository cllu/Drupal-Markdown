<?php

$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = 'status@chunliang.me';
$password = '%E9V7md3';

define('DRUPAL_ROOT', '/var/www/chunliang.me/drupal/');
$_SERVER['REMOTE_ADDR'] = 'localhost';
chdir(DRUPAL_ROOT);
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

function _node_save($node_type, $title, $body, $optional) {
    $node = new stdClass();
    $node->type = $node_type;
    node_object_prepare($node);
    $node->status = 0;
    $node->promote = 0;
    $node->sticky = 0;
    
    $node->title = $title;
    $node->language = LANGUAGE_NONE;
    $node->uid = 1;
    $node->body[$node->language][0]['value'] = $body;
    $node->body[$node->language][0]['summary'] = text_summary($body);
    $node->body[$node->language][0]['format'] = 'markdown';

    // since node_submit will set $node->created by $node->date
    if (isset($optional['created'])) {
        $node->date = $optional['created'];
    }

    if ($node = node_submit($node)) {
        node_save($node);
        return true;
    } else {
        return false;
    }
}

function check_status_mailbox($hostname, $username, $password) {

    $statuses = array()

    $inbox = imap_open($hostname, $username, $password) or die("Cannot connect to Gmail: " . imap_last_error());
    
    $emails = imap_search($inbox, 'ALL');
    
    if ($emails) {
        $output = '';
        rsort($emails);
    
        foreach ($emails as $email_number) {
            $uid = imap_uid($inbox, $email_number);
            $header = imap_fetchheader($inbox, $email_number);
            $overview = imap_fetch_overview($inbox, $email_number, 0);
            $message = imap_fetchbody($inbox, $email_number, 2);
    
            $output .= $email_number;
            $output .= '<li>' . $overview[0]->subject . '</li>';
            $output .= '<li>' . $overview[0]->date . '</li>';
            $output .= '<li>' . $overview[0]->from . '</li>';
    
            print_r($overview);
            print_r($header);
            print_r($uid);
            $title = 'email:' . $uid;
            echo "\n\n";

            $status = array(
                'title' => 'email:' . $uid,
                'body'  => message,
                'created' => $overview->date
            );

            $statuses[] = $status;
        }
    }

    imap_close($inbox);
    return $statuses;
}
