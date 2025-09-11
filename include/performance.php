<?php
// Performance Optimization Utilities

class Performance {
    
    private static $start_time;
    private static $queries = [];
    private static $cache = [];
    
    // Start performance monitoring
    public static function start() {
        self::$start_time = microtime(true);
        self::$queries = [];
    }
    
    // End performance monitoring
    public static function end() {
        $end_time = microtime(true);
        $execution_time = ($end_time - self::$start_time) * 1000; // Convert to milliseconds
        
        return [
            "execution_time" => round($execution_time, 2),
            "memory_usage" => self::formatBytes(memory_get_usage(true)),
            "peak_memory" => self::formatBytes(memory_get_peak_usage(true)),
            "query_count" => count(self::$queries)
        ];
    }
    
    // Add query to performance log
    public static function logQuery($sql, $execution_time = null) {
        self::$queries[] = [
            "sql" => $sql,
            "execution_time" => $execution_time,
            "timestamp" => microtime(true)
        ];
    }
    
    // Simple caching system
    public static function cache($key, $data, $duration = 300) { // 5 minutes default
        if (!defined('CACHE_ENABLED') || !CACHE_ENABLED) {
            return $data;
        }
        
        $cache_file = SITE_ROOT . DS . 'cache' . DS . md5($key) . '.cache';
        
        // Create cache directory if it doesn't exist
        $cache_dir = SITE_ROOT . DS . 'cache';
        if (!is_dir($cache_dir)) {
            mkdir($cache_dir, 0755, true);
        }
        
        // Check if cache exists and is still valid
        if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $duration) {
            return unserialize(file_get_contents($cache_file));
        }
        
        // Save data to cache
        file_put_contents($cache_file, serialize($data));
        return $data;
    }
    
    // Get cached data
    public static function getCached($key) {
        if (!defined('CACHE_ENABLED') || !CACHE_ENABLED) {
            return false;
        }
        
        $cache_file = SITE_ROOT . DS . 'cache' . DS . md5($key) . '.cache';
        
        if (file_exists($cache_file)) {
            return unserialize(file_get_contents($cache_file));
        }
        
        return false;
    }
    
    // Clear cache
    public static function clearCache($key = null) {
        $cache_dir = SITE_ROOT . DS . 'cache';
        
        if ($key) {
            $cache_file = $cache_dir . DS . md5($key) . '.cache';
            if (file_exists($cache_file)) {
                unlink($cache_file);
            }
        } else {
            // Clear all cache
            if (is_dir($cache_dir)) {
                $files = glob($cache_dir . DS . '*.cache');
                foreach ($files as $file) {
                    unlink($file);
                }
            }
        }
    }
    
    // Format bytes to human readable format
    private static function formatBytes($size, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
    
    // Database query optimization
    public static function optimizeQuery($sql) {
        // Add LIMIT to SELECT queries if not present (safety measure)
        if (preg_match("/^SELECT/i", $sql) && !preg_match("/LIMIT\\s+\\d+/i", $sql)) {
            $sql .= " LIMIT 1000"; // Default limit for safety
        }
        
        return $sql;
    }
    
    // Pagination helper
    public static function paginate($total_items, $items_per_page, $current_page, $url_pattern) {
        $total_pages = ceil($total_items / $items_per_page);
        $current_page = max(1, min($current_page, $total_pages));
        
        $pagination = '<div class="pagination">';
        
        if ($current_page > 1) {
            $pagination .= '<a href="' . str_replace('{page}', $current_page - 1, $url_pattern) . '">&laquo; Previous</a>';
        }
        
        // Show page numbers
        for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++) {
            if ($i == $current_page) {
                $pagination .= '<span class="current">' . $i . '</span>';
            } else {
                $pagination .= '<a href="' . str_replace('{page}', $i, $url_pattern) . '">' . $i . '</a>';
            }
        }
        
        if ($current_page < $total_pages) {
            $pagination .= '<a href="' . str_replace('{page}', $current_page + 1, $url_pattern) . '">Next &raquo;</a>';
        }
        
        $pagination .= '</div>';
        
        return [
            'html' => $pagination,
            'offset' => ($current_page - 1) * $items_per_page,
            'limit' => $items_per_page
        ];
    }
}

// Start performance monitoring if debug is enabled
if (defined('APP_DEBUG') && APP_DEBUG) {
    Performance::start();
}
?>