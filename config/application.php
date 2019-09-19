<?php
/**
 * Your base production configuration goes in this file. Environment-specific
 * overrides go in their respective config/environments/{{WP_ENV}}.php file.
 *
 * A good default policy is to deviate from the production config as little as
 * possible. Try to define as much of your configuration in this file as you
 * can.
 */

use Roots\WPConfig\Config;

/** @var string Directory containing all of the site's files */
$root_dir = dirname(__DIR__);

/** @var string Document Root */
$webroot_dir = $root_dir . '/public';

/**
 * Expose global env() function from oscarotero/env
 */
Env::init();

/**
 * Use Dotenv to set required environment variables and load .env file in root
 */
$dotenv = Dotenv\Dotenv::create($root_dir);
if (file_exists($root_dir . '/.env')) {
    $dotenv->load();
    $dotenv->required(['WP_HOME', 'WP_SITEURL']);
    if (!env('DATABASE_URL')) {
        $dotenv->required(['DB_NAME', 'DB_USER', 'DB_PASSWORD']);
    }
}

/**
 * Set up our global environment constant and load its config first
 * Default: production
 */
define('WP_ENV', env('WP_ENV') ?: 'production');


/**
 * Allow WordPress to detect HTTPS when used behind a reverse proxy or a load balancer
 * @see https://codex.wordpress.org/Function_Reference/is_ssl#Notes
 */
if (array_key_exists('HTTP_X_FORWARDED_PROTO', $_SERVER) && $_SERVER["HTTP_X_FORWARDED_PROTO"] == 'https') {
    $_SERVER['HTTPS'] = 'on';
}

$_http_schema = array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
$_http_name   = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_site_url    = $_http_schema."://".$_http_name;

Config::define('WP_HOME',     env('WP_HOME') ?: $_site_url);
Config::define('WP_SITEURL',  env('WP_SITEURL') ?: $_site_url);

/**
 * Custom Content Directory
 */
Config::define('CONTENT_DIR',       '/app');
Config::define('WP_CONTENT_DIR',    $webroot_dir . Config::get('CONTENT_DIR'));
Config::define('WP_CONTENT_URL',    Config::get('WP_HOME') . Config::get('CONTENT_DIR'));
Config::define('WP_DEFAULT_THEME',  env('WP_DEFAULT_THEME') ?: 'wpbp');


/**
 * DB settings
 */
Config::define('DB_NAME',      env('DB_NAME'));
Config::define('DB_USER',      env('DB_USER'));
Config::define('DB_PASSWORD',  env('DB_PASSWORD'));
Config::define('DB_HOST',      env('DB_HOST') ?: 'localhost');
Config::define('DB_CHARSET',   'utf8mb4');
Config::define('DB_COLLATE',   '');
$table_prefix = env('DB_PREFIX') ?: 'wp_';

/**
 * ClearDB config on Heroku
 */
if (env('CLEARDB_DATABASE_URL')) {
    putenv(sprintf('DATABASE_URL=%s', env('CLEARDB_DATABASE_URL')));
}

if (env('DATABASE_URL')) {
    $dsn = (object) parse_url(env('DATABASE_URL'));

    Config::define('DB_NAME',      substr($dsn->path, 1));
    Config::define('DB_USER',      $dsn->user);
    Config::define('DB_PASSWORD',  $dsn->pass ?? null);
    Config::define('DB_HOST',      isset($dsn->port) ? "{$dsn->host}:{$dsn->port}" : $dsn->host);
}

/**
 * Authentication Unique Keys and Salts
 */
Config::define('AUTH_KEY',          env('AUTH_KEY'));
Config::define('SECURE_AUTH_KEY',   env('SECURE_AUTH_KEY'));
Config::define('LOGGED_IN_KEY',     env('LOGGED_IN_KEY'));
Config::define('NONCE_KEY',         env('NONCE_KEY'));
Config::define('AUTH_SALT',         env('AUTH_SALT'));
Config::define('SECURE_AUTH_SALT',  env('SECURE_AUTH_SALT'));
Config::define('LOGGED_IN_SALT',    env('LOGGED_IN_SALT'));
Config::define('NONCE_SALT',        env('NONCE_SALT'));

/**
 * Custom Settings
 */
Config::define('DISABLE_WP_CRON',     env('DISABLE_WP_CRON') ?: false);
// Disable the plugin and theme file editor in the admin
Config::define('DISALLOW_FILE_EDIT',  true);
// Disable plugin and theme updates and installation from the admin
Config::define('DISALLOW_FILE_MODS',  true);

/**
 * Multisite Settings
 */
// Config::define('WP_ALLOW_MULTISITE', env('WP_ALLOW_MULTISITE'));
$multisite = env('MULTISITE') ?: false;

if ($multisite) {
    Config::define('MULTISITE', $multisite);
    Config::define('SUBDOMAIN_INSTALL',    env('SUBDOMAIN_INSTALL') ?: false);
    Config::define('DOMAIN_CURRENT_SITE',  env('DOMAIN_CURRENT_SITE'));
    Config::define('PATH_CURRENT_SITE',    env('PATH_CURRENT_SITE') ?: '/');
    Config::define('SITE_ID_CURRENT_SITE', env('SITE_ID_CURRENT_SITE') ?: 1);
    Config::define('BLOG_ID_CURRENT_SITE', env('BLOG_ID_CURRENT_SITE') ?: 1);
}

if (file_exists($env_config = __DIR__ . '/environments/' . WP_ENV . '.php')) {
    require_once $env_config;
}

Config::apply();

/**
 * Bootstrap WordPress
 */
if (!defined('ABSPATH')) {
    define('ABSPATH', $webroot_dir . '/wp/');
}
