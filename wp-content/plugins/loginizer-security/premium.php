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
		$loginizer_updater = PucFactory::buildUpdateChecker(LOGINIZER_API.'updates.php?version='.LOGINIZER_VERSION, LOGINIZER_FILE);
		
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
	
	// Email to Login
	$options = get_option('loginizer_epl');
	$loginizer['pl_d_sub'] = 'Login at $site_name';
	$loginizer['pl_d_msg'] = 'Hi,

A login request was submitted for your account $email at :
$site_name - $site_url

Login at $site_name by visiting this url : 
$login_url

If you have not requested for the Login URL, please ignore this email.

Regards,
$site_name';
	$loginizer['email_pass_less'] = empty($options['email_pass_less']) ? 0 : $options['email_pass_less'];
	$loginizer['passwordless_sub'] = empty($options['passwordless_sub']) ? $loginizer['pl_d_sub'] : $options['passwordless_sub'];
	$loginizer['passwordless_msg'] = empty($options['passwordless_msg']) ? $loginizer['pl_d_msg'] : $options['passwordless_msg'];
	
	// For SitePad its always on
	if(defined('SITEPAD')){
		$loginizer['email_pass_less'] = 1;
	}
	
	// Captcha
	$options = get_option('loginizer_captcha');
	$loginizer['captcha_type'] = empty($options['captcha_type']) ? '' : $options['captcha_type'];
	$loginizer['captcha_key'] = empty($options['captcha_key']) ? '' : $options['captcha_key'];
	$loginizer['captcha_secret'] = empty($options['captcha_secret']) ? '' : $options['captcha_secret'];
	$loginizer['captcha_theme'] = empty($options['captcha_theme']) ? 'light' : $options['captcha_theme'];
	$loginizer['captcha_size'] = empty($options['captcha_size']) ? 'normal' : $options['captcha_size'];
	$loginizer['captcha_lang'] = empty($options['captcha_lang']) ? '' : $options['captcha_lang'];
	$loginizer['captcha_user_hide'] = !isset($options['captcha_user_hide']) ? 0 : $options['captcha_user_hide'];
	$loginizer['captcha_no_css_login'] = !isset($options['captcha_no_css_login']) ? 0 : $options['captcha_no_css_login'];
	$loginizer['captcha_no_js'] = 1;
	$loginizer['captcha_login'] = !isset($options['captcha_login']) ? 1 : $options['captcha_login'];
	$loginizer['captcha_lostpass'] = !isset($options['captcha_lostpass']) ? 1 : $options['captcha_lostpass'];
	$loginizer['captcha_resetpass'] = !isset($options['captcha_resetpass']) ? 1 : $options['captcha_resetpass'];
	$loginizer['captcha_register'] = !isset($options['captcha_register']) ? 1 : $options['captcha_register'];
	$loginizer['captcha_comment'] = !isset($options['captcha_comment']) ? 1 : $options['captcha_comment'];
	$loginizer['captcha_wc_checkout'] = !isset($options['captcha_wc_checkout']) ? 1 : $options['captcha_wc_checkout'];
	
	$loginizer['captcha_no_google'] =  !isset($options['captcha_no_google']) ? 0 : $options['captcha_no_google'];
	$loginizer['captcha_text'] =  empty($options['captcha_text']) ? __('Math Captcha', 'loginizer') : $options['captcha_text'];
	$loginizer['captcha_time'] =  empty($options['captcha_time']) ? 300 : $options['captcha_time'];
	$loginizer['captcha_words'] =  !isset($options['captcha_words']) ? 0 : $options['captcha_words'];
	$loginizer['captcha_add'] =  !isset($options['captcha_add']) ? 1 : $options['captcha_add'];
	$loginizer['captcha_subtract'] =  !isset($options['captcha_subtract']) ? 1 : $options['captcha_subtract'];
	$loginizer['captcha_multiply'] =  !isset($options['captcha_multiply']) ? 0 : $options['captcha_multiply'];
	$loginizer['captcha_divide'] =  !isset($options['captcha_divide']) ? 0 : $options['captcha_divide'];
	
	// 2fa/question
	$options = get_option('loginizer_2fa');
	$loginizer['2fa_app'] = !isset($options['2fa_app']) ? 0 : $options['2fa_app'];
	$loginizer['2fa_email'] = !isset($options['2fa_email']) ? 0 : $options['2fa_email'];
	$loginizer['2fa_email_force'] = !isset($options['2fa_email_force']) ? 0 : $options['2fa_email_force'];
	$loginizer['2fa_sms'] = !isset($options['2fa_sms']) ? 0 : $options['2fa_sms'];
	$loginizer['question'] = !isset($options['question']) ? 0 : $options['question'];
	$loginizer['2fa_default'] = empty($options['2fa_default']) ? 'question' : $options['2fa_default'];
	$loginizer['2fa_roles'] = empty($options['2fa_roles']) ? array() : $options['2fa_roles'];
	
	// Security Settings
	$options = get_option('loginizer_security');
	$loginizer['login_slug'] = empty($options['login_slug']) ? '' : $options['login_slug'];
	$loginizer['rename_login_secret'] = empty($options['rename_login_secret']) ? '' : $options['rename_login_secret'];
	$loginizer['xmlrpc_slug'] = empty($options['xmlrpc_slug']) ? '' : $options['xmlrpc_slug'];
	$loginizer['xmlrpc_disable'] = empty($options['xmlrpc_disable']) ? '' : $options['xmlrpc_disable'];// Disable XML-RPC
	$loginizer['pingbacks_disable'] = empty($options['pingbacks_disable']) ? '' : $options['pingbacks_disable'];// Disable Pingbacks
	
	// A way to remove the settings
	if(file_exists(LOGINIZER_DIR.'/reset_admin.txt')){
		update_option('loginizer_wp_admin', array());
	}
	
	// Admin Slug Settings
	$options = get_option('loginizer_wp_admin');
	$loginizer['admin_slug'] = empty($options['admin_slug']) ? '' : $options['admin_slug'];
	$loginizer['restrict_wp_admin'] = empty($options['restrict_wp_admin']) ? '' : $options['restrict_wp_admin'];
	$loginizer['wp_admin_msg'] = empty($options['wp_admin_msg']) ? '' : $options['wp_admin_msg'];
	
	// Checksum Settings
	$options = get_option('loginizer_checksums');
	$loginizer['disable_checksum'] = empty($options['disable_checksum']) ? '' : $options['disable_checksum'];
	$loginizer['checksum_time'] = empty($options['checksum_time']) ? '' : $options['checksum_time'];
	$loginizer['checksum_frequency'] = empty($options['checksum_frequency']) ? 7 : $options['checksum_frequency'];
	$loginizer['no_checksum_email'] = empty($options['no_checksum_email']) ? '' : $options['no_checksum_email'];
	$loginizer['checksums_last_run'] = get_option('loginizer_checksums_last_run');
	
	// Auto Blacklist Usernames
	$loginizer['username_blacklist'] = get_option('loginizer_username_blacklist');
	
	$loginizer['domains_blacklist'] = get_option('loginizer_domains_blacklist');

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
	
	$loginizer['wp_admin_d_msg'] = 'LZ : Not allowed via WP-ADMIN. Please access over the new Admin URL';
	
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
			
			if(!empty($loginizer['captcha_wc_checkout'])){
				add_filter('woocommerce_register_form', 'loginizer_cap_form_login');
				add_filter('woocommerce_registration_errors', 'loginizer_cap_register_verify', 10, 3);
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
		
		// Throw an error
		return new WP_Error('token_invalid', 'The token is invalid or has expired. Please request a new email', 'loginizer_epl');
		
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
		return new WP_Error('invalid_account', 'The username or email you provided does not exist !', 'loginizer_epl');
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

	$sent = wp_mail($email, $subject, $message);
	
	//echo $login_url;
	
	if(empty($sent)){
		return new WP_Error('email_not_sent', 'There was a problem sending your email. Please try again or contact an admin.', 'loginizer_epl');
	}else{
		$loginizer['no_loginizer_logs'] = 1;
		return new WP_Error('email_sent', 'An email has been sent with the Login URL', 'message');
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
	
	global $loginizer;
	
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
		return new WP_Error('loginizer_cap_login_error', 'The CAPTCHA verification failed. Please try again.', 'loginizer_cap');
	}
	
	return $user;

}

// Verify the lostpass captcha is valid ?
function loginizer_cap_lostpass_verify($res, $uid){

	if(!loginizer_cap_verify()){
		return new WP_Error('loginizer_cap_lostpass_error', 'The CAPTCHA verification failed. Please try again.', 'loginizer_cap');
	}
	
	return $res;
	
}

// Verify the resetpass captcha is valid ?
function loginizer_cap_resetpass_verify($errors, $user){
	
	if(!loginizer_cap_verify()){
		$errors->add('loginizer_resetpass_cap_error', 'The CAPTCHA verification failed. Please try again.', 'loginizer_cap');
	}
	
}

// Verify the register captcha is valid ?
function loginizer_cap_register_verify($errors, $username, $email){
	
	if(!loginizer_cap_verify()){
		$errors->add('loginizer_cap_register_error', 'The CAPTCHA verification failed. Please try again.', 'loginizer_cap');
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
		wc_add_notice('The CAPTCHA verification failed. Please try again.', 'error');
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
	
	$ops = array(	'add' => '+',
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
		$subject = __("OTP : Login at $site_name");
		$message = __("Hi,

A login request was submitted for your account ".$user->data->user_email." at :
$site_name - ".get_site_url()."

Please use the following One Time password (OTP) to login : 
$otp

Note : The OTP expires after 10 minutes.

If you haven't requested for the OTP, please ignore this email.

Regards,
$site_name");

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
		$url = wp_login_url().'?action=loginizer_security&uid='.$user->ID.'&lutoken='.$token.(!empty($_REQUEST['redirect_to']) ? '&redirect_to='.urlencode($_REQUEST['redirect_to']) : '');
		
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
	
	global $loginizer, $lz;
	
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
	
	// Process the post
	if(!empty($_POST['lus_submit'])){
	
		if(@$lz['settings']['pref'] == 'question'){
			
			// Is there an answer ?
			$answer = lz_optpost('lus_value');
			
			// Is the answer correct ?
			if($answer != @base64_decode($lz['settings']['answer'])){
				
				$lz['error'] = 'The answer is wrong !';
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
				
				$lz['error'] = 'The OTP is wrong !';
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
				
					$lz['error'] = 'The OTP is wrong !';
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
			
			// Login the User
			wp_set_auth_cookie($uid);
			
			// Delete the meta
			delete_user_meta($uid, $action);
			
			$redirect_to = !empty($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : admin_url();
			$remember_me = !empty($meta['rememberme']) ? true : false;
			
			// Redirect and exit
			wp_safe_redirect($redirect_to);
			exit;
			
		}
		
	}
	
	loginizer_user_security_form();
	
}

// Shows the secondary form i.e. question / 2fa, etc.
function loginizer_user_security_form(){
	
	global $loginizer, $lz;
	
	login_header();
	
	if(!empty($lz['error'])){
		echo '<div id="login_error">'.wp_kses($lz['error'], NULL).'</div>';
	}
	
	if(!empty($lz['settings'])){
	
		echo '<form action="" method="post" autocomplete="off">';
		wp_nonce_field('loginizer-enduser');
		
		// Are we to ask a question
		if(@$lz['settings']['pref'] == 'question'){
		
			echo '<p>
				Please answer your security question : <br /><br />
				<span title="" style="color:#444; font-size:16px">
					'.$lz['settings']['question'].'<br />
				</span>
			</p>
			<br />
			<p>
				<label title="">
					'.__('Your Answer').'
					<input type="password" name="lus_value" id="lus_value" class="input" value="" size="20" />
				</label>
			</p>';
		
		}
		
		// Its a 2fa email
		if(@$lz['settings']['pref'] == '2fa_email'){
		
			echo '<p>Please enter the OTP emailed to you</p>
			<br>
			<p>
				<label title="">
					'.__('One Time Password').'
					<input type="password" name="lus_value" id="lus_value" class="input" value="" size="20" />
				</label>
			</p>';
		
		}
		
		// Its a 2fa app ?
		if(@$lz['settings']['pref'] == '2fa_app'){
		
			echo '<p>Please enter the OTP as seen in your App</p>
			<br>
			<p>
				<label title="">
					'.__('One Time Password').'
					<input type="text" name="lus_value" id="lus_value" class="input" value="" size="20" />
				</label>
			</p>';
		
		}
		
		echo '<p class="submit">
			<input type="submit" id="lus_submit" name="lus_submit" class="button button-primary button-large" value="Log In" />
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
	<p style="font-size:16px">The site admin has enabled Two Factor Authentication features to secure your account. <br>For your safety, you must setup your login security preferences.</p>
	<p>
		<a class="lz_button lz_button1" href="'.admin_url('?page=loginizer_user').'">Setup My Security Settings</a>
		<a id="" class="lz_button lz_button2" href="javascript:void(0)">Remind me later</a>
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
			$error['lz_not_allowed'] = 'You have submitted an invalid preference';
			return 0;
		}
		
		// Process security question
		if($Nsettings['pref'] == 'question'){
			
			$Nsettings['question'] = lz_optpost('lz_question');
			$Nsettings['answer'] = lz_optpost('lz_answer');
			
			// Was there a question ?
			if(empty($Nsettings['question'])){
				$error['lz_no_question'] = 'No question was submitted';
			}
			
			// Question too long ?
			if(strlen($Nsettings['question']) > 255){
				$error['lz_question_long'] = 'The question is too long';
			}
			
			// Was there an answer ?
			if(empty($Nsettings['answer'])){
				$error['lz_no_answer'] = 'No answer was submitted';
			}
			
			// Question too long ?
			if(strlen($Nsettings['answer']) > 255){
				$error['lz_answer_long'] = 'The answer is too long';
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
					$error['lz_emergency'] = 'The emergency code(s) are incorrect : '.implode(', ', $incorrect);
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
			<b>NOTE :</b> Generating two-factor codes depends upon your web-server and your device agreeing upon the time. <br>
			The current UTC time according to this server when this page loaded: <b id="loginizer_2fa_app_time">'.$app2fa['2fa_server_time'].'</b>
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
					<b>One Time Emergency Codes :</b><br>
					(Optional) You can specify 6 digit emergency codes seperated by a comma. Each can be used only once. You can specify upto 10.
				</td>
				<td><input type="text" name="lz_2fa_emergency" value="'.lz_POSTval('lz_2fa_emergency', (empty($settings['2fa_emergency']) ? '' : implode(', ', $settings['2fa_emergency']) ) ).'" placeholder="e.g. 124667, 976493, 644335" /></td>
			</tr>
		</table>
		<br><br>
		
		<table border="0" cellpadding="8" cellspacing="1" width="500" style="background: #FFF" align="center">
			<tr>
				<td colspan="2"><b>If you enable app based Two Factor Auth, then verify that your application is showing the same One Time Password (OTP) as shown on this page before you log out.</b>
			</tr>
			<tr>
				<td>
					<b>Current OTP :</b><br>
					<a href="javascript:loginizer_2fa_app_load(2)">Refresh</a>
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
		
		<p>A secondary question set by you will be asked on a successful login</p>
		
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
			A One Time Password (OTP) will be asked on a successful login. The OTP will be emailed to your email address : <br>
			<h2>'.$user->data->user_email.'</h2>
		</p>
				
	</div>
	
	<div id="loginizer_2fa_sms" class="loginizer_upd">

		<h2>'.__('SMS Two Factor Auth Code Settings').'</h2>
		
		<p>
			A One Time Password (OTP) will be asked on a successful login. The OTP will be sent via SMS to your mobile. <br>
		</p>
		
		<table border="0" cellpadding="8" cellspacing="1">
			<tr>
				<td><b>Mobile Number :</b></td>
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
	$lz_error['user_blacklisted'] = 'This username has been blacklisted, and so have you been blacklisted !';
	
	return true;
}

//---------------------
// Admin Menu Pages
//---------------------



// Loginizer - reCaptcha Page
function loginizer_page_recaptcha(){
	
	global $loginizer, $lz_error, $lz_env;
	 
	if(!current_user_can('manage_options')){
		wp_die('Sorry, but you do not have permissions to change settings.');
	}

	/* Make sure post was from this page */
	if(count($_POST) > 0){
		check_admin_referer('loginizer-options');
	}
	
	// Themes
	$lz_env['theme']['light'] = 'Light';
	$lz_env['theme']['dark'] = 'Dark';
	
	// Langs
	$lz_env['lang'][''] = 'Auto Detect';
	$lz_env['lang']['ar'] = 'Arabic';
	$lz_env['lang']['bg'] = 'Bulgarian';
	$lz_env['lang']['ca'] = 'Catalan';
	$lz_env['lang']['zh-CN'] = 'Chinese (Simplified)';
	$lz_env['lang']['zh-TW'] = 'Chinese (Traditional)';
	$lz_env['lang']['hr'] = 'Croatian';
	$lz_env['lang']['cs'] = 'Czech';
	$lz_env['lang']['da'] = 'Danish';
	$lz_env['lang']['nl'] = 'Dutch';
	$lz_env['lang']['en-GB'] = 'English (UK)';
	$lz_env['lang']['en'] = 'English (US)';
	$lz_env['lang']['fil'] = 'Filipino';
	$lz_env['lang']['fi'] = 'Finnish';
	$lz_env['lang']['fr'] = 'French';
	$lz_env['lang']['fr-CA'] = 'French (Canadian)';
	$lz_env['lang']['de'] = 'German';
	$lz_env['lang']['de-AT'] = 'German (Austria)';
	$lz_env['lang']['de-CH'] = 'German (Switzerland)';
	$lz_env['lang']['el'] = 'Greek';
	$lz_env['lang']['iw'] = 'Hebrew';
	$lz_env['lang']['hi'] = 'Hindi';
	$lz_env['lang']['hu'] = 'Hungarain';
	$lz_env['lang']['id'] = 'Indonesian';
	$lz_env['lang']['it'] = 'Italian';
	$lz_env['lang']['ja'] = 'Japanese';
	$lz_env['lang']['ko'] = 'Korean';
	$lz_env['lang']['lv'] = 'Latvian';
	$lz_env['lang']['lt'] = 'Lithuanian';
	$lz_env['lang']['no'] = 'Norwegian';
	$lz_env['lang']['fa'] = 'Persian';
	$lz_env['lang']['pl'] = 'Polish';
	$lz_env['lang']['pt'] = 'Portuguese';
	$lz_env['lang']['pt-BR'] = 'Portuguese (Brazil)';
	$lz_env['lang']['pt-PT'] = 'Portuguese (Portugal)';
	$lz_env['lang']['ro'] = 'Romanian';
	$lz_env['lang']['ru'] = 'Russian';
	$lz_env['lang']['sr'] = 'Serbian';
	$lz_env['lang']['sk'] = 'Slovak';
	$lz_env['lang']['sl'] = 'Slovenian';
	$lz_env['lang']['es'] = 'Spanish';
	$lz_env['lang']['es-419'] = 'Spanish (Latin America)';
	$lz_env['lang']['sv'] = 'Swedish';
	$lz_env['lang']['th'] = 'Thai';
	$lz_env['lang']['tr'] = 'Turkish';
	$lz_env['lang']['uk'] = 'Ukrainian';
	$lz_env['lang']['vi'] = 'Vietnamese';
	
	// Sizes
	$lz_env['size']['normal'] = 'Normal';
	$lz_env['size']['compact'] = 'Compact';
	
	if(isset($_POST['save_lz'])){
		
		// Google Captcha
		$option['captcha_type'] = lz_optpost('captcha_type');
		$option['captcha_key'] = lz_optpost('captcha_key');
		$option['captcha_secret'] = lz_optpost('captcha_secret');
		$option['captcha_theme'] = lz_optpost('captcha_theme');
		$option['captcha_size'] = lz_optpost('captcha_size');
		$option['captcha_lang'] = lz_optpost('captcha_lang');
		
		// No Google Captcha
		$option['captcha_text'] = lz_optpost('captcha_text');
		$option['captcha_time'] = (int) lz_optpost('captcha_time');
		$option['captcha_words'] = (int) lz_optpost('captcha_words');
		$option['captcha_add'] = (int) lz_optpost('captcha_add');
		$option['captcha_subtract'] = (int) lz_optpost('captcha_subtract');
		$option['captcha_multiply'] = (int) lz_optpost('captcha_multiply');
		$option['captcha_divide'] = (int) lz_optpost('captcha_divide');
		
		// Checkboxes
		$option['captcha_user_hide'] = (int) lz_optpost('captcha_user_hide');
		$option['captcha_no_css_login'] = (int) lz_optpost('captcha_no_css_login');
		$option['captcha_login'] = (int) lz_optpost('captcha_login');
		$option['captcha_lostpass'] = (int) lz_optpost('captcha_lostpass');
		$option['captcha_resetpass'] = (int) lz_optpost('captcha_resetpass');
		$option['captcha_register'] = (int) lz_optpost('captcha_register');
		$option['captcha_comment'] = (int) lz_optpost('captcha_comment');
		$option['captcha_wc_checkout'] = (int) lz_optpost('captcha_wc_checkout');
		
		// Are we to use Math Captcha ?
		if(isset($_POST['captcha_no_google'])){
			
			$option['captcha_no_google'] = 1;
			
			// Make the checks
			if(strlen($option['captcha_text']) < 1){
				$lz_error['captcha_text'] = __('The Captcha key was not submitted', 'loginizer');
			}
			
		}else{
		
			// Make the checks
			if(strlen($option['captcha_key']) < 32 || strlen($option['captcha_key']) > 50){
				$lz_error['captcha_key'] = __('The reCAPTCHA key is invalid', 'loginizer');
			}
			
			// Is secret valid ?
			if(strlen($option['captcha_secret']) < 32 || strlen($option['captcha_secret']) > 50){
				$lz_error['captcha_secret'] = __('The reCAPTCHA secret is invalid', 'loginizer');
			}
			
			// Is theme valid ?
			if(empty($lz_env['theme'][$option['captcha_theme']])){
				$lz_error['captcha_theme'] = __('The reCAPTCHA theme is invalid', 'loginizer');
			}
			
			// Is size valid ?
			if(empty($lz_env['size'][$option['captcha_size']])){
				$lz_error['captcha_size'] = __('The reCAPTCHA size is invalid', 'loginizer');
			}
			
			// Is lang valid ?
			if(empty($lz_env['lang'][$option['captcha_lang']])){
				$lz_error['captcha_lang'] = __('The reCAPTCHA language is invalid', 'loginizer');
			}
			
		}
		
		// Is there an error ?
		if(!empty($lz_error)){
			return loginizer_page_recaptcha_T();
		}
		
		// Save the options
		update_option('loginizer_captcha', $option);
		
		// Mark as saved
		$GLOBALS['lz_saved'] = true;
		
	}
	
	// Clear this
	if(isset($_POST['clear_captcha_lz'])){
		
		// Save the options
		update_option('loginizer_captcha', '');
		
		// Mark as saved
		$GLOBALS['lz_cleared'] = true;
		
	}
	
	// Call the theme
	loginizer_page_recaptcha_T();
	
}

// Loginizer - reCaptcha Page Theme
function loginizer_page_recaptcha_T(){
	
	global $loginizer, $lz_error, $lz_env;
	
	// Universal header
	loginizer_page_header('reCAPTCHA Settings');
	
	// Saved ?
	if(!empty($GLOBALS['lz_saved'])){
		echo '<div id="message" class="updated"><p>'. __('The settings were saved successfully', 'loginizer'). '</p></div><br />';
	}
	
	// Cleared ?
	if(!empty($GLOBALS['lz_cleared'])){
		echo '<div id="message" class="updated"><p>'. __('reCAPTCHA has been disabled !', 'loginizer'). '</p></div><br />';
	}
	
	// Any errors ?
	if(!empty($lz_error)){
		lz_report_error($lz_error);echo '<br />';
	}
	
	?>

<style>
input[type="text"], textarea, select {
    width: 70%;
}
</style>

	<div id="" class="postbox">
	
		<button class="handlediv button-link" aria-expanded="true" type="button">
			<span class="screen-reader-text">Toggle panel: reCAPTCHA Settings</span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
		
		<h2 class="hndle ui-sortable-handle">
			<span><?php echo __('reCAPTCHA Settings', 'loginizer'); ?></span>
		</h2>
		
		<div class="inside">
		
		<form action="" method="post" enctype="multipart/form-data">
		<?php wp_nonce_field('loginizer-options'); ?>
		<table class="form-table">
			<tr class="lz_google_cap">
				<td scope="row" valign="top" style="width:300px !important; padding-left:0px"><label><b><?php echo __('reCAPTCHA type', 'loginizer'); ?></b></label><br>
				<?php echo __('Choose the type of reCAPTCHA', 'loginizer'); ?><br />
				<?php echo __('<a href="https://g.co/recaptcha/sitetypes/" target="_blank">See Site Types for more details</a>', 'loginizer'); ?>
				</td>
				<td>
					<input type="radio" value="v3" onchange="google_recaptcha_type(this)" <?php echo lz_POSTradio('captcha_type', 'v3', $loginizer['captcha_type']); ?> name="captcha_type" id="captcha_type_v3" /> <label for="captcha_type_v3"><?php echo __('reCAPTCHA v3', 'loginizer'); ?></label><br /><br />
					<input type="radio" value="" onchange="google_recaptcha_type(this)" <?php echo lz_POSTradio('captcha_type', '', $loginizer['captcha_type']); ?> name="captcha_type" id="captcha_type_v2" /> <label for="captcha_type_v2"><?php echo __('reCAPTCHA v2 - Checkbox', 'loginizer'); ?></label><br /><br />
					<input type="radio" value="v2_invisible" onchange="google_recaptcha_type(this)" <?php echo lz_POSTradio('captcha_type', 'v2_invisible', $loginizer['captcha_type']); ?> name="captcha_type" id="captcha_type_v2_invisible" /> <label for="captcha_type_v2_invisible"><?php echo __('reCAPTCHA v2 - Invisible', 'loginizer'); ?></label><br />
				</td>
			</tr>
			<tr class="lz_google_cap">
				<td scope="row" valign="top" style="width:300px !important; padding-left:0px"><label><b><?php echo __('Site Key', 'loginizer'); ?></b></label><br>
				<?php echo __('Make sure you enter the correct keys as per the reCAPTCHA type selected above', 'loginizer'); ?>
				</td>
				<td>
					<input type="text" size="50" value="<?php echo lz_optpost('captcha_key', $loginizer['captcha_key']); ?>" name="captcha_key" /><br />
					<?php echo __('Get the Site Key and Secret Key from <a href="https://www.google.com/recaptcha/" target="_blank">Google</a>', 'loginizer'); ?>
				</td>
			</tr>
			<tr class="lz_google_cap">
				<th scope="row" valign="top"><label><?php echo __('Secret Key', 'loginizer'); ?></label></th>
				<td>
					<input type="text" size="50" value="<?php echo lz_optpost('captcha_secret', $loginizer['captcha_secret']); ?>" name="captcha_secret" />
				</td>
			</tr>
			<tr class="lz_google_cap">
				<th scope="row" valign="top"><label><?php echo __('Theme', 'loginizer'); ?></label></th>
				<td>
					<select name="captcha_theme">
						<?php
							foreach($lz_env['theme'] as $k => $v){
								echo '<option '.lz_POSTselect('captcha_theme', $k, ($loginizer['captcha_theme'] == $k ? true : false)).' value="'.$k.'">'.$v.'</value>';								
							}
						?>
					</select>
				</td>
			</tr>
			<tr class="lz_google_cap">
				<th scope="row" valign="top"><label><?php echo __('Language', 'loginizer'); ?></label></th>
				<td>
					<select name="captcha_lang">
						<?php
							foreach($lz_env['lang'] as $k => $v){
								echo '<option '.lz_POSTselect('captcha_lang', $k, ($loginizer['captcha_lang'] == $k ? true : false)).' value="'.$k.'">'.$v.'</value>';								
							}
						?>
					</select>
				</td>
			</tr>
			<tr class="lz_google_cap lz_google_cap_size">
				<th scope="row" valign="top"><label><?php echo __('Size', 'loginizer'); ?></label></th>
				<td>
					<select name="captcha_size">
						<?php
							foreach($lz_env['size'] as $k => $v){
								echo '<option '.lz_POSTselect('captcha_size', $k, ($loginizer['captcha_size'] == $k ? true : false)).' value="'.$k.'">'.$v.'</value>';								
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td scope="row" valign="top" style="padding-left:0px">
					<label><b><?php echo __('Don\'t use Google reCAPTCHA', 'loginizer'); ?></b></label><br>
					<?php echo __('If selected, '.$loginizer['prefix'].' will use a simple Math Captcha instead of Google reCAPTCHA', 'loginizer'); ?>
				</td>
				<td>
					<input type="checkbox" onclick="no_google_recaptcha(this)" id="captcha_no_google" value="1" name="captcha_no_google" <?php echo lz_POSTchecked('captcha_no_google', (empty($loginizer['captcha_no_google']) ? false : true)); ?> />
				</td>
			</tr>
			<tr class="lz_math_cap">
				<td scope="row" valign="top" style="width:300px !important; padding-left:0px">
					<label><b><?php echo __('Captcha Text', 'loginizer'); ?></b></label><br>
					<?php echo __('The text to be shown for the Captcha Field', 'loginizer'); ?>
				</td>
				<td>
					<input type="text" size="30" value="<?php echo lz_optpost('captcha_text', @$loginizer['captcha_text']); ?>" name="captcha_text" />
				</td>
			</tr>
			<tr class="lz_math_cap">
				<td scope="row" valign="top" style="padding-left:0px">
					<label><b><?php echo __('Captcha Time', 'loginizer'); ?></b></label><br>
					<?php echo __('Enter the number of seconds, a user has to enter captcha value.', 'loginizer'); ?>
				</td>
				<td>
					<input type="text" size="30" value="<?php echo lz_optpost('captcha_time', @$loginizer['captcha_time']); ?>" name="captcha_time" />
				</td>
			</tr>
			<tr class="lz_math_cap">
				<td scope="row" valign="top" style="padding-left:0px">
					<label><b><?php echo __('Display Captcha in Words', 'loginizer'); ?></b></label><br>
					<?php echo __('If selected the Captcha will be displayed in words rather than numbers', 'loginizer'); ?>
				</td>
				<td>
					<input type="checkbox" value="1" name="captcha_words" <?php echo lz_POSTchecked('captcha_words', (empty($loginizer['captcha_words']) ? false : true));?> />
				</td>
			</tr>
			<tr class="lz_math_cap">
				<td scope="row" valign="top" style="vertical-align: top !important; padding-left:0px">
					<label><b><?php echo __('Mathematical operations', 'loginizer'); ?></b></label><br>
					<?php echo __('The Mathematical operations to use for Captcha', 'loginizer'); ?>
				</td>
				<td valign="top">
					<table class="wp-list-table fixed users" cellpadding="8" cellspacing="1">
						<?php echo '
						<tr>
							<td>'.__('Addition (+)', 'loginizer').'</td>
							<td><input type="checkbox" value="1" name="captcha_add" '.lz_POSTchecked('captcha_add', (empty($loginizer['captcha_add']) ? false : true)).' /></td>
						</tr>
						<tr>
							<td>'.__('Subtraction (-)', 'loginizer').'</td>
							<td><input type="checkbox" value="1" name="captcha_subtract" '.lz_POSTchecked('captcha_subtract', (empty($loginizer['captcha_subtract']) ? false : true)).' /></td>
						</tr>
						<tr>
							<td>'.__('Multiplication (x)', 'loginizer').'</td>
							<td><input type="checkbox" value="1" name="captcha_multiply" '.lz_POSTchecked('captcha_multiply', (empty($loginizer['captcha_multiply']) ? false : true)).' /></td>
						</tr>
						<tr>
							<td>'.__('Division (÷)', 'loginizer').'</td>
							<td><input type="checkbox" value="1" name="captcha_divide" '.lz_POSTchecked('captcha_divide', (empty($loginizer['captcha_divide']) ? false : true)).' /></td>
						</tr>';
						?>
					</table>
				</td>
			</tr>
			<tr>
				<th scope="row" valign="top"><label><?php echo __('Show Captcha On', 'loginizer'); ?></label></th>
				<td valign="top">
					<table class="wp-list-table fixed users" cellpadding="8" cellspacing="1">
						<?php echo '
						<tr>
							<td>'.__('Login Form', 'loginizer').'</td>
							<td><input type="checkbox" value="1" name="captcha_login" '.lz_POSTchecked('captcha_login', (empty($loginizer['captcha_login']) ? false : true)).' /></td>
						</tr>
						<tr>
							<td>'.__('Lost Password Form', 'loginizer').'</td>
							<td><input type="checkbox" value="1" name="captcha_lostpass" '.lz_POSTchecked('captcha_lostpass', (empty($loginizer['captcha_lostpass']) ? false : true)).' /></td>
						</tr>
						<tr>
							<td>'.__('Reset Password Form', 'loginizer').'</td>
							<td><input type="checkbox" value="1" name="captcha_resetpass" '.lz_POSTchecked('captcha_resetpass', (empty($loginizer['captcha_resetpass']) ? false : true)).' /></td>
						</tr>
						<tr>
							<td>'.__('Registration Form', 'loginizer').'</td>
							<td><input type="checkbox" value="1" name="captcha_register" '.lz_POSTchecked('captcha_register', (empty($loginizer['captcha_register']) ? false : true)).' /></td>
						</tr>
						<tr>
							<td>'.__('Comment Form', 'loginizer').'</td>
							<td><input type="checkbox" value="1" name="captcha_comment" '.lz_POSTchecked('captcha_comment', (empty($loginizer['captcha_comment']) ? false : true)).' /></td>
						</tr>';
						
						if(!defined('SITEPAD')){
						
						echo '<tr>
							<td>'.__('WooCommerce Checkout', 'loginizer').'</td>
							<td><input type="checkbox" value="1" name="captcha_wc_checkout" '.lz_POSTchecked('captcha_wc_checkout', (empty($loginizer['captcha_wc_checkout']) ? false : true)).' /></td>
						</tr>';
						
						}
						
						?>
					</table>
				</td>
			</tr>
			<tr>
				<th scope="row" valign="top"><label><?php echo __('Hide CAPTCHA for logged in Users', 'loginizer'); ?></label></th>
				<td>
					<input type="checkbox" value="1" name="captcha_user_hide" <?php echo lz_POSTchecked('captcha_user_hide', (empty($loginizer['captcha_user_hide']) ? false : true)); ?> />
				</td>
			</tr>
			<tr class="lz_google_cap">
				<th scope="row" valign="top"><label><?php echo __('Disable CSS inserted on Login Page', 'loginizer'); ?></label></th>
				<td>
					<input type="checkbox" value="1" name="captcha_no_css_login" <?php echo lz_POSTchecked('captcha_no_css_login', (empty($loginizer['captcha_no_css_login']) ? false : true)); ?> />
				</td>
			</tr>
		</table><br />
		<center><input name="save_lz" class="button button-primary action" value="<?php echo __('Save Settings','loginizer'); ?>" type="submit" />
		<input style="float:right" name="clear_captcha_lz" class="button action" value="<?php echo __('Disable reCAPTCHA','loginizer'); ?>" type="submit" /></center>
		</form>
	
		</div>
	</div>
	<br />

<script type="text/javascript">

function no_google_recaptcha(obj){
	
	if(obj.checked){
		jQuery(".lz_google_cap").hide();
		jQuery(".lz_math_cap").show();
	}else{
		jQuery(".lz_google_cap").show();
		jQuery(".lz_math_cap").hide();
	}
	
	var cur_captcha_type = jQuery("input:radio[name='captcha_type']:checked").val();
	
	if(cur_captcha_type == 'v3' || cur_captcha_type == 'v2_invisible'){
		jQuery(".lz_google_cap_size").hide();
	}else{
		jQuery(".lz_google_cap_size").show();
	}
	
}

no_google_recaptcha(jQuery("#captcha_no_google")[0]);

function google_recaptcha_type(obj){
	if(obj.value == 'v3' || obj.value == 'v2_invisible'){
		jQuery(".lz_google_cap_size").hide();
	}else{
		jQuery(".lz_google_cap_size").show();
	}
}


</script>
	
	<?php
	loginizer_page_footer();
	
}


// Loginizer - Two Factor Auth Page
function loginizer_page_2fa(){
	
	global $loginizer, $lz_error, $lz_env, $lz_roles;
	 
	if(!current_user_can('manage_options')){
		wp_die('Sorry, but you do not have permissions to change settings.');
	}

	$lz_roles = get_editable_roles();
	
	/* Make sure post was from this page */
	if(count($_POST) > 0){
		check_admin_referer('loginizer-options');
	}
	
	// Settings submitted
	if(isset($_POST['save_lz'])){
		
		// In the future there can be more settings
		$option['2fa_app'] = (int) lz_optpost('2fa_app');
		$option['2fa_email'] = (int) lz_optpost('2fa_email');
		$option['question'] = (int) lz_optpost('question');
		$option['2fa_email_force'] = (int) lz_optpost('2fa_email_force');
		
		// Any roles to apply to ?
		foreach($lz_roles as $k => $v){
			
			if(lz_optpost('2fa_roles_'.$k)){
				$option['2fa_roles'][$k] = 1;
			}
			
		}
		
		// If its all, then blank it
		if(lz_optpost('2fa_roles_all') || empty($option['2fa_roles'])){
			$option['2fa_roles'] = '';
		}
		
		// Is there an error ?
		if(!empty($lz_error)){
			return loginizer_page_2fa_T();
		}
		
		// Save the options
		update_option('loginizer_2fa', $option);
		
		// Mark as saved
		$GLOBALS['lz_saved'] = true;
		
	}
	
	// Reset a users 2FA
	if(isset($_POST['reset_user_lz'])){
		
		$_username = lz_optpost('lz_user_2fa_disable');
		
		// Try to get the user
		$user_search = get_user_by('login', $_username);
		
		// If not found then search by email
		if(empty($user_search)){
			$user_search = get_user_by('email', $_username);
		}
		
		// If not found then give error
		if(empty($user_search)){
			$lz_error['2fa_user_not'] = __('There is no such user with the email or username you submitted', 'loginizer');
			return loginizer_page_2fa_T();
		}
		
		// Get the user prefences
		$user_pref = get_user_meta($user_search->ID, 'loginizer_user_settings');
		
		// Blank it
		$user_pref['pref'] = 'none';
		
		// Save it
		update_user_meta($user_search->ID, 'loginizer_user_settings', $user_pref);
		
		// Mark as saved
		$GLOBALS['lz_saved'] = __('The user\'s 2FA settings have been reset', 'loginizer');
		
	}
	
	// Call theme
	loginizer_page_2fa_T();
	
}


// Loginizer - Two Factor Auth Page
function loginizer_page_2fa_T(){
	
	global $loginizer, $lz_error, $lz_env, $lz_roles;
	
	// Universal header
	loginizer_page_header('Loginizer - Two Factor Authentication');
	
	// Saved ?
	if(!empty($GLOBALS['lz_saved'])){
		echo '<div id="message" class="updated"><p>'. __(is_string($GLOBALS['lz_saved']) ? $GLOBALS['lz_saved'] : 'The settings were saved successfully', 'loginizer'). '</p></div><br />';
	}
	
	// Any errors ?
	if(!empty($lz_error)){
		lz_report_error($lz_error);echo '<br />';
	}

	?>

<style>
input[type="text"], textarea, select {
    width: 70%;
}

.form-table label{
	font-weight:bold;
}

.exp{
	font-size:12px;
}
</style>

	<div id="" class="postbox">
	
		<button class="handlediv button-link" aria-expanded="true" type="button">
			<span class="screen-reader-text">Toggle panel: Two Factor Authentication Settings</span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
		
		<h2 class="hndle ui-sortable-handle">
			<span><?php echo __('Two Factor Authentication Settings', 'loginizer'); ?></span>
		</h2>
		
		<div class="inside">
		
		<form action="" method="post" enctype="multipart/form-data">
		<?php wp_nonce_field('loginizer-options'); ?>
		<table class="form-table">
			<tr>
				<td scope="row" valign="top" colspan="2">
					<i>Please choose from the following Two Factor Authentication methods. Each user can choose any one method from the ones enabled by you. You can enable all or anyone that you would like.</i>
				</td>
			</tr>
			<tr>
				<td scope="row" valign="top" style="width:70% !important">
					<label><?php echo __('OTP via App', 'loginizer'); ?></label><br>
					<span class="exp"><?php echo __('After entering the correct login credentials, the user will be asked for the OTP. The OTP will be obtained from the users mobile app e.g. <b>Google Authenticator, Authy, etc.</b>', 'loginizer'); ?></span>
				</td>
				<td>
					<input type="checkbox" value="1" name="2fa_app" <?php echo lz_POSTchecked('2fa_app', (empty($loginizer['2fa_app']) ? false : true)); ?> />
				</td>
			</tr>
			<tr>
				<td scope="row" valign="top">
					<label><?php echo __('OTP via Email', 'loginizer'); ?></label><br>
					<span class="exp"><?php echo __('After entering the correct login credentials, the user will be asked for the OTP. The OTP will be emailed to the user.', 'loginizer'); ?></span>
				</td>
				<td>
					<input type="checkbox" value="1" name="2fa_email" <?php echo lz_POSTchecked('2fa_email', (empty($loginizer['2fa_email']) ? false : true)); ?> />
				</td>
			</tr>
			<tr>
				<td scope="row" valign="top">
					<label><?php echo __('User Defined Question & Answer', 'loginizer'); ?></label><br>
					<span class="exp"><?php echo __('In this method the user will be asked to set a secret personal question and answer. After entering the correct login credentials, the user will be asked to answer the question set by them, thus increasing the security', 'loginizer'); ?></span>
				</td>
				<td>
					<input type="checkbox" value="1" name="question" <?php echo lz_POSTchecked('question', (empty($loginizer['question']) ? false : true)); ?> />
				</td>
			</tr>
		</table><br />
		
		<table class="form-table">
			<tr>
				<td scope="row" valign="top" style="width:70% !important">
					<label><?php echo __('Force OTP via Email', 'loginizer'); ?></label><br>
					<span class="exp"><?php echo __('If the user does not have any 2FA method selected, this will enforce the OTP via Email for the users.', 'loginizer'); ?></span>
				</td>
				<td>
					<input type="checkbox" value="1" name="2fa_email_force" <?php echo lz_POSTchecked('2fa_email_force', (empty($loginizer['2fa_email_force']) ? false : true)); ?> />
				</td>
			</tr>
			<tr>
				<td scope="row" valign="top" style="width:70% !important">
					<label><?php echo __('Apply 2FA to Roles', 'loginizer'); ?></label><br>
					<span class="exp"><?php echo __('Select the Roles to which 2FA should be applied.', 'loginizer'); ?></span>
				</td>
				<td>
					<input type="checkbox" value="1" onchange="lz_roles_handle()" name="2fa_roles_all" id="2fa_roles_all" <?php echo lz_POSTchecked('2fa_roles_all', (empty($loginizer['2fa_roles']) ? true : false)); ?> /> All<br />
					<?php
					
					foreach($lz_roles as $k => $v){
						echo '<span class="lz_roles"><input type="checkbox" value="1" name="2fa_roles_'.$k.'" '.lz_POSTchecked('2fa_roles_'.$k, (empty($loginizer['2fa_roles'][$k]) ? false : true)).' /> '.$v['name'].'<br /></span>';
					}
					
					?>
				</td>
			</tr>
		</table><br />
		<center><input name="save_lz" class="button button-primary action" value="<?php echo __('Save Settings', 'loginizer'); ?>" type="submit" /></center>
		</form>
	
		</div>
	</div>

<script type="text/javascript">

function lz_roles_handle(){
	
	var obj = jQuery("#2fa_roles_all")[0];
	
	if(obj.checked){
		jQuery(".lz_roles").hide();
	}else{
		jQuery(".lz_roles").show();
	}
	
}

lz_roles_handle();

</script>
	
	<!--Bypass a single user-->
	<div id="" class="postbox">
	
		<button class="handlediv button-link" aria-expanded="true" type="button">
			<span class="screen-reader-text">Toggle panel: Disable Two Factor Authentication for a User</span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
		
		<h2 class="hndle ui-sortable-handle">
			<span><?php echo __('Disable Two Factor Authentication for a User', 'loginizer'); ?></span>
		</h2>
		
		<div class="inside">
		
		<form action="" method="post" enctype="multipart/form-data">
		<?php wp_nonce_field('loginizer-options'); ?>
		<table class="form-table">
			<tr>
				<td scope="row" valign="top" colspan="2">
					<i>Here you can disable the Two Factor Authentication settings of a user. In the event a user has forgotten his secret answer or lost his Device App, he will not be able to login. You can reset such a users settings from here.</i>
				</td>
			</tr>
			<tr>
				<td scope="row" valign="top">
					<label><?php echo __('Username / Email', 'loginizer'); ?></label><br>
					<span class="exp"><?php echo __('The username or email of the user whose 2FA you would like to disable', 'loginizer'); ?></span>
				</td>
				<td>
					<input type="text" size="50" value="<?php echo lz_optpost('lz_user_2fa_disable', ''); ?>" name="lz_user_2fa_disable" />
				</td>
			</tr>
		</table><br />
		
		<center><input name="reset_user_lz" class="button button-primary action" value="<?php echo __('Reset 2FA for User', 'loginizer'); ?>" type="submit" /></center>
		</form>
	
		</div>
	</div>
	
	<br />

	<?php
	loginizer_page_footer();
	
}

// Loginizer - PasswordLess Page
function loginizer_page_passwordless(){
	
	global $loginizer, $lz_error, $lz_env;
	 
	if(!current_user_can('manage_options')){
		wp_die('Sorry, but you do not have permissions to change settings.');
	}

	/* Make sure post was from this page */
	if(count($_POST) > 0){
		check_admin_referer('loginizer-options');
	}
	
	if(isset($_POST['save_lz'])){
		
		// In the future there can be more settings
		$option['email_pass_less'] = (int) lz_optpost('email_pass_less');
		$option['passwordless_sub'] = lz_optpost('lz_passwordless_sub');
		$option['passwordless_msg'] = lz_optpost('lz_passwordless_msg');
		
		// Is there an error ?
		if(!empty($lz_error)){
			return loginizer_page_passwordless_T();
		}
		
		// Save the options
		update_option('loginizer_epl', $option);
		
		// Mark as saved
		$GLOBALS['lz_saved'] = true;
		
	}
	
	// Call theme
	loginizer_page_passwordless_T();
}

// Loginizer - PasswordLess Page Theme
function loginizer_page_passwordless_T(){
	
	global $loginizer, $lz_error, $lz_env;
	
	$lz_options = get_option('loginizer_epl');
	
	// Universal header
	loginizer_page_header('PasswordLess Settings');
	
	// Saved ?
	if(!empty($GLOBALS['lz_saved'])){
		echo '<div id="message" class="updated"><p>'. __('The settings were saved successfully', 'loginizer'). '</p></div><br />';
	}
	
	// Any errors ?
	if(!empty($lz_error)){
		lz_report_error($lz_error);echo '<br />';
	}

	?>

<style>
input[type="text"], textarea, select {
    width: 90%;
}

.form-table label{
	font-weight:bold;
}

.form-table td{
	vertical-align:top;
}

.exp{
	font-size:12px;
}
</style>

	<div id="" class="postbox">
	
		<button class="handlediv button-link" aria-expanded="true" type="button">
			<span class="screen-reader-text">Toggle panel: PasswordLess Settings</span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
		
		<h2 class="hndle ui-sortable-handle">
			<span><?php echo __('PasswordLess Settings', 'loginizer'); ?></span>
		</h2>
		
		<div class="inside">
		
		<form action="" method="post" enctype="multipart/form-data">
		<?php wp_nonce_field('loginizer-options'); ?>
		<table class="form-table">
			<tr>
				<th scope="row" valign="top" style="width:350px !important"><label><?php echo __('Enable PasswordLess Login', 'loginizer'); ?></label></th>
				<td>
					<input type="checkbox" value="1" name="email_pass_less" <?php echo lz_POSTchecked('email_pass_less', (empty($loginizer['email_pass_less']) ? false : true)); echo (defined('SITEPAD') ? 'disabled="disabled"' : '') ?> />
				</td>
			</tr>
			<tr>
				<td colspan="2" valign="top">
					<?php echo __('If enabled, the login screen will just ask for the username <b>OR</b> email address of the user. If such a user exists, an email with a <b>One Time Login </b> link will be sent to the email address of the user. The link will be valid for 10 minutes only.', 'loginizer'); ?><br><br>
					<?php echo __('If a wrong username/email is given, the brute force checker will prevent any brute force attempt !', 'loginizer'); ?>
				</td>
			</tr>
			<tr>
				<td scope="row" valign="top">
					<label><?php echo __('Email Subject', 'loginizer'); ?></label><br>
					<span class="exp"><?php echo __('Set blank to reset to the default subject', 'loginizer'); ?></span>
					<br />Default : <?php echo @$loginizer['pl_d_sub']; ?>
				</td>
				<td valign="top">
					<input type="text" size="40" value="<?php echo lz_optpost('lz_passwordless_sub', @$lz_options['passwordless_sub']); ?>" name="lz_passwordless_sub" />
				</td>
			</tr>
			<tr>
				<td scope="row" valign="top">
					<label><?php echo __('Email Body', 'loginizer'); ?></label><br>
					<span class="exp"><?php echo __('Set blank to reset to the default message', 'loginizer'); ?></span>
					<br />Default : <pre style="font-size:10px"><?php echo @$loginizer['pl_d_msg']; ?></pre>
				</td>
				<td valign="top">
					<textarea rows="10" name="lz_passwordless_msg"><?php echo lz_optpost('lz_passwordless_msg', @$lz_options['passwordless_msg']); ?></textarea>
					<br />
					Variables :
					<br />$email  - Users Email
					<br />$site_name - The Site Name
					<br />$site_url - The Site URL
					<br />$login_url - The Login URL
				</td>
			</tr>
		</table><br />
		<center><input name="save_lz" class="button button-primary action" value="<?php echo __('Save Settings', 'loginizer'); ?>" type="submit" /></center>
		</form>
	
		</div>
	</div>
	<br />

	<?php
	loginizer_page_footer();
	
}

// Loginizer - Security Settings Page
function loginizer_page_security(){
	
	global $loginizer, $lz_error, $lz_env, $wpdb;
	 
	if(!current_user_can('manage_options')){
		wp_die('Sorry, but you do not have permissions to change settings.');
	}

	/* Make sure post was from this page */
	if(count($_POST) > 0){
		check_admin_referer('loginizer-options');
	}
	
	if(isset($_POST['save_lz'])){
		
		$option['login_slug'] = lz_optpost('login_slug');
		$option['rename_login_secret'] = (int) lz_optpost('rename_login_secret');
		$option['xmlrpc_slug'] = lz_optpost('xmlrpc_slug');
		$option['xmlrpc_disable'] = (int) lz_optpost('xmlrpc_disable');
		$option['pingbacks_disable'] = (int) lz_optpost('pingbacks_disable');
		
		// Login Slug Valid ?
		if(!empty($option['login_slug'])){
			if(strlen($option['login_slug']) <= 4 || strlen($option['login_slug']) > 50){
				$lz_error['login_slug'] = __('The Login slug length must be greater than <b>4</b> chars and upto <b>50</b> chars long', 'loginizer');
			}
		}
		
		// XML-RPC Slug Valid ?
		if(!empty($option['xmlrpc_slug'])){
			if(strlen($option['xmlrpc_slug']) <= 4 || strlen($option['xmlrpc_slug']) > 50){
				$lz_error['xmlrpc_slug'] = __('The XML-RPC slug length must be greater than <b>4</b> chars and upto <b>50</b> chars long', 'loginizer');
			}
		}
		
		// Is there an error ?
		if(!empty($lz_error)){
			return loginizer_page_security_T();
		}
		
		// Save the options
		update_option('loginizer_security', $option);
		
		// Mark as saved
		$GLOBALS['lz_saved'] = true;
		
	}
	
	// Reset the username
	if(isset($_POST['save_lz_admin'])){
		
		// Get the new username
		$new_username = lz_optpost('new_username');
		
		// Is the starting of the username having 'admin' ?
		if(@strtolower(substr($new_username, 0, 5)) == 'admin'){
			$lz_error['user_exists'] = __('The username begins with <b>admin</b>. Please change it !', 'loginizer');
			return loginizer_page_security_T();
		}
		
		// Lets check if there is such a user
		$found = get_user_by('login', $new_username);
		
		// Found one !
		if(!empty($found->ID)){
			$lz_error['user_exists'] = __('The user you submitted already exists', 'loginizer');
			return loginizer_page_security_T();
		}
		
		// Update the username
		$wpdb->query("UPDATE `".$wpdb->prefix."users`
					SET user_login = '$new_username'
					WHERE `ID` = 1");
		
		// Mark as saved
		$GLOBALS['lz_saved'] = true;
		
	}
	
	// Change the wp-admin slug
	if(isset($_POST['save_lz_wp_admin'])){
		
		// Get the new username
		$option['admin_slug'] = lz_optpost('admin_slug');
		$option['restrict_wp_admin'] = (int) lz_optpost('restrict_wp_admin');
		$option['wp_admin_msg'] = @stripslashes($_POST['wp_admin_msg']);
		$lz_wp_admin_docs = (int) lz_optpost('lz_wp_admin_docs');
		
		// Did you agree to this ?
		if(empty($lz_wp_admin_docs)){
			$lz_error['lz_wp_admin_docs'] = __('You have not confirmed that you have read the guide and configured .htaccess. Please read the guide, configure .htaccess and then save these settings and check this checkbox', 'loginizer');
			return loginizer_page_security_T();
		}
		
		// Length
		if(strlen($option['admin_slug']) <= 4 || strlen($option['admin_slug']) > 50){
			$lz_error['admin_slug'] = __('The new Admin slug length must be greater than <b>4</b> chars and upto <b>50</b> chars long', 'loginizer');
			return loginizer_page_security_T();
		}
		
		// Only regular characters
		if(preg_match('/[^\w\d\-_]/is', $option['admin_slug'])){
			$lz_error['admin_slug_chars'] = __('Special characters are not allowed', 'loginizer');
			return loginizer_page_security_T();
		}
		
		// Update the option
		update_option('loginizer_wp_admin', $option);
		
		// Mark as saved
		$GLOBALS['lz_saved'] = true;
		
	}
	
	
	// Save blacklisted usernames
	if(isset($_POST['save_lz_bl_users'])){
		
		$usernames = isset($_POST['lz_bl_users']) && is_array($_POST['lz_bl_users']) ? $_POST['lz_bl_users'] : array();
		
		// Process the usernames i.e. remove blanks
		foreach($usernames as $k => $v){
			$v = trim($v);
			
			// Unset blank values
			if(empty($v)){
				unset($usernames[$k]);
			}
			
			// Disallow these special characters to avoid XSS or any other security vulnerability
			if(preg_match('/[\<\>\"\']/', $v)){
				unset($usernames[$k]);
			}
		}
		
		// Update the blacklist
		update_option('loginizer_username_blacklist', array_values($usernames));
		
		// Mark as saved
		$GLOBALS['lz_saved'] = true;
		
	}
	
	
	// Save blacklisted domains
	if(isset($_POST['save_lz_bl_domains'])){
		
		$domains = isset($_POST['lz_bl_domains']) && is_array($_POST['lz_bl_domains']) ? $_POST['lz_bl_domains'] : array();
		
		// Process the domains i.e. remove blanks
		foreach($domains as $k => $v){
			$v = trim($v);
			
			// Unset blank values
			if(empty($v)){
				unset($domains[$k]);
			}
			
			// Disallow these special characters to avoid XSS or any other security vulnerability
			if(preg_match('/[\<\>\"\']/', $v)){
				unset($domains[$k]);
			}
		}
		
		// Update the blacklist
		update_option('loginizer_domains_blacklist', array_values($domains));
		
		// Mark as saved
		$GLOBALS['lz_saved'] = true;
		
	}
	
	// Call theme
	loginizer_page_security_T();
	
}

// Loginizer - Security Settings Page Theme
function loginizer_page_security_T(){
	
	global $loginizer, $lz_error, $lz_env;
	
	// Universal header
	loginizer_page_header('Security Settings');
	
	// Saved ?
	if(!empty($GLOBALS['lz_saved'])){
		echo '<div id="message" class="updated"><p>'. __('The settings were saved successfully', 'loginizer'). '</p></div><br />';
	}
	
	// Any errors ?
	if(!empty($lz_error)){
		lz_report_error($lz_error);echo '<br />';
	}
	
	$current_admin = get_user_by('id', 1);

	?>

<style>
input[type="text"], textarea, select {
    width: 70%;
}

.form-table label{
	font-weight:bold;
}

.exp{
	font-size:12px;
}
</style>

<form action="" method="post" enctype="multipart/form-data">

	<div id="" class="postbox">
	
		<button class="handlediv button-link" aria-expanded="true" type="button">
			<span class="screen-reader-text">Toggle panel: Rename Login Page</span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
		
		<h2 class="hndle ui-sortable-handle">
			<span><?php echo __('Rename Login Page', 'loginizer'); ?></span>
		</h2>
		
		<div class="inside">
		
		<?php wp_nonce_field('loginizer-options'); ?>
		<table class="form-table">
			<tr>
				<td scope="row" valign="top" colspan="2">
					<i>You can rename your Login page from <b><?php echo $loginizer['login_basename']; ?></b> to anything of your choice e.g. mylogin. This would make it very difficult for automated attack bots to know where to login !</i>
				</td>
			</tr>
			<tr>
				<td scope="row" valign="top" style="width:40% !important">
					<label><?php echo __('New Login Slug', 'loginizer'); ?></label><br>
					<span class="exp"><?php echo __('Set blank to reset to the original login URL', 'loginizer'); ?></span>
				</td>
				<td>
					<input type="text" size="50" value="<?php echo lz_POSTval('login_slug', $loginizer['login_slug']); ?>" name="login_slug" />
				</td>
			</tr>
	
<?php

if(!defined('SITEPAD')){

?>
			<tr>
				<td scope="row" valign="top" style="width:200px !important">
					<label><?php echo __('Access Secretly Only', 'loginizer'); ?></label><br>
					<span class="exp"><?php echo __('If set, then all Login URL\'s will still point to '.$loginizer['login_basename'].' and users will have to access the New Login Slug by typing it in the browser.', 'loginizer'); ?></span>
				</td>
				<td>
					<input type="checkbox" value="1" name="rename_login_secret" <?php echo lz_POSTchecked('rename_login_secret', (empty($loginizer['rename_login_secret']) ? false : true)); ?> /></td>
				</td>
			</tr>
	
<?php

}

?>
		</table><br />
		<center><input name="save_lz" class="button button-primary action" value="<?php echo __('Save Settings', 'loginizer'); ?>" type="submit" /></center>
	
		</div>
	</div>
	<br />
	
	<?php
	
	if(!defined('SITEPAD')){

	?>

	<div id="" class="postbox">
	
		<button class="handlediv button-link" aria-expanded="true" type="button">
			<span class="screen-reader-text">Toggle panel: XML-RPC Settings</span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
		
		<h2 class="hndle ui-sortable-handle">
			<span><?php echo __('XML-RPC Settings', 'loginizer'); ?></span>
		</h2>
		
		<div class="inside">
		
		<?php wp_nonce_field('loginizer-options'); ?>
		<table class="form-table">
			<tr>
				<td scope="row" valign="top" colspan="2">
					<i>WordPress's XML-RPC feature allows external services to access and modify content on the site. Services like the Jetpack plugin, the WordPress mobile app, pingbacks, etc make use of the XML-RPC feature. If this site does not use a service that requires XML-RPC, please <b>disable</b> the XML-RPC feature as it prevents attackers from using the feature to attack the site. If your service can use a custom XML-RPC URL, you can also <b>rename</b> the XML-RPC page to a <b>custom slug</b>.</i>
				</td>
			</tr>
			<tr>
				<td scope="row" valign="top" style="width:40% !important">
					<label><?php echo __('Disable XML-RPC', 'loginizer'); ?></label>
				</td>
				<td>
					<input type="checkbox" value="1" name="xmlrpc_disable" <?php echo lz_POSTchecked('xmlrpc_disable', (empty($loginizer['xmlrpc_disable']) ? false : true)); ?> />
				</td>
			</tr>
			<tr>
				<td scope="row" valign="top" style="width:40% !important">
					<label><?php echo __('Disable Pingbacks', 'loginizer'); ?></label>
				</td>
				<td>
					<input type="checkbox" value="1" name="pingbacks_disable" <?php echo lz_POSTchecked('pingbacks_disable', (empty($loginizer['pingbacks_disable']) ? false : true)); ?> />
				</td>
			</tr>
			<tr>
				<td scope="row" valign="top">
					<label><?php echo __('New XML-RPC Slug', 'loginizer'); ?></label><br>
					<span class="exp"><?php echo __('Set blank to reset to the original XML-RPC URL', 'loginizer'); ?></span>
				</td>
				<td>
					<input type="text" size="50" value="<?php echo lz_optpost('xmlrpc_slug', $loginizer['xmlrpc_slug']); ?>" name="xmlrpc_slug" />
				</td>
			</tr>
		</table><br />
		<center><input name="save_lz" class="button button-primary action" value="<?php echo __('Save Settings', 'loginizer'); ?>" type="submit" /></center>
	
		</div>
	</div>
	<br />
	
	<?php
	
	}

	?>
	
</form>
	
<?php

if(!defined('SITEPAD')){

?>

<script type="text/javascript">


function dirname(path) {
  return path.replace(/\\/g, '/').replace(/\/[^/]*\/?$/, '');
}

function lz_test_wp_admin(){
	
	var data = new Object();
	data["action"] = "loginizer_wp_admin";
	data["nonce"]	= "<?php echo wp_create_nonce('loginizer_admin_ajax');?>";
	
	var new_ajaxurl = dirname(dirname(ajaxurl))+'/'+jQuery('#lz_admin_slug').val()+'/admin-ajax.php';
	
	// AJAX and on success function
	jQuery.post(new_ajaxurl, data, function(response){
		
		if(response['result'] == 1){
			alert("<?php echo __('Everything seems to be good. You can proceed to save the settings !', 'loginizer'); ?>");
		}		
	
	// Throw an error for failures
	}).fail(function() {
		alert("<?php echo __('There was an error connecting to WordPress with the new Admin Slug. Did you configure everything properly ?', 'loginizer'); ?>");
	});
	//jQuery.ajax('<input type="text" size="30" value="" name="lz_bl_users[]" class="lz_bl_users" />');
	return false;
};

</script>

<form action="" method="post" enctype="multipart/form-data">
	<div id="" class="postbox">
	
		<button class="handlediv button-link" aria-expanded="true" type="button">
			<span class="screen-reader-text">Toggle panel: Rename wp-admin access</span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
		
		<h2 class="hndle ui-sortable-handle">
			<span><?php echo __('Rename wp-admin access', 'loginizer'); ?></span>
		</h2>
		
		<div class="inside">
		
		<?php wp_nonce_field('loginizer-options'); ?>
		<table class="form-table">
			<tr>
				<td scope="row" valign="top" colspan="2">
					<i>You can rename your WordPress Admin access URL <b>wp-admin</b> to anything of your choice e.g. my-admin. This will require you to change .htaccess, so please follow <a href="<?php echo LOGINIZER_DOCS;?>Renaming_the_WP-Admin_Area" target="_blank">our guide</a> on how to do so !</i>
				</td>
			</tr>
			<tr>
				<td scope="row" valign="top" style="width:40% !important">
					<label><?php echo __('New wp-admin Slug', 'loginizer'); ?></label><br>
					<span class="exp"><?php echo __('Set blank to reset to the original wp-admin URL', 'loginizer'); ?></span>
				</td>
				<td>
					<input type="text" size="50" value="<?php echo lz_optpost('admin_slug', $loginizer['admin_slug']); ?>" name="admin_slug" id="lz_admin_slug" />
				</td>
			</tr>
			<tr>
				<td scope="row" valign="top" style="width:200px !important">
					<label><?php echo __('Disable wp-admin access', 'loginizer'); ?></label><br>
					<span class="exp"><?php echo __('If set, then only the new admin slug will work and access to the Old Admin Slug i.e. wp-admin will be disabled. If anyone accesses wp-admin, a warning will be shown.<br><label>NOTE: Please use this option cautiously !</label>', 'loginizer'); ?></span>
				</td>
				<td>
					<input type="checkbox" id="lz_restrict_wp_admin" onchange="lz_wp_admin_msg_toggle()" value="1" name="restrict_wp_admin" <?php echo lz_POSTchecked('restrict_wp_admin', (empty($loginizer['restrict_wp_admin']) ? false : true)); ?> /></td>
				</td>
			</tr>
			<tr id="lz_wp_admin_msg_row" style="display:none">
				<td scope="row" valign="top">
					<label><?php echo __('WP-Admin Error Message', 'loginizer'); ?></label><br>
					<span class="exp"><?php echo __('Error message to show if someone accesses wp-admin', 'loginizer'); ?></span> Default : <?php echo $loginizer['wp_admin_d_msg']; ?>
				</td>
				<td>
					<input type="text" size="50" value="<?php echo lz_htmlizer(!empty($_POST['wp_admin_msg']) ? stripslashes($_POST['wp_admin_msg']) : @$loginizer['wp_admin_msg']); ?>" name="wp_admin_msg" id="lz_wp_admin_msg" />
				</td>
			</tr>
			<tr>
				<td scope="row" valign="top" style="width:200px !important">
					<label><?php echo __('I have setup .htaccess', 'loginizer'); ?></label><br>
					<span class="exp"><?php echo __('You need to confirm that you have configured .htaccess as per <a href="'.LOGINIZER_DOCS.'Renaming_the_WP-Admin_Area" target="_blank">our guide</a> so that we can safely enable this feature', 'loginizer'); ?></span>
				</td>
				<td>
					<input type="checkbox" value="1" name="lz_wp_admin_docs" />
					<input type="button" onclick="lz_test_wp_admin()" class="button" style="background: #5cb85c; color:white; border:#5cb85c" value="<?php echo __('Test New WP-Admin Slug', 'loginizer'); ?>" />
				</td>
			</tr>
		</table><br />
		<center><input name="save_lz_wp_admin" class="button button-primary action" value="<?php echo __('Save Settings', 'loginizer'); ?>" type="submit" /></center>
	
		</div>
	</div>
	<br />
</form>

<script type="text/javascript">

function lz_wp_admin_msg_toggle(){
	var ele = jQuery('#lz_restrict_wp_admin')[0];
	if(ele.checked){
		jQuery('#lz_wp_admin_msg_row').show();
	}else{
		jQuery('#lz_wp_admin_msg_row').hide();
	}
};

lz_wp_admin_msg_toggle();

</script>
	

<form action="" method="post" enctype="multipart/form-data">
	<div id="" class="postbox">
	
		<button class="handlediv button-link" aria-expanded="true" type="button">
			<span class="screen-reader-text">Toggle panel: Change Admin Username</span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
		
		<h2 class="hndle ui-sortable-handle">
			<span><?php echo __('Change Admin Username', 'loginizer'); ?></span>
		</h2>
		
		<div class="inside">
		
		<?php wp_nonce_field('loginizer-options'); ?>
		<table class="form-table">
			<tr>
				<td scope="row" valign="top" colspan="2">
					<i>You can change the Admin Username from here to anything of your choice e.g. iamtheboss. This would make it very difficult for automated attack bots to know what is the admin username !</i>
				</td>
			</tr>
			<tr>
				<td scope="row" valign="top" style="width:40% !important">
					<label><?php echo __('New Username', 'loginizer'); ?></label><br>
					<span class="exp"><?php echo __('The new Username you want to set for the admin user <br>i.e. UID : 1', 'loginizer'); ?></span>
				</td>
				<td>
					<input type="text" size="50" value="<?php echo lz_optpost('new_username', $current_admin->user_login); ?>" name="new_username" />
				</td>
			</tr>
		</table><br />
		<center><input name="save_lz_admin" class="button button-primary action" value="<?php echo __('Set the Username', 'loginizer'); ?>" type="submit" /></center>
	
		</div>
	</div>
</form>

<script type="text/javascript">
function add_lz_bl_users(){
	jQuery("#lz_bl_users").append('<input type="text" size="30" value="" name="lz_bl_users[]" class="lz_bl_users" />');
	return false;
};
</script>

<style>
.lz_bl_users, .lz_bl_domains{
	margin-bottom:20px;
}
</style>

<form action="" method="post" enctype="multipart/form-data">
	<div id="" class="postbox">
	
		<button class="handlediv button-link" aria-expanded="true" type="button">
			<span class="screen-reader-text">Toggle panel: Username Auto Blacklist</span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
		
		<h2 class="hndle ui-sortable-handle">
			<span><?php echo __('Username Auto Blacklist', 'loginizer'); ?></span>
		</h2>
		
		<div class="inside">
		
		<?php wp_nonce_field('loginizer-options'); ?>
		<table class="form-table">
			<tr>
				<td scope="row" valign="top" colspan="2">
					<i>Attackers generally use common usernames like <b>admin, administrator, or variations of your domain name / business name</b>. You can specify such username here and Loginizer will auto-blacklist the IP Address(s) of clients who try to use such username(s).</i>
				</td>
			</tr>
			<tr>
				<td scope="row" valign="top" style="width:40% !important; vertical-align:top !important;">
					<label><?php echo __('Username(s)', 'loginizer'); ?></label><br>
					<span class="exp"><?php echo __('You can use - <b>*</b> (Star)- as a wild card as well. Blank fields will be ignored', 'loginizer'); ?></span>
				</td>
				<td>
					<div id="lz_bl_users">
					<?php
					
					$usernames = isset($_POST['lz_bl_users']) && is_array($_POST['lz_bl_users']) ? $_POST['lz_bl_users'] : $loginizer['username_blacklist'];
					
					if(empty($usernames)){
						$usernames[] = '';
					}
					
					foreach($usernames as $_user){
						echo '<input type="text" size="30" value="'.$_user.'" name="lz_bl_users[]" class="lz_bl_users" />';
					}
					
					?>
					</div>
					<br />
					<input class="button" value="<?php echo __('Add New Username', 'loginizer'); ?>" onclick="return add_lz_bl_users();" style="float:right" />
				</td>
			</tr>
		</table><br />
		<center><input name="save_lz_bl_users" class="button button-primary action" value="<?php echo __('Save Username(s)', 'loginizer'); ?>" type="submit" /></center>
	
		</div>
	</div>
</form>

<script type="text/javascript">
function add_lz_bl_domains(){
	jQuery("#lz_bl_domains").append('<input type="text" size="30" value="" name="lz_bl_domains[]" class="lz_bl_domains" />');
	return false;
};
</script>


<form action="" method="post" enctype="multipart/form-data">
	<div id="" class="postbox">
	
		<button class="handlediv button-link" aria-expanded="true" type="button">
			<span class="screen-reader-text">Toggle panel: New Registration Domain Blacklist</span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
		
		<h2 class="hndle ui-sortable-handle">
			<span><?php echo __('New Registration Domain Blacklist', 'loginizer'); ?></span>
		</h2>
		
		<div class="inside">
		
		<?php wp_nonce_field('loginizer-options'); ?>
		<table class="form-table">
			<tr>
				<td scope="row" valign="top" colspan="2">
					<i>If you would like to ban new registrations from a particular domain, you can use this utility to do so.</i>
				</td>
			</tr>
			<tr>
				<td scope="row" valign="top" style="width:40% !important; vertical-align:top !important;">
					<label><?php echo __('Domain(s)', 'loginizer'); ?></label><br>
					<span class="exp"><?php echo __('You can use - <b>*</b> (Star)- as a wild card as well. Blank fields will be ignored', 'loginizer'); ?></span>
				</td>
				<td>
					<div id="lz_bl_domains">
					<?php
					
					$domains = isset($_POST['lz_bl_domains']) && is_array($_POST['lz_bl_domains']) ? $_POST['lz_bl_domains'] : $loginizer['domains_blacklist'];
					
					if(empty($domains)){
						$domains[] = '';
					}
					
					foreach($domains as $_domain){
						echo '<input type="text" size="30" value="'.$_domain.'" name="lz_bl_domains[]" class="lz_bl_domains" />';
					}
					
					?>
					</div>
					<br />
					<input class="button" value="<?php echo __('Add New Domain', 'loginizer'); ?>" onclick="return add_lz_bl_domains();" style="float:right" />
				</td>
			</tr>
		</table><br />
		<center><input name="save_lz_bl_domains" class="button button-primary action" value="<?php echo __('Save Domains(s)', 'loginizer'); ?>" type="submit" /></center>
	
		</div>
	</div>	
</form>

<?php

}
	
	loginizer_page_footer();
	
}

// Loginizer - Checksum load data
function loginizer_page_checksums_L(&$files, &$_ignores){
	
	global $loginizer, $lz_error, $lz_env;
	
	// Load any mismatched files and ignores
	$files = get_option('loginizer_checksums_diff');
	$_ignores = get_option('loginizer_checksums_ignore');
	$_ignores = is_array($_ignores) ? $_ignores : array(); // SHOULD ALWAYS BE PURE
	$ignores = array();
	
	foreach($_ignores as $ik => $iv){
		$ignores[$iv] = array();
		if(!empty($files[$iv])){
			$ignores[$iv] = $files[$iv];
		}
	}
	
	$lz_env['files'] = $files;
	$lz_env['ignores'] = $ignores;

}
	
// Loginizer - PasswordLess Page
function loginizer_page_checksums(){
	
	global $loginizer, $lz_error, $lz_env;
	
	// Are we to run it ?
	if(isset($_REQUEST['lz_run_checksum'])){
		loginizer_checksums();
	}
	
	if(!current_user_can('manage_options')){
		wp_die('Sorry, but you do not have permissions to change settings.');
	}
	
	loginizer_page_checksums_L($files, $_ignores);

	/* Make sure post was from this page */
	if(count($_POST) > 0){
		check_admin_referer('loginizer-options');
	}
	
	$lz_env['csum_freq'][1] = 'Once a Day';
	$lz_env['csum_freq'][7] = 'Once a Week';
	$lz_env['csum_freq'][30] = 'Once a Month';
	
	if(isset($_POST['save_lz'])){
		
		// In the future there can be more settings
		$option['disable_checksum'] = (int) lz_optpost('disable_checksum');
		$option['no_checksum_email'] = (int) lz_optpost('no_checksum_email');
		$option['checksum_frequency'] = (int) lz_optpost('checksum_frequency');
		$option['checksum_time'] = lz_optpost('checksum_time');
		
		// Is there an error ?
		if(!empty($lz_error)){
			return loginizer_page_checksums_T();
		}
		
		// Save the options
		update_option('loginizer_checksums', $option);
		
		// Mark as saved
		$GLOBALS['lz_saved'] = true;
		
	}
	
	// Add or remove from ignore list
	if(isset($_POST['save_lz_csum_ig'])){
		
		if(@is_array($_POST['checksum_del_ignore'])){
			
			foreach($_POST['checksum_del_ignore'] as $k => $v){
				$key = array_search($v, $_ignores);
				if($key !== false){
					unset($_ignores[$key]);
				}
			}
			
			// Save it
			update_option('loginizer_checksums_ignore', $_ignores);
			
		}
		
		if(@is_array($_POST['checksum_add_ignore'])){
			
			foreach($_POST['checksum_add_ignore'] as $k => $v){
				if(!empty($files[$v])){
					$_ignores[] = $v;
				}
			}
			
			// Save it
			update_option('loginizer_checksums_ignore', $_ignores);
			
		}
		
		// Reload
		loginizer_page_checksums_L($files, $_ignores);
		
		// Mark as saved
		$GLOBALS['lz_saved'] = true;
		
	}
	
	// Call theme
	loginizer_page_checksums_T();
}

// Loginizer - PasswordLess Page Theme
function loginizer_page_checksums_T(){
	
	global $loginizer, $lz_error, $lz_env;
	
	// Universal header
	loginizer_page_header('File Checkum Settings');
	
	wp_enqueue_script('jquery-clockpicker', LOGINIZER_URL.'/jquery-clockpicker.min.js', array('jquery'), '0.0.7');
	wp_enqueue_style('jquery-clockpicker', LOGINIZER_URL.'/jquery-clockpicker.min.css', array(), '0.0.7');
	
	// Saved ?
	if(!empty($GLOBALS['lz_saved'])){
		echo '<div id="message" class="updated"><p>'. __('The settings were saved successfully', 'loginizer'). '</p></div><br />';
	}
	
	// Did we just run the checksums
	if(isset($_REQUEST['lz_run_checksum'])){
		echo '<div id="message" class="updated"><p>'. __('The Checksum process was executed successfully', 'loginizer'). '</p></div><br />';
	}
	
	// Any errors ?
	if(!empty($lz_error)){
		lz_report_error($lz_error);echo '<br />';
	}

	?>

<style>
input[type="text"], textarea, select {
    width: 70%;
}

.form-table label{
	font-weight:bold;
}

.exp{
	font-size:12px;
}
</style>

<script>
function lz_apply_status(ele, the_class){
	
	var status = ele.checked;
	jQuery(the_class).each(function(){
		this.checked = status;
	});
	
}
</script>

	<div id="" class="postbox">
	
		<button class="handlediv button-link" aria-expanded="true" type="button">
			<span class="screen-reader-text">Toggle panel: Checksum Settings</span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
		
		<h2 class="hndle ui-sortable-handle">
			<span><?php echo __('Checksum Settings', 'loginizer'); ?></span>
		</h2>
		
		<div class="inside">
		
		<form action="" method="post" enctype="multipart/form-data">
		<?php wp_nonce_field('loginizer-options'); ?>
		<table class="form-table">
			<tr>
				<td scope="row" valign="top" style="width:400px !important">
					<label><?php echo __('Disable Checksum of WP Core', 'loginizer'); ?></label><br>
					<span class="exp"><?php echo __('If disabled, Loginizer will not check your sites core files against the WordPress checksum list.', 'loginizer'); ?></span>
				</td>
				<td valign="top">
					<input type="checkbox" value="1" name="disable_checksum" <?php echo lz_POSTchecked('disable_checksum', (empty($loginizer['disable_checksum']) ? false : true)); ?> />
				</td>
			</tr>
			<tr>
				<td scope="row" valign="top" style="width:400px !important">
					<label><?php echo __('Disable Email of Checksum Results', 'loginizer'); ?></label><br>
					<span class="exp"><?php echo __('If checked, Loginizer will not email you the checksum results.', 'loginizer'); ?></span>
				</td>
				<td valign="top">
					<input type="checkbox" value="1" name="no_checksum_email" <?php echo lz_POSTchecked('no_checksum_email', (empty($loginizer['no_checksum_email']) ? false : true)); ?> />
				</td>
			</tr>
			<tr>
				<td scope="row" valign="top" style="width:400px !important">
					<label><?php echo __('Checksum Frequency', 'loginizer'); ?></label><br>
					<span class="exp"><?php echo __('If Checksum is enabled, at what frequency should the checksums be performed.', 'loginizer'); ?></span>
				</td>
				<td valign="top">					
					<select name="checksum_frequency">
						<?php
							foreach($lz_env['csum_freq'] as $k => $v){
								echo '<option '.lz_POSTselect('checksum_frequency', $k, ($loginizer['checksum_frequency'] == $k ? true : false)).' value="'.$k.'">'.$v.'</value>';								
							}
						?>
					</select>
				</td>
			</tr>
			<tr id="lz_checksum_time">
				<td scope="row" valign="top" style="width:400px !important">
					<label><?php echo __('Time of Day', 'loginizer'); ?></label><br>
					<span class="exp"><?php echo __('If Checksum is enabled, what time of day should Loginizer do the check. Note : The check will be done on or after this time has elapsed as per the accesses being made.', 'loginizer'); ?></span>
				</td>
				<td valign="top">
					<div class="input-group clockpicker" data-autoclose="true">
						<input type="text" name="checksum_time" class="form-control" value="<?php echo (empty($loginizer['checksum_time']) ? '00:00' : $loginizer['checksum_time']);?>">
						<span class="input-group-addon">
							<span class="glyphicon glyphicon-time"></span>
						</span>
					</div>
					<script type="text/javascript">
					jQuery(document).ready(function(){
						(function($) {
							$('.clockpicker').clockpicker({donetext: 'Done'});
						})(jQuery);
					});
					</script>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<?php echo __('If disabled, Loginizer will not check your sites core files against the WordPress checksum list.', 'loginizer'); ?>
				</td>
			</tr>
		</table><br />
		<center><input name="save_lz" class="button button-primary action" value="<?php echo __('Save Settings', 'loginizer'); ?>" type="submit" /><input name="lz_run_checksum" style="float:right; background: #5cb85c; color:white; border:#5cb85c" class="button button-secondary" value="<?php echo __('Do a Checksum Now', 'loginizer'); ?>" type="submit" /></center>
		</form>
	
		</div>
	</div>
	
	<div id="" class="postbox">
	
		<button class="handlediv button-link" aria-expanded="true" type="button">
			<span class="screen-reader-text">Toggle panel: Mismatching Files</span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>	
		
		<h2 class="hndle ui-sortable-handle">
			<span><?php echo __('Mismatching Files', 'loginizer'); ?></span>
		</h2>
		
		<div class="inside">
		
		<form action="" method="post" enctype="multipart/form-data">
		<?php wp_nonce_field('loginizer-options'); ?>
		<table class="wp-list-table fixed striped users" border="0" width="100%" cellpadding="10" align="center">
			<?php
			
			$files = $lz_env['files'];
			
			// Avoid undefined notice for $files
			if(!empty($files)){
				foreach($files as $k => $v){
					if(!empty($lz_env['ignores'][$k])){
						unset($files[$k]);
					}
				}
			}
			
			echo '
			<tr>
				<th style="background:#EFEFEF;">'.__('Relative Path', 'loginizer').'</th>
				<th style="width:240px; background:#EFEFEF;">'.__('Found', 'loginizer').'</th>
				<th style="width:240px; background:#EFEFEF;">'.__('Should be', 'loginizer').'</th>
				<th style="width:10px; background:#EFEFEF;"><input type="checkbox" onchange="lz_apply_status(this, \'.csum_add_ig\');" /></th>
			</tr>';
			
			if(is_array($files) && count($files) > 0){
				
				foreach($files as $k => $v){
					
					echo '
				<tr>
					<td>'.$k.'</td>
					<td>'.$v['cur_md5'].'</td>
					<td>'.$v['md5'].'</td>
					<td><input type="checkbox" name="checksum_add_ignore[]" class="csum_add_ig" value="'.$k.'" /></td>
				</tr>';
					
				}
				
			}else{
				
				echo '
				<tr>
					<td colspan="4" align="center">'.__('This is great ! No file with any wrong checksum has been found.').'</td>
				</tr>';
				
			}
			
			?>
		</table><br />
		<center><input name="save_lz_csum_ig" class="button button-primary action" value="<?php echo __('Add Selected to Ignore List', 'loginizer'); ?>" type="submit" /></center>
		</form>
		</div>
		
	</div>
	<br />
	
	<div id="" class="postbox">
	
		<button class="handlediv button-link" aria-expanded="true" type="button">
			<span class="screen-reader-text">Toggle panel: Ignore List</span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>	
		
		<h2 class="hndle ui-sortable-handle">
			<span><?php echo __('Ignore List', 'loginizer'); ?></span>
		</h2>
		
		<div class="inside">
		
		<form action="" method="post" enctype="multipart/form-data">
		<?php wp_nonce_field('loginizer-options'); ?>
		<table class="wp-list-table fixed striped users" border="0" width="100%" cellpadding="10" align="center">
			<?php

			$ignores = $lz_env['ignores'];
			
			echo '
			<tr>
				<th style="background:#EFEFEF;">'.__('Relative Path', 'loginizer').'</th>
				<th style="width:240px; background:#EFEFEF;">'.__('Found', 'loginizer').'</th>
				<th style="width:240px; background:#EFEFEF;">'.__('Should be', 'loginizer').'</th>
				<th style="width:10px; background:#EFEFEF;"><input type="checkbox" onchange="lz_apply_status(this, \'.csum_del_ig\');" /></th>
			</tr>';
	
			// Load any mismatched files
			$files = $ignores;
			
			if(is_array($files) && count($files) > 0){
				
				foreach($files as $k => $v){
					
					echo '
				<tr>
					<td>'.$k.'</td>
					<td>'.$v['cur_md5'].'</td>
					<td>'.$v['md5'].'</td>
					<td><input type="checkbox" name="checksum_del_ignore[]" class="csum_del_ig" value="'.$k.'" /></td>
				</tr>';
					
				}
				
			}else{
				
				echo '
				<tr>
					<td colspan="4" align="center">'.__('No files have been added to the ignore list').'</td>
				</tr>';
				
			}
			
			?>
		</table><br />
		<center><input name="save_lz_csum_ig" class="button button-primary action" value="<?php echo __('Remove Selected from Ignore List', 'loginizer'); ?>" type="submit" /></center>
		</form>
		</div>
		
	</div>
	<br />

	<?php
	loginizer_page_footer();
	
}