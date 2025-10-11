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
    $precio_text = $precio ? '$' . number_format($precio, 0, ',', '.') : 'Consultar';
    if ($operacion === 'arriendo') {
        $precio_text .= '/mes';
    }
    
    $uf_precio = $precio ? round($precio / 40000) : 0;
    ?>
    
    <!-- Hero Section - Galería de Imágenes -->
    <section class="hero-gallery">
        <div class="gallery-main">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('full', array('class' => 'hero-image')); ?>
            <?php else : ?>
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
            
            <!-- Tag de operación -->
            <div class="operation-tag <?php echo esc_attr($operacion); ?>">
                <?php echo ucfirst($operacion); ?>
            </div>
            
            <!-- Botones de acción flotantes -->
            <div class="floating-actions">
                <button class="floating-btn share-btn" title="Compartir">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="18" cy="5" r="3"></circle>
                        <circle cx="6" cy="12" r="3"></circle>
                        <circle cx="18" cy="19" r="3"></circle>
                        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                        <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                    </svg>
                </button>
                <button class="floating-btn favorite-btn" title="Agregar a favoritos">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Miniaturas de galería -->
        <div class="gallery-thumbnails">
            <?php 
            $gallery_images = get_post_meta(get_the_ID(), '_propiedad_gallery', true);
            if ($gallery_images) {
                $image_ids = explode(',', $gallery_images);
                foreach ($image_ids as $index => $image_id) {
                    $image_url = wp_get_attachment_image_url($image_id, 'medium');
                    $large_image_url = wp_get_attachment_image_url($image_id, 'large');
                    if ($image_url) {
                        $active_class = $index === 0 ? 'active' : '';
                        echo '<div class="thumbnail ' . $active_class . '" data-image="' . esc_url($large_image_url) . '">';
                        echo '<img src="' . esc_url($image_url) . '" alt="Imagen ' . ($index + 1) . ' de la propiedad">';
                        echo '</div>';
                    }
                }
            } else if (has_post_thumbnail()) {
                $main_image_url = wp_get_attachment_image_url(get_post_thumbnail_id(), 'large');
                echo '<div class="thumbnail active" data-image="' . esc_url($main_image_url) . '">';
                the_post_thumbnail('medium');
                echo '</div>';
            }
            ?>
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
                        <div class="price-uf">UF <?php echo number_format($uf_precio, 0, ',', '.'); ?></div>
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
                                
                                <a href="tel:+56912345678" class="btn-secondary">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                    </svg>
                                    Llamar ahora
                                </a>
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
                        
                        $similar_precio_text = $similar_precio ? '$' . number_format($similar_precio, 0, ',', '.') : 'Consultar';
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
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Hero Gallery */
.hero-gallery {
    position: relative;
    height: 60vh;
    min-height: 400px;
    overflow: hidden;
}

.gallery-main {
    position: relative;
    height: 100%;
    width: 100%;
}

.hero-image {
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

.operation-tag {
    position: absolute;
    top: 1.5rem;
    left: 1.5rem;
    background: var(--primary-color);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: var(--radius-xl);
    font-weight: 600;
    font-size: 0.875rem;
    z-index: 10;
}

.operation-tag.arriendo {
    background: var(--secondary-color);
}

.floating-actions {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    display: flex;
    gap: 0.75rem;
    z-index: 10;
}

.floating-btn {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.95);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    color: var(--text-primary);
    box-shadow: var(--shadow-lg);
}

.floating-btn:hover {
    background: white;
    transform: translateY(-2px);
    box-shadow: var(--shadow-xl);
}

.floating-btn svg {
    transition: color 0.3s ease;
}

.favorite-btn.active svg {
    color: #ef4444;
    fill: currentColor;
}

.gallery-thumbnails {
    position: absolute;
    bottom: 1.5rem;
    left: 1.5rem;
    right: 1.5rem;
    display: flex;
    gap: 0.75rem;
    overflow-x: auto;
    padding-bottom: 0.5rem;
}

.thumbnail {
    flex-shrink: 0;
    width: 80px;
    height: 60px;
    border-radius: var(--radius-md);
    overflow: hidden;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.thumbnail.active,
.thumbnail:hover {
    border-color: white;
    transform: scale(1.05);
}

.thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
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

.contact-header {
    text-align: center;
    margin-bottom: 2rem;
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
    .hero-gallery {
        height: 50vh;
        min-height: 300px;
    }
    
    .container {
        padding: 0 1rem;
    }
    
    .property-info {
        padding: 2rem 0;
    }
    
    .property-title {
        font-size: 1.875rem;
    }
    
    .price-main {
        font-size: 2rem;
    }
    
    .main-features {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .feature-item {
        padding: 1rem;
    }
    
    .property-content {
        padding: 2rem 0;
    }
    
    .content-section {
        padding: 1.5rem;
    }
    
    .contact-card {
        padding: 1.5rem;
    }
    
    .similar-properties {
        padding: 2rem 0;
    }
    
    .properties-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .floating-actions {
        top: 1rem;
        right: 1rem;
    }
    
    .floating-btn {
        width: 40px;
        height: 40px;
    }
    
    .operation-tag {
        top: 1rem;
        left: 1rem;
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
    }
    
    .gallery-thumbnails {
        bottom: 1rem;
        left: 1rem;
        right: 1rem;
    }
    
    .thumbnail {
        width: 60px;
        height: 45px;
    }
}

@media (max-width: 480px) {
    .hero-gallery {
        height: 40vh;
        min-height: 250px;
    }
    
    .property-title {
        font-size: 1.5rem;
    }
    
    .price-main {
        font-size: 1.75rem;
    }
    
    .main-features {
        gap: 0.75rem;
    }
    
    .feature-item {
        padding: 0.875rem;
        gap: 0.75rem;
    }
    
    .feature-icon {
        width: 40px;
        height: 40px;
    }
    
    .feature-number {
        font-size: 1.25rem;
    }
    
    .content-section {
        padding: 1rem;
    }
    
    .contact-card {
        padding: 1rem;
    }
    
    .form-actions {
        gap: 0.75rem;
    }
    
    .btn-primary,
    .btn-secondary {
        padding: 0.75rem 1.25rem;
        font-size: 0.875rem;
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

.content-section {
    animation: fadeIn 0.6s ease-out;
}

.contact-card {
    animation: fadeIn 0.8s ease-out;
}

.property-card {
    animation: fadeIn 0.6s ease-out;
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Galería de imágenes
    const thumbnails = document.querySelectorAll('.thumbnail');
    const heroImage = document.querySelector('.hero-image');
    
    if (thumbnails.length > 0 && heroImage) {
        thumbnails.forEach(function(thumbnail) {
            thumbnail.addEventListener('click', function() {
                // Remover clase active de todas las miniaturas
                thumbnails.forEach(function(thumb) {
                    thumb.classList.remove('active');
                });
                
                // Agregar clase active a la miniatura clickeada
                this.classList.add('active');
                
                // Cambiar la imagen principal
                const imageUrl = this.getAttribute('data-image');
                if (imageUrl) {
                    heroImage.style.opacity = '0.7';
                    setTimeout(function() {
                        heroImage.src = imageUrl;
                        heroImage.style.opacity = '1';
                    }, 150);
                }
            });
        });
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