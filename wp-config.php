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
define('AUTH_KEY',         '8cqx7EnB/GqepkZkEzmjs2DUo4eIGw9M91OnOaSwfTFGjqvc3hHb8PzatOl30mvDRwXQKmUVpDn/FRmKVaTdZw==');
define('SECURE_AUTH_KEY',  'c+p9htZJQ1mloKO3SGme4qKAhvGduQIOuq9yKhrWAhFohIr1VwUffCiSiSF3h9XjR2AKcLkXaxkbXvmnH92crg==');
define('LOGGED_IN_KEY',    'zF1m9vEpZ4G30tkOqpODcKOmp7+QQFl3usnizs3k4DpDbjZ+1De1wtLFrfu19Kvs48Nj1JG0piTIQSJDatQ2gA==');
define('NONCE_KEY',        'b13T/iyyu3ZQ+5ql+0YS6nDoAMbRcO+7eZdYR9hKj2bUlmEKhiVK7fBH16S75eWQxk4J+0SFLEmcFKnMTKy0+g==');
define('AUTH_SALT',        'qfiK7sUZ0cltoCsmAw1NQltRBfhl7UAqs8JWDTx2DOqLDJoPP/gOzMaTRTzB47yNtpDUBSu4U5MEmfCwQAElAA==');
define('SECURE_AUTH_SALT', '8uLOIg+CIbOlMZkmf/L8Dt8DpNkGoVlXKpz2SWkwE8Y7Z1/r6c5s3zulQJ8X6uwBC85PkCszLFjf27PG7+xeGQ==');
define('LOGGED_IN_SALT',   'zxFtcOE/Qh97S1QE+FT9YnvUEQOKB9wY+2Pv6SSgBrbFs3pEPf9O7SAywS3bSzC/aYAy9/z0ynbVMdOG7V5ILg==');
define('NONCE_SALT',       'hI9zpkRPQRbAKU3efeN9D2a8l/KTff4HcfibmWooD9n0uDldFzzv/nhnhapiUxrFEF0zGjzRgGtLZp5l2yKWcw==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_a39g9wda1k_';





/* Inserted by Local by Flywheel. See: http://codex.wordpress.org/Administration_Over_SSL#Using_a_Reverse_Proxy */
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
	$_SERVER['HTTPS'] = 'on';
}

/* Inserted by Local by Flywheel. Fixes $is_nginx global for rewrites. */
if ( ! empty( $_SERVER['SERVER_SOFTWARE'] ) && strpos( $_SERVER['SERVER_SOFTWARE'], 'Flywheel/' ) !== false ) {
	$_SERVER['SERVER_SOFTWARE'] = 'nginx/1.10.1';
}
/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
