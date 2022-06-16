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
define( 'DB_NAME', 'feed_me_more' );

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
define( 'AUTH_KEY',         'NhY#9/R5[?MnFF]tS,MHt$Il$Bvf=-_$/Q;fqCSlobXkv?!@*9x|c II5F2ok<zT' );
define( 'SECURE_AUTH_KEY',  '0-N?>$q&J6w4cW60mQuz NNp;(6[X6$,dH%M(`b ymw7lYxkvF`5ufK #}i;zcPz' );
define( 'LOGGED_IN_KEY',    'jKitqZ8/El8z[Vp6YZ4MyZAhdVI&dgsX {*a.I-2*2LXA0MGLK%zE =Ggxp>GQwC' );
define( 'NONCE_KEY',        '~f?z*o:iH9b[~:;DnPwo6+YF-.wHycvX(6iRp^+i;hMfAPB ZIvMR?{wg~=viK,E' );
define( 'AUTH_SALT',        'x2QLQb>VaBlO&|M+?8js#Ctx2*wNC:rn;)Zj}l4.B9WO[1Xc{E(Hp6S,y3a kdkX' );
define( 'SECURE_AUTH_SALT', '$i2{L8 vf%)|>t#iD8.pSnx;$Y1$De46/2nU:vWFU/XM{vj?zOY-oC}:y.6;*[ !' );
define( 'LOGGED_IN_SALT',   'OVwIqYFz[YAaHl^S4tNh2foj(<KKf[IUR  A05()(K[/|gG)=v^223y+tc1,NTiy' );
define( 'NONCE_SALT',       'B0UZY~/K,Z#A!,cFkSOA=RV[hj9pTfEG6Oe ,:ZR+h, JPBON*2d.7I[{{>b6}&)' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'fmm_';

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
