<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');
AUTH::require_user('toolbar');
AUTH::set_timezone($_SESSION['user_timezone']);
include_once('202-ministats.php');
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<script type='text/javascript' src='http://partner.googleadservices.com/gampad/google_service.js'>
</script>
<script type='text/javascript'>
GS_googleAddAdSenseService("ca-pub-9868787942961354");
GS_googleEnableAllServices();
</script>
<script type='text/javascript'>
GA_googleAddSlot("ca-pub-9868787942961354", "T202Bar_Sponsors_250x60");
</script>
<script type='text/javascript'>
GA_googleFetchAds();
</script>
<title>Mini Account Overview</title>
<meta name="description" content="description" />
<meta name="keywords" content="keywords" />
<meta name="copyright" content="Prosper202, Inc" />
<meta name="author" content="Prosper202, Inc" />
<meta name="MSSmartTagsPreventParsing" content="TRUE" />
<meta name="viewport" content = "width=device-width ,  user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta http-equiv="refresh" content="10">
<meta name="robots" content="noindex, nofollow" />

<link href="/202-css/toolbar.css" rel="stylesheet" type="text/css" />



</head>
<body onload="setTimeout(function() { window.scrollTo(0, 1) }, 1);" id="ministats" class="ministats"

<div class="start box">
<div class="vbox col1 shadow">
<div class="dealFooter"><!-- T202Bar_Sponsors_250x60 --> <img
	src="/202-img/prosper202Mobile.png"></img></div>
<div class="dealHeader">202 Mini Account Overview</div>

<div class="spacer dealContent">
<div>
<table cellpadding="0" cellspacing="1" width="100%">
	<tbody>
		<tr class="alt_row">

			<td>Clicks</td>
			<td><strong><? echo $html['total_clicks']; ?></strong></td>
		</tr>
		<tr>
			<td>Leads</td>
			<td class="m-row1 m-row-bottom"><strong><? echo $html['total_leads']; ?></strong></td>
		</tr>
		<tr class="alt_row">
			<td>S/U</td>
			<td class="alt_row"><strong><? echo $html['total_su_ratio']; ?></strong></td>
		</tr>
		<tr>
			<td>EPC</td>
			<td class="m-row3 m-row-bottom"><strong><? echo $html['total_epc']; ?></strong></td>
		</tr>
		<tr class="alt_row">
			<td>CPC</td>
			<td class="alt_row"><strong><? echo $html['total_avg_cpc']; ?></strong></td>
		</tr>
		<tr>
			<td>Income</td>
			<td class="m-row4 m-row-bottom"><strong><? echo $html['total_income']; ?></strong></td>
		</tr>
		<tr class="alt_row">
			<td>Cost</td>
			<td class="alt_row"><strong>(<? echo $html['total_cost']; ?>)</strong></td>
		</tr>
		<td>Net</td>
		<td
			class="<? if ($total_net > 0) { echo 'm-row_pos'; } elseif ($total_net < 0) { echo 'm-row_neg'; } else { echo 'm-row_zero'; } ?> m-row-bottom"><strong><? echo $html['total_net']; ?></strong></td>
		</tr>
		<tr class="alt_row">
			<td>ROI</td>
			<td
				class="<? if ($total_net > 0) { echo 'm-row_pos'; } elseif ($total_net < 0) { echo 'm-row_neg'; } else { echo 'm-row_zero'; } ?> m-row-bottom"><strong><? echo $html['total_roi']; ?></strong></td>
		</tr>
		</tr>

</table>
<div style="height:67px;padding-top:5px" align="center">
<!-- T202Mini_Sponsors_250x60 -->
<script type='text/javascript'>
GA_googleFillSlot("T202Bar_Sponsors_250x60");
</script></div>
</div>
</div>

</div>
</div>



</body>
</html>
