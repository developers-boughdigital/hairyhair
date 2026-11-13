<?php
require_once('../../../../wp-load.php'); 

echo '1<br>';
$secure_key = 'mysecret123';
if (!isset($_GET['key']) || $_GET['key'] !== $secure_key) {
    http_response_code(403);
    exit('Access Denied');
}  

$table = $wpdb->prefix . 'campaign_cms_campaigns';
$now   = current_time('mysql');
 
$campaigns = $wpdb->get_results("SELECT id, start_date, end_date, status FROM $table WHERE status != 'expired'");

foreach ( $campaigns as $campaign ) {
    $id         = $campaign->id;
    $start_date = $campaign->start_date;
    $end_date   = $campaign->end_date;
    $status     = $campaign->status;
    $new_status = $status;

    if ( $status === 'paused' ) { 
        if ( $now > $end_date ) {
            $new_status = 'expired';
        }
    } else {
        if ( $now >= $start_date && $now <= $end_date ) {
            $new_status = 'live';
        } elseif ( $now > $end_date ) {
            $new_status = 'expired';
        } elseif ( $now < $start_date ) {
            $new_status = 'upcoming';
        }
    }

    if ( $new_status !== $status ) {
        $wpdb->update(
            $table,
            [ 'status' => $new_status ],
            [ 'id' => $id ],
            [ '%s' ],
            [ '%d' ]
        );
    }
}