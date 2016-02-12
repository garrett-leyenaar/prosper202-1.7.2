<? 

#only allow numeric t202ids
$t202id = $_GET['t202id']; 
if (!is_numeric($t202id)) die();

# check to see if mysql connection works, if not fail over to cached .CSV stored redirect urls
include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config.php'); 


include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect2.php'); 

//grab tracker data

$mysql['tracker_id_public'] = mysql_real_escape_string($t202id);
$tracker_sql = "SELECT 202_trackers.user_id,
						202_trackers.aff_campaign_id,
						text_ad_id,
						ppc_account_id,
						click_cpc,
						click_cloaking,
						aff_campaign_rotate,
						aff_campaign_url,
						aff_campaign_url_2,
						aff_campaign_url_3,
						aff_campaign_url_4,
						aff_campaign_url_5,
						aff_campaign_payout,
						aff_campaign_cloaking
				FROM    202_trackers 
				LEFT JOIN 202_aff_campaigns USING (aff_campaign_id) 
				WHERE   tracker_id_public='".$mysql['tracker_id_public']."'"; 
$tracker_row = memcache_mysql_fetch_assoc($tracker_sql);

 



if (!$tracker_row) { die(); }                                


//now gather variables for the clicks record db
//lets determine if cloaking is on
if (($tracker_row['click_cloaking'] == 1) or //if tracker has overrided cloaking on                                                             
	(($tracker_row['click_cloaking'] == -1) and ($tracker_row['aff_campaign_cloaking'] == 1)) or
	((!isset($tracker_row['click_cloaking'])) and ($tracker_row['aff_campaign_cloaking'] == 1)) //if no tracker but but by default campaign has cloaking on
) {
	$cloaking_on = true;
	$mysql['click_cloaking'] = 1;
	//if cloaking is on, add in a click_id_public, because we will be forwarding them to a cloaked /cl/xxxx link
	$click_id_public = rand(1,9) . $click_id . rand(1,9);
	$mysql['click_id_public'] = mysql_real_escape_string($click_id_public); 
} else { 
	$mysql['click_cloaking'] = 0; 
}
//ok we have our click recorded table, now lets insert theses
$click_sql = "INSERT INTO   202_clicks_record
			  SET           click_id='".$mysql['click_id']."',
							click_id_public='".$mysql['click_id_public']."',
							click_cloaking='".$mysql['click_cloaking']."',
							click_in='".$mysql['click_in']."',
							click_out='".$mysql['click_out']."'"; 
$click_result = mysql_query($click_sql) or record_mysql_error($click_sql);  

if ($cloaking_on == true) {
	$cloaking_site_url = 'http://'.$_SERVER['SERVER_NAME'] . '/tracking202/redirect/cl.php?pci=' . $click_id_public;      
}


//rotate the urls
$redirect_site_url = rotateTrackerUrl($tracker_row);
//$redirect_site_url = $redirect_site_url . $click_id;
$redirect_site_url = replaceTrackerPlaceholders($redirect_site_url,$click_id);



//now we've recorded, now lets redirect them
if ($cloaking_on == true) {
	//if cloaked, redirect them to the cloaked site. 
	header('location: '.$cloaking_site_url); 
	//echo $cloaking_site_url;
} else {
	header('location: '.$redirect_site_url);        
} 

