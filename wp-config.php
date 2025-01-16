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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'd;q(`DLZ;>=Auzb&YLT4{>wG,~S7f,TwP!1mjTC3USzYl$O.[85mWdlOAa^j~s$1' );
define( 'SECURE_AUTH_KEY',   'Wp<<~R4|v|MJ*:k)vCCC@O3.}G(XZL#{^pnacscLupTODcr &.z|{qHzD>? fJr=' );
define( 'LOGGED_IN_KEY',     '(ao}HF/Uhvsm06;P8/ 7o,_1i$xAwk2Ff,;nK6 ^Ry?KgQ?iL*o7c9.5[&@|N6W[' );
define( 'NONCE_KEY',         'tpyh{SEnl?.I&a1Y=^Kbsr<m1ypTr/n *7h0GWbU* INUrW*-t#=kw)&(S#pM/V~' );
define( 'AUTH_SALT',         '5P8lSt0PS|nK]}yr %J<3O?Kv{]9^|D;TB}4Afsz6c&=2M()_(nYZ)><I3 |4/5j' );
define( 'SECURE_AUTH_SALT',  '^-*&E@Von?t5YgMCPtm0J_khd/!2i<O?Y{+2!~OJMUR-D@ZC|PN3.;w%$r*8XmZT' );
define( 'LOGGED_IN_SALT',    'V=.2ds)aR1}$BD+P6EdiRZO_3`w&(%7>SrwL-HhAXl9k -<oI(Cb{tL,[w5Yq<k7' );
define( 'NONCE_SALT',        's*j^ibtuG=8^/j%jLp5Zw?OFUStRP4jZrY{^:aP9Q0e/91x g||UyrgKQrS+s8CG' );
define( 'WP_CACHE_KEY_SALT', 'Ca*>ch:$f#5z/DlsG%?=w(uCC@Uc8j1^~AMQ7ls!bZ*3ZH9}1E1BAv/xg@K_mS5)' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpu2_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
