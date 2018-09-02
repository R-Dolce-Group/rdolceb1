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
define('AUTH_KEY',         'qNCqYbJ1KxN+XHHJpuKlmGg0G7/iJ48L7zeUFzaZPGtJVOVuQbYxJeiVGR/39rUqXjCUub5SQl4tWSbrMGS9sg==');
define('SECURE_AUTH_KEY',  '0tZsOjR0I6y/XXiUOhvZOOD7nMRzdSN3KN40LTWdRZzdrDc/5FAhgSkmFD2ACbwrHrmQqT4pZGebjsmtl/zqhg==');
define('LOGGED_IN_KEY',    'PWLN5YaxAokbMowqtzfOjtfLQTKKvxjx4rd6J+AtJKWkpQXqGtU4wuFhSthqXUac905xty6THg0s19FClybeUA==');
define('NONCE_KEY',        'BuGj+l5hNRbPA/1kqR9TyRfk4ErDfUW1YOpBOkoVPt4zqoq3QF9yVjnL070Tr7r1l0vFMEuhpcc/1dCKAb3lcA==');
define('AUTH_SALT',        '83AVuoxsVCRO6UDq2sMn+GEqetvv9pJpl4KCBYB/6U+cA13grZmKSyJiJEfdCW0U7rIyUajuf/4ZZz9CiIjZBQ==');
define('SECURE_AUTH_SALT', 'slL/w6smblss+bpph3xRl+0TY45EAepKhEgIcPIZSXeymuFkm4b6HfVaRbVPC8FT0M7AaITzy9u+mrXpzx61MQ==');
define('LOGGED_IN_SALT',   'BDxHIhzRLdHPK7i65rGTHYbDzOld+lT79C/QpYxg6S7GFVyNEyIBYirwsGqL82k7lPrZ0D8vztYNJEnqLyAOyA==');
define('NONCE_SALT',       '6pKE/t1EcarHPFRd0/JKeeXBUFiT379IgOQ6IKNDUQoh3onHwi8045AHSoGjSAv/hoazJWABDC0jL7CgpiGG7Q==');

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
