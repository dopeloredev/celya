<?php
/**
 * Walker Menu personnalisé pour Celya
 * Gère les sous-menus déroulants avec Tailwind CSS
 * 
 * @package Celya
 * @since 1.0.0
 */

// Sécurité : empêcher l'accès direct au fichier
if (!defined('ABSPATH')) {
    exit;
}

class Celya_Walker_Nav_Menu extends Walker_Nav_Menu {
    
    /**
     * Commence le niveau (ul)
     */
    function start_lvl(&$output, $depth = 0, $args = null) {
        if ($depth === 0) {
            // Sous-menu de premier niveau
            $output .= '<ul class="absolute left-0 top-full mt-0 w-48 bg-white shadow-lg rounded-b-lg py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">';
        } else {
            // Sous-menu de second niveau (si nécessaire)
            $output .= '<ul class="submenu-level-2">';
        }
    }
    
    /**
     * Termine le niveau (ul)
     */
    function end_lvl(&$output, $depth = 0, $args = null) {
        $output .= '</ul>';
    }
    
    /**
     * Commence l'élément (li)
     */
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $has_children = in_array('menu-item-has-children', $classes);
        
        // Classes pour le <li>
        if ($depth === 0) {
            // Menu principal
            $li_class = 'relative group';
        } else {
            // Sous-menu
            $li_class = '';
        }
        
        $output .= '<li class="' . $li_class . '">';
        
        // Classes pour le <a>
        if ($depth === 0) {
            // Lien principal avec effet de soulignement au survol
            $link_class = 'relative block font-sans text-sm font-medium uppercase tracking-wide text-celya-dark hover:text-celya-orange_dark transition-colors pb-1';
            
            // Ajouter l'effet de soulignement
            $link_before = '<span class="relative inline-block">';
            $link_after = '<span class="absolute bottom-0 left-0 w-0 h-0.5 bg-celya-orange_dark transition-all duration-300 group-hover:w-full"></span></span>';
        } else {
            // Lien sous-menu
            $link_class = 'block px-4 py-2 text-sm font-sans text-celya-dark hover:bg-celya-orange_light hover:text-celya-orange_dark transition-colors';
            $link_before = '';
            $link_after = '';
        }
        
        // Attributs du lien
        $attributes = '';
        $attributes .= !empty($item->url) ? ' href="' . esc_url($item->url) . '"' : '';
        $attributes .= !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= ' class="' . $link_class . '"';
        
        // Construire le lien
        $item_output = '<a' . $attributes . '>';
        $item_output .= $link_before;
        $item_output .= apply_filters('the_title', $item->title, $item->ID);
        $item_output .= $link_after;
        $item_output .= '</a>';
        
        $output .= $item_output;
    }
    
    /**
     * Termine l'élément (li)
     */
    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= '</li>';
    }
}