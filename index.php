<?php
/*
Plugin Name: SSL Status
Description: Check SSL status from yuor dashboard
Version: 0.1
Author: Paul Maloney
Author URI: https://paulmaloney.net
*/

if (is_admin()) {
$myCssFileSrc = plugins_url( '/assets/styles.css', __FILE__ );
wp_enqueue_style( 'my-css', $myCssFileSrc );
} 

function wpdb_register_widgets() {
	global $wp_meta_boxes;
	
	wp_add_dashboard_widget('widget_dashboo', __('SSL Status', 'rc_mdm'), 'wpdb_create_my_dashboo');
}
add_action('wp_dashboard_setup', 'wpdb_register_widgets');

function wpdb_create_my_dashboo() {
	
$url = site_url(); 
$name = wp_title();
$ht = parse_url($url, PHP_URL_SCHEME);
$op = parse_url($url, PHP_URL_HOST);
$urls = "http://" . $op;
    $nows = date('m/d/Y');

if($ht === 'https'){ 
       ini_set("default_socket_timeout","05");
       set_time_limit(5);
       $f=fopen($url,"r");
       $r=fread($f,1000);
       fclose($f);
       if(strlen($r)>1) {?>
        <div class="ssl-status"><img src="<?php echo plugin_dir_url( __FILE__ ).'images/valid.svg';?>" alt="valid" title="Valid" /></div>
       <?php } else { ?>
    <div class="ssl-status"><img src="<?php echo plugin_dir_url( __FILE__ ).'images/invalid.svg';?>" alt="invalid" title="Invalid" /></div>
  <?php }};?>




                     
<?php

if($ht === 'https'){ 
    
       ini_set("default_socket_timeout","05");
       set_time_limit(5);
       $f=fopen($url,"r");
       $r=fread($f,1000);
       fclose($f);
       if(strlen($r)>1) {
    
    $orignal_parse = parse_url($url, PHP_URL_HOST);
    $get = stream_context_create(array("ssl" => array("capture_peer_cert" => TRUE)));
    $read = stream_socket_client("ssl://".$orignal_parse.":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
    $cert = stream_context_get_params($read);
    $certinfo = openssl_x509_parse($cert['options']['ssl']['peer_certificate']);




$valid_from = date('m/d/Y', $certinfo['validFrom_time_t']);
$valid_to = date('m/d/Y', $certinfo['validTo_time_t']);
$now = time();

$date = $valid_to;
$type = $certinfo['signatureTypeSN'];
$types = $certinfo['signatureTypeLN'];
$issue = $certinfo['issuer']['O'];
$issues = $certinfo['issuer']['CN'];


$dda = strtotime($nows);
$ddb = strtotime($date);

$datediff = $ddb - $dda;

$days = floor($datediff / (24*60*60) );



$t = microtime( TRUE );
file_get_contents( $url );
$t = microtime( TRUE ) - $t;



?>
<div class="more-info">
<p><b>Valid From:</b> <?php echo $valid_from;?></p>
<p><b>Valid To:</b> <?php echo $valid_to;?></p>
<p><b>Certificate Status:</b>
<?php
if (strtotime($date) > $now) {
echo "Valid";
} else {
echo "Expired";
}

?></p>
<p class="<?php if($days <=14 ){echo "warn";}?>"><b>Days Left: </b> <?php echo $days;?></p>
<p><b>Encryption Type: </b><?php echo $type;?> | <?php echo $types;?></p>
<p><b>Issuer Name: </b> <?php echo $issue;?></p>
<p><b>Issuer Common Name:</b> </b> <?php echo $issues;?> </p> 
<p><b>Response Time:</b> <?php echo round($t, 5);?></p>
 </div>   
    
   <?php 
} else {

$ts = microtime( TRUE );
file_get_contents( $urls );
$ts = microtime( TRUE ) - $ts;

?>  

<div class="more-info"><p><b>Certificate Status:</b> None Detected</p>
<p><b>Response Time:</b> <?php echo round($ts, 5);?></p>
<a href="https://www.ssl.com/affiliate/program.php?id=523_4_1_100" target="_blank"><img src="images/sslaf.svg" alt="get your ssl"></a>
</div> 

<?php      

 
}
}


}