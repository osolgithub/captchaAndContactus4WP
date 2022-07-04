<!-- Replace ^[^#]([\r\n]*) with blank to make a template. In note pad you can also use `negative lookahead` ^(?!") -->
# Short Description

Wordpress is prefered CMS for sites with `Pages` and `Blogs`

# Detailed Description

### Types of extensions possible

Wordpress extensions are called `plugins`. 
Plugins can
1. Show Custom Functionalities in `Pages` and `Blogs`. See `add_shortcode` below
2. Extend/Replace Core funtionalities with `hooks`

# How system identifies extension

[Header Requirements](https://developer.wordpress.org/plugins/plugin-basics/header-requirements/)
```
/**
 * Plugin Name
 *
 * @package           PluginPackage
 * @author            Your Name
 * @copyright         2019 Your Name or Company Name
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Plugin Name
 * Plugin URI:        https://example.com/plugin-name
 * Description:       Description of the plugin.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Your Name
 * Author URI:        https://example.com
 * Text Domain:       plugin-slug
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://example.com/my-plugin/
 */
 // short code for this plugin will be 'osolwpccc'.This line is not mandatory but useful for future reference for developers , while modifying the plugin
```

# Frontend

### Detecting activation

*register_activation_hook and register_deactivation_hook*
[register_activation_hook](https://developer.wordpress.org/reference/functions/register_activation_hook/)
```
/* Main Plugin File */
function my_plugin_activate() {
  add_option( 'Activated_Plugin', 'Plugin-Slug' );
  /* activation code here */
}
register_activation_hook( __FILE__, 'my_plugin_activate' );
//-----------------------------------------------
function load_plugin() {
    if ( is_admin() && get_option( 'Activated_Plugin' ) == 'Plugin-Slug' ) {
        delete_option( 'Activated_Plugin' );
        /* do stuff once right after activation */
        // example: add_action( 'init', 'my_init_function' );
    }
}
add_action( 'admin_init', 'load_plugin' );
```

### Interaction with system

1. *add_action and add_filter*

An `add_action` in WordPress is what you use to create a trigger `hook`. When something happens, then do-something-else.

An `add_filter` is used to `hook` data, i.e. change/replace. For example, where there is [some-code], change it to some-other-expanded-code. 

Hooks are handled in `wp-includes\class-wp-hook.php`

eg:

2. *add_action('init',*
3. *add_shortcode*
4. *wp_enqueue_script and wp_enqueue_style* to load scripts
    1. the `wp_head`, `wp_footer`,`admin_head`,`admin_footer`,`login_head` and `login_footer` action hooks
    2. the `wp_enqueue_scripts` , `admin_enqueue_scripts` , and `login_enqueue_scripts` action hooks
    3. the `wp_enqueue_script()` WordPress function

```
function cccontact_jquery_enqueue() {

	   wp_enqueue_script('jquery');
	   //also initiate thick box
		if(!is_admin()) // thickbox wont load in admin
		{
			//wp_enqueue_script('jquery');
			wp_enqueue_script('thickbox',null,array('jquery'));
			wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
		}
	}
add_action("wp_enqueue_scripts", "cccontact_jquery_enqueue", 11);	
```
5. *Adding custom JS or CSS to `head` tag*
```
function osolwpccc_custom_javascript() {
    ?>
        <script>
          // your javscript code goes here
		  console.log('osolwpccc_custom_javascript')
        </script>
    <?php
}
add_action('wp_head', 'osolwpccc_custom_javascript');
```
6. *Develelopment Prop*

```
// see all functions hooked to 'wp_footer'
add_action('wp', function () {
    echo '<pre>';
    print_r($GLOBALS['wp_filter']['wp_footer']);
    echo '</pre>';
    exit;
});
```

### Display Errors
[WP_Error class](https://developer.wordpress.org/reference/classes/wp_error/)
```
$errors = new WP_Error();
$errors->add('error code', 'detailed error');
```
**System generated Errors** are available in  `add_filter` functions, $errors is passed as an argument, which should be returned back `return($errors);`

### Handling AJAX

[reference](https://developer.wordpress.org/reference/hooks/wp_ajax_action/)
1. wp_ajax_$action 
2. wp_ajax_nopriv_$action

**Example**

```
//***********************************************display captcha hooks 
// depending on wether logged in or not, the following hooks gets triggered when calling url contains action=cccontact_cccontact_display_captcha
add_action('wp_ajax_cccontact_display_captcha', [$OSOLCCC_CommonClass_inst,'cust_captcha_display_captcha']);// executed when logged in
add_action('wp_ajax_nopriv_cccontact_display_captcha', [$OSOLCCC_CommonClass_inst,'cust_captcha_display_captcha']);// executed when logged out
```

### Using DB

[refer](https://developer.wordpress.org/reference/classes/wpdb/)

*DB Class:* `$wpdb`/`$GLOBALS['wpdb']`
*DB Class File:* `wp-includes/wp-db.php`
*Table Prefix:* `$wpdb->prefix`(prefix to table names). 
*Last Insert Id*: `$wpdb->insert_id`
*Affected Rows*: `$wpdb->rows_affected`


*Main Methods:*
1. $wpdb->get_results()
2. $wpdb->query()
3. $wpdb->prepare()
4. *Display Error*
```
<?php 
define( 'DIEONDBERROR', true );
$wpdb->print_error(); 
?>
```

*Example Usages*
```
$results = $GLOBALS['wpdb']->get_results( query, output_type );
```
Where `output_type` is 
One of three pre-defined constants. Defaults to OBJECT.

1. OBJECT – result will be output as an object.
2. ARRAY_A – result will be output as an associative array.
3. ARRAY_N – result will be output as a numerically indexed array.
```
// 1st Method - Declaring $wpdb as global and using it to execute an SQL query statement that returns a PHP object
global $wpdb;
$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}options WHERE option_id = 1", OBJECT );
```
OR
```
<?php
// 2nd Method - Utilizing the $GLOBALS superglobal. Does not require global keyword ( but may not be best practice )
$results = $GLOBALS['wpdb']->get_results( "SELECT * FROM {$wpdb->prefix}options WHERE option_id = 1", OBJECT );
``` 
*Prepared Statements*
```
<?php
$metakey   = 'Funny Phrases';
$metavalue = "WordPress' database interface is like Sunday Morning: Easy.";
$wpdb->query(
   $wpdb->prepare(
      "INSERT INTO $wpdb->postmeta
      ( post_id, meta_key, meta_value )
      VALUES ( %d, %s, %s )",
      10,
      $metakey,
      $metavalue
   )
);
```
The same query using vsprintf()-like syntax. Note that in this example we pack the values together in an array. This can be useful when we don’t know the number of arguments we need to pass until runtime.
```
<?php
$metakey   = 'Funny Phrases';
$metavalue = "WordPress' database interface is like Sunday Morning: Easy.";
$wpdb->query(
   $wpdb->prepare(
   "INSERT INTO $wpdb->postmeta
   ( post_id, meta_key, meta_value )
   VALUES ( %d, %s, %s )",
   array(
         10,
         $metakey,
         $metavalue,
      )
   )
);
```
*Select a variable*

```
<?php $wpdb->get_var( 'query', column_offset, row_offset ); ?> 
```
eg:
```
<?php
$user_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->users" );
echo "<p>User count is {$user_count}</p>";
?>
```
*Get Row*
```	
<?php $wpdb->get_row('query', output_type, row_offset); ?> 
```

*Get Col*
```
get_col( 'query', column_offset ); ?>
```



### Getting User Details

[refer](https://developer.wordpress.org/reference/functions/get_userdata/)
`get_userdata( int $user_id )`
Example
```
<?php $user_info = get_userdata(1);
      echo 'Username: ' . $user_info->user_login . "\n";
      echo 'User roles: ' . implode(', ', $user_info->roles) . "\n";
      echo 'User ID: ' . $user_info->ID . "\n";
?>
```
Another one
```
<?php $user_info = get_userdata(1);
      $username = $user_info->user_login;
      $first_name = $user_info->first_name;
      $last_name = $user_info->last_name;
      echo "$first_name $last_name logs into her WordPress site with the user name of $username.";
	  $display_name = $user_info->display_name;
	  $user_email = $user_info->user_email;
?>
```

### Pagination

[refer](https://developer.wordpress.org/themes/functionality/pagination/)
In posts use the tag `<!--nextpage-->`
*Incorporating Pagination in `Template`*
```
<?php if ( have_posts() ) : ?> 
    <!-- Add the pagination functions here. --> 
    <!-- Start of the main loop. -->
    <?php while ( have_posts() ) : the_post(); ?> 
    <!-- the rest of your theme's main loop --> 
    <?php endwhile; ?>
    <!-- End of the main loop --> 
    <!-- Add the pagination functions here. --> 
<div class="nav-previous alignleft"><?php next_posts_link( 'Older posts' ); ?></div> 
<div class="nav-next alignright"><?php previous_posts_link( 'Newer posts' ); ?></div> 
<?php else : ?> 	
<?php _e('Sorry, no posts matched your criteria.'); ?> 
<?php endif; ?>
```

### Mailing

`wp_mail` function.

### Javascript builtin options availavle

1. Show Modal 

### Relative URLs 

1. plugins_url()

### Multilanguage implementation
Call hook `load_plugin_textdomain` for internationalising. [refer](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/)

```
load_plugin_textdomain('your-plugin-short-code', false, dirname( plugin_basename(__FILE__)).'/languages');
```

For more info [refer](https://wordpress.org/support/article/multilingual-wordpress/)
Wordpress uses [gettext Library](https://www.gnu.org/software/gettext/)
Language files must be saved as `.mo (Machine Object)files` , create .mo files from `.po` files with [poedit](https://poedit.net/download), `.pot` is `PO Template files( POT files are the template files for PO files. They will have all the translation strings left empty)`. So the workflow is `POT (original strings) to PO (original strings and their translation to a specific language) to MO (compiled binary result)`
texts should be called like `echo __( 'WordPress is the best!', 'my-plugin' );`
OR

```
printf(			
			__( 'Your city is %s.', 'my-plugin' ),
			$city
		);
```

If you have more than one placeholder in a string, it is recommended that you use argument swapping. In this case, single quotes (') around the string are mandatory because double quotes (") will tell php to interpret the $s as the s variable, which is not what we want.

```
printf(
			// translators: 1: Name of a city 2: ZIP code 
			__( 'Your city is %1$s, and your zip code is %2$s.', 'my-plugin' ),
			$city,
			$zipcode
		);
```

### Adding 'adsense'

Simplest method is to use `Head, Footer and Post Injections` plugin.
To install,go to Plugins → Add New from your admin dashboard, then searching for `Head, Footer and Post Injections`. 


After you install	
1. Get your adsense id from your adsense account.
2. Settings → Head & Footer Code
3. Add the following code in `footer`

```
<SCRIPT src="js/tagdiv_theme.js" type="text/javascript"></SCRIPT>
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
<script type="text/javascript">
_uacct = "UA-1565652-1";
urchinTracker();
</script>
```

# Backend

### Adding configuration pages

1. /* Hook to initalize the admin menu */
```
add_action('admin_menu',
```

### Additional options to interact with plugin

### Identifying User Levels

1. is_admin()
2. is_super_admin()

# Crons

[refer](https://developer.wordpress.org/plugins/cron/)
 With WP-Cron, all scheduled tasks are put into a queue and will run at the next opportunity (meaning the next page load). So while you can’t be 100% sure when your task will run, you can be 100% sure that it will run eventually.
 
 *To setup cron jobs* , [refer](https://developer.wordpress.org/plugins/cron/understanding-wp-cron-scheduling/)

To prevent WP-Cron on each page load.In `wp-config.php`.
```
define('DISABLE_WP_CRON', true);
```
*Adding the Hook*
[refer](https://developer.wordpress.org/plugins/cron/scheduling-wp-cron-events/)
In order to get your task to run you must create your own custom hook and give that hook the name of a function to execute. This is a very important step. Forget it and your task will never run.
eg:Remember, the `bl_` part of the function name is a `function prefix`(to uniquely identify our plugin's cron tasks ). similar to `wp_ajax_$action` 
```
add_action( 'bl_cron_hook', 'bl_cron_exec' );
```
and schedule cron [refer](https://developer.wordpress.org/plugins/cron/hooking-wp-cron-into-the-system-task-scheduler/)
```
0 0 * * * wget --delete-after http://YOUR_SITE_URL/wp-cron.php
```


### Disable from DB
Use phpMyAdmin to deactivate all plugins with the following steps.
1. In the table wp_options, under the option_name column (field) find the active_plugins row
2. Change the option_value field to: a:0:{}

# Disabling extensions without removing settings

1. Rename `plugins` folder to `plugins.hold`
2. Login to Admin >> Plugins . This will disable all plugins.

# Show "Briefly unavailable for scheduled maintenance. Check back in a minute"

Place a file named `.maintenance` in the blog base folder (folder that contains the wp-admin folder)

# Debugging 

[Debugging](https://wordpress.org/support/article/debugging-in-wordpress/)

in ` wp-config.php`, comment `define( 'WP_DEBUG', false );` 

and add 

```
if(!defined('WP_DEBUG'))
{
	// Enable WP_DEBUG mode
	define( 'WP_DEBUG', true );
	// Enable Debug logging to the /wp-content/debug.log file
	define( 'WP_DEBUG_LOG', true );
	// Disable display of errors and warnings
	define( 'WP_DEBUG_DISPLAY', false );
	@ini_set( 'display_errors', 0 );
	// Use dev versions of core JS and CSS files (only needed if you are modifying these core files)
	define( 'SCRIPT_DEBUG', true );
}
```

# Troubleshooting

[Troubleshooting](https://wordpress.org/support/article/faq-troubleshooting/)


# Templating

