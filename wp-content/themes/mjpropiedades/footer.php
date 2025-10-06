<!-- CTA Section -->
<!-- <section class="cta-section">
    <div class="container">
        <h2 class="cta-title">¿Listo para Dar el Siguiente Paso?</h2>
        <p class="cta-text">No importa si quieres comprar, vender o arrendar. Estoy aquí para ayudarte a tomar la mejor decisión.</p>
        
        <div class="cta-buttons">
            <a href="#contacto" class="cta-btn primary">Agendar Cita Gratuita</a>
            <a href="tel:<?php echo get_theme_mod('mjpropiedades_phone', '+56987654321'); ?>" class="cta-btn secondary">Llamar Ahora</a>
        </div>
    </div>
</section> -->

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-brand">
                <?php 
                // Logo específico del footer
                $footer_logo_id = get_theme_mod('mjpropiedades_footer_logo_image', '');
                $footer_logo_show_text = get_theme_mod('mjpropiedades_footer_logo_show_text', true);
                $footer_logo_text = get_theme_mod('mjpropiedades_footer_logo_text', 'Tu corredora de confianza especializada en la Cuarta Región de Chile.');
                
                if ($footer_logo_id) {
                    // Logo específico del footer desde medios
                    echo '<div class="footer-logo">';
                    echo '<a href="' . home_url() . '">';
                    echo wp_get_attachment_image($footer_logo_id, 'full', false, array('alt' => get_bloginfo('name')));
                    echo '</a>';
                    echo '</div>';
                } elseif (has_custom_logo()) {
                    // Fallback: Logo del header si no hay logo específico del footer
                    echo '<div class="footer-logo">';
                    the_custom_logo();
                    echo '</div>';
                } else {
                    // Fallback: Logo de texto si no hay ningún logo configurado
                    echo '<div class="footer-logo-text">';
                    echo '<a href="' . home_url() . '" class="logo-plus-propiedades">';
                    echo '<span class="logo-plus">PLUS</span>';
                    echo '<span class="logo-propiedades">PROPIEDADES</span>';
                    echo '</a>';
                    echo '</div>';
                }
                
                // Mostrar texto descriptivo si está habilitado
                if ($footer_logo_show_text) {
                    echo '<p>' . esc_html($footer_logo_text) . '</p>';
                }
                ?>
                <div class="social-icons">
                    <a href="https://facebook.com/Home isa spa" class="social-icon facebook" target="_blank">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="https://instagram.com/homeisa_spa" class="social-icon instagram" target="_blank">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                    <a href="https://wa.me/<?php echo str_replace(array('+', ' ', '-'), '', get_theme_mod('mjpropiedades_phone', '+56987654321')); ?>" class="social-icon whatsapp" target="_blank">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                        </svg>
                    </a>
                </div>
            </div>
            
            <div class="footer-section">
                <h4>Servicios</h4>
                <ul>
                    <li><a href="#venta">Venta de Propiedades</a></li>
                    <li><a href="#arriendo">Arriendo de Propiedades</a></li>
                    <li><a href="#">Tasaciones</a></li>
                    <li><a href="#">Gestión de Documentos</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Cuarta Región</h4>
                <ul>
                    <li><a href="#">La Serena</a></li>
                    <li><a href="#">Coquimbo</a></li>
                    <li><a href="#">Ovalle</a></li>
                    <li><a href="#">Vicuña</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Contacto</h4>
                <div class="contact-info">
                    <span>📞</span>
                    <span><?php echo get_theme_mod('mjpropiedades_phone', '+56 9 4927 6448'); ?></span>
                </div>
                <div class="contact-info">
                    <span>✉️</span>
                    <span><?php echo get_theme_mod('mjpropiedades_email', 'homeisaspa@gmail.com'); ?></span>
                </div>
                <div class="contact-info">
                    <span>📍</span>
                    <span><?php echo get_theme_mod('mjpropiedades_address', 'Cuarta Región, Chile'); ?></span>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>©<?php echo date('Y'); ?> Home Isa Propiedades. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<!-- WhatsApp Button -->
<a href="https://wa.me/<?php echo str_replace(array('+', ' ', '-'), '', get_theme_mod('mjpropiedades_phone', '+56949276448')); ?>" class="whatsapp-btn" target="_blank" title="Contactar por WhatsApp">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" fill="currentColor"/>
    </svg>
</a>

<?php wp_footer(); ?>

<script>
// Smooth scrolling para enlaces internos
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

// Carousel functionality
let currentSlide = 0;
const dots = document.querySelectorAll('.dot');
const slides = document.querySelectorAll('.hero-slide');
const totalSlides = dots.length;

function updateCarousel() {
    // Actualizar dots
    dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === currentSlide);
    });
    
    // Actualizar slides
    slides.forEach((slide, index) => {
        slide.classList.toggle('active', index === currentSlide);
    });
}

if (dots.length > 0) {
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            currentSlide = index;
            updateCarousel();
        });
    });

    // Navegación con botones
    const prevBtn = document.querySelector('.carousel-nav.prev');
    const nextBtn = document.querySelector('.carousel-nav.next');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            currentSlide = currentSlide > 0 ? currentSlide - 1 : totalSlides - 1;
            updateCarousel();
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateCarousel();
        });
    }

    // Auto-advance carousel
    setInterval(() => {
        currentSlide = (currentSlide + 1) % totalSlides;
        updateCarousel();
    }, 5000);
}

// Form submission - Simplificado para evitar errores de JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.querySelector('.contact-form');
    const submitBtn = document.querySelector('.submit-btn');
    
    if (contactForm && submitBtn) {
        contactForm.addEventListener('submit', function() {
            // Mostrar estado de envío
            submitBtn.textContent = 'Enviando...';
            submitBtn.disabled = true;
            
            // Validar campos requeridos antes de enviar
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#dc3545';
                    isValid = false;
                } else {
                    field.style.borderColor = '#e9ecef';
                }
            });
            
            if (!isValid) {
                // Si hay errores, restaurar el botón
                submitBtn.textContent = 'Enviar Solicitud';
                submitBtn.disabled = false;
                return false; // Prevenir envío
            }
            
            // Si todo está bien, permitir el envío normal del formulario
            return true;
        });
    }
});

// Mostrar mensaje de confirmación si existe
<?php mjpropiedades_show_contact_message(); ?>

// Funcionalidad del menú móvil
document.addEventListener('DOMContentLoaded', function() {
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    const mobileNav = document.querySelector('.mobile-nav');
    const mobileOverlay = document.querySelector('.mobile-menu-overlay');
    const mobileLinks = document.querySelectorAll('.mobile-nav-menu a');
    
    if (mobileToggle && mobileNav && mobileOverlay) {
        // Abrir menú
        mobileToggle.addEventListener('click', function() {
            mobileNav.classList.add('active');
            mobileOverlay.classList.add('active');
            mobileToggle.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevenir scroll
        });
        
        // Cerrar menú al hacer clic en overlay
        mobileOverlay.addEventListener('click', function() {
            closeMobileMenu();
        });
        
        // Cerrar menú al hacer clic en enlaces
        mobileLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                closeMobileMenu();
            });
        });
        
        // Función para cerrar menú
        function closeMobileMenu() {
            mobileNav.classList.remove('active');
            mobileOverlay.classList.remove('active');
            mobileToggle.classList.remove('active');
            document.body.style.overflow = ''; // Restaurar scroll
        }
        
        // Cerrar menú con tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mobileNav.classList.contains('active')) {
                closeMobileMenu();
            }
        });
        
        // REDISEÑO COMPLETO DEL BOTÓN DE CERRAR
        // Función específica para cerrar menú desde botón X
        function closeMenuFromButton() {
            console.log('Cerrando menú desde botón X');
            mobileNav.classList.remove('active');
            mobileOverlay.classList.remove('active');
            mobileToggle.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        // Método 1: Event listener directo cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            const closeBtn = document.querySelector('.mobile-menu-close');
            console.log('Botón de cerrar encontrado:', closeBtn);
            
            if (closeBtn) {
                closeBtn.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Click directo en botón X');
                    closeMenuFromButton();
                    return false;
                };
            }
        });
        
        // Método 2: Delegación de eventos global
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('mobile-menu-close') || 
                e.target.classList.contains('close-icon') ||
                e.target.closest('.mobile-menu-close')) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Click delegado en botón X');
                closeMenuFromButton();
                return false;
            }
        });
        
        // Método 3: Event listener en el contenedor del menú
        if (mobileNav) {
            mobileNav.addEventListener('click', function(e) {
                if (e.target.classList.contains('mobile-menu-close') || 
                    e.target.classList.contains('close-icon') ||
                    e.target.closest('.mobile-menu-close')) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Click en contenedor del menú');
                    closeMenuFromButton();
                    return false;
                }
            });
        }
        
        // Método 4: Implementación con jQuery si está disponible
        if (typeof jQuery !== 'undefined') {
            jQuery(document).ready(function($) {
                $('.mobile-menu-close').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Click con jQuery en botón X');
                    closeMenuFromButton();
                    return false;
                });
                
                // También para el icono dentro del botón
                $('.close-icon').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Click con jQuery en icono X');
                    closeMenuFromButton();
                    return false;
                });
            });
        }
        
        // Método 5: Verificación periódica para asegurar que el botón funcione
        setInterval(function() {
            const closeBtn = document.querySelector('.mobile-menu-close');
            if (closeBtn && !closeBtn.hasAttribute('data-listener-added')) {
                closeBtn.setAttribute('data-listener-added', 'true');
                closeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Click periódico en botón X');
                    closeMenuFromButton();
                    return false;
                });
            }
        }, 500);
    }
});

// FUNCIÓN GLOBAL PARA EL ONCLICK DEL BOTÓN X
function closeMobileMenuFromButton(event) {
    console.log('Función global llamada para cerrar menú');
    event.preventDefault();
    event.stopPropagation();
    
    // Obtener elementos del menú
    const mobileNav = document.querySelector('.mobile-nav');
    const mobileOverlay = document.querySelector('.mobile-menu-overlay');
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    
    if (mobileNav) {
        mobileNav.classList.remove('active');
    }
    if (mobileOverlay) {
        mobileOverlay.classList.remove('active');
    }
    if (mobileToggle) {
        mobileToggle.classList.remove('active');
    }
    
    // Restaurar scroll del body
    document.body.style.overflow = '';
    
    console.log('Menú cerrado desde función global');
    return false;
}

// Mejoras para sliders móviles
document.addEventListener('DOMContentLoaded', function() {
    // Configurar Swiper para móvil
    if (typeof Swiper !== 'undefined') {
        // Configuración para slider de venta
        const ventaSlider = new Swiper('.venta-slider', {
            slidesPerView: 'auto',
            spaceBetween: 20,
            centeredSlides: false,
            loop: false,
            grabCursor: true,
            touchRatio: 1,
            touchAngle: 45,
            threshold: 5,
            resistanceRatio: 0.85,
            pagination: {
                el: '.venta-slider-pagination',
                clickable: true,
                dynamicBullets: true,
            },
            navigation: {
                nextEl: '.venta-slider-next',
                prevEl: '.venta-slider-prev',
            },
            breakpoints: {
                320: {
                    slidesPerView: 1.2,
                    spaceBetween: 15,
                    centeredSlides: true,
                },
                480: {
                    slidesPerView: 1.5,
                    spaceBetween: 20,
                    centeredSlides: false,
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 25,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 30,
                }
            }
        });
        
        // Configuración para slider de arriendo
        const arriendoSlider = new Swiper('.arriendo-slider', {
            slidesPerView: 'auto',
            spaceBetween: 20,
            centeredSlides: false,
            loop: false,
            grabCursor: true,
            touchRatio: 1,
            touchAngle: 45,
            threshold: 5,
            resistanceRatio: 0.85,
            pagination: {
                el: '.arriendo-slider-pagination',
                clickable: true,
                dynamicBullets: true,
            },
            navigation: {
                nextEl: '.arriendo-slider-next',
                prevEl: '.arriendo-slider-prev',
            },
            breakpoints: {
                320: {
                    slidesPerView: 1.2,
                    spaceBetween: 15,
                    centeredSlides: true,
                },
                480: {
                    slidesPerView: 1.5,
                    spaceBetween: 20,
                    centeredSlides: false,
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 25,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 30,
                }
            }
        });
        
        // Mejorar experiencia táctil
        const sliders = [ventaSlider, arriendoSlider];
        
        sliders.forEach(slider => {
            if (slider) {
                // Ajustar velocidad de swipe para móvil
                slider.params.touchStartPreventDefault = false;
                slider.params.touchMoveStopPropagation = false;
                
                // Mejorar feedback táctil
                slider.on('touchStart', function() {
                    this.el.style.transition = 'none';
                });
                
                slider.on('touchEnd', function() {
                    this.el.style.transition = '';
                });
                
                // Auto-hide navigation en móvil
                if (window.innerWidth <= 480) {
                    slider.params.navigation = false;
                    slider.navigation.destroy();
                }
            }
        });
    }
    
    // Mejorar carrusel del hero para móvil
    const heroSlider = document.querySelector('.hero-slider');
    if (heroSlider && window.innerWidth <= 768) {
        // Deshabilitar parallax en móvil para mejor rendimiento
        heroSlider.style.willChange = 'transform';
        
        // Mejorar dots para touch
        const dots = document.querySelectorAll('.dot');
        dots.forEach(dot => {
            dot.style.minWidth = '44px';
            dot.style.minHeight = '44px';
            dot.style.display = 'flex';
            dot.style.alignItems = 'center';
            dot.style.justifyContent = 'center';
        });
    }
});
</script>

</body>
</html>
