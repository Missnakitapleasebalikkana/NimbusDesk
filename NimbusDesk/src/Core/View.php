<?php

namespace NimbusDesk\Core;

/**
 * Class View
 * Handles loading and rendering of frontend templates, layouts, and offline avatars.
 */
class View {
    /**
     * Renders a specific view file within a layout.
     *
     * @param string $viewPath The path to the view relative to the views directory (e.g., 'employee/dashboard')
     * @param string $layout The layout to wrap the view in (e.g., 'employee')
     * @param array $data Data to be extracted and made available to the view
     */
    public static function render($viewPath, $layout = 'main', $data = []) {
        // Extract data to variables
        if (!empty($data)) {
            extract($data);
        }

        // Start output buffering for the view content
        ob_start();
        $fullViewPath = __DIR__ . '/../../views/' . $viewPath . '.php';
        
        if (file_exists($fullViewPath)) {
            require $fullViewPath;
        } else {
            echo "<h1>View not found: {$viewPath}</h1>";
        }
        
        $content = ob_get_clean();

        // Load the layout and inject the content
        $layoutPath = __DIR__ . '/../../views/layouts/' . $layout . '.php';
        if (file_exists($layoutPath)) {
            require $layoutPath;
        } else {
            // Fallback if no layout
            echo $content;
        }
    }

    /**
     * Generate a 100% offline SVG Data URI avatar with user initials.
     *
     * @param string $name User full name
     * @param string $bg Hex background color without '#'
     * @param string $color Hex text color without '#'
     * @return string Data URI ready for <img src="...">
     */
    public static function avatarUrl(string $name, string $bg = 'f191b5', string $color = '601a35'): string {
        $cleanName = trim($name);
        $words = preg_split('/\s+/', $cleanName);
        $initials = '';
        foreach ($words as $w) {
            if (!empty($w)) {
                $initials .= mb_strtoupper(mb_substr($w, 0, 1));
                if (mb_strlen($initials) >= 2) {
                    break;
                }
            }
        }
        if (empty($initials)) {
            $initials = 'ND';
        }

        $bg = ltrim($bg, '#');
        $color = ltrim($color, '#');

        $svg = "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'>"
             . "<circle cx='50' cy='50' r='50' fill='#{$bg}'/>"
             . "<text x='50%' y='55%' text-anchor='middle' dominant-baseline='central' font-family='Segoe UI, Tahoma, sans-serif' font-weight='bold' font-size='38' fill='#{$color}'>{$initials}</text>"
             . "</svg>";

        return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
    }
}
