<?php
/**
 * Template para mostrar una propiedad individual
 * Diseño mobile-first, minimalista y profesional
 */

get_header(); ?>

<div class="property-detail-page">
        <?php while (have_posts()) : the_post(); ?>
        <?php
        // Obtener datos de la propiedad
        $precio = get_post_meta(get_the_ID(), '_propiedad_precio', true);
        $operacion = get_post_meta(get_the_ID(), '_propiedad_operacion', true);
        $dormitorios = get_post_meta(get_the_ID(), '_propiedad_dormitorios', true);
        $banos = get_post_meta(get_the_ID(), '_propiedad_banos', true);
        $metros = get_post_meta(get_the_ID(), '_propiedad_metros', true);
        $comuna = get_post_meta(get_the_ID(), '_propiedad_comuna', true);
        $tipo = get_post_meta(get_the_ID(), '_propiedad_tipo', true);
        $direccion = get_post_meta(get_the_ID(), '_propiedad_direccion', true);
        $ano_construccion = get_post_meta(get_the_ID(), '_propiedad_ano_construccion', true);
        $orientacion = get_post_meta(get_the_ID(), '_propiedad_orientacion', true);
        $gastos_comunes = get_post_meta(get_the_ID(), '_propiedad_gastos_comunes', true);
        $estado = get_post_meta(get_the_ID(), '_propiedad_estado', true);
        $disponibilidad = get_post_meta(get_the_ID(), '_propiedad_disponibilidad', true);
        $caracteristicas = get_post_meta(get_the_ID(), '_propiedad_caracteristicas', true);
        $latitud = get_post_meta(get_the_ID(), '_propiedad_latitud', true);
        $longitud = get_post_meta(get_the_ID(), '_propiedad_longitud', true);
        $lugares_cercanos = get_post_meta(get_the_ID(), '_propiedad_lugares_cercanos', true);
        $estacionamientos = get_post_meta(get_the_ID(), '_propiedad_estacionamientos', true);
        
        // Si no hay operación configurada, determinar por precio
        if (!$operacion) {
            $operacion = ($precio && $precio < 1000000) ? 'arriendo' : 'venta';
        }
        
    // Formatear precio en CLP con separadores de miles
    $precio_text = $precio ? '$' . number_format(floatval($precio), 0, ',', '.') : 'Consultar';
        if ($operacion === 'arriendo') {
            $precio_text .= '/mes';
        }
        
        $uf_precio = $precio ? round($precio / 40000) : 0;
        ?>
        
    <!-- Hero Section - Galería de Imágenes -->
    <section class="hero-gallery-section">
        <div class="hero-container">
            <!-- Galería principal -->
            <div class="gallery-container">
                <div class="gallery-main">
                    <?php 
                    // Obtener todas las imágenes de la galería
                    $gallery_images = get_post_meta(get_the_ID(), '_propiedad_gallery', true);
                    $all_images = array();
                    
                    if ($gallery_images) {
                        $image_ids = explode(',', $gallery_images);
                        foreach ($image_ids as $image_id) {
                            $image_url = wp_get_attachment_image_url($image_id, 'large');
                            if ($image_url) {
                                $all_images[] = array(
                                    'url' => $image_url,
                                    'id' => $image_id
                                );
                            }
                        }
                    }
                    
                    // Si no hay galería, usar imagen destacada
                    if (empty($all_images) && has_post_thumbnail()) {
                        $all_images[] = array(
                            'url' => wp_get_attachment_image_url(get_post_thumbnail_id(), 'large'),
                            'id' => get_post_thumbnail_id()
                        );
                    }
                    
                    if (!empty($all_images)) :
                        foreach ($all_images as $index => $image) :
                            $active_class = $index === 0 ? 'active' : '';
                            ?>
                            <div class="gallery-image <?php echo $active_class; ?>" data-index="<?php echo $index; ?>">
                                <img src="<?php echo esc_url($image['url']); ?>" alt="Imagen <?php echo $index + 1; ?> de la propiedad">
                            </div>
                            <?php
                        endforeach;
                    else :
                    ?>
                        <div class="no-image">
                            <div class="no-image-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    <polyline points="9,22 9,12 15,12 15,22"></polyline>
                                </svg>
                            </div>
                            <p>Sin imagen disponible</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Navegación de la galería -->
                <?php if (count($all_images) > 1) : ?>
                    <div class="gallery-navigation">
                        <button class="nav-btn prev-btn" onclick="changeImage(-1)">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="15,18 9,12 15,6"></polyline>
                            </svg>
                        </button>
                        <button class="nav-btn next-btn" onclick="changeImage(1)">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="9,18 15,12 9,6"></polyline>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Indicadores de puntos -->
                    <div class="gallery-dots">
                        <?php foreach ($all_images as $index => $image) : ?>
                            <button class="dot <?php echo $index === 0 ? 'active' : ''; ?>" onclick="goToImage(<?php echo $index; ?>)"></button>
                        <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <!-- Miniaturas de la galería -->
                <div class="gallery-thumbnails">
                            <?php 
                    if (!empty($all_images)) {
                        foreach ($all_images as $index => $image) {
                            $thumbnail_url = wp_get_attachment_image_url($image['id'], 'medium');
                            if ($thumbnail_url) {
                                $active_class = $index === 0 ? 'active' : '';
                                echo '<div class="thumbnail ' . $active_class . '" onclick="goToImage(' . $index . ')">';
                                echo '<img src="' . esc_url($thumbnail_url) . '" alt="Miniatura ' . ($index + 1) . '">';
                                echo '</div>';
                            }
                        }
                    }
                    ?>
                </div>
            </div>
            
            <!-- Panel de información -->
            <div class="info-panel">
                <div class="property-price"><?php echo $precio_text; ?></div>
                <h1 class="property-title"><?php the_title(); ?></h1>
                <p class="property-location">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <?php echo $direccion ? esc_html($direccion) : esc_html($comuna); ?>
                </p>
                
                <!-- Características principales -->
                <div class="main-features-grid">
                    <div class="feature-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 7V5C3 3.89543 3.89543 3 5 3H19C20.1046 3 21 3.89543 21 5V7M3 7V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V7M3 7H21M7 11H17M7 15H13"></path>
                        </svg>
                        <span><?php echo $dormitorios ?: '2'; ?> dorm</span>
                    </div>
                    <div class="feature-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 5.5V7.5C15 8.3 14.3 9 13.5 9S12 8.3 12 7.5V5.5L6 7V9L12 7.5V9.5C12 10.3 12.7 11 13.5 11S15 10.3 15 9.5V7.5L21 9Z"></path>
                            <path d="M12 12C8.7 12 6 14.7 6 18V22H18V18C18 14.7 15.3 12 12 12Z"></path>
                        </svg>
                        <span><?php echo $banos ?: '2'; ?> baños</span>
                    </div>
                    <div class="feature-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                        </svg>
                        <span><?php echo $metros ?: '85'; ?> m²</span>
                    </div>
                </div>
                
                <!-- Separador -->
                <div class="separator"></div>
                
                <!-- Información del agente -->
                <?php
                // Obtener datos del agente
                $agente = mjpropiedades_get_property_agent();
                $agente_data = $agente ? mjpropiedades_get_agent_data($agente->ID) : null;
                
                if ($agente_data) : ?>
                <div class="agent-info">
                    <h3 class="agent-title">Agente Inmobiliario</h3>
                    <div class="agent-profile">
                        <div class="agent-avatar">
                            <?php if ($agente_data['avatar']) : ?>
                                <img src="<?php echo esc_url($agente_data['avatar']); ?>" alt="<?php echo esc_attr($agente_data['nombre']); ?>" />
                            <?php else : ?>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            <?php endif; ?>
                        </div>
                        <div class="agent-details">
                            <div class="agent-name"><?php echo esc_html($agente_data['nombre']); ?></div>
                            <div class="agent-specialization">
                                <?php 
                                if ($agente_data['cargo']) {
                                    echo esc_html($agente_data['cargo']);
                                } else {
                                    echo 'Especialista en ' . esc_html($comuna);
                                }
                                ?>
                            </div>
                            <?php if (get_theme_mod('mjpropiedades_show_rating', true) && $agente_data['rating'] && $agente_data['resenas']) : ?>
                            <div class="agent-rating">
                                <div class="stars">
                                    <?php 
                                    $rating = floatval($agente_data['rating']);
                                    $full_stars = floor($rating);
                                    $has_half_star = ($rating - $full_stars) >= 0.5;
                                    
                                    // Mostrar estrellas llenas
                                    for ($i = 0; $i < $full_stars; $i++) {
                                        echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">';
                                        echo '<polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"></polygon>';
                                        echo '</svg>';
                                    }
                                    
                                    // Mostrar media estrella si es necesario
                                    if ($has_half_star) {
                                        echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">';
                                        echo '<defs>';
                                        echo '<linearGradient id="half-star">';
                                        echo '<stop offset="50%" stop-color="currentColor"/>';
                                        echo '<stop offset="50%" stop-color="transparent"/>';
                                        echo '</linearGradient>';
                                        echo '</defs>';
                                        echo '<polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26" fill="url(#half-star)"></polygon>';
                                        echo '</svg>';
                                    }
                                    
                                    // Mostrar estrellas vacías
                                    $empty_stars = 5 - $full_stars - ($has_half_star ? 1 : 0);
                                    for ($i = 0; $i < $empty_stars; $i++) {
                                        echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">';
                                        echo '<polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"></polygon>';
                                        echo '</svg>';
                                    }
                                    ?>
                                </div>
                                <span class="rating-text"><?php echo esc_html($agente_data['rating']); ?> (<?php echo esc_html($agente_data['resenas']); ?> reseñas)</span>
                                </div>
                            <?php endif; ?>
                </div>
            </div>
                    
                    <!-- Botones de contacto -->
                    <div class="contact-buttons">
                        <?php if ($agente_data['telefono']) : ?>
                        <a href="tel:<?php echo esc_attr($agente_data['telefono']); ?>" class="contact-btn call-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                            Llamar Ahora
                        </a>
                        <?php endif; ?>
                        
                        <?php 
                        // Determinar qué número usar para WhatsApp (priorizar WhatsApp, luego teléfono, luego número por defecto)
                        $whatsapp_number = $agente_data['whatsapp'] ? $agente_data['whatsapp'] : ($agente_data['telefono'] ? $agente_data['telefono'] : '+56912345678');
                        
                        // Crear mensaje para WhatsApp
                        $whatsapp_message = "Hola, estoy interesado/a en esta propiedad: " . get_the_title() . " en " . $comuna . ". ¿Podrías darme más información?";
                        $whatsapp_url = "https://wa.me/" . str_replace(array('+', ' ', '-'), '', $whatsapp_number) . "?text=" . urlencode($whatsapp_message);
                        ?>
                        <a href="<?php echo esc_url($whatsapp_url); ?>" class="contact-btn whatsapp-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                            </svg>
                            Conversar Ahora
                        </a>
                    </div>
                </div>
                <?php else : ?>
                <!-- Mensaje cuando no hay agente -->
                <div class="agent-info">
                    <h3 class="agent-title">Contacto</h3>
                    <div class="agent-profile">
                        <div class="agent-avatar">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <div class="agent-details">
                            <div class="agent-name"><?php echo esc_html(get_theme_mod('mjpropiedades_no_agent_message', 'Contactar con nuestro equipo')); ?></div>
                            <div class="agent-specialization">Estaremos encantados de atenderte</div>
                        </div>
                    </div>
                    
                    <!-- Botones de contacto genéricos -->
                    <div class="contact-buttons">
                        <a href="tel:+56912345678" class="contact-btn call-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                            Llamar Ahora
                        </a>
                        <?php 
                            // Crear mensaje para WhatsApp (sin agente)
                            $whatsapp_message_fallback = "Hola, estoy interesado/a en esta propiedad: " . get_the_title() . " en " . $comuna . ". ¿Podrías darme más información?";
                            $whatsapp_url_fallback = "https://wa.me/56912345678?text=" . urlencode($whatsapp_message_fallback);
                        ?>
                        <a href="<?php echo esc_url($whatsapp_url_fallback); ?>" class="contact-btn whatsapp-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                            </svg>
                            Conversar Ahora
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
        
        <!-- Información Principal -->
    <section class="property-info">
        <div class="container">
            <!-- Header con título y precio -->
                <div class="property-header">
                    <div class="property-title-section">
                        <h1 class="property-title"><?php the_title(); ?></h1>
                    <p class="property-location">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <?php echo $direccion ? esc_html($direccion) : esc_html($comuna); ?>
                    </p>
                                </div>
                    
                <div class="price-section">
                        <div class="price-main"><?php echo $precio_text; ?></div>
                        <?php if ($uf_precio > 0) : ?>
                            <div class="price-uf">UF <?php echo number_format(floatval($uf_precio), 0, ',', '.'); ?></div>
                            <?php endif; ?>
                    </div>
                </div>
                
                <!-- Características principales -->
            <div class="main-features">
                <div class="feature-item">
                        <div class="feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 7V5C3 3.89543 3.89543 3 5 3H19C20.1046 3 21 3.89543 21 5V7M3 7V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V7M3 7H21M7 11H17M7 15H13"></path>
                            </svg>
                                </div>
                    <div class="feature-content">
                        <span class="feature-number"><?php echo $dormitorios ?: '3'; ?></span>
                        <span class="feature-label">Dormitorios</span>
                    </div>
                    </div>
                    
                <div class="feature-item">
                        <div class="feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 5.5V7.5C15 8.3 14.3 9 13.5 9S12 8.3 12 7.5V5.5L6 7V9L12 7.5V9.5C12 10.3 12.7 11 13.5 11S15 10.3 15 9.5V7.5L21 9Z"></path>
                            <path d="M12 12C8.7 12 6 14.7 6 18V22H18V18C18 14.7 15.3 12 12 12Z"></path>
                            </svg>
                                </div>
                    <div class="feature-content">
                        <span class="feature-number"><?php echo $banos ?: '2'; ?></span>
                        <span class="feature-label">Baños</span>
                    </div>
                        </div>
                        
                <div class="feature-item">
                        <div class="feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            </svg>
                        </div>
                    <div class="feature-content">
                        <span class="feature-number"><?php echo $metros ?: '120'; ?> m²</span>
                        <span class="feature-label">Construidos</span>
                    </div>
                    </div>
                    
                <?php if ($estacionamientos) : ?>
                <div class="feature-item">
                        <div class="feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8C21 6.89543 20.1046 6 19 6H5C3.89543 6 3 6.89543 3 8V16C3 17.1046 3.89543 18 5 18H19C20.1046 18 21 17.1046 21 16Z"></path>
                            <path d="M7 10H17M7 14H13"></path>
                            <circle cx="12" cy="12" r="2"></circle>
                            </svg>
                        </div>
                    <div class="feature-content">
                        <span class="feature-number"><?php echo $estacionamientos; ?></span>
                        <span class="feature-label">Estacionamientos</span>
                    </div>
                </div>
                <?php endif; ?>
                </div>
            </div>
        </section>
        
        <!-- Contenido Principal -->
        <section class="property-content">
            <div class="container">
            <div class="content-layout">
                <!-- Columna principal -->
                    <div class="content-main">
                    
                    <!-- Descripción -->
                    <div class="content-section">
                            <h2 class="section-title">Descripción</h2>
                        <div class="description-content">
                                <?php if (get_the_content()) : ?>
                                    <?php the_content(); ?>
                                <?php else : ?>
                                    <p>Hermosa <?php echo esc_html($tipo); ?> moderna en sector exclusivo, ofrece espacios amplios y luminosos, perfecta para una familia que busca comodidad y elegancia en <?php echo esc_html($comuna); ?>.</p>
                                    
                                    <p>Esta propiedad cuenta con acabados de primera calidad, cocina equipada con electrodomésticos premium, amplios dormitorios con closets empotrados y hermoso jardín trasero. Su ubicación privilegiada permite fácil acceso a centros comerciales, colegios y transporte público.</p>
                                <?php endif; ?>
                        </div>
                            </div>
                            
                    <!-- Características -->
                    <div class="content-section">
                        <h2 class="section-title">Características</h2>
                        <div class="features-grid">
                                <?php
                                if ($caracteristicas) {
                                    $caracteristicas_array = explode("\n", $caracteristicas);
                                    foreach ($caracteristicas_array as $caracteristica) {
                                        $caracteristica = trim($caracteristica);
                                        if (!empty($caracteristica)) {
                                        echo '<div class="feature-item-list">';
                                        echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">';
                                        echo '<polyline points="20,6 9,17 4,12"></polyline>';
                                        echo '</svg>';
                                            echo '<span>' . esc_html($caracteristica) . '</span>';
                                            echo '</div>';
                                        }
                                    }
                                } else {
                                // Características por defecto
                                    $default_features = array(
                                        'Cocina equipada',
                                        'Jardín privado',
                                        'Terraza techada',
                                        'Calefacción central',
                                        'Closets empotrados',
                                        'Portón automático'
                                    );
                                    
                                    foreach ($default_features as $feature) {
                                    echo '<div class="feature-item-list">';
                                    echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">';
                                    echo '<polyline points="20,6 9,17 4,12"></polyline>';
                                    echo '</svg>';
                                        echo '<span>' . esc_html($feature) . '</span>';
                                        echo '</div>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    
                        <!-- Ubicación -->
                        <div class="content-section">
                            <h2 class="section-title">Ubicación</h2>
                        <div class="location-content">
                                    <?php if ($latitud && $longitud) : ?>
                                <div class="map-container">
                                        <iframe 
                                            src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBFw0Qbyq9zTFTd-tUY6dgsWUxW4U5Q&q=<?php echo $latitud; ?>,<?php echo $longitud; ?>" 
                                            width="100%" 
                                            height="300" 
                                        style="border:0; border-radius: 12px;" 
                                            allowfullscreen="" 
                                            loading="lazy">
                                        </iframe>
                                </div>
                                    <?php else : ?>
                                <div class="map-placeholder">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                            <p>Ubicación: <?php echo esc_html($comuna); ?></p>
                    </div>
                            <?php endif; ?>
                
                            <?php if ($lugares_cercanos) : ?>
                                <div class="nearby-places">
                                    <h3>Lugares cercanos</h3>
                                    <div class="places-grid">
                                    <?php
                                        $lugares_array = json_decode($lugares_cercanos, true);
                                        if (is_array($lugares_array)) {
                                            foreach ($lugares_array as $lugar) {
                                                $icon_class = 'fas fa-map-marker-alt';
                                                
                                                switch ($lugar['tipo']) {
                                                    case 'shopping':
                                                        $icon_class = 'fas fa-shopping-cart';
                                                        break;
                                                    case 'transporte':
                                                        $icon_class = 'fas fa-subway';
                                                        break;
                                                    case 'salud':
                                                        $icon_class = 'fas fa-hospital';
                                                        break;
                                                    case 'educacion':
                                                        $icon_class = 'fas fa-graduation-cap';
                                                        break;
                                                    case 'recreacion':
                                                        $icon_class = 'fas fa-gamepad';
                                                    break;
                                            }
                                            
                                            echo '<div class="place-item">';
                                            echo '<i class="' . esc_attr($icon_class) . '"></i>';
                                            echo '<div class="place-info">';
                                            echo '<span class="place-name">' . esc_html($lugar['nombre']) . '</span>';
                                            echo '<span class="place-distance">' . esc_html($lugar['distancia']) . '</span>';
                                            echo '</div>';
                                            echo '</div>';
                                        }
                                    }
                                    ?>
                                </div>
                                </div>
                            <?php endif; ?>
                                </div>
                        </div>
                        
                    <!-- Información adicional -->
                        <div class="content-section">
                        <h2 class="section-title">Información adicional</h2>
                                <div class="info-grid">
                                    <div class="info-item">
                                <span class="info-label">Año construcción</span>
                                        <span class="info-value"><?php echo $ano_construccion ?: '2018'; ?></span>
                                    </div>
                                    <div class="info-item">
                                <span class="info-label">Orientación</span>
                                        <span class="info-value"><?php echo $orientacion ?: 'Norte'; ?></span>
                                    </div>
                                    <div class="info-item">
                                <span class="info-label">Gastos comunes</span>
                                        <span class="info-value">$<?php echo $gastos_comunes ? number_format($gastos_comunes, 0, ',', '.') : '85.000'; ?></span>
                                    </div>
                                    <div class="info-item">
                                <span class="info-label">Estado</span>
                                <span class="info-value status-good"><?php echo $estado ?: 'Excelente'; ?></span>
                                    </div>
                                    <div class="info-item">
                                <span class="info-label">Disponibilidad</span>
                                        <span class="info-value"><?php echo $disponibilidad ?: 'Inmediata'; ?></span>
                                    </div>
                                    <div class="info-item">
                                <span class="info-label">Tipo</span>
                                <span class="info-value"><?php echo $tipo ?: 'Casa'; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Formulario de contacto móvil (solo visible en móvil) -->
                    <div class="contact-card contact-card-mobile">
                        <div class="contact-header">
                            <div class="cta-icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                            </div>
                            <h3>¿Te gustó esta propiedad?</h3>
                            <p>Agenda una visita o solicita más información</p>
                        </div>
                        
                        <form class="contact-form" method="post" action="">
                                <?php wp_nonce_field('property_contact_nonce', 'property_contact_nonce'); ?>
                                <input type="hidden" name="property_id" value="<?php echo get_the_ID(); ?>">
                                <input type="hidden" name="property_contact_submitted" value="1">
                                
                        <div class="form-group">
                                    <label for="contact_name_mobile">Nombre completo</label>
                                <input type="text" id="contact_name_mobile" name="contact_name" placeholder="Tu nombre" required>
                        </div>
                                
                        <div class="form-group">
                                    <label for="contact_email_mobile">Email</label>
                                    <input type="email" id="contact_email_mobile" name="contact_email" placeholder="tu@email.com" required>
                        </div>
                                
                        <div class="form-group">
                                    <label for="contact_phone_mobile">Teléfono</label>
                                    <input type="tel" id="contact_phone_mobile" name="contact_phone" placeholder="+56 9 1234 5678" required>
                        </div>
                                
                        <div class="form-group">
                                    <label for="contact_message_mobile">Mensaje</label>
                                <textarea id="contact_message_mobile" name="contact_message" placeholder="Cuéntanos qué te interesa..." rows="3"></textarea>
                        </div>
                    
                                <div class="form-group checkbox-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="request_visit" value="1">
                                        <span class="checkmark"></span>
                                        Solicitar visita programada
                                    </label>
                    </div>
                    
                                <div class="form-actions">
                                <button type="submit" class="btn-primary">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="22" y1="2" x2="11" y2="13"></line>
                                        <polygon points="22,2 15,22 11,13 2,9 22,2"></polygon>
                                    </svg>
                                    Enviar consulta
                                </button>
                                </div>
                    </form>
                </div>
                    
                <!-- Sidebar con formulario -->
                    <div class="content-sidebar">
                    <div class="contact-card">
                        <div class="contact-header">
                            <h3>¿Te interesa esta propiedad?</h3>
                            <p>Contáctanos para más información o agendar una visita</p>
                        </div>
                        
                        <form class="contact-form" method="post" action="">
                                <?php wp_nonce_field('property_contact_nonce', 'property_contact_nonce'); ?>
                                <input type="hidden" name="property_id" value="<?php echo get_the_ID(); ?>">
                                <input type="hidden" name="property_contact_submitted" value="1">
                                
                        <div class="form-group">
                                    <label for="contact_name">Nombre completo</label>
                                <input type="text" id="contact_name" name="contact_name" placeholder="Tu nombre" required>
                        </div>
                                
                        <div class="form-group">
                                    <label for="contact_email">Email</label>
                                    <input type="email" id="contact_email" name="contact_email" placeholder="tu@email.com" required>
                        </div>
                                
                        <div class="form-group">
                                    <label for="contact_phone">Teléfono</label>
                                    <input type="tel" id="contact_phone" name="contact_phone" placeholder="+56 9 1234 5678" required>
                        </div>
                                
                        <div class="form-group">
                                    <label for="contact_message">Mensaje</label>
                                <textarea id="contact_message" name="contact_message" placeholder="Cuéntanos qué te interesa..." rows="4"></textarea>
                        </div>
                    
                                <div class="form-group checkbox-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="request_visit" value="1">
                                        <span class="checkmark"></span>
                                        Solicitar visita programada
                                    </label>
                    </div>
                    
                                <div class="form-actions">
                                <button type="submit" class="btn-primary">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="22" y1="2" x2="11" y2="13"></line>
                                        <polygon points="22,2 15,22 11,13 2,9 22,2"></polygon>
                                    </svg>
                                    Enviar consulta
                                </button>
                                </div>
                    </form>
                </div>
                    </div>
                </div>
            </div>
        </section>
        
    <!-- Propiedades similares -->
        <section class="similar-properties">
            <div class="container">
            <h2 class="section-title">Propiedades similares</h2>
                <div class="properties-grid">
                    <?php
                    // Query para propiedades similares
                    $similar_args = array(
                        'post_type' => 'propiedad',
                        'posts_per_page' => 3,
                        'post__not_in' => array(get_the_ID()),
                        'meta_query' => array(
                            array(
                                'key' => '_propiedad_operacion',
                                'value' => $operacion,
                                'compare' => '='
                            )
                        )
                    );
                    
                    $similar_query = new WP_Query($similar_args);
                    
                    if ($similar_query->have_posts()) :
                        while ($similar_query->have_posts()) : $similar_query->the_post();
                            $similar_precio = get_post_meta(get_the_ID(), '_propiedad_precio', true);
                            $similar_operacion = get_post_meta(get_the_ID(), '_propiedad_operacion', true);
                            $similar_dormitorios = get_post_meta(get_the_ID(), '_propiedad_dormitorios', true);
                            $similar_banos = get_post_meta(get_the_ID(), '_propiedad_banos', true);
                            $similar_metros = get_post_meta(get_the_ID(), '_propiedad_metros', true);
                            $similar_comuna = get_post_meta(get_the_ID(), '_propiedad_comuna', true);
                            
                            $similar_precio_text = $similar_precio ? '$' . number_format(floatval($similar_precio), 0, ',', '.') : 'Consultar';
                            if ($similar_operacion === 'arriendo') {
                                $similar_precio_text .= '/mes';
                            }
                            ?>
                            
                            <div class="property-card">
                                <div class="card-image">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium'); ?>
                                        </a>
                                    <?php else : ?>
                                        <div class="no-image">
                                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                            <polyline points="9,22 9,12 15,12 15,22"></polyline>
                                        </svg>
    </div>
                                    <?php endif; ?>
                                    
                                    <div class="card-tag <?php echo $similar_operacion; ?>">
                                        <?php echo ucfirst($similar_operacion); ?>
                                    </div>
                                </div>
                                
                                <div class="card-content">
                                    <h3 class="card-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    
                                    <div class="card-details">
                                        <?php if ($similar_dormitorios) : ?>
                                        <span><?php echo $similar_dormitorios; ?> dorm</span>
                                        <?php endif; ?>
                                        <?php if ($similar_banos) : ?>
                                            <span>• <?php echo $similar_banos; ?> baños</span>
                                        <?php endif; ?>
                                        <?php if ($similar_metros) : ?>
                                            <span>• <?php echo $similar_metros; ?> m²</span>
                                        <?php endif; ?>
                        </div>
                                    
                                    <div class="card-footer">
                                        <div class="card-price"><?php echo $similar_precio_text; ?></div>
                                    <a href="<?php the_permalink(); ?>" class="card-btn">Ver detalles</a>
                        </div>
                        </div>
                        </div>
                            
                        <?php endwhile;
                        wp_reset_postdata();
                    endif; ?>
                </div>
                
            <div class="view-all">
                <a href="<?php echo get_post_type_archive_link('propiedad'); ?>" class="btn-view-all">
                    Ver todas las propiedades
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="7" y1="17" x2="17" y2="7"></line>
                        <polyline points="7,7 17,7 17,17"></polyline>
                    </svg>
                </a>
                </div>
            </div>
        </section>
        
        <?php endwhile; ?>
</div>

<!-- Botones de contacto fijos para móvil -->
<div class="fixed-contact-buttons">
    <?php
    // Obtener datos del agente para los botones fijos
    $agente = mjpropiedades_get_property_agent();
    $agente_data = $agente ? mjpropiedades_get_agent_data($agente->ID) : null;
    
    if ($agente_data && $agente_data['telefono']) :
        ?>
        <a href="tel:<?php echo esc_attr($agente_data['telefono']); ?>" class="fixed-btn call-btn">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
            </svg>
            <span>Llamar Ahora</span>
        </a>
        <?php
    else :
        ?>
        <a href="tel:+56912345678" class="fixed-btn call-btn">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
            </svg>
            <span>Llamar Ahora</span>
        </a>
        <?php
    endif;
    
    // Determinar número de WhatsApp
    $whatsapp_number = $agente_data['whatsapp'] ? $agente_data['whatsapp'] : ($agente_data['telefono'] ? $agente_data['telefono'] : '+56912345678');
    $whatsapp_message = "Hola, estoy interesado/a en esta propiedad: " . get_the_title() . " en " . $comuna . ". ¿Podrías darme más información?";
    $whatsapp_url = "https://wa.me/" . str_replace(array('+', ' ', '-'), '', $whatsapp_number) . "?text=" . urlencode($whatsapp_message);
    ?>
    
    <!-- Botón de WhatsApp del agente específico -->
    <?php if ($agente_data && $agente_data['telefono']) : ?>
        <?php
        // Usar WhatsApp específico del agente o su teléfono como fallback
        $agente_whatsapp = $agente_data['whatsapp'] ? $agente_data['whatsapp'] : $agente_data['telefono'];
        $agente_whatsapp_message = "Hola " . esc_html($agente_data['nombre']) . ", estoy interesado/a en esta propiedad: " . get_the_title() . " en " . $comuna . ". ¿Podrías darme más información?";
        $agente_whatsapp_url = "https://wa.me/" . str_replace(array('+', ' ', '-'), '', $agente_whatsapp) . "?text=" . urlencode($agente_whatsapp_message);
        ?>
        <a href="<?php echo esc_url($agente_whatsapp_url); ?>" class="fixed-btn whatsapp-btn">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
            </svg>
            <span>WhatsApp</span>
        </a>
    <?php else : ?>
        <!-- Fallback para cuando no hay agente específico -->
        <a href="<?php echo esc_url($whatsapp_url); ?>" class="fixed-btn whatsapp-btn">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
            </svg>
            <span>WhatsApp</span>
        </a>
    <?php endif; ?>
</div>

<style>
/* Variables CSS */
:root {
    --primary-color: #2563eb;
    --primary-hover: #1d4ed8;
    --secondary-color: #f59e0b;
    --secondary-hover: #d97706;
    --success-color: #10b981;
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
    --text-light: #9ca3af;
    --bg-primary: #ffffff;
    --bg-secondary: #f9fafb;
    --bg-light: #f3f4f6;
    --border-color: #e5e7eb;
    --border-light: #f3f4f6;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --radius-sm: 6px;
    --radius-md: 8px;
    --radius-lg: 12px;
    --radius-xl: 16px;
    --header-height: 80px;
}

/* Reset y base */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.property-detail-page {
    background: var(--bg-secondary);
    min-height: 100vh;
    padding-top: var(--header-height);
}

/* Ajuste para la barra de administración de WordPress */
body.admin-bar .property-detail-page {
    padding-top: calc(var(--header-height) + 32px);
}

@media (max-width: 782px) {
    body.admin-bar .property-detail-page {
        padding-top: var(--header-height);
    }
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Hero Gallery Section */
.hero-gallery-section {
    background: var(--bg-primary);
    padding: 2rem 0;
}

.hero-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 3rem;
    align-items: start;
}

.gallery-container {
    position: relative;
    background: var(--bg-secondary);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
}

.gallery-main {
    position: relative;
    height: 400px;
    overflow: hidden;
}

.gallery-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
}

.gallery-image.active {
    opacity: 1;
}

.gallery-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    background: var(--bg-light);
    color: var(--text-light);
}

.no-image-icon {
    margin-bottom: 1rem;
}

.no-image p {
    font-size: 1.125rem;
    font-weight: 500;
}

/* Navegación de galería */
.gallery-navigation {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 100%;
    display: flex;
    justify-content: space-between;
    padding: 0 1rem;
    pointer-events: none;
}

.nav-btn {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.7);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    color: white;
    pointer-events: auto;
    z-index: 10;
}

.nav-btn:hover {
    background: rgba(0, 0, 0, 0.9);
    transform: scale(1.1);
}

/* Indicadores de puntos */
.gallery-dots {
    position: absolute;
    bottom: 1rem;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 0.5rem;
    z-index: 10;
}

.dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    border: none;
    background: rgba(255, 255, 255, 0.5);
    cursor: pointer;
    transition: all 0.3s ease;
}

.dot.active,
.dot:hover {
    background: white;
    transform: scale(1.2);
}

/* Miniaturas */
.gallery-thumbnails {
    position: absolute;
    bottom: 3rem;
    left: 1rem;
    right: 1rem;
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    padding-bottom: 0.5rem;
}

.thumbnail {
    flex-shrink: 0;
    width: 60px;
    height: 45px;
    border-radius: var(--radius-sm);
    overflow: hidden;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.3s ease;
    opacity: 0.7;
}

.thumbnail.active,
.thumbnail:hover {
    border-color: white;
    opacity: 1;
    transform: scale(1.05);
}

.thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Panel de información */
.info-panel {
    background: var(--bg-primary);
    padding: 2rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-light);
    position: sticky;
    top: 2rem;
}

.property-price {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.property-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    line-height: 1.3;
}

.property-location {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
    font-size: 1rem;
    margin-bottom: 1.5rem;
}

.property-location svg {
    flex-shrink: 0;
}

/* Características principales */
.main-features-grid {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-primary);
    font-size: 0.875rem;
    font-weight: 500;
}

.feature-item svg {
    color: var(--text-secondary);
}

/* Separador */
.separator {
    height: 1px;
    background: var(--border-color);
    margin: 1.5rem 0;
}

/* Información del agente */
.agent-info {
    margin-top: 1.5rem;
}

.agent-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.agent-profile {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.agent-avatar {
    width: 48px;
    height: 48px;
    background: var(--primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
    overflow: hidden;
}

.agent-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.agent-details {
    flex: 1;
}

.agent-name {
    font-size: 1rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.agent-specialization {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
}

.agent-rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.stars {
    display: flex;
    gap: 0.125rem;
}

.stars svg {
    color: #fbbf24;
    width: 16px;
    height: 16px;
}

.rating-text {
    font-size: 0.75rem;
    color: var(--text-secondary);
}

/* Botones de contacto */
.contact-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.contact-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 0.875rem 1rem;
    border-radius: var(--radius-md);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.call-btn {
    background: var(--primary-color);
    color: white;
}

.call-btn:hover {
    background: var(--primary-hover);
    transform: translateY(-1px);
}

.whatsapp-btn {
    background: #25d366 !important;
    color: white !important;
    position: static !important;
    width: auto !important;
    height: auto !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 0.5rem !important;
    padding: 12px 20px !important;
    font-weight: 600 !important;
    text-decoration: none !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 2px 8px rgba(37, 211, 102, 0.2) !important;
}

.whatsapp-btn:hover {
    background: #128c7e !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3) !important;
}

.whatsapp-btn svg {
    width: 20px !important;
    height: 20px !important;
    fill: currentColor !important;
}

.email-btn {
    background: var(--bg-primary);
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
}

.email-btn:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-1px);
}

/* Property Info */
.property-info {
    background: var(--bg-primary);
    padding: 3rem 0;
}

.property-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 3rem;
    gap: 2rem;
}

.property-title {
    font-size: 2.25rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    line-height: 1.2;
}

.property-location {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
    font-size: 1.125rem;
}

.property-location svg {
    flex-shrink: 0;
}

.price-section {
    text-align: right;
}

.price-main {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-color);
    line-height: 1;
}

.price-uf {
    font-size: 1.125rem;
    color: var(--text-light);
    margin-top: 0.25rem;
}

.main-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: var(--bg-secondary);
    border-radius: var(--radius-lg);
    transition: all 0.3s ease;
}

.feature-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.feature-icon {
    width: 48px;
    height: 48px;
    background: var(--primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.feature-content {
    display: flex;
    flex-direction: column;
}

.feature-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1;
}

.feature-label {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-top: 0.25rem;
}

/* Property Content */
.property-content {
    padding: 4rem 0;
}

.content-layout {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 3rem;
}

.content-main {
    display: flex;
    flex-direction: column;
    gap: 2.5rem;
}

.content-section {
    background: var(--bg-primary);
    padding: 2rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-light);
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1.5rem;
}

.description-content {
    color: var(--text-primary);
    line-height: 1.7;
    font-size: 1rem;
}

.description-content p {
    margin-bottom: 1rem;
}

.description-content p:last-child {
    margin-bottom: 0;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.feature-item-list {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 0;
}

.feature-item-list svg {
    color: var(--success-color);
    flex-shrink: 0;
}

.feature-item-list span {
    color: var(--text-primary);
}

/* Location */
.location-content {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.map-container {
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md);
}

.map-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 300px;
    background: var(--bg-light);
    border-radius: var(--radius-lg);
    color: var(--text-light);
}

.map-placeholder svg {
    margin-bottom: 1rem;
}

.nearby-places h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.places-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.place-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--bg-secondary);
    border-radius: var(--radius-md);
}

.place-item i {
    width: 40px;
    height: 40px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
}

.place-info {
    display: flex;
    flex-direction: column;
}

.place-name {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.875rem;
}

.place-distance {
    font-size: 0.75rem;
    color: var(--text-secondary);
}

/* Info Grid */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    padding: 1rem;
    background: var(--bg-secondary);
    border-radius: var(--radius-md);
}

.info-label {
    font-weight: 600;
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.info-value {
    font-weight: 600;
    color: var(--text-primary);
}

.status-good {
    color: var(--success-color);
}

/* Contact Card */
.content-sidebar {
    position: sticky;
    top: 2rem;
    height: fit-content;
}

.contact-card {
    background: var(--bg-primary);
    padding: 2rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-light);
}

/* Formulario móvil - oculto por defecto */
.contact-card-mobile {
    display: none;
}

/* Botones de contacto fijos para móvil */
.fixed-contact-buttons {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--bg-primary);
    border-top: 1px solid var(--border-color);
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
    padding: 1rem;
    z-index: 1000;
    gap: 0.75rem;
    flex-direction: column; /* Cambiar a columna */
}

.fixed-btn {
    width: 100%; /* Ancho completo */
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1rem;
    border-radius: var(--radius-md);
    text-decoration: none;
    font-weight: 700;
    font-size: 0.9375rem;
    transition: all 0.2s ease;
    min-height: 56px;
    box-shadow: var(--shadow-sm);
}

.fixed-btn.call-btn {
    background: var(--primary-color);
    color: white;
    border: 2px solid var(--primary-color);
}

/* Estilos específicos para botones fijos de WhatsApp - Mayor especificidad */
.fixed-contact-buttons .fixed-btn.whatsapp-btn {
    background: #25d366 !important;
    color: white !important;
    border: 2px solid #25d366 !important;
    width: 100% !important;
    padding: 1rem !important;
    border-radius: var(--radius-md) !important;
    gap: 0.5rem !important;
    font-size: 0.9375rem !important;
    font-weight: 700 !important;
    min-height: 56px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    box-shadow: var(--shadow-sm) !important;
    transition: all 0.2s ease !important;
}

.fixed-contact-buttons .fixed-btn.whatsapp-btn:active {
    transform: scale(0.98) !important;
    box-shadow: none !important;
}

.fixed-contact-buttons .fixed-btn.whatsapp-btn svg {
    width: 24px !important;
    height: 24px !important;
    fill: currentColor !important;
}

.fixed-btn:active {
    transform: scale(0.98);
    box-shadow: none;
}

.fixed-btn svg {
    width: 24px;
    height: 24px;
    flex-shrink: 0;
}

.contact-header {
    text-align: center;
    margin-bottom: 2rem;
}

/* Ícono CTA para formulario móvil */
.cta-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-blue));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.25rem;
    color: white;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
}

.cta-icon svg {
    width: 32px;
    height: 32px;
}

.contact-header h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.contact-header p {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.contact-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.875rem;
}

.form-group input,
.form-group textarea {
    padding: 0.75rem 1rem;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    font-size: 1rem;
    transition: all 0.3s ease;
    background: var(--bg-primary);
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.checkbox-group {
    flex-direction: row;
    align-items: center;
    gap: 0.75rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    font-size: 0.875rem;
    color: var(--text-primary);
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    margin: 0;
}

.form-actions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
    padding: 0.875rem 1.5rem;
    border: none;
    border-radius: var(--radius-md);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-primary:hover {
    background: var(--primary-hover);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.btn-secondary {
    background: var(--secondary-color);
    color: white;
    padding: 0.875rem 1.5rem;
    border: none;
    border-radius: var(--radius-md);
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-secondary:hover {
    background: var(--secondary-hover);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

/* Similar Properties */
.similar-properties {
    background: var(--bg-primary);
    padding: 4rem 0;
}

.similar-properties .section-title {
    text-align: center;
    margin-bottom: 3rem;
}

.properties-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.property-card {
    background: var(--bg-primary);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-light);
    transition: all 0.3s ease;
}

.property-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.card-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.property-card:hover .card-image img {
    transform: scale(1.05);
}

.no-image {
    height: 100%;
    background: var(--bg-light);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-light);
}

.card-tag {
    position: absolute;
    top: 1rem;
    left: 1rem;
    background: var(--primary-color);
    color: white;
    padding: 0.375rem 0.75rem;
    border-radius: var(--radius-xl);
    font-weight: 600;
    font-size: 0.75rem;
}

.card-tag.arriendo {
    background: var(--secondary-color);
}

.card-content {
    padding: 1.5rem;
}

.card-title {
    margin-bottom: 1rem;
}

.card-title a {
    color: var(--text-primary);
    text-decoration: none;
    font-weight: 600;
    font-size: 1.125rem;
    transition: color 0.3s ease;
}

.card-title a:hover {
    color: var(--primary-color);
}

.card-details {
    color: var(--text-secondary);
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-price {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary-color);
}

.card-btn {
    background: var(--secondary-color);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: var(--radius-sm);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

.card-btn:hover {
    background: var(--secondary-hover);
    transform: translateY(-1px);
}

.view-all {
    text-align: center;
}

.btn-view-all {
    background: var(--primary-color);
    color: white;
    padding: 1rem 2rem;
    border-radius: var(--radius-md);
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-view-all:hover {
    background: var(--primary-hover);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .hero-container {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .info-panel {
        position: static;
        order: -1;
    }
    
    .content-layout {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .content-sidebar {
        position: static;
        order: -1;
    }
    
    .property-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1.5rem;
    }
    
    .price-section {
        text-align: left;
    }
}

@media (max-width: 768px) {
    /* Layout principal móvil */
    .hero-gallery-section {
        padding: 0;
        background: var(--bg-primary);
    }
    
    .hero-container {
        padding: 0;
        gap: 0;
        grid-template-columns: 1fr;
    }
    
    /* Galería móvil - Pantalla completa inmersiva */
    .gallery-container {
        border-radius: 0;
        box-shadow: none;
        margin-bottom: 0;
    }
    
    .gallery-main {
        height: 280px;
        border-radius: 0;
    }
    
    /* Ocultar elementos innecesarios de la galería en móvil */
    .gallery-dots,
    .gallery-thumbnails {
        display: none;
    }
    
    /* Mostrar navegación minimalista en móvil */
    .gallery-navigation {
        display: flex !important;
        position: absolute !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        width: 100% !important;
        justify-content: space-between !important;
        padding: 0 1rem !important;
        pointer-events: none !important;
        z-index: 20 !important;
        left: 0 !important;
        right: 0 !important;
    }
    
    /* Asegurar que las flechas sean visibles solo cuando hay múltiples imágenes */
    .gallery-container.single-image .gallery-navigation {
        display: none !important;
    }
    
    /* Flechas minimalistas para móvil */
    .nav-btn {
        width: 44px !important;
        height: 44px !important;
        background: rgba(255, 255, 255, 0.95) !important;
        border: none !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        backdrop-filter: blur(10px) !important;
        pointer-events: auto !important;
        opacity: 1 !important;
        color: #1f2937 !important;
        z-index: 21 !important;
        position: relative !important;
    }
    
    .nav-btn:hover,
    .nav-btn:active {
        background: rgba(255, 255, 255, 1) !important;
        transform: scale(1.05) !important;
        opacity: 1 !important;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2) !important;
    }
    
    .nav-btn svg {
        width: 20px !important;
        height: 20px !important;
        color: #1f2937 !important;
        stroke: #1f2937 !important;
        stroke-width: 2.5 !important;
        fill: none !important;
    }
    
    /* Panel de información móvil - Diseño limpio y espacioso */
    .info-panel {
        position: static;
        padding: 1.75rem 1.25rem;
        border-radius: 0;
        box-shadow: none;
        border: none;
        border-bottom: 1px solid var(--border-light);
    }
    
    /* Precio destacado en móvil */
    .property-price {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 1rem;
        letter-spacing: -0.5px;
    }
    
    /* Título optimizado */
    .property-title {
        font-size: 1.375rem;
        font-weight: 700;
        line-height: 1.4;
        margin-bottom: 0.75rem;
    }
    
    /* Ubicación más visible */
    .property-location {
        font-size: 0.9375rem;
        margin-bottom: 1.5rem;
        color: var(--text-secondary);
    }
    
    /* Grid de características móvil - Diseño horizontal compacto */
    .main-features-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }
    
    .main-features-grid .feature-item {
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 0.375rem;
        padding: 0.75rem 0.5rem;
        background: var(--bg-light);
        border-radius: var(--radius-md);
        font-size: 0.8125rem;
    }
    
    .main-features-grid .feature-item svg {
        width: 18px;
        height: 18px;
    }
    
    /* Separador sutil */
    .separator {
        margin: 1.25rem 0;
        background: var(--border-light);
    }
    
    /* Información del agente - Diseño centrado y minimalista */
    .agent-info {
        text-align: center;
        margin-top: 0;
    }
    
    .agent-title {
        font-size: 0.9375rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-secondary);
        margin-bottom: 1rem;
        text-align: center;
    }
    
    .agent-profile {
        flex-direction: column;
        align-items: center;
        gap: 0.875rem;
        margin-bottom: 1.25rem;
    }
    
    .agent-avatar {
        width: 56px;
        height: 56px;
    }
    
    .agent-details {
        text-align: center;
    }
    
    .agent-name {
        font-size: 1.125rem;
        font-weight: 700;
        margin-bottom: 0.375rem;
    }
    
    .agent-specialization {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-bottom: 0.625rem;
    }
    
    .agent-rating {
        justify-content: center;
        gap: 0.5rem;
    }
    
    .stars {
        justify-content: center;
    }
    
    .rating-text {
        font-size: 0.8125rem;
    }
    
    /* Ocultar botones de contacto del panel en móvil */
    .contact-buttons {
        display: none;
    }
    
    /* Mostrar botones fijos en móvil */
    .fixed-contact-buttons {
        display: flex;
    }
    
    /* Ajustar padding bottom para los botones fijos (ahora en columna) */
    .property-detail-page {
        padding-bottom: 180px; /* Más espacio para 3 botones en columna */
    }
    
    /* Container general móvil */
    .container {
        padding: 0 1.25rem;
    }
    
    /* Ocultar sección duplicada de información en móvil */
    .property-info {
        display: none;
    }
    
    /* Contenido principal móvil */
    .property-content {
        padding: 1.5rem 0;
        background: var(--bg-secondary);
    }
    
    .content-section {
        padding: 1.5rem 1.25rem;
        margin-bottom: 1rem;
        border-radius: var(--radius-lg);
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1.25rem;
    }
    
    /* Características lista móvil */
    .features-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .feature-item-list {
        padding: 0.625rem 0;
        border-bottom: 1px solid var(--border-light);
    }
    
    .feature-item-list:last-child {
        border-bottom: none;
    }
    
    /* Info grid móvil */
    .info-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .info-item {
        padding: 1rem;
        text-align: center;
    }
    
    .info-label {
        font-size: 0.8125rem;
        margin-bottom: 0.375rem;
    }
    
    .info-value {
        font-size: 0.9375rem;
    }
    
    /* Sidebar de contacto - ocultar en móvil */
    .content-sidebar {
        display: none;
    }
    
    /* Mostrar formulario móvil después de información adicional */
    .contact-card-mobile {
        display: block;
        padding: 1.5rem 1.25rem;
        margin-top: 1rem;
        margin-bottom: 1rem;
        border-radius: var(--radius-lg);
        background: var(--bg-primary);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-light);
        animation: fadeIn 0.6s ease-out;
    }
    
    .contact-header h3 {
        font-size: 1.25rem;
        margin-bottom: 0.625rem;
    }
    
    .contact-header p {
        font-size: 0.875rem;
    }
    
    /* Formulario móvil */
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-group input,
    .form-group textarea {
        padding: 0.875rem 1rem;
        font-size: 1rem;
        border-radius: var(--radius-md);
    }
    
    .btn-primary {
        padding: 1rem 1.5rem;
        font-size: 1rem;
        font-weight: 700;
        min-height: 52px;
    }
    
    .btn-primary:active {
        transform: scale(0.98);
    }
    
    /* Propiedades similares móvil */
    .similar-properties {
        padding: 2rem 0;
        background: var(--bg-secondary);
    }
    
    .similar-properties .section-title {
        font-size: 1.375rem;
        margin-bottom: 1.5rem;
        padding: 0 1.25rem;
    }
    
    .properties-grid {
        grid-template-columns: 1fr;
        gap: 1.25rem;
        padding: 0 1.25rem;
    }
    
    .property-card {
        border-radius: var(--radius-lg);
        overflow: hidden;
    }
    
    .card-image {
        height: 220px;
    }
    
    .card-content {
        padding: 1.25rem;
    }
    
    .card-title a {
        font-size: 1.0625rem;
    }
    
    /* Navegación con swipe para galería móvil */
    .gallery-container {
        position: relative;
        touch-action: pan-y pinch-zoom;
    }
    
    /* Indicador visual sutil de imágenes */
    .gallery-main::after {
        content: attr(data-count);
        position: absolute;
        bottom: 1rem;
        right: 1rem;
        background: rgba(0, 0, 0, 0.6);
        color: white;
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        backdrop-filter: blur(10px);
    }
}

@media (max-width: 480px) {
    /* Layout para dispositivos pequeños */
    .hero-gallery-section {
        padding: 0;
    }
    
    /* Galería más compacta en pantallas pequeñas */
    .gallery-main {
        height: 240px;
    }
    
    /* Panel de información aún más compacto */
    .info-panel {
        padding: 1.5rem 1rem;
    }
    
    /* Ajustes de tipografía para pantallas pequeñas */
    .property-price {
        font-size: 1.75rem;
        font-weight: 800;
    }
    
    .property-title {
        font-size: 1.25rem;
        line-height: 1.35;
    }
    
    .property-location {
        font-size: 0.875rem;
    }
    
    /* Grid de características más compacto */
    .main-features-grid {
        gap: 0.5rem;
    }
    
    .main-features-grid .feature-item {
        padding: 0.625rem 0.375rem;
        font-size: 0.75rem;
        gap: 0.25rem;
    }
    
    .main-features-grid .feature-item svg {
        width: 16px;
        height: 16px;
    }
    
    /* Información del agente compacta */
    .agent-profile {
        gap: 0.75rem;
    }
    
    .agent-avatar {
        width: 48px;
        height: 48px;
    }
    
    .agent-name {
        font-size: 1.0625rem;
    }
    
    .agent-specialization {
        font-size: 0.8125rem;
    }
    
    .agent-title {
        font-size: 0.875rem;
    }
    
    .agent-rating {
        gap: 0.375rem;
    }
    
    .rating-text {
        font-size: 0.75rem;
    }
    
    /* Botones fijos para móviles pequeños */
    .fixed-contact-buttons {
        padding: 0.875rem;
        gap: 0.625rem;
    }
    
    .fixed-btn {
        padding: 0.875rem;
        font-size: 0.875rem;
        min-height: 52px;
    }
    
    .fixed-btn svg {
        width: 22px;
        height: 22px;
    }
    
    /* Estilos específicos para WhatsApp en móviles pequeños */
    .fixed-contact-buttons .fixed-btn.whatsapp-btn {
        padding: 0.875rem !important;
        font-size: 0.875rem !important;
        min-height: 52px !important;
    }
    
    .fixed-contact-buttons .fixed-btn.whatsapp-btn svg {
        width: 22px !important;
        height: 22px !important;
    }
    
    /* Ajustar padding bottom para móviles pequeños (3 botones en columna) */
    .property-detail-page {
        padding-bottom: 170px; /* Ajustado para 3 botones en columna */
    }
    
    /* Container más compacto */
    .container {
        padding: 0 1rem;
    }
    
    /* Secciones de contenido */
    .content-section {
        padding: 1.25rem 1rem;
        margin-bottom: 0.875rem;
    }
    
    .section-title {
        font-size: 1.125rem;
        margin-bottom: 1rem;
    }
    
    /* Descripción más compacta */
    .description-content {
        font-size: 0.9375rem;
        line-height: 1.6;
    }
    
    /* Características en lista */
    .feature-item-list {
        padding: 0.5rem 0;
        font-size: 0.875rem;
    }
    
    .feature-item-list svg {
        width: 14px;
        height: 14px;
    }
    
    /* Grid de info más compacto */
    .info-grid {
        gap: 0.75rem;
    }
    
    .info-item {
        padding: 0.875rem;
    }
    
    .info-label {
        font-size: 0.75rem;
    }
    
    .info-value {
        font-size: 0.875rem;
    }
    
    /* Formulario de contacto móvil */
    .contact-card-mobile {
        padding: 1.25rem 1rem;
        margin-top: 0.875rem;
    }
    
    .contact-card-mobile .cta-icon {
        width: 56px;
        height: 56px;
        margin-bottom: 1rem;
    }
    
    .contact-card-mobile .cta-icon svg {
        width: 28px;
        height: 28px;
    }
    
    .contact-card-mobile .contact-header h3 {
        font-size: 1.125rem;
    }
    
    .contact-card-mobile .contact-header p {
        font-size: 0.8125rem;
    }
    
    .form-group {
        margin-bottom: 0.875rem;
    }
    
    .form-group input,
    .form-group textarea {
        padding: 0.75rem 0.875rem;
        font-size: 0.9375rem;
    }
    
    .form-group label {
        font-size: 0.8125rem;
    }
    
    .btn-primary {
        padding: 0.875rem 1.25rem;
        font-size: 0.9375rem;
        min-height: 48px;
    }
    
    /* Propiedades similares */
    .similar-properties {
        padding: 1.5rem 0;
    }
    
    .similar-properties .section-title {
        font-size: 1.25rem;
        padding: 0 1rem;
        margin-bottom: 1.25rem;
    }
    
    .properties-grid {
        gap: 1rem;
        padding: 0 1rem;
    }
    
    .card-image {
        height: 200px;
    }
    
    .card-content {
        padding: 1rem;
    }
    
    .card-title a {
        font-size: 1rem;
    }
    
    .card-details {
        font-size: 0.8125rem;
    }
    
    .card-price {
        font-size: 1.125rem;
    }
    
    .card-btn {
        padding: 0.5rem 0.875rem;
        font-size: 0.8125rem;
    }
    
    /* Botón ver todas */
    .btn-view-all {
        padding: 0.875rem 1.5rem;
        font-size: 0.9375rem;
    }
    
    /* Indicador de contador de imágenes */
    .gallery-main::after {
        bottom: 0.75rem;
        right: 0.75rem;
        padding: 0.25rem 0.625rem;
        font-size: 0.6875rem;
    }
    
    /* Flechas más compactas para móviles pequeños */
    .nav-btn {
        width: 40px !important;
        height: 40px !important;
        background: rgba(255, 255, 255, 0.98) !important;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.12) !important;
    }
    
    .nav-btn svg {
        width: 18px !important;
        height: 18px !important;
        color: #1f2937 !important;
        stroke: #1f2937 !important;
        stroke-width: 2.5 !important;
        fill: none !important;
    }
    
    /* Ajustar padding para móviles pequeños */
    .gallery-navigation {
        padding: 0 0.75rem !important;
    }
}

/* Scrollbar personalizada */
.gallery-thumbnails::-webkit-scrollbar {
    height: 4px;
}

.gallery-thumbnails::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 2px;
}

.gallery-thumbnails::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.5);
    border-radius: 2px;
}

.gallery-thumbnails::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.8);
}

/* Animaciones suaves */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-10px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.content-section {
    animation: fadeIn 0.6s ease-out;
}

.contact-card {
    animation: fadeIn 0.8s ease-out;
}

.property-card {
    animation: fadeIn 0.6s ease-out;
}

/* Animación para la galería */
.gallery-image {
    transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Feedback visual mejorado para dispositivos táctiles */
@media (hover: none) and (pointer: coarse) {
    /* Estilos específicos para dispositivos táctiles */
    
    .contact-btn,
    .btn-primary,
    .btn-secondary,
    .card-btn,
    .btn-view-all {
        -webkit-tap-highlight-color: transparent;
        touch-action: manipulation;
        user-select: none;
    }
    
    /* Efecto de presionado más pronunciado en móviles */
    .contact-btn:active {
        transform: scale(0.96);
        opacity: 0.9;
    }
    
    .btn-primary:active {
        transform: scale(0.96);
        opacity: 0.9;
    }
    
    /* Flechas de navegación para táctiles */
    .nav-btn {
        -webkit-tap-highlight-color: transparent;
        touch-action: manipulation;
        user-select: none;
        min-width: 44px;
        min-height: 44px;
    }
    
    .nav-btn:active {
        transform: scale(0.95);
        background: rgba(255, 255, 255, 0.8);
        transition: all 0.1s ease;
    }
    
    /* Área táctil extendida para elementos pequeños */
    .agent-rating .stars svg,
    .feature-item svg {
        padding: 4px;
        margin: -4px;
    }
    
    /* Feedback visual en cards */
    .property-card:active {
        transform: scale(0.98);
        transition: transform 0.1s ease;
    }
    
    /* Transiciones más rápidas en móvil para mejor respuesta */
    * {
        transition-duration: 0.2s !important;
    }
}

/* Mejoras de rendimiento para animaciones en móvil */
@media (max-width: 768px) {
    .gallery-image,
    .gallery-image img {
        will-change: opacity;
        transform: translateZ(0);
        backface-visibility: hidden;
    }
    
    /* Optimización de scroll */
    .property-content,
    .similar-properties {
        -webkit-overflow-scrolling: touch;
    }
}

/* Estados de carga */
.loading {
    opacity: 0.7;
    pointer-events: none;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Modo oscuro deshabilitado - Siempre usar tema claro */
/* @media (prefers-color-scheme: dark) {
    :root {
        --text-primary: #f3f4f6;
        --text-secondary: #d1d5db;
        --text-light: #9ca3af;
        --bg-primary: #1f2937;
        --bg-secondary: #111827;
        --bg-light: #374151;
        --border-color: #374151;
        --border-light: #4b5563;
    }
    
    @media (max-width: 768px) {
        /* Ajustes específicos para móvil en modo oscuro */
        .info-panel {
            background: var(--bg-primary);
            border-bottom-color: var(--border-color);
        }
        
        .content-section,
        .contact-card {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
        }
        
        .main-features-grid .feature-item {
            background: var(--bg-light);
        }
        
        /* Mejorar contraste de botones en modo oscuro */
        .call-btn {
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
        }
        
        .whatsapp-btn {
            box-shadow: 0 2px 8px rgba(37, 211, 102, 0.3) !important;
        }
    }
} */

/* Mejoras de accesibilidad */
@media (prefers-reduced-motion: reduce) {
    /* Reducir animaciones para usuarios con preferencias de movimiento reducido */
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
    
    .gallery-image {
        transition: none;
    }
}

/* Mejoras para lectores de pantalla */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}

/* Focus visible mejorado para navegación con teclado */
.contact-btn:focus-visible,
.btn-primary:focus-visible,
.nav-btn:focus-visible,
.dot:focus-visible {
    outline: 3px solid var(--primary-color);
    outline-offset: 2px;
}

/* Mejoras de contraste para WCAG AA */
@media (max-width: 768px) {
    .agent-specialization,
    .property-location,
    .info-label {
        color: var(--text-secondary);
        /* Asegurar contraste mínimo 4.5:1 */
    }
    
    /* Hacer los enlaces más visibles en móvil */
    a {
        text-decoration-skip-ink: auto;
    }
    
    a:focus-visible {
        outline: 2px solid var(--primary-color);
        outline-offset: 2px;
        border-radius: 2px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables globales para la galería
    let currentImageIndex = 0;
    const galleryImages = document.querySelectorAll('.gallery-image');
    const totalImages = galleryImages.length;
    
    // Función para cambiar imagen
    window.changeImage = function(direction) {
        if (totalImages <= 1) return;
        
        currentImageIndex += direction;
        
        if (currentImageIndex >= totalImages) {
            currentImageIndex = 0;
        } else if (currentImageIndex < 0) {
            currentImageIndex = totalImages - 1;
        }
        
        updateGallery();
    };
    
    // Función para ir a una imagen específica
    window.goToImage = function(index) {
        if (index >= 0 && index < totalImages) {
            currentImageIndex = index;
            updateGallery();
        }
    };
    
    // Función para actualizar la galería
    function updateGallery() {
        // Actualizar imágenes principales
        galleryImages.forEach(function(img, index) {
            img.classList.toggle('active', index === currentImageIndex);
        });
        
        // Actualizar miniaturas
        const thumbnails = document.querySelectorAll('.thumbnail');
        thumbnails.forEach(function(thumb, index) {
            thumb.classList.toggle('active', index === currentImageIndex);
        });
        
        // Actualizar puntos indicadores
        const dots = document.querySelectorAll('.dot');
        dots.forEach(function(dot, index) {
            dot.classList.toggle('active', index === currentImageIndex);
        });
    }
    
    // Navegación con teclado
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
            changeImage(-1);
        } else if (e.key === 'ArrowRight') {
            changeImage(1);
        }
    });
    
    // Soporte táctil (swipe) para navegación en móvil
    const galleryContainer = document.querySelector('.gallery-container');
    if (galleryContainer && totalImages > 1) {
        let touchStartX = 0;
        let touchEndX = 0;
        let touchStartY = 0;
        let touchEndY = 0;
        
        // Agregar clase para ocultar flechas si solo hay una imagen
        if (totalImages <= 1) {
            galleryContainer.classList.add('single-image');
        }
        
        // Eventos táctiles para swipe
        galleryContainer.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
            touchStartY = e.changedTouches[0].screenY;
        }, { passive: true });
        
        galleryContainer.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            touchEndY = e.changedTouches[0].screenY;
            handleSwipe();
        }, { passive: true });
        
        function handleSwipe() {
            const swipeThreshold = 50; // Mínimo de píxeles para considerar un swipe
            const horizontalSwipe = Math.abs(touchEndX - touchStartX);
            const verticalSwipe = Math.abs(touchEndY - touchStartY);
            
            // Solo procesar si el swipe es más horizontal que vertical
            if (horizontalSwipe > verticalSwipe && horizontalSwipe > swipeThreshold) {
                if (touchEndX < touchStartX) {
                    // Swipe izquierda - siguiente imagen
                    changeImage(1);
                }
                if (touchEndX > touchStartX) {
                    // Swipe derecha - imagen anterior
                    changeImage(-1);
                }
            }
        }
        
        // Eventos para click en escritorio (opcional)
        galleryContainer.addEventListener('click', function(e) {
            // Solo en dispositivos no táctiles
            if (!('ontouchstart' in window)) {
                const rect = this.getBoundingClientRect();
                const clickX = e.clientX - rect.left;
                const containerWidth = rect.width;
                
                // Si se hace click en el 20% izquierdo de la imagen
                if (clickX < containerWidth * 0.2) {
                    changeImage(-1);
                }
                // Si se hace click en el 20% derecho de la imagen
                else if (clickX > containerWidth * 0.8) {
                    changeImage(1);
                }
            }
        });
    }
    
    // Actualizar contador de imágenes en el atributo data
    const galleryMain = document.querySelector('.gallery-main');
    if (galleryMain && totalImages > 1) {
        function updateImageCounter() {
            galleryMain.setAttribute('data-count', `${currentImageIndex + 1}/${totalImages}`);
        }
        updateImageCounter();
        
        // Actualizar contador cuando cambie la imagen
        const originalUpdateGallery = updateGallery;
        updateGallery = function() {
            originalUpdateGallery();
            updateImageCounter();
        };
    }
    
    // Auto-play de la galería (opcional)
    let autoPlayInterval;
    
    function startAutoPlay() {
        autoPlayInterval = setInterval(function() {
            if (totalImages > 1) {
                changeImage(1);
            }
        }, 5000); // Cambiar cada 5 segundos
    }
    
    function stopAutoPlay() {
        if (autoPlayInterval) {
            clearInterval(autoPlayInterval);
        }
    }
    
    // Iniciar auto-play
    if (totalImages > 1) {
        startAutoPlay();
        
        // Pausar auto-play al hacer hover
        const galleryContainer = document.querySelector('.gallery-container');
        if (galleryContainer) {
            galleryContainer.addEventListener('mouseenter', stopAutoPlay);
            galleryContainer.addEventListener('mouseleave', startAutoPlay);
        }
    }
    
    // Botones de acción
    const shareBtn = document.querySelector('.share-btn');
    const favoriteBtn = document.querySelector('.favorite-btn');
    
    if (shareBtn) {
        shareBtn.addEventListener('click', function() {
            if (navigator.share) {
                navigator.share({
                    title: document.title,
                    url: window.location.href
                });
            } else {
                // Fallback para navegadores que no soportan Web Share API
                navigator.clipboard.writeText(window.location.href).then(function() {
                    // Mostrar notificación de éxito
                    showNotification('Enlace copiado al portapapeles');
                }).catch(function() {
                    showNotification('Error al copiar enlace', 'error');
                });
            }
        });
    }
    
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', function() {
            this.classList.toggle('active');
            
            if (this.classList.contains('active')) {
                showNotification('Agregado a favoritos');
                // Aquí podrías enviar una petición AJAX para guardar en la base de datos
            } else {
                showNotification('Removido de favoritos');
            }
        });
    }
    
    // Formulario de contacto
    const contactForm = document.querySelector('.contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Agregar clase de carga
            this.classList.add('loading');
            
            // Simular envío (aquí deberías implementar la lógica real)
            setTimeout(function() {
                contactForm.classList.remove('loading');
                showNotification('Mensaje enviado correctamente', 'success');
                contactForm.reset();
            }, 2000);
        });
    }
    
    // Navegación con teclado para miniaturas
    document.addEventListener('keydown', function(e) {
        if (thumbnails.length > 0) {
            const activeThumbnail = document.querySelector('.thumbnail.active');
            if (activeThumbnail) {
                const currentIndex = Array.from(thumbnails).indexOf(activeThumbnail);
                let newIndex = currentIndex;
                
                if (e.key === 'ArrowLeft' && currentIndex > 0) {
                    newIndex = currentIndex - 1;
                } else if (e.key === 'ArrowRight' && currentIndex < thumbnails.length - 1) {
                    newIndex = currentIndex + 1;
                }
                
                if (newIndex !== currentIndex) {
                    thumbnails.forEach(function(thumb) {
                        thumb.classList.remove('active');
                    });
                    
                    thumbnails[newIndex].classList.add('active');
                    
                    const imageUrl = thumbnails[newIndex].getAttribute('data-image');
                    if (imageUrl && heroImage) {
                        heroImage.style.opacity = '0.7';
                        setTimeout(function() {
                            heroImage.src = imageUrl;
                            heroImage.style.opacity = '1';
                        }, 150);
                    }
                }
            }
        }
    });
    
    // Función para mostrar notificaciones
    function showNotification(message, type = 'info') {
        // Crear elemento de notificación
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        // Estilos de la notificación
        Object.assign(notification.style, {
            position: 'fixed',
            top: '20px',
            right: '20px',
            background: type === 'error' ? '#ef4444' : type === 'success' ? '#10b981' : '#2563eb',
            color: 'white',
            padding: '1rem 1.5rem',
            borderRadius: '8px',
            boxShadow: '0 10px 15px -3px rgba(0, 0, 0, 0.1)',
            zIndex: '9999',
            transform: 'translateX(100%)',
            transition: 'transform 0.3s ease'
        });
        
        document.body.appendChild(notification);
        
        // Animar entrada
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Remover después de 3 segundos
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
    
    // Lazy loading para imágenes
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
    
    // Smooth scroll para enlaces internos
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>

<?php get_footer(); ?>