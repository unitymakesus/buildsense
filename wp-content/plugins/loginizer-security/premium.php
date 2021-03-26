<?php

function loginizer_security_init(){
	
	global $loginizer;

	// TODO : Disable loginizer/loginizer.php
	// ATM, it will not load if the premium plugin is loaded
	
	// Load license
	if(!defined('SITEPAD')){
		
		$loginizer['license'] = get_option('loginizer_license');
		
		// Check for updates
		include_once('updater/plugin-update-checker.php');
		$loginizer_updater = Loginizer_PucFactory::buildUpdateChecker(LOGINIZER_API.'updates.php?version='.LOGINIZER_VERSION, LOGINIZER_FILE);
		
		// Add the license key to query arguments
		$loginizer_updater->addQueryArgFilter('loginizer_updater_filter_args');
		
		// Show the text to install the license key
		add_filter('puc_manual_final_check_link-loginizer-security', 'loginizer_updater_check_link', 10, 1);

		add_filter('plugin_row_meta', 'loginizer_plugin_row_links', 10, 2);
		
		// Update license details as well
		if(!empty($loginizer['license']) && (time() - @$loginizer['license']['last_update']) >= 86400){
			
			$resp = wp_remote_get(LOGINIZER_API.'license.php?license='.$loginizer['license']['license']);
			
			// Did we get a response ?
			if(is_array($resp)){
				
				$tosave = json_decode($resp['body'], true);
				
				// Is it the license ?
				if(!empty($tosave['license'])){
					$tosave['last_update'] = time();
					update_option('loginizer_license', $tosave);
				}
				
			}
			
		}
	}
	
	add_filter('loginizer_system_information', 'loginizer_premium_system_info', 10);
	add_filter('loginizer_pre_page_dashboard', 'loginizer_premium_page_dashboard', 10);

	// A way to remove the settings
	if(file_exists(LOGINIZER_DIR.'/reset_admin.txt')){
		update_option('loginizer_wp_admin', array());
	}
	
	// Are we to ban user emails ?
	if(!empty($loginizer['domains_blacklist']) && count($loginizer['domains_blacklist']) > 0){			
		add_filter('registration_errors', 'loginizer_domains_blacklist', 10, 3);
		add_filter('woocommerce_registration_errors', 'loginizer_domains_blacklist', 10, 3);
	}
	
	// Is email password less login enabled ?
	if(!empty($loginizer['email_pass_less']) && !defined('XMLRPC_REQUEST')){
		
		// Add a handler for the GUI Login
		add_filter('authenticate', 'loginizer_epl_wp_authenticate', 10002, 3);
		
		// Dont show password error
		add_filter('wp_login_errors', 'loginizer_epl_error_handler', 10000, 2);
		
		// Hide the password field
		add_action('login_enqueue_scripts', 'loginizer_epl_hide_pass');
		
	}
	
	// Are we to rename the login ?
	if(!empty($loginizer['login_slug'])){
		
		//$loginizer['login_slug'] = 'login';
		
		// Add the filters / actions
		add_filter('site_url', 'loginizer_rl_site_url', 10, 2);
		add_filter('network_site_url', 'loginizer_rl_site_url', 10, 2);
		add_filter('wp_redirect', 'loginizer_rl_wp_redirect', 10, 2);
		add_filter('register', 'loginizer_rl_register');
		add_action('wp_loaded', 'loginizer_rl_wp_loaded');
		
	}
	
	// Rename the WP-ADMIN folder
	if(!defined('SITEPAD') && !empty($loginizer['admin_slug'])){
		
		add_filter('admin_url', 'loginizer_admin_url', 10001, 3);
		add_action('set_auth_cookie', 'loginizer_admin_url_cookie');
		
		// For multisite
		if(lz_is_multisite()){
			add_filter('network_admin_url', 'loginizer_network_admin_url', 10001, 2);
		}
		
		if(!empty($loginizer['restrict_wp_admin']) && preg_match('/\/wp-admin/is', $_SERVER['REQUEST_URI'])){
			die(empty($loginizer['wp_admin_msg']) ? $loginizer['wp_admin_d_msg'] : $loginizer['wp_admin_msg']);
		}
		
	}
	
	// WP-Admin Test AJAX handler
	add_action('wp_ajax_loginizer_wp_admin', 'loginizer_wp_admin_ajax');
	
	// Are we to rename the xmlrpc ?
	if(!defined('SITEPAD') && !empty($loginizer['xmlrpc_slug']) && empty($loginizer['xmlrpc_disable'])){
		
		// Add the filters / actions
		add_action('wp_loaded', 'loginizer_xml_rename_wp_loaded');
		
	}

	// Are we to DISABLE the xmlrpc ?
	if(!empty($loginizer['xmlrpc_disable'])){
		
		// Add the filters / actions
		add_filter('xmlrpc_enabled', 'loginizer_xmlrpc_null');
		add_filter('bloginfo_url', 'loginizer_xmlrpc_remove_pingback_url', 10000, 2);
		add_action('wp_loaded', 'loginizer_xmlrpc_disable');
		
	}
	
	// Are we to disable pingbacks ?
	if(!empty($loginizer['pingbacks_disable'])){
		
		// Add the filters / actions
		add_filter('xmlrpc_methods', 'loginizer_pingbacks_disable');
		
	}
	
	//-----------------------------------
	// Add the captcha filters / actions
	//-----------------------------------
	
	if(!empty($loginizer['captcha_key']) || !empty($loginizer['captcha_no_google'])){
		
		add_action('login_init', 'loginizer_cap_session_key');
	
		// Is reCaptcha on for login ?
		if(!empty($loginizer['captcha_login']) && !defined('XMLRPC_REQUEST')){
			
			add_filter('authenticate', 'loginizer_cap_login_verify', 10000);
			add_action('login_form', 'loginizer_cap_form_login', 100);
			add_action('woocommerce_login_form', 'loginizer_cap_form_login', 100);
			
			// Need to make more room for login form
			if(empty($loginizer['captcha_remove_css'])){
				add_action('login_enqueue_scripts', 'loginizer_cap_login_form');
			}
			
		}

		// Is reCaptcha on for Lost Password utility ?
		if(!empty($loginizer['captcha_lostpass'])){			
			add_action('allow_password_reset', 'loginizer_cap_lostpass_verify', 10, 2);
			add_action('lostpassword_form', 'loginizer_cap_form_login', 100);
			add_filter('woocommerce_lostpassword_form', 'loginizer_cap_form_login');
		}

		// Is reCaptcha on for Reset Password utility ?
		if(!empty($loginizer['captcha_resetpass'])){
			add_filter('validate_password_reset', 'loginizer_cap_resetpass_verify', 10, 2);
			add_action('resetpass_form', 'loginizer_cap_reset_form', 99);
			add_filter('woocommerce_resetpassword_form', 'loginizer_cap_form_login');
		}

		// Is reCaptcha on for registration ?
		if(!empty($loginizer['captcha_register'])){			
			add_filter('registration_errors', 'loginizer_cap_register_verify', 10, 3);
			add_action('register_form', 'loginizer_cap_form_login', 100);
			
			// For BuddyPress
			add_filter('bp_signup_validate', 'loginizer_cap_register_verify_buddypress', 10, 3);
			add_action('bp_after_signup_profile_fields', 'loginizer_cap_form_login', 100);
			
			add_filter('woocommerce_before_checkout_process', 'loginizer_wc_before_checkout_process', 10);
			
			add_filter('woocommerce_register_form', 'loginizer_cap_form_login');
			add_filter('woocommerce_registration_errors', 'loginizer_cap_register_verify', 10, 3);
			
			if(!empty($loginizer['captcha_wc_checkout'])){
				add_action('woocommerce_checkout_order_review', 'loginizer_cap_form_ecommerce');
			}
		}
		
		// Are we to show Captcha for guests only ?
		if((is_user_logged_in() && empty($loginizer['captcha_user_hide'])) || !is_user_logged_in()){
		
			// Is reCaptcha on for comment utility ?
			if(!empty($loginizer['captcha_comment'])){
				add_filter('preprocess_comment', 'loginizer_cap_comment_verify');
				add_action('comment_form', 'loginizer_cap_comment_form');
			}
			
			// Is reCaptcha on for WooCommerce Logout utility ?
			if(!empty($loginizer['captcha_wc_checkout'])){
				add_action('woocommerce_after_checkout_validation', 'loginizer_wc_checkout_verify');
				add_action('woocommerce_checkout_order_review', 'loginizer_cap_form_ecommerce');
			}
		
		}
	
	}
	
	//-----------------
	// Two Factor Auth
	//-----------------
	
	if(!defined('SITEPAD') && (!empty($loginizer['2fa_app']) || !empty($loginizer['2fa_email']) || !empty($loginizer['2fa_sms']) || !empty($loginizer['question']))
		&& !defined('XMLRPC_REQUEST')){
		
		// After username and password check has been verified, are we to redirect ?
		add_filter('authenticate', 'loginizer_user_redirect', 10003, 3);
		
		// Shows the Question / 2fa field
		add_action('login_form_loginizer_security', 'loginizer_user_security');
		
		// Ajax handler
		add_action('wp_ajax_loginizer_ajax', 'loginizer_user_page_ajax');
		
		// Is the user logged in ?
		if(is_user_logged_in()){
			
			// Load user settings
			loginizer_load_user_settings($tfa_uid, $tfa_user, $tfa_settings, $tfa_current_pref);
			
			// If 2FA applicable as per role
			if(loginizer_is_2fa_applicable($tfa_user)){
		
				// Add to Settings menu on sites
				add_action('admin_menu', 'loginizer_user_menu');
			
				// Show the user the notification to set a 2FA
				$loginizer['loginizer_2fa_notice'] = get_user_meta($tfa_uid, 'loginizer_2fa_notice');
				
				// Are we to show the loginizer notification to set a 2FA
				if(empty($loginizer['loginizer_2fa_notice']) && 
					@$_COOKIE['loginizer_2fa_notice_'.$tfa_uid] != md5(wp_get_session_token()) && 
					(empty($tfa_current_pref) || $tfa_current_pref == 'none') &&
					lz_optget('page') != 'loginizer_user'
				){
				
					add_action('admin_notices', 'loginizer_2fa_notice');
				
				}
				
				// Are we to disable the notice forever ?
				if(isset($_GET['loginizer_2fa_notice']) && (int)$_GET['loginizer_2fa_notice'] == 0){
					update_user_meta($tfa_uid, 'loginizer_2fa_notice', time());
					die('DONE');
				}
				
				// Are we to disable the notice temporarily ?
				if(isset($_GET['loginizer_2fa_notice']) && (int)$_GET['loginizer_2fa_notice'] == 1){
					@setcookie('loginizer_2fa_notice_'.$tfa_uid, md5(wp_get_session_token()), time() + (3 * DAY_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN);
				}
			
			}
			
		}
		
	}
	
	// Checksum is enabled right i.e. its not disabled ?
	if(!defined('SITEPAD') && empty($loginizer['disable_checksum'])){
		
		// Create an action always
		add_action('loginizer_do_checksum', 'loginizer_checksums');
		
		// Difference in seconds since last time
		$diff = (time() - $loginizer['checksums_last_run']);
		
		// Has it crossed the time ?
		if(($diff / 86400) >= $loginizer['checksum_frequency']){
			//loginizer_checksums();
			wp_schedule_single_event(time(), 'loginizer_do_checksum');
		}
		
	}

}

function loginizer_premium_system_info(){
	
	global $loginizer;
	
	echo '
	<tr>			
		<th align="left" valign="top">'.__('Loginizer License', 'loginizer').'</th>
		<td align="left">
			'.(empty($loginizer['license']) ? '<span style="color:red">Unlicensed</span> &nbsp; &nbsp;' : '').' 
			<input type="text" name="lz_license" value="'.(empty($loginizer['license']) ? '' : $loginizer['license']['license']).'" size="30" placeholder="e.g. WXCSE-SFJJX-XXXXX-AAAAA-BBBBB" style="width:300px;" /> &nbsp; 
			<input name="save_lz" class="button button-primary" value="Update License" type="submit" />';
			
			if(!empty($loginizer['license'])){
				
				$expires = $loginizer['license']['expires'];
				$expires = substr($expires, 0, 4).'/'.substr($expires, 4, 2).'/'.substr($expires, 6);
				
				echo '<div style="margin-top:10px;">License Active : '.(empty($loginizer['license']['active']) ? '<span style="color:red">No</span>' : '<span style="color:green">Yes</span>').' &nbsp; &nbsp; &nbsp; 
				License Expires : '.($loginizer['license']['expires'] <= date('Ymd') ? '<span style="color:red">'.$expires.'</span>' : $expires).'
				</div>';
			}
		echo 
		'</td>
	</tr>';
	
}

function loginizer_premium_page_dashboard(){
	
	global $loginizer, $lz_error;

	// Is there a license key ?
	if(isset($_POST['save_lz'])){
	
		$license = lz_optpost('lz_license');
		
		// Check if its a valid license
		if(empty($license)){
			$lz_error['lic_invalid'] = __('The license key was not submitted', 'loginizer');
			return loginizer_page_dashboard_T();
		}
		
		$resp = wp_remote_get(LOGINIZER_API.'license.php?license='.$license, array('timeout' => 30));
		
		if(is_array($resp)){
			$json = json_decode($resp['body'], true);
			//print_r($json);
		}else{
		
			$lz_error['resp_invalid'] = __('The response was malformed<br>'.var_export($resp, true), 'loginizer');
			return loginizer_page_dashboard_T();
			
		}
		
		// Save the License
		if(empty($json['license'])){
		
			$lz_error['lic_invalid'] = __('The license key is invalid', 'loginizer');
			return loginizer_page_dashboard_T();
			
		}else{
			
			update_option('loginizer_license', $json);
			
			// Mark as saved
			$GLOBALS['lz_saved'] = true;
		}
		
	}
	
}

// Add our license key if ANY
function loginizer_updater_filter_args($queryArgs) {
	
	global $loginizer;
	
	if ( !empty($loginizer['license']['license']) ) {
		$queryArgs['license'] = $loginizer['license']['license'];
	}
	
	return $queryArgs;
}

// Handle the Check for update link and ask to install license key
function loginizer_updater_check_link($final_link){
	
	global $loginizer;
	
	if(empty($loginizer['license']['license'])){
		return '<a href="'.admin_url('admin.php?page=loginizer').'">Install License Key to Update</a>';
	}
	
	return $final_link;
}

// Change the Admin URL
function loginizer_admin_url($url, $path, $blog_id){
	
	global $loginizer;
	
	//echo $url."\n";echo $path."\n";
	$new = str_replace('wp-admin', $loginizer['admin_slug'], $url);
	
	//echo $new.'<br>';
	return $new;
}

function loginizer_network_admin_url($url, $path){
	
	global $loginizer;
	
	//echo $url.'<br>';echo $path.'<br>';
	$new = str_replace('wp-admin', $loginizer['admin_slug'], $url);
	
	//echo $new.'<br>';	
	return $new;
}

// Required to be able to Login
function loginizer_admin_url_cookie($auth_cookie, $expire = 0, $expiration = '', $user_id = '', $scheme = ''){
	
	global $loginizer;
	
	if($scheme == 'secure_auth' || is_ssl()){
		$auth_cookie_name = SECURE_AUTH_COOKIE;
		$secure = true;
	}else {
		$auth_cookie_name = AUTH_COOKIE;
		$secure = false;
	}
	
	setcookie($auth_cookie_name, $auth_cookie, $expire, SITECOOKIEPATH . $loginizer['admin_slug'], COOKIE_DOMAIN, $secure, true);
	
}

// Verifies if the token is valid and creates the user session
function loginizer_epl_verify(){
	
	global $loginizer;
	
	if(empty($_GET['uid']) || empty($_GET['lepltoken'])){
		return false;
	}
	
	$uid = (int) sanitize_key($_GET['uid']);
	$token = sanitize_key($_GET['lepltoken']);
	$action = 'loginizer_epl_'.$uid;
	
	$hash = get_user_meta($uid, $action, true);
	$expires = get_user_meta($uid, $action.'_expires', true);
	
	include_once(ABSPATH.'/'.$loginizer['wp-includes'].'/class-phpass.php');
	$wp_hasher = new PasswordHash(8, TRUE);
	$time = time();

	if(!$wp_hasher->CheckPassword($expires.$token, $hash) || $expires < $time){
		$token_error_msg = __('The token is invalid or has expired. Please request a new email', 'loginizer');
		// Throw an error
		return new WP_Error('token_invalid', $token_error_msg, 'loginizer_epl');
		
	}else{
		
		// Login the User
		wp_set_auth_cookie($uid);
		
		// Delete the meta
		delete_user_meta($uid, $action);
		delete_user_meta($uid, $action.'_expires');
		
		// Redirect and exit
		wp_redirect(admin_url());
		exit;
		
	}
	
	return false;
		
}

// Hides the password field for the password less email login
function loginizer_epl_hide_pass() { 
	?>
	<style type="text/css">
	label[for="user_pass"], .user-pass-wrap {
	display:none;
	}
	</style>
	<?php 
}

// Handles the error of the password not being there
function loginizer_epl_error_handler($errors, $redirect_to){
	
	//echo 'loginizer_epl_error_handler :';print_r($errors->errors);echo '<br>';
	
	// Remove the empty password error
	if(is_wp_error($errors)){
		$errors->remove('empty_password');
	}
	
	return $errors;
	
}

// Handles the verification of the username or email
function loginizer_epl_wp_authenticate($user, $username, $password){
	
	global $loginizer;
	
	//echo 'loginizer_epl_wp_authenticate : '; print_r($user).'<br>';
	
	if(is_wp_error($user)){
		
		// Ignore certain codes
		$ignore_codes = array('empty_username', 'empty_password');

		if(is_wp_error($user) && !in_array($user->get_error_code(), $ignore_codes)) {
			return $user;
		}
		
	}
	
	// Is it a login attempt
	$verified = loginizer_epl_verify();
	if(is_wp_error($verified)){
		return $verified;
	}
	
	if(empty($username) && empty($_POST)){
		return $user;
	}
	
	$email = NULL;
	
	// Is it an email address ?
	if(is_email($username) && email_exists($username)){
		$email = $username;
	}
	
	// Maybe its a username
	if(!is_email($username) && username_exists($username)){
		$user = get_user_by('login', $username);
		if($user){
			$email = $user->data->user_email;
		}
	}
	
	// Did you get any valid email ?
	if(empty($email)){
		$account_error_msg = __('The username or email you provided does not exist !', 'loginizer');
		return new WP_Error('invalid_account', $account_error_msg, 'loginizer_epl');
	}
	
	// Send the email
	$site_name = get_bloginfo('name');
	$login_url = loginizer_epl_login_url($email);
	
	$vars = array('email' => $email,
				'site_name' => $site_name,
				'site_url' => get_site_url(),
				'login_url' => $login_url);
				
	$subject = lz_lang_vars_name($loginizer['passwordless_sub'], $vars);
	$message = lz_lang_vars_name($loginizer['passwordless_msg'], $vars);

	//echo $subject.'<br><br>';echo $message;
	
	$headers = array();
	
	// Do we need to send the email as HTML ? 
	if(!empty($loginizer['passwordless_html'])){
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		
		if(!empty($loginizer['passwordless_msg_is_custom'])){
			$message = html_entity_decode($message);
		}else{
			$message = preg_replace("/\<br\s*\/\>/i", "<br/>", $message);
			$message = preg_replace('/(?<!<br\/>)\n/i', "<br/>\n", $message);
		}
	}

	$sent = wp_mail($email, $subject, $message, $headers);
	
	//echo $login_url;
	
	if(empty($sent)){
		$email_not_sent = __('There was a problem sending your email. Please try again or contact an admin.', 'loginizer');
		return new WP_Error('email_not_sent', $email_not_sent, 'loginizer_epl');
	}else{
		$loginizer['no_loginizer_logs'] = 1;
		$email_sent_msg = __('An email has been sent with the Login URL', 'loginizer');
		return new WP_Error('email_sent', $email_sent_msg, 'message');
	}
	
}


// Generate the URL for the 
function loginizer_epl_login_url($email){
	
	// Get the User ID
	$user = get_user_by('email', $email);
	$token = loginizer_epl_token($user->ID);
	
	// The current URL
	$url = wp_login_url().'?uid='.$user->ID.'&lepltoken='.$token;

	return $url;
	
}

// Creates a one time token
function loginizer_epl_token($uid = 0){
	
	global $loginizer;
	
	// Variables
	$time = time();
	$expires = ($time + 600);
	$action =  'loginizer_epl_'.$uid;

	include_once( ABSPATH . '/'.$loginizer['wp-includes'].'/class-phpass.php');
	$wp_hasher = new PasswordHash(8, TRUE);

	// Create the token with a random salt and the time
	$token  = wp_hash(wp_generate_password(20, false).$action.$time);

	// Create a hash of the token
	$stored_hash = $wp_hasher->HashPassword($expires.$token);
	
	// Store the hash and when it expires
	update_user_meta($uid, $action, $stored_hash);
	update_user_meta($uid, $action.'_expires', $expires);
	
	return $token;
	
}

// Send a 404
function loginizer_set_404(){
	
	global $wp_query;
	
	status_header(404);	
	$wp_query->set_404();
	
	if( (($template = get_404_template()) || ($template = get_index_template()))
		&& ($template = apply_filters('template_include', $template))
	){
		include($template);
	}
	
	die();
	
}

// Find the page being accessed
function loginizer_cur_page(){
	
	$blog_url = trailingslashit(get_bloginfo('url'));
	
	// Build the Current URL
	$url = (is_ssl() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	
	if(is_ssl() && preg_match('/^http\:/is', $blog_url)){
		$blog_url = substr_replace($blog_url, 's', 4, 0);
	}
	
	// The relative URL to the Blog URL
	$req = str_replace($blog_url, '', $url);
	$req = str_replace('index.php/', '', $req);
	
	// We dont need the args
	$parts = explode('?', $req, 2);
	$relative = basename($parts[0]);

	// Remove trailing slash
	$relative = rtrim($relative, '/');
	$tmp = explode('/', $relative, 2);
	$page = end($tmp);
	
	//echo 'Page : '.$page.'<br>';
	
	return $page;
	
}

// Converts the URL as per the one stored
function loginizer_rl_convert_url($link){
	
	global $loginizer;
	
	// If the login page is to be kept secret
	if(!empty($loginizer['rename_login_secret']) && loginizer_cur_page() !== $loginizer['login_slug'] && !is_user_logged_in()){
		return $link;
	}
	
	$result = $link;
	
	if(!empty($loginizer['login_slug']) && strpos($link, $loginizer['login_basename']) !== false){
		$result = str_replace($loginizer['login_basename'], $loginizer['login_slug'], $link);
	}
	
	if(!empty($loginizer['xmlrpc_slug']) && strpos($link, 'xmlrpc.php') !== false){
		$result = str_replace($loginizer['login_basename'], $loginizer['login_slug'], $link);
	}
	
	return $result;
}

function loginizer_rl_site_url($link){
	$result = loginizer_rl_convert_url($link);
	return $result;
}

function loginizer_rl_wp_redirect($link){
	$result = loginizer_rl_convert_url($link);
	return $result;
}

function loginizer_rl_register($link){
	$result = loginizer_rl_convert_url($link);
	return $result;
}
	
// Shows the Login correctly
function loginizer_rl_wp_loaded(){
	
	global $loginizer, $interim_login;
	
	$page = loginizer_cur_page();

	// Is it wp-login.php ?
	if ($page === $loginizer['login_basename']) {
		loginizer_set_404();
	}

	// Is it our SLUG ? If not then return
	if($page !== rtrim($loginizer['login_slug'], '/')){
		return false;
	}

	// We dont want a WP plugin caching this page
	@define('NO_CACHE', true);
	@define('WTC_IN_MINIFY', true);
	@define('WP_CACHE', false);

	// Prevent errors from defining constants again
	error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR);

	include ABSPATH.'/'.$loginizer['login_basename'];
	
	exit();
	
}
	
// Renames the XML-RPC functionality
function loginizer_xml_rename_wp_loaded(){
	
	global $loginizer;
	
	$page = loginizer_cur_page();
	
	// Is it xmlrpc.php ?
	if ($page === 'xmlrpc.php') {
		loginizer_set_404();
	}

	// Is it our SLUG ? If not then return
	if($page !== $loginizer['xmlrpc_slug']){
		return false;
	}

	// We dont want a WP plugin caching this page
	@define('NO_CACHE', true);
	@define('WTC_IN_MINIFY', true);
	@define('WP_CACHE', false);

	// Prevent errors from defining constants again
	error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR);

	include ABSPATH.'/xmlrpc.php';
	
	exit();
	
}
	
// Disables the XML-RPC functionality
function loginizer_xmlrpc_null(){
	return null;
}

// Disables the XML-RPC functionality
function loginizer_xmlrpc_disable(){
	
	global $loginizer;
	
	$page = loginizer_cur_page();
	
	// Is it xmlrpc.php ?
	if ($page === 'xmlrpc.php'){	
		echo 'XML-RPC is disabled';
		exit();
	}
	
}
	
// Disables the XML-RPC functionality
function loginizer_xmlrpc_remove_pingback_url($output, $show) {

	if($show == 'pingback_url'){
		$output = '';
	}

	return $output;
	
}

// Disable Pingbacks
function loginizer_pingbacks_disable($methods) {

	if(isset($methods['pingback.ping'])){
		unset($methods['pingback.ping']);
	}

	if(isset($methods['pingback.extensions.getPingbacks'])){
		unset($methods['pingback.extensions.getPingbacks']);
	}

	return $methods;

}

//========================
// Captcha Codes
//========================

// Adjusts the login form
function loginizer_cap_login_form(){
	?>
	<style type="text/css">
	#login {
	width: 350px !important;
	padding: 4% 0 0 !important;
	}
	</style>
	<?php
}

// Verify the login captcha is valid ?
function loginizer_cap_login_verify($user){
	
	if(!loginizer_cap_verify()){
		$captcha_fail_msg = __('The CAPTCHA verification failed. Please try again.', 'loginizer');
		return new WP_Error('loginizer_cap_login_error', $captcha_fail_msg, 'loginizer_cap');
	}
	
	return $user;

}

// Verify the lostpass captcha is valid ?
function loginizer_cap_lostpass_verify($res, $uid){

	if(!loginizer_cap_verify()){
		$captcha_fail_msg = __('The CAPTCHA verification failed. Please try again.', 'loginizer');
		return new WP_Error('loginizer_cap_lostpass_error', $captcha_fail_msg, 'loginizer_cap');
	}
	
	return $res;
	
}

// Verify the resetpass captcha is valid ?
function loginizer_cap_resetpass_verify($errors, $user){
	
	if(!loginizer_cap_verify()){
		$captcha_fail_msg = __('The CAPTCHA verification failed. Please try again.', 'loginizer');
		$errors->add('loginizer_resetpass_cap_error', $captcha_fail_msg, 'loginizer_cap');
	}
	
}

// Verify the register captcha is valid ?
function loginizer_cap_register_verify($errors, $username = '', $email = ''){
	
	if(!loginizer_cap_verify()){
		$captcha_fail_msg = __('The CAPTCHA verification failed. Please try again.', 'loginizer');
		$errors->add('loginizer_cap_register_error', $captcha_fail_msg, 'loginizer_cap');
	}
	
	return $errors;
	
}

// Verify the register captcha is valid ?
function loginizer_cap_register_verify_buddypress($errors, $username = '', $email = ''){
	
	global $bp;
	
	// $bp is for BuddyPress Registration it does not pass $errors
	if(!loginizer_cap_verify()){
		$captcha_fail_msg = __('The CAPTCHA verification failed. Please try again.', 'loginizer');
		
		// $bp is for BuddyPress
		$bp->signup->errors['signup_username'] = $captcha_fail_msg;
	}
	
	return $errors;
	
}

// Verify the register captcha is valid ?
function loginizer_cap_comment_verify($comment){
	
	if(!loginizer_cap_verify()){
		wp_die('The CAPTCHA verification failed. Please try again.', 200);
	}
	
	return $comment;
	
}

// Verify WooCommerce Checkout Orders
function loginizer_wc_checkout_verify(){
	
	global $loginizer;
	
	// Is the registration function verifying it ?
	if(!is_user_logged_in() 
		&& get_option('woocommerce_enable_signup_and_login_from_checkout', 'yes') == 'yes'
		&& !empty($loginizer['captcha_register'])){
			
		// So, no need of any more verification
	
	// Lets verify
	}elseif(!loginizer_cap_verify()){
		$captcha_fail_msg = __('The CAPTCHA verification failed. Please try again.', 'loginizer');
		wc_add_notice($captcha_fail_msg, 'error');
	}
}

// Reset password form passes $user, hence we need to manually write echo
function loginizer_cap_reset_form($user = false){
	loginizer_cap_form_login(false);
}

// For comment form pass false to echo the form
function loginizer_cap_comment_form($post_id = 0){
	echo '<br />';loginizer_cap_form_social(false);
}

// Converts numbers to words
function loginizer_cap_num_to_words( $number ) {
	$words = array(
		1	 => __( 'one', 'loginizer' ),
		2	 => __( 'two', 'loginizer' ),
		3	 => __( 'three', 'loginizer' ),
		4	 => __( 'four', 'loginizer' ),
		5	 => __( 'five', 'loginizer' ),
		6	 => __( 'six', 'loginizer' ),
		7	 => __( 'seven', 'loginizer' ),
		8	 => __( 'eight', 'loginizer' ),
		9	 => __( 'nine', 'loginizer' ),
		10	 => __( 'ten', 'loginizer' ),
		11	 => __( 'eleven', 'loginizer' ),
		12	 => __( 'twelve', 'loginizer' ),
		13	 => __( 'thirteen', 'loginizer' ),
		14	 => __( 'fourteen', 'loginizer' ),
		15	 => __( 'fifteen', 'loginizer' ),
		16	 => __( 'sixteen', 'loginizer' ),
		17	 => __( 'seventeen', 'loginizer' ),
		18	 => __( 'eighteen', 'loginizer' ),
		19	 => __( 'nineteen', 'loginizer' ),
		20	 => __( 'twenty', 'loginizer' ),
		30	 => __( 'thirty', 'loginizer' ),
		40	 => __( 'forty', 'loginizer' ),
		50	 => __( 'fifty', 'loginizer' ),
		60	 => __( 'sixty', 'loginizer' ),
		70	 => __( 'seventy', 'loginizer' ),
		80	 => __( 'eighty', 'loginizer' ),
		90	 => __( 'ninety', 'loginizer' )
	);

	if ( isset( $words[$number] ) )
		return $words[$number];
	else {
		$reverse = false;

		switch ( get_bloginfo( 'language' ) ) {
			case 'de-DE':
				$spacer = 'und';
				$reverse = true;
				break;

			case 'nl-NL':
				$spacer = 'en';
				$reverse = true;
				break;

			case 'ru-RU':
			case 'pl-PL':
			case 'en-EN':
			default:
				$spacer = ' ';
		}

		$first = (int) (substr( $number, 0, 1 ) * 10);
		$second = (int) substr( $number, -1 );

		return ($reverse === false ? $words[$first] . $spacer . $words[$second] : $words[$second] . $spacer . $words[$first]);
	}
}

// Encode the operation
function loginizer_cap_encode_op($string){
	return $string;
}

// Get the session key. If not there create one
function loginizer_cap_session_key(){
	
	if(isset($_COOKIE['lz_math_sess']) && preg_match('/[a-z0-9]/is', $_COOKIE['lz_math_sess']) && strlen($_COOKIE['lz_math_sess']) == 40){
		return $_COOKIE['lz_math_sess'];
	}
	
	// Generate the key
	$new_session_key = lz_RandomString(40);
	
	// Set the cookie
	if(@setcookie('lz_math_sess', $new_session_key, time() + (30 * DAY_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN)){
		// Set this to use first time
		$_COOKIE['lz_math_sess'] = $new_session_key;
	}
	
	return $new_session_key;
	
}

// Generate the Captcha field if its a Math Captcha
function loginizer_cap_phrase($form = 'default'){
	
	global $loginizer;
	
	$ops = array('add' => '+',
				'subtract' => '&#8722;',
				'multiply' => '&#215;',
				'divide' => '&#247;',
			);

	$input = '<input type="text" size="2" length="2" id="loginizer_cap_math" style="display: inline-block;	width: 60px!important; vertical-align: middle; margin-bottom: 0;" name="loginizer_cap_math" value="" aria-required="true"/>';

	if(empty($loginizer['captcha_add'])){
		unset($ops['add']);
	}
	
	if(empty($loginizer['captcha_subtract'])){
		unset($ops['subtract']);
	}
	
	if(empty($loginizer['captcha_multiply'])){
		unset($ops['multiply']);
	}
	
	if(empty($loginizer['captcha_divide'])){
		unset($ops['divide']);
	}

	// Randomly select an operation
	$rnd_op = array_rand($ops, 1);
	$number[3] = $ops[$rnd_op];

	// Select where to place empty input
	$rnd_input = mt_rand(0, 2);

	// Generate the numbers
	switch ($rnd_op){
		
		case 'add':
		
			if($rnd_input === 0){
				$number[0] = mt_rand(1, 10);
				$number[1] = mt_rand(1, 89);
			}elseif($rnd_input === 1) {
				$number[0] = mt_rand(1, 89);
				$number[1] = mt_rand(1, 10);
			}elseif($rnd_input === 2){
				$number[0] = mt_rand(1, 9);
				$number[1] = mt_rand(1, 10 - $number[0]);
			}

			$number[2] = $number[0] + $number[1];
			break;

		case 'subtract':
			if($rnd_input === 0){
				$number[0] = mt_rand(2, 10);
				$number[1] = mt_rand(1, $number[0] - 1);
			}elseif($rnd_input === 1){
				$number[0] = mt_rand(11, 99);
				$number[1] = mt_rand(1, 10);
			}elseif($rnd_input === 2){
				$number[0] = mt_rand(11, 99);
				$number[1] = mt_rand($number[0] - 10, $number[0] - 1);
			}

			$number[2] = $number[0] - $number[1];
			break;

		case 'multiply':
			if($rnd_input === 0){
				$number[0] = mt_rand(1, 10);
				$number[1] = mt_rand(1, 9);
			}elseif($rnd_input === 1){
				$number[0] = mt_rand(1, 9);
				$number[1] = mt_rand(1, 10);
			}elseif($rnd_input === 2){
				$number[0] = mt_rand(1, 10);
				$number[1] = ($number[0] > 5 ? 1 : ($number[0] === 4 && $number[0] === 5 ? mt_rand(1, 2 ) : ($number[0] === 3 ? mt_rand(1, 3 ) : ($number[0] === 2 ? mt_rand(1, 5 ) : mt_rand(1, 10 )))));
			}

			$number[2] = $number[0] * $number[1];
			break;

		case 'divide':
			$divide = array( 1 => 99, 2 => 49, 3 => 33, 4 => 24, 5 => 19, 6 => 16, 7 => 14, 8 => 12, 9 => 11, 10 => 9 );

			if($rnd_input === 0){
				$divide = array( 2 => array( 1, 2 ), 3 => array( 1, 3 ), 4 => array( 1, 2, 4 ), 5 => array( 1, 5 ), 6 => array( 1, 2, 3, 6 ), 7 => array( 1, 7 ), 8 => array( 1, 2, 4, 8 ), 9 => array( 1, 3, 9 ), 10 => array( 1, 2, 5, 10 ) );
				$number[0] = mt_rand(2, 10);
				$number[1] = $divide[$number[0]][mt_rand(0, count( $divide[$number[0]] ) - 1 )];
			}elseif($rnd_input === 1){
				$number[1] = mt_rand(1, 10);
				$number[0] = $number[1] * mt_rand(1, $divide[$number[1]]);
			}elseif($rnd_input === 2){
				$number[2] = mt_rand(1, 10 );
				$number[0] = $number[2] * mt_rand(1, $divide[$number[2]]);
				$number[1] = (int) ($number[0] / $number[2]);
			}

			if(! isset( $number[2] ) )
				$number[2] = (int) ($number[0] / $number[1]);

			break;
	}

	// Are we to display in words ?
	if(!empty($loginizer['captcha_words'])){
		if($rnd_input === 0){
			$number[1] = loginizer_cap_num_to_words( $number[1] );
			$number[2] = loginizer_cap_num_to_words( $number[2] );
		}elseif($rnd_input === 1){
			$number[0] = loginizer_cap_num_to_words( $number[0] );
			$number[2] = loginizer_cap_num_to_words( $number[2] );
		}elseif($rnd_input === 2){
			$number[0] = loginizer_cap_num_to_words( $number[0] );
			$number[1] = loginizer_cap_num_to_words( $number[1] );
		}
	}
	
	// Finally make the input field
	if(in_array( $form, array( 'default' ) ) ){
		
		// As per the position of the empty input
		if($rnd_input === 0 ){
			$return = $input . ' ' . $number[3] . ' ' . loginizer_cap_encode_op( $number[1] ) . ' = ' . loginizer_cap_encode_op( $number[2] );
		}elseif($rnd_input === 1 ){
			$return = loginizer_cap_encode_op( $number[0] ) . ' ' . $number[3] . ' ' . $input . ' = ' . loginizer_cap_encode_op( $number[2] );
		}elseif($rnd_input === 2 ){
			$return = loginizer_cap_encode_op( $number[0] ) . ' ' . $number[3] . ' ' . loginizer_cap_encode_op( $number[1] ) . ' = ' . $input;
		}
	}
	
	// Get the session ID
	$session_id = loginizer_cap_session_key();
	
	// Save the time
	set_transient('lz_math_cap_'.$session_id, sha1(AUTH_KEY . $number[$rnd_input] . $session_id, false), $loginizer['captcha_time']);
	
	// Save the value in the users cookie
	//loginizer_cap_cookie_set(sha1(AUTH_KEY . $number[$rnd_input] . $session_id, false));
	
	return $return;
}

// Captcha form for ecommerce
function loginizer_cap_form_ecommerce($return = false, $id = ''){
	return loginizer_cap_form($return, $id, 'ecommerce');
}

// Captcha form for login
function loginizer_cap_form_login($return = false, $id = ''){
	return loginizer_cap_form($return, $id, 'login');
}

// Captcha form for comments/social
function loginizer_cap_form_social($return = false, $id = ''){
	return loginizer_cap_form($return, $id, 'social');
}

// Shows the captcha

function loginizer_cap_form($return = false, $id = '', $page_type = 'login'){
	
	global $loginizer;
	
	// Math Captcha
	if(!empty($loginizer['captcha_no_google'])){
		
		// We generate it only once
		if(empty($GLOBALS['lz_captcha_no_google'])){		
			$GLOBALS['lz_captcha_no_google'] = $loginizer['captcha_text'].'<br>'.loginizer_cap_phrase().'<br><br>';		
		}
		
		// Store this value
		$field = $GLOBALS['lz_captcha_no_google'];
		
	}else{
	
		$field = '';
		$query_string = array();
		
		$captcha_type = (!empty($loginizer['captcha_type']) ? $loginizer['captcha_type'] : '');
		$site_key = $loginizer['captcha_key'];
		$theme = $loginizer['captcha_theme'];
		$size = $loginizer['captcha_size'];
		$no_js = $loginizer['captcha_no_js'];
		$captcha_ver = 2;
		$captcha_js_ver = '2.0';
		$invisible = 0;
		
		if($captcha_type == 'v3'){
			$invisible = 1;
			$captcha_ver = 3;
			$captcha_js_ver = '3.0';
			$do_multiple = 1;
			$lz_cap_div_class = 'lz-recaptcha-invisible-v3';
				
			if(!empty($site_key)){
				$query_string['render'] = $site_key;
			}
		}
		
		// For v2 invisible
		if($captcha_type == 'v2_invisible'){
			$invisible = 1;
			$do_multiple = 1;
			$size = 'invisible';
			$lz_cap_div_class = 'lz-recaptcha-invisible-v2';
			$query_string['render'] = 'explicit';
		}
		
		// Is this a first call ?
		if(!wp_script_is('loginizer_cap_script', 'registered')){
			
			$language = $loginizer['captcha_lang'];
			if(!empty($language)){
				$query_string['hl'] = $language;
			}
			
			// We need these variables in JS
			if(!empty($invisible)){
				$field .= '<script>
				var lz_cap_ver = "'.$captcha_ver.'";
				var lz_cap_sitekey = "'.$site_key.'";
				var lz_cap_div_class = "'.$lz_cap_div_class.'";
				var lz_cap_page_type = "'.$page_type.'";
				var lz_cap_invisible = "1";
				</script>';
			}
			
			wp_register_script('loginizer_cap_script', "https://www.google.com/recaptcha/api.js?".http_build_query($query_string), array('jquery'), $captcha_js_ver, true);
			
		// We need to load multiple times
		}else{
			$do_multiple = 1;
		}
		
		if(!empty($do_multiple)){
		
			if(!wp_script_is('loginizer_multi_cap_script', 'registered')){
				wp_register_script('loginizer_multi_cap_script', LOGINIZER_URL.'/multi-recaptcha.js', array('jquery'), $captcha_js_ver, true);	
			}
			wp_enqueue_script('loginizer_multi_cap_script');
		
		}
		
		wp_enqueue_script('loginizer_cap_script');
		
		// For v3 everything is done in javascript
		if(empty($invisible)){
		
			$field .= "<div ".(!empty($id) ? 'id="'.$id.'"' : '')." class='g-recaptcha lz-recaptcha' data-sitekey='$site_key' data-theme='$theme' data-size='$size'></div>";
			
			if($no_js == 1){
				
				$field .= "
<noscript>
	<div style='width: 302px; height: 352px;'>
		<div style='width: 302px; height: 352px; position: relative;'>
			<div style='width: 302px; height: 352px; position: absolute;'>
				<iframe src='https://www.google.com/recaptcha/api/fallback?k=$site_key' frameborder='0' scrolling='no' style='width: 302px; height:352px; border-style: none;'>
				</iframe>
			</div>
			<div style='width: 250px; height: 80px; position: absolute; border-style: none; bottom: 21px; left: 25px; margin: 0px; padding: 0px; right: 25px;'>
				<textarea name='g-recaptcha-response' class='g-recaptcha-response' style='width: 250px; height: 80px; border: 1px solid #c1c1c1; margin: 0px; padding: 0px; resize: none;' value=''>
				</textarea>
			</div>
		</div>
	</div>
</noscript>";

			}
			
			$field .= '<br>';
		
		}else{
			
			$field .= '<div class="'.$lz_cap_div_class.'"></div>';
			
			if($captcha_ver == 3){
				$field .= '<input type="hidden" name="g-recaptcha-response" class="lz-v3-input" value="">';
			}
		}
		
	
	}
	
	// Are we to return the code ?
	if($return){
		return $field;
	
	// Lets echo it
	}else{
		echo $field;
	}
}

// Verifies the Google Captcha	and is called by individual for verifiers	
function loginizer_cap_verify(){
	
	global $loginizer;
	
	// WooCommerce is calling this function as well. Hence Captcha fails
	if(isset($GLOBALS['called_loginizer_cap_verify'])){
		return $GLOBALS['called_loginizer_cap_verify'];
	}
	
	// Is the post set ?
	if(count($_POST) < 1){
		return true;
	}
	
	$GLOBALS['called_loginizer_cap_verify'] = true;
	
	// Math Captcha
	if(!empty($loginizer['captcha_no_google'])){
	
		$response = (int) (!empty($_POST['loginizer_cap_math']) ? $_POST['loginizer_cap_math'] : '');
		
		// Is the response valid ?
		if(!is_numeric($response) || empty($response)){
			$GLOBALS['called_loginizer_cap_verify'] = false;
			return false;
		}
	
		// Get the session ID
		$session_id = loginizer_cap_session_key();
		
		// Is the response valid ?
		if(empty($session_id)){
			$GLOBALS['called_loginizer_cap_verify'] = false;
			return false;
		}
		
		// Get the Value stored
		$captcha_value = get_transient('lz_math_cap_'.$session_id);
		
		// Do we have a stored value ?
		if(empty($captcha_value) || strlen($captcha_value) != 40){
			$GLOBALS['called_loginizer_cap_verify'] = false;
			return false;
		}
		
		// Is the value matching
		if($captcha_value != sha1(AUTH_KEY . $response . $session_id, false)){
			$GLOBALS['called_loginizer_cap_verify'] = false;
			return false;
		}
		
		return true;
	
	// Google Captcha
	}else{
	
		// If secret key is not there, return
		if(empty($loginizer['captcha_secret'])){
			return true;
		}
		
		$response = (!empty($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '');
		$ip = lz_getip();
		
		// Is the IP or response not there ?
		if(empty($response) || empty($ip)){
			$GLOBALS['called_loginizer_cap_verify'] = false;
			return false;
		}
		
		$url = 'https://www.google.com/recaptcha/api/siteverify';

		// Verify the post
		$req = wp_remote_post($url, array(
						'timeout' => 10, 
						'body' => array(
							'secret' => $loginizer['captcha_secret'],
							'response' => $response, 
							'remoteip' => $ip
						)
					)
				);
				
		

		// Was there an error posting ?
		if(is_wp_error($req)){		
			$GLOBALS['called_loginizer_cap_verify'] = false;
			return false;
		}
		
		// Process the post response
		$resp = wp_remote_retrieve_body($req);
			
		// Is the body valid
		if(empty($resp)){
			$GLOBALS['called_loginizer_cap_verify'] = false;
			return false;
		}
		
		$json = json_decode($resp, true);
		
		if(!empty($json['success'])){
			return true;
		}
	
	}
	
	// Couldnt verify
	$GLOBALS['called_loginizer_cap_verify'] = false;
	return false;

}

function loginizer_wc_before_checkout_process(){
	
	global $loginizer;
	
	// This is checkout page. If admin has disabled captcha on checkout
	// and the user is registering during checkout we have not displayed the captcha form so we should not verify the same
	if(empty($loginizer['captcha_wc_checkout'])){
		remove_filter('woocommerce_registration_errors', 'loginizer_cap_register_verify');
	}
}

//=========================================
// Registration Domain Blacklist
//=========================================

function loginizer_domains_blacklist($errors, $username, $email){
		
	global $wpdb, $loginizer, $lz_error;
	
	$domains = $loginizer['domains_blacklist'];
	$domains = is_array($domains) ? $domains : array();
	
	// Are you blacklisted ?
	foreach($domains as $domain_to_match){
		
		$domain_to_match = str_replace('*', '(.*?)', $domain_to_match);
		
		if(preg_match('/'.$domain_to_match.'$/is', $email)){
			$match_found = 1;
		}
		
	}
	
	// Did we get a match ?
	if(!empty($match_found)){
		$errors->add('loginizer_domains_blacklist_error', 'The domain of your email is banned from registering on this website', 'loginizer_domains_blacklist');
	}
	
	return $errors;
	
}

//=========================================
// 2 Factor Auth / Question based security
//=========================================

// Handle the users secondary login i.e. 2fa / question, etc.
function loginizer_user_redirect($user, $username, $password){
	
	global $loginizer;
	
	// Is the post set ?
	if(count($_POST) < 1){
		return $user;
	}
	
	//print_r($user);die();
	
	// Is it a valid user ?
	if(!is_a($user, 'WP_User')){
		return $user;
	}
	
	// The user has given correct details
	// Now does the user have any of our features enabled ?	
	$settings = get_user_meta($user->ID, 'loginizer_user_settings', true);
	//print_r($settings);die();
	
	// Is it applicable as per role
	if(!loginizer_is_2fa_applicable($user)){
		return $user;
	}
	
	if(loginizer_is_whitelisted_2fa()){
		return $user;
	}
	
	// Set the default return to the user only
	$ret = $user;
	
	// Is it a secondary question ?
	if(!empty($settings['pref']) && $settings['pref'] == 'question'){
		
		// Is there a question and answer
		if(!empty($settings['question']) && !empty($settings['answer'])){
			$save = 1;
		}
		
	}
	
	// Is it a 2fa via App ?
	if(!empty($settings['pref']) && $settings['pref'] == '2fa_app'){
		
		if(!empty($settings['app_enable'])){
			$save = 1;
		}
		
	}
	
	// Is it a 2fa via email ?
	if((!empty($settings['pref']) && $settings['pref'] == '2fa_email') 
		|| ((empty($settings['pref']) || @$settings['pref'] == 'none') && !empty($loginizer['2fa_email_force']))
	){
		
		// Generate a 6 digit code
		$otp = wp_rand(100000, 999999);
		$r['code'] = base64_encode($otp);
		
		// Email them
		$site_name = get_bloginfo('name');
		
		$first_name = get_user_meta($user->ID, 'first_name', true);
		$last_name = get_user_meta($user->ID, 'last_name', true);
		
		if(empty($first_name)){
			$first_name = $user->data->display_name;
		}
		
		$vars = array('email' => $user->data->user_email,
					'otp' => $otp,
					'site_name' => $site_name,
					'site_url' => get_site_url(),
					'display_name' => $user->data->display_name,
					'user_login' => $user->data->user_login,
					'first_name' => $first_name,
					'last_name' => $last_name);
					
		$subject = lz_lang_vars_name($loginizer['2fa_email_sub'], $vars);
		$message = lz_lang_vars_name($loginizer['2fa_email_msg'], $vars);
		
		//echo $user->data->user_email.'<br>'.$message;die();
		
		$sent = wp_mail($user->data->user_email, $subject, $message);
	
		if(empty($sent)){
			return new WP_Error('email_not_sent', 'There was a problem sending your email with the OTP. Please try again or contact an admin.', 'loginizer_2fa_email');
		}else{
			$save = 1;
		}
		
	}
	
	// Are we to create and save a token ?
	if(!empty($save)){
			
		// Are we to be remembered ?
		$r['rememberme'] = lz_optreq('rememberme');
		
		// Create a token
		$token = loginizer_user_token($user->ID, $r);
		
		// Form the URL
		$url = wp_login_url().'?action=loginizer_security&uid='.$user->ID.'&lutoken='.$token.(!empty($_REQUEST['redirect_to']) ? '&redirect_to='.urlencode($_REQUEST['redirect_to']) : '').(isset( $_REQUEST['interim-login'] ) ? '&interim-login=1' : '');
		
		// Lets redirect
		wp_safe_redirect($url);
		die();
		
	}
	
	return $ret;
	
}

// Creates a one time token
function loginizer_user_token($uid = 0, $r = array()){
	
	global $loginizer;
	
	// Variables
	$time = time();
	$expires = ($time + 600);
	$action =  'loginizer_user_token';

	include_once( ABSPATH.'/'.$loginizer['wp-includes'].'/class-phpass.php');
	$wp_hasher = new PasswordHash(8, TRUE);

	// Create the token with a random salt and the time
	$token  = wp_hash(wp_generate_password(20, false).$action.$time);

	// Create a hash of the token
	$r['stored_hash'] = $wp_hasher->HashPassword($expires.$token);
	$r['expires'] = $expires;
	
	// Store the hash and when it expires
	update_user_meta($uid, $action, $r);
	
	return $token;
	
}

// Process the secondary form i.e. question / 2fa, etc.
function loginizer_user_security(){
	
	global $loginizer, $lz, $lz_error, $interim_login;
	
	if(empty($_GET['uid']) || empty($_GET['lutoken'])){
		return false;
	}
	
	$uid = (int) sanitize_key($_GET['uid']);
	$token = sanitize_key($_GET['lutoken']);
	$action = 'loginizer_user_token';
	
	$meta = get_user_meta($uid, $action, true);
	$hash = @$meta['stored_hash'];
	$expires = @$meta['expires'];

	include_once(ABSPATH.'/'.$loginizer['wp-includes'].'/class-phpass.php');
	$wp_hasher = new PasswordHash(8, TRUE);
	$time = time();

	if(!$wp_hasher->CheckPassword($expires.$token, $hash) || $expires < $time){

		// Throw an error
		$lz['error'] = 'The token is invalid or has expired. Please provide your user details by clicking <a href="'.wp_login_url().'">here</a>';
		loginizer_user_security_form();
		
	}
	
	// Get the username
	$userdata = get_userdata($uid);
	$username = $userdata->data->user_login;
	
	// Load the settings
	$lz['settings'] = get_user_meta($uid, 'loginizer_user_settings', true);
	
	// If the user was just created and the settings is empty
	if(empty($lz['settings'])){
		$lz['settings'] = array();
	}
	
	if((empty($lz['settings']['pref']) || $lz['settings']['pref'] == 'none') && !empty($loginizer['2fa_email_force'])){
		$lz['settings']['pref'] = '2fa_email';
	}

	/* Make sure post was from this page */
	if(count($_POST) > 0 && !check_admin_referer('loginizer-enduser')){
		$lz['error'] = 'The form security was compromised !';	
		loginizer_user_security_form();
	}
	
	// Has the user reached max attempts ? 
	if(!loginizer_can_login()){
		$lz['error'] = $lz_error;
		loginizer_user_security_form();
	}
	
	$interim_login = isset( $_REQUEST['interim-login'] );
	
	// Process the post
	if(!empty($_POST['lus_submit'])){
	
		if(@$lz['settings']['pref'] == 'question'){
			
			// Is there an answer ?
			$answer = lz_optpost('lus_value');
			
			// Is the answer correct ?
			if($answer != @base64_decode($lz['settings']['answer'])){
				
				loginizer_login_failed($username.' | 2FA-Answer', 1);
				$lz['error'][] = __('The answer is wrong !', 'loginizer');
				$lz['error'][] = loginizer_retries_left();
				loginizer_user_security_form();
				
			// Login the user
			}else{
				
				$do_login = 1;
				
			}
			
		}
	
		if(@$lz['settings']['pref'] == '2fa_email'){
			
			// Is there an OTP ?
			$otp = lz_optpost('lus_value');
			
			// Is the answer correct ?
			if($otp != @base64_decode($meta['code'])){
				
				loginizer_login_failed($username.' | 2FA-Email', 1);
				$lz['error'][] = __('The OTP is wrong !', 'loginizer');
				$lz['error'][] = loginizer_retries_left();
				loginizer_user_security_form();
				
			// Login the user
			}else{
				
				$do_login = 1;
				
			}
			
		}
		
		// App based login
		if(@$lz['settings']['pref'] == '2fa_app'){
			
			// Is there an OTP ?
			$otp = lz_optpost('lus_value');
			
			$app2fa = loginizer_2fa_app($uid);
			
			// Is the answer correct ?
			if($otp != $app2fa['2fa_otp']){
				
				// Maybe its an Emergency OTP
				if(!@in_array($otp, $lz['settings']['2fa_emergency'])){
				
					loginizer_login_failed($username.' | 2FA-APP', 1);
					$lz['error'][] = __('The OTP is wrong !', 'loginizer');
					$lz['error'][] = loginizer_retries_left();
					loginizer_user_security_form();
				
				}else{
					
					// Remove the Emergency used and save the rest
					unset($lz['settings']['2fa_emergency'][$otp]);
					
					// Save it
					update_user_meta($uid, 'loginizer_user_settings', $lz['settings']);
					
					$do_login = 1;
					
				}
				
			// Login the user
			}else{
				
				$do_login = 1;
				
			}
			
		}
		
		// Are we to login ?
		if(!empty($do_login)){
			
			$remember_me = !empty($meta['rememberme']) ? true : false;
			
			// Login the User
			wp_set_auth_cookie($uid, $remember_me);
			
			// Delete the meta
			delete_user_meta($uid, $action);
			
			$redirect_to = !empty($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : admin_url();
			
			// Redirect and exit
			
			// Interim Login is used when session times out due to inactivity and login form is opened in a popup iframe
			if ( $interim_login ) {
				
				$message       = '<p class="message">' . __( 'You have logged in successfully12222.' ) . '</p>';
				$interim_login = 'success';
				login_header( '', $message );

				?>
				</div>
				<?php

				/** This action is documented in wp-login.php */
				do_action( 'login_footer' );
				
				?>
				
				</body></html>
				
				<?php
			}else{
				wp_safe_redirect($redirect_to);
			}
			exit;
			
		}
		
	}
	
	loginizer_user_security_form();
	
}

// Shows the secondary form i.e. question / 2fa, etc.
function loginizer_user_security_form(){
	
	global $loginizer, $lz;
	
	// Some plugins uses these actions
	do_action( 'login_init' );
	do_action( "login_form_login" );
	
	login_header();
	
	if(!empty($lz['error'])){
		
		if(!is_array($lz['error'])){
			$lz['error'] = array($lz['error']);
		}
		
		echo '<div id="login_error">';
		foreach($lz['error'] as $ek => $error_txt){
			echo wp_kses($error_txt, NULL).'<br />';
		}
		echo '</div>';
	}
	
	if(!empty($lz['settings'])){
	
		echo '<form action="" method="post" autocomplete="new-password">';
		wp_nonce_field('loginizer-enduser');
		
		// Are we to ask a question
		if(@$lz['settings']['pref'] == 'question'){
		
			echo '<p>
				'.$loginizer['2fa_msg']['otp_question'].' : <br /><br />
				<span title="" style="color:#444; font-size:16px">
					'.$lz['settings']['question'].'<br />
				</span>
			</p>
			<br />
			<p>
				<label title="">
					'.$loginizer['2fa_msg']['otp_answer'].'
					<input type="password" name="lus_value" id="lus_value" class="input" value="" size="20" autocomplete="new-password" />
				</label>
			</p>';
		
		}
		
		// Its a 2fa email
		if(@$lz['settings']['pref'] == '2fa_email'){
		
			echo '<p>'.$loginizer['2fa_msg']['otp_email'].'</p>
			<br>
			<p>
				<label title="">
					'.$loginizer['2fa_msg']['otp_field'].'
					<input type="password" name="lus_value" id="lus_value" class="input" value="" size="20" autocomplete="new-password" />
				</label>
			</p>';
		
		}
		
		// Its a 2fa app ?
		if(@$lz['settings']['pref'] == '2fa_app'){
		
			echo '<p>'.$loginizer['2fa_msg']['otp_app'].'</p>
			<br>
			<p>
				<label title="">
					'.$loginizer['2fa_msg']['otp_field'].'
					<input type="password" name="lus_value" id="lus_value" class="input" value="" size="20" autocomplete="new-password" />
				</label>
			</p>';
		
		}
		
		echo '<p class="submit">
			<input type="submit" id="lus_submit" name="lus_submit" class="button button-primary button-large" value="'.__('Log In', 'loginizer').'" />
		</p>
		</form>';
	
	}
	
	// Focus on the field
	login_footer('lus_value'); 
	exit();

}

// Show the 2fa Notice
function loginizer_2fa_notice(){
	
	echo '
<style>
.lz_button {
background-color: #4CAF50; /* Green */
border: none;
color: white;
padding: 8px 16px;
text-align: center;
text-decoration: none;
display: inline-block;
font-size: 16px;
margin: 4px 2px;
-webkit-transition-duration: 0.4s; /* Safari */
transition-duration: 0.4s;
cursor: pointer;
}

.lz_button:focus{
border: none;
color: white;
}

.lz_button1 {
color: white;
background-color: #4CAF50;
border:3px solid #4CAF50;
}

.lz_button1:hover {
box-shadow: 0 6px 8px 0 rgba(0,0,0,0.24), 0 9px 25px 0 rgba(0,0,0,0.19);
color: white;
border:3px solid #4CAF50;
}

.lz_button2 {
color: white;
background-color: #0085ba;
}

.lz_button2:hover {
box-shadow: 0 6px 8px 0 rgba(0,0,0,0.24), 0 9px 25px 0 rgba(0,0,0,0.19);
color: white;
}

.lz_button3 {
color: white;
background-color: #365899;
}

.lz_button3:hover {
box-shadow: 0 6px 8px 0 rgba(0,0,0,0.24), 0 9px 25px 0 rgba(0,0,0,0.19);
color: white;
}

.lz_button4 {
color: white;
background-color: rgb(66, 184, 221);
}

.lz_button4:hover {
box-shadow: 0 6px 8px 0 rgba(0,0,0,0.24), 0 9px 25px 0 rgba(0,0,0,0.19);
color: white;
}

.loginizer_2fa_notice-close{
float:right;
text-decoration:none;
margin: 5px 10px 0px 0px;
}

.loginizer_2fa_notice-close:hover{
color: red;
}
</style>	

<script>
jQuery(document).ready( function() {
	(function($) {
		$("#loginizer_2fa_notice .loginizer_2fa_notice-close").click(function(){
			var data;
			
			// Hide it
			$("#loginizer_2fa_notice").hide();
			
			// Save this preference
			$.post("'.admin_url('?loginizer_2fa_notice=0').'", data, function(response) {
				//alert(response);
			});
		});
		
		
		$("#loginizer_2fa_notice .lz_button2").click(function(){
			var data;
			
			// Hide it
			$("#loginizer_2fa_notice").hide();
			
			// Save this preference
			$.post("'.admin_url('?loginizer_2fa_notice=1').'", data, function(response) {
				//alert(response);
			});
		});
	})(jQuery);
});
</script>

<div class="notice notice-success" id="loginizer_2fa_notice" style="min-height:120px">
	<a class="loginizer_2fa_notice-close" href="javascript:" aria-label="Dismiss this Notice">
		<span class="dashicons dashicons-dismiss"></span> Dismiss Forever
	</a>
	<img src="'.LOGINIZER_URL.'/loginizer-200.png" style="float:left; margin:10px 20px 10px 10px" width="100" />
	<p style="font-size:16px">'.__('The site admin has enabled Two Factor Authentication features to secure your account. <br>For your safety, you must setup your login security preferences.', 'loginizer').'</p>
	<p>
		<a class="lz_button lz_button1" href="'.admin_url('?page=loginizer_user').'">'.__('Setup My Security Settings', 'loginizer').'</a>
		<a id="" class="lz_button lz_button2" href="javascript:void(0)">'.__('Remind me later', 'loginizer').'</a>
	</p>
</div>';

}

// Shows the user menu to all users
function loginizer_user_menu(){
	add_menu_page(__('My Loginizer Security Settings'), __('My Security'), 'read', 'loginizer_user', 'loginizer_user_page', '', 72);
}

// Generates the 2FA as seen in the APP
function loginizer_2fa_app_key($settings, $length = 6, $counter = 0){
			
	$key = $settings['2fa_key'];
	$type = (empty($settings['2fa_type']) ? 'totp' : $settings['2fa_type']);
	
	if($type == 'hotp'){
		$stored_in_db = 1;
		$counter = !empty($counter) ? $counter : $stored_in_db;
		$res = HOTP::generateByCounter($key, $counter);
	}else{		
		$time = !empty($counter) ? $counter : time();
		$res = HOTP::generateByTime($key, 30, $time);
	}
	
	return $res->toHotp($length);
	
}

// Returns the 2fa_app data. Is also used during ajax
function loginizer_2fa_app($uid = 0){
	
	// Include necessary stuff
	include_once(LOGINIZER_DIR.'/hotp.php');
	include_once(LOGINIZER_DIR.'/Base32.php');
	
	$uid = empty($uid) ? get_current_user_id() : $uid;
	
	// For 2fa_app we must be prepared
	$tmpkey = get_user_meta($uid, 'loginizer_user_2fa_tmpkey', true);	
	$settings['2fa_key'] = empty($tmpkey) ? '' : base64_decode($tmpkey);// Just decode it
	
	// We might need to create a 10 char secret KEY for 2fa App based
	if(empty($settings['2fa_key']) || isset($_REQUEST['reset_2fa_key'])){
			
		// Generate
		$settings['2fa_key'] = strtoupper(lz_RandomString(10));
		
		// Save the new one
		update_user_meta($uid, 'loginizer_user_2fa_tmpkey', base64_encode($settings['2fa_key']));
		
	}
	
	// Base32 Key
	$settings['2fa_key_32'] = Base32::encode($settings['2fa_key']);
	
	// The QR Code text
	$url = preg_replace('/^https?:\/\//', '', site_url());
	$site_name = get_bloginfo('name');
	$settings['2fa_qr'] = 'otpauth://'.(empty($settings['2fa_type']) ? 'totp' : $settings['2fa_type']).'/'.rawurlencode($url).':'.@$user->user_login.'?secret='.Base32::encode($settings['2fa_key']).'&issuer='.rawurlencode($site_name).'&counter=';
	
	// Time now
	$settings['2fa_server_time'] = get_date_from_gmt(gmdate('Y-m-d H:i:s'), 'Y-m-d H:i:s');
	
	// Current OTP
	$settings['2fa_otp'] = loginizer_2fa_app_key($settings);
	
	return $settings;

}
	
// Handles the users choice page POST
function loginizer_user_page_post(&$error = array()){
	
	global $loginizer, $loginizer_allowed;

	/* Make sure post was from this page */
	if(count($_POST) > 0){
		check_admin_referer('loginizer-user');
	}
	
	$uid = get_current_user_id();
	
	if(!empty($_POST['submit'])){
		
		// What has the user selected ?
		$Nsettings['pref'] = lz_optpost('loginizer_user_choice');
		
		if(empty($loginizer_allowed[$Nsettings['pref']])){
			$error['lz_not_allowed'] = __('You have submitted an invalid preference', 'loginizer');
			return 0;
		}
		
		// Process security question
		if($Nsettings['pref'] == 'question'){
			
			$Nsettings['question'] = lz_optpost('lz_question');
			$Nsettings['answer'] = lz_optpost('lz_answer');
			
			// Was there a question ?
			if(empty($Nsettings['question'])){
				$error['lz_no_question'] = __('No question was submitted', 'loginizer');
			}
			
			// Question too long ?
			if(strlen($Nsettings['question']) > 255){
				$error['lz_question_long'] = __('The question is too long', 'loginizer');
			}
			
			// Was there an answer ?
			if(empty($Nsettings['answer'])){
				$error['lz_no_answer'] = __('No answer was submitted', 'loginizer');
			}
			
			// Question too long ?
			if(strlen($Nsettings['answer']) > 255){
				$error['lz_answer_long'] = __('The answer is too long', 'loginizer');
			}
			
			if(!empty($error)){
				return 0;
			}
			
			// Hash the answer
			$Nsettings['answer'] = base64_encode($Nsettings['answer']);
			
		}
		
		// Process 2fa via Email
		if($Nsettings['pref'] == '2fa_email'){
			// Actually nothing to store !
		}
		
		// Process 2fa via App
		if($Nsettings['pref'] == '2fa_app'){
			
			// Enable APP
			$Nsettings['app_enable'] = (int) lz_optpost('lz_app_enable');
			
			// Any one time passwords ?
			$emergency = lz_optpost('lz_2fa_emergency');
			
			// Is there any Emergency OTP
			if(!empty($emergency)){
				
				$emergency = explode(',', $emergency);
				
				// Loop through and correct
				foreach($emergency as $ek => $ev){
					
					$orig = $ev;
					
					$ev = (int) $ev;
					
					$_emergency[$ev] = $ev;
					
					if(strlen($ev) != 6){
						$incorrect[] = $orig;
					}
					
				}
				
				if(!empty($incorrect)){
					$error['lz_emergency'] = __('The emergency code(s) are incorrect', 'loginizer').' : '.implode(', ', $incorrect);
				}
				
				$Nsettings['2fa_emergency'] = $_emergency;
				
			}
			
			if(!empty($error)){
				return 0;
			}
			
		}
		
		// Lets save the settings
		update_user_meta($uid, 'loginizer_user_settings', $Nsettings);
		
		return 1;
		
	}

}

// Loginizer 2FA User settings loader
function loginizer_load_user_settings(&$uid, &$user, &$settings, &$current_pref){
	
	$uid = get_current_user_id();
	$user = wp_get_current_user();//print_r($user);	
	$settings = get_user_meta($uid, 'loginizer_user_settings', true);
	$settings = empty($settings) ? array() : $settings;
	
	$current_pref = @$settings['pref'];
	$current_pref = empty($current_pref) ? '' : $current_pref;
	
}

function loginizer_is_whitelisted_2fa(){
	
	global $wpdb, $loginizer, $lz_error;
	
	$whitelist = $loginizer['2fa_whitelist'];
			
	foreach($whitelist as $k => $v){
		
		// Is the IP in the blacklist ?
		if(inet_ptoi($v['start']) <= inet_ptoi($loginizer['current_ip']) && inet_ptoi($loginizer['current_ip']) <= inet_ptoi($v['end'])){
			$result = 1;
			break;
		}
		
		// Is it in a wider range ?
		if(inet_ptoi($v['start']) >= 0 && inet_ptoi($v['end']) < 0){
			
			// Since the end of the RANGE (i.e. current IP range) is beyond the +ve value of inet_ptoi, 
			// if the current IP is <= than the start of the range, it is within the range
			// OR
			// if the current IP is <= than the end of the range, it is within the range
			if(inet_ptoi($v['start']) <= inet_ptoi($loginizer['current_ip'])
				|| inet_ptoi($loginizer['current_ip']) <= inet_ptoi($v['end'])){				
				$result = 1;
				break;
			}
			
		}
		
	}
		
	// You are whitelisted
	if(!empty($result)){
		return true;
	}
	
	return false;
	
}

// If 2FA is ON and there are roles, then is 2FA applicable to the user
function loginizer_is_2fa_applicable($user = array()){
	
	global $loginizer;
	
	// If roles is empty then its applicable to all
	if(empty($loginizer['2fa_roles'])){
		return true;
	}
	
	// Are there any roles we need to check
	if(!empty($loginizer['2fa_roles'])){
		
		foreach($loginizer['2fa_roles'] as $role => $v){
			if(in_array($role, $user->roles)){
				return true;
			}
		}
		
	}
	
	return false;
	
}

// The settings page shown to users
function loginizer_user_page(){
	
	global $loginizer, $loginizer_allowed;
	
	$loginizer_allowed = array();
	$loginizer_allowed['none'] = 1;
	if(!empty($loginizer['2fa_app'])){ $loginizer_allowed['2fa_app'] = 1; }
	if(!empty($loginizer['2fa_email'])){ $loginizer_allowed['2fa_email'] = 1; }
	if(!empty($loginizer['2fa_sms'])){ $loginizer_allowed['2fa_sms'] = 1; }
	if(!empty($loginizer['question'])){ $loginizer_allowed['question'] = 1; }
	
	//------------------
	// Process the form
	//------------------
	$error = array();
	$saved = loginizer_user_page_post($error);
	
	//------------------
	// Load Settings
	//------------------
	loginizer_load_user_settings($uid, $user, $settings, $current_pref);
	
	$app2fa = loginizer_2fa_app();
	
	//------------------
	// Show the Page
	//------------------
	
	echo '<h2>'.__('Loginizer Security Settings').'</h2>';

	if(!empty($saved)){
		echo '<div class="updated notice is-dismissible"><p><strong>'.__('Settings saved.').'</strong></p></div>';
	}
	
	if(!empty($error)){
		lz_report_error($error);
	}
	
	echo '<p class="">'.__('These are your personal security and login settings and will not affect other users.').'</p>';
	
	if (current_user_can('manage_options')) { 
		echo '<p><a href="https://wordpress.org/plugins/loginizer/faq/">'._e('You should also bookmark the FAQs, which explain how to de-activate the plugin even if you cannot log in.').'</a></p>';
	}
	
	wp_enqueue_script('jquery-qrcode', LOGINIZER_URL.'/jquery.qrcode.min.js', array('jquery'), '0.12.0');
	
	// Give the user the drop down to choose the settings
	echo 'Choose Preference : <form method="post" action="">
	'.wp_nonce_field('loginizer-user').'
	<select name="loginizer_user_choice" id="loginizer_user_choice" onchange="loginizer_pref_handle();">
		<option value="none" '.($current_pref == 'none' ? 'selected="selected"' : '').'>None (Not Recommended !)</option>
		'.(empty($loginizer['2fa_app']) ? '' : '<option value="2fa_app" '.($current_pref == '2fa_app' ? 'selected="selected"' : '').'>2fa : Google Authenticator, Authy, etc</option>').'
		'.(empty($loginizer['2fa_email']) ? '' : '<option value="2fa_email" '.($current_pref == '2fa_email' || ((empty($current_pref) || $current_pref == 'none') && !empty($loginizer['2fa_email_force'])) ? 'selected="selected"' : '').'>2fa : Email Auth Code</option>').'
		'.(empty($loginizer['2fa_sms']) ? '' : '<option value="2fa_sms" '.($current_pref == '2fa_sms' ? 'selected="selected"' : '').'>2fa : SMS Auth Code</option>').'
		'.(empty($loginizer['question']) ? '' : '<option value="question" '.($current_pref == 'question' ? 'selected="selected"' : '').'>Solve Security Question</option>').'
	</select>
	
<script>

var loginizer_nonce = "'.wp_create_nonce('loginizer_ajax').'";
	
// Handle on change
function loginizer_pref_handle(){
	(function($) {
		
		// Get the value
		var current = $("#loginizer_user_choice").val();
		$(".loginizer_upd").each(function(){
			if($(this).attr("id") == "loginizer_"+current){
				$(this).show();
			}else{
				$(this).hide();
			}
		});
		
		// Are we to show the QR Code ?
		if(current == "2fa_app"){			
			loginizer_2fa_app_load();
		}
		
	})(jQuery);	
};

// Show the QR Code and stuff
function loginizer_2fa_app_load(reset){
	
	reset = reset || 0;
	
	// Refresh OTP
	if(reset == 2){
		
		// Remove existing QRCode
		jQuery("#loginizer_2fa_app_qr").html("");
		
		var data = new Object();
		data["action"] = "loginizer_ajax";
		data["nonce"]	= loginizer_nonce;
		
		// AJAX and on success function
		jQuery.post(ajaxurl, data, function(response){
			jQuery("#loginizer_2fa_app_time").html(response["2fa_server_time"]);
			jQuery("#loginizer_2fa_app_key").val(response["2fa_key"]);
			jQuery("#loginizer_2fa_app_key_32").val(response["2fa_key_32"]);
			jQuery("#loginizer_2fa_app_otp").html(response["2fa_otp"]);
			jQuery("#loginizer_2fa_app_qr").attr("data-qrcode", response["2fa_qr"]);
		});
		
	}
	
	// Reset code
	if(reset == 1){
		
		var confirmed = confirm("Warning: If you reset the secret key you will have to update your apps with the new one. Are you sure you want to continue ?");
		
		if(confirmed){
		
			// Remove existing QRCode
			jQuery("#loginizer_2fa_app_qr").html("");
			
			// Data to Post
			var data = new Object();
			data["action"] = "loginizer_ajax";
			data["nonce"]	= loginizer_nonce;
			data["reset_2fa_key"]	= 1;
			
			// AJAX and on success function
			jQuery.post(ajaxurl, data, function(response){
				jQuery("#loginizer_2fa_app_time").html(response["2fa_server_time"]);
				jQuery("#loginizer_2fa_app_key").val(response["2fa_key"]);
				jQuery("#loginizer_2fa_app_key_32").val(response["2fa_key_32"]);
				jQuery("#loginizer_2fa_app_otp").html(response["2fa_otp"]);
				jQuery("#loginizer_2fa_app_qr").attr("data-qrcode", response["2fa_qr"]);
			});
			
		}else{
			return;
		}
	
	}
	
	var qrtext = jQuery("#loginizer_2fa_app_qr").attr("data-qrcode");
	jQuery("#loginizer_2fa_app_qr").qrcode({"text" : qrtext});
	
	return;
	
};
	
// Onload stuff
jQuery(document).ready(function(){
	loginizer_pref_handle();
});
	
</script>

<style>
.loginizer_upd{
	display: none;
}
</style>
	
	<br />
	
	<div id="loginizer_2fa_app" class="loginizer_upd">

		<h2>'.__('App based Two Factor Auth Code Settings').'</h2>
		
		<p>
			'.__('<b>NOTE :</b> Generating two-factor codes depends upon your web-server and your device agreeing upon the time.', 'loginizer').' <br>
			'.__('The current UTC time according to this server when this page loaded', 'loginizer').': <b id="loginizer_2fa_app_time">'.$app2fa['2fa_server_time'].'</b>
		</p>
		
		<table border="0" cellpadding="8" cellspacing="1" width="500">
			<tr>
				<td width="50%"><b>Enable :</b></td>
				<td><input type="checkbox" value="1" name="lz_app_enable" '.lz_POSTchecked('lz_app_enable', (empty($settings['app_enable']) ? false : true)).' /></td>
			</tr>
			<tr>
			<tr>
				<td>
					<b>Secret Key :</b><br>
					<a href="javascript:loginizer_2fa_app_load(1)">Reset Secret Key</a>
				</td>
				<td><input type="text" name="lz_2fa_key" id="loginizer_2fa_app_key" value="'.$app2fa['2fa_key'].'" disabled="disabled" /></td>
			</tr>
			<tr>
				<td><b>Secret Key (Base32) :</b><br>
				Used by Google Authenticator, Authy, etc.</td>
				<td><input type="text" name="lz_2fa_key_32" id="loginizer_2fa_app_key_32" value="'.$app2fa['2fa_key_32'].'" disabled="disabled" /></td>
			</tr>
			<tr>
				<td>
					<b>'.__('One Time Emergency Codes', 'loginizer').' :</b><br>
					'.__('(Optional) You can specify 6 digit emergency codes seperated by a comma. Each can be used only once. You can specify upto 10.', 'loginizer').'
				</td>
				<td><input type="text" name="lz_2fa_emergency" value="'.lz_POSTval('lz_2fa_emergency', (empty($settings['2fa_emergency']) ? '' : implode(', ', $settings['2fa_emergency']) ) ).'" placeholder="e.g. 124667, 976493, 644335" /></td>
			</tr>
		</table>
		<br><br>
		
		<table border="0" cellpadding="8" cellspacing="1" width="500" style="background: #FFF" align="center">
			<tr>
				<td colspan="2"><b>'.__('If you enable app based Two Factor Auth, then verify that your application is showing the same One Time Password (OTP) as shown on this page before you log out.', 'loginizer').'</b>
			</tr>
			<tr>
				<td>
					<b>'.__('Current OTP', 'loginizer').' :</b><br>
					<a href="javascript:loginizer_2fa_app_load(2)">'.__('Refresh', 'loginizer').'</a>
				</td>
				<td><h1 id="loginizer_2fa_app_otp">'.loginizer_2fa_app_key($app2fa).'</h1></td>
			</tr>
			<tr>
				<td width="30%" valign="top"><b>QR Code :</b></td>
				<td><div id="loginizer_2fa_app_qr" data-qrcode="'.esc_attr($app2fa['2fa_qr']).'"></div></td>
			</tr>
		</table>
	</div>
	
	<div id="loginizer_question" class="loginizer_upd">

		<h2>'.__('Security Question Settings').'</h2>
		
		<p>'.__('A secondary question set by you will be asked on a successful login', 'loginizer').'</p>
		
		<table border="0" cellpadding="8" cellspacing="1">
			<tr>
				<td><b>Question :</b></td>
				<td><input type="text" name="lz_question" value="'.(empty($settings['question']) ? '' : $settings['question']).'" size="40"  placeholder="e.g. The name of my pet is ?" /></td>
			</tr>
			<tr>
				<td><b>Answer :</b><br />Is case sensitive</td>
				<td><input type="text" name="lz_answer" value="'.(empty($settings['answer']) ? '' : base64_decode($settings['answer'])).'" placeholder="e.g. tommy" /></td>
			</tr>
		</table>
	
	</div>
	
	<div id="loginizer_2fa_email" class="loginizer_upd">

		<h2>'.__('Email Two Factor Auth Code Settings').'</h2>
		
		<p>
			'.__('A One Time Password (OTP) will be asked on a successful login. The OTP will be emailed to your email address', 'loginizer').' : <br>
			<h2>'.$user->data->user_email.'</h2>
		</p>
				
	</div>
	
	<div id="loginizer_2fa_sms" class="loginizer_upd">

		<h2>'.__('SMS Two Factor Auth Code Settings').'</h2>
		
		<p>
			'.__('A One Time Password (OTP) will be asked on a successful login. The OTP will be sent via SMS to your mobile.', 'loginizer').' <br>
		</p>
		
		<table border="0" cellpadding="8" cellspacing="1">
			<tr>
				<td><b>'.__('Mobile Number', 'loginizer').' :</b></td>
				<td><input type="text" name="lz_mobile" value="'.(empty($settings['mobile']) ? '' : base64_decode($settings['mobile'])).'" placeholder="e.g. +18557852145" /></td>
			</tr>
		</table>
				
	</div>';
	
	submit_button();
	
	echo '</form>';
	
}


// AJAX callback function used to generate new secret
function loginizer_user_page_ajax(){
	
	global $user_id;

	// Some AJAX security
	check_ajax_referer('loginizer_ajax', 'nonce');
	
	header('Content-Type: application/json');
	
	// Data
	$result = loginizer_2fa_app();
	
	// Echo JSON and die
	echo json_encode($result);
	die(); 
	
}


// AJAX callback function used to TEST the new SLUG
function loginizer_wp_admin_ajax(){
	
	global $user_id;

	// Some AJAX security
	check_ajax_referer('loginizer_admin_ajax', 'nonce');
	 
	if(!current_user_can('manage_options')){
		wp_die('Sorry, but you do not have permissions to change settings.');
	}
	
	header('Content-Type: application/json');
	
	// Data
	$result['result'] = '1';
	
	// Echo JSON and die
	echo json_encode($result);
	die(); 
	
}

// Do the checksum of the core
function loginizer_checksums(){
	
	global $loginizer, $loginizer_allowed;
	
	// Update the time
	update_option('loginizer_checksums_last_run', time());
	
	// Get the locale and version
	$locale = get_locale();
	$version = $GLOBALS['wp_version'];
	//echo $version.' - '.$locale;
	
	// Load the checksums
	$resp = wp_remote_get('https://api.wordpress.org/core/checksums/1.0/?version='.$version.'&locale='.$locale,
					array('timeout' => 10));
	//lz_print($resp);
	
	if(!is_array($resp)){
		return false;
	}
	
	$checksums = json_decode($resp['body'], true);//lz_print($checksums);
	$checksums = $checksums['checksums'];
	//lz_print($checksums);
	
	// WP-content could be renamed !
	$wp_content = basename(dirname(dirname(dirname(__FILE__))));
	
	// Loop through and check
	foreach($checksums as $ck => $md5){
		
		// Do not check for plugins and themes as some user might not have updated files
		if(substr($ck, 0, 18) == 'wp-content/plugins'){
			continue;
		}
		
		if(substr($ck, 0, 17) == 'wp-content/themes'){
			continue;
		}
		
		if(substr($ck, 0, 10) == 'wp-content'){
			$ck = substr_replace($ck, $wp_content, 0, 10);
			//echo $ck."\n";
		}
		
		$path = lz_cleanpath(ABSPATH.'/'.$ck);
		
		// Skip checksum for the file that does not exists, it is possible that the theme/plugin is deleted by the user
		if(!file_exists($path) && preg_match('#/(themes|plugins)#is', $path)){
			continue;
		}
		
		$cur_md5 = @md5_file($path);
		if($cur_md5 != $md5){
			$diffed[$ck]['cur_md5'] = $cur_md5;
			$diffed[$ck]['md5'] = $md5;
		}
		
	}
	
	//lz_print($diffed);
	
	// Store the diffed ones
	update_option('loginizer_checksums_diff', $diffed);
	
	// Load any ignored files
	$ignores = get_option('loginizer_checksums_ignore');
	
	// Create a final diff list to email the admin
	if(is_array($ignores)){
		
		foreach($ignores as $ck => $path){
			unset($diffed[$path]);
		}
		
	}
	
	// Send an email to the admin, IF we are to email
	if(is_array($diffed) && count($diffed) > 0 && empty($loginizer['no_checksum_email'])){
		
		// Send the email
		$site_name = get_bloginfo('name');
		$email = lz_is_multisite() ? get_site_option('admin_email') : get_option('admin_email');	
		$subject = __("File Checksum Mismatch - $site_name");
		$message = "Hi,

Loginizer has just completed checking the MD5 checksums of your WordPress site :
$site_name - ".get_site_url()."

The following files have been found that do not match the MD5 checksums as per your version : 
";

		foreach($diffed as $path => $val){
			$message .= "Path: ".$path." MD5: ".$val['md5']."
Found MD5: ".$val['cur_md5']."

";
		}

		$message .= "
It is recommended you check this ASAP and download the files again to replace them.
If you are aware of modifications made to the above files, please update the Ignored files list in Loginizer.

Regards,
$site_name";

		//echo $message;

		$sent = wp_mail($email, $subject, $message);
		
	}
	
}


// Is the Username blacklisted
function loginizer_user_blacklisted($username){
		
	global $wpdb, $loginizer, $lz_error;
	
	$username_blacklist = $loginizer['username_blacklist'];
	$username_blacklist = is_array($username_blacklist) ? $username_blacklist : array();
	
	// Are you blacklisted ?
	foreach($username_blacklist as $user_to_match){
		
		$user_to_match = str_replace('*', '(.*?)', $user_to_match);
		
		if(preg_match('/^'.$user_to_match.'$/is', $username)){
			$match_found = 1;
		}
		
	}
	
	// Did we get a match ?
	if(empty($match_found)){
		return false;
	}
	
	// Lets make sure there is no username in the database by that name
	$user_search = get_user_by('login', $username);
		
	// If not found then search by email
	if(!empty($user_search)){
		return false;
	}
		
	$blacklist = get_option('loginizer_blacklist');
	$newid = ( empty($blacklist) ? 0 : max(array_keys($blacklist)) ) + 1;
	
	// Add to the blacklist
	$blacklist[$newid]['start'] = lz_getip();
	$blacklist[$newid]['end'] = lz_getip();
	$blacklist[$newid]['time'] = time();
	
	// Update the database
	update_option('loginizer_blacklist', $blacklist);
	
	// Reload
	$loginizer['blacklist'] = get_option('loginizer_blacklist');
	
	// Show the error
	$lz_error['user_blacklisted'] = __('This username has been blacklisted, and so have you been blacklisted !', 'loginizer');
	
	return true;
}

function loginizer_plugin_row_links($pluginMeta, $pluginFile){
	
	global $loginizer;
	
	$isRelevant = $pluginFile == 'loginizer-security/loginizer-security.php';

	if (!empty($isRelevant) && current_user_can('update_plugins')){
		
		// Show the Renew License link if the license is expired
		if(!empty($loginizer['license']['expires']) && ($loginizer['license']['expires'] <= date('Ymd'))){
		
			$linkUrl = 'https://www.softaculous.com/clients?ca=loginizer_buy&plan='.$loginizer['license']['plan'].'&license='.$loginizer['license']['license'];

			$linkText = __('Renew License', 'loginizer');
			
			$pluginMeta[] = sprintf('<a href="%s" target="_blank" style="color:red;">%s</a>', esc_attr($linkUrl), $linkText);
			
		}
	}
	
	return $pluginMeta;
}
