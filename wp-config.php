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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'carnegie' );

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
define( 'AUTH_KEY',         '@*{P.T+G+]@)0XXHo;kDA%=U=IC]O]HppEY4R_pFzuFNA5|8/^p]>eV}YGZ9##R$' );
define( 'SECURE_AUTH_KEY',  'Y$a4cgJk*1Oju4ba^-Cjto`;H.dmE})4&$oQ?gG)rY%[HMVB5K}<x~|<390lYOPa' );
define( 'LOGGED_IN_KEY',    'KT{xT%Yasl9Q;,qT #M_YRs}L23Y}*28(;com#Li8iY5O#DW*I%i~N/Q&Etd1Vh&' );
define( 'NONCE_KEY',        '6u*VAih$)al~$WbH4,C.25aI ?BB%()X3J@E>yD0eBWfJ;O!Z)M1Ki[JXIHE]ZOy' );
define( 'AUTH_SALT',        'R%qv}`B%U$*kI~0-OJoFKy?Sp.ba[jV=) 8,B4L)nkG9Z5#(H{fAz+aQHP[cBmhd' );
define( 'SECURE_AUTH_SALT', '.}!_Q8,ah*Btn8snxsA~ #bDT#U^jt 2sSa7g/|l6AIT*cx_{A-tq5%3JBLq& @R' );
define( 'LOGGED_IN_SALT',   '8N*7VRa/PmmdE{A>0Ggx-}%%/`Fd }-~/TOqc_`s;9QKSr~S$+DI]iID4,CV2]&W' );
define( 'NONCE_SALT',       'd:8s~E26t-Yc*7#1#?_ejoh>N`AayS0OB_$N}M<BgZ*BW9JMLy;K 3p46YT:-G4T' );

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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
