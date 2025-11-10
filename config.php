<?php
/**
 * Configuration Loader for Saffron Restaurant Website
 * 
 * This file securely loads environment variables from .env file
 * and provides configuration values to the application.
 * 
 * Security: Never commit .env file to version control
 * 
 * @package SaffronRestaurant
 * @version 1.0
 */

// Prevent direct access to config file
if (!defined('SAFFRON_CONFIG_LOADED')) {
    define('SAFFRON_CONFIG_LOADED', true);
}

/**
 * Load environment variables from .env file
 * 
 * @param string $envFile Path to .env file
 * @return bool True if loaded successfully, false otherwise
 */
function loadEnvFile($envFile = __DIR__ . '/.env') {
    if (!file_exists($envFile)) {
        return false;
    }
    
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE format
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            $value = trim($value, '"\'');
            
            // Set environment variable if not already set
            if (!empty($key) && !getenv($key)) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
    
    return true;
}

// Load .env file
$envLoaded = loadEnvFile();

/**
 * Get configuration value from environment
 * 
 * @param string $key Configuration key
 * @param mixed $default Default value if key not found
 * @return mixed Configuration value or default
 */
function getConfig($key, $default = null) {
    // Try different methods to get environment variable
    $value = getenv($key);
    
    if ($value === false) {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
    
    return $value;
}

/**
 * Get reCAPTCHA secret key securely
 * 
 * @return string Secret key or empty string if not configured
 * @throws Exception If secret key is not configured
 */
function getRecaptchaSecret() {
    $secret = getConfig('RECAPTCHA_SECRET_KEY');
    
    if (empty($secret) || $secret === 'your_recaptcha_secret_key_here') {
        // Log error but don't expose details
        error_log('reCAPTCHA secret key not configured. Please set RECAPTCHA_SECRET_KEY in .env file.');
        throw new Exception('reCAPTCHA configuration error. Please contact the administrator.');
    }
    
    return $secret;
}

/**
 * Get reCAPTCHA site key (for frontend)
 * 
 * @return string Site key or empty string if not configured
 */
function getRecaptchaSiteKey() {
    return getConfig('RECAPTCHA_SITE_KEY', '');
}

/**
 * Validate that required configuration is loaded
 * 
 * @return bool True if configuration is valid
 */
function validateConfig() {
    try {
        $secret = getRecaptchaSecret();
        return !empty($secret);
    } catch (Exception $e) {
        return false;
    }
}

// Validate configuration on load
if (!$envLoaded) {
    // Log warning but don't break the site
    error_log('Warning: .env file not found. Using default configuration.');
}

// Set error reporting based on environment
$displayErrors = getConfig('DISPLAY_ERRORS', '0');
$errorReporting = getConfig('ERROR_REPORTING', '0');

ini_set('display_errors', $displayErrors);
error_reporting($errorReporting === '0' ? 0 : E_ALL);

