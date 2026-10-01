<?php

namespace NimbusDesk\Core;

/**
 * Class View
 * Handles loading and rendering of frontend templates and layouts.
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
}
