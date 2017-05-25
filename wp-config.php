<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'wkYq4FadYMDVR2Y96N4kDJ6RT50aLVwoWT+swkFbIs6TnjHtBz5qStFtnrZ6psmQva8YME+nMSOmrqJtgEVCrQ==');
define('SECURE_AUTH_KEY',  '5Mu1qEBIqjC82cQk7svx3iza6sjWom4judWb/R+z/CdMm0Tha6a3UpEiTjR7J6BgBTxbe5pE0jmpYhcGSyQqZA==');
define('LOGGED_IN_KEY',    'jGnfqTxauldyPagiJ42UskoUAeY9kD3Erta87K8b7XtAQp80F3kugqm/s/FfPZUOmeAbb5dsniZQW46yP528UA==');
define('NONCE_KEY',        '9v61hFPcZI6+2OuLPmXshq9xdf9PNK0Jlv6oS7uQdEdo+0OGRhS2euIzC8O1tf+ISj7jcUbDI7TvRWS7z7QXYA==');
define('AUTH_SALT',        'A7UitofMur5DAfs/cbufUe4cXmXKK48/mF3IIWWTMW6ZM+yJwqTRYkf37ToA/N9c1Lkn8oCTPXudpMKE7XiedQ==');
define('SECURE_AUTH_SALT', 'ClwHqQF1mWTphOwA4JVPulcPv1tqV23sVJD5/Jc5l1/VuLpT8VbJHmVwRnh2IZ1zqghq4Lb27N1aJceUJ2jOmw==');
define('LOGGED_IN_SALT',   'axy7zDDyOZU/JjuYq5WEYFI6OImnON2mJHUCAUI4RWgANlwMU8yI/Kvcw9ynyG/F8kR0q15iBep3gK/xZ935IQ==');
define('NONCE_SALT',       'EiSE33PFVGrg9+9svcCZY9f8QELaus/IJlufGH6ExiGPur6I1rC8z+Nt4lVjDqSoL5GCAWXXGkeByN0PJ8jDTA==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';





/* Inserted by Local by Flywheel. See: http://codex.wordpress.org/Administration_Over_SSL#Using_a_Reverse_Proxy */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
	$_SERVER['HTTPS'] = 'on';
}
/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
