<?php
/**
 * Template Name: Página de Inicio
 * 
 * Plantilla personalizada para la página de inicio
 * Muestra el hero y las propiedades destacadas
 */

get_header(); ?>

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
        $has_images = false;
        foreach ($hero_images as $index => $image_id) {
            if ($image_id) {
                $image_url = wp_get_attachment_image_url($image_id, 'full');
                if ($image_url) {
                    $has_images = true;
                    $active_class = ($index === 0) ? ' active' : '';
                    echo '<div class="hero-slide' . $active_class . '" style="background-image: url(\'' . esc_url($image_url) . '\');">';
                    echo '<div class="hero-overlay"></div>';
                    echo '</div>';
                }
            }
        }
        if (!$has_images) {
            echo '<div class="hero-slide active" style="background-image: url(\'' . get_template_directory_uri() . '/images/hero-default.jpg\');">';
            echo '<div class="hero-overlay"></div>';
            echo '</div>';
        }
        ?>
    </div>
    
    <div class="hero-container">
        <div class="hero-content">
            <span class="hero-tag"><?php echo get_theme_mod('mjpropiedades_hero_tag', 'Compra de Propiedades'); ?></span>
            <h1><?php echo get_theme_mod('mjpropiedades_hero_title', 'Encuentra el Hogar de tus Sueños'); ?></h1>
            <p class="hero-description">
                <?php echo get_theme_mod('mjpropiedades_hero_description', 'Descubre propiedades exclusivas que se ajustan a tu estilo de vida. Asesoría personalizada en todo el proceso de compra.'); ?>
            </p>
            <a href="#buscar" class="hero-btn">
                <?php echo get_theme_mod('mjpropiedades_hero_button', 'Buscar Propiedades'); ?> →
            </a>
        </div>
    </div>
    
    <!-- Carousel Navigation -->
    <button class="carousel-nav prev">‹</button>
    <button class="carousel-nav next">›</button>
    
    <!-- Carousel Dots -->
    <div class="carousel-dots">
        <?php
        $image_count = 0;
        foreach ($hero_images as $image_id) {
            if ($image_id) {
                $image_url = wp_get_attachment_image_url($image_id, 'full');
                if ($image_url) {
                    $active_class = ($image_count === 0) ? ' active' : '';
                    echo '<span class="dot' . $active_class . '"></span>';
                    $image_count++;
                }
            }
        }
        if ($image_count === 0) {
            echo '<span class="dot active"></span>';
        }
        ?>
    </div>
</section>

<!-- Search Section -->
<section id="buscar" class="search-section section">
    <div class="container">
        <div class="search-header">
            <span class="search-tag">Búsqueda Rápida</span>
            <h2 class="section-title">Encuentra tu Propiedad Ideal</h2>
            <p class="section-subtitle">Utiliza nuestro buscador avanzado para encontrar exactamente lo que necesitas</p>
        </div>
        
        <div class="search-form-container">
            <form class="search-form" method="get" action="<?php echo home_url('/propiedades/'); ?>">
                <div class="search-form-grid">
                    <div class="search-group">
                        <label for="search-comuna" class="search-label">Comuna</label>
                        <select id="search-comuna" name="comuna" class="search-select">
                            <option value="">Seleccionar comuna</option>
                            <option value="la serena">La Serena</option>
                            <option value="coquimbo">Coquimbo</option>
                            <option value="ovalle">Ovalle</option>
                            <option value="vicuña">Vicuña</option>
                            <option value="paihuano">Paihuano</option>
                        </select>
                    </div>
                    
                    <div class="search-group search-text-group">
                        <label for="search-text" class="search-label">¿Qué estás buscando?</label>
                        <input type="text" id="search-text" name="busqueda" class="search-input" placeholder="Ej: departamento 2 dormitorios, casa con jardín...">
                    </div>
                    
                    <div class="search-group search-type-group">
                        <label for="search-type" class="search-label">Tipo de Operación</label>
                        <select id="search-type" name="operacion" class="search-select">
                            <option value="">Cualquier operación</option>
                            <option value="venta">Venta</option>
                            <option value="arriendo">Arriendo</option>
                        </select>
                    </div>
                </div>
                
                <div class="search-actions">
                    <button type="submit" class="search-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                            <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        Buscar Propiedades
                    </button>
                    <button type="reset" class="search-reset">Limpiar</button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Propiedades en Venta -->
<section id="venta" class="section">
    <div class="container">
        <h2 class="section-title">Propiedades en Venta</h2>
        <p class="section-subtitle">Encuentra tu nuevo hogar entre nuestras propiedades destacadas</p>
        
        <div class="properties-slider-container">
            <!-- Swiper -->
            <div class="swiper venta-slider">
                <div class="swiper-wrapper">
                    <?php
                    // Obtener propiedades en venta
                    $venta_properties = mjpropiedades_get_properties('venta', 6); // Más propiedades para el slider
                    
                    // Si no hay propiedades con operación específica, mostrar todas
                    if (!$venta_properties->have_posts()) {
                        $venta_properties = mjpropiedades_get_properties('', 6);
                    }
                    
                    if ($venta_properties->have_posts()) :
                        while ($venta_properties->have_posts()) : $venta_properties->the_post();
                            $precio = get_post_meta(get_the_ID(), '_propiedad_precio', true);
                            $dormitorios = get_post_meta(get_the_ID(), '_propiedad_dormitorios', true);
                            $banos = get_post_meta(get_the_ID(), '_propiedad_banos', true);
                            $metros = get_post_meta(get_the_ID(), '_propiedad_metros', true);
                            $comuna = get_post_meta(get_the_ID(), '_propiedad_comuna', true);
                            $operacion_real = get_post_meta(get_the_ID(), '_propiedad_operacion', true);
                            
                            // Si no hay operación configurada, determinar por precio
                            if (!$operacion_real) {
                                $operacion_real = ($precio && $precio < 1000000) ? 'arriendo' : 'venta';
                            }
                            
                            // Solo mostrar propiedades de venta
                            if ($operacion_real !== 'venta') continue;
                            
                            $tag_class = ($operacion_real === 'arriendo') ? 'style="background: var(--orange);"' : '';
                            $tag_text = ucfirst($operacion_real);
                            $precio_text = $precio ? '$' . number_format($precio, 0, ',', '.') : 'Consultar';
                            if ($operacion_real === 'arriendo') {
                                $precio_text .= '/mes';
                            }
                    ?>
                            <div class="swiper-slide">
                                <div class="property-card">
                                    <div class="property-image">
                                        <div class="property-tag" <?php echo $tag_class; ?>><?php echo $tag_text; ?></div>
                                        <?php if (has_post_thumbnail()) : ?>
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('medium'); ?>
                                            </a>
                                        <?php else : ?>
                                            <a href="<?php the_permalink(); ?>">
                                                <img src="<?php echo get_template_directory_uri(); ?>/images/propiedades/placeholder.jpg" alt="<?php the_title(); ?>" onerror="this.src='data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 400 250\'><rect fill=\'%23f0f0f0\' width=\'400\' height=\'250\'/><text x=\'50%\' y=\'50%\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\'><?php the_title(); ?></text></svg>'">
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="property-content">
                                        <h3 class="property-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h3>
                                        
                                        <?php if ($comuna) : ?>
                                            <p class="property-location">📍 <?php echo $comuna; ?></p>
                                        <?php endif; ?>
                                        
                                        <div class="property-details">
                                            <?php if ($dormitorios) : ?>
                                                <span><?php echo $dormitorios; ?> dormitorios</span>
                                            <?php endif; ?>
                                            
                                            <?php if ($banos) : ?>
                                                <span><?php echo $banos; ?> baños</span>
                                            <?php endif; ?>
                                            
                                            <?php if ($metros) : ?>
                                                <span><?php echo $metros; ?> m²</span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if ($precio) : ?>
                                            <div class="property-price">
                                                <?php echo $precio_text; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <a href="<?php the_permalink(); ?>" class="property-btn">
                                            Ver Detalles
                                        </a>
                                    </div>
                                </div>
                            </div>
                    <?php 
                        endwhile;
                        wp_reset_postdata();
                    else : ?>
                        <div class="swiper-slide">
                            <div class="no-properties">
                                <p>No hay propiedades disponibles en este momento.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Navigation arrows -->
                <div class="swiper-button-next venta-slider-next"></div>
                <div class="swiper-button-prev venta-slider-prev"></div>
                
                <!-- Pagination -->
                <div class="swiper-pagination venta-slider-pagination"></div>
            </div>
        </div>
        
        <!-- Ver todas button -->
        <div class="section-footer">
            <a href="<?php echo home_url('/propiedades/?operacion=venta'); ?>" class="view-all-btn">
                Ver Todas las Propiedades en Venta
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2"/>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h3>¿Listo para comenzar?</h3>
            <p>Contáctanos hoy mismo y descubre cómo podemos ayudarte a alcanzar tus objetivos inmobiliarios.</p>
            <div class="cta-buttons">
                <a href="#contacto" class="cta-btn primary">Agendar Cita Gratuita</a>
                <a href="tel:<?php echo get_theme_mod('mjpropiedades_phone', '+56987654321'); ?>" class="cta-btn secondary">Llamar Ahora</a>
            </div>
        </div>
    </div>
</section>

<!-- Quiénes Somos -->
<section id="about" class="about section">
    <div class="container">
        <div class="about-container">
            <div class="about-content">
                <h2>Conoce a María José</h2>
                <p class="about-text">
                    <?php echo get_theme_mod('mjpropiedades_about_text_1', 'Con más de 8 años de experiencia en el mercado inmobiliario chileno, me especializo en ayudar a familias a encontrar su hogar ideal y a propietarios a obtener el mejor precio por sus propiedades.'); ?>
                </p>
                <p class="about-text">
                    <?php echo get_theme_mod('mjpropiedades_about_text_2', 'Mi compromiso es brindarte un servicio personalizado, transparente y profesional en cada paso del proceso. Desde la primera consulta hasta la firma del contrato, estaré contigo para hacer realidad tus objetivos inmobiliarios.'); ?>
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
                        echo '<img src="' . esc_url($about_image_url) . '" alt="María José">';
                    } else {
                        echo '<img src="' . get_template_directory_uri() . '/images/maria-jose.jpg" alt="María José">';
                    }
                } else {
                    echo '<img src="' . get_template_directory_uri() . '/images/maria-jose.jpg" alt="María José">';
                }
                ?>
                <div class="certification-badge">
                    <div class="badge-icon">✓</div>
                    <div class="badge-text">
                        <strong>Certificada</strong>
                        Colegio de Corredores
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Propiedades en Arriendo -->
<section id="arriendo" class="section bg-light section-spacing">
    <div class="container">
        <h2 class="section-title">Propiedades en Arriendo</h2>
        <p class="section-subtitle">Opciones de arriendo para todos los presupuestos</p>
        
        <div class="properties-slider-container">
            <!-- Swiper -->
            <div class="swiper arriendo-slider">
                <div class="swiper-wrapper">
                    <?php
                    // Obtener propiedades en arriendo
                    $arriendo_properties = mjpropiedades_get_properties('arriendo', 6); // Más propiedades para el slider
                    
                    // Si no hay propiedades con operación específica, mostrar todas
                    if (!$arriendo_properties->have_posts()) {
                        $arriendo_properties = mjpropiedades_get_properties('', 6);
                    }
                    
                    if ($arriendo_properties->have_posts()) :
                        while ($arriendo_properties->have_posts()) : $arriendo_properties->the_post();
                            $precio = get_post_meta(get_the_ID(), '_propiedad_precio', true);
                            $dormitorios = get_post_meta(get_the_ID(), '_propiedad_dormitorios', true);
                            $banos = get_post_meta(get_the_ID(), '_propiedad_banos', true);
                            $metros = get_post_meta(get_the_ID(), '_propiedad_metros', true);
                            $comuna = get_post_meta(get_the_ID(), '_propiedad_comuna', true);
                            $operacion_real = get_post_meta(get_the_ID(), '_propiedad_operacion', true);
                            
                            // Si no hay operación configurada, determinar por precio
                            if (!$operacion_real) {
                                $operacion_real = ($precio && $precio < 1000000) ? 'arriendo' : 'venta';
                            }
                            
                            // Solo mostrar propiedades de arriendo
                            if ($operacion_real !== 'arriendo') continue;
                            
                            $tag_class = ($operacion_real === 'arriendo') ? 'style="background: var(--orange);"' : '';
                            $tag_text = ucfirst($operacion_real);
                            $precio_text = $precio ? '$' . number_format($precio, 0, ',', '.') : 'Consultar';
                            if ($operacion_real === 'arriendo') {
                                $precio_text .= '/mes';
                            }
                    ?>
                            <div class="swiper-slide">
                                <div class="property-card">
                                    <div class="property-image">
                                        <div class="property-tag" <?php echo $tag_class; ?>><?php echo $tag_text; ?></div>
                                        <?php if (has_post_thumbnail()) : ?>
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('medium'); ?>
                                            </a>
                                        <?php else : ?>
                                            <a href="<?php the_permalink(); ?>">
                                                <img src="<?php echo get_template_directory_uri(); ?>/images/propiedades/placeholder.jpg" alt="<?php the_title(); ?>" onerror="this.src='data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 400 250\'><rect fill=\'%23f0f0f0\' width=\'400\' height=\'250\'/><text x=\'50%\' y=\'50%\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\'><?php the_title(); ?></text></svg>'">
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="property-content">
                                        <h3 class="property-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h3>
                                        
                                        <?php if ($comuna) : ?>
                                            <p class="property-location">📍 <?php echo $comuna; ?></p>
                                        <?php endif; ?>
                                        
                                        <div class="property-details">
                                            <?php if ($dormitorios) : ?>
                                                <span><?php echo $dormitorios; ?> dormitorios</span>
                                            <?php endif; ?>
                                            
                                            <?php if ($banos) : ?>
                                                <span><?php echo $banos; ?> baños</span>
                                            <?php endif; ?>
                                            
                                            <?php if ($metros) : ?>
                                                <span><?php echo $metros; ?> m²</span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if ($precio) : ?>
                                            <div class="property-price">
                                                <?php echo $precio_text; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <a href="<?php the_permalink(); ?>" class="property-btn">
                                            Ver Detalles
                                        </a>
                                    </div>
                                </div>
                            </div>
                    <?php 
                        endwhile;
                        wp_reset_postdata();
                    else : ?>
                        <div class="swiper-slide">
                            <div class="no-properties">
                                <p>No hay propiedades disponibles en este momento.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Navigation arrows -->
                <div class="swiper-button-next arriendo-slider-next"></div>
                <div class="swiper-button-prev arriendo-slider-prev"></div>
                
                <!-- Pagination -->
                <div class="swiper-pagination arriendo-slider-pagination"></div>
            </div>
        </div>
        
        <!-- Ver todas button -->
        <div class="section-footer">
            <a href="<?php echo home_url('/propiedades/?operacion=arriendo'); ?>" class="view-all-btn">
                Ver Todas las Propiedades en Arriendo
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2"/>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- Servicios -->
<section id="servicios" class="services section">
    <div class="container">
        <div class="services-header">
            <span class="services-tag">Nuestros Servicios</span>
            <h2 class="section-title">Te Acompañamos en Cada Paso</h2>
            <p class="section-subtitle">Servicios profesionales diseñados para hacer realidad tus objetivos inmobiliarios</p>
        </div>
        
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">
                    <div class="icon-wrapper">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 22V12H15V22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                <div class="service-content">
                    <h3>Venta</h3>
                    <p>Vendemos tu propiedad al mejor precio del mercado con estrategias efectivas de marketing.</p>
                    <ul class="service-features">
                        <li>Marketing profesional</li>
                        <li>Precio optimizado</li>
                        <li>Venta garantizada</li>
                    </ul>
                </div>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <div class="icon-wrapper">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 2L2 7L10 13L21 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M10 13L7 20L2 7L10 13Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                <div class="service-content">
                    <h3>Arriendo</h3>
                    <p>Gestionamos el arriendo de tu propiedad con inquilinos verificados y contratos seguros.</p>
                    <ul class="service-features">
                        <li>Arrendatarios verificados</li>
                        <li>Contratos seguros</li>
                        <li>Gestión completa</li>
                    </ul>
                </div>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <div class="icon-wrapper">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                <div class="service-content">
                    <h3>Tasaciones</h3>
                    <p>Realizamos tasaciones precisas basadas en el análisis del mercado inmobiliario actual.</p>
                    <ul class="service-features">
                        <li>Análisis de mercado</li>
                        <li>Precisión garantizada</li>
                        <li>Informe detallado</li>
                    </ul>
                </div>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <div class="icon-wrapper">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 11H15M9 15H15M17 21H7C5.89543 21 5 20.1046 5 19V5C5 3.89543 5.89543 3 7 3H12.5858C12.851 3 13.1054 3.10536 13.2929 3.29289L19.7071 9.70711C19.8946 9.89464 20 10.149 20 10.4142V19C20 20.1046 19.1046 21 18 21H17Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                <div class="service-content">
                    <h3>Garantía Total</h3>
                    <p>Tu tranquilidad es nuestra prioridad. Garantizamos transacciones 100% seguras y transparentes.</p>
                    <ul class="service-features">
                        <li>Protección completa</li>
                        <li>Transparencia total</li>
                        <li>Respaldo legal</li>
                    </ul>
                </div>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <div class="icon-wrapper">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 11H15M9 15H15M17 21H7C5.89543 21 5 20.1046 5 19V5C5 3.89543 5.89543 3 7 3H12.5858C12.851 3 13.1054 3.10536 13.2929 3.29289L19.7071 9.70711C19.8946 9.89464 20 10.149 20 10.4142V19C20 20.1046 19.1046 21 18 21H17Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                <div class="service-content">
                    <h3>Gestión de Documentos</h3>
                    <p>Te acompañamos en todo el proceso legal y administrativo para que no tengas que preocuparte por nada.</p>
                    <ul class="service-features">
                        <li>Tramitación de escrituras</li>
                        <li>Gestión de permisos</li>
                        <li>Seguimiento legal</li>
                    </ul>
                </div>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <div class="icon-wrapper">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                <div class="service-content">
                    <h3>Atención Personalizada</h3>
                    <p>Un servicio único y personalizado que se adapta a tus necesidades específicas y presupuesto.</p>
                    <ul class="service-features">
                        <li>Consultoría de 09:00 a 18:00 hrs</li>
                        <li>Respuesta rápida</li>
                        <li>Seguimiento continuo</li>
                    </ul>
                </div>
        </div>
        
    </div>
</section>

<!-- Contact Section -->
<section id="contacto" class="section">
    <div class="container">
        <div class="contact-header">
            <span class="contact-tag">Contacto</span>
            <h2 class="section-title">¡Hagamos Realidad tu Proyecto Inmobiliario!</h2>
            <p class="section-subtitle">Completa el formulario y te contactaré en menos de 24 horas</p>
            
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
        
        <form class="contact-form" method="post" action="">
            <?php wp_nonce_field('contact_form_nonce', 'contact_nonce'); ?>
            <input type="hidden" name="contact_form_submitted" value="1">
            <div class="form-group">
                <label for="nombre" class="form-label">Nombre Completo</label>
                <input type="text" id="nombre" name="nombre" class="form-input" placeholder="Tu nombre completo" required>
            </div>
            
            <div class="form-group">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="tel" id="telefono" name="telefono" class="form-input" placeholder="+56 9 1234 5678" required>
            </div>
            
            <div class="form-group email-full">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="tu@email.com" required>
            </div>
            
            <div class="form-group radio-full">
                <label class="form-label">¿Qué necesitas?</label>
                <div class="radio-group">
                    <div class="radio-item">
                        <input type="radio" id="vender" name="tipo_consulta" value="vender">
                        <label for="vender">Vender mi propiedad</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" id="comprar" name="tipo_consulta" value="comprar">
                        <label for="comprar">Comprar propiedad</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" id="tasacion" name="tipo_consulta" value="tasacion">
                        <label for="tasacion">Tasación</label>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="tipo_propiedad" class="form-label">Tipo de Propiedad</label>
                <select id="tipo_propiedad" name="tipo_propiedad" class="form-select">
                    <option value="">Seleccionar tipo</option>
                    <option value="casa">Casa</option>
                    <option value="departamento">Departamento</option>
                    <option value="oficina">Oficina</option>
                    <option value="local">Local Comercial</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="comuna" class="form-label">Comuna</label>
                <input type="text" id="comuna" name="comuna" class="form-input" placeholder="¿En qué comuna de la Cuarta Región?">
            </div>
            
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="mensaje" class="form-label">Mensaje Adicional</label>
                <textarea id="mensaje" name="mensaje" class="form-textarea" placeholder="Cuéntame más detalles sobre lo que necesitas..."></textarea>
            </div>
            
            <div class="form-group" style="grid-column: 1 / -1;">
                <button type="submit" class="submit-btn">Enviar Solicitud</button>
                
                <p class="form-disclaimer">
                    Al enviar este formulario, aceptas que María José se contacte contigo para brindarte información sobre propiedades.
                </p>
            </div>
        </form>
    </div>
</section>

<?php get_footer(); ?>
