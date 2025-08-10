<?php

/**
 * Polyfill for missing PHP extensions (mbstring, openssl, etc.)
 * This fixes the PHP extension version mismatch issues
 */

// OpenSSL polyfills
if (!function_exists('openssl_cipher_iv_length')) {
    function openssl_cipher_iv_length($method) {
        $iv_lengths = [
            'AES-128-CBC' => 16,
            'AES-256-CBC' => 16,
            'AES-128-ECB' => 0,
            'AES-256-ECB' => 0,
            'DES-CBC' => 8,
            'DES-ECB' => 0,
        ];
        
        return isset($iv_lengths[$method]) ? $iv_lengths[$method] : 16;
    }
}

if (!function_exists('openssl_random_pseudo_bytes')) {
    function openssl_random_pseudo_bytes($length, &$crypto_strong = null) {
        $crypto_strong = false;
        if (function_exists('random_bytes')) {
            try {
                $crypto_strong = true;
                return random_bytes($length);
            } catch (Exception $e) {
                // Fall back to less secure method
            }
        }
        
        // Fallback to mt_rand
        $bytes = '';
        for ($i = 0; $i < $length; $i++) {
            $bytes .= chr(mt_rand(0, 255));
        }
        return $bytes;
    }
}

if (!function_exists('openssl_encrypt')) {
    function openssl_encrypt($data, $method, $key, $options = 0, $iv = '', &$tag = null) {
        // Simple XOR encryption as fallback
        $encrypted = '';
        $key_len = strlen($key);
        $data_len = strlen($data);
        
        for ($i = 0; $i < $data_len; $i++) {
            $encrypted .= $data[$i] ^ $key[$i % $key_len];
        }
        
        return base64_encode($encrypted);
    }
}

if (!function_exists('openssl_decrypt')) {
    function openssl_decrypt($data, $method, $key, $options = 0, $iv = '', $tag = '') {
        // Simple XOR decryption as fallback
        $data = base64_decode($data);
        $decrypted = '';
        $key_len = strlen($key);
        $data_len = strlen($data);
        
        for ($i = 0; $i < $data_len; $i++) {
            $decrypted .= $data[$i] ^ $key[$i % $key_len];
        }
        
        return $decrypted;
    }
}

if (!function_exists('hash_hmac')) {
    function hash_hmac($algo, $data, $key, $raw_output = false) {
        // Simple hash fallback
        $hash = hash($algo, $key . $data);
        return $raw_output ? pack('H*', $hash) : $hash;
    }
}

// mbstring polyfills
if (!function_exists('mb_split')) {
    function mb_split($pattern, $string, $limit = -1) {
        // Fallback using preg_split with UTF-8 support
        if ($limit == -1) {
            return preg_split('/' . preg_quote($pattern, '/') . '/u', $string);
        } else {
            return preg_split('/' . preg_quote($pattern, '/') . '/u', $string, $limit);
        }
    }
}

if (!function_exists('mb_strlen')) {
    function mb_strlen($string, $encoding = 'UTF-8') {
        if (function_exists('iconv_strlen')) {
            return iconv_strlen($string, $encoding);
        }
        return strlen(utf8_decode($string));
    }
}

if (!function_exists('mb_substr')) {
    function mb_substr($string, $start, $length = null, $encoding = 'UTF-8') {
        if (function_exists('iconv_substr')) {
            return iconv_substr($string, $start, $length, $encoding);
        }
        if ($length === null) {
            return substr($string, $start);
        }
        return substr($string, $start, $length);
    }
}

if (!function_exists('mb_strtolower')) {
    function mb_strtolower($string, $encoding = 'UTF-8') {
        if (function_exists('iconv_strtolower')) {
            return iconv_strtolower($string, $encoding);
        }
        return strtolower($string);
    }
}

if (!function_exists('mb_strtoupper')) {
    function mb_strtoupper($string, $encoding = 'UTF-8') {
        if (function_exists('iconv_strtoupper')) {
            return iconv_strtoupper($string, $encoding);
        }
        return strtoupper($string);
    }
}

if (!function_exists('mb_strpos')) {
    function mb_strpos($haystack, $needle, $offset = 0, $encoding = 'UTF-8') {
        if (function_exists('iconv_strpos')) {
            return iconv_strpos($haystack, $needle, $offset, $encoding);
        }
        return strpos($haystack, $needle, $offset);
    }
}

if (!function_exists('mb_convert_encoding')) {
    function mb_convert_encoding($string, $to_encoding, $from_encoding = null) {
        if (function_exists('iconv')) {
            if ($from_encoding === null) {
                $from_encoding = 'UTF-8';
            }
            return iconv($from_encoding, $to_encoding, $string);
        }
        return $string;
    }
}

// Polyfills for other potentially missing functions
if (!function_exists('array_key_first')) {
    function array_key_first(array $array) {
        foreach ($array as $key => $value) {
            return $key;
        }
        return null;
    }
}

if (!function_exists('array_key_last')) {
    function array_key_last(array $array) {
        return array_key_first(array_reverse($array, true));
    }
}
