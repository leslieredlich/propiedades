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
                <h3>HOME ISA</h3>
                <p>Tu corredora de confianza especializada en la Cuarta Región de Chile.</p>
                <div class="social-icons">
                    <a href="#" class="social-icon">f</a>
                    <a href="#" class="social-icon">📷</a>
                    <a href="https://wa.me/<?php echo str_replace(array('+', ' ', '-'), '', get_theme_mod('mjpropiedades_phone', '+56987654321')); ?>" class="social-icon" target="_blank">💬</a>
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
                    <span><?php echo get_theme_mod('mjpropiedades_phone', '++56 9 4927 6448'); ?></span>
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
            <p>©<?php echo date('Y'); ?> María José Propiedades. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<!-- WhatsApp Button -->
<a href="https://wa.me/<?php echo str_replace(array('+', ' ', '-'), '', get_theme_mod('mjpropiedades_phone', '+56987654321')); ?>" class="whatsapp-btn" target="_blank">
    💬
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
