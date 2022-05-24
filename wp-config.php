<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'enjoyee_db' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'w-y*%Jvvl^FXgY:ta*<gL5P2R}>]@gnjr.IC!NC=YlAr Cw2d3dsRj^ozC)FDnM<' );
define( 'SECURE_AUTH_KEY',  ']I/L720#v+)2.s;!3C<(~U`LmN/@bBsMs0;n$M~S^)d;`]eHbk$}+XGa[g: ]!~J' );
define( 'LOGGED_IN_KEY',    '+#L;#-Y75`phaMXy5VaJZ?8*Bce@4mhVVZL~YddSOZ.%cm3`GU=()hLT!q3C0DM(' );
define( 'NONCE_KEY',        '/>f_-B?Ogye,SL7;-/HA)xe)yCggW,L/n_Lc#g?,9Uv/,i;[bxgKOc9Q%?1XYgc1' );
define( 'AUTH_SALT',        ' {>iX,KS^UZ706~tEMK3]8HPe$ %H)UNpEQ8wm-Eh!2>SWXLdGAg]Xe+DUn(?OWT' );
define( 'SECURE_AUTH_SALT', 'iVYf3>]lQRnd33}q:6BY}NaVBV.-u5Hn{4zauBejLr8[>xErsDxm4vQbcS3NUU W' );
define( 'LOGGED_IN_SALT',   '*{`7UT&qG?#mEd`C{U0tD>nB7n&bHE(.w4hQ6a;+l4VNRfv~L?QctT;Cvs8nM}{H' );
define( 'NONCE_SALT',       '1,3Rrx+ 5@ldzFk`WPFP;_dJW29vYA=+ys9G,/q~8)J4+lSvLaBWtKNfeZKVw7>~' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
