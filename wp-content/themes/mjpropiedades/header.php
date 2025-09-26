<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php 
        if (is_home() || is_front_page()) {
            echo 'Corredora de Propiedades en La Serena y Coquimbo | Home Isa Propiedades';
        } else {
            wp_title('|', true, 'right');
            bloginfo('name');
        }
    ?></title>
    <meta name="description" content="<?php 
        if (is_home() || is_front_page()) {
            echo 'Especialistas en venta y arriendo en la Región de Coquimbo. Encuentra tu casa o departamento en La Serena, Coquimbo y Ovalle. Asesoría experta y certificada.';
        } else {
            echo get_bloginfo('description');
        }
    ?>">
    <meta name="keywords" content="corredora de propiedades, La Serena, Coquimbo, Región de Coquimbo, venta de casas, arriendo departamentos, propiedades Ovalle, inmobiliaria La Serena">
    <?php wp_head(); ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Dancing+Script:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style.css?v=<?php echo time(); ?>">
</head>
<body <?php body_class(); ?>>

<!-- Header -->
<header class="header">
    <div class="header-container">
        <?php 
        // Usar el logo personalizado de WordPress Admin
        if (has_custom_logo()) {
            // Logo configurado en WordPress Admin
            the_custom_logo();
        } else {
            // Fallback: Logo Plus Propiedades si no hay logo configurado
            echo '<a href="' . home_url() . '" class="logo-plus-propiedades">';
            echo '<span class="logo-plus">PLUS</span>';
            echo '<span class="logo-propiedades">PROPIEDADES</span>';
            echo '</a>';
        }
        ?>
        
        <!-- Menú Desktop -->
        <nav class="nav nav-desktop nav-<?php echo get_theme_mod('mjpropiedades_menu_alignment', 'right'); ?>">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_class'     => 'nav-menu',
                'container'      => false,
                'fallback_cb'    => 'mjpropiedades_fallback_menu',
            ));
            ?>
        </nav>
        
        <a href="#contacto" class="contact-btn contact-btn-desktop">Contactar</a>
        
        <!-- Botón Hamburger Móvil -->
        <button class="mobile-menu-toggle" aria-label="Abrir menú">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>
    </div>
    
    <!-- Menú Móvil -->
    <nav class="mobile-nav">
        <div class="mobile-nav-content">
            <!-- Botón de cerrar -->
            <button class="mobile-menu-close" aria-label="Cerrar menú" onclick="closeMobileMenuFromButton(event)">
                <span class="close-icon">×</span>
            </button>
            
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_class'     => 'mobile-nav-menu',
                'container'      => false,
                'fallback_cb'    => 'mjpropiedades_mobile_fallback_menu',
            ));
            ?>
            <a href="#contacto" class="mobile-contact-btn">Contactar</a>
        </div>
    </nav>
    
    <!-- Overlay para cerrar menú -->
    <div class="mobile-menu-overlay"></div>
</header>

<?php
// Función de respaldo para el menú desktop
function mjpropiedades_fallback_menu() {
    echo '<ul class="nav-menu">';
    echo '<li><a href="' . home_url() . '">Inicio</a></li>';
    echo '<li><a href="#about">Quiénes Somos</a></li>';
    echo '<li><a href="#servicios">Servicios</a></li>';
    echo '<li><a href="' . get_post_type_archive_link('propiedad') . '">Propiedades</a></li>';
    echo '<li><a href="#venta">Venta</a></li>';
    echo '<li><a href="#arriendo">Arriendo</a></li>';
    echo '</ul>';
}

// Función de respaldo para el menú móvil
function mjpropiedades_mobile_fallback_menu() {
    echo '<ul class="mobile-nav-menu">';
    echo '<li><a href="' . home_url() . '">Inicio</a></li>';
    echo '<li><a href="#about">Quiénes Somos</a></li>';
    echo '<li><a href="#servicios">Servicios</a></li>';
    echo '<li><a href="' . get_post_type_archive_link('propiedad') . '">Propiedades</a></li>';
    echo '<li><a href="#venta">Venta</a></li>';
    echo '<li><a href="#arriendo">Arriendo</a></li>';
    echo '</ul>';
}
?>
