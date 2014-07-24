<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'db1192991_prop1');

/** MySQL database username */
define('DB_USER', 'u1192991_prop1');

/** MySQL database password */
define('DB_PASSWORD', 'Nugent12#');

/** MySQL hostname */
define('DB_HOST', 'mysql2275int.cp.blacknight.com');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'YFh(Ne#gA6bc-m]]C,D<^hwLc0Zf3t0xa5cxA*H%* Xid?]_=]3#eYbkg%kI# Z)');
define('SECURE_AUTH_KEY',  '|8$P_&69-$yE-s3yS0@skjc-9,;]76>.#k]]&lZ# Ef|a;l{<j9T,XB%!#8F7 Z/');
define('LOGGED_IN_KEY',    'g14uf^9|dghO~kMaC`;#s~#{Q1u5Poa-I5A|HXwXF*IV21bb&egupW23b_Tixa|G');
define('NONCE_KEY',        'iNA@B(Sc?;J%.m&HaN$!W0^qjB]PrB9}|wcI#WvC!tU6u{!tI|G dwfm1-3A+19`');
define('AUTH_SALT',        '&guW;+1#pXnR@qdM0gT}b $-?RiX&l`_+3kRv%XP|WGBkWwDT-Pok1y1|`H)u>s9');
define('SECURE_AUTH_SALT', 'S9z- `qK7mmcK2E*$)ZO6PAI:W:S4iWw-=jf(;K-n7DW{cW8a@_+JY,O}TZY+@o ');
define('LOGGED_IN_SALT',   'I|+/5u+}< al~`of;WQB-zS/ QtVP|3F{T?x+A,yrZxDL$QF`3cD0|92I,E0+jGT');
define('NONCE_SALT',       '[<F+b_.>`F)+vQN] Ncim3-I3IwYiq(/=0%t;$S-J}4r&Cm3+N1C.^VYET$O{N-:');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
