<?php
/**
 * Template Name: Página de Inicio
 * 
 * Plantilla personalizada para la página de inicio
 * Muestra el hero y las propiedades destacadas
 */

get_header(); ?>

<!-- CSS dinámico para colores de tarjetas de propiedades -->
<style type="text/css" id="mjpropiedades-property-cards-colors-inicio">
<?php echo mjpropiedades_property_cards_dynamic_css(); ?>
</style>

<!-- Hero Section -->
<section class="hero">
    <!-- Carrusel de Imágenes -->
    <div class="hero-slider">
        <?php
        $hero_images = array(
            get_theme_mod('mjpropiedades_hero_1'),
            get_theme_mod('mjpropiedades_hero_2'),
            get_theme_mod('mjpropiedades_hero_3')
        );
        
        $default_images = array(
            'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80', // Casas
            'https://images.unsplash.com/photo-1554224155-6726b3ff858f?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80', // Tasación
            'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80'  // Ciudades
        );
        
        foreach ($hero_images as $index => $image_id) {
            $active_class = ($index === 0) ? ' active' : '';
            $image_url = '';
            
            if ($image_id) {
                $image_url = wp_get_attachment_image_url($image_id, 'full');
            }
            
            if (!$image_url) {
                $image_url = $default_images[$index];
            }
            
            echo '<div class="hero-slide' . $active_class . '" style="background-image: url(\'' . esc_url($image_url) . '\');">';
            echo '<div class="hero-overlay"></div>';
            echo '</div>';
        }
        ?>
    </div>
    
    <div class="hero-container">
        <!-- Diapositiva 1: Compra de Propiedades -->
        <div class="hero-content slide-content active" data-slide="0">
            <span class="hero-tag"><?php echo get_theme_mod('mjpropiedades_slide_1_tag', 'Compra de Propiedades'); ?></span>
            <h1><?php echo get_theme_mod('mjpropiedades_slide_1_title', 'Encuentra tu Hogar Ideal en Chile'); ?></h1>
            <p class="hero-description">
                <?php echo get_theme_mod('mjpropiedades_slide_1_description', 'Atendemos en Copiapó, Viña del Mar, La Serena y nos expandimos a más ciudades. Descubre propiedades exclusivas con asesoría personalizada y certificada en todo el proceso de compra.'); ?>
            </p>
            <div class="hero-buttons">
                <a href="#venta" class="hero-btn primary"><?php echo get_theme_mod('mjpropiedades_slide_1_btn_primary', 'Ver Propiedades'); ?> →</a>
                <a href="#contacto" class="hero-btn secondary"><?php echo get_theme_mod('mjpropiedades_slide_1_btn_secondary', 'Solicitar Tasación'); ?></a>
            </div>
        </div>

        <!-- Diapositiva 2: Venta de Propiedades -->
        <div class="hero-content slide-content" data-slide="1">
            <span class="hero-tag"><?php echo get_theme_mod('mjpropiedades_slide_2_tag', 'Venta de Propiedades'); ?></span>
            <h1><?php echo get_theme_mod('mjpropiedades_slide_2_title', 'Vende tu Propiedad al Mejor Precio'); ?></h1>
            <p class="hero-description">
                <?php echo get_theme_mod('mjpropiedades_slide_2_description', '¿Tienes una propiedad para vender? Te ayudamos a obtener el mejor valor de mercado. Servicios profesionales de tasación y comercialización en Copiapó, Viña del Mar, La Serena y próximamente en más ciudades.'); ?>
            </p>
            <div class="hero-buttons">
                <a href="#contacto" class="hero-btn primary"><?php echo get_theme_mod('mjpropiedades_slide_2_btn_primary', 'Solicitar Tasación'); ?></a>
                <a href="#venta" class="hero-btn secondary"><?php echo get_theme_mod('mjpropiedades_slide_2_btn_secondary', 'Ver Propiedades'); ?> →</a>
            </div>
        </div>

        <!-- Diapositiva 3: Arriendo de Propiedades -->
        <div class="hero-content slide-content" data-slide="2">
            <span class="hero-tag"><?php echo get_theme_mod('mjpropiedades_slide_3_tag', 'Arriendo de Propiedades'); ?></span>
            <h1><?php echo get_theme_mod('mjpropiedades_slide_3_title', 'Arrienda o Arrienda tu Propiedad'); ?></h1>
            <p class="hero-description">
                <?php echo get_theme_mod('mjpropiedades_slide_3_description', 'Ya sea que busques arrendar o tengas una propiedad para arrendar, te conectamos con las mejores opciones. Servicio profesional en Copiapó, Viña del Mar, La Serena con expansión continua a nuevas ciudades.'); ?>
            </p>
            <div class="hero-buttons">
                <a href="#arriendo" class="hero-btn primary"><?php echo get_theme_mod('mjpropiedades_slide_3_btn_primary', 'Ver Arriendos'); ?> →</a>
                <a href="#contacto" class="hero-btn secondary"><?php echo get_theme_mod('mjpropiedades_slide_3_btn_secondary', 'Arrendar Propiedad'); ?></a>
            </div>
        </div>
    </div>
    
    <!-- Carousel Navigation -->
    <button class="carousel-nav prev">‹</button>
    <button class="carousel-nav next">›</button>
    
    <!-- Carousel Dots -->
    <div class="carousel-dots">
        <span class="dot active" data-slide="0"></span>
        <span class="dot" data-slide="1"></span>
        <span class="dot" data-slide="2"></span>
    </div>
</section>

<!-- Search Section -->
<section id="buscar" class="search-section section">
    <br><br><br><br>
    <div class="container">
        <?php 
        echo mjpropiedades_get_search_form(array(
            'show_title' => true,
            'title' => 'Encuentra tu Propiedad Ideal',
            'button_text' => 'Buscar Propiedades',
            'action' => home_url('/propiedades/'),
            'preserve_values' => true,
            'form_id' => 'home-search-form'
        ));
        ?>
    </div>
    <br><br><br><br><br><br>
</section>

<!-- Servicios -->
<section id="servicios" class="services section">
    <div class="container">
        <div class="services-header">
            <span class="services-tag"><?php echo esc_html(get_theme_mod('mjpropiedades_services_tag', 'NUESTROS SERVICIOS')); ?></span>
            <br><br><br><br>
            <h2 class="section-title" style="color: <?php echo esc_attr(get_theme_mod('mjpropiedades_services_title_color', '#374151')); ?>">
                <?php echo esc_html(get_theme_mod('mjpropiedades_services_title', 'Te Acompañamos en Cada Paso')); ?>
            </h2>
            <p class="section-subtitle" style="color: <?php echo esc_attr(get_theme_mod('mjpropiedades_services_subtitle_color', '#6b7280')); ?>">
                <?php echo esc_html(get_theme_mod('mjpropiedades_services_subtitle', 'Servicios profesionales diseñados para hacer realidad tus objetivos inmobiliarios')); ?>
            </p>
        </div>
        
        <?php
        // Servicios por defecto si no hay configuración
        $default_services = array(
            1 => array(
                'title' => 'Venta',
                'description' => 'Te ayudamos a vender tu propiedad al mejor precio del mercado con estrategias personalizadas.',
                'features' => array('Marketing digital especializado', 'Fotografía profesional', 'Tours virtuales', 'Asesoría de precios')
            ),
            2 => array(
                'title' => 'Arriendo',
                'description' => 'Encontramos el inquilino ideal para tu propiedad con procesos seguros y eficientes.',
                'features' => array('Selección de inquilinos', 'Verificación de antecedentes', 'Contratos legales', 'Gestión de pagos')
            ),
            3 => array(
                'title' => 'Tasaciones',
                'description' => 'Valoramos tu propiedad con precisión profesional para tomar las mejores decisiones.',
                'features' => array('Análisis de mercado', 'Comparación de propiedades', 'Informe detallado', 'Certificación profesional')
            ),
            4 => array(
                'title' => 'Asesoría Legal',
                'description' => 'Te acompañamos en todo el proceso legal y administrativo para que no tengas que preocuparte por nada.',
                'features' => array('Tramitación de escrituras', 'Gestión de permisos', 'Seguimiento legal', 'Asesoría especializada')
            )
        );
        
        // Contar servicios primero
        $services_count = 0;
        for ($i = 1; $i <= 6; $i++) {
            $title = get_theme_mod("mjpropiedades_service_{$i}_title", '');
            if (!empty($title)) {
                $services_count++;
            }
        }
        
        // Si no hay servicios configurados, usar los por defecto
        if ($services_count == 0) {
            $services_count = 4;
        }
        ?>
        <div class="services-grid services-count-<?php echo $services_count; ?>">
            <?php
            for ($i = 1; $i <= 6; $i++) {
                $title = get_theme_mod("mjpropiedades_service_{$i}_title", '');
                $description = get_theme_mod("mjpropiedades_service_{$i}_description", '');
                $features = get_theme_mod("mjpropiedades_service_{$i}_features", '');
                
                // Si no hay configuración completa, usar valores por defecto
                if (empty($title) && isset($default_services[$i])) {
                    $title = $default_services[$i]['title'];
                    $description = $default_services[$i]['description'];
                    $features_array = $default_services[$i]['features'];
                } else {
                    $features_array = !empty($features) ? explode("\n", $features) : array();
                }
                
                // Solo mostrar si tiene título
                if (!empty($title)) {
                    ?>
                    <div class="service-card">
                        <div class="service-icon">
                            <div class="icon-wrapper">
                                <?php
                                // Iconos directamente en el código según el servicio
                                switch($i) {
                                    case 1:
                                        echo '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 22V12H15V22" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 6V12" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 9L12 6L15 9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
                                        break;
                                    case 2:
                                        echo '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21 2L2 7L10 13L21 2Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 13L7 20L2 7L10 13Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
                                        break;
                                    case 3:
                                        echo '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M2 17L12 22L22 17" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M2 12L12 17L22 12" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
                                        break;
                                    case 4:
                                        echo '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 2H6C4.9 2 4 2.9 4 4V20C4 21.1 4.89 22 5.99 22H18C19.1 22 20 21.1 20 20V8L14 2Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2V8H20" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 13H8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 17H8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 9H8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
                                        break;
                                    default:
                                        echo '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 2H6C4.9 2 4 2.9 4 4V20C4 21.1 4.89 22 5.99 22H18C19.1 22 20 21.1 20 20V8L14 2Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2V8H20" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 13H8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 17H8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 9H8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
                                }
                                ?>
                </div>
            </div>
                        <div class="service-content">
                            <h3><?php echo esc_html($title); ?></h3>
                            <p><?php echo esc_html($description); ?></p>
                            <?php if (!empty($features_array)): ?>
                                <ul class="service-features">
                                    <?php foreach ($features_array as $feature): ?>
                                        <?php if (!empty(trim($feature))): ?>
                                            <li><?php echo esc_html(trim($feature)); ?></li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                </div>
            </div>
                    <?php
                }
            }
            ?>
                </div>
        
            </div>
    <br><br><br><br>
</section>

<!-- Propiedades en Venta -->
<section id="venta" class="section">
    <br><br>
    <div class="container">
        <div class="section-header">
            
            <h2 class="section-title">Propiedades Destacadas</h2>
            <p class="section-subtitle">Descubre las mejores oportunidades inmobiliarias disponibles</p>
        </div>
        <br><br>
        

        <div class="section-tag venta-tag">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="#90ee90" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9 22V12H15V22" stroke="#90ee90" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 6V12" stroke="#90ee90" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9 9L12 6L15 9" stroke="#90ee90" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Propiedades en Venta</span>
        </div>
        <?php
        // Obtener propiedades destacadas en venta
        $featured_properties = mjpropiedades_get_featured_properties_by_operation('venta', 6);
        
        if (!empty($featured_properties)) :
            $properties_count = count($featured_properties);
        ?>
        <div class="featured-properties-grid featured-properties-count-<?php echo $properties_count; ?>">
            <?php foreach ($featured_properties as $property) :
                $precio = get_post_meta($property->ID, '_propiedad_precio', true);
                $dormitorios = get_post_meta($property->ID, '_propiedad_dormitorios', true);
                $banos = get_post_meta($property->ID, '_propiedad_banos', true);
                $metros = get_post_meta($property->ID, '_propiedad_metros', true);
                $comuna = get_post_meta($property->ID, '_propiedad_comuna', true);
                $operacion = get_post_meta($property->ID, '_propiedad_operacion', true);
                $estacionamientos = get_post_meta($property->ID, '_propiedad_estacionamientos', true);
                
                // Obtener imagen destacada
                $featured_image = get_the_post_thumbnail_url($property->ID, 'large');
                if (!$featured_image) {
                    $featured_image = get_template_directory_uri() . '/images/property-placeholder.jpg';
                }
                
                // Formatear precio
                $precio_formatted = $precio ? '$' . number_format(floatval($precio), 0, ',', '.') : 'Consultar';
                
                // Determinar operación si no está configurada
                if (!$operacion) {
                    $operacion = ($precio && $precio < 1000000) ? 'arriendo' : 'venta';
                }
            ?>
                                <div class="property-card">
                                    <div class="property-image">
                    <img src="<?php echo esc_url($featured_image); ?>" alt="<?php echo esc_attr($property->post_title); ?>">
                    <div class="property-status-tag <?php echo esc_attr($operacion); ?>">
                        <?php echo esc_html(ucfirst($operacion)); ?>
                    </div>
                                    </div>
                                    
                                    <div class="property-content">
                    <div class="property-price"><?php echo esc_html($precio_formatted); ?></div>
                    <h3 class="property-title"><?php echo esc_html($property->post_title); ?></h3>
                    
                    <div class="property-location">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 10C21 17 12 23 12 23S3 17 3 10C3 7.61305 3.94821 5.32387 5.63604 3.63604C7.32387 1.94821 9.61305 1 12 1C14.3869 1 16.6761 1.94821 18.3639 3.63604C20.0518 5.32387 21 7.61305 21 10Z" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 13C13.6569 13 15 11.6569 15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13Z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <?php echo esc_html($comuna ? $comuna . ', Coquimbo' : 'Coquimbo'); ?>
                    </div>
                    
                    <div class="property-features">
                        <?php if ($dormitorios): ?>
                        <div class="property-feature">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2"/>
                                <path d="M9 22V12H15V22" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <?php echo esc_html($dormitorios); ?> dorm
                        </div>
                                            <?php endif; ?>
                                            
                        <?php if ($banos): ?>
                        <div class="property-feature">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 6L9 7C9 8.10457 9.89543 9 11 9H13C14.1046 9 15 8.10457 15 7V6" stroke="currentColor" stroke-width="2"/>
                                <path d="M8 6V4C8 2.89543 8.89543 2 10 2H14C15.1046 2 16 2.89543 16 4V6" stroke="currentColor" stroke-width="2"/>
                                <path d="M4 12H20C21.1046 12 22 12.8954 22 14V18C22 19.1046 21.1046 20 20 20H4C2.89543 20 2 19.1046 2 18V14C2 12.8954 2.89543 12 4 12Z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <?php echo esc_html($banos); ?> baños
                        </div>
                                            <?php endif; ?>
                                            
                        <?php if ($estacionamientos): ?>
                        <div class="property-feature">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 17H17V19C17 19.5523 16.5523 20 16 20H8C7.44772 20 7 19.5523 7 19V17Z" stroke="currentColor" stroke-width="2"/>
                                <path d="M7 17V7C7 5.89543 7.89543 5 9 5H15C16.1046 5 17 5.89543 17 7V17" stroke="currentColor" stroke-width="2"/>
                                <path d="M10 9H14" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <?php echo esc_html($estacionamientos); ?> estac
                                        </div>
                        <?php endif; ?>
                        
                        <?php if ($metros): ?>
                        <div class="property-feature">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 3H21V21H3V3Z" stroke="currentColor" stroke-width="2"/>
                                <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <?php echo esc_html($metros); ?> m²
                                            </div>
                                        <?php endif; ?>
                    </div>
                                        
                    <a href="<?php echo get_permalink($property->ID); ?>" class="property-button">
                                            Ver Detalles
                                        </a>
                                    </div>
                                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Ver todas button -->
        <div class="section-footer">
            <a href="<?php echo home_url('/propiedades/?operacion=venta'); ?>" class="view-all-btn">
                Ver Todas
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2"/>
                </svg>
            </a>
        </div>
        
        <?php else : ?>
        <div class="no-properties-message">
            <p>No hay propiedades destacadas en venta disponibles en este momento.</p>
            <a href="<?php echo home_url('/propiedades/?operacion=venta'); ?>" class="view-all-btn">
                Ver Todas
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2"/>
                </svg>
            </a>
        </div>
        <?php endif; ?>
    </div>
    <br><br>
</section>

<!-- Propiedades Destacadas en Arriendo -->
<section id="arriendo" class="section bg-light">
    <br><br>
    <div class="container">
        <div class="section-tag arriendo-tag">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 11H5C3.89543 11 3 11.8954 3 13V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V13C21 11.8954 20.1046 11 19 11Z" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M7 11V7C7 4.79086 8.79086 3 11 3H13C15.2091 3 17 4.79086 17 7V11" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Propiedades en Arriendo</span>
        </div>
        <?php
        // Obtener propiedades destacadas en arriendo
        $featured_arriendo_properties = mjpropiedades_get_featured_properties_by_operation('arriendo', 6);
        
        if (!empty($featured_arriendo_properties)) :
            $arriendo_count = count($featured_arriendo_properties);
        ?>
        <div class="featured-properties-grid featured-properties-count-<?php echo $arriendo_count; ?>">
            <?php foreach ($featured_arriendo_properties as $property) :
                $precio = get_post_meta($property->ID, '_propiedad_precio', true);
                $dormitorios = get_post_meta($property->ID, '_propiedad_dormitorios', true);
                $banos = get_post_meta($property->ID, '_propiedad_banos', true);
                $metros = get_post_meta($property->ID, '_propiedad_metros', true);
                $comuna = get_post_meta($property->ID, '_propiedad_comuna', true);
                $operacion = get_post_meta($property->ID, '_propiedad_operacion', true);
                $estacionamientos = get_post_meta($property->ID, '_propiedad_estacionamientos', true);
                
                // Obtener imagen destacada
                $featured_image = get_the_post_thumbnail_url($property->ID, 'large');
                if (!$featured_image) {
                    $featured_image = get_template_directory_uri() . '/images/property-placeholder.jpg';
                }
                
                // Formatear precio
                $precio_formatted = $precio ? '$' . number_format(floatval($precio), 0, ',', '.') : 'Consultar';
                
                // Determinar operación si no está configurada
                if (!$operacion) {
                    $operacion = ($precio && $precio < 1000000) ? 'arriendo' : 'venta';
                }
            ?>
                                <div class="property-card">
                                    <div class="property-image">
                    <img src="<?php echo esc_url($featured_image); ?>" alt="<?php echo esc_attr($property->post_title); ?>">
                    <div class="property-status-tag <?php echo esc_attr($operacion); ?>">
                        <?php echo esc_html(ucfirst($operacion)); ?>
                    </div>
                                    </div>
                                    
                                    <div class="property-content">
                    <div class="property-price"><?php echo esc_html($precio_formatted); ?></div>
                    <h3 class="property-title"><?php echo esc_html($property->post_title); ?></h3>
                    
                    <div class="property-location">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 10C21 17 12 23 12 23S3 17 3 10C3 7.61305 3.94821 5.32387 5.63604 3.63604C7.32387 1.94821 9.61305 1 12 1C14.3869 1 16.6761 1.94821 18.3639 3.63604C20.0518 5.32387 21 7.61305 21 10Z" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 13C13.6569 13 15 11.6569 15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13Z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <?php echo esc_html($comuna ? $comuna . ', Coquimbo' : 'Coquimbo'); ?>
                    </div>
                    
                    <div class="property-features">
                        <?php if ($dormitorios): ?>
                        <div class="property-feature">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2"/>
                                <path d="M9 22V12H15V22" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <?php echo esc_html($dormitorios); ?> dorm
                        </div>
                                            <?php endif; ?>
                                            
                        <?php if ($banos): ?>
                        <div class="property-feature">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 6L9 7C9 8.10457 9.89543 9 11 9H13C14.1046 9 15 8.10457 15 7V6" stroke="currentColor" stroke-width="2"/>
                                <path d="M8 6V4C8 2.89543 8.89543 2 10 2H14C15.1046 2 16 2.89543 16 4V6" stroke="currentColor" stroke-width="2"/>
                                <path d="M4 12H20C21.1046 12 22 12.8954 22 14V18C22 19.1046 21.1046 20 20 20H4C2.89543 20 2 19.1046 2 18V14C2 12.8954 2.89543 12 4 12Z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <?php echo esc_html($banos); ?> baños
                        </div>
                                            <?php endif; ?>
                                            
                        <?php if ($estacionamientos): ?>
                        <div class="property-feature">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 17H17V19C17 19.5523 16.5523 20 16 20H8C7.44772 20 7 19.5523 7 19V17Z" stroke="currentColor" stroke-width="2"/>
                                <path d="M7 17V7C7 5.89543 7.89543 5 9 5H15C16.1046 5 17 5.89543 17 7V17" stroke="currentColor" stroke-width="2"/>
                                <path d="M10 9H14" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <?php echo esc_html($estacionamientos); ?> estac
                                        </div>
                        <?php endif; ?>
                        
                        <?php if ($metros): ?>
                        <div class="property-feature">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 3H21V21H3V3Z" stroke="currentColor" stroke-width="2"/>
                                <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <?php echo esc_html($metros); ?> m²
                                            </div>
                                        <?php endif; ?>
                    </div>
                                        
                    <a href="<?php echo get_permalink($property->ID); ?>" class="property-button">
                                            Ver Detalles
                                        </a>
                                    </div>
                                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Ver todas button -->
        <div class="section-footer">
            <a href="<?php echo home_url('/propiedades/?operacion=arriendo'); ?>" class="view-all-btn">
                Ver Todas
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2"/>
                </svg>
            </a>
        </div>
        
        <?php else : ?>
        <div class="no-properties-message">
            <p>No hay propiedades destacadas en arriendo disponibles en este momento.</p>
            <a href="<?php echo home_url('/propiedades/?operacion=arriendo'); ?>" class="view-all-btn">
                Ver Todas
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2"/>
                </svg>
            </a>
                    </div>
        <?php endif; ?>
    </div>
    <br><br>
</section>

<!-- Quiénes Somos -->
<section id="about" class="about section">
    <br><br>
    <div class="container">
        <div class="about-container">
            <div class="about-content">
                <h2>Sobre Home Isa</h2>
                <p class="about-text">
                    <?php echo get_theme_mod('mjpropiedades_about_text_1', 'Home Isa es una empresa inmobiliaria innovadora fundada en 2025, con alcance nacional en Chile. Nos especializamos en brindar servicios integrales de corretaje inmobiliario, asesoría y tasación de propiedades.'); ?>
                </p>
                <p class="about-text">
                    <?php echo get_theme_mod('mjpropiedades_about_text_2', 'Nuestro compromiso es facilitar el proceso inmobiliario para todo tipo de clientes: familias que buscan su primer hogar, inversionistas experimentados que buscan oportunidades de crecimiento, propietarios que desean vender sus propiedades al mejor precio, y personas que necesitan arrendar o encontrar inquilinos para sus inmuebles. Con sede en La Serena y cobertura nacional, combinamos la experiencia local con una visión moderna del mercado inmobiliario chileno.'); ?>
                </p>
                
                <div class="stats">
                    <div class="stat">
                        <span class="stat-number"><?php echo get_theme_mod('mjpropiedades_about_stat_1_number', '500+'); ?></span>
                        <span class="stat-label"><?php echo get_theme_mod('mjpropiedades_about_stat_1_label', 'Propiedades Vendidas'); ?></span>
                    </div>
                    <div class="stat">
                        <span class="stat-number"><?php echo get_theme_mod('mjpropiedades_about_stat_2_number', '98%'); ?></span>
                        <span class="stat-label"><?php echo get_theme_mod('mjpropiedades_about_stat_2_label', 'Clientes Satisfechos'); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="about-image">
                <?php
                $about_image_id = get_theme_mod('mjpropiedades_about_image');
                if ($about_image_id) {
                    $about_image_url = wp_get_attachment_image_url($about_image_id, 'large');
                    if ($about_image_url) {
                        echo '<img src="' . esc_url($about_image_url) . '" alt="Home Isa - Corredora de Propiedades Certificada en La Serena y Coquimbo">';
                    } else {
                        echo '<img src="' . get_template_directory_uri() . '/images/maria-jose.jpg" alt="Home Isa - Corredora de Propiedades Certificada en La Serena y Coquimbo">';
                    }
                } else {
                    echo '<img src="' . get_template_directory_uri() . '/images/maria-jose.jpg" alt="Home Isa - Corredora de Propiedades Certificada en La Serena y Coquimbo">';
                }
                ?>
            </div>
        </div>
    </div>
    <br><br>
</section>

<!-- Testimonios -->
<section id="testimonios" class="testimonials section bg-light">
    <br><br>
    <div class="container">
        <h2 class="section-title"><?php echo get_theme_mod('mjpropiedades_testimonials_title', 'Lo que dicen nuestros clientes'); ?></h2>
        <p class="section-subtitle"><?php echo get_theme_mod('mjpropiedades_testimonials_subtitle', 'Testimonios reales de clientes satisfechos en la Cuarta Región'); ?></p>
        <br>
        <!-- Estadísticas de Satisfacción -->
        <div class="testimonials-stats">
            <div class="stat-item">
                <span class="stat-number">100+</span>
                <span class="stat-label">Clientes Felices</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">98%</span>
                <span class="stat-label">Recomiendan</span>
            </div>
        </div>
        <br><br>
        <?php
        // Testimonios por defecto si no hay configuración
        $default_testimonials = array(
            1 => array(
                'text' => 'Vendí mi casa en Peñuelas, Coquimbo, en menos de 30 días. Home Isa fue increíble, muy profesional y siempre disponible para resolver mis dudas.',
                'name' => 'Carlos Mendoza',
                'location' => 'Peñuelas, Coquimbo'
            ),
            2 => array(
                'text' => 'Encontré el departamento perfecto en La Serena gracias a Home Isa. Su conocimiento de la zona es excepcional y el proceso fue muy transparente.',
                'name' => 'Ana Rodríguez',
                'location' => 'La Serena'
            ),
            3 => array(
                'text' => 'Arrendé mi casa en Ovalle con Home Isa. El servicio fue impecable, desde la tasación hasta la entrega de llaves. Totalmente recomendable.',
                'name' => 'Roberto Silva',
                'location' => 'Ovalle'
            )
        );
        
        // Contar testimonios con contenido
        $testimonials_count = 0;
        for ($i = 1; $i <= 6; $i++) {
            $text = get_theme_mod("mjpropiedades_testimonial_{$i}_text", '');
            if (!empty(trim($text))) {
                $testimonials_count++;
            }
        }
        
        // Si no hay testimonios configurados, usar los por defecto
        if ($testimonials_count == 0) {
            $testimonials_count = 3;
        }
        ?>
        <div class="testimonials-grid testimonials-count-<?php echo $testimonials_count; ?>">
            <?php
            for ($i = 1; $i <= 6; $i++) {
                $text = get_theme_mod("mjpropiedades_testimonial_{$i}_text", '');
                $name = get_theme_mod("mjpropiedades_testimonial_{$i}_name", '');
                $location = get_theme_mod("mjpropiedades_testimonial_{$i}_location", '');
                
                // Si no hay configuración, usar valores por defecto
                if (empty(trim($text)) && isset($default_testimonials[$i])) {
                    $text = $default_testimonials[$i]['text'];
                    $name = $default_testimonials[$i]['name'];
                    $location = $default_testimonials[$i]['location'];
                }
                
                // Solo mostrar si tiene texto
                if (!empty(trim($text))) {
                    ?>
            <div class="testimonial-card">
                <div class="testimonial-content">
                    <div class="stars">★★★★★</div>
                            <p class="testimonial-text">"<?php echo esc_html($text); ?>"</p>
                </div>
                <div class="testimonial-author">
                    <div class="author-info">
                                <?php if (!empty($name)): ?>
                                    <h4><?php echo esc_html($name); ?></h4>
                                <?php endif; ?>
                                <?php if (!empty($location)): ?>
                                    <span class="author-location"><?php echo esc_html($location); ?></span>
                                <?php endif; ?>
                    </div>
                </div>
            </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
    <br><br><br><br>
</section>


<!-- Contact Section -->
<section id="contacto" class="section">
    <div class="container">
        <br><br>
        <div class="contact-header">
            <h2 class="section-title">Contáctanos</h2>
            <p class="section-subtitle">Estamos aquí para ayudarte con todas tus necesidades inmobiliarias</p>
            <br>
            <?php
            // Mostrar mensajes de éxito o error
            if (isset($_GET['contact'])) {
                if ($_GET['contact'] == 'success') {
                    echo '<div class="contact-message success">¡Mensaje enviado correctamente! Te contactaremos pronto.</div>';
                } elseif ($_GET['contact'] == 'error') {
                    echo '<div class="contact-message error">Hubo un error al enviar el mensaje. Por favor, inténtalo de nuevo.</div>';
                }
            }
            ?>
        </div>
        
        <div class="contact-content">
            <!-- Información de Contacto -->
            <div class="contact-info">
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M22 6L12 13L2 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="contact-details">
                        <strong>Email</strong>
                        <span><?php echo esc_html(get_theme_mod('mjpropiedades_contact_email', 'consultas@homeisa.cl')); ?></span>
                </div>
            </div>
            
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22 16.92V19.92C22.0011 20.1985 21.9441 20.4742 21.8325 20.7293C21.7209 20.9845 21.5573 21.2136 21.3521 21.4019C21.1468 21.5901 20.9046 21.7335 20.6407 21.8227C20.3769 21.9119 20.0974 21.9451 19.82 21.92C16.7428 21.5856 13.787 20.5341 11.19 18.85C8.77382 17.3147 6.72533 15.2662 5.18999 12.85C3.49997 10.2412 2.44824 7.27099 2.11999 4.18C2.095 3.90347 2.12787 3.62476 2.21649 3.36162C2.30512 3.09849 2.44756 2.85669 2.63476 2.65162C2.82196 2.44655 3.0498 2.28271 3.30379 2.17052C3.55777 2.05833 3.83233 2.00026 4.10999 2H7.10999C7.59531 1.99522 8.06679 2.16708 8.43376 2.48353C8.80073 2.79999 9.03996 3.23945 9.10999 3.72C9.23662 4.68007 9.47144 5.62273 9.80999 6.53C9.94454 6.88792 9.97366 7.27691 9.89391 7.65088C9.81415 8.02485 9.62886 8.36811 9.35999 8.64L8.08999 9.91C9.51355 12.4135 11.5865 14.4864 14.09 15.91L15.36 14.64C15.6319 14.3711 15.9751 14.1858 16.3491 14.1061C16.7231 14.0263 17.1121 14.0555 17.47 14.19C18.3773 14.5286 19.3199 14.7634 20.28 14.89C20.7658 14.9585 21.2094 15.2032 21.5265 15.5775C21.8437 15.9518 22.0122 16.4296 22 16.92Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="contact-details">
                        <strong>Teléfono</strong>
                        <span><?php echo esc_html(get_theme_mod('mjpropiedades_contact_phone', '+56 9 4927 6448')); ?></span>
                </div>
            </div>
            
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 10C21 17 12 23 12 23S3 17 3 10C3 7.61305 3.94821 5.32387 5.63604 3.63604C7.32387 1.94821 9.61305 1 12 1C14.3869 1 16.6761 1.94821 18.3639 3.63604C20.0518 5.32387 21 7.61305 21 10Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 13C13.6569 13 15 11.6569 15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="contact-details">
                        <strong>Ubicación</strong>
                        <span><?php echo esc_html(get_theme_mod('mjpropiedades_contact_address', 'La Serena, Chile')); ?></span>
                </div>
            </div>
            
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 6V12L16 14" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="contact-details">
                        <strong>Horarios</strong>
                        <span><?php echo wp_kses_post(get_theme_mod('mjpropiedades_contact_hours', 'Lunes a Viernes: 9:00 - 18:00<br>Sábados: 9:00 - 14:00')); ?></span>
                </div>
                </div>
            </div>
            
            <!-- Formulario de Contacto -->
            <div class="contact-form-container">
                <?php 
                // Verificar si Contact Form 7 está activo
                if (function_exists('wpcf7_contact_form')) {
                    $form_id = mjpropiedades_get_contact_form_id();
                    echo do_shortcode('[contact-form-7 id="' . $form_id . '" title="Formulario de Contacto"]');
                } else {
                    // Fallback al formulario original si CF7 no está instalado
                    ?>
        <form class="contact-form" method="post" action="">
            <?php wp_nonce_field('contact_form_nonce', 'contact_nonce'); ?>
            <input type="hidden" name="contact_form_submitted" value="1">
            
                        <div class="form-row">
            <div class="form-group">
                                <label for="nombre" class="form-label">Nombre completo</label>
                                <input type="text" id="nombre" name="nombre" class="form-input" required>
            </div>
            
                            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-input" required>
                            </div>
            </div>
            
                        <div class="form-row">
                            <div class="form-group">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" id="telefono" name="telefono" class="form-input" required>
            </div>
            
            <div class="form-group">
                                <label for="tipo_consulta" class="form-label">Tipo de consulta</label>
                                <select id="tipo_consulta" name="tipo_consulta" class="form-select" required>
                    <option value="">Seleccionar tipo</option>
                                    <option value="compra">Compra de propiedad</option>
                                    <option value="venta">Venta de propiedad</option>
                                    <option value="arriendo">Arriendo de propiedad</option>
                                    <option value="tasacion">Tasación</option>
                                    <option value="asesoria">Asesoría inmobiliaria</option>
                </select>
                            </div>
            </div>
            
            <div class="form-group">
                            <label for="mensaje" class="form-label">Mensaje</label>
                            <textarea id="mensaje" name="mensaje" class="form-textarea" placeholder="Cuéntanos más sobre tu consulta..." required></textarea>
            </div>
            
                        <button type="submit" class="submit-btn">Enviar Consulta</button>
                    </form>
                    <?php
                }
                ?>
            </div>
            </div>
    </div>
    <br><br><br><br>
</section>

<?php get_footer(); ?>

