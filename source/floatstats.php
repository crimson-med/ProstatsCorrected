<?php 
/*
 _______________________________________________________
|                                                       |
| Name: FloatStats 1.2.3                                |
| Type: MyBB Plugin's additional script                 |
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

// initialization
define("IN_MYBB", "1");

if(!defined('MYBB_ROOT'))
{
	define('MYBB_ROOT', dirname(__FILE__)."/");
}

error_reporting(E_ALL & ~E_NOTICE);

// generate the JavaScript codes
if ($_GET['fs_action'] == 'js'){
	require_once MYBB_ROOT."global.php";
	fs_js();
}

// generate the CSS codes
if ($_GET['fs_action'] == 'css'){
	require_once MYBB_ROOT."global.php";
	fs_css();
}

require_once MYBB_ROOT."inc/config.php";

if(!isset($config['database']) || !is_array($config['database'])){ exit; }
if(is_dir(MYBB_ROOT."install") && !file_exists(MYBB_ROOT."install/lock")){ exit; }

// set the target settings
$target['uid'] = $_GET['t_uid'] ? intval($_GET['t_uid']) : 0;

if (in_array($_GET['t_script'], array('index.php', 'portal.php', 'showthread.php'))){
	$target['script'] = $_GET['t_script'];
} else {
	$target['script'] = 'index.php';
}

if (in_array($_GET['t_key'], array('tid', 'pid')) && $_GET['t_value'])
{
	$target['input'] = array('key' => $_GET['t_key'], 'value' => intval($_GET['t_value']));
}

if($_GET['hash'] !== md5($config['database']['username'] . $config['database']['password'])){ exit; }

$post_arr = array();

if ($_POST && is_array($_POST) && count($_POST))
{
	foreach ($_POST as $post_key => $post_value)
	{
		$post_arr[$post_key] = $post_value;
	}
}

require_once MYBB_ROOT."inc/functions.php";

// Load DB interface
require_once MYBB_ROOT."inc/db_base.php";
// Connect to Database
require_once MYBB_ROOT."inc/db_".$config['database']['type'].".php";

switch($config['database']['type'])
{
	case "sqlite":
		$db = new DB_SQLite;
		break;
	case "pgsql":
		$db = new DB_PgSQL;
		break;
	case "mysqli":
		$db = new DB_MySQLi;
		break;
	default:
		$db = new DB_MySQL;
}

if(!extension_loaded($db->engine)){ exit; }

define("TABLE_PREFIX", $config['database']['table_prefix']);
$db->connect($config['database']);
$db->set_table_prefix(TABLE_PREFIX);
$db->type = $config['database']['type'];

// get uset data
$udata = get_user($target['uid']);

// set the cookie
$_COOKIE['mybbuser'] = $udata['uid'].'_'.$udata['loginkey'];

// set the script name
define('THIS_SCRIPT', $target['script']);

// default template lists
switch ($target['script'])
{
	case 'index.php':
		$templatelist = "index,index_whosonline,index_welcomemembertext,index_welcomeguest,index_whosonline_memberbit,forumbit_depth1_cat,forumbit_depth1_forum,forumbit_depth2_cat,forumbit_depth2_forum,forumbit_depth1_forum_lastpost,forumbit_depth2_forum_lastpost,index_modcolumn,forumbit_moderators,forumbit_subforums,index_welcomeguesttext";
		$templatelist .= ",index_birthdays_birthday,index_birthdays,index_pms,index_loginform,index_logoutlink,index_stats,forumbit_depth3,forumbit_depth3_statusicon,index_boardstats";
	break;
	
	case 'portal.php':
		$templatelist = "portal_welcome,portal_welcome_membertext,portal_stats,portal_search,portal_whosonline_memberbit,portal_whosonline,portal_latestthreads_thread_lastpost,portal_latestthreads_thread,portal_latestthreads,portal_announcement_numcomments_no,portal_announcement,portal_announcement_numcomments,portal_pms,portal";
	break;
	
	case 'showthread.php':
		$templatelist = "showthread,postbit,postbit_author_user,postbit_author_guest,showthread_newthread,showthread_newreply,showthread_newreply_closed,postbit_sig,showthread_newpoll,postbit_avatar,postbit_profile,postbit_find,postbit_pm,postbit_www,postbit_email,postbit_edit,postbit_quote,postbit_report,postbit_signature, postbit_online,postbit_offline,postbit_away,postbit_gotopost,showthread_ratethread,showthread_inline_ratethread,showthread_moderationoptions";
		$templatelist .= ",multipage_prevpage,multipage_nextpage,multipage_page_current,multipage_page,multipage_start,multipage_end,multipage";
		$templatelist .= ",postbit_editedby,showthread_similarthreads,showthread_similarthreads_bit,postbit_iplogged_show,postbit_iplogged_hiden,showthread_quickreply";
		$templatelist .= ",forumjump_advanced,forumjump_special,forumjump_bit,showthread_multipage,postbit_reputation,postbit_quickdelete,postbit_attachments,thumbnails_thumbnail,postbit_attachments_attachment,postbit_attachments_thumbnails,postbit_attachments_images_image,postbit_attachments_images,postbit_posturl,postbit_rep_button";
		$templatelist .= ",postbit_inlinecheck,showthread_inlinemoderation,postbit_attachments_thumbnails_thumbnail,postbit_quickquote,postbit_qqmessage,postbit_ignored,postbit_groupimage,postbit_multiquote,showthread_search,postbit_warn,postbit_warninglevel,showthread_moderationoptions_custom_tool,showthread_moderationoptions_custom,showthread_inlinemoderation_custom_tool,showthread_inlinemoderation_custom,postbit_classic,showthread_classic_header,showthread_poll_resultbit,showthread_poll_results";
		$templatelist .= ",showthread_usersbrowsing,showthread_usersbrowsing_user";
	break;
	
	default: exit;
}

require_once MYBB_ROOT."inc/init.php";

// apply settings on the fly
foreach ($post_arr as $p_k => $p_v)
{
	$mybb->settings[$p_k] = $p_v;
}

require_once MYBB_ROOT."global.php";

if ($_GET['fs_action'] != 'preview')
{
	$plugins->add_hook("pre_output_page", "fs_run", 1);
}

//$mybb->debug_mode = true;

// set the optional input (URL query)
if (is_array($target['input']) && count($target['input']))
{
	$mybb->input[$target['input']['key']] = $target['input']['value'];
}

// generate preview page
if ($_GET['fs_action'] == 'preview')
{
	$mybb->settings['ps_global_tag'] = 1;
	$page = '<html>
<head>
<title></title>
'.$headerinclude.'
</head>
<body>
	<div id="container">
		<div id="content">
			<ProStats>
		</div>
	</div>
<script>
/* overriding the original reload function */
prostats_reload = function(str) {
	parent.fs_refresh();
}
</script>
</body>
</html>';
	$plugins->run_hooks('pre_output_page');
	send_page_headers;
	output_page($page);
	exit;
}
else
{
	require_once MYBB_ROOT.$target['script'];
}


// main function to return the stats array
function fs_run()
{
	global $db, $debug, $templates, $templatelist, $mybb, $maintimer, $globaltime, $ptimer, $parsetime, $target, $udata;
	
	if(function_exists("memory_get_usage"))
	{
		$memory_usage = get_friendly_size(memory_get_peak_usage(true));
	}
	else
	{
		$memory_usage = 'Unknown';
	}
	
	$query_count = $db->query_count;

	// patchs
	if ($target['script'] == 'index.php' && empty($target['uid']))
	{
		--$query_count;
	}
	else if ($target['script'] == 'portal.php')
	{
		//++$query_count;
	}
	else if ($target['script'] == 'showthread.php')
	{
		++$query_count;
	}
	
	if (!is_array($udata) || empty($udata['uid']))
	{
		--$query_count;
	}
	
	header("content-type: text/xml");
	
	$output = "<?xml version='1.0' encoding='UTF-8'?>
<FloatStats>
	<DatabaseQueries>$query_count</DatabaseQueries>
	<MemoryUsage>$memory_usage</MemoryUsage>
</FloatStats>";
	
	echo $output;
	exit;
}


function fs_js()
{
	global $mybb, $config;
	header("Content-type: text/javascript");

?>

var ScriptTag="<script>";

<?php 
	if ($mybb->user['uid'] && $mybb->usergroup['cancp'])
	{
		echo 'var hashcode = "'.md5($config['database']['username'] . $config['database']['password']).'";';
		echo "\n";
		echo 'var cur_admin_id = "'.$mybb->user['uid'].'";';	
?>

var welcomeDefTop = $("#welcome").offset().top;

function DragCorner(container, handle, iframe)
{
	var container = $('#'+container);
	var handle = $('#'+handle);
	var iframe = $('#'+iframe);
	
	dragged = function(e)
	{
		container_new_height = e.clientY;
		if(container_new_height < 50)
		{
			$(document).unbind('mousemove');
			handle.attr('class', 'unselectable preview_handle_transition');
			container.animate({'height': '0px'}, 100);
			$('body').animate({'margin-top': '0px'}, 100);
			window.setTimeout(function() {
				handle.removeClass('preview_handle_transition');
				handle.addClass('preview_handle');
			}, 500);
		}
		if(container_new_height > $(window).height()-40) {
			container_new_height = $(window).height()-40;
		}
		container.css('height', container_new_height + 'px');
		$('body').css('margin-top', container_new_height + 'px');
		
	};

	$(document).bind('mousedown', function(e){
		if($(e.target).closest(handle).length == 1 && e.clientY >= 30)
		{
			$(document).bind('mousemove', dragged);
			$(document).disableSelection();
			iframe.css('visibility', 'hidden');
			container_new_height = (e.clientY);
			if(container_new_height < 0) {
				container_new_height = 0;
			}
			if(container_new_height > $(window).height()-40) {
				container_new_height = $(window).height()-40;
			}
			container.animate({'height': container_new_height + 'px'}, 100);
			$('body').animate({'margin-top': container_new_height + 'px'}, 100);
		}
	});
	$(document).bind('mouseup', function(e){
		$(document).unbind('mousemove');
		$(document).enableSelection();
		iframe.css('visibility', 'visible');
	});
	
	handle.mousedown(function(e)
	{
		if(e.clientY < 30)
		{
			container.animate({'height': '300px'}, 'fast');
			$('body').animate({'margin-top': '300px'}, 'fast');
		}
	});
	handle.hover(function()
	{
		if(container.height() == 0) {
			$(this).css('cursor', 'pointer');
		} else {
			$(this).css('cursor', 's-resize');
		}
	});
};

var spinner = false;
$("#float_notification").css({display:"block"});
$("#preview_iframe_holder").css({display:"block"});
$("#preview_iframe_holder").append('<div id="preview_iframe_spinner"><img src="../images/spinner_big.gif" alt="spinner" /></div>');
$("#preview_handle").css({display:"block"});
$("#fs_auto_refresh_chk").attr('checked',true);
$("#fs_uid").val(cur_admin_id);
$("#fs_script").val("index.php");
$("#fs_key").val("tid");
$("#fs_value").val("");

function fs_refresh()
{
	if(spinner) {
		return false;
	}
	
	spinner = $('#preview_iframe_spinner').css('height', $('#preview_iframe_holder').height()+'px').show();
	var fs_postbody = "";
	
	$.each(settings_options, function(index, sname) {
		$("#setting_"+sname+"_yes").prop('checked') ? chkstats=1 : chkstats=0;
		fs_postbody += sname+"="+chkstats+"&";
	});

	$.each(settings_text, function(index, sname) {
		fs_postbody += sname+"="+$("#setting_"+sname).val()+"&";
	});
	
	$.each(settings_select, function(index, sname) {
		fs_postbody += sname+"="+$("#setting_"+sname).val()+"&";
	});
	
	$.each(settings_selectforums, function(index, sname) {
		var selforum = $(':radio[name="upsetting['+sname+']"]:checked', '#change').val();
		if (selforum == 'custom')
		{
			selforum = $('#setting_'+sname).val();
		}
		fs_postbody += sname+"="+selforum+"&";
	});
	
	$.ajax({
		url: "../floatstats.php?fs_action=preview&hash="+hashcode+"&t_script="+$("#fs_script").val()+"&t_uid="+$("#fs_uid").val()+"&t_key="+$("#fs_key").val()+"&t_value="+$("#fs_value").val(),
		type: 'POST',
		data: fs_postbody,
		success: fs_do_preview
	});
	
	$.ajax({
		url: "../floatstats.php?hash="+hashcode+"&t_script="+$("#fs_script").val()+"&t_uid="+$("#fs_uid").val()+"&t_key="+$("#fs_key").val()+"&t_value="+$("#fs_value").val(),
		type: 'POST',
		data: fs_postbody,
		dataType: 'XML',
		success: fs_do_refresh
	});
}

function fs_do_preview(data)
{
	iframe = $("#preview_iframe")[0];
	//Martin Honnen <mahotrash@yahoo.de> 
	var iframeDoc;
	if (iframe.contentDocument) {
		iframeDoc = iframe.contentDocument;
	}
	else if (iframe.contentWindow) {
		iframeDoc = iframe.contentWindow.document;
	}
	else if (window.frames[iframe.name]) {
		iframeDoc = window.frames[iframe.name].document;
	}
	if (iframeDoc) {
		iframeDoc.open();
		iframeDoc.write(data);
		iframeDoc.close();
	}

	if(spinner) {
		spinner.hide();
	}
	spinner = false;

	return false;
}

function fs_do_refresh(xml)
{
	try
	{
		var db_queries = xml.getElementsByTagName("DatabaseQueries").item(0).firstChild.data;
		var mem_usage = xml.getElementsByTagName("MemoryUsage").item(0).firstChild.data;
		if (db_queries) {
			$("#fs_queries_count").html(db_queries);
			$("#fs_mem_usage").html(mem_usage);
		}
		else 
		{
			alert("Connection failed!");
		}
	}
	catch(err)
	{
		alert(err);
	}
	finally
	{
		if(spinner) {
			spinner.hide();
		}
		spinner = false;
		return false;
	}
}

function toggle_float_note()
{
	if($("#close_float_note").html() != "^"){
		$("#close_float_note").html("^");
		$("#float_notification").css({
			left: "-181px",
			bottom: "-196px"
		});
	}else{
		$("#close_float_note").html("×");
		$("#float_notification").css({
			left: "0px",
			bottom: "0px"
		});
	}
}

function fs_autoupdate(){
	if ($("#fs_auto_refresh_chk").prop('checked')){
		fs_refresh();
	}
}

$(document).ready(function() {
	$.each(settings_options, function(index, sname) {
		$(':radio[name="upsetting['+sname+']"]', '#change').click(fs_autoupdate);
	});
	$.each(settings_text, function(index, sname) {
		$("#setting_"+sname).blur(fs_autoupdate);
	});

	$.each(settings_select, function(index, sname) {
		$("#setting_"+sname).change(fs_autoupdate);
	});
	
	$.each(settings_selectforums, function(index, sname) {
		$(':radio[name="upsetting['+sname+']"]', '#change').click(fs_autoupdate);
		$('#setting_'+sname).change(fs_autoupdate);
	});
	
	fs_refresh();
	DragCorner('preview_iframe_holder','preview_handle','preview_iframe');
}); 

<?php 
	} else {
?>
	var alertMsg = '<div class="error" id="flash_message">Two advanced features of ProStats ("FloatStats" & "Instant Viewer") are disabled because you are only logged in AdminCP, not in your Forum! In order to active those features, please log in your forum as current user and then come back and refresh this page.</div>';
	$('#inner').before(alertMsg);
<?php 
	}
	exit;
}


function fs_css()
{
	global $mybb;
	header("Content-type: text/css");
?>
#float_notification {
	position: fixed;
	display:none;
	bottom:0;
	left:0;
	height:200px;
	width:185px;
	padding:10px;
	margin:auto auto;
	overflow:hidden;
	border-top:1px solid #016BAE;
	border-right:1px solid #016BAE;
	background:url(./images/thead.png) repeat-x scroll left top #F1F1F1;
	background-size: 100% 25px;
	font-size:x-small;
	text-align:justify;
	z-index:1000;
	box-shadow:2px -2px 3px #999999;
	border-radius:0 8px 0 0;
	-moz-border-radius:0 8px 0 0;
	-webkit-border-radius:0 8px 0 0;
}
#close_float_note, #help_float_note {
	float:right;
	width:14px;
	height:14px;
	background:orange;
	color:#fff;
	text-align:center;
	margin:-6px -6px 0 0;
	padding:1px;
	font-weight:bold;
	cursor:pointer;
	font-size:12px;
	font-family:Verdana,Arial,sans-serif;
	z-index:1100;
	border-radius:4px;
	-moz-border-radius:4px;
	-webkit-border-radius:4px;
}
#help_float_note {
	background:#5BAFD3;
	margin:-6px 4px 0 0;
}
#fs_queries_count, #fs_mem_usage {
	float:right;
	text-align:center;
	color:red;
	font-size:large;
	margin-top:5px;
	width:90px;
	clear:both;
}
.textbox50 {
	border:1px solid #ccc;
	padding:1px;
	width:50px;
}
.selectbox60, .selectbox130 {
	border:1px solid #ccc;
	padding:1px;
}
.selectbox60 {
	width:60px;
}
.selectbox130 {
	width:130px;
}
.fs_tbl tr td {
	background:none;
	font-size:x-small;
	padding:2px 0 0 0;
	border:0;
}
#preview_iframe_holder {
	position:fixed;
	display:none;
	left:0;
	top:0;
	width:100%;
	border-bottom:1px solid #555;
	height:0px;
	background:#e5e5e5;
	box-shadow:0px 2px 3px #999;
	overflow: visible!important;
}

#preview_iframe_spinner {
	position: absolute;
	top: 0;
	right: 0;
	left: 0;
	background: rgba(0, 0, 0, 0.5);
	text-align: center;
	padding: 0;
	overflow: hidden;
	height: 0;
	box-sizing: border-box;
	display: none;
}

#preview_iframe_spinner img {
	margin: 25px;
}

.preview_handle, .preview_handle_transition {
	display:none;
	top:0;
	width:100px;
	padding:10px;
	margin:0 auto;
	background-color:#555555;
	color:#FFF;
	text-align:center;
	box-shadow:0px 2px 3px #999;
	border-radius:0 0 4px 4px;
	-moz-border-radius:0 0 4px 4px;
	-webkit-border-radius:0 0 4px 4px;
	transition: background-color 0.5s linear, color 0.5s linear;
	-moz-transition: background-color 0.5s linear, color 0.5s linear;    /* FF3.7+ */
	-o-transition: background-color 0.5s linear, color 0.5s linear;      /* Opera 10.5 */
	-webkit-transition: background-color 0.5s linear, color 0.5s linear; /* Saf3.2+, Chrome */
}
.preview_handle {
	cursor:pointer;
}
.preview_handle_transition {
	background-color: #ffffff;
	color:#333;
	transition: background-color 0.5s linear, color 0.5s linear;
	-moz-transition: background-color 0.5s linear, color 0.5s linear;    /* FF3.7+ */
	-o-transition: background-color 0.5s linear, color 0.5s linear;      /* Opera 10.5 */
	-webkit-transition: background-color 0.5s linear, color 0.5s linear; /* Saf3.2+, Chrome */
}
body {
	margin-bottom:50px !important;
}
.unselectable {
	-moz-user-select: -moz-none;
	-khtml-user-select: none;
	-webkit-user-select: none;
	user-select: none;
}

<?php 
	exit;
}

?>