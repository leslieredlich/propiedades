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
        
        // Si no hay imágenes configuradas, mostrar imagen por defecto
        if (!$has_images) {
            echo '<div class="hero-slide active" style="background-image: url(\'' . get_template_directory_uri() . '/images/hero-default.jpg\');">';
            echo '<div class="hero-overlay"></div>';
            echo '</div>';
        }
        ?>
    </div>
    
    <div class="hero-container">
        <div class="hero-content">
            <span class="hero-tag">Compra de Propiedades</span>
            <h1>Encuentra el Hogar de tus Sueños</h1>
            <p class="hero-description">
                Descubre propiedades exclusivas que se ajustan a tu estilo de vida. Asesoría personalizada en todo el proceso de compra.
            </p>
            <a href="#venta" class="hero-btn">
                Buscar Propiedades →
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
                <h2>Conoce a María José</h2>
                <p class="about-text">
                    Especialistas N°1 en la Cuarta Región. Con más de 8 años de experiencia, me especializo en inversión en La Serena, arriendos en Coquimbo y propiedades en Ovalle, ayudando a familias a encontrar su hogar ideal.
                </p>
                <p class="about-text">
                    Mi compromiso es brindarte un servicio personalizado, transparente y profesional en cada paso del proceso. Desde la primera consulta hasta la firma del contrato, estaré contigo para hacer realidad tus objetivos inmobiliarios en La Serena, Coquimbo y Ovalle.
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
                <img src="<?php echo get_template_directory_uri(); ?>/images/maria-jose.jpg" alt="María José">
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
