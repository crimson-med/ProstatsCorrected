<?php 
/*
 _______________________________________________________
|                                                       |
| Name: ProStats 1.9.7                                  |
| Type: MyBB Plugin                                     |
| Author: SaeedGh (SaeehGhMail@Gmail.com)               |
| Author2: AliReza Tofighi (http://my-bb.ir)            |
| Support: http://prostats.wordpress.com/support/       |
| Last edit: May 31, 2016                               |
|_______________________________________________________|

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.

*/


function prostats_g()
{
	global $mybb;

	$mybb->psga['prostats_version'] = '1.9.7';
	$mybb->psga['update_popup_link'] = 'https://docs.google.com/uc?export=view&id=0B1io8D4cQytcemFVVFh3VXJzdWs';
	$mybb->psga['surprise_link'] = 'https://docs.google.com/uc?export=view&id=0B1io8D4cQytcV0dPSTBYTTFNN00';
}

if (!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.");
}

$plugins->add_hook('global_start', 'prostats_run_global');
$plugins->add_hook('pre_output_page', 'prostats_run_pre_output');
$plugins->add_hook('index_start', 'prostats_run_index');
$plugins->add_hook('portal_start', 'prostats_run_portal');
$plugins->add_hook('xmlhttp', 'prostats_run_ajax');
$plugins->add_hook('admin_config_plugins_activate_commit', 'prostats_install_redirect');

function prostats_info()
{
	global $mybb, $db;

	$settings_link = '';

	$query = $db->simple_select('settinggroups', '*', "name='prostats'");

	if ($db->num_rows($query))
	{
		$settings_link = '(<a href="index.php?module=config&action=change&search=prostats" style="color:#FF1493;">Settings</a>)';
	}

	prostats_g();

	//DO NOT EDIT/TRANSLATE THIS SECTION
	return array(
		'name'			=>	'<img border="0" src="../images/prostats/prostats.png" align="absbottom" /> ProStats <span style="color:#000;">/proʊˈstæts/</span>',
		'description'	=>	'Professional stats for MyBB. ' . $settings_link,
		'website'		=>	'http://prostats.wordpress.com',
		'author'		=>	'<a href="mailto:SaeedGhMail@Gmail.com">SaeedGh</a> and <a href="http://my-bb.ir">AliReza Tofighi</a>',
		'authorsite'	=>	'',
		'version'		=>	$mybb->psga['prostats_version'], //*** ALSO IN THE SETTING "ps_version" ***
		'guid'			=>	'124b68d05dcdaf6b7971050baddf340f',
		'compatibility'	=>	'18*'
	);
}

function prostats_is_installed()
{
	global $db;

	$query = $db->simple_select('settinggroups', '*', "name='prostats'");

	if ($db->num_rows($query))
	{
		return true;
	}

	return false;
}

function prostats_install()
{
	global $mybb, $db;

	$extra_cells = "select\n0=--\n1=Most replies\n2=Most reputations\n3=Most thanks\n4=Most viewed\n5=New members\n6=Top downloads\n7=Top posters\n9=Top Thread Posters\n8=Top referrers";

	prostats_uninstall();

	prostats_g();

	$ps_group = array(
		'name'			=> "prostats",
		'title'			=> "ProStats",
		'description'	=> "Professional stats for MyBB.",
		'disporder'		=> 1,
		'isdefault'		=> 1
	);

	$gid = $db->insert_query("settinggroups", $ps_group);
	$mybb->prostats_insert_gid = $gid;

	$ps[]= array(
		'name'			=> "ps_enable",
		'title'			=> "Enable",
		'description'	=> "Do you want to enable the plugin?
		<style type=\"text/css\">
		#row_setting_ps_enable td.first,
		#row_setting_ps_position td.first,
		#row_setting_ps_date_format_ty td.first,
		#row_setting_ps_trow_message_pos td.first,
		#row_setting_ps_latest_posts_pos td.first,
		#row_setting_ps_cell_6 td.first
		{
			border-bottom: 4px solid #0069B1;
			padding-bottom: 40px;
			border-left: 0px;
			border-right: 0px;
			background-repeat: no-repeat;
			background-position: bottom left;
		}
		#row_setting_ps_enable td.first {
			background-image: url(../images/prostats/ps_settings_vp.png);
		}
		#row_setting_ps_position td.first {
			background-image: url(../images/prostats/ps_settings_ga.png);
		}
		#row_setting_ps_date_format_ty td.first {
			background-image: url(../images/prostats/ps_settings_mb.png);
		}
		#row_setting_ps_trow_message_pos td.first {
			background-image: url(../images/prostats/ps_settings_lp.png);
		}
		#row_setting_ps_latest_posts_pos td.first {
			background-image: url(../images/prostats/ps_settings_ec.png);
		}
		#row_setting_ps_cell_6 td.first {
			background-image: url(../images/prostats/ps_settings_mc.png);
		}
		#row_setting_ps_version {
			display: none;
		}
		.ec_div {
			width:98px;
			height:43px;
			overflow:hidden;
			text-direction:rtl;
			margin-top:5px;
		}
		</style>
		
		<link type=\"text/css\" rel=\"stylesheet\" href=\"../floatstats.php?fs_action=css\" />
		
		<div id=\"float_notification\">
		<div id=\"close_float_note\" onclick=\"toggle_float_note();\">×</div>
		<div id=\"help_float_note\" title=\"This helps you find a good balance between appearance and performance before you save the new settings. For more information click!\"><a style=\"color:#FFFFFF;\" target=\"_blank\" href=\"http://prostats.wordpress.com/features/\">?</a></div>
		<div style=\"float:left;margin:-4px 0 0 -5px;color:#FFFFFF;font-weight:bold;\">FloatStats</div>
		<br /><br />
		<table class=\"fs_tbl\" cellspacing=\"0\" cellpadding=\"0\">
		<tr><td>User ID:</td><td><input id=\"fs_uid\" type=\"text\" class=\"textbox50\" /></td></tr>
		<tr><td>Script:</td><td><select id=\"fs_script\" class=\"selectbox130\"><option value=\"index.php\">index.php</option><option value=\"portal.php\">portal.php</option><option value=\"showthread.php\">showthread.php</option></select></td></tr>
		<tr><td>URI:</td><td><select id=\"fs_key\" class=\"selectbox60\"><option value=\"tid\">tid</option><option value=\"pid\">pid</option></select> = <input id=\"fs_value\" type=\"text\" class=\"textbox50\" /></td></tr>
		</table>
		<strong>
		<div id=\"fs_queries_count\">?</div><br />
		No. DB Queries:<br />
		<div id=\"fs_mem_usage\">?</div><br />
		Memory Usage:<br /><br />
		<input style=\"float:left;\" type=\"checkbox\" id=\"fs_auto_refresh_chk\"><label for=\"fs_auto_refresh_chk\" style=\"float:left;margin-top:3px;\">Auto Refresh</label>
		<input type=\"submit\" value=\"Refresh\" onclick=\"fs_refresh();return false;\" style=\"width:80px;float:right;\" /><br /><br />
		</strong>
		</div>
		
		<script type=\"text/javascript\">
		var settings_options = [\"ps_enable\", \"ps_index\", \"ps_portal\", \"ps_global_tag\", \"ps_format_name\", \"ps_highlight\", \"ps_latest_posts\", \"ps_latest_posts_prefix\", \"ps_chkupdates\"];
		var settings_text = [\"ps_subject_length\", \"ps_num_rows\", \"ps_date_format\", \"ps_date_format_ty\", \"ps_trow_message\", \"ps_latest_posts_cells\"];
		var settings_select = [\"ps_trow_message_pos\", \"ps_latest_posts_pos\", \"ps_cell_1\", \"ps_cell_2\", \"ps_cell_3\", \"ps_cell_4\", \"ps_cell_5\", \"ps_cell_6\"];
		var settings_selectforums = [\"ps_ignoreforums\"]; 
		</script>
		
		
		<div id=\"preview_iframe_holder\">
		<iframe id=\"preview_iframe\" name=\"preview_iframe\" src=\"../floatstats.php?fs_action=preview\" style=\"width:100%;border:0;height:inherit;\"></iframe>
		<div id=\"preview_handle\" class=\"unselectable preview_handle\">Instant Preview</div>
		</div>
		
		<script type=\"text/javascript\" src=\"../floatstats.php?fs_action=js\"></script>
		",
		'optionscode'	=> "yesno",
		'value'			=> ps_SetSettingsValue('ps_enable', '1'),
		'disporder'		=> 1,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_index",
		'title'			=> "Show in index",
		'description'	=> "Show the ProStats table in the index page.",
		'optionscode'	=> "yesno",
		'value'			=> ps_SetSettingsValue('ps_index', '1'),
		'disporder'		=> 3,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_portal",
		'title'			=> "Show in portal",
		'description'	=> "Show the ProStats table in the portal page.",
		'optionscode'	=> "yesno",
		'value'			=> ps_SetSettingsValue('ps_portal', '0'),
		'disporder'		=> 4,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_global_tag",
		'title'			=> "Activate global tag",
		'description'	=> "So you can edit themes and insert &lt;ProStats&gt; tag wherever you want to show the stats.",
		'optionscode'	=> "yesno",
		'value'			=> ps_SetSettingsValue('ps_global_tag', '0'),
		'disporder'		=> 5,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_hidefrombots",
		'title'			=> "Hide from search bots",
		'description'	=> "Using this option you can hide stats from all search bots you\'ve defined them in <strong><a href=\"index.php?module=config-spiders\" target=\"_blank\">Spiders/Bots</a></strong> page. This will save bandwidth and decrease server load.",
		'optionscode'	=> "yesno",
		'value'			=> ps_SetSettingsValue('ps_hidefrombots', '1'),
		'disporder'		=> 6,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_ignoreforums",
		'title'			=> "Ignore list",
		'description'	=> "Forums not to be shown on ProStats. To select multiple items hold down the Ctrl key, and click each of the items. To deselect items hold down the Ctrl key and click on them.",
		'optionscode'	=> "forumselect",
		'value'			=> ps_SetSettingsValue('ps_ignoreforums', ''),
		'disporder'		=> 7,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_position",
		'title'			=> "Table position in index and portal",
		'description'	=> "Position of stats in index and portal pages.",
		'optionscode'	=> "select\n0=Top (Header)\n1=Bottom (Footer)",
		'value'			=> ps_SetSettingsValue('ps_position', '1'),
		'disporder'		=> 10,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_format_name",
		'title'			=> "Style usernames",
		'description'	=> "Style the username in true color, font, etc.",
		'optionscode'	=> "yesno",
		'value'			=> ps_SetSettingsValue('ps_format_name', '1'),
		'disporder'		=> 20,
		'gid'			=> $gid
	);

	$ps[]= array(
		'name'			=> "ps_highlight",
		'title'			=> "Highlighting System",
		'description'	=> "Highlight soft-deleted threads, unapproved threads, and threads which are posted in forums moderated by current user.<br />
		Color scheme: <span style=\"background-color:#E8DEFF;\">Deleted</span>, <span style=\"background-color:#FFDDE0;\">Unapproved</span>, <span style=\"background-color:#FFFE92;\">In moderation zone</span>, <span style=\"background-color:#FFDA91;\">Unapproved & In moderation zone</span> ",
		'optionscode'	=> "yesno",
		'value'			=> ps_SetSettingsValue('ps_highlight', '1'),
		'disporder'		=> 25,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_subject_length",
		'title'			=> "Subject length",
		'description'	=> "Maximum length of topic/post subjects. (Input 0 to remove the limitation)",
		'optionscode'	=> "text",
		'value'			=> ps_SetSettingsValue('ps_subject_length', '25'),
		'disporder'		=> 30,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_num_rows",
		'title'			=> "Number of rows",
		'description'	=> "How much items must be shown? Input an <strong style=\"color:red;\">odd</strong> number greater than or equal to 3.",
		'optionscode'	=> "text",
		'value'			=> ps_SetSettingsValue('ps_num_rows', '11'),
		'disporder'		=> 41,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_date_format",
		'title'			=> "Date and Time format",
		'description'	=> "The format of Date and Time which would be used in stats. [<a href=\"http://php.net/manual/en/function.date.php\" target=\"_blank\">More Information</a>]",
		'optionscode'	=> "text",
		'value'			=> ps_SetSettingsValue('ps_date_format', 'm-d, H:i'),
		'disporder'		=> 42,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_date_format_ty",
		'title'			=> "Date and Time variable part",
		'description'	=> "A part of Date and Time format that must be replaced with \"Yesterday\" or \"Today\".",
		'optionscode'	=> "text",
		'value'			=> ps_SetSettingsValue('ps_date_format_ty', 'm-d'),
		'disporder'		=> 43,
		'gid'			=> $gid
	);
		
	$ps[]= array(
		'name'			=> "ps_trow_message",
		'title'			=> "Message block",
		'description'	=> "This is a block on top/bottom of the ProStats table that you can put your HTML contents in it. Leave it empty to hide it.",
		'optionscode'	=> "textarea",
		'value'			=> ps_SetSettingsValue('ps_trow_message', ''),
		'disporder'		=> 45,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_trow_message_pos",
		'title'			=> "Message block position",
		'description'	=> "The position of message block in the ProStats table.",
		'optionscode'	=> "select\n0=Top\n1=Down (Default)",
		'value'			=> ps_SetSettingsValue('ps_trow_message_pos', '1'),
		'disporder'		=> 46,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_latest_posts",
		'title'			=> "Show latest posts",
		'description'	=> "Show latest posts in the ProStats table.",
		'optionscode'	=> "yesno",
		'value'			=> ps_SetSettingsValue('ps_latest_posts', '1'),
		'disporder'		=> 50,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_latest_posts_prefix",
		'title'			=> "Show prefix for latest posts",
		'description'	=> "Show prefixes in subject of latest posts (if there are any).",
		'optionscode'	=> "yesno",
		'value'			=> ps_SetSettingsValue('ps_latest_posts_prefix', '1'),
		'disporder'		=> 52,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_latest_posts_cells",
		'title'			=> "Stats of latest posts",
		'description'	=> "What type of stats you want to be shown for latest posts?<br />Your choices are: <strong>Latest_posts, Date, Starter, Last_sender, Forum</strong><br />Separate them by comma (\",\").",
		'optionscode'	=> "text",
		'value'			=> ps_SetSettingsValue('ps_latest_posts_cells', 'Latest_posts, Date, Starter, Last_sender, Forum'),
		'disporder'		=> 55,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_latest_posts_pos",
		'title'			=> "Latest posts position",
		'description'	=> "The position of the Latest posts block.",
		'optionscode'	=> "select\n0=Left\n1=Right",
		'value'			=> ps_SetSettingsValue('ps_latest_posts_pos', '0'),
		'disporder'		=> 60,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_cell_1",
		'title'			=> "Extra cell 1 (Top-Left)",
		'description'	=> "<div class=\"ec_div\"><img style=\"float:left;\" src=\"../images/prostats/ps_cells.gif\" /><img style=\"float:left;margin-top:-178px;margin-left:-28px;\" src=\"../images/prostats/ps_cells.gif\" /></div>",
		'optionscode'	=> $extra_cells,
		'value'			=> ps_SetSettingsValue('ps_cell_1', '4'),
		'disporder'		=> 62,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_cell_2",
		'title'			=> "Extra cell 2 (Bottom-Left)",
		'description'	=> "<div class=\"ec_div\"><img style=\"float:left;\" src=\"../images/prostats/ps_cells.gif\" /><img style=\"float:left;margin-top:-159px;margin-left:-28px;\" src=\"../images/prostats/ps_cells.gif\" /></div>",
		'optionscode'	=> $extra_cells,
		'value'			=> ps_SetSettingsValue('ps_cell_2', '2'),
		'disporder'		=> 64,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_cell_3",
		'title'			=> "Extra cell 3 (Top-Middle)",
		'description'	=> "<div class=\"ec_div\"><img style=\"float:left;\" src=\"../images/prostats/ps_cells.gif\" /><img style=\"float:left;margin-top:-178px;margin-left:-14px;\" src=\"../images/prostats/ps_cells.gif\" /></div>",
		'optionscode'	=> $extra_cells,
		'value'			=> ps_SetSettingsValue('ps_cell_3', '1'),
		'disporder'		=> 66,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_cell_4",
		'title'			=> "Extra cell 4 (Bottom-Middle)",
		'description'	=> "<div class=\"ec_div\"><img style=\"float:left;\" src=\"../images/prostats/ps_cells.gif\" /><img style=\"float:left;margin-top:-159px;margin-left:-14px;\" src=\"../images/prostats/ps_cells.gif\" /></div>",
		'optionscode'	=> $extra_cells,
		'value'			=> ps_SetSettingsValue('ps_cell_4', '7'),
		'disporder'		=> 68,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_cell_5",
		'title'			=> "Extra cell 5 (Top-Right)",
		'description'	=> "<div class=\"ec_div\"><img style=\"float:left;\" src=\"../images/prostats/ps_cells.gif\" /><img style=\"float:left;margin-top:-178px;margin-left:0px;\" src=\"../images/prostats/ps_cells.gif\" /></div>",
		'optionscode'	=> $extra_cells,
		'value'			=> ps_SetSettingsValue('ps_cell_5', '3'),
		'disporder'		=> 70,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_cell_6",
		'title'			=> "Extra cell 6 (Bottom-Right)",
		'description'	=> "<div class=\"ec_div\"><img style=\"float:left;\" src=\"../images/prostats/ps_cells.gif\" /><img style=\"float:left;margin-top:-159px;margin-left:0px;\" src=\"../images/prostats/ps_cells.gif\" /></div>",
		'optionscode'	=> $extra_cells,
		'value'			=> ps_SetSettingsValue('ps_cell_6', '5'),
		'disporder'		=> 72,
		'gid'			=> $gid
	);

	$ps[]= array(
		'name'			=> "ps_xml_feed",
		'title'			=> "Activate XML feed",
		'description'	=> "Output the stats in XML format to show on other websites. [<a href=\"http://community.mybb.com/thread-48686.html\" target=\"_blank\">More Information</a>]",
		'optionscode'	=> "yesno",
		'value'			=> ps_SetSettingsValue('ps_xml_feed', '0'),
		'disporder'		=> 78,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_chkupdates",
		'title'			=> "Check for updates",
		'description'	=> "Turn this setting On and you\'ll be notified whenever a new version released. Notification will be shown above the ProStats\' table and would be visible only to Admins.",
		'optionscode'	=> "yesno",
		'value'			=> ps_SetSettingsValue('ps_chkupdates', '1'),
		'disporder'		=> 80,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_surprise",
		'title'			=> "Surprise!",
		'description'	=> "This option may add a little fun to your forum sometimes! It would probably happen once or twice a year and only Admins can see the result.",
		'optionscode'	=> "yesno",
		'value'			=> ps_SetSettingsValue('ps_surprise', '1'),
		'disporder'		=> 85,
		'gid'			=> $gid
	);
	
	$ps[]= array(
		'name'			=> "ps_version",
		'title'			=> "ProStats Version",
		'description'	=> "DO NOT MODIFY THIS SETTING",
		'optionscode'	=> "text",
		'value'			=> $mybb->psga['prostats_version'],
		'disporder'		=> 90,
		'gid'			=> $gid
	);
	
	foreach ($ps as $p)
	{
		$db->insert_query("settings", $p);
	}
	
	rebuild_settings();
}


function ps_SetSettingsValue($setting_name, $default_value)
{
	global $mybb;
	
	return $mybb->settings[$setting_name] ? $mybb->settings[$setting_name] : $default_value;
}


function prostats_activate()
{
	global $db;
	
	prostats_deactivate();
	
	require_once MYBB_ROOT.'inc/adminfunctions_templates.php';
	find_replace_templatesets('index', '#{\$header}(\r?)\n#', "{\$header}\n{\$ps_header_index}\n");
	find_replace_templatesets('index', '#{\$forums}(\r?)\n#', "{\$forums}\n{\$ps_footer_index}\n");
	find_replace_templatesets('portal', '#{\$header}(\r?)\n#', "{\$header}\n{\$ps_header_portal}\n");
	find_replace_templatesets('portal', '#{\$footer}(\r?)\n#', "{\$ps_footer_portal}\n{\$footer}\n");
	
	$extra_cells = "select\n0=--\n1=Most replies\n2=Most reputations\n3=Most thanks\n4=Most viewed\n5=New members\n6=Top downloads\n7=Top posters\n9=Top Thread Posters\n8=Top referrers";

	$templatearray = array(
		'title' => "prostats",
		'template' => $db->escape_string(<<<EOT
<script type="text/javascript">
<!--

function prostats_reload()
{
	\$("#prostats_table").prepend('<div id="ps_reload_layer" class="tborder"><img src="{\$mybb->settings['bburl']}/images/spinner_big.gif" alt="spinner" /></div>');
	
	\$('#ps_reload_layer').css({
		"height": \$('#prostats_table').find('table:first').height()+'px',
		"width": \$('#prostats_table').find('table:first').width()+'px',
		"top": \$('#prostats_table').offset().top,
	}).fadeTo(0, 0.1).fadeTo("slow" , 0.8);
	
    \$.ajax(
    {
        type: 'POST',
        url: '{\$mybb->settings['bburl']}/xmlhttp.php?action=prostats_reload',
        dataType: 'json',
        data: { ajax: 1, my_post_key: my_post_key },
        success: prostats_done
    });
	return false;
}

function prostats_done(data)
{
	if(data.hasOwnProperty("errors"))
	{
		\$(".jGrowl").jGrowl("close");

		\$.each(data.errors, function(i, message)
		{
			\$.jGrowl(message);
		});
	}
	else if(data.html)
	{
		\$("#prostats_table").html(data.html);
	}
}
-->
</script>
<style type="text/css">
#ps_reload_layer {
	display: block;
	position: absolute;
	z-order:1000;
	background: #fff;
	text-align: center;
	vertical-align: middle;
}

#ps_reload_layer img {
	position: relative;
	top: 30%;
}

.ps_unapproved {
	background-color:#FFDDE0;
}

.ps_deleted {
	background-color:#E8DEFF;
}

.ps_undermod {
	background-color:#FFFE92;
}

.ps_both {
	background-color:#FFDA91;
}

.ps_status {
	display: inline-block;
	width: 16px;
	height: 16px;
	background-image: url({\$mybb->settings['bburl']}/images/prostats/status_sprite.png);
	vertical-align: bottom;
}

.ps_status.ps_minion {
	background-position: 0 0;
}

.ps_status.ps_minioff {
	background-position: 0 -16px;
}

.ps_status.ps_miniofflock {
	background-position: 0 -32px;
}

/* Fix bad border-radius */
#prostats_table .tcat td,
#prostats_table .trow1 td,
#prostats_table .trow2 td {
	border-radius: 0;
}

#prostats_table .thead {
	border-bottom-left-radius: 0;
	border-bottom-right-radius: 0;
}
</style>

		<div id="prostats_table">
		{\$remote_msg}
		<table width="100%" border="0" cellspacing="{\$theme[borderwidth]}" cellpadding="0" class="tborder">
		<thead>
		<tr><td colspan="{\$num_columns}">
			<table border="0" cellspacing="0" cellpadding="{\$theme[tablespace]}" width="100%">
			<tr>
			<td class="thead">
				<div class="float_{\$ps_ralign}"><a href="" onclick="return prostats_reload();">{\$lang->prostats_reload} <img src="{\$mybb->settings['bburl']}/images/prostats/ps_reload.png" style="vertical-align:middle;" alt="" /></a></div>
				<strong>{\$lang->prostats_prostats}</strong>
			</td>
			</tr>
			</table>
		</td>
		</tr>
		</thead>
		<tbody>
		{\$trow_message_top}
		<tr valign="top">
		{\$prostats_content}
		</tr>
		{\$trow_message_down}
		</tbody>
		</table>
		<br />
		</div>
EOT
),
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
	
	$templatearray = array(
		'title' => "prostats_readstate_icon",
		'template' => "<span class=\"ps_status ps_mini{\$lightbulb[\'folder\']}\"> </span>&nbsp;",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
	
	$templatearray = array(
		'title' => "prostats_newestposts",
		'template' => "<td class=\"{\$trow}\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"{\$theme[tablespace]}\">
		<tr class=\"tcat smalltext\">
		<td colspan=\"{\$colspan}\">{\$lang->prostats_latest_posts}</td>
		</tr>
		<tr>
		<td colspan=\"{\$colspan}\">
<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
		<tr class=\"{\$trow} smalltext\">
		{\$newestposts_cols_name}
		</tr>
		{\$newestposts_row}
</table></td>
		</tr>
		</table></td>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
	
	$templatearray = array(
		'title' => "prostats_newestposts_row",
		'template' => "<tr class=\"{\$trow} smalltext{\$highlight}\">
		{\$newestposts_cols}
		</tr>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
	
	$templatearray = array(
		'title' => "prostats_newestposts_specialchar",
		'template' => "<a href=\"{\$threadlink}\" style=\"text-decoration: none;\"><font face=\"arial\" style=\"line-height:10px;\">▼</font></a>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
	
	//Most Replies
	$templatearray = array(
		'title' => "prostats_mostreplies",
		'template' => "<td><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"{\$theme[tablespace]}\">
		<tr class=\"tcat smalltext\">
		<td colspan=\"2\">{\$lang->prostats_most_replies}</td>
		</tr>
		<tr>
<td colspan=\"2\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
		{\$mostreplies_row}
</table></td></tr>
		</table></td>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
		
	$templatearray = array(
		'title' => "prostats_mostreplies_row",
		'template' => "<tr class=\"smalltext{\$highlight}\">
		<td>{\$readstate_icon}<a href=\"{\$threadlink}\" title=\"{\$subject_long}\">{\$subject}</a></td>
		<td align=\"{\$ps_align}\"><a href=\"javascript:MyBB.whoPosted({\$tid});\">{\$replies}</a></td>
		</tr>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
	
	//Most Reputation
	$templatearray = array(
		'title' => "prostats_mostreputation",
		'template' => "<td><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"{\$theme[tablespace]}\">
		<tr class=\"tcat smalltext\">
		<td colspan=\"2\">{\$lang->prostats_most_reputations}</td>
		</tr>
		<tr>
<td colspan=\"2\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
		{\$mostreputation_row}
</table></td></tr>
		</table></td>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
		
	$templatearray = array(
		'title' => "prostats_mostreputation_row",
		'template' => "<tr class=\"smalltext\">
		<td><a href=\"{\$profilelink}\">{\$username}</a></td>
		<td align=\"{\$ps_align}\"><a href=\"reputation.php?uid={\$uid}\">{\$repscount}</a></td>
		</tr>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
	
	//Most Thanks
	$templatearray = array(
		'title' => "prostats_mostthanks",
		'template' => "<td><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"{\$theme[tablespace]}\">
		<tr class=\"tcat smalltext\">
		<td colspan=\"2\">{\$lang->prostats_most_thanks}</td>
		</tr>
		<tr>
<td colspan=\"2\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
		{\$mostthanks_row}
</table></td></tr>
		</table></td>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
		
	$templatearray = array(
		'title' => "prostats_mostthanks_row",
		'template' => "<tr class=\"smalltext\">
		<td><a href=\"{\$profilelink}\">{\$username}</a></td>
		<td align=\"{\$ps_align}\">{\$thxnum}</td>
		</tr>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
	
	//Most Views
	$templatearray = array(
		'title' => "prostats_mostviews",
		'template' => "<td><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"{\$theme[tablespace]}\">
		<tr class=\"tcat smalltext\">
		<td colspan=\"2\">{\$lang->prostats_most_views}</td>
		</tr>
		<tr>
<td colspan=\"2\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
		{\$mostviews_row}
</table></td></tr>
		</table></td>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
		
	$templatearray = array(
		'title' => "prostats_mostviews_row",
		'template' => "<tr class=\"smalltext{\$highlight}\">
		<td>{\$readstate_icon}<a href=\"{\$threadlink}\" title=\"{\$subject_long}\">{\$subject}</a></td>
		<td align=\"{\$ps_align}\">{\$views}</td>
		</tr>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
	
	//Newest Members
	$templatearray = array(
		'title' => "prostats_newmembers",
		'template' => "<td><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"{\$theme[tablespace]}\">
		<tr class=\"tcat smalltext\">
		<td colspan=\"2\">{\$lang->prostats_newest_members}</td>
		</tr>
		<tr>
<td colspan=\"2\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
		{\$newmembers_row}
</table></td></tr>
		</table></td>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
		
	$templatearray = array(
		'title' => "prostats_newmembers_row",
		'template' => "<tr class=\"smalltext\">
		<td><a href=\"{\$profilelink}\">{\$username}</a></td>
		<td align=\"{\$ps_align}\">{\$regdate}</td>
		</tr>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
	
	//Top Downloads
	$templatearray = array(
		'title' => "prostats_topdownloads",
		'template' => "<td><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"{\$theme[tablespace]}\">
		<tr class=\"tcat smalltext\">
		<td colspan=\"2\">{\$lang->prostats_top_downloads}</td>
		</tr>
		<tr>
<td colspan=\"2\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
		{\$topdownloads_row}
</table></td></tr>
		</table></td>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
		
	$templatearray = array(
		'title' => "prostats_topdownloads_row",
		'template' => "<tr class=\"smalltext{\$highlight}\">
		<td><img src=\"{\$attach_icon}\" width=\"11\" height=\"11\" align=\"absmiddle\" alt=\"\" />&nbsp;<a href=\"{\$postlink}\" title=\"{\$subject_long}\">{\$subject}</a></td>
		<td align=\"{\$ps_align}\">{\$downloadnum}</td>
		</tr>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
	
	//Top Posters
	$templatearray = array(
		'title' => "prostats_topposters",
		'template' => "<td><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"{\$theme[tablespace]}\">
		<tr class=\"tcat smalltext\">
		<td colspan=\"2\">{\$lang->prostats_top_posters}</td>
		</tr>
		<tr>
<td colspan=\"2\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
		{\$topposters_row}
</table></td></tr>
		</table></td>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
		
	$templatearray = array(
		'title' => "prostats_topposters_row",
		'template' => "<tr class=\"smalltext\">
		<td><a href=\"{\$profilelink}\">{\$username}</a></td>
		<td align=\"{\$ps_align}\"><a href=\"search.php?action=finduser&amp;uid={\$uid}\">{\$postnum}</a></td>
		</tr>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
	
	//Top Thread Posters
	$templatearray = array(
		'title' => "prostats_topthreadposters",
		'template' => "<td><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"{\$theme[tablespace]}\">
		<tr class=\"tcat smalltext\">
		<td colspan=\"2\">{\$lang->prostats_top_thread_posters}</td>
		</tr>
		<tr>
<td colspan=\"2\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
		{\$topthreadposters_row}
</table></td></tr>
		</table></td>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
		
	$templatearray = array(
		'title' => "prostats_topthreadposters_row",
		'template' => "<tr class=\"smalltext\">
		<td><a href=\"{\$profilelink}\">{\$username}</a></td>
		<td align=\"{\$ps_align}\"><a href=\"search.php?action=finduserthreads&amp;uid={\$uid}\">{\$postnum}</a></td>
		</tr>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
	
	//Top Referrers
	$templatearray = array(
		'title' => "prostats_topreferrers",
		'template' => "<td><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"{\$theme[tablespace]}\">
		<tr class=\"tcat smalltext\">
		<td colspan=\"2\">{\$lang->prostats_top_topreferrers}</td>
		</tr>
		<tr>
<td colspan=\"2\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
		{\$topreferrers_row}
</table></td></tr>
		</table></td>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
		
	$templatearray = array(
		'title' => "prostats_topreferrers_row",
		'template' => "<tr class=\"smalltext\">
		<td><a href=\"{\$profilelink}\">{\$username}</a></td>
		<td align=\"{\$ps_align}\">{\$refnum}</td>
		</tr>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
	
	$templatearray = array(
		'title' => "prostats_message",
		'template' => "<tr class=\"trow1\">
		<td colspan=\"{\$num_columns}\">
		<table  border=\"0\" cellspacing=\"0\" cellpadding=\"{\$theme[tablespace]}\" width=\"100%\">
		<tr class=\"smalltext\">
		<td>
		{\$prostats_message}
		</td>
		</tr>
		</table>
		</td>
		</tr>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
	
	$templatearray = array(
		'title' => "prostats_onerowextra",
		'template' => "<td class=\"{\$trow}\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr>{\$single_extra_content}</tr></table></td>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);
	
	$templatearray = array(
		'title' => "prostats_tworowextra",
		'template' => "<td class=\"{\$trow}\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr>{\$extra_content_one}</tr><tr>{\$extra_content_two}</tr></table></td>",
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);

	$templatearray = array(
		'title' => "prostats_newestposts_head_latest_posts",
		'template' => $db->escape_string('<td>{$lang->prostats_topic}</td>'),
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);

	$templatearray = array(
		'title' => "prostats_newestposts_head_date",
		'template' => $db->escape_string('<td>{$lang->prostats_datetime}&nbsp;</td>'),
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);

	$templatearray = array(
		'title' => "prostats_newestposts_head_starter",
		'template' => $db->escape_string('<td>{$lang->prostats_author}</td>'),
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);

	$templatearray = array(
		'title' => "prostats_newestposts_head_last_sender",
		'template' => $db->escape_string('<td>{$lang->prostats_last_sender}</td>'),
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);

	$templatearray = array(
		'title' => "prostats_newestposts_head_forum",
		'template' => $db->escape_string('<td>{$lang->prostats_forum}</td>'),
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);



	$templatearray = array(
		'title' => "prostats_newestposts_latest_posts",
		'template' => $db->escape_string('<td>{$readstate_icon}<a href="{$threadlink}" title="{$subject_long}">{$subject}</a></td>'),
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);

	$templatearray = array(
		'title' => "prostats_newestposts_date",
		'template' => $db->escape_string('<td>{$newestposts_specialchar}{$datetime}</td>'),
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);

	$templatearray = array(
		'title' => "prostats_newestposts_starter",
		'template' => $db->escape_string('<td><a href="{$profilelink}">{$username}</a></td>'),
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);

	$templatearray = array(
		'title' => "prostats_newestposts_last_sender",
		'template' => $db->escape_string('<td><a href="{$lastposter_profile}">{$lastposter_uname}</a></td>'),
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);

	$templatearray = array(
		'title' => "prostats_newestposts_forum",
		'template' => $db->escape_string('<td><a href="{$forumlink}" title="{$forumname_long}">{$forumname}</a></td>'),
		'sid' => "-1"
		);
	$db->insert_query("templates", $templatearray);


}


function prostats_uninstall()
{
	global $mybb, $db;
	
	$db->delete_query("settings", "name IN ('ps_enable','ps_ignoreforums','ps_index','ps_portal','ps_position','ps_format_name','ps_highlight','ps_subject_length','ps_num_rows','ps_date_format','ps_date_format_ty','ps_trow_message','ps_trow_message_pos','ps_latest_posts','ps_latest_posts_prefix','ps_latest_posts_cells','ps_latest_posts_pos','ps_cell_1','ps_cell_2','ps_cell_3','ps_cell_4','ps_cell_5','ps_cell_6','ps_hidefrombots','ps_global_tag','ps_xml_feed','ps_chkupdates','ps_surprise','ps_version')");
	$db->delete_query("settinggroups", "name='prostats'");
	
	rebuild_settings();
}

function prostats_install_redirect()
{
	global $installed, $mybb;
	
	if($installed == false && $mybb->input['plugin'] == 'prostats')
	{
		global $message;
	
		flash_message($message, 'success');
		admin_redirect("index.php?module=config-settings&action=change&gid=".$mybb->prostats_insert_gid);
	}
}

function prostats_deactivate()
{
	global $db;
	
	require_once MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("index", '#{\$ps_header_index}(\r?)\n#', "", 0);
	find_replace_templatesets("index", '#{\$ps_footer_index}(\r?)\n#', "", 0);
	find_replace_templatesets("portal", '#{\$ps_header_portal}(\r?)\n#', "", 0);
	find_replace_templatesets("portal", '#{\$ps_footer_portal}(\r?)\n#', "", 0);
	
	$db->query("DELETE FROM ".TABLE_PREFIX."templates WHERE title='prostats'");
	$db->query("DELETE FROM ".TABLE_PREFIX."templates WHERE title LIKE 'prostats_%'");
}


function prostats_run_global()
{
	global $mybb, $session;
	
	if (isset($GLOBALS['templatelist']))
	{
		if ($mybb->settings['ps_enable'] && defined('THIS_SCRIPT'))
		{
			if (!$mybb->settings['ps_hidefrombots'] || empty($session->is_spider))
			{
				if (($mybb->settings['ps_index'] && THIS_SCRIPT == 'index.php')
					|| ($mybb->settings['ps_portal'] && THIS_SCRIPT == 'portal.php')
					|| $mybb->settings['ps_global_tag'])
				{
					$GLOBALS['templatelist'] .= ",prostats,prostats_readstate_icon,prostats_newmembers,prostats_newmembers_row,prostats_topposters,prostats_topposters_row,prostats_topthreadposters,prostats_topthreadposters_row,prostats_topreferrers,prostats_topreferrers_row,prostats_mostthanks,prostats_mostthanks_row,prostats_newestposts,prostats_newestposts_row,prostats_newestposts_specialchar,prostats_mostreplies,prostats_mostreplies_row,prostats_mostviews,prostats_mostviews_row,prostats_topdownloads,prostats_topdownloads_row,prostats_mostreputation,prostats_mostreputation_row,prostats_message,prostats_onerowextra,prostats_tworowextra";
				}
			}
		}
	}
}


function prostats_run_index($force = false)
{
	global $mybb, $parser, $session, $unviewables, $prostats_tbl, $ps_header_index, $ps_footer_index, $ps_header_portal, $ps_footer_portal;

	if (!$mybb->settings['ps_enable']) {return false;}

	if ($mybb->settings['ps_hidefrombots'] && !empty($session->is_spider)) {return false;}
	
	if (!is_object($parser))
	{
		require_once MYBB_ROOT.'inc/class_parser.php';
		$parser = new postParser;
	}
	
	if (ceil($mybb->settings['ps_num_rows']) != $mybb->settings['ps_num_rows'] || ceil($mybb->settings['ps_subject_length']) != $mybb->settings['ps_subject_length']){return false;}
	if (intval($mybb->settings['ps_num_rows']) < 3) {return false;}
	
	if (strtolower($mybb->input['stats'])=='xml' && $mybb->settings['ps_xml_feed'])
	{
		prostats_run_feed();
		exit;
	}
	
	if (!$mybb->settings['ps_index'] && !$force) {return false;}
	
	$numofrows = $mybb->settings['ps_num_rows'];
	$prostats_tbl = "";
	
	$prostats_tbl = ps_MakeTable();

	if ($mybb->settings['ps_position'] == 0)
	{
		$ps_header_index = $prostats_tbl;
	}
	else if ($mybb->settings['ps_position'] == 1)
	{
		$ps_footer_index = $prostats_tbl;
	}
}


function prostats_run_portal()
{
	global $mybb, $parser, $session, $ps_header_index, $ps_footer_index, $ps_header_portal, $ps_footer_portal;
	
	if (!$mybb->settings['ps_enable']) {return false;}

	if ($mybb->settings['ps_hidefrombots'] && !empty($session->is_spider)) {return false;}
	
	if (!is_object($parser))
	{
		require_once MYBB_ROOT.'inc/class_parser.php';
		$parser = new postParser;
	}
	
	if (ceil($mybb->settings['ps_num_rows']) != $mybb->settings['ps_num_rows'] || ceil($mybb->settings['ps_subject_length']) != $mybb->settings['ps_subject_length']){return false;}
	
	if (!$mybb->settings['ps_portal']) {return false;}
	if (intval($mybb->settings['ps_num_rows']) < 3) {return false;}
	
	$numofrows = $mybb->settings['ps_num_rows'];
	$prostats_tbl = "";
	
	$prostats_tbl = ps_MakeTable();

	if ($mybb->settings['ps_position'] == 0)
	{
		$ps_header_portal = $prostats_tbl;
	}
	else if ($mybb->settings['ps_position'] == 1)
	{
		$ps_footer_portal = $prostats_tbl;
	}
}


function prostats_run_pre_output(&$contents)
{
	global $mybb, $parser, $session, $prostats_tbl, $ps_header_index, $ps_footer_index, $ps_header_portal, $ps_footer_portal;

	if (!$mybb->settings['ps_enable']) {return false;}
	
	if ($mybb->settings['ps_hidefrombots'] && !empty($session->is_spider)) {return false;}
	
	if (!is_object($parser))
	{
		require_once MYBB_ROOT.'inc/class_parser.php';
		$parser = new postParser;
	}
	
	if (ceil($mybb->settings['ps_num_rows']) != $mybb->settings['ps_num_rows'] || ceil($mybb->settings['ps_subject_length']) != $mybb->settings['ps_subject_length']){return false;}
	if (intval($mybb->settings['ps_num_rows']) < 3) {return false;}
	
	if (!$mybb->settings['ps_global_tag']){
		$contents = str_replace('<ProStats>', '', $contents);
		return false;
	}
	
	$numofrows = $mybb->settings['ps_num_rows'];
	$prostats_tbl = "";
	
	$prostats_tbl = ps_MakeTable();

	$contents = str_replace('<ProStats>', $prostats_tbl, $contents);
}


function ps_GetNewestPosts($NumOfRows, $feed=false)
{
	global $mybb, $db, $templates, $theme, $lang, $unviewables, $under_mod_forums_arr, $vcheck, $parser, $lightbulb, $trow, $newestposts_cols_name, $newestposts_cols, $colspan, $feeditem;

	if (!is_object($parser))
	{
		require_once MYBB_ROOT.'inc/class_parser.php';
		$parser = new postParser;
	}
	
	$query = $db->query ("
		SELECT t.subject,t.username,t.uid,t.tid,t.fid,t.lastpost,t.lastposter,t.lastposteruid,t.replies,t.visible,tr.uid AS truid,tr.dateline,tp.displaystyle AS styledprefix,f.name 
		FROM ".TABLE_PREFIX."threads t 
		LEFT JOIN ".TABLE_PREFIX."threadsread tr ON (tr.tid=t.tid AND tr.uid='".$mybb->user['uid']."') 
		LEFT JOIN ".TABLE_PREFIX."threadprefixes tp ON (tp.pid = t.prefix) 
		LEFT JOIN ".TABLE_PREFIX."forums f ON (f.fid = t.fid) 
		WHERE (t.visible = '1' ".$vcheck.") 
		".$unviewables['string']." 
		AND t.closed NOT LIKE 'moved|%' 
		AND t.visible != '-2' 
		AND f.active = '1' 
		ORDER BY t.lastpost DESC 
		LIMIT 0,".$NumOfRows);
		
		$newestposts_cols_name = "";
		$newestposts_cols = "";
		$colspan = 0;
		$active_cells = "";

		$latest_posts_cells_arr = escaped_explode(",", htmlspecialchars_uni($mybb->settings['ps_latest_posts_cells']),20);
		
		foreach($latest_posts_cells_arr as $latest_posts_cell)
		{
			++$colspan;
			
			switch($latest_posts_cell)
			{
				case "Latest_posts" : 
					$active_cells['Latest_posts']=1;
					eval("\$newestposts_cols_name .= \"".$templates->get("prostats_newestposts_head_latest_posts")."\";");
					$cell_order[$colspan]='Latest_posts';
					break;
				case "Date" :
					$active_cells['Date']=1;
					eval("\$newestposts_cols_name .= \"".$templates->get("prostats_newestposts_head_date")."\";");
					$cell_order[$colspan]='Date';
					break;
				case "Starter" :
					$active_cells['Starter']=1;
					eval("\$newestposts_cols_name .= \"".$templates->get("prostats_newestposts_head_starter")."\";");
					$cell_order[$colspan]='Starter';
					break;
				case "Last_sender" :
					$active_cells['Last_sender']=1;
					eval("\$newestposts_cols_name .= \"".$templates->get("prostats_newestposts_head_last_sender")."\";");
					$cell_order[$colspan]='Last_sender';
					break;
				case "Forum" :
					$active_cells['Forum']=1;
					eval("\$newestposts_cols_name .= \"".$templates->get("prostats_newestposts_head_forum")."\";");
					$cell_order[$colspan]='Forum';
					break;
				default: --$colspan;
			}
		}

	$trow = "trow1";
	
	$loop_counter = 0;
	
	while ($newest_threads = $db->fetch_array($query))
	{
		$tid = $newest_threads['tid'];
		$fuid = $newest_threads['uid'];
		$fid = $newest_threads['fid'];
		$lightbulb['folder'] = "off";
		$newestposts_cols = "";
		$plainprefix = "";
		$styledprefix = "";
		
		$highlight = ps_GetHighlight($newest_threads);
		
		if ($newest_threads['styledprefix'] && $mybb->settings['ps_latest_posts_prefix'])
		{
			$plainprefix = strip_tags($newest_threads['styledprefix']) . ' ';
			$styledprefix = $newest_threads['styledprefix'];
		}
		
		if ($mybb->user['uid'])
		{
			if ($newest_threads['dateline'] && $newest_threads['truid'] == $mybb->user['uid'])
			{
				if ($newest_threads['lastpost'] > $newest_threads['dateline'])
				{
					$lightbulb['folder'] = "on";
				}
			}
			else
			{
				if ($newest_threads['lastpost'] > $mybb->user['lastvisit'])
				{
					$lightbulb['folder'] = "on";
				}
			}
		}
		
		$dateformat = $mybb->settings['ps_date_format'];
		
		if ($active_cells['Date'])
		{
			$isty = ps_GetTY($mybb->settings['ps_date_format_ty'], $newest_threads['lastpost'], $offset="", $ty=1);
			if ($isty)
			{
				// Relative Time:
				if(TIME_NOW - $newest_threads['lastpost'] < 43200)
				{
					$datetime = my_date('relative', $newest_threads['lastpost'], NULL, 1);
				}
				else
				{
					$dateformat = preg_replace('#'.$mybb->settings['ps_date_format_ty'].'#', "vvv", $dateformat);
					$datetime = my_date($dateformat, $newest_threads['lastpost'], NULL, 1);
					$datetime = preg_replace('#vvv#', $isty, $datetime);
				}
			}
			else
			{
				$datetime = my_date($dateformat, $newest_threads['lastpost'], NULL, 1);
			}
		}
		
		if ($active_cells['Latest_posts'])
		{
			$parsed_subject = $parser->parse_badwords($newest_threads['subject']);
			$subject = ps_SubjectLength($plainprefix . $parsed_subject);
			$subject = $styledprefix . htmlspecialchars_uni(my_substr($subject, my_strlen($plainprefix)));
			$subject_long = $plainprefix . htmlspecialchars_uni($parsed_subject);
			$threadlink = $mybb->settings['bburl'].'/'.get_thread_link($tid,NULL,"lastpost");
			eval("\$readstate_icon = \"".$templates->get("prostats_readstate_icon")."\";");
			eval("\$newestposts_specialchar = \"".$templates->get("prostats_newestposts_specialchar")."\";");
		}
		
		if ($active_cells['Starter'])
		{
			$username = ps_FormatNameDb($fuid, htmlspecialchars_uni($newest_threads['username']));
			$profilelink = $mybb->settings['bburl'].'/'.get_profile_link($fuid);
		}
		
		if ($active_cells['Last_sender'])
		{
			$lastposter_uname = ps_FormatNameDb($newest_threads['lastposteruid'], htmlspecialchars_uni($newest_threads['lastposter']));
			$lastposter_profile = $mybb->settings['bburl'].'/'.get_profile_link($newest_threads['lastposteruid']);
		}
		
		if ($active_cells['Forum'])
		{
			$forumlink = get_forum_link($fid);
			$forumname_long = $parser->parse_badwords(strip_tags($newest_threads['name']));
			$forumname = htmlspecialchars_uni(ps_SubjectLength($forumname_long, NULL, true));		
		}
		
		for($i=1;$i<=$colspan;++$i)
		{
			switch($cell_order[$i])
			{
				case "Latest_posts" : 
					eval("\$newestposts_cols .= \"".$templates->get("prostats_newestposts_latest_posts")."\";");
					break;
				case "Date" :
					eval("\$newestposts_cols .= \"".$templates->get("prostats_newestposts_date")."\";");
					break;
				case "Starter" :
					eval("\$newestposts_cols .= \"".$templates->get("prostats_newestposts_starter")."\";");
					break;
				case "Last_sender" :
					eval("\$newestposts_cols .= \"".$templates->get("prostats_newestposts_last_sender")."\";");
					break;
				case "Forum" :
					eval("\$newestposts_cols .= \"".$templates->get("prostats_newestposts_forum")."\";");
					break;
				default: NULL;
			}
		}

		eval("\$newestposts_row .= \"".$templates->get("prostats_newestposts_row")."\";");
		
		
		if ($feed)
		{
			$feeditem[$loop_counter]['tid'] = $tid;
			$feeditem[$loop_counter]['fuid'] = $fuid;
			$feeditem[$loop_counter]['fid'] = $fid;
			$feeditem[$loop_counter]['bulb'] = $lightbulb['folder'];
			$feeditem[$loop_counter]['lasttime'] = $newest_threads['lastpost'];
			$feeditem[$loop_counter]['datetime'] = $datetime;
			
			if ($active_cells['Latest_posts'])
			{
				$feeditem[$loop_counter]['subject'] = $subject;
				$feeditem[$loop_counter]['subject_long'] = $subject_long;
			}
			
			if ($active_cells['Starter'])
			{
				$feeditem[$loop_counter]['username'] = htmlspecialchars_uni($newest_threads['username']);
				$feeditem[$loop_counter]['username_formed'] = $username;
			}
			
			if ($active_cells['Last_sender'])
			{
				$feeditem[$loop_counter]['lastposter_uid'] = $newest_threads['lastposteruid'];
				$feeditem[$loop_counter]['lastposter_uname'] = htmlspecialchars_uni($newest_threads['lastposter']);
				$feeditem[$loop_counter]['lastposter_uname_formed'] = $lastposter_uname;
			}
			
			if ($active_cells['Forum'])
			{
				$feeditem[$loop_counter]['forumname'] = $forumname;
				$feeditem[$loop_counter]['forumname_long'] = $forumname_long;
			}
		}
		
		++$loop_counter;
	}
	
	eval("\$newestposts = \"".$templates->get("prostats_newestposts")."\";");
	
	return $newestposts;
}


function ps_GetMostReplies($NumOfRows)
{
	global $mybb, $db, $templates, $theme, $lang, $unviewables, $under_mod_forums_arr, $vcheck, $parser, $ps_align;

	if (!is_object($parser))
	{
		require_once MYBB_ROOT.'inc/class_parser.php';
		$parser = new postParser;
	}
	
	$query = $db->query ("
		SELECT t.subject,t.tid,t.fid,t.replies,t.lastpost,t.visible,tr.uid AS truid,tr.dateline 
		FROM ".TABLE_PREFIX."threads t 
		LEFT JOIN ".TABLE_PREFIX."threadsread tr ON (tr.tid=t.tid AND tr.uid='".$mybb->user['uid']."') 
		LEFT JOIN ".TABLE_PREFIX."forums f ON (f.fid = t.fid) 
		WHERE (t.visible = '1' ".$vcheck.") 
		".$unviewables['string']." 
		AND t.closed NOT LIKE 'moved|%' 
		AND t.visible != '-2' 
		AND f.active = '1' 
		ORDER BY t.replies DESC 
		LIMIT 0,".$NumOfRows);

	while ($most_replies = $db->fetch_array($query))
	{
		$subject_long = htmlspecialchars_uni($parser->parse_badwords($most_replies['subject']));
		$tid = $most_replies['tid'];
		$subject = htmlspecialchars_uni(ps_SubjectLength($parser->parse_badwords($most_replies['subject']), NULL, true));
		$replies = $most_replies['replies'];
		$lightbulb['folder'] = "off";

		$highlight = ps_GetHighlight($most_replies);
		
		if ($mybb->user['uid'])
		{
			if ($most_replies['dateline'] && $most_replies['truid'] == $mybb->user['uid'])
			{
				if ($most_replies['lastpost'] > $most_replies['dateline'])
				{
					$lightbulb['folder'] = "on";
				}
			}
			else
			{
				if ($most_replies['lastpost'] > $mybb->user['lastvisit'])
				{
					$lightbulb['folder'] = "on";
				}
			}
		}
		
		$threadlink = $mybb->settings['bburl'].'/'.get_thread_link($tid);
		
		eval("\$readstate_icon = \"".$templates->get("prostats_readstate_icon")."\";");
		eval("\$mostreplies_row .= \"".$templates->get("prostats_mostreplies_row")."\";");
	}
	eval("\$column_mostreplies = \"".$templates->get("prostats_mostreplies")."\";");

	return $column_mostreplies;
}


function ps_GetMostReputation($NumOfRows)
{
	global $mybb, $db, $templates, $theme, $lang, $parser, $ps_align;

	$query = $db->query("
		SELECT u.uid,u.reputation,u.username,u.usergroup,u.displaygroup 
		FROM ".TABLE_PREFIX."users u 
		LEFT JOIN ".TABLE_PREFIX."usergroups ug ON (u.usergroup = ug.gid) 
		WHERE ug.usereputationsystem='1' 
		ORDER BY u.reputation DESC 
		LIMIT 0,".$NumOfRows
	);

	while ($most_reputations = $db->fetch_array($query)) {
		$uid = $most_reputations['uid'];
		$profilelink = $mybb->settings['bburl'].'/'.get_profile_link($uid);
		$repscount = intval($most_reputations['reputation']);
		$username = ps_FormatName(htmlspecialchars_uni($most_reputations['username']), $most_reputations['usergroup'], $most_reputations['displaygroup']);
		
		eval("\$mostreputation_row .= \"".$templates->get("prostats_mostreputation_row")."\";");
	}
	eval("\$column_mostreputation = \"".$templates->get("prostats_mostreputation")."\";");

	return $column_mostreputation;
}


function ps_GetMostThanks($NumOfRows)
{
	global $mybb, $db, $templates, $theme, $lang, $ps_align;
	
	if (!$db->field_exists("thxcount","users"))
	{
		$mostthanks_row .= "<tr class=\"smalltext\"><td colspan=\"2\" align=\"center\"><small>".$lang->prostats_err_thxplugin."</small></td></tr>";
		eval("\$column_mostthanks = \"".$templates->get("prostats_mostthanks")."\";");
		return $column_mostthanks;
	}
	
	$query = $db->query("SELECT uid,username,usergroup,displaygroup,thxcount FROM ".TABLE_PREFIX."users ORDER BY thxcount DESC LIMIT 0,".$NumOfRows);

	while ($most_thanks = $db->fetch_array($query))
	{
		$uid = $most_thanks['uid'];
		$username = ps_FormatName(htmlspecialchars_uni($most_thanks['username']), $most_thanks['usergroup'], $most_thanks['displaygroup']);
		$thxnum = $most_thanks['thxcount'];
		$profilelink = $mybb->settings['bburl'].'/'.get_profile_link($uid);		
		eval("\$mostthanks_row .= \"".$templates->get("prostats_mostthanks_row")."\";");
	}
	eval("\$column_mostthanks = \"".$templates->get("prostats_mostthanks")."\";");

	return $column_mostthanks;
}


function ps_GetMostViewed($NumOfRows)
{
	global $mybb, $db, $templates, $theme, $lang, $unviewables, $under_mod_forums_arr, $vcheck, $parser, $ps_align;
	
	if (!is_object($parser))
	{
		require_once MYBB_ROOT.'inc/class_parser.php';
		$parser = new postParser;
	}
	
	$query = $db->query ("
		SELECT t.subject,t.tid,t.fid,t.lastpost,t.views,t.visible,tr.uid AS truid,tr.dateline 
		FROM ".TABLE_PREFIX."threads t 
		LEFT JOIN ".TABLE_PREFIX."threadsread tr ON (tr.tid=t.tid AND tr.uid='".$mybb->user['uid']."') 
		LEFT JOIN ".TABLE_PREFIX."forums f ON (f.fid = t.fid) 
		WHERE (t.visible = '1' ".$vcheck.") 
		".$unviewables['string']." 
		AND t.closed NOT LIKE 'moved|%' 
		AND t.visible != '-2' 
		AND f.active = '1' 
		ORDER BY t.views DESC 
		LIMIT 0,".$NumOfRows);

	while ($most_views = $db->fetch_array($query))
	{
		$subject_long = htmlspecialchars_uni($parser->parse_badwords($most_views['subject']));
		$tid = $most_views['tid'];
		$subject = htmlspecialchars_uni(ps_SubjectLength($parser->parse_badwords($most_views['subject']), NULL, true));
		$views = $most_views['views'];
		$lightbulb['folder'] = "off";

		$highlight = ps_GetHighlight($most_views);
		
		if ($mybb->user['uid'])
		{
			if ($most_views['dateline'] && $most_views['truid'] == $mybb->user['uid'])
			{
				if ($most_views['lastpost'] > $most_views['dateline'])
				{
					$lightbulb['folder'] = "on";
				}
			}
			else
			{
				if ($most_views['lastpost'] > $mybb->user['lastvisit'])
				{
					$lightbulb['folder'] = "on";
				}
			}
		}
		
		$threadlink = $mybb->settings['bburl'].'/'.get_thread_link($tid);
		
		eval("\$readstate_icon = \"".$templates->get("prostats_readstate_icon")."\";");
		eval("\$mostviews_row .= \"".$templates->get("prostats_mostviews_row")."\";");
	}
	eval("\$column_mostviews = \"".$templates->get("prostats_mostviews")."\";");

	return $column_mostviews;
}


function ps_GetNewMembers($NumOfRows)
{
	global $mybb, $db, $templates, $theme, $lang, $ps_align;

	$query = $db->query("SELECT uid,regdate,username,usergroup,displaygroup FROM ".TABLE_PREFIX."users ORDER BY uid DESC LIMIT 0,".$NumOfRows);

	while ($newest_members = $db->fetch_array($query)) {
		$uid = $newest_members['uid'];
		$profilelink = $mybb->settings['bburl'].'/'.get_profile_link($uid);
		$username = ps_FormatName(htmlspecialchars_uni($newest_members['username']), $newest_members['usergroup'], $newest_members['displaygroup']);
		if ($newest_members['regdate']==0 || !$mybb->settings['ps_date_format_ty'])
		{
			$regdate = $lang->prostats_err_undefind;
		}
		else
		{
			$isty = ps_GetTY($mybb->settings['ps_date_format_ty'], $newest_members['regdate'], $offset="", $ty=1);
			if(TIME_NOW - $newest_members['regdate'] < 43200)
			{
				$regdate = my_date('relative', $newest_members['regdate'], NULL, 1);
			}
			if ($isty)
			{
				$regdate = $isty;
			}
			else
			{
				$regdate = my_date($mybb->settings['ps_date_format_ty'], $newest_members['regdate'], NULL, 1);
			}
		}

		eval("\$newmembers_row .= \"".$templates->get("prostats_newmembers_row")."\";");
	}
	eval("\$column_newmembers = \"".$templates->get("prostats_newmembers")."\";");

	return $column_newmembers;
}


function ps_GetTopDownloads($NumOfRows)
{
	global $mybb, $db, $templates, $theme, $lang, $parser, $unviewables, $under_mod_forums_arr, $vcheck, $ps_align;
	
	if (!is_object($parser))
	{
		require_once MYBB_ROOT.'inc/class_parser.php';
		$parser = new postParser;
	}
	
	$query = $db->query("
		SELECT p.subject,t.fid,t.visible,a.pid,a.downloads,a.filename 
		FROM ".TABLE_PREFIX."attachments a 
		LEFT JOIN ".TABLE_PREFIX."posts p ON (p.pid = a.pid) 
		LEFT JOIN ".TABLE_PREFIX."threads t ON (t.tid = p.tid) 
		WHERE (t.visible = '1' ".$vcheck.") 
		".$unviewables['string']." 
		AND t.closed NOT LIKE 'moved|%' 
		AND t.visible != '-2' 
		AND a.thumbnail = '' 
		GROUP BY p.pid 
		ORDER BY a.downloads DESC 
		LIMIT 0,".$NumOfRows);
		
	$query_icon = $db->query("SELECT extension,icon FROM ".TABLE_PREFIX."attachtypes");
	while ($result_icon = $db->fetch_array($query_icon))
	{
		$mimicon[$result_icon['extension']] = $result_icon['icon'];
	}
	
	while ($top_downloads = $db->fetch_array($query))
	{
		$subject_long = htmlspecialchars_uni($parser->parse_badwords($top_downloads['subject']));
		$pid = $top_downloads['pid'];
		$subject = htmlspecialchars_uni(ps_SubjectLength($parser->parse_badwords($top_downloads['subject']), NULL, true));
		$downloadnum = $top_downloads['downloads'];
		$attach_icon =  $mimicon[get_extension($top_downloads['filename'])];

		$highlight = ps_GetHighlight($top_downloads);
		
		$postlink = get_post_link($pid)."#pid".$pid;
		
		eval("\$topdownloads_row .= \"".$templates->get("prostats_topdownloads_row")."\";");
	}
	eval("\$column_topdownloads = \"".$templates->get("prostats_topdownloads")."\";");

	return $column_topdownloads;
}


function ps_GetTopPosters($NumOfRows)
{
	global $mybb, $db, $templates, $theme, $lang, $ps_align;

	$query = $db->query("SELECT username,postnum,uid,usergroup,displaygroup FROM ".TABLE_PREFIX."users ORDER BY postnum DESC LIMIT 0,".$NumOfRows);

	while ($topposters = $db->fetch_array($query))
	{
		$uid = $topposters['uid'];
		$username = ps_FormatName(htmlspecialchars_uni($topposters['username']), $topposters['usergroup'], $topposters['displaygroup']);
		$postnum = $topposters['postnum'];
		
		$profilelink = $mybb->settings['bburl'].'/'.get_profile_link($uid);
		
		eval("\$topposters_row .= \"".$templates->get("prostats_topposters_row")."\";");
	}
	eval("\$column_topposters = \"".$templates->get("prostats_topposters")."\";");

	return $column_topposters;
}


function ps_GetTopThreadPosters($NumOfRows)
{
	global $mybb, $db, $templates, $theme, $lang, $ps_align;

	$query = $db->query("SELECT username,threadnum,uid,usergroup,displaygroup FROM ".TABLE_PREFIX."users ORDER BY threadnum DESC LIMIT 0,".$NumOfRows);

	while ($topposters = $db->fetch_array($query))
	{
		$uid = $topposters['uid'];
		$username = ps_FormatName(htmlspecialchars_uni($topposters['username']), $topposters['usergroup'], $topposters['displaygroup']);
		$postnum = $topposters['threadnum'];
		
		$profilelink = $mybb->settings['bburl'].'/'.get_profile_link($uid);
		
		eval("\$topthreadposters_row .= \"".$templates->get("prostats_topthreadposters_row")."\";");
	}
	eval("\$column_topthreadposters = \"".$templates->get("prostats_topthreadposters")."\";");

	return $column_topthreadposters;
}


function ps_GetTopReferrers($NumOfRows)
{
	global $mybb, $db, $templates, $theme, $lang, $ps_align;

	$query = $db->query("
	SELECT u.uid,u.username,u.usergroup,u.displaygroup,count(*) as refcount 
	FROM ".TABLE_PREFIX."users u 
	LEFT JOIN ".TABLE_PREFIX."users r ON (r.referrer = u.uid) 
	WHERE r.referrer = u.uid 
	GROUP BY r.referrer DESC 
	ORDER BY refcount DESC 
	LIMIT 0 ,".$NumOfRows);

	while ($topreferrer = $db->fetch_array($query)) {
		$uid = $topreferrer['uid'];
		$username = ps_FormatName(htmlspecialchars_uni($topreferrer['username']), $topreferrer['usergroup'], $topreferrer['displaygroup']);
		$refnum = $topreferrer['refcount'];
		
		$profilelink = $mybb->settings['bburl'].'/'.get_profile_link($uid);
		
		eval("\$topreferrers_row .= \"".$templates->get("prostats_topreferrers_row")."\";");
	}
	eval("\$column_topreferrers = \"".$templates->get("prostats_topreferrers")."\";");

	return $column_topreferrers;
}


function ps_MakeTable()
{
	global $mybb, $db, $theme, $lang, $templates, $parser, $unviewables, $vcheck, $under_mod_forums_arr, $lightbulb, $unread_forums, $ps_align;
	$lang->load("prostats");
	
	$right_cols = $left_cols = $middle_cols = $extra_content = $extra_content_1_2 = $extra_content_3_4 = $extra_content_5_6 = $remote_msg = "";
	$num_columns = 3;
	
	$ps_align = $lang->settings['rtl'] ? "right" : "left";
	$ps_ralign = $lang->settings['rtl'] ? "left" : "right";
	
	//Highlighting under moderation posts
	$_psGU = ps_GetUnviewable("t");
	
	$unviewables = array(
		'string'	=>	$_psGU[0],
		'array'		=>	$_psGU[1],
	);
	
	$user_perms = user_permissions($mybb->user['uid']);
	
	if ($mybb->settings['ps_highlight'])
	{
		$_groups = $mybb->user['usergroup'];
		if(!empty($mybb->user['additionalgroups']))
		{
			$_groups .= ",'{$mybb->user['additionalgroups']}'";
		}
		$_query1 = $db->simple_select("moderators", "*", "((id IN ({$_groups}) AND isgroup='1') OR (id='{$mybb->user['uid']}' AND isgroup='0'))");
		
		while($results1 = $db->fetch_array($_query1))
		{
			$parent_mod_forums[] = " parentlist LIKE '%" . $results1['fid'] . "%' ";
		}
		
		if (count($parent_mod_forums))
		{
			$_query2 = $db->simple_select("forums", "fid", implode($parent_mod_forums, "OR"));
			while($results2 = $db->fetch_array($_query2))
			{
				$under_mod_forums_arr[] = $results2['fid'];
			}
			
			if (count($under_mod_forums_arr))
			{
				$moderated_forums = implode($under_mod_forums_arr, ',');
				$vcheck = " OR t.fid IN (".$moderated_forums.") ";
			}
		}
	}
	
	if ($user_perms['issupermod'] == 1)
	{
		$vcheck = " OR '1'='1' ";
	}
	
	if ($mybb->settings['ps_latest_posts'] == 1)
	{
		$middle_cols = ps_GetNewestPosts($mybb->settings['ps_num_rows']);
		$num_columns = 4;
	}
	
	for($i=1;$i<7;++$i)
	{
		$extra_cell[$i] = $mybb->settings['ps_cell_'.$i];
	}

	$extra_row[1] = $extra_row[2] = $extra_row[3] = 2;
	$extra_cols = 3;
	
	if ($extra_cell[5] > 0)
	{
		$trow = "trow2";
		$extra_cols = 3;
		if ($extra_cell[6] == 0)
		{
			$extra_row[3] = 1;
			$single_extra_content = ps_GetExtraData($extra_cell[5],true);
			eval("\$extra_content_5_6 = \"".$templates->get("prostats_onerowextra")."\";");
		}
		else
		{
			$extra_content_one = ps_GetExtraData($extra_cell[5]);
			$extra_content_two = ps_GetExtraData($extra_cell[6]);
			eval("\$extra_content_5_6 = \"".$templates->get("prostats_tworowextra")."\";");
		}
	}

	
	if ($extra_cell[3] > 0)
	{
		$trow = "trow1";
		$extra_cols = 2;
		if ($extra_cell[4] == 0)
		{
			$extra_row[2] = 1;
			$single_extra_content = ps_GetExtraData($extra_cell[3],true);
			eval("\$extra_content_3_4 = \"".$templates->get("prostats_onerowextra")."\";");
		}
		else
		{
			$extra_content_one = ps_GetExtraData($extra_cell[3]);
			$extra_content_two = ps_GetExtraData($extra_cell[4]);
			eval("\$extra_content_3_4 = \"".$templates->get("prostats_tworowextra")."\";");
		}
	}
	
	if ($extra_cell[1] > 0)
	{
		$trow = "trow2";
		$extra_cols = 1;
		if ($extra_cell[2] == 0)
		{
			$extra_row[1] = 1;
			$single_extra_content = ps_GetExtraData($extra_cell[1],true);
			eval("\$extra_content_1_2 = \"".$templates->get("prostats_onerowextra")."\";");
		}
		else
		{
			$extra_content_one = ps_GetExtraData($extra_cell[1]);
			$extra_content_two = ps_GetExtraData($extra_cell[2]);
			eval("\$extra_content_1_2 = \"".$templates->get("prostats_tworowextra")."\";");
		}
	}
	
	if ($lang->settings['rtl'])
	{
		$extra_content = $extra_content_5_6 . $extra_content_3_4 . $extra_content_1_2;
		$mybb->settings['ps_latest_posts_pos'] ? $right_cols = $extra_content : $left_cols = $extra_content;
	}
	else
	{
		$extra_content = $extra_content_1_2 . $extra_content_3_4 . $extra_content_5_6;
		$mybb->settings['ps_latest_posts_pos'] ? $left_cols = $extra_content : $right_cols = $extra_content;
	}

	$prostats_content = $left_cols . $middle_cols . $right_cols;
	
	if ($mybb->settings['ps_trow_message'] != "") {
		$prostats_message = unhtmlentities(htmlspecialchars_uni($mybb->settings['ps_trow_message']));
		if ($mybb->settings['ps_trow_message_pos'] == 0) {
			eval("\$trow_message_top = \"".$templates->get("prostats_message")."\";");
		}
		else
		{
			eval("\$trow_message_down = \"".$templates->get("prostats_message")."\";");
		}
	}
	
	if ($mybb->settings['ps_surprise'] && $mybb->user['uid'] && $mybb->usergroup['cancp'])
	{
		prostats_g();
		$remote_msg .= '<a href="http://prostats.wordpress.com/surprises/"><embed src="'.$mybb->psga['surprise_link'].'" type="image/svg+xml" /></a>';
	}
	
	if ($mybb->settings['ps_chkupdates'] && $mybb->user['uid'] && $mybb->usergroup['cancp'])
	{
		prostats_g();
		$remote_msg .= '<a href="http://prostats.wordpress.com/"><embed src="'.$mybb->psga['update_popup_link'].'" type="image/svg+xml" /></a>';
	}
		
	eval("\$prostats = \"".$templates->get("prostats")."\";");
	return $prostats;
}


function ps_GetExtraData($cellnum,$fullrows=false)
{
	global $mybb;
	
	if ($fullrows)
	{
		$rows = ($mybb->settings['ps_num_rows'] + 1);
	}
	else
	{
		$rows = $mybb->settings['ps_num_rows'];
		$rows = (ceil($rows/2)-1);
		if (!(($mybb->settings['ps_num_rows'])%2) && !($cellnum%2)){++$rows;}
	}

	switch($cellnum)
	{
		case 0: $res = ''; break;
		case 1: $res = ps_GetMostReplies($rows); break;
		case 2: $res = ps_GetMostReputation($rows); break;
		case 3: $res = ps_GetMostThanks($rows); break;
		case 4: $res = ps_GetMostViewed($rows); break;
		case 5: $res = ps_GetNewMembers($rows); break;
		case 6: $res = ps_GetTopDownloads($rows); break;
		case 7: $res = ps_GetTopPosters($rows); break;
		case 8: $res = ps_GetTopReferrers($rows); break;
		case 9: $res = ps_GetTopThreadPosters($rows); break;
		default: $res = ''; NULL;
	}
	
	return $res;
}


function ps_GetUnviewable($name="")
{
	global $mybb, $forum_cache;
	$unviewwhere = $comma = '';
	$name ? $name .= '.' : NULL;
	$unviewable = get_unviewable_forums();
	
	if ($mybb->settings['ps_ignoreforums'])
	{
		!is_array($forum_cache) ? cache_forums() : NULL;
		
		if (in_array($mybb->settings['ps_ignoreforums'], array(-1, 'all')))
		{
			foreach($forum_cache as $fid => $forum)
			{
				$ignoreforums[] = $forum['fid'];
			}
		}
		else
		{
			$ignoreforums = explode(',', $mybb->settings['ps_ignoreforums']);
		}
		
		if (count($ignoreforums))
		{
			$unviewable ? $unviewable .= ',' : NULL;
			
			foreach($ignoreforums as $fid)
			{
				$unviewable .= $comma."'".intval($fid)."'";
				$comma = ',';
			}
		}
	}
	
	if ($unviewable)
	{
		$unviewwhere = "AND ".$name."fid NOT IN (".$unviewable.")";
	}

	return array($unviewwhere, explode(',', $unviewable));
}


function ps_FormatName($username, $usergroup, $displaygroup)
{
	global $mybb;

	if ($mybb->settings['ps_format_name'] == '1')
	{
		$username = format_name($username, $usergroup, $displaygroup);
	}
	return $username;
}


function ps_FormatNameDb($uid, $username="")
{
	global $mybb, $db, $cache;

	if ($mybb->settings['ps_format_name'] == "1")
	{
		$query = $db->query("SELECT username,usergroup,displaygroup FROM ".TABLE_PREFIX."users WHERE uid = '".$uid."'");
		$query_array = $db->fetch_array($query);
		$username = format_name($query_array['username'], $query_array['usergroup'], $query_array['displaygroup']);
	}
	else if ($username=="")
	{
		$query = $db->query("SELECT username FROM ".TABLE_PREFIX."users WHERE uid = '".$uid."'");
		$query_array = $db->fetch_array($query);
		$username = $query_array['username'];
	}
	
	return $username;
}


function ps_SubjectLength($subject, $length="", $half=false)
{
	global $mybb;
	$length = $length ? intval($length) : intval($mybb->settings['ps_subject_length']);
	$half ? $length = ceil($length/2) : NULL;
	if ($length != 0)
	{
		if (my_strlen($subject) > $length) 
		{
			$subject = my_substr($subject, 0, $length) . '...';
		}
	}
	return $subject;
}


function ps_GetTY($format='m-d', $stamp='', $offset='', $ty=1)
{
	global $mybb, $lang, $mybbadmin, $plugins;

	if (!$offset && $offset != '0')
	{
		if ($mybb->user['uid'] != 0 && array_key_exists('timezone', $mybb->user))
		{
			$offset = $mybb->user['timezone'];
			$dstcorrection = $mybb->user['dst'];
		}
		else
		{
			$offset = $mybb->settings['timezoneoffset'];
			$dstcorrection = $mybb->settings['dstcorrection'];
		}

		if ($dstcorrection == 1)
		{
			++$offset;
			if (my_substr($offset, 0, 1) != '-')
			{
				$offset = '+'.$offset;
			}
		}
	}

	if ($offset == '-')
	{
		$offset = 0;
	}
	
	$date = gmdate($format, $stamp + ($offset * 3600));
	
	if ($format && $ty)
	{
		$stamp = TIME_NOW;
		
		$todaysdate = gmdate($format, $stamp + ($offset * 3600));
		$yesterdaysdate = gmdate($format, ($stamp - 86400) + ($offset * 3600));

		if ($todaysdate == $date)
		{
			$date = $lang->today;
			return $date;
		}
		else if ($yesterdaysdate == $date)
		{
			$date = $lang->yesterday;
			return $date;
		}
	}
	return false;
}


function ps_GetHighlight($query_arr=array())
{
	global $mybb, $under_mod_forums_arr;
	
	if (!$mybb->settings['ps_highlight']) { return false; }	
	if (!count($query_arr)) { return false; }
	
	$highlight_arr['style'] = array(
		'unapproved'	=>	' ps_unapproved',//background-color:#FFDDE0;
		'deleted'	=>	' ps_deleted',//background-color:#E8DEFF;
		'undermod'	=>	' ps_undermod',//background-color:#FFFE92;
		'both'		=>	' ps_both'//background-color:#FFDA91;
	);
	
	
	if ($query_arr['visible'] != 1 && is_array($under_mod_forums_arr) && in_array($query_arr['fid'], $under_mod_forums_arr))
	{
		$highlight = $highlight_arr['style']['both'];
	}
	else if ($query_arr['visible'] == -1)
	{
		$highlight = $highlight_arr['style']['deleted'];
	}
	else if ($query_arr['visible'] != 1)
	{
		$highlight = $highlight_arr['style']['unapproved'];
	}
	else if (is_array($under_mod_forums_arr) && in_array($query_arr['fid'], $under_mod_forums_arr))
	{
		$highlight = $highlight_arr['style']['undermod'];
	}
	
	return $highlight;
}


function prostats_run_ajax()
{
	global $mybb, $plugins, $lang, $parser, $session, $prostats_tbl;

	$lang->load('prostats');

	if ($mybb->input['action'] != "prostats_reload" || $mybb->request_method != "post")
	{
		return false;
	}

	if (!$mybb->settings['ps_enable'] || $mybb->settings['ps_hidefrombots'] && !empty($session->is_spider))
	{
		error($lang->prostats_disabled);
	}
		
	if (!is_object($parser))
	{
		require_once MYBB_ROOT.'inc/class_parser.php';
		$parser = new postParser;
	}

	if (!verify_post_check($mybb->input['my_post_key'], true))
	{
		xmlhttp_error($lang->invalid_post_code);
	}	
	
	prostats_run_index(true);
	
	$plugins->run_hooks('prostats_xml_pre_output_page');
	
	header('Content-Type: text/json');
	echo json_encode(array("html" => $prostats_tbl));
}


function prostats_run_feed()
{
	global $mybb, $db, $templates, $theme, $lang, $unviewables, $parser, $session, $lightbulb, $trow, $newestposts_cols_name, $newestposts_cols, $colspan, $feeditem;
	
	if (!$mybb->settings['ps_enable'] || !$mybb->settings['ps_xml_feed']) {return false;}
	
	if ($mybb->settings['ps_hidefrombots'] && !empty($session->is_spider)) {return false;}
	
	if (!is_object($parser))
	{
		require_once MYBB_ROOT.'inc/class_parser.php';
		$parser = new postParser;
	}
	
	$seo = 0;
	
	if ($mybb->settings['seourls'] == "yes" || ($mybb->settings['seourls'] == "auto" && $_SERVER['SEO_SUPPORT'] == 1))
	{
		$seo = 1;
	}
	
	ps_GetNewestPosts($mybb->settings['ps_num_rows'], true);
	
	//echo '<pre>';print_r($feeditem);echo '</pre>';exit;//just for test! ;-)

	/*
	$feeditem
	{
		[tid]
		[fuid]
		[fid]
		[bulb]
		[lasttime]
		[datetime]
		[subject]
		[username]
		[username_formed]
		[lastposter_uid]
		[lastposter_uname]
		[lastposter_uname_formed]
		[lastposter_profile]
		[forumname]
		[forumname_long]
	}
	*/
	
	$xml_feed = '<?xml version="1.0" encoding="UTF-8"?>';
	$xml_feed .= '<ProStats>';
	$xml_feed .= '<bburl>'.$mybb->settings['bburl'].'</bburl>';
	$xml_feed .= '<seo>'.intval($seo).'</seo>';
	
	foreach($feeditem as $key => $value)
	{
		$xml_feed .= '<record num="'.($key+1).'">';
		$xml_feed .= '<tid>'.$feeditem[$key]['tid'].'</tid>';
		$xml_feed .= '<fuid>'.$feeditem[$key]['fuid'].'</fuid>';
		$xml_feed .= '<fid>'.$feeditem[$key]['fid'].'</fid>';
		$xml_feed .= '<bulb>'.$feeditem[$key]['bulb'].'</bulb>';
		$xml_feed .= '<lasttime>'.$feeditem[$key]['lasttime'].'</lasttime>';
		$xml_feed .= '<datetime>'.htmlspecialchars_uni($feeditem[$key]['datetime']).'</datetime>';
		$xml_feed .= '<subject>'.htmlspecialchars_uni($feeditem[$key]['subject']).'</subject>';
		$xml_feed .= '<longsubject>'.htmlspecialchars_uni($feeditem[$key]['subject_long']).'</longsubject>';
		$xml_feed .= '<uname>'.htmlspecialchars_uni($feeditem[$key]['username']).'</uname>';
		$xml_feed .= '<uname2>'.htmlspecialchars_uni($feeditem[$key]['username_formed']).'</uname2>';
		$xml_feed .= '<luid>'.$feeditem[$key]['lastposter_uid'].'</luid>';
		$xml_feed .= '<luname>'.htmlspecialchars_uni($feeditem[$key]['lastposter_uname']).'</luname>';
		$xml_feed .= '<luname2>'.htmlspecialchars_uni($feeditem[$key]['lastposter_uname_formed']).'</luname2>';
		$xml_feed .= '<fname>'.htmlspecialchars_uni($feeditem[$key]['forumname']).'</fname>';
		$xml_feed .= '<ffullname>'.htmlspecialchars_uni($feeditem[$key]['forumname_long']).'</ffullname>';
		$xml_feed .= '</record>';
	}

	$xml_feed .= '</ProStats>';
	
	
	if ($mybb->settings['gzipoutput'] == 1)
	{
		if (version_compare(PHP_VERSION, '4.2.0', '>='))
		{
			$xml_feed = gzip_encode($xml_feed, $mybb->settings['gziplevel']);
		}
		else
		{
			$xml_feed = gzip_encode($xml_feed);
		}
	}
	
	header("content-type: text/xml");
	echo $xml_feed;
}
