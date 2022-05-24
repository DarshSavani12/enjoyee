<?php

namespace phonepe;

if(!is_admin())
	return;

global $pagenow;

if($pagenow != "plugins.php")
	return;

if(defined('SGITS_DEACTIVATE_FEEDBACK_FORM_INCLUDED'))
	return;
define('SGITS_DEACTIVATE_FEEDBACK_FORM_INCLUDED', true);

add_action('admin_enqueue_scripts', function() {
	
	// Enqueue scripts
	wp_enqueue_script('remodal', plugin_dir_url(__FILE__) . 'remodal.min.js');
	wp_enqueue_style('remodal', plugin_dir_url(__FILE__) . 'remodal.css');
	wp_enqueue_style('remodal-default-theme', plugin_dir_url(__FILE__) . 'remodal-default-theme.css');
	
	wp_enqueue_script('sgits-deactivate-feedback-form', plugin_dir_url(__FILE__) . 'deactivate-feedback-form.js');
	wp_enqueue_style('sgits-deactivate-feedback-form', plugin_dir_url(__FILE__) . 'deactivate-feedback-form.css');
	
	// Localized strings
	wp_localize_script('sgits-deactivate-feedback-form', 'sgits_deactivate_feedback_form_strings', array(
		'quick_feedback'			=> __('Quick Feedback', 'phonepe'),
		'foreword'					=> __('If you would be kind enough, please tell us why you\'re deactivating?', 'phonepe'),
		'better_plugins_name'		=> __('Please tell us which plugin?', 'phonepe'),
		'please_tell_us'			=> __('Please tell us the reason so we can improve the plugin', 'phonepe'),
		'do_not_attach_email'		=> __('Do not send my e-mail address with this feedback', 'phonepe'),
		
		'brief_description'			=> __('Please give us any feedback that could help us improve', 'phonepe'),
		
		'cancel'					=> __('Cancel', 'phonepe'),
		'skip_and_deactivate'		=> __('Skip &amp; Deactivate', 'phonepe'),
		'submit_and_deactivate'		=> __('Submit &amp; Deactivate', 'phonepe'),
		'please_wait'				=> __('Please wait', 'phonepe'),
		'thank_you'					=> __('Thank you!', 'phonepe')
	));
	
	// Plugins
	$plugins = apply_filters('sgits_deactivate_feedback_form_plugins', array());
	
	// Reasons
	$defaultReasons = array(
		'suddenly-stopped-working'	=> __('The plugin suddenly stopped working', 'phonepe'),
		'plugin-broke-site'			=> __('The plugin broke my site', 'phonepe'),
		'no-longer-needed'			=> __('I don\'t need this plugin any more', 'phonepe'),
		'found-better-plugin'		=> __('I found a better plugin', 'phonepe'),
		'temporary-deactivation'	=> __('It\'s a temporary deactivation, I\'m troubleshooting', 'phonepe'),
		'other'						=> __('Other', 'phonepe')
	);
	
	foreach($plugins as $plugin)
	{
		$plugin->reasons = apply_filters('sgits_deactivate_feedback_form_reasons', $defaultReasons, $plugin);
	}
	
	// Send plugin data
	wp_localize_script('sgits-deactivate-feedback-form', 'sgits_deactivate_feedback_form_plugins', $plugins);
});

/**
 * Hook for adding plugins, pass an array of objects in the following format:
 *  'slug'		=> 'plugin-slug'
 *  'version'	=> 'plugin-version'
 * @return array The plugins in the format described above
 */
add_filter('sgits_deactivate_feedback_form_plugins', function($plugins) {
	return $plugins;
});
