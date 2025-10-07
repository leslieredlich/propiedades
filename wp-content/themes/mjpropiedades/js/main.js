/**
 * Home Isa Propiedades - JavaScript Principal
 */

jQuery(document).ready(function($) {
    
    // Smooth scrolling para enlaces internos
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 800);
        }
    });
    
    // Funcionalidad de los sliders de precio
    function formatPrice(value) {
        return '$' + parseInt(value).toLocaleString('es-CL');
    }
    
    // Slider precio mínimo
    $('#search-precio-min').on('input', function() {
        const value = $(this).val();
        $('#precio-min-value').text(formatPrice(value));
        
        // Asegurar que el precio mínimo no sea mayor que el máximo
        const maxValue = $('#search-precio-max').val();
        if (parseInt(value) > parseInt(maxValue)) {
            $('#search-precio-max').val(value);
            $('#precio-max-value').text(formatPrice(value));
        }
    });
    
    // Slider precio máximo
    $('#search-precio-max').on('input', function() {
        const value = $(this).val();
        $('#precio-max-value').text(formatPrice(value));
        
        // Asegurar que el precio máximo no sea menor que el mínimo
        const minValue = $('#search-precio-min').val();
        if (parseInt(value) < parseInt(minValue)) {
            $('#search-precio-min').val(value);
            $('#precio-min-value').text(formatPrice(value));
        }
    });
    
    // Inicializar valores de los sliders
    $('#precio-min-value').text(formatPrice($('#search-precio-min').val()));
    $('#precio-max-value').text(formatPrice($('#search-precio-max').val()));
    
    // Carousel functionality
let currentSlide = 0;
const $dots = $('.dot');
const $slides = $('.hero-slide');
const $slideContents = $('.slide-content');
const totalSlides = $dots.length;

function updateCarousel() {
    // Actualizar dots
    $dots.removeClass('active').eq(currentSlide).addClass('active');
    
    // Actualizar slides de fondo
    $slides.removeClass('active').eq(currentSlide).addClass('active');
    
    // Actualizar contenido de las diapositivas
    $slideContents.removeClass('active').eq(currentSlide).addClass('active');
}

if ($dots.length > 0) {
    $dots.on('click', function() {
        const slideIndex = $(this).data('slide');
        if (slideIndex !== undefined) {
            currentSlide = slideIndex;
        } else {
            currentSlide = $(this).index();
        }
        updateCarousel();
    });
    
    // Navegación con botones
    $('.carousel-nav.prev').on('click', function() {
        currentSlide = currentSlide > 0 ? currentSlide - 1 : totalSlides - 1;
        updateCarousel();
    });
    
    $('.carousel-nav.next').on('click', function() {
        currentSlide = (currentSlide + 1) % totalSlides;
        updateCarousel();
    });
    
    // Auto-advance carousel
    setInterval(() => {
        currentSlide = (currentSlide + 1) % totalSlides;
        updateCarousel();
    }, 6000); // Aumentado a 6 segundos para mejor experiencia
}
    
    // Form submission con AJAX
    $('.contact-form').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $submitBtn = $form.find('.submit-btn');
        const originalText = $submitBtn.text();
        
        // Validar campos requeridos
        let isValid = true;
        $form.find('[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).css('border-color', '#dc3545');
                isValid = false;
            } else {
                $(this).css('border-color', '#e9ecef');
            }
        });
        
        if (!isValid) {
            alert('Por favor completa todos los campos obligatorios.');
            return;
        }
        
        // Mostrar loading
        $submitBtn.text('Enviando...').prop('disabled', true);
        
        // Enviar formulario
        $.ajax({
            url: mjpropiedades_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mjpropiedades_contact_form',
                nombre: $form.find('[name="nombre"]').val(),
                email: $form.find('[name="email"]').val(),
                telefono: $form.find('[name="telefono"]').val(),
                tipo_consulta: $form.find('[name="tipo_consulta"]:checked').val(),
                tipo_propiedad: $form.find('[name="tipo_propiedad"]').val(),
                comuna: $form.find('[name="comuna"]').val(),
                mensaje: $form.find('[name="mensaje"]').val(),
                nonce: mjpropiedades_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('¡Gracias por tu mensaje! Te contactaremos pronto.');
                    $form[0].reset();
                } else {
                    alert('Error al enviar el mensaje. Por favor intenta nuevamente.');
                }
            },
            error: function() {
                alert('Error al enviar el mensaje. Por favor intenta nuevamente.');
            },
            complete: function() {
                $submitBtn.text(originalText).prop('disabled', false);
            }
        });
    });
    
    // Animaciones al hacer scroll
    function animateOnScroll() {
        $('.property-card, .about-content, .about-image').each(function() {
            const elementTop = $(this).offset().top;
            const elementBottom = elementTop + $(this).outerHeight();
            const viewportTop = $(window).scrollTop();
            const viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                $(this).addClass('animate-in');
            }
        });
    }
    
    $(window).on('scroll', animateOnScroll);
    animateOnScroll(); // Ejecutar al cargar
    
    // Header scroll effect
    $(window).on('scroll', function() {
        if ($(window).scrollTop() > 100) {
            $('.header').addClass('scrolled');
        } else {
            $('.header').removeClass('scrolled');
        }
    });
    
    // Mobile menu toggle (si se implementa)
    $('.mobile-menu-toggle').on('click', function() {
        $('.nav-menu').toggleClass('mobile-open');
    });
    
    // Lazy loading para imágenes
    if ('IntersectionObserver' in window) {
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
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // Validación en tiempo real
    $('.form-input, .form-select, .form-textarea').on('blur', function() {
        const $field = $(this);
        const value = $field.val().trim();
        
        if ($field.prop('required') && !value) {
            $field.addClass('error');
        } else {
            $field.removeClass('error');
        }
        
        // Validación específica para email
        if ($field.attr('type') === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                $field.addClass('error');
            } else {
                $field.removeClass('error');
            }
        }
    });
    
    // Tooltip para botones de redes sociales
    $('.social-icon').hover(
        function() {
            $(this).attr('title', $(this).text() === 'f' ? 'Facebook' : 
                                $(this).text() === '📷' ? 'Instagram' : 'WhatsApp');
        }
    );
    
    // Contador animado para estadísticas
    function animateCounters() {
        $('.stat-number').each(function() {
            const $this = $(this);
            const countTo = $this.attr('data-count');
            
            if (countTo) {
                $({ countNum: $this.text() }).animate({
                    countNum: countTo
                }, {
                    duration: 2000,
                    easing: 'swing',
                    step: function() {
                        $this.text(Math.floor(this.countNum));
                    },
                    complete: function() {
                        $this.text(this.countNum);
                    }
                });
            }
        });
    }
    
    // Activar contadores cuando la sección about sea visible
    const aboutObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                aboutObserver.unobserve(entry.target);
            }
        });
    });
    
    const aboutSection = document.querySelector('#about');
    if (aboutSection) {
        aboutObserver.observe(aboutSection);
    }
    
    // Prevenir envío múltiple del formulario
    let formSubmitting = false;
    $('.contact-form').on('submit', function() {
        if (formSubmitting) {
            return false;
        }
        formSubmitting = true;
        setTimeout(() => {
            formSubmitting = false;
        }, 3000);
    });
    
    // Mejorar accesibilidad
    $('.property-card').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            $(this).find('.property-btn').click();
        }
    });
    
    // Agregar atributos ARIA
    $('.carousel-nav').attr('aria-label', 'Navegar carrusel');
    $('.dot').attr('role', 'button').attr('aria-label', 'Ir a diapositiva');
    
    // Mejorar experiencia en móviles
    if ($(window).width() < 768) {
        $('.hero-content h1').css('font-size', '2.5rem');
        $('.section-title').css('font-size', '2rem');
    }
    
    // Optimización de rendimiento
    let ticking = false;
    
    function updateOnScroll() {
        animateOnScroll();
        ticking = false;
    }
    
    $(window).on('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(updateOnScroll);
            ticking = true;
        }
    });
    
});

// Funciones globales
window.mjpropiedades = {
    // Función para abrir WhatsApp
    openWhatsApp: function(message = '') {
        const phone = '56987654321'; // Número por defecto
        const url = `https://wa.me/${phone}${message ? '?text=' + encodeURIComponent(message) : ''}`;
        window.open(url, '_blank');
    },
    
    // Función para llamar
    call: function(phone = '+56987654321') {
        window.location.href = `tel:${phone}`;
    },
    
    // Función para enviar email
    email: function(subject = '', body = '') {
        const email = 'homeisaspa@gmail.com';
        const url = `mailto:${email}${subject ? '?subject=' + encodeURIComponent(subject) : ''}${body ? '&body=' + encodeURIComponent(body) : ''}`;
        window.location.href = url;
    }
};

// Inicializar Sliders de Propiedades
jQuery(document).ready(function($) {
    // Esperar a que Swiper esté disponible
    if (typeof Swiper !== 'undefined') {
        // Slider para Propiedades en Venta
        if (document.querySelector('.venta-slider')) {
            const ventaSlider = new Swiper('.venta-slider', {
            slidesPerView: 'auto',
            spaceBetween: 40,
            loop: true,
            centeredSlides: false,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            navigation: {
                nextEl: '.venta-slider-next',
                prevEl: '.venta-slider-prev',
            },
            pagination: {
                el: '.venta-slider-pagination',
                clickable: true,
                dynamicBullets: true,
            },
            breakpoints: {
                480: {
                    spaceBetween: 25,
                },
                768: {
                    spaceBetween: 30,
                },
                1024: {
                    spaceBetween: 35,
                },
                1200: {
                    spaceBetween: 40,
                }
            },
            on: {
                init: function() {
                    // Asegurar que los botones de navegación funcionen
                    this.update();
                },
                resize: function() {
                    this.update();
                }
            }
        });
    }
    
    // Slider para Propiedades en Arriendo
    if (document.querySelector('.arriendo-slider')) {
        const arriendoSlider = new Swiper('.arriendo-slider', {
            slidesPerView: 'auto',
            spaceBetween: 40,
            loop: true,
            centeredSlides: false,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            navigation: {
                nextEl: '.arriendo-slider-next',
                prevEl: '.arriendo-slider-prev',
            },
            pagination: {
                el: '.arriendo-slider-pagination',
                clickable: true,
                dynamicBullets: true,
            },
            breakpoints: {
                480: {
                    spaceBetween: 25,
                },
                768: {
                    spaceBetween: 30,
                },
                1024: {
                    spaceBetween: 35,
                },
                1200: {
                    spaceBetween: 40,
                }
            },
            on: {
                init: function() {
                    // Asegurar que los botones de navegación funcionen
                    this.update();
                },
                resize: function() {
                    this.update();
                }
            }
        });
    }
    
    } else {
        // Si Swiper no está disponible, intentar cargar después
        setTimeout(function() {
            if (typeof Swiper !== 'undefined') {
                // Reintenta la inicialización
                location.reload();
            }
        }, 1000);
    }
});

// Funcionalidades específicas para la página de propiedades
jQuery(document).ready(function($) {
    
    // Solo ejecutar si estamos en la página de propiedades
    if ($('.properties-page').length) {
        
        // Toggle de filtros en mobile
        $('.filters-toggle').on('click', function() {
            $(this).toggleClass('active');
            $('.filters-content').toggleClass('active');
        });
        
        // Cambio de vista (grid/list)
        $('.view-btn').on('click', function() {
            $('.view-btn').removeClass('active');
            $(this).addClass('active');
            
            const view = $(this).data('view');
            const grid = $('#properties-grid');
            
            if (view === 'list') {
                grid.addClass('list-view');
            } else {
                grid.removeClass('list-view');
            }
        });
        
        // Ordenamiento
        $('#sort-by').on('change', function() {
            const sortValue = $(this).val();
            const url = new URL(window.location);
            url.searchParams.set('sort', sortValue);
            window.location.href = url.toString();
        });
        
        // Favoritos
        $('.property-favorite').on('click', function(e) {
            e.preventDefault();
            $(this).toggleClass('active');
            
            if ($(this).hasClass('active')) {
                $(this).find('svg').css('fill', '#ff6b35');
                // Aquí podrías agregar lógica para guardar en localStorage o enviar al servidor
                localStorage.setItem('favorite_' + $(this).closest('.property-card').data('id'), 'true');
            } else {
                $(this).find('svg').css('fill', 'currentColor');
                localStorage.removeItem('favorite_' + $(this).closest('.property-card').data('id'));
            }
        });
        
        // Compartir
        $('.property-share').on('click', function(e) {
            e.preventDefault();
            
            const propertyCard = $(this).closest('.property-card');
            const title = propertyCard.find('.property-title a').text();
            const url = propertyCard.find('.property-title a').attr('href');
            
            if (navigator.share) {
                navigator.share({
                    title: title,
                    url: url
                });
            } else {
                // Fallback: copiar URL al portapapeles
                navigator.clipboard.writeText(url).then(function() {
                    alert('URL copiada al portapapeles');
                });
            }
        });
        
        // Auto-submit del formulario de filtros cuando cambian los selects
        $('.filter-select').on('change', function() {
            $(this).closest('form').submit();
        });
        
        // Sliders de rango de precio mejorados
        function formatPrice(value) {
            return '$' + parseInt(value).toLocaleString('es-CL');
        }
        
        // Slider precio mínimo
        $('#precio-min-slider').on('input', function() {
            const value = $(this).val();
            $('#filter-precio-min').val(value);
            $('.price-min-label').text(formatPrice(value));
            
            // Asegurar que el precio mínimo no sea mayor que el máximo
            const maxValue = $('#precio-max-slider').val();
            if (parseInt(value) > parseInt(maxValue)) {
                $('#precio-max-slider').val(value);
                $('#filter-precio-max').val(value);
                $('.price-max-label').text(formatPrice(value));
            }
        });
        
        // Slider precio máximo
        $('#precio-max-slider').on('input', function() {
            const value = $(this).val();
            $('#filter-precio-max').val(value);
            $('.price-max-label').text(formatPrice(value));
            
            // Asegurar que el precio máximo no sea menor que el mínimo
            const minValue = $('#precio-min-slider').val();
            if (parseInt(value) < parseInt(minValue)) {
                $('#precio-min-slider').val(value);
                $('#filter-precio-min').val(value);
                $('.price-min-label').text(formatPrice(value));
            }
        });
        
        // Inputs de precio
        $('#filter-precio-min').on('input', function() {
            const value = $(this).val();
            $('#precio-min-slider').val(value);
            $('.price-min-label').text(formatPrice(value));
        });
        
        $('#filter-precio-max').on('input', function() {
            const value = $(this).val();
            $('#precio-max-slider').val(value);
            $('.price-max-label').text(formatPrice(value));
        });
        
        // Inicializar valores de los sliders
        if ($('#precio-min-slider').length) {
            $('.price-min-label').text(formatPrice($('#precio-min-slider').val()));
            $('.price-max-label').text(formatPrice($('#precio-max-slider').val()));
        }
        
        // Cargar favoritos guardados
        $('.property-card').each(function() {
            const cardId = $(this).data('id');
            if (cardId && localStorage.getItem('favorite_' + cardId)) {
                $(this).find('.property-favorite').addClass('active').find('svg').css('fill', '#ff6b35');
            }
        });
        
        // Animaciones de entrada para las tarjetas
        const propertyObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1
        });
        
        $('.property-card').each(function(index) {
            $(this).css({
                'opacity': '0',
                'transform': 'translateY(20px)',
                'transition': 'opacity 0.6s ease, transform 0.6s ease',
                'transition-delay': (index * 0.1) + 's'
            });
            propertyObserver.observe(this);
        });
        
        // Mejorar accesibilidad
        $('.property-card').attr('tabindex', '0').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                $(this).find('.property-title a')[0].click();
            }
        });
        
        // Loading state para el formulario de filtros
        $('.filters-form').on('submit', function() {
            const $form = $(this);
            const $submitBtn = $form.find('.filter-btn.primary');
            const originalText = $submitBtn.text();
            
            $submitBtn.text('Buscando...').prop('disabled', true);
            
            // Re-habilitar después de un tiempo
            setTimeout(() => {
                $submitBtn.text(originalText).prop('disabled', false);
            }, 2000);
        });
        
        // Smooth scroll para paginación
        $('.pagination a').on('click', function(e) {
            e.preventDefault();
            const href = $(this).attr('href');
            
            $('html, body').animate({
                scrollTop: $('.properties-results').offset().top - 100
            }, 500, function() {
                window.location.href = href;
            });
        });
        
        // Mejorar UX en móviles
        if ($(window).width() < 768) {
            // Hacer que las tarjetas sean más táctiles
            $('.property-card').on('touchstart', function() {
                $(this).addClass('touch-active');
            }).on('touchend', function() {
                $(this).removeClass('touch-active');
            });
        }
    }
});
