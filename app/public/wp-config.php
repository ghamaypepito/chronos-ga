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

// ** MySQL settings - You can get this info from your web host ** //
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
define('AUTH_KEY',         'uE4uFouXlC7giW0RaAbmbX72MUy4krVdehIXFL4gFkfMO+7rNVTEK9hhPRhZI6tTAdumsO23BhgOll8qZ6Vmyw==');
define('SECURE_AUTH_KEY',  '0mrLfEgOD4OHuDobqss/xC+wUnTJ98eFvAcMPJ9Jb331OgU2ytdgbJUSlREVQGhilKr2G1y87sI3GLo2O15Y7Q==');
define('LOGGED_IN_KEY',    'FXW3ksx/6uJoZaUxAmsGGNq9U1wFh3Ew7z9vr0Y+LtRYna0vwRex8zkRaRFrwA+tgI1paJlxmg1THEtemQxV/w==');
define('NONCE_KEY',        'bhVT9DtvKYMHthsADD/TwohhW+yNuaL6u2jeCn7NawvoEQYf3rRqgPsKnXOzP+LkdTIdR1pE9cW7/UlWX99o9g==');
define('AUTH_SALT',        'Mgpf4mG1UGH04AdSAcUEiRyxvb9AQxd3ZUh6Kyxn1hjVSBiyGdxGUs2LqNNFZAcpGNgEhbSw7MQG+Bo2uSSB9Q==');
define('SECURE_AUTH_SALT', 'EtTc22NZNKTP0wPwhkbbsVPDgpHOsNjXrHfrjXLGZ4M2knRVOY5LpZtBNaUZiCVMrcxT/B5LAInM9tynFqZH7w==');
define('LOGGED_IN_SALT',   '16F5tdB1SiJIkK37EsRId49pa2nykXRyhcut4jei7DMd2QYJEiduiZhwctzYBf8eMVjdCANLzCHQ0edgOycdhA==');
define('NONCE_SALT',       'hwK1AgsT1dUnv7efaN85kP6QYaKBde8+cgxe1pX6IyVHZQbHNDSEAc7j1Cs1vO7LA2A1ESHFR1hx5SmvTVsGLg==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
