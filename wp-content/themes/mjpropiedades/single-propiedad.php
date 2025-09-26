<?php
/**
 * Template para mostrar una propiedad individual
 */

get_header(); ?>

<div class="property-single-page">
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
        
        // Si no hay operación configurada, determinar por precio
        if (!$operacion) {
            $operacion = ($precio && $precio < 1000000) ? 'arriendo' : 'venta';
        }
        
        $precio_text = $precio ? '$' . number_format($precio, 0, ',', '.') : 'Consultar';
        if ($operacion === 'arriendo') {
            $precio_text .= '/mes';
        }
        
        $uf_precio = $precio ? round($precio / 40000) : 0;
        ?>
        
        <!-- Hero Section con Galería -->
        <section class="property-hero">
            <div class="property-gallery-container">
                <!-- Imagen principal -->
                        <div class="property-main-image">
                    <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('full', array('class' => 'hero-image')); ?>
                    <?php else : ?>
                        <div class="no-image-placeholder">
                            <i class="fas fa-home"></i>
                            <p>Sin imagen disponible</p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Tag de operación -->
                    <div class="property-tag <?php echo $operacion; ?>">
                        <?php echo ucfirst($operacion); ?>
                </div>
                
                    <!-- Botones de acción -->
                    <div class="property-actions">
                        <button class="action-btn share-btn" title="Compartir">
                            <i class="fas fa-share-alt"></i>
                        </button>
                        <button class="action-btn favorite-btn" title="Favorito">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Galería de miniaturas -->
                <div class="property-thumbnails">
                            <?php 
                    $gallery_images = get_post_meta(get_the_ID(), '_propiedad_gallery', true);
                    if ($gallery_images) {
                        $image_ids = explode(',', $gallery_images);
                        foreach ($image_ids as $index => $image_id) {
                            $image_url = wp_get_attachment_image_url($image_id, 'medium');
                            $large_image_url = wp_get_attachment_image_url($image_id, 'large');
                            if ($image_url) {
                                $active_class = $index === 0 ? 'active' : '';
                                echo '<div class="thumbnail-item ' . $active_class . '" data-image-url="' . esc_url($large_image_url) . '">';
                                echo '<img src="' . esc_url($image_url) . '" alt="Galería de la propiedad">';
                                echo '</div>';
                            }
                        }
                    } else {
                        // Mostrar imagen destacada como miniatura
                        if (has_post_thumbnail()) {
                            $main_image_url = wp_get_attachment_image_url(get_post_thumbnail_id(), 'large');
                            echo '<div class="thumbnail-item active" data-image-url="' . esc_url($main_image_url) . '">';
                            the_post_thumbnail('medium');
                            echo '</div>';
                        }
                    }
                    ?>
                    
                    <!-- Indicador de más imágenes -->
                        <?php
                    $gallery_images = get_post_meta(get_the_ID(), '_propiedad_gallery', true);
                    $total_images = 0;
                    if ($gallery_images) {
                        $image_ids = explode(',', $gallery_images);
                        $total_images = count($image_ids);
                    } else if (has_post_thumbnail()) {
                        $total_images = 1;
                    }
                    
                    if ($total_images > 4) : ?>
                        <div class="more-images" id="show-more-images">
                            <span>+<?php echo ($total_images - 4); ?> más</span>
                                </div>
                            <?php endif; ?>
                </div>
            </div>
        </section>
        
        
        <!-- Información Principal -->
        <section class="property-main-info">
            <div class="container property-detail">
                <div class="property-header">
                    <div class="property-title-section">
                        <h1 class="property-title"><?php the_title(); ?></h1>
                        <p class="property-address"><?php echo $direccion ? esc_html($direccion) : esc_html($comuna); ?></p>
                                </div>
                    
                    <div class="property-price-section">
                        <div class="price-main"><?php echo $precio_text; ?></div>
                        <?php if ($uf_precio > 0) : ?>
                            <div class="price-uf">UF <?php echo number_format($uf_precio, 0, ',', '.'); ?></div>
                            <?php endif; ?>
                    </div>
                </div>
                
                <!-- Características principales -->
                <div class="property-features">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 7V5C3 3.89543 3.89543 3 5 3H19C20.1046 3 21 3.89543 21 5V7M3 7V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V7M3 7H21M7 11H17M7 15H13" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                                </div>
                        <div class="feature-number"><?php echo $dormitorios ?: '3'; ?></div>
                        <div class="feature-label">Dormitorios</div>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 5.5V7.5C15 8.3 14.3 9 13.5 9S12 8.3 12 7.5V5.5L6 7V9L12 7.5V9.5C12 10.3 12.7 11 13.5 11S15 10.3 15 9.5V7.5L21 9Z" fill="white"/>
                                <path d="M12 12C8.7 12 6 14.7 6 18V22H18V18C18 14.7 15.3 12 12 12Z" fill="white"/>
                            </svg>
                                </div>
                        <div class="feature-number"><?php echo $banos ?: '2'; ?></div>
                        <div class="feature-label">Baños</div>
                        </div>
                        
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 3H21V21H3V3Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 9H15V15H9V9Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3 9H21M3 15H21M9 3V21M15 3V21" stroke="white" stroke-width="1" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div class="feature-number"><?php echo $metros ?: '120'; ?> m²</div>
                        <div class="feature-label">Construidos</div>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 16V8C21 6.89543 20.1046 6 19 6H5C3.89543 6 3 6.89543 3 8V16C3 17.1046 3.89543 18 5 18H19C20.1046 18 21 17.1046 21 16Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7 10H17M7 14H13" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="12" r="2" fill="white"/>
                            </svg>
                        </div>
                        <div class="feature-number"><?php echo $estacionamientos ?: '2'; ?></div>
                        <div class="feature-label">Estacionamientos</div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Contenido Principal -->
        <section class="property-content">
            <div class="container">
                <div class="content-grid">
                    <!-- Columna izquierda - Contenido -->
                    <div class="content-main">
                    <!-- Descripción y Características -->
                        <div class="content-section-unified">
                            <h2 class="section-title">Descripción</h2>
                            <div class="property-description">
                                <?php if (get_the_content()) : ?>
                                    <?php the_content(); ?>
                                <?php else : ?>
                                    <p>Hermosa <?php echo esc_html($tipo); ?> moderna en sector exclusivo, ofrece espacios amplios y luminosos, perfecta para una familia que busca comodidad y elegancia en <?php echo esc_html($comuna); ?>.</p>
                                    
                                    <p>Esta propiedad cuenta con acabados de primera calidad, cocina equipada con electrodomésticos premium, amplios dormitorios con closets empotrados y hermoso jardín trasero. Su ubicación privilegiada permite fácil acceso a centros comerciales, colegios y transporte público.</p>
                                <?php endif; ?>
                            </div>
                            
                            <h2 class="section-title">Características Destacadas</h2>
                            <div class="features-list">
                                <?php
                                if ($caracteristicas) {
                                    $caracteristicas_array = explode("\n", $caracteristicas);
                                    foreach ($caracteristicas_array as $caracteristica) {
                                        $caracteristica = trim($caracteristica);
                                        if (!empty($caracteristica)) {
                                            echo '<div class="feature-item">';
                                            echo '<i class="fas fa-check-circle"></i>';
                                            echo '<span>' . esc_html($caracteristica) . '</span>';
                                            echo '</div>';
                                        }
                                    }
                                } else {
                                    // Características por defecto si no hay datos
                                    $default_features = array(
                                        'Cocina equipada',
                                        'Jardín privado',
                                        'Terraza techada',
                                        'Calefacción central',
                                        'Closets empotrados',
                                        'Portón automático'
                                    );
                                    
                                    foreach ($default_features as $feature) {
                                        echo '<div class="feature-item">';
                                        echo '<i class="fas fa-check-circle"></i>';
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
                            <div class="location-card">
                                <div class="map-container">
                                    <?php if ($latitud && $longitud) : ?>
                                        <iframe 
                                            src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBFw0Qbyq9zTFTd-tUY6dgsWUxW4U5Q&q=<?php echo $latitud; ?>,<?php echo $longitud; ?>" 
                                            width="100%" 
                                            height="300" 
                                            style="border:0;" 
                                            allowfullscreen="" 
                                            loading="lazy">
                                        </iframe>
                                    <?php else : ?>
                                        <div class="map-fallback">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <p>Ubicación: <?php echo esc_html($comuna); ?></p>
                                            <p>Coordenadas no disponibles</p>
                    </div>
                            <?php endif; ?>
                </div>
                
                                <div class="nearby-places">
                                    <?php
                                    if ($lugares_cercanos) {
                                        $lugares_array = json_decode($lugares_cercanos, true);
                                        if (is_array($lugares_array)) {
                                            foreach ($lugares_array as $lugar) {
                                                $icon_class = 'fas fa-map-marker-alt'; // Icono por defecto
                                                
                                                // Asignar icono según el tipo
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
                                    } else {
                                        // Lugares por defecto si no hay datos
                                        $default_places = array(
                                            array('nombre' => 'Mall Parque Arauco', 'distancia' => '5 min caminando', 'tipo' => 'shopping'),
                                            array('nombre' => 'Metro Escuela Militar', 'distancia' => '8 min caminando', 'tipo' => 'transporte'),
                                            array('nombre' => 'Clínica Las Condes', 'distancia' => '10 min en auto', 'tipo' => 'salud')
                                        );
                                        
                                        foreach ($default_places as $lugar) {
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
                        </div>
                        
                        <!-- Información Adicional -->
                        <div class="content-section">
                            <h2 class="section-title">Información Adicional</h2>
                            <div class="additional-info-card">
                                <div class="info-grid">
                                    <div class="info-item">
                                        <span class="info-label">Año construcción:</span>
                                        <span class="info-value"><?php echo $ano_construccion ?: '2018'; ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Orientación:</span>
                                        <span class="info-value"><?php echo $orientacion ?: 'Norte'; ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Gastos comunes:</span>
                                        <span class="info-value">$<?php echo $gastos_comunes ? number_format($gastos_comunes, 0, ',', '.') : '85.000'; ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Estado:</span>
                                        <span class="info-value status-excellent"><?php echo $estado ?: 'Excelente'; ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Disponibilidad:</span>
                                        <span class="info-value"><?php echo $disponibilidad ?: 'Inmediata'; ?></span>
                                    </div>
                                    <?php if ($estacionamientos) : ?>
                                    <div class="info-item">
                                        <span class="info-label">Estacionamientos:</span>
                                        <span class="info-value"><?php echo $estacionamientos; ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Columna derecha - Formulario de contacto -->
                    <div class="content-sidebar">
                        <div class="contact-form-card">
                            <h3 class="form-title">Contactar por esta propiedad</h3>
                            <form class="property-contact-form" method="post" action="">
                                <?php wp_nonce_field('property_contact_nonce', 'property_contact_nonce'); ?>
                                <input type="hidden" name="property_id" value="<?php echo get_the_ID(); ?>">
                                <input type="hidden" name="property_contact_submitted" value="1">
                                
                        <div class="form-group">
                                    <label for="contact_name">Nombre completo</label>
                                    <input type="text" id="contact_name" name="contact_name" placeholder="Tu nombre completo" required>
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
                                    <textarea id="contact_message" name="contact_message" placeholder="Estoy interesado en esta propiedad..." rows="4"></textarea>
                        </div>
                    
                                <div class="form-group checkbox-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="request_visit" value="1">
                                        <span class="checkmark"></span>
                                        Solicitar visita programada
                                    </label>
                    </div>
                    
                                <div class="form-actions">
                                    <button type="submit" class="btn-primary">Enviar Consulta</button>
                                    <a href="tel:+56912345678" class="btn-secondary">Hablar Ahora</a>
                                </div>
                    </form>
                </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Propiedades Similares -->
        <section class="similar-properties">
            <div class="container">
                <h2 class="section-title">Propiedades Similares</h2>
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
                                            <i class="fas fa-home"></i>
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
                                            <span><?php echo $similar_dormitorios; ?> dormitorios</span>
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
                                        <a href="<?php the_permalink(); ?>" class="card-btn">Ver Detalles</a>
                        </div>
                        </div>
                        </div>
                            
                        <?php endwhile;
                        wp_reset_postdata();
                    endif; ?>
                </div>
                
                <div class="view-all-properties">
                    <a href="<?php echo get_post_type_archive_link('propiedad'); ?>" class="btn-view-all">Ver Todas las Propiedades</a>
                </div>
            </div>
        </section>
        
        <?php endwhile; ?>
</div>

<style>
/* Estilos para la página de detalle de propiedad */
.property-single-page {
    background: #f8f9fa;
    min-height: 100vh;
}

/* Hero Section */
.property-hero {
    position: relative;
    height: 70vh;
    overflow: hidden;
}

.property-gallery-container {
    position: relative;
    height: 100%;
}

.property-main-image {
    position: relative;
    height: 100%;
    width: 100%;
}

.hero-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    background: #e9ecef;
    color: #6c757d;
    font-size: 3rem;
}

.no-image-placeholder p {
    margin-top: 1rem;
    font-size: 1.2rem;
}

.property-tag {
    position: absolute;
    top: 20px;
    left: 20px;
    background: var(--primary-blue);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
    z-index: 10;
}

.property-tag.arriendo {
    background: var(--orange);
}

.property-actions {
    position: absolute;
    top: 20px;
    right: 20px;
    display: flex;
    gap: 10px;
    z-index: 10;
}

.action-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    color: var(--dark-gray);
}

.action-btn:hover {
    background: white;
    transform: scale(1.1);
}

.property-thumbnails {
    position: absolute;
    bottom: 20px;
    left: 20px;
    right: 20px;
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding-bottom: 10px;
}

.thumbnail-item {
    flex-shrink: 0;
    width: 80px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.thumbnail-item.active,
.thumbnail-item:hover {
    border-color: white;
}

.thumbnail-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.more-images {
    flex-shrink: 0;
    width: 80px;
    height: 60px;
    background: rgba(0, 0, 0, 0.7);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.9rem;
}

/* Información Principal */
.property-main-info {
    background: white;
    padding: 3rem 0;
}

.property-header {
    text-align: center;
    margin-bottom: 3rem;
}

.property-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--dark-gray);
    margin-bottom: 1rem;
}

.property-address {
    font-size: 1.2rem;
    color: var(--light-gray);
    margin-bottom: 2rem;
}

.property-price-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.price-main {
    font-size: 3rem;
    font-weight: 700;
    color: var(--primary-blue);
}

.price-uf {
    font-size: 1.2rem;
    color: var(--light-gray);
}

.property-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    max-width: 800px;
    margin: 0 auto;
}

.feature-card {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
}

.feature-icon {
    width: 50px;
    height: 50px;
    background: var(--primary-blue);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.feature-text {
    display: flex;
    flex-direction: column;
}

.feature-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dark-gray);
}

.feature-label {
    font-size: 0.9rem;
    color: var(--light-gray);
}

/* Contenido Principal */
.property-content {
    padding: 4rem 0;
}

.content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 4rem;
}

.content-main {
    display: flex;
    flex-direction: column;
    gap: 3rem;
}

.content-section {
    background: white;
    padding: 2.5rem;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.section-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--dark-gray);
    margin-bottom: 1.5rem;
}

.property-description {
    font-size: 1.1rem;
    line-height: 1.8;
    color: var(--dark-gray);
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 0;
}

.feature-item i {
    color: #28a745;
    font-size: 1.1rem;
}

/* Ubicación */
.location-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.map-container {
    height: 300px;
    background: #f8f9fa;
}

.map-placeholder {
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--light-gray);
}

.map-fallback {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: var(--light-gray);
}

.map-fallback i {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.nearby-places {
    padding: 2rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.place-item {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.place-item i {
    width: 40px;
    height: 40px;
    background: var(--primary-blue);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.place-info {
    display: flex;
    flex-direction: column;
}

.place-name {
    font-weight: 600;
    color: var(--dark-gray);
}

.place-distance {
    font-size: 0.9rem;
    color: var(--light-gray);
}

/* Información Adicional */
.additional-info-card {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.info-label {
    font-weight: 600;
    color: var(--light-gray);
    font-size: 0.9rem;
}

.info-value {
    font-weight: 600;
    color: var(--dark-gray);
}

.status-excellent {
    color: #28a745;
}

/* Formulario de Contacto */
.content-sidebar {
    position: sticky;
    top: 100px;
    height: fit-content;
}

.contact-form-card {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.form-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dark-gray);
    margin-bottom: 2rem;
    text-align: center;
}

.property-contact-form {
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
    color: var(--dark-gray);
    font-size: 0.9rem;
}

.form-group input,
.form-group textarea {
    padding: 12px 16px;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-blue);
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
    font-size: 0.9rem;
    color: var(--dark-gray);
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
    background: var(--primary-blue);
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: var(--secondary-blue);
    transform: translateY(-2px);
}

.btn-secondary {
    background: var(--orange);
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background: #e67e22;
    transform: translateY(-2px);
}

/* Propiedades Similares */
.similar-properties {
    background: #f8f9fa;
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
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.property-card:hover {
    transform: translateY(-5px);
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
}

.no-image {
    height: 100%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 2rem;
}

.card-tag {
    position: absolute;
    top: 15px;
    left: 15px;
    background: var(--primary-blue);
    color: white;
    padding: 6px 12px;
    border-radius: 15px;
    font-weight: 600;
    font-size: 0.8rem;
}

.card-tag.arriendo {
    background: var(--orange);
}

.card-content {
    padding: 1.5rem;
}

.card-title {
    margin-bottom: 1rem;
}

.card-title a {
    color: var(--dark-gray);
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
}

.card-title a:hover {
    color: var(--primary-blue);
}

.card-details {
    color: var(--light-gray);
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-price {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--primary-blue);
}

.card-btn {
    background: var(--orange);
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.card-btn:hover {
    background: #e67e22;
    transform: translateY(-2px);
}

.view-all-properties {
    text-align: center;
}

.btn-view-all {
    background: var(--primary-blue);
    color: white;
    padding: 15px 30px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    display: inline-block;
}

.btn-view-all:hover {
    background: var(--secondary-blue);
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 1024px) {
    .content-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .content-sidebar {
        position: static;
    }
}

@media (max-width: 768px) {
    .property-hero {
        height: 50vh;
    }
    
    .property-title {
        font-size: 2rem;
    }
    
    .price-main {
        font-size: 2.5rem;
    }
    
    .property-features {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .feature-card {
        padding: 1.5rem;
    }
    
    .content-section {
        padding: 1.5rem;
    }
    
    .contact-form-card {
        padding: 1.5rem;
    }
    
    .properties-grid {
        grid-template-columns: 1fr;
    }
}

/* Scrollbar personalizada para miniaturas */
.property-thumbnails::-webkit-scrollbar {
    height: 4px;
}

.property-thumbnails::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 2px;
}

.property-thumbnails::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.5);
    border-radius: 2px;
}

.property-thumbnails::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.8);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Galería de imágenes
    const thumbnails = document.querySelectorAll('.thumbnail-item');
    const mainImage = document.querySelector('.hero-image');
    
    if (thumbnails.length > 0 && mainImage) {
        thumbnails.forEach(function(thumbnail) {
            thumbnail.addEventListener('click', function() {
                // Remover clase active de todas las miniaturas
                thumbnails.forEach(function(thumb) {
                    thumb.classList.remove('active');
                });
                
                // Agregar clase active a la miniatura clickeada
                this.classList.add('active');
                
                // Cambiar la imagen principal usando data-image-url
                const imageUrl = this.getAttribute('data-image-url');
                if (imageUrl) {
                    mainImage.src = imageUrl;
                    mainImage.alt = this.querySelector('img').alt;
                    
                    // Efecto de transición suave
                    mainImage.style.opacity = '0.7';
                    setTimeout(function() {
                        mainImage.style.opacity = '1';
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
                    alert('Enlace copiado al portapapeles');
                });
            }
        });
    }
    
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', function() {
            this.classList.toggle('active');
            const icon = this.querySelector('i');
            if (this.classList.contains('active')) {
                icon.classList.remove('fa-heart');
                icon.classList.add('fa-heart');
                icon.style.color = '#dc3545';
            } else {
                icon.style.color = '';
            }
        });
    }
    
    // Modal de galería
    const galleryModal = document.getElementById('gallery-modal');
    const showMoreImages = document.getElementById('show-more-images');
    const galleryClose = document.getElementById('gallery-close');
    const galleryMainImg = document.getElementById('gallery-main-img');
    const galleryThumbnailsModal = document.querySelectorAll('.gallery-thumbnail-modal');
    
    // Mostrar modal al hacer clic en "más imágenes"
    if (showMoreImages && galleryModal) {
        showMoreImages.addEventListener('click', function() {
            galleryModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            
            // Establecer la primera imagen como activa
            if (galleryThumbnailsModal.length > 0) {
                const firstThumbnail = galleryThumbnailsModal[0];
                const firstImageUrl = firstThumbnail.getAttribute('data-image-url');
                if (firstImageUrl && galleryMainImg) {
                    galleryMainImg.src = firstImageUrl;
                }
            }
        });
    }
    
    // Cerrar modal
    if (galleryClose && galleryModal) {
        galleryClose.addEventListener('click', function() {
            galleryModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        });
    }
    
    // Cerrar modal al hacer clic fuera del contenido
    if (galleryModal) {
        galleryModal.addEventListener('click', function(e) {
            if (e.target === galleryModal) {
                galleryModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    }
    
    // Cambiar imagen principal al hacer clic en miniatura
    galleryThumbnailsModal.forEach(function(thumbnail) {
        thumbnail.addEventListener('click', function() {
            // Remover clase active de todas las miniaturas
            galleryThumbnailsModal.forEach(function(thumb) {
                thumb.classList.remove('active');
            });
            
            // Agregar clase active a la miniatura clickeada
            this.classList.add('active');
            
            // Cambiar la imagen principal
            const imageUrl = this.getAttribute('data-image-url');
            if (imageUrl && galleryMainImg) {
                galleryMainImg.src = imageUrl;
            }
        });
    });
    
    // Cerrar modal con tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && galleryModal && galleryModal.style.display === 'block') {
            galleryModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
    
    // Navegación con teclado para miniaturas
    document.addEventListener('keydown', function(e) {
        if (thumbnails.length > 0) {
            const activeThumbnail = document.querySelector('.thumbnail-item.active');
            if (activeThumbnail) {
                const currentIndex = Array.from(thumbnails).indexOf(activeThumbnail);
                let newIndex = currentIndex;
                
                if (e.key === 'ArrowLeft' && currentIndex > 0) {
                    newIndex = currentIndex - 1;
                } else if (e.key === 'ArrowRight' && currentIndex < thumbnails.length - 1) {
                    newIndex = currentIndex + 1;
                }
                
                if (newIndex !== currentIndex) {
                    // Remover clase active de todas las miniaturas
                    thumbnails.forEach(function(thumb) {
                        thumb.classList.remove('active');
                    });
                    
                    // Agregar clase active a la nueva miniatura
                    thumbnails[newIndex].classList.add('active');
                    
                    // Cambiar la imagen principal
                    const imageUrl = thumbnails[newIndex].getAttribute('data-image-url');
                    if (imageUrl && mainImage) {
                        mainImage.style.opacity = '0.7';
                        setTimeout(function() {
                            mainImage.src = imageUrl;
                            mainImage.alt = thumbnails[newIndex].querySelector('img').alt;
                            mainImage.style.opacity = '1';
                        }, 150);
                    }
                }
            }
        }
    });
});
</script>

<!-- Modal de Galería Completa -->
<div id="gallery-modal" class="gallery-modal">
    <div class="gallery-modal-content">
        <div class="gallery-modal-header">
            <h3>Galería de Imágenes</h3>
            <button class="gallery-close" id="gallery-close">&times;</button>
        </div>
        <div class="gallery-modal-body">
            <div class="gallery-main-image">
                <img id="gallery-main-img" src="" alt="Imagen de la propiedad">
            </div>
            <div class="gallery-thumbnails-modal">
                <?php 
                $gallery_images = get_post_meta(get_the_ID(), '_propiedad_gallery', true);
                if ($gallery_images) {
                    $image_ids = explode(',', $gallery_images);
                    foreach ($image_ids as $index => $image_id) {
                        $image_url = wp_get_attachment_image_url($image_id, 'large');
                        $thumbnail_url = wp_get_attachment_image_url($image_id, 'medium');
                        if ($image_url) {
                            $active_class = $index === 0 ? 'active' : '';
                            echo '<div class="gallery-thumbnail-modal ' . $active_class . '" data-image-url="' . esc_url($image_url) . '">';
                            echo '<img src="' . esc_url($thumbnail_url) . '" alt="Galería de la propiedad">';
                            echo '</div>';
                        }
                    }
                } else if (has_post_thumbnail()) {
                    $main_image = wp_get_attachment_image_url(get_post_thumbnail_id(), 'large');
                    $thumbnail = wp_get_attachment_image_url(get_post_thumbnail_id(), 'medium');
                    echo '<div class="gallery-thumbnail-modal active" data-image-url="' . esc_url($main_image) . '">';
                    echo '<img src="' . esc_url($thumbnail) . '" alt="Imagen principal">';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>