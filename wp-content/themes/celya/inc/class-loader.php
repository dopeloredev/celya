<?php
/**
 * Chargement automatique des classes personnalisées
 * 
 * À ajouter dans functions.php :
 * require_once get_template_directory() . '/inc/functions-loader.php';
 * 
 * @package Celya
 * @since 1.0.0
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Charger les classes personnalisées
 */
function celya_load_custom_classes() {
    
    // Chemin vers le répertoire des classes
    $classes_dir = get_template_directory() . '/inc/classes/';
    
    // Liste des classes à charger
    $classes = array(
        'class-celya-walker-desktop-nav-menu.php',     

        // Ajoutez d'autres classes ici si nécessaire
        //'class-celya-walker-mobile-nav-menu.php',
    );
    
    // Charger chaque classe
    foreach ($classes as $class_file) {
        $file_path = $classes_dir . $class_file;
        
        if (file_exists($file_path)) {
            require_once $file_path;
        } else {
            // Log l'erreur en mode debug
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Celya - Classe introuvable : ' . $file_path);
            }
        }
    }
}
add_action('after_setup_theme', 'celya_load_custom_classes');

/**
 * Autoloader PSR-4 (optionnel, pour une approche plus moderne)
 * 
 * Permet de charger automatiquement les classes selon leur namespace
 */
spl_autoload_register(function ($class) {
    
    // Namespace de base pour vos classes
    $namespace = 'Celya\\';
    
    // Vérifier si la classe appartient à notre namespace
    if (strpos($class, $namespace) !== 0) {
        return;
    }
    
    // Retirer le namespace de base
    $class = str_replace($namespace, '', $class);
    
    // Convertir le nom de classe en chemin de fichier
    $class = str_replace('\\', '/', $class);
    $file = get_template_directory() . '/inc/classes/class-' . strtolower($class) . '.php';
    
    // Charger le fichier s'il existe
    if (file_exists($file)) {
        require_once $file;
    }
});