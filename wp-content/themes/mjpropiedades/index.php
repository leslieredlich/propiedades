<?php get_header(); ?>

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

<!-- Propiedades en Venta -->
<section id="venta" class="section">
    <div class="container">
        <h2 class="section-title">Propiedades en Venta</h2>
        <p class="section-subtitle">Encuentra tu nuevo hogar entre nuestras propiedades destacadas</p>
        
        <?php mjpropiedades_display_properties('venta', 3); ?>
    </div>
</section>

<!-- About Section -->
<section id="about" class="about section">
    <div class="container">
        <div class="about-container">
            <div class="about-content">
                <h2>Sobre Home Isa</h2>
                <p class="about-text">
                    Home Isa es una empresa inmobiliaria innovadora fundada en 2025, con alcance nacional en Chile. Nos especializamos en brindar servicios integrales de corretaje inmobiliario, asesoría y tasación de propiedades.
                </p>
                <p class="about-text">
                    Nuestro compromiso es facilitar el proceso inmobiliario para todo tipo de clientes: familias que buscan su primer hogar, inversionistas experimentados que buscan oportunidades de crecimiento, propietarios que desean vender sus propiedades al mejor precio, y personas que necesitan arrendar o encontrar inquilinos para sus inmuebles. Con sede en La Serena y cobertura nacional, combinamos la experiencia local con una visión moderna del mercado inmobiliario chileno.
                </p>
                
                <div class="stats">
                    <div class="stat">
                        <span class="stat-number">500+</span>
                        <span class="stat-label">Propiedades Vendidas</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">98%</span>
                        <span class="stat-label">Clientes Satisfechos</span>
                    </div>
                </div>
            </div>
            
            <div class="about-image">
                <img src="<?php echo get_template_directory_uri(); ?>/images/maria-jose.jpg" alt="Home Isa">
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
<section id="arriendo" class="section">
    <div class="container">
        <h2 class="section-title">Propiedades en Arriendo</h2>
        <p class="section-subtitle">Opciones de arriendo para todos los presupuestos</p>
        
        <?php mjpropiedades_display_properties('arriendo', 3); ?>
    </div>
</section>

<!-- Contact Section -->
<section id="contacto" class="section">
    <div class="container">
        <h2 class="section-title">¿Qué Necesitas Hoy?</h2>
        <p class="section-subtitle">Completa el formulario y te contactaré en menos de 24 horas</p>
        
        <form class="contact-form" method="post" action="">
            <div class="form-group">
                <label for="nombre" class="form-label">Nombre Completo</label>
                <input type="text" id="nombre" name="nombre" class="form-input" placeholder="Tu nombre completo" required>
            </div>
            
            <div class="form-group">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="tel" id="telefono" name="telefono" class="form-input" placeholder="+56 9 1234 5678" required>
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="tu@email.com" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">¿Qué necesitas?</label>
                <div class="radio-group">
                    <div class="radio-item">
                        <input type="radio" id="vender" name="tipo_consulta" value="vender">
                        <label for="vender">Vender mi propiedad</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" id="comprar" name="tipo_consulta" value="comprar">
                        <label for="comprar">Comprar una propiedad</label>
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
                <input type="text" id="comuna" name="comuna" class="form-input" placeholder="¿En qué comuna?">
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

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2 class="cta-title">¿Listo para Dar el Siguiente Paso?</h2>
        <p class="cta-text">No importa si quieres comprar, vender o arrendar. Estoy aquí para ayudarte a tomar la mejor decisión.</p>
        
        <div class="cta-buttons">
            <a href="#contacto" class="cta-btn primary">Agendar Cita Gratuita</a>
            <a href="tel:+56987654321" class="cta-btn secondary">Llamar Ahora</a>
        </div>
    </div>
</section>

<?php get_footer(); ?>
