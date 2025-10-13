<?php
/**
 * Home Isa Propiedades Theme Functions
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// ========================================
// CONFIGURACIÓN DE EMAIL SMTP
// ========================================

// Configurar SMTP para Contact Form 7
function mjpropiedades_configure_smtp($phpmailer) {
    // Configuración SMTP para homeisa.cl
    $phpmailer->isSMTP();
    $phpmailer->Host = 'vps-1765142.promecanicaservicios.com'; // Cambia por tu servidor SMTP
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = 465; // Puerto TLS (usa 465 para SSL)
    $phpmailer->Username = 'contacto@devkreativo.cl';
    $phpmailer->Password = 'dy0*Z6-SGy$h*h6M'; // Cambia por tu contraseña real
    $phpmailer->SMTPSecure = 'ssl'; // 'tls' o 'ssl'
    $phpmailer->From = 'contacto@devkreativo.cl';
    $phpmailer->FromName = 'Home Isa - Corredora de Propiedades';
    $phpmailer->CharSet = 'UTF-8';
    $phpmailer->isHTML(true);
}
add_action('phpmailer_init', 'mjpropiedades_configure_smtp');

// Configuración alternativa usando wp_mail
function mjpropiedades_configure_wp_mail() {
    // Configurar headers por defecto
    add_filter('wp_mail_from', function() {
        return 'contacto@devkreativo.cl';
    });
    
    add_filter('wp_mail_from_name', function() {
        return 'Home Isa - Corredora de Propiedades';
    });
    
    add_filter('wp_mail_content_type', function() {
        return 'text/html; charset=UTF-8';
    });
}
add_action('init', 'mjpropiedades_configure_wp_mail');

// ========================================
// CONFIGURACIÓN AUTOMÁTICA DE CONTACT FORM 7
// ========================================

// Crear formulario de contacto automáticamente al activar el tema
function mjpropiedades_create_default_contact_form() {
    // Verificar si ya existe un formulario
    $existing_form = get_posts(array(
        'post_type' => 'wpcf7_contact_form',
        'posts_per_page' => 1,
        'post_status' => 'publish'
    ));
    
    if (empty($existing_form)) {
        // Crear el formulario
        $form_id = wp_insert_post(array(
            'post_type' => 'wpcf7_contact_form',
            'post_title' => 'Formulario de Contacto',
            'post_content' => mjpropiedades_get_contact_form_template(),
            'post_status' => 'publish'
        ));
        
        if ($form_id) {
            // Configurar el formulario
            mjpropiedades_configure_contact_form($form_id);
        }
    }
}
add_action('after_switch_theme', 'mjpropiedades_create_default_contact_form');

// Plantilla del formulario de contacto
function mjpropiedades_get_contact_form_template() {
    return '
<div class="form-row">
    <div class="form-group">
        <label for="nombre">Nombre completo</label>
        [text* nombre id:nombre class:form-input placeholder "Tu nombre completo"]
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        [email* email id:email class:form-input placeholder "tu@email.com"]
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="telefono">Teléfono</label>
        [tel telefono id:telefono class:form-input placeholder "Tu teléfono"]
    </div>
    <div class="form-group">
        <label for="tipo-consulta">Tipo de consulta</label>
        [select tipo-consulta id:tipo-consulta class:form-select include_blank "Seleccionar tipo" "Compra de propiedad" "Venta de propiedad" "Arriendo" "Tasación" "Consulta general"]
    </div>
</div>

<div class="form-group">
    <label for="mensaje">Mensaje</label>
    [textarea* mensaje id:mensaje class:form-textarea placeholder "Cuéntanos más sobre tu consulta..."]
</div>

<div class="form-submit">
    [submit class:submit-btn "ENVIAR CONSULTA"]
</div>
    ';
}

// Configurar el formulario de contacto
function mjpropiedades_configure_contact_form($form_id) {
    // Configurar mensajes
    update_post_meta($form_id, '_form', mjpropiedades_get_contact_form_template());
    update_post_meta($form_id, '_mail', array(
        'subject' => 'Nueva consulta desde homeisa.cl',
        'sender' => '[nombre] <[email]>',
        'body' => mjpropiedades_get_email_template(),
        'recipient' => 'contacto@devkreativo.cl',
        'additional_headers' => '',
        'attachments' => '',
        'use_html' => 1,
        'exclude_blank' => 0
    ));
    
    // Configurar mensaje de envío exitoso
    update_post_meta($form_id, '_mail_2', array(
        'active' => 1,
        'subject' => 'Confirmación de consulta recibida',
        'sender' => 'Home Isa <contacto@devkreativo.cl>',
        'body' => 'Hola [nombre],<br><br>Hemos recibido tu consulta y te contactaremos pronto.<br><br>Gracias por contactarnos,<br>Equipo Home Isa',
        'recipient' => '[email]',
        'additional_headers' => '',
        'attachments' => '',
        'use_html' => 1,
        'exclude_blank' => 0
    ));
    
    // Mensajes del formulario
    update_post_meta($form_id, '_messages', array(
        'mail_sent_ok' => '¡Gracias por tu consulta! Hemos recibido tu mensaje y te contactaremos pronto.',
        'mail_sent_ng' => 'Hubo un error al enviar tu mensaje. Por favor, inténtalo de nuevo.',
        'validation_error' => 'Uno o más campos tienen un error. Por favor, revísalos e inténtalo de nuevo.',
        'spam' => 'Tu mensaje fue considerado como spam. Por favor, inténtalo de nuevo.',
        'acceptance_missing' => 'Debes aceptar los términos y condiciones.',
        'quiz_answer_not_correct' => 'La respuesta del cuestionario es incorrecta.',
        'captcha_not_match' => 'Tu respuesta no es correcta.',
        'invalid_email' => 'La dirección de correo electrónico que ingresaste no es válida.',
        'invalid_required' => 'Este campo es obligatorio.',
        'invalid_too_long' => 'Este campo es demasiado largo.',
        'invalid_too_short' => 'Este campo es demasiado corto.'
    ));
}

// Plantilla del email
function mjpropiedades_get_email_template() {
    return '
<h2>Nueva Consulta desde homeisa.cl</h2>

<p><strong>Detalles de la consulta:</strong></p>
<ul>
    <li><strong>Nombre:</strong> [nombre]</li>
    <li><strong>Email:</strong> [email]</li>
    <li><strong>Teléfono:</strong> [telefono]</li>
    <li><strong>Tipo de consulta:</strong> [tipo-consulta]</li>
</ul>

<p><strong>Mensaje:</strong></p>
<p>[mensaje]</p>

<hr>
<p><em>Este mensaje fue enviado desde el formulario de contacto de homeisa.cl</em></p>
    ';
}

// Función para obtener el ID del formulario automáticamente
function mjpropiedades_get_contact_form_id() {
    $forms = get_posts(array(
        'post_type' => 'wpcf7_contact_form',
        'posts_per_page' => 1,
        'post_status' => 'publish'
    ));
    
    if (!empty($forms)) {
        return $forms[0]->ID;
    }
    
    return 1; // Fallback
}

// Hook para ejecutar la creación del formulario cuando se active Contact Form 7
add_action('wpcf7_init', function() {
    if (!get_option('mjpropiedades_contact_form_created')) {
        mjpropiedades_create_default_contact_form();
        update_option('mjpropiedades_contact_form_created', true);
    }
});

// Configuración del tema
function mjpropiedades_setup() {
    // Soporte para título dinámico
    add_theme_support('title-tag');
    
    // Soporte para imágenes destacadas
    add_theme_support('post-thumbnails');
    
    // Soporte para HTML5
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    
    // Soporte para logo personalizado
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    
    // Menús de navegación
    register_nav_menus(array(
        'primary' => __('Menú Principal', 'mjpropiedades'),
        'footer'  => __('Menú Footer', 'mjpropiedades'),
    ));
    
    // Registrar plantillas de página
    add_filter('page_template', 'mjpropiedades_page_template');
}
add_action('after_setup_theme', 'mjpropiedades_setup');

// Función helper para obtener URLs dinámicas del menú
function mjpropiedades_get_dynamic_menu_url($link_text, $url) {
    // Detectar si estamos en la página de inicio
    $is_homepage = is_front_page() || is_page_template('page-inicio.php');
    
    // Modificar URLs específicas
    if (strpos($url, '#servicios') !== false) {
        return $is_homepage ? '#servicios' : home_url('/inicio/#servicios');
    }
    
    if (strpos($url, '#propiedades') !== false || 
        (strpos($link_text, 'Propiedades') !== false && strpos($url, '#') !== false)) {
        // Siempre usar el archive de propiedades (unificado)
        return $is_homepage ? '#venta' : get_post_type_archive_link('propiedad');
    }
    
    return $url;
}

// Filtro para modificar los enlaces del menú principal
add_filter('nav_menu_link_attributes', 'mjpropiedades_modify_menu_links', 10, 3);
function mjpropiedades_modify_menu_links($atts, $item, $args) {
    if (isset($atts['href'])) {
        $atts['href'] = mjpropiedades_get_dynamic_menu_url($item->title, $atts['href']);
    }
    return $atts;
}


// Registrar post type para consultas de contacto
function mjpropiedades_register_contact_inquiry_post_type() {
    register_post_type('contact_inquiry', array(
        'labels' => array(
            'name' => 'Consultas de Contacto',
            'singular_name' => 'Consulta de Contacto',
            'add_new' => 'Agregar Nueva',
            'add_new_item' => 'Agregar Nueva Consulta',
            'edit_item' => 'Editar Consulta',
            'new_item' => 'Nueva Consulta',
            'view_item' => 'Ver Consulta',
            'search_items' => 'Buscar Consultas',
            'not_found' => 'No se encontraron consultas',
            'not_found_in_trash' => 'No se encontraron consultas en la papelera'
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 25,
        'menu_icon' => 'dashicons-email-alt',
        'supports' => array('title', 'editor', 'custom-fields'),
        'capability_type' => 'post',
        'capabilities' => array(
            'create_posts' => 'manage_options',
            'edit_posts' => 'manage_options',
            'edit_others_posts' => 'manage_options',
            'publish_posts' => 'manage_options',
            'read_private_posts' => 'manage_options',
            'delete_posts' => 'manage_options'
        )
    ));
}
add_action('init', 'mjpropiedades_register_contact_inquiry_post_type');

// Función para procesar el formulario de contacto
function mjpropiedades_handle_contact_form() {
    if (isset($_POST['contact_form_submitted']) && wp_verify_nonce($_POST['contact_nonce'], 'contact_form_nonce')) {
        
        // Sanitizar datos del formulario
        $nombre = sanitize_text_field($_POST['nombre'] ?? '');
        $telefono = sanitize_text_field($_POST['telefono'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $tipo_consulta = sanitize_text_field($_POST['tipo_consulta'] ?? '');
        $tipo_propiedad = sanitize_text_field($_POST['tipo_propiedad'] ?? '');
        $comuna = sanitize_text_field($_POST['comuna'] ?? '');
        $mensaje = sanitize_textarea_field($_POST['mensaje'] ?? '');
        
        // Validar campos requeridos
        if (empty($nombre) || empty($telefono) || empty($email)) {
            wp_redirect(add_query_arg('contact', 'error', home_url()));
            exit;
        }
        
        // Validar email
        if (!is_email($email)) {
            wp_redirect(add_query_arg('contact', 'error', home_url()));
            exit;
        }
        
        // Configurar el email
        $to = get_option('admin_email');
        $subject = 'Nueva consulta desde ' . get_bloginfo('name');
        
        // Crear el mensaje en texto plano (más compatible)
        $message_text = "Nueva Consulta de Contacto\n\n";
        $message_text .= "Nombre: " . $nombre . "\n";
        $message_text .= "Teléfono: " . $telefono . "\n";
        $message_text .= "Email: " . $email . "\n";
        $message_text .= "Tipo de Consulta: " . $tipo_consulta . "\n";
        $message_text .= "Tipo de Propiedad: " . $tipo_propiedad . "\n";
        $message_text .= "Comuna: " . $comuna . "\n";
        $message_text .= "Mensaje: " . $mensaje . "\n\n";
        $message_text .= "Enviado desde: " . get_bloginfo('name') . " - " . home_url();
        
        // Headers simples
        $headers = array(
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
        );
        
        // Intentar enviar el email
        $sent = wp_mail($to, $subject, $message_text, $headers);
        
        // Si falla el email, guardar en base de datos como respaldo
        if (!$sent) {
            // Crear un post personalizado para guardar la consulta
            $post_data = array(
                'post_title'   => 'Consulta de: ' . $nombre . ' - ' . date('Y-m-d H:i:s'),
                'post_content' => $message_text,
                'post_status'  => 'private',
                'post_type'    => 'contact_inquiry',
                'meta_input'   => array(
                    'contact_nombre' => $nombre,
                    'contact_email' => $email,
                    'contact_telefono' => $telefono,
                    'contact_tipo_consulta' => $tipo_consulta,
                    'contact_tipo_propiedad' => $tipo_propiedad,
                    'contact_comuna' => $comuna,
                    'contact_mensaje' => $mensaje,
                    'contact_fecha' => current_time('mysql')
                )
            );
            
            $post_id = wp_insert_post($post_data);
            
            if ($post_id) {
                error_log('Consulta guardada en base de datos con ID: ' . $post_id);
            }
        }
        
        // Siempre redirigir a éxito
        wp_redirect(add_query_arg('contact', 'success', home_url()));
        exit;
    }
}
add_action('init', 'mjpropiedades_handle_contact_form');

// Cargar estilos y scripts
function mjpropiedades_scripts() {
    // Cargar estilos
    wp_enqueue_style('mjpropiedades-style', get_stylesheet_uri(), array(), time());
    
    // Cargar Google Fonts
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Dancing+Script:wght@400;700&display=swap', array(), null);
    
    // Cargar SwiperJS CSS
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.0');
    
    // Cargar SwiperJS JavaScript
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.0', true);
    
    // Cargar JavaScript personalizado
    wp_enqueue_script('mjpropiedades-script', get_template_directory_uri() . '/js/main.js', array('jquery', 'swiper-js'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'mjpropiedades_scripts');

// Registrar áreas de widgets
function mjpropiedades_widgets_init() {
    register_sidebar(array(
        'name'          => __('Sidebar Principal', 'mjpropiedades'),
        'id'            => 'sidebar-1',
        'description'   => __('Widgets que aparecerán en la sidebar.', 'mjpropiedades'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer Widgets', 'mjpropiedades'),
        'id'            => 'footer-widgets',
        'description'   => __('Widgets que aparecerán en el footer.', 'mjpropiedades'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget-title">',
        'after_title'   => '</h4>',
    ));
}
add_action('widgets_init', 'mjpropiedades_widgets_init');

// Personalizar el excerpt
function mjpropiedades_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'mjpropiedades_excerpt_length');

// Personalizar el "leer más"
function mjpropiedades_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'mjpropiedades_excerpt_more');

// Agregar clases CSS al body
function mjpropiedades_body_classes($classes) {
    if (is_front_page()) {
        $classes[] = 'home-page';
    }
    return $classes;
}
add_filter('body_class', 'mjpropiedades_body_classes');


// Mostrar mensaje de confirmación
function mjpropiedades_show_contact_message() {
    if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'enviado') {
        echo '<div class="contact-success" style="background: #d4edda; color: #155724; padding: 15px; margin: 20px 0; border-radius: 5px; text-align: center;">¡Mensaje enviado correctamente! Te contactaremos pronto.</div>';
    }
}

// Crear Custom Post Type para Propiedades
function mjpropiedades_create_property_post_type() {
    $labels = array(
        'name'                  => 'Propiedades',
        'singular_name'         => 'Propiedad',
        'menu_name'             => 'Propiedades',
        'add_new'               => 'Agregar Nueva',
        'add_new_item'          => 'Agregar Nueva Propiedad',
        'edit_item'             => 'Editar Propiedad',
        'new_item'              => 'Nueva Propiedad',
        'view_item'             => 'Ver Propiedad',
        'search_items'          => 'Buscar Propiedades',
        'not_found'             => 'No se encontraron propiedades',
        'not_found_in_trash'    => 'No se encontraron propiedades en la papelera',
    );
    
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'propiedades'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-admin-home',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
    );
    
    register_post_type('propiedad', $args);
}
add_action('init', 'mjpropiedades_create_property_post_type');

// ===== META BOXES PARA DETALLE DE PROPIEDADES =====

// Agregar meta boxes para propiedades
function mjpropiedades_add_property_meta_boxes() {
    add_meta_box(
        'property_basic_info',
        'Información Básica',
        'mjpropiedades_property_basic_info_callback',
        'propiedad',
        'normal',
        'high'
    );
    
    add_meta_box(
        'property_characteristics',
        'Características Destacadas',
        'mjpropiedades_property_characteristics_callback',
        'propiedad',
        'normal',
        'high'
    );
    
    add_meta_box(
        'property_location',
        'Ubicación y Mapa',
        'mjpropiedades_property_location_callback',
        'propiedad',
        'normal',
        'high'
    );
    
    add_meta_box(
        'property_additional_info',
        'Información Adicional',
        'mjpropiedades_property_additional_info_callback',
        'propiedad',
        'normal',
        'high'
    );
    
    add_meta_box(
        'property_gallery',
        'Galería de Imágenes',
        'mjpropiedades_property_gallery_callback',
        'propiedad',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'mjpropiedades_add_property_meta_boxes');

// Agregar meta box para propiedades destacadas
function mjpropiedades_add_featured_property_meta_box() {
    add_meta_box(
        'featured_property',
        'Propiedad Destacada',
        'mjpropiedades_featured_property_callback',
        'propiedad',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'mjpropiedades_add_featured_property_meta_box');

// Callback para propiedad destacada
function mjpropiedades_featured_property_callback($post) {
    wp_nonce_field('mjpropiedades_featured_property_nonce', 'featured_property_nonce');
    
    $featured = get_post_meta($post->ID, '_featured_property', true);
    $featured_checked = $featured ? 'checked' : '';
    
    echo '<p><label for="featured_property">';
    echo '<input type="checkbox" id="featured_property" name="featured_property" value="1" ' . $featured_checked . '> ';
    echo '⭐ Marcar como Propiedad Destacada';
    echo '</label></p>';
    echo '<p class="description">Las propiedades destacadas aparecerán en la página principal.</p>';
}

// Callback para información básica
function mjpropiedades_property_basic_info_callback($post) {
    wp_nonce_field('mjpropiedades_property_meta_nonce', 'property_meta_nonce');
    
    $precio = get_post_meta($post->ID, '_propiedad_precio', true);
    $operacion = get_post_meta($post->ID, '_propiedad_operacion', true);
    $dormitorios = get_post_meta($post->ID, '_propiedad_dormitorios', true);
    $banos = get_post_meta($post->ID, '_propiedad_banos', true);
    $metros = get_post_meta($post->ID, '_propiedad_metros', true);
    $comuna = get_post_meta($post->ID, '_propiedad_comuna', true);
    $tipo = get_post_meta($post->ID, '_propiedad_tipo', true);
    $direccion = get_post_meta($post->ID, '_propiedad_direccion', true);
    ?>
    
    <table class="form-table">
        <tr>
            <th><label for="propiedad_precio">Precio</label></th>
            <td><input type="number" id="propiedad_precio" name="propiedad_precio" value="<?php echo esc_attr($precio); ?>" placeholder="450000000" /></td>
        </tr>
        <tr>
            <th><label for="propiedad_operacion">Operación</label></th>
            <td>
                <select id="propiedad_operacion" name="propiedad_operacion">
                    <option value="venta" <?php selected($operacion, 'venta'); ?>>Venta</option>
                    <option value="arriendo" <?php selected($operacion, 'arriendo'); ?>>Arriendo</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="propiedad_dormitorios">Dormitorios</label></th>
            <td><input type="number" id="propiedad_dormitorios" name="propiedad_dormitorios" value="<?php echo esc_attr($dormitorios); ?>" min="0" max="20" /></td>
        </tr>
        <tr>
            <th><label for="propiedad_banos">Baños</label></th>
            <td><input type="number" id="propiedad_banos" name="propiedad_banos" value="<?php echo esc_attr($banos); ?>" min="0" max="20" /></td>
        </tr>
        <tr>
            <th><label for="propiedad_metros">Metros Construidos</label></th>
            <td><input type="number" id="propiedad_metros" name="propiedad_metros" value="<?php echo esc_attr($metros); ?>" min="0" /></td>
        </tr>
        <tr>
            <th><label for="propiedad_comuna">Comuna</label></th>
            <td><input type="text" id="propiedad_comuna" name="propiedad_comuna" value="<?php echo esc_attr($comuna); ?>" placeholder="Las Condes" /></td>
        </tr>
        <tr>
            <th><label for="propiedad_tipo">Tipo de Propiedad</label></th>
            <td>
                <select id="propiedad_tipo" name="propiedad_tipo">
                    <option value="casa" <?php selected($tipo, 'casa'); ?>>Casa</option>
                    <option value="departamento" <?php selected($tipo, 'departamento'); ?>>Departamento</option>
                    <option value="oficina" <?php selected($tipo, 'oficina'); ?>>Oficina</option>
                    <option value="local" <?php selected($tipo, 'local'); ?>>Local Comercial</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="propiedad_direccion">Dirección Completa</label></th>
            <td><input type="text" id="propiedad_direccion" name="propiedad_direccion" value="<?php echo esc_attr($direccion); ?>" placeholder="Av. Apoquindo 4567, Las Condes, Santiago" style="width: 100%;" /></td>
        </tr>
    </table>
    
    <?php
}

// Callback para características destacadas
function mjpropiedades_property_characteristics_callback($post) {
    $caracteristicas = get_post_meta($post->ID, '_propiedad_caracteristicas', true);
    if (empty($caracteristicas)) {
        $caracteristicas = array(
            'Cocina equipada',
            'Jardín privado',
            'Terraza techada',
            'Calefacción central',
            'Closets empotrados',
            'Portón automático'
        );
    } else {
        $caracteristicas = explode("\n", $caracteristicas);
    }
    ?>
    
    <p><strong>Características Destacadas:</strong> (Una por línea)</p>
    <textarea id="propiedad_caracteristicas" name="propiedad_caracteristicas" rows="10" style="width: 100%;"><?php echo esc_textarea(implode("\n", $caracteristicas)); ?></textarea>
    <p class="description">Escribe cada característica en una línea separada. Ejemplo:<br>
    Cocina equipada<br>
    Jardín privado<br>
    Terraza techada</p>
    
    <?php
}

// Callback para ubicación y mapa
function mjpropiedades_property_location_callback($post) {
    $latitud = get_post_meta($post->ID, '_propiedad_latitud', true);
    $longitud = get_post_meta($post->ID, '_propiedad_longitud', true);
    $lugares_cercanos = get_post_meta($post->ID, '_propiedad_lugares_cercanos', true);
    
    if (empty($lugares_cercanos)) {
        $lugares_cercanos = array(
            array('nombre' => 'Mall Parque Arauco', 'distancia' => '5 min caminando', 'tipo' => 'shopping'),
            array('nombre' => 'Metro Escuela Militar', 'distancia' => '8 min caminando', 'tipo' => 'transporte'),
            array('nombre' => 'Clínica Las Condes', 'distancia' => '10 min en auto', 'tipo' => 'salud')
        );
    } else {
        $lugares_cercanos = json_decode($lugares_cercanos, true);
    }
    ?>
    
    <table class="form-table">
        <tr>
            <th><label for="propiedad_latitud">Latitud</label></th>
            <td><input type="text" id="propiedad_latitud" name="propiedad_latitud" value="<?php echo esc_attr($latitud); ?>" placeholder="-33.4172" /></td>
        </tr>
        <tr>
            <th><label for="propiedad_longitud">Longitud</label></th>
            <td><input type="text" id="propiedad_longitud" name="propiedad_longitud" value="<?php echo esc_attr($longitud); ?>" placeholder="-70.5506" /></td>
        </tr>
    </table>
    
    <h4>Lugares Cercanos</h4>
    <div id="lugares-cercanos">
        <?php foreach ($lugares_cercanos as $index => $lugar) : ?>
            <div class="lugar-item" style="border: 1px solid #ddd; padding: 15px; margin: 10px 0; background: #f9f9f9;">
                <table class="form-table">
                    <tr>
                        <th>Nombre del Lugar</th>
                        <td><input type="text" name="lugar_nombre[]" value="<?php echo esc_attr($lugar['nombre']); ?>" style="width: 100%;" /></td>
                    </tr>
                    <tr>
                        <th>Distancia</th>
                        <td><input type="text" name="lugar_distancia[]" value="<?php echo esc_attr($lugar['distancia']); ?>" style="width: 100%;" /></td>
                    </tr>
                    <tr>
                        <th>Tipo de Icono</th>
                        <td>
                            <select name="lugar_tipo[]">
                                <option value="shopping" <?php selected($lugar['tipo'], 'shopping'); ?>>🛒 Compras</option>
                                <option value="transporte" <?php selected($lugar['tipo'], 'transporte'); ?>>🚇 Transporte</option>
                                <option value="salud" <?php selected($lugar['tipo'], 'salud'); ?>>🏥 Salud</option>
                                <option value="educacion" <?php selected($lugar['tipo'], 'educacion'); ?>>🎓 Educación</option>
                                <option value="recreacion" <?php selected($lugar['tipo'], 'recreacion'); ?>>🎯 Recreación</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <button type="button" class="button remove-lugar" style="color: #dc3545;">Eliminar Lugar</button>
            </div>
        <?php endforeach; ?>
    </div>
    
    <button type="button" id="add-lugar" class="button">+ Agregar Lugar Cercano</button>
    
    <script>
    jQuery(document).ready(function($) {
        $('#add-lugar').click(function() {
            var lugarHtml = '<div class="lugar-item" style="border: 1px solid #ddd; padding: 15px; margin: 10px 0; background: #f9f9f9;">' +
                '<table class="form-table">' +
                '<tr><th>Nombre del Lugar</th><td><input type="text" name="lugar_nombre[]" style="width: 100%;" /></td></tr>' +
                '<tr><th>Distancia</th><td><input type="text" name="lugar_distancia[]" style="width: 100%;" /></td></tr>' +
                '<tr><th>Tipo de Icono</th><td><select name="lugar_tipo[]"><option value="shopping">🛒 Compras</option><option value="transporte">🚇 Transporte</option><option value="salud">🏥 Salud</option><option value="educacion">🎓 Educación</option><option value="recreacion">🎯 Recreación</option></select></td></tr>' +
                '</table>' +
                '<button type="button" class="button remove-lugar" style="color: #dc3545;">Eliminar Lugar</button>' +
                '</div>';
            $('#lugares-cercanos').append(lugarHtml);
        });
        
        $(document).on('click', '.remove-lugar', function() {
            $(this).closest('.lugar-item').remove();
        });
    });
    </script>
    
    <?php
}

// Callback para información adicional
function mjpropiedades_property_additional_info_callback($post) {
    $ano_construccion = get_post_meta($post->ID, '_propiedad_ano_construccion', true);
    $orientacion = get_post_meta($post->ID, '_propiedad_orientacion', true);
    $gastos_comunes = get_post_meta($post->ID, '_propiedad_gastos_comunes', true);
    $estado = get_post_meta($post->ID, '_propiedad_estado', true);
    $disponibilidad = get_post_meta($post->ID, '_propiedad_disponibilidad', true);
    $estacionamientos = get_post_meta($post->ID, '_propiedad_estacionamientos', true);
    ?>
    
    <table class="form-table">
        <tr>
            <th><label for="propiedad_ano_construccion">Año de Construcción</label></th>
            <td><input type="number" id="propiedad_ano_construccion" name="propiedad_ano_construccion" value="<?php echo esc_attr($ano_construccion); ?>" min="1900" max="2030" /></td>
        </tr>
        <tr>
            <th><label for="propiedad_orientacion">Orientación</label></th>
            <td>
                <select id="propiedad_orientacion" name="propiedad_orientacion">
                    <option value="Norte" <?php selected($orientacion, 'Norte'); ?>>Norte</option>
                    <option value="Sur" <?php selected($orientacion, 'Sur'); ?>>Sur</option>
                    <option value="Este" <?php selected($orientacion, 'Este'); ?>>Este</option>
                    <option value="Oeste" <?php selected($orientacion, 'Oeste'); ?>>Oeste</option>
                    <option value="Noreste" <?php selected($orientacion, 'Noreste'); ?>>Noreste</option>
                    <option value="Noroeste" <?php selected($orientacion, 'Noroeste'); ?>>Noroeste</option>
                    <option value="Sureste" <?php selected($orientacion, 'Sureste'); ?>>Sureste</option>
                    <option value="Suroeste" <?php selected($orientacion, 'Suroeste'); ?>>Suroeste</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="propiedad_gastos_comunes">Gastos Comunes</label></th>
            <td><input type="number" id="propiedad_gastos_comunes" name="propiedad_gastos_comunes" value="<?php echo esc_attr($gastos_comunes); ?>" min="0" placeholder="85000" /></td>
        </tr>
        <tr>
            <th><label for="propiedad_estado">Estado</label></th>
            <td>
                <select id="propiedad_estado" name="propiedad_estado">
                    <option value="Excelente" <?php selected($estado, 'Excelente'); ?>>Excelente</option>
                    <option value="Muy Bueno" <?php selected($estado, 'Muy Bueno'); ?>>Muy Bueno</option>
                    <option value="Bueno" <?php selected($estado, 'Bueno'); ?>>Bueno</option>
                    <option value="Regular" <?php selected($estado, 'Regular'); ?>>Regular</option>
                    <option value="A Renovar" <?php selected($estado, 'A Renovar'); ?>>A Renovar</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="propiedad_disponibilidad">Disponibilidad</label></th>
            <td>
                <select id="propiedad_disponibilidad" name="propiedad_disponibilidad">
                    <option value="Inmediata" <?php selected($disponibilidad, 'Inmediata'); ?>>Inmediata</option>
                    <option value="30 días" <?php selected($disponibilidad, '30 días'); ?>>30 días</option>
                    <option value="60 días" <?php selected($disponibilidad, '60 días'); ?>>60 días</option>
                    <option value="90 días" <?php selected($disponibilidad, '90 días'); ?>>90 días</option>
                    <option value="Consultar" <?php selected($disponibilidad, 'Consultar'); ?>>Consultar</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="propiedad_estacionamientos">Estacionamientos</label></th>
            <td><input type="number" id="propiedad_estacionamientos" name="propiedad_estacionamientos" value="<?php echo esc_attr($estacionamientos); ?>" min="0" max="10" /></td>
        </tr>
    </table>
    
    <?php
}

// Callback para galería de imágenes
function mjpropiedades_property_gallery_callback($post) {
    $gallery_images = get_post_meta($post->ID, '_propiedad_gallery', true);
    ?>
    
    <div id="gallery-container">
        <p><strong>Galería de Imágenes:</strong></p>
        <p class="description">Selecciona múltiples imágenes para crear una galería. La primera imagen será la imagen principal.</p>
        
        <button type="button" id="upload-gallery-button" class="button button-primary">Seleccionar Imágenes</button>
        <button type="button" id="clear-gallery-button" class="button">Limpiar Galería</button>
        
        <div id="gallery-preview" style="margin-top: 20px;">
            <?php
            if ($gallery_images) {
                $image_ids = explode(',', $gallery_images);
                foreach ($image_ids as $image_id) {
                    $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
                    if ($image_url) {
                        echo '<div class="gallery-item" data-id="' . $image_id . '" style="display: inline-block; position: relative; margin: 5px;">';
                        echo '<img src="' . esc_url($image_url) . '" style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #ddd; border-radius: 5px;">';
                        echo '<button type="button" class="remove-gallery-item" style="position: absolute; top: -5px; right: -5px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer;">×</button>';
                        echo '</div>';
                    }
                }
            }
            ?>
        </div>
        
        <input type="hidden" id="propiedad_gallery" name="propiedad_gallery" value="<?php echo esc_attr($gallery_images); ?>" />
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        var gallery_frame;
        
        $('#upload-gallery-button').click(function(e) {
            e.preventDefault();
            
            if (gallery_frame) {
                gallery_frame.open();
        return;
    }
    
            gallery_frame = wp.media({
                title: 'Seleccionar Imágenes para Galería',
                button: {
                    text: 'Usar estas imágenes'
                },
                multiple: true
            });
            
            gallery_frame.on('select', function() {
                var selection = gallery_frame.state().get('selection');
                var current_gallery = $('#propiedad_gallery').val();
                var image_ids = current_gallery ? current_gallery.split(',') : [];
                
                selection.map(function(attachment) {
                    image_ids.push(attachment.id);
                });
                
                $('#propiedad_gallery').val(image_ids.join(','));
                updateGalleryPreview();
            });
            
            gallery_frame.open();
        });
        
        $('#clear-gallery-button').click(function() {
            $('#propiedad_gallery').val('');
            $('#gallery-preview').empty();
        });
        
        $(document).on('click', '.remove-gallery-item', function() {
            var item = $(this).closest('.gallery-item');
            var image_id = item.data('id');
            var current_gallery = $('#propiedad_gallery').val();
            var image_ids = current_gallery ? current_gallery.split(',') : [];
            
            image_ids = image_ids.filter(function(id) {
                return id != image_id;
            });
            
            $('#propiedad_gallery').val(image_ids.join(','));
            item.remove();
        });
        
        function updateGalleryPreview() {
            var gallery_ids = $('#propiedad_gallery').val();
            if (!gallery_ids) return;
            
            $('#gallery-preview').empty();
            var image_ids = gallery_ids.split(',');
            
            image_ids.forEach(function(image_id) {
                if (image_id) {
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'get_attachment_url',
                            attachment_id: image_id
                        },
                        success: function(response) {
                            if (response.success) {
                                var html = '<div class="gallery-item" data-id="' + image_id + '" style="display: inline-block; position: relative; margin: 5px;">';
                                html += '<img src="' + response.data + '" style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #ddd; border-radius: 5px;">';
                                html += '<button type="button" class="remove-gallery-item" style="position: absolute; top: -5px; right: -5px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer;">×</button>';
                                html += '</div>';
                                $('#gallery-preview').append(html);
                            }
                        }
                    });
                }
            });
        }
    });
    </script>
    
    <?php
}

// Función AJAX para obtener URL de imagen
function mjpropiedades_get_attachment_url() {
    $attachment_id = intval($_POST['attachment_id']);
    $url = wp_get_attachment_image_url($attachment_id, 'thumbnail');
    
    if ($url) {
        wp_send_json_success($url);
    } else {
        wp_send_json_error('No se pudo obtener la URL de la imagen');
    }
}
add_action('wp_ajax_get_attachment_url', 'mjpropiedades_get_attachment_url');

// Guardar meta boxes
function mjpropiedades_save_property_meta($post_id) {
    // Verificar nonce
    if (!isset($_POST['property_meta_nonce']) || !wp_verify_nonce($_POST['property_meta_nonce'], 'mjpropiedades_property_meta_nonce')) {
        return;
    }
    
    // Verificar permisos
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Guardar campos básicos
    $fields = array(
        'propiedad_precio',
        'propiedad_operacion',
        'propiedad_dormitorios',
        'propiedad_banos',
        'propiedad_metros',
        'propiedad_comuna',
        'propiedad_tipo',
        'propiedad_direccion',
        'propiedad_ano_construccion',
        'propiedad_orientacion',
        'propiedad_gastos_comunes',
        'propiedad_estado',
        'propiedad_disponibilidad',
        'propiedad_estacionamientos',
        'propiedad_latitud',
        'propiedad_longitud',
        'propiedad_caracteristicas',
        'propiedad_gallery'
    );
    
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $value = sanitize_text_field($_POST[$field]);
            update_post_meta($post_id, '_' . $field, $value);
        }
    }
    
    // Guardar lugares cercanos
    if (isset($_POST['lugar_nombre']) && isset($_POST['lugar_distancia']) && isset($_POST['lugar_tipo'])) {
        $lugares = array();
        $nombres = $_POST['lugar_nombre'];
        $distancias = $_POST['lugar_distancia'];
        $tipos = $_POST['lugar_tipo'];
        
        for ($i = 0; $i < count($nombres); $i++) {
            if (!empty($nombres[$i]) && !empty($distancias[$i])) {
                $lugares[] = array(
                    'nombre' => sanitize_text_field($nombres[$i]),
                    'distancia' => sanitize_text_field($distancias[$i]),
                    'tipo' => sanitize_text_field($tipos[$i])
                );
            }
        }
        
        update_post_meta($post_id, '_propiedad_lugares_cercanos', json_encode($lugares));
    }
}
add_action('save_post', 'mjpropiedades_save_property_meta');

// Guardar campo destacada
function mjpropiedades_save_featured_property($post_id) {
    // Verificar nonce
    if (!isset($_POST['featured_property_nonce']) || !wp_verify_nonce($_POST['featured_property_nonce'], 'mjpropiedades_featured_property_nonce')) {
        return;
    }
    
    // Verificar permisos
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Solo para propiedades
    if (get_post_type($post_id) !== 'propiedad') {
        return;
    }
    
    // Guardar campo destacada
    if (isset($_POST['featured_property'])) {
        update_post_meta($post_id, '_featured_property', '1');
    } else {
        delete_post_meta($post_id, '_featured_property');
    }
}
add_action('save_post', 'mjpropiedades_save_featured_property');

// Función para obtener propiedades destacadas
function mjpropiedades_get_featured_properties($limit = 6) {
    $args = array(
        'post_type' => 'propiedad',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_featured_property',
                'value' => '1',
                'compare' => '='
            )
        ),
        'orderby' => 'date',
        'order' => 'DESC'
    );
    
    return get_posts($args);
}

// Función para obtener propiedades destacadas por operación
function mjpropiedades_get_featured_properties_by_operation($operacion = '', $limit = 6) {
    $meta_query = array(
        'relation' => 'AND',
        array(
            'key' => '_featured_property',
            'value' => '1',
            'compare' => '='
        )
    );
    
    // Si se especifica operación, agregar filtro
    if (!empty($operacion)) {
        $meta_query[] = array(
            'key' => '_propiedad_operacion',
            'value' => $operacion,
            'compare' => '='
        );
    }
    
    $args = array(
        'post_type' => 'propiedad',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'meta_query' => $meta_query,
        'orderby' => 'date',
        'order' => 'DESC'
    );
    
    return get_posts($args);
}

// Función para obtener propiedades
function mjpropiedades_get_properties($operacion = '', $limit = -1) {
    $args = array(
        'post_type' => 'propiedad',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
    );
    
    if (!empty($operacion)) {
        $args['meta_query'] = array(
            array(
                'key' => '_propiedad_operacion',
                'value' => $operacion,
                'compare' => '='
            )
        );
    }
    
    return new WP_Query($args);
}

// Función para mostrar propiedades en la página principal
function mjpropiedades_display_properties($operacion = 'venta', $limit = 3) {
    // Si no hay propiedades con la operación específica, mostrar todas las propiedades
    $properties = mjpropiedades_get_properties($operacion, $limit);
    
    // Si no hay propiedades con operación específica, mostrar todas
    if (!$properties->have_posts()) {
        $properties = mjpropiedades_get_properties('', $limit);
    }
    
    if ($properties->have_posts()) {
        echo '<div class="properties-grid">';
        
        while ($properties->have_posts()) {
            $properties->the_post();
            
            $precio = get_post_meta(get_the_ID(), '_propiedad_precio', true);
            $dormitorios = get_post_meta(get_the_ID(), '_propiedad_dormitorios', true);
            $banos = get_post_meta(get_the_ID(), '_propiedad_banos', true);
            $metros = get_post_meta(get_the_ID(), '_propiedad_metros', true);
            $comuna = get_post_meta(get_the_ID(), '_propiedad_comuna', true);
            $operacion_real = get_post_meta(get_the_ID(), '_propiedad_operacion', true);
            
            // Si no hay operación configurada, determinar por precio (si es menor a 1 millón, es arriendo)
            if (!$operacion_real) {
                $operacion_real = ($precio && $precio < 1000000) ? 'arriendo' : 'venta';
            }
            
            $tag_class = ($operacion_real === 'arriendo') ? 'style="background: var(--orange);"' : '';
            $tag_text = ucfirst($operacion_real);
            $precio_text = $precio ? '$' . number_format(floatval($precio), 0, ',', '.') : 'Consultar';
            if ($operacion_real === 'arriendo') {
                $precio_text .= '/mes';
            }
            
            echo '<div class="property-card">';
            echo '<div class="property-image">';
            echo '<div class="property-tag" ' . $tag_class . '>' . $tag_text . '</div>';
            
            if (has_post_thumbnail()) {
                echo '<a href="' . get_permalink() . '">';
                the_post_thumbnail('medium');
                echo '</a>';
            } else {
                echo '<a href="' . get_permalink() . '">';
                echo '<img src="' . get_template_directory_uri() . '/images/propiedades/placeholder.jpg" alt="' . get_the_title() . '" onerror="this.src=\'data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 400 250\'><rect fill=\'%23f0f0f0\' width=\'400\' height=\'250\'/><text x=\'50%\' y=\'50%\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\'>' . get_the_title() . '</text></svg>\'">';
                echo '</a>';
            }
            
            echo '</div>';
            echo '<div class="property-content">';
            echo '<h3 class="property-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
            
            if ($comuna) {
                echo '<p class="property-location">📍 ' . $comuna . '</p>';
            }
            
            echo '<p class="property-details">';
            if ($dormitorios) echo $dormitorios . ' dormitorios';
            if ($banos) echo ' • ' . $banos . ' baños';
            if ($metros) echo ' • ' . $metros . ' m²';
            echo '</p>';
            
            echo '<div class="property-price">' . $precio_text . '</div>';
            echo '<a href="' . get_permalink() . '" class="property-btn">Ver Detalles</a>';
            echo '</div>';
            echo '</div>';
        }
        
        echo '</div>';
        wp_reset_postdata();
    } else {
        echo '<p>No hay propiedades disponibles en este momento.</p>';
    }
}

// Agregar soporte para Customizer
function mjpropiedades_customize_register($wp_customize) {
    // ===== CONFIGURACIÓN DEL LOGO EN IDENTIDAD DEL SITIO =====
    
    // Altura máxima del logo en desktop
    $wp_customize->add_setting('mjpropiedades_logo_height_desktop', array(
        'default'           => '50',
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('mjpropiedades_logo_height_desktop', array(
        'label'       => __('Altura del Logo en Desktop (px)', 'mjpropiedades'),
        'section'     => 'title_tagline',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 20,
            'max'  => 150,
            'step' => 5,
        ),
        'description' => __('Controla la altura máxima del logo en pantallas de escritorio (20-150px)', 'mjpropiedades'),
        'priority'    => 8,
    ));
    
    // Altura máxima del logo en tablet
    $wp_customize->add_setting('mjpropiedades_logo_height_tablet', array(
        'default'           => '45',
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('mjpropiedades_logo_height_tablet', array(
        'label'       => __('Altura del Logo en Tablet (px)', 'mjpropiedades'),
        'section'     => 'title_tagline',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 15,
            'max'  => 120,
            'step' => 5,
        ),
        'description' => __('Controla la altura máxima del logo en tablets (15-120px)', 'mjpropiedades'),
        'priority'    => 9,
    ));
    
    // Altura máxima del logo en móvil
    $wp_customize->add_setting('mjpropiedades_logo_height_mobile', array(
        'default'           => '40',
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('mjpropiedades_logo_height_mobile', array(
        'label'       => __('Altura del Logo en Móvil (px)', 'mjpropiedades'),
        'section'     => 'title_tagline',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 15,
            'max'  => 80,
            'step' => 5,
        ),
        'description' => __('Controla la altura máxima del logo en dispositivos móviles (15-80px)', 'mjpropiedades'),
        'priority'    => 10,
    ));
    
    // Ancho máximo del logo
    $wp_customize->add_setting('mjpropiedades_logo_max_width', array(
        'default'           => '200',
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('mjpropiedades_logo_max_width', array(
        'label'       => __('Ancho Máximo del Logo (px)', 'mjpropiedades'),
        'section'     => 'title_tagline',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 50,
            'max'  => 400,
            'step' => 10,
        ),
        'description' => __('Controla el ancho máximo del logo en todas las pantallas (50-400px)', 'mjpropiedades'),
        'priority'    => 11,
    ));
    
    // Presets de tamaño de logo
    $wp_customize->add_setting('mjpropiedades_logo_size_preset', array(
        'default'           => 'medium',
        'sanitize_callback' => 'mjpropiedades_sanitize_logo_size_preset',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('mjpropiedades_logo_size_preset', array(
        'label'       => __('Presets de Tamaño de Logo', 'mjpropiedades'),
        'section'     => 'title_tagline',
        'type'        => 'select',
        'choices'     => array(
            'small'  => __('Pequeño (30px)', 'mjpropiedades'),
            'medium' => __('Mediano (50px)', 'mjpropiedades'),
            'large'  => __('Grande (70px)', 'mjpropiedades'),
            'custom' => __('Personalizado', 'mjpropiedades'),
        ),
        'description' => __('Selecciona un tamaño predefinido o personaliza manualmente', 'mjpropiedades'),
        'priority'    => 12,
    ));
    
    // Separador visual
    $wp_customize->add_setting('mjpropiedades_logo_separator', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('mjpropiedades_logo_separator', array(
        'label' => '',
        'section' => 'title_tagline',
        'type' => 'text',
        'input_attrs' => array(
            'style' => 'display: none;',
        ),
        'priority' => 13,
    ));
    
    // ===== SECCIÓN DE LOGO DEL FOOTER =====
    $wp_customize->add_section('mjpropiedades_footer_logo', array(
        'title'    => __('Logo del Footer', 'mjpropiedades'),
        'priority' => 28,
        'description' => __('Configura el logo específico para el footer, independiente del logo del header', 'mjpropiedades'),
        'active_callback' => '__return_true',
    ));
    
    // Logo del footer
    $wp_customize->add_setting('mjpropiedades_footer_logo_image', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'mjpropiedades_footer_logo_image', array(
        'label'     => __('Logo del Footer', 'mjpropiedades'),
        'section'   => 'mjpropiedades_footer_logo',
        'mime_type' => 'image',
        'description' => __('Selecciona una imagen para el logo del footer. Si no se selecciona, se usará el logo del header.', 'mjpropiedades'),
    )));
    
    // Posicionamiento del logo en el footer
    $wp_customize->add_setting('mjpropiedades_footer_logo_position', array(
        'default'           => 'left',
        'sanitize_callback' => 'mjpropiedades_sanitize_logo_position',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('mjpropiedades_footer_logo_position', array(
        'label'       => __('Posición del Logo en el Footer', 'mjpropiedades'),
        'section'     => 'mjpropiedades_footer_logo',
        'type'        => 'select',
        'choices'     => array(
            'left'   => __('Izquierda', 'mjpropiedades'),
            'center' => __('Centro', 'mjpropiedades'),
            'right'  => __('Derecha', 'mjpropiedades'),
        ),
        'description' => __('Selecciona dónde quieres que aparezca el logo en el footer', 'mjpropiedades'),
    ));
    
    // Tamaño del logo del footer
    $wp_customize->add_setting('mjpropiedades_footer_logo_size', array(
        'default'           => 'medium',
        'sanitize_callback' => 'mjpropiedades_sanitize_footer_logo_size',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('mjpropiedades_footer_logo_size', array(
        'label'       => __('Tamaño del Logo del Footer', 'mjpropiedades'),
        'section'     => 'mjpropiedades_footer_logo',
        'type'        => 'select',
        'choices'     => array(
            'small'  => __('Pequeño (60px)', 'mjpropiedades'),
            'medium' => __('Mediano (80px)', 'mjpropiedades'),
            'large'  => __('Grande (120px)', 'mjpropiedades'),
            'custom' => __('Personalizado', 'mjpropiedades'),
        ),
        'description' => __('Selecciona el tamaño del logo en el footer', 'mjpropiedades'),
    ));
    
    // Tamaño personalizado del logo del footer
    $wp_customize->add_setting('mjpropiedades_footer_logo_custom_size', array(
        'default'           => '80',
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('mjpropiedades_footer_logo_custom_size', array(
        'label'       => __('Tamaño Personalizado (px)', 'mjpropiedades'),
        'section'     => 'mjpropiedades_footer_logo',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 30,
            'max'  => 200,
            'step' => 5,
        ),
        'description' => __('Especifica el tamaño exacto del logo en píxeles (30-200px)', 'mjpropiedades'),
    ));
    
    // Mostrar texto alternativo junto al logo
    $wp_customize->add_setting('mjpropiedades_footer_logo_show_text', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('mjpropiedades_footer_logo_show_text', array(
        'label'       => __('Mostrar Texto Descriptivo', 'mjpropiedades'),
        'section'     => 'mjpropiedades_footer_logo',
        'type'        => 'checkbox',
        'description' => __('Muestra el texto descriptivo debajo del logo', 'mjpropiedades'),
    ));
    
    // Texto personalizado del footer
    $wp_customize->add_setting('mjpropiedades_footer_logo_text', array(
        'default'           => 'Tu corredora de confianza especializada en la Cuarta Región de Chile.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('mjpropiedades_footer_logo_text', array(
        'label'       => __('Texto Descriptivo del Footer', 'mjpropiedades'),
        'section'     => 'mjpropiedades_footer_logo',
        'type'        => 'textarea',
        'description' => __('Texto que aparece debajo del logo en el footer', 'mjpropiedades'),
    ));
    
    // Separador visual
    $wp_customize->add_setting('mjpropiedades_footer_logo_separator', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('mjpropiedades_footer_logo_separator', array(
        'label' => '',
        'section' => 'mjpropiedades_footer_logo',
        'type' => 'text',
        'input_attrs' => array(
            'style' => 'display: none;',
        ),
    ));
    
    // Sección del Hero Slider
    $wp_customize->add_section('mjpropiedades_hero', array(
        'title'    => __('Hero Slider', 'mjpropiedades'),
        'priority' => 25,
        'active_callback' => '__return_true',
    ));
    
    // Hero Image 1 - Compra de Propiedades
    $wp_customize->add_setting('mjpropiedades_hero_1', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'mjpropiedades_hero_1', array(
        'label'   => __('Diapositiva 1 - Imagen de Fondo (Compra)', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'mime_type' => 'image',
    )));
    
    // Contenido Diapositiva 1
    $wp_customize->add_setting('mjpropiedades_slide_1_tag', array(
        'default' => 'Compra de Propiedades',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_slide_1_tag', array(
        'label' => __('Diapositiva 1 - Tag', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('mjpropiedades_slide_1_title', array(
        'default' => 'Encuentra tu Hogar Ideal en Chile',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_slide_1_title', array(
        'label' => __('Diapositiva 1 - Título', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('mjpropiedades_slide_1_description', array(
        'default' => 'Atendemos en Copiapó, Viña del Mar, La Serena y nos expandimos a más ciudades. Descubre propiedades exclusivas con asesoría personalizada y certificada en todo el proceso de compra.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('mjpropiedades_slide_1_description', array(
        'label' => __('Diapositiva 1 - Descripción', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'type' => 'textarea',
    ));
    
    $wp_customize->add_setting('mjpropiedades_slide_1_btn_primary', array(
        'default' => 'Ver Propiedades',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_slide_1_btn_primary', array(
        'label' => __('Diapositiva 1 - Botón Primario', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('mjpropiedades_slide_1_btn_secondary', array(
        'default' => 'Solicitar Tasación',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_slide_1_btn_secondary', array(
        'label' => __('Diapositiva 1 - Botón Secundario', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'type' => 'text',
    ));
    
    // Separador visual
    $wp_customize->add_setting('mjpropiedades_hero_separator_1', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_hero_separator_1', array(
        'label' => '',
        'section' => 'mjpropiedades_hero',
        'type' => 'text',
        'input_attrs' => array(
            'style' => 'display: none;',
        ),
    ));
    
    // Hero Image 2 - Venta de Propiedades
    $wp_customize->add_setting('mjpropiedades_hero_2', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'mjpropiedades_hero_2', array(
        'label'   => __('Diapositiva 2 - Imagen de Fondo (Venta)', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'mime_type' => 'image',
    )));
    
    // Contenido Diapositiva 2
    $wp_customize->add_setting('mjpropiedades_slide_2_tag', array(
        'default' => 'Venta de Propiedades',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_slide_2_tag', array(
        'label' => __('Diapositiva 2 - Tag', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('mjpropiedades_slide_2_title', array(
        'default' => 'Vende tu Propiedad al Mejor Precio',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_slide_2_title', array(
        'label' => __('Diapositiva 2 - Título', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('mjpropiedades_slide_2_description', array(
        'default' => '¿Tienes una propiedad para vender? Te ayudamos a obtener el mejor valor de mercado. Servicios profesionales de tasación y comercialización en Copiapó, Viña del Mar, La Serena y próximamente en más ciudades.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('mjpropiedades_slide_2_description', array(
        'label' => __('Diapositiva 2 - Descripción', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'type' => 'textarea',
    ));
    
    $wp_customize->add_setting('mjpropiedades_slide_2_btn_primary', array(
        'default' => 'Solicitar Tasación',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_slide_2_btn_primary', array(
        'label' => __('Diapositiva 2 - Botón Primario', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('mjpropiedades_slide_2_btn_secondary', array(
        'default' => 'Ver Propiedades',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_slide_2_btn_secondary', array(
        'label' => __('Diapositiva 2 - Botón Secundario', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'type' => 'text',
    ));
    
    // Separador visual
    $wp_customize->add_setting('mjpropiedades_hero_separator_2', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_hero_separator_2', array(
        'label' => '',
        'section' => 'mjpropiedades_hero',
        'type' => 'text',
        'input_attrs' => array(
            'style' => 'display: none;',
        ),
    ));
    
    // Hero Image 3 - Arriendo de Propiedades
    $wp_customize->add_setting('mjpropiedades_hero_3', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'mjpropiedades_hero_3', array(
        'label'   => __('Diapositiva 3 - Imagen de Fondo (Arriendo)', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'mime_type' => 'image',
    )));
    
    // Contenido Diapositiva 3
    $wp_customize->add_setting('mjpropiedades_slide_3_tag', array(
        'default' => 'Arriendo de Propiedades',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_slide_3_tag', array(
        'label' => __('Diapositiva 3 - Tag', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('mjpropiedades_slide_3_title', array(
        'default' => 'Arrienda o Arrienda tu Propiedad',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_slide_3_title', array(
        'label' => __('Diapositiva 3 - Título', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('mjpropiedades_slide_3_description', array(
        'default' => 'Ya sea que busques arrendar o tengas una propiedad para arrendar, te conectamos con las mejores opciones. Servicio profesional en Copiapó, Viña del Mar, La Serena con expansión continua a nuevas ciudades.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('mjpropiedades_slide_3_description', array(
        'label' => __('Diapositiva 3 - Descripción', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'type' => 'textarea',
    ));
    
    $wp_customize->add_setting('mjpropiedades_slide_3_btn_primary', array(
        'default' => 'Ver Arriendos',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_slide_3_btn_primary', array(
        'label' => __('Diapositiva 3 - Botón Primario', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('mjpropiedades_slide_3_btn_secondary', array(
        'default' => 'Arrendar Propiedad',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_slide_3_btn_secondary', array(
        'label' => __('Diapositiva 3 - Botón Secundario', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'type' => 'text',
    ));
    
    // Separador visual para colores
    $wp_customize->add_setting('mjpropiedades_hero_colors_separator', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_hero_colors_separator', array(
        'label' => '',
        'section' => 'mjpropiedades_hero',
        'type' => 'text',
        'input_attrs' => array(
            'style' => 'display: none;',
        ),
    ));
    
    // Color de las viñetas (tags)
    $wp_customize->add_setting('mjpropiedades_hero_tag_color', array(
        'default' => '#1e40af',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_hero_tag_color', array(
        'label' => __('Color de las Viñetas (Tags)', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'description' => __('Selecciona el color de fondo de las viñetas que aparecen arriba de los títulos', 'mjpropiedades'),
    )));
    
    // Color del texto de las viñetas
    $wp_customize->add_setting('mjpropiedades_hero_tag_text_color', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_hero_tag_text_color', array(
        'label' => __('Color del Texto de las Viñetas', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'description' => __('Selecciona el color del texto dentro de las viñetas', 'mjpropiedades'),
    )));
    
    // Color de los títulos
    $wp_customize->add_setting('mjpropiedades_hero_title_color', array(
        'default' => '#1e293b',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_hero_title_color', array(
        'label' => __('Color de los Títulos', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'description' => __('Selecciona el color de los títulos principales del slider', 'mjpropiedades'),
    )));
    
    // Color de las descripciones
    $wp_customize->add_setting('mjpropiedades_hero_description_color', array(
        'default' => '#64748b',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_hero_description_color', array(
        'label' => __('Color de las Descripciones', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'description' => __('Selecciona el color del texto de las descripciones', 'mjpropiedades'),
    )));
    
    // Botón para resetear valores por defecto
    $wp_customize->add_setting('mjpropiedades_hero_reset', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_hero_reset', array(
        'label' => __('Resetear a Valores por Defecto', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'type' => 'button',
        'input_attrs' => array(
            'value' => 'Resetear Valores',
            'class' => 'button button-secondary',
            'onclick' => 'mjpropiedadesResetHeroValues()',
        ),
    ));
    
    // Función para resetear valores del hero
    function mjpropiedades_reset_hero_values() {
        if (isset($_POST['action']) && $_POST['action'] === 'reset_hero_values') {
            check_ajax_referer('reset_hero_values', 'nonce');
            
            // Valores por defecto
            $default_values = array(
                'mjpropiedades_slide_1_tag' => 'Compra de Propiedades',
                'mjpropiedades_slide_1_title' => 'Encuentra tu Hogar Ideal en Chile',
                'mjpropiedades_slide_1_description' => 'Atendemos en Copiapó, Viña del Mar, La Serena y nos expandimos a más ciudades. Descubre propiedades exclusivas con asesoría personalizada y certificada en todo el proceso de compra.',
                'mjpropiedades_slide_1_btn_primary' => 'Ver Propiedades',
                'mjpropiedades_slide_1_btn_secondary' => 'Solicitar Tasación',
                
                'mjpropiedades_slide_2_tag' => 'Venta de Propiedades',
                'mjpropiedades_slide_2_title' => 'Vende tu Propiedad al Mejor Precio',
                'mjpropiedades_slide_2_description' => '¿Tienes una propiedad para vender? Te ayudamos a obtener el mejor valor de mercado. Servicios profesionales de tasación y comercialización en Copiapó, Viña del Mar, La Serena y próximamente en más ciudades.',
                'mjpropiedades_slide_2_btn_primary' => 'Solicitar Tasación',
                'mjpropiedades_slide_2_btn_secondary' => 'Ver Propiedades',
                
                'mjpropiedades_slide_3_tag' => 'Arriendo de Propiedades',
                'mjpropiedades_slide_3_title' => 'Arrienda o Arrienda tu Propiedad',
                'mjpropiedades_slide_3_description' => 'Ya sea que busques arrendar o tengas una propiedad para arrendar, te conectamos con las mejores opciones. Servicio profesional en Copiapó, Viña del Mar, La Serena con expansión continua a nuevas ciudades.',
                'mjpropiedades_slide_3_btn_primary' => 'Ver Arriendos',
                'mjpropiedades_slide_3_btn_secondary' => 'Arrendar Propiedad',
                
                // Colores por defecto
                'mjpropiedades_hero_tag_color' => '#1e40af',
                'mjpropiedades_hero_tag_text_color' => '#ffffff',
                'mjpropiedades_hero_title_color' => '#1e293b',
                'mjpropiedades_hero_description_color' => '#64748b',
            );
            
            // Actualizar valores
            foreach ($default_values as $key => $value) {
                set_theme_mod($key, $value);
            }
            
            wp_send_json_success('Valores reseteados correctamente');
        }
    }
    add_action('wp_ajax_reset_hero_values', 'mjpropiedades_reset_hero_values');
    
    // Agregar JavaScript al Customizer
    function mjpropiedades_customizer_scripts() {
        ?>
        <script type="text/javascript">
        function mjpropiedadesResetHeroValues() {
            if (confirm('¿Estás seguro de que quieres resetear todos los valores del Hero Slider a los valores por defecto?')) {
                jQuery.post(ajaxurl, {
                    action: 'reset_hero_values',
                    nonce: '<?php echo wp_create_nonce('reset_hero_values'); ?>'
                }, function(response) {
                    if (response.success) {
                        alert('Valores reseteados correctamente. Recarga la página para ver los cambios.');
                        location.reload();
                    } else {
                        alert('Error al resetear los valores.');
                    }
                });
            }
        }
        </script>
        <?php
    }
    add_action('customize_controls_print_scripts', 'mjpropiedades_customizer_scripts');
    
    // Agregar CSS dinámico para los colores del hero
    function mjpropiedades_hero_dynamic_css() {
        $tag_color = get_theme_mod('mjpropiedades_hero_tag_color', '#1e40af');
        $tag_text_color = get_theme_mod('mjpropiedades_hero_tag_text_color', '#ffffff');
        $title_color = get_theme_mod('mjpropiedades_hero_title_color', '#1e293b');
        $description_color = get_theme_mod('mjpropiedades_hero_description_color', '#64748b');
        
        $css = "
        <style type='text/css' id='mjpropiedades-hero-colors'>
        :root {
            --hero-tag-color: {$tag_color} !important;
            --hero-tag-text-color: {$tag_text_color} !important;
            --hero-title-color: {$title_color} !important;
            --hero-description-color: {$description_color} !important;
        }
        
        .hero-tag {
            background-color: {$tag_color} !important;
            color: {$tag_text_color} !important;
        }
        
        .hero-content h1 {
            color: {$title_color} !important;
        }
        
        .hero-description {
            color: {$description_color} !important;
        }
        </style>
        ";
        
        echo $css;
    }
    add_action('wp_head', 'mjpropiedades_hero_dynamic_css');
    
    // Limpiar caché cuando se cambien los colores del hero
    function mjpropiedades_clear_cache_on_color_change() {
        // Limpiar caché de WordPress si existe
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        // Limpiar caché de objetos si existe
        if (function_exists('wp_cache_delete')) {
            wp_cache_delete('mjpropiedades_hero_colors', 'theme_mods');
        }
    }
    add_action('customize_save_after', 'mjpropiedades_clear_cache_on_color_change');
    
    // Agregar parámetros de versión para evitar caché
    function mjpropiedades_add_version_to_css() {
        return time(); // Cambia cada vez que se carga la página
    }
    add_filter('style_loader_src', function($src) {
        if (strpos($src, 'style.css') !== false) {
            $src = add_query_arg('v', mjpropiedades_add_version_to_css(), $src);
        }
        return $src;
    });
    
    // Sección About
    $wp_customize->add_section('mjpropiedades_about', array(
        'title'    => __('Sección Quiénes Somos', 'mjpropiedades'),
        'priority' => 30,
        'active_callback' => '__return_true',
    ));
    
    // Sección Servicios
    $wp_customize->add_section('mjpropiedades_services', array(
        'title'    => __('Sección Nuestros Servicios', 'mjpropiedades'),
        'priority' => 32,
        'active_callback' => '__return_true',
    ));
    
    // Imagen de María José
    $wp_customize->add_setting('mjpropiedades_about_image', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'mjpropiedades_about_image', array(
        'label'   => __('Imagen de María José', 'mjpropiedades'),
        'section' => 'mjpropiedades_about',
        'mime_type' => 'image',
    )));
    
    // Texto de María José
    $wp_customize->add_setting('mjpropiedades_about_text_1', array(
        'default' => 'Home Isa es una empresa inmobiliaria innovadora fundada en 2025, con alcance nacional en Chile. Nos especializamos en brindar servicios integrales de corretaje inmobiliario, asesoría y tasación de propiedades.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('mjpropiedades_about_text_1', array(
        'label' => __('Primer Párrafo', 'mjpropiedades'),
        'section' => 'mjpropiedades_about',
        'type' => 'textarea',
    ));
    
    $wp_customize->add_setting('mjpropiedades_about_text_2', array(
        'default' => 'Nuestro compromiso es facilitar el proceso inmobiliario para todo tipo de clientes: familias que buscan su primer hogar, inversionistas experimentados que buscan oportunidades de crecimiento, propietarios que desean vender sus propiedades al mejor precio, y personas que necesitan arrendar o encontrar inquilinos para sus inmuebles. Con sede en La Serena y cobertura nacional, combinamos la experiencia local con una visión moderna del mercado inmobiliario chileno.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('mjpropiedades_about_text_2', array(
        'label' => __('Segundo Párrafo', 'mjpropiedades'),
        'section' => 'mjpropiedades_about',
        'type' => 'textarea',
    ));
    
    // Estadísticas
    $wp_customize->add_setting('mjpropiedades_about_stat_1_number', array(
        'default' => '500+',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_about_stat_1_number', array(
        'label' => __('Estadística 1 - Número', 'mjpropiedades'),
        'section' => 'mjpropiedades_about',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('mjpropiedades_about_stat_1_label', array(
        'default' => 'Propiedades Vendidas',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_about_stat_1_label', array(
        'label' => __('Estadística 1 - Etiqueta', 'mjpropiedades'),
        'section' => 'mjpropiedades_about',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('mjpropiedades_about_stat_2_number', array(
        'default' => '98%',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_about_stat_2_number', array(
        'label' => __('Estadística 2 - Número', 'mjpropiedades'),
        'section' => 'mjpropiedades_about',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('mjpropiedades_about_stat_2_label', array(
        'default' => 'Clientes Satisfechos',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_about_stat_2_label', array(
        'label' => __('Estadística 2 - Etiqueta', 'mjpropiedades'),
        'section' => 'mjpropiedades_about',
        'type' => 'text',
    ));
    
    // Controles para Sección Servicios
    // Tag de servicios
    $wp_customize->add_setting('mjpropiedades_services_tag', array(
        'default' => 'NUESTROS SERVICIOS',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_services_tag', array(
        'label' => __('Tag de Servicios', 'mjpropiedades'),
        'section' => 'mjpropiedades_services',
        'type' => 'text',
    ));
    
    // Título principal de servicios
    $wp_customize->add_setting('mjpropiedades_services_title', array(
        'default' => 'Te Acompañamos en Cada Paso',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_services_title', array(
        'label' => __('Título Principal', 'mjpropiedades'),
        'section' => 'mjpropiedades_services',
        'type' => 'text',
    ));
    
    // Subtítulo de servicios
    $wp_customize->add_setting('mjpropiedades_services_subtitle', array(
        'default' => 'Servicios profesionales diseñados para hacer realidad tus objetivos inmobiliarios',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('mjpropiedades_services_subtitle', array(
        'label' => __('Subtítulo', 'mjpropiedades'),
        'section' => 'mjpropiedades_services',
        'type' => 'textarea',
    ));
    
    // Color del título de servicios
    $wp_customize->add_setting('mjpropiedades_services_title_color', array(
        'default' => '#374151',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_services_title_color', array(
        'label' => __('Color del Título', 'mjpropiedades'),
        'section' => 'mjpropiedades_services',
    )));
    
    // Color del subtítulo de servicios
    $wp_customize->add_setting('mjpropiedades_services_subtitle_color', array(
        'default' => '#6b7280',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_services_subtitle_color', array(
        'label' => __('Color del Subtítulo', 'mjpropiedades'),
        'section' => 'mjpropiedades_services',
    )));
    
    // Servicios dinámicos (hasta 6 servicios)
    $default_services = array(
        1 => array(
            'title' => 'Venta',
            'description' => 'Te ayudamos a vender tu propiedad al mejor precio del mercado con estrategias personalizadas.',
            'features' => "Marketing digital especializado\nFotografía profesional\nTours virtuales\nAsesoría de precios"
        ),
        2 => array(
            'title' => 'Arriendo',
            'description' => 'Encontramos el inquilino ideal para tu propiedad con procesos seguros y eficientes.',
            'features' => "Selección de inquilinos\nVerificación de antecedentes\nContratos legales\nGestión de pagos"
        ),
        3 => array(
            'title' => 'Tasaciones',
            'description' => 'Valoramos tu propiedad con precisión profesional para tomar las mejores decisiones.',
            'features' => "Análisis de mercado\nComparación de propiedades\nInforme detallado\nCertificación profesional"
        ),
        4 => array(
            'title' => 'Asesoría Legal',
            'description' => 'Te acompañamos en todo el proceso legal y administrativo para que no tengas que preocuparte por nada.',
            'features' => "Tramitación de escrituras\nGestión de permisos\nSeguimiento legal\nAsesoría especializada"
        )
    );
    
    for ($i = 1; $i <= 6; $i++) {
        $default_title = isset($default_services[$i]) ? $default_services[$i]['title'] : '';
        $default_description = isset($default_services[$i]) ? $default_services[$i]['description'] : '';
        $default_features = isset($default_services[$i]) ? $default_services[$i]['features'] : '';
        
        // Título del servicio
        $wp_customize->add_setting("mjpropiedades_service_{$i}_title", array(
            'default' => $default_title,
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("mjpropiedades_service_{$i}_title", array(
            'label' => sprintf(__('Servicio %d - Título', 'mjpropiedades'), $i),
            'section' => 'mjpropiedades_services',
            'type' => 'text',
        ));
        
        // Descripción del servicio
        $wp_customize->add_setting("mjpropiedades_service_{$i}_description", array(
            'default' => $default_description,
            'sanitize_callback' => 'sanitize_textarea_field',
        ));
        $wp_customize->add_control("mjpropiedades_service_{$i}_description", array(
            'label' => sprintf(__('Servicio %d - Descripción', 'mjpropiedades'), $i),
            'section' => 'mjpropiedades_services',
            'type' => 'textarea',
        ));
        
        // Características del servicio (una por línea)
        $wp_customize->add_setting("mjpropiedades_service_{$i}_features", array(
            'default' => $default_features,
            'sanitize_callback' => 'sanitize_textarea_field',
        ));
        $wp_customize->add_control("mjpropiedades_service_{$i}_features", array(
            'label' => sprintf(__('Servicio %d - Características (una por línea)', 'mjpropiedades'), $i),
            'section' => 'mjpropiedades_services',
            'type' => 'textarea',
        ));
        
    }
    
    // Sección Testimonios
    $wp_customize->add_section('mjpropiedades_testimonials', array(
        'title'    => __('Sección Testimonios', 'mjpropiedades'),
        'priority' => 33,
        'active_callback' => '__return_true',
    ));
    
    // Título de la sección
    $wp_customize->add_setting('mjpropiedades_testimonials_title', array(
        'default' => 'Lo que dicen nuestros clientes',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_testimonials_title', array(
        'label' => __('Título de la Sección', 'mjpropiedades'),
        'section' => 'mjpropiedades_testimonials',
        'type' => 'text',
    ));
    
    // Subtítulo de la sección
    $wp_customize->add_setting('mjpropiedades_testimonials_subtitle', array(
        'default' => 'Testimonios reales de clientes satisfechos en la Cuarta Región',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('mjpropiedades_testimonials_subtitle', array(
        'label' => __('Subtítulo de la Sección', 'mjpropiedades'),
        'section' => 'mjpropiedades_testimonials',
        'type' => 'textarea',
    ));
    
    // Testimonio 1
    $wp_customize->add_setting('mjpropiedades_testimonial_1_text', array(
        'default' => 'Vendí mi casa en Peñuelas, Coquimbo, en menos de 30 días. Home Isa fue increíble, muy profesional y siempre disponible para resolver mis dudas.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('mjpropiedades_testimonial_1_text', array(
        'label' => __('Testimonio 1 - Texto', 'mjpropiedades'),
        'section' => 'mjpropiedades_testimonials',
        'type' => 'textarea',
    ));
    
    $wp_customize->add_setting('mjpropiedades_testimonial_1_name', array(
        'default' => 'Carlos Mendoza',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_testimonial_1_name', array(
        'label' => __('Testimonio 1 - Nombre', 'mjpropiedades'),
        'section' => 'mjpropiedades_testimonials',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('mjpropiedades_testimonial_1_location', array(
        'default' => 'Peñuelas, Coquimbo',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_testimonial_1_location', array(
        'label' => __('Testimonio 1 - Ubicación', 'mjpropiedades'),
        'section' => 'mjpropiedades_testimonials',
        'type' => 'text',
    ));
    
    // Testimonio 2
    $wp_customize->add_setting('mjpropiedades_testimonial_2_text', array(
        'default' => 'Encontré el departamento perfecto en La Serena gracias a Home Isa. Su conocimiento de la zona es excepcional y el proceso fue muy transparente.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('mjpropiedades_testimonial_2_text', array(
        'label' => __('Testimonio 2 - Texto', 'mjpropiedades'),
        'section' => 'mjpropiedades_testimonials',
        'type' => 'textarea',
    ));
    
    $wp_customize->add_setting('mjpropiedades_testimonial_2_name', array(
        'default' => 'Ana Rodríguez',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_testimonial_2_name', array(
        'label' => __('Testimonio 2 - Nombre', 'mjpropiedades'),
        'section' => 'mjpropiedades_testimonials',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('mjpropiedades_testimonial_2_location', array(
        'default' => 'La Serena',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_testimonial_2_location', array(
        'label' => __('Testimonio 2 - Ubicación', 'mjpropiedades'),
        'section' => 'mjpropiedades_testimonials',
        'type' => 'text',
    ));
    
    // Testimonio 3
    $wp_customize->add_setting('mjpropiedades_testimonial_3_text', array(
        'default' => 'Arrendé mi casa en Ovalle con Home Isa. El servicio fue impecable, desde la tasación hasta la entrega de llaves. Totalmente recomendable.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('mjpropiedades_testimonial_3_text', array(
        'label' => __('Testimonio 3 - Texto', 'mjpropiedades'),
        'section' => 'mjpropiedades_testimonials',
        'type' => 'textarea',
    ));
    
    $wp_customize->add_setting('mjpropiedades_testimonial_3_name', array(
        'default' => 'Roberto Silva',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_testimonial_3_name', array(
        'label' => __('Testimonio 3 - Nombre', 'mjpropiedades'),
        'section' => 'mjpropiedades_testimonials',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('mjpropiedades_testimonial_3_location', array(
        'default' => 'Ovalle',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_testimonial_3_location', array(
        'label' => __('Testimonio 3 - Ubicación', 'mjpropiedades'),
        'section' => 'mjpropiedades_testimonials',
        'type' => 'text',
    ));
    
    // Testimonio 4
    $wp_customize->add_setting('mjpropiedades_testimonial_4_text', array(
        'default' => 'Excelente asesoría para mi inversión en Coquimbo. Home Isa me ayudó a encontrar la propiedad ideal con el mejor retorno. Muy satisfecho.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('mjpropiedades_testimonial_4_text', array(
        'label' => __('Testimonio 4 - Texto', 'mjpropiedades'),
        'section' => 'mjpropiedades_testimonials',
        'type' => 'textarea',
    ));
    
    $wp_customize->add_setting('mjpropiedades_testimonial_4_name', array(
        'default' => 'María González',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_testimonial_4_name', array(
        'label' => __('Testimonio 4 - Nombre', 'mjpropiedades'),
        'section' => 'mjpropiedades_testimonials',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('mjpropiedades_testimonial_4_location', array(
        'default' => 'Coquimbo',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_testimonial_4_location', array(
        'label' => __('Testimonio 4 - Ubicación', 'mjpropiedades'),
        'section' => 'mjpropiedades_testimonials',
        'type' => 'text',
    ));
    
    // Testimonio 5
    $wp_customize->add_setting('mjpropiedades_testimonial_5_text', array(
        'default' => 'Compré mi primera casa en La Serena con Home Isa. Su paciencia y dedicación hicieron que todo el proceso fuera muy fácil para mí.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('mjpropiedades_testimonial_5_text', array(
        'label' => __('Testimonio 5 - Texto', 'mjpropiedades'),
        'section' => 'mjpropiedades_testimonials',
        'type' => 'textarea',
    ));
    
    $wp_customize->add_setting('mjpropiedades_testimonial_5_name', array(
        'default' => 'Diego Herrera',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_testimonial_5_name', array(
        'label' => __('Testimonio 5 - Nombre', 'mjpropiedades'),
        'section' => 'mjpropiedades_testimonials',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('mjpropiedades_testimonial_5_location', array(
        'default' => 'La Serena',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_testimonial_5_location', array(
        'label' => __('Testimonio 5 - Ubicación', 'mjpropiedades'),
        'section' => 'mjpropiedades_testimonials',
        'type' => 'text',
    ));
    
    // Testimonio 6
    $wp_customize->add_setting('mjpropiedades_testimonial_6_text', array(
        'default' => 'Vendí mi terreno en Ovalle rápidamente gracias a la estrategia de marketing de Home Isa. Su experiencia en la región es invaluable.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('mjpropiedades_testimonial_6_text', array(
        'label' => __('Testimonio 6 - Texto', 'mjpropiedades'),
        'section' => 'mjpropiedades_testimonials',
        'type' => 'textarea',
    ));
    
    $wp_customize->add_setting('mjpropiedades_testimonial_6_name', array(
        'default' => 'Patricia Morales',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_testimonial_6_name', array(
        'label' => __('Testimonio 6 - Nombre', 'mjpropiedades'),
        'section' => 'mjpropiedades_testimonials',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('mjpropiedades_testimonial_6_location', array(
        'default' => 'Ovalle',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_testimonial_6_location', array(
        'label' => __('Testimonio 6 - Ubicación', 'mjpropiedades'),
        'section' => 'mjpropiedades_testimonials',
        'type' => 'text',
    ));
    
    // Logo personalizado
    $wp_customize->add_setting('mjpropiedades_logo', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'mjpropiedades_logo', array(
        'label'   => __('Logo del Sitio', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'mime_type' => 'image',
    )));
    
    // Sección de configuración del menú
    $wp_customize->add_section('mjpropiedades_menu', array(
        'title'    => __('Configuración del Menú', 'mjpropiedades'),
        'priority' => 38,
        'active_callback' => '__return_true',
    ));
    
    // Alineación del menú
    $wp_customize->add_setting('mjpropiedades_menu_alignment', array(
        'default'           => 'right',
        'sanitize_callback' => 'mjpropiedades_sanitize_menu_alignment',
    ));
    
    $wp_customize->add_control('mjpropiedades_menu_alignment', array(
        'label'   => __('Alineación del Menú', 'mjpropiedades'),
        'section' => 'mjpropiedades_menu',
        'type'    => 'select',
        'choices' => array(
            'left'   => __('Izquierda', 'mjpropiedades'),
            'center' => __('Centro', 'mjpropiedades'),
            'right'  => __('Derecha', 'mjpropiedades'),
        ),
    ));
    
    // Color de fondo de la barra superior del menú
    $wp_customize->add_setting('mjpropiedades_header_background_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_header_background_color', array(
        'label'    => __('Color de Fondo de la Barra Superior', 'mjpropiedades'),
        'section'  => 'mjpropiedades_menu',
        'description' => __('Selecciona el color de fondo para la barra superior del menú', 'mjpropiedades'),
    )));
    
    // Color del texto del menú
    $wp_customize->add_setting('mjpropiedades_menu_text_color', array(
        'default'           => '#333333',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_menu_text_color', array(
        'label'    => __('Color del Texto del Menú', 'mjpropiedades'),
        'section'  => 'mjpropiedades_menu',
        'description' => __('Selecciona el color del texto para los enlaces del menú', 'mjpropiedades'),
    )));
    
    // Color del texto del menú al pasar el mouse
    $wp_customize->add_setting('mjpropiedades_menu_hover_color', array(
        'default'           => '#1e40af',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_menu_hover_color', array(
        'label'    => __('Color del Menú al Pasar el Mouse', 'mjpropiedades'),
        'section'  => 'mjpropiedades_menu',
        'description' => __('Selecciona el color cuando pasas el mouse sobre los enlaces del menú', 'mjpropiedades'),
    )));
    
    // Color del botón "Contactar"
    $wp_customize->add_setting('mjpropiedades_contact_button_color', array(
        'default'           => '#1e40af',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_contact_button_color', array(
        'label'    => __('Color del Botón "Contactar"', 'mjpropiedades'),
        'section'  => 'mjpropiedades_menu',
        'description' => __('Selecciona el color de fondo del botón "Contactar"', 'mjpropiedades'),
    )));
    
    // Color del texto del botón "Contactar"
    $wp_customize->add_setting('mjpropiedades_contact_button_text_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_contact_button_text_color', array(
        'label'    => __('Color del Texto del Botón "Contactar"', 'mjpropiedades'),
        'section'  => 'mjpropiedades_menu',
        'description' => __('Selecciona el color del texto del botón "Contactar"', 'mjpropiedades'),
    )));
    
    // Separador visual
    $wp_customize->add_setting('mjpropiedades_menu_separator_1', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('mjpropiedades_menu_separator_1', array(
        'label' => '',
        'section' => 'mjpropiedades_menu',
        'type' => 'text',
        'input_attrs' => array(
            'style' => 'display: none;',
        ),
    ));
    
    // Presets de colores para el menú
    $wp_customize->add_setting('mjpropiedades_menu_color_preset', array(
        'default'           => 'default',
        'sanitize_callback' => 'mjpropiedades_sanitize_color_preset',
    ));
    
    $wp_customize->add_control('mjpropiedades_menu_color_preset', array(
        'label'    => __('Presets de Colores Rápidos', 'mjpropiedades'),
        'section'  => 'mjpropiedades_menu',
        'type'     => 'select',
        'choices'  => array(
            'default' => __('Personalizado (Actual)', 'mjpropiedades'),
            'blue'    => __('Azul Profesional', 'mjpropiedades'),
            'dark'    => __('Oscuro Elegante', 'mjpropiedades'),
            'light'   => __('Claro Minimalista', 'mjpropiedades'),
            'green'   => __('Verde Naturaleza', 'mjpropiedades'),
            'purple'  => __('Morado Creativo', 'mjpropiedades'),
            'orange'  => __('Naranja Energético', 'mjpropiedades'),
        ),
        'description' => __('Selecciona un preset para aplicar automáticamente una combinación de colores', 'mjpropiedades'),
    ));
    
    // ===== SECCIÓN DE TIPOGRAFÍA =====
    $wp_customize->add_section('mjpropiedades_typography', array(
        'title'    => __('Tipografía', 'mjpropiedades'),
        'priority' => 34,
        'active_callback' => '__return_true',
    ));
    
    // Tamaño de títulos principales (H1) - Basado en la imagen de referencia
    $wp_customize->add_setting('mjpropiedades_h1_font_size', array(
        'default'           => '2.25rem',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('mjpropiedades_h1_font_size', array(
        'label'    => __('Tamaño de Títulos Principales (H1)', 'mjpropiedades'),
        'section'  => 'mjpropiedades_typography',
        'type'     => 'text',
        'description' => __('Ejemplo: 2.25rem, 36px, 2.25em', 'mjpropiedades'),
    ));
    
    // Tamaño de subtítulos (H2) - Basado en la imagen de referencia
    $wp_customize->add_setting('mjpropiedades_h2_font_size', array(
        'default'           => '1.5rem',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('mjpropiedades_h2_font_size', array(
        'label'    => __('Tamaño de Subtítulos (H2)', 'mjpropiedades'),
        'section'  => 'mjpropiedades_typography',
        'type'     => 'text',
        'description' => __('Ejemplo: 1.5rem, 24px, 1.5em', 'mjpropiedades'),
    ));
    
    // Tamaño de títulos de sección (H3) - Basado en la imagen de referencia
    $wp_customize->add_setting('mjpropiedades_h3_font_size', array(
        'default'           => '1.25rem',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('mjpropiedades_h3_font_size', array(
        'label'    => __('Tamaño de Títulos de Sección (H3)', 'mjpropiedades'),
        'section'  => 'mjpropiedades_typography',
        'type'     => 'text',
        'description' => __('Ejemplo: 1.25rem, 20px, 1.25em', 'mjpropiedades'),
    ));
    
    // Tamaño de texto normal - Basado en la imagen de referencia
    $wp_customize->add_setting('mjpropiedades_body_font_size', array(
        'default'           => '1rem',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('mjpropiedades_body_font_size', array(
        'label'    => __('Tamaño de Texto Normal', 'mjpropiedades'),
        'section'  => 'mjpropiedades_typography',
        'type'     => 'text',
        'description' => __('Ejemplo: 1rem, 16px, 1em', 'mjpropiedades'),
    ));
    
    // Tamaño de texto pequeño - Basado en la imagen de referencia
    $wp_customize->add_setting('mjpropiedades_small_font_size', array(
        'default'           => '0.875rem',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('mjpropiedades_small_font_size', array(
        'label'    => __('Tamaño de Texto Pequeño', 'mjpropiedades'),
        'section'  => 'mjpropiedades_typography',
        'type'     => 'text',
        'description' => __('Ejemplo: 0.875rem, 14px, 0.875em', 'mjpropiedades'),
    ));
    
    // Tamaño de botones - Basado en la imagen de referencia
    $wp_customize->add_setting('mjpropiedades_button_font_size', array(
        'default'           => '1rem',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('mjpropiedades_button_font_size', array(
        'label'    => __('Tamaño de Botones', 'mjpropiedades'),
        'section'  => 'mjpropiedades_typography',
        'type'     => 'text',
        'description' => __('Ejemplo: 1rem, 16px, 1em', 'mjpropiedades'),
    ));
    
    // Tamaño de precios - Basado en la imagen de referencia
    $wp_customize->add_setting('mjpropiedades_price_font_size', array(
        'default'           => '2rem',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('mjpropiedades_price_font_size', array(
        'label'    => __('Tamaño de Precios', 'mjpropiedades'),
        'section'  => 'mjpropiedades_typography',
        'type'     => 'text',
        'description' => __('Ejemplo: 2rem, 32px, 2em', 'mjpropiedades'),
    ));
    
    // Tamaño de etiquetas/tags - Basado en la imagen de referencia
    $wp_customize->add_setting('mjpropiedades_tag_font_size', array(
        'default'           => '0.75rem',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('mjpropiedades_tag_font_size', array(
        'label'    => __('Tamaño de Etiquetas/Tags', 'mjpropiedades'),
        'section'  => 'mjpropiedades_typography',
        'type'     => 'text',
        'description' => __('Ejemplo: 0.75rem, 12px, 0.75em', 'mjpropiedades'),
    ));
    
    // Tamaño de navegación - Basado en la imagen de referencia
    $wp_customize->add_setting('mjpropiedades_nav_font_size', array(
        'default'           => '1rem',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('mjpropiedades_nav_font_size', array(
        'label'    => __('Tamaño de Navegación', 'mjpropiedades'),
        'section'  => 'mjpropiedades_typography',
        'type'     => 'text',
        'description' => __('Ejemplo: 1rem, 16px, 1em', 'mjpropiedades'),
    ));
    
    // Tamaño de características de propiedades - Basado en la imagen de referencia
    $wp_customize->add_setting('mjpropiedades_feature_font_size', array(
        'default'           => '1.5rem',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('mjpropiedades_feature_font_size', array(
        'label'    => __('Tamaño de Características de Propiedades', 'mjpropiedades'),
        'section'  => 'mjpropiedades_typography',
        'type'     => 'text',
        'description' => __('Ejemplo: 1.5rem, 24px, 1.5em', 'mjpropiedades'),
    ));
    
    // Tamaño de formularios - Basado en la imagen de referencia
    $wp_customize->add_setting('mjpropiedades_form_font_size', array(
        'default'           => '1rem',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('mjpropiedades_form_font_size', array(
        'label'    => __('Tamaño de Formularios', 'mjpropiedades'),
        'section'  => 'mjpropiedades_typography',
        'type'     => 'text',
        'description' => __('Ejemplo: 1rem, 16px, 1em', 'mjpropiedades'),
    ));
    
    // Tamaño de estadísticas - Basado en la imagen de referencia
    $wp_customize->add_setting('mjpropiedades_stats_font_size', array(
        'default'           => '2rem',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('mjpropiedades_stats_font_size', array(
        'label'    => __('Tamaño de Estadísticas', 'mjpropiedades'),
        'section'  => 'mjpropiedades_typography',
        'type'     => 'text',
        'description' => __('Ejemplo: 2rem, 32px, 2em', 'mjpropiedades'),
    ));
    
    // Tamaño de títulos de tarjetas de propiedades - Específico para página de inicio
    $wp_customize->add_setting('mjpropiedades_property_card_title_font_size', array(
        'default'           => '1.25rem',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('mjpropiedades_property_card_title_font_size', array(
        'label'    => __('Tamaño de Títulos de Tarjetas de Propiedades', 'mjpropiedades'),
        'section'  => 'mjpropiedades_typography',
        'type'     => 'text',
        'description' => __('Específico para las tarjetas en la página de inicio. Ejemplo: 1.25rem, 20px, 1.25em', 'mjpropiedades'),
    ));
    
    // ===== SECCIÓN DE TÍTULOS DE SECCIONES =====
    $wp_customize->add_section('mjpropiedades_section_titles', array(
        'title'    => __('Títulos de Secciones', 'mjpropiedades'),
        'priority' => 31,
        'description' => __('Controla el estilo y alineación de los títulos de secciones en la página de inicio', 'mjpropiedades'),
        'active_callback' => '__return_true',
    ));
    
    // Alineación de títulos de secciones
    $wp_customize->add_setting('mjpropiedades_section_title_alignment', array(
        'default'           => 'center',
        'sanitize_callback' => 'mjpropiedades_sanitize_text_alignment',
    ));
    
    $wp_customize->add_control('mjpropiedades_section_title_alignment', array(
        'label'    => __('Alineación de Títulos de Secciones', 'mjpropiedades'),
        'section'  => 'mjpropiedades_section_titles',
        'type'     => 'select',
        'choices'  => array(
            'left'   => __('Izquierda', 'mjpropiedades'),
            'center' => __('Centro', 'mjpropiedades'),
            'right'  => __('Derecha', 'mjpropiedades'),
        ),
    ));
    
    // Tamaño de títulos de secciones
    $wp_customize->add_setting('mjpropiedades_section_title_size', array(
        'default'           => '2.5rem',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('mjpropiedades_section_title_size', array(
        'label'    => __('Tamaño de Títulos de Secciones', 'mjpropiedades'),
        'section'  => 'mjpropiedades_section_titles',
        'type'     => 'text',
        'description' => __('Ejemplo: 2.5rem, 40px, 2.5em', 'mjpropiedades'),
    ));
    
    // Color de títulos de secciones
    $wp_customize->add_setting('mjpropiedades_section_title_color', array(
        'default'           => '#1e40af',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_section_title_color', array(
        'label'    => __('Color de Títulos de Secciones', 'mjpropiedades'),
        'section'  => 'mjpropiedades_section_titles',
    )));
    
    // Peso de fuente de títulos de secciones
    $wp_customize->add_setting('mjpropiedades_section_title_weight', array(
        'default'           => '700',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('mjpropiedades_section_title_weight', array(
        'label'    => __('Peso de Fuente de Títulos', 'mjpropiedades'),
        'section'  => 'mjpropiedades_section_titles',
        'type'     => 'select',
        'choices'  => array(
            '300' => __('Ligero (300)', 'mjpropiedades'),
            '400' => __('Normal (400)', 'mjpropiedades'),
            '500' => __('Medio (500)', 'mjpropiedades'),
            '600' => __('Semi-bold (600)', 'mjpropiedades'),
            '700' => __('Bold (700)', 'mjpropiedades'),
            '800' => __('Extra-bold (800)', 'mjpropiedades'),
            '900' => __('Black (900)', 'mjpropiedades'),
        ),
    ));
    
    // Espaciado de letras
    $wp_customize->add_setting('mjpropiedades_section_title_letter_spacing', array(
        'default'           => '0',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('mjpropiedades_section_title_letter_spacing', array(
        'label'    => __('Espaciado de Letras', 'mjpropiedades'),
        'section'  => 'mjpropiedades_section_titles',
        'type'     => 'text',
        'description' => __('Ejemplo: 0, 0.5px, 0.1em, -0.025em', 'mjpropiedades'),
    ));
    
    // Margen inferior de títulos de secciones
    $wp_customize->add_setting('mjpropiedades_section_title_margin_bottom', array(
        'default'           => '1rem',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('mjpropiedades_section_title_margin_bottom', array(
        'label'    => __('Margen Inferior de Títulos', 'mjpropiedades'),
        'section'  => 'mjpropiedades_section_titles',
        'type'     => 'text',
        'description' => __('Ejemplo: 1rem, 20px, 1.5em', 'mjpropiedades'),
    ));
    
    // Mostrar línea decorativa debajo del título
    $wp_customize->add_setting('mjpropiedades_section_title_show_line', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('mjpropiedades_section_title_show_line', array(
        'label'    => __('Mostrar Línea Decorativa', 'mjpropiedades'),
        'section'  => 'mjpropiedades_section_titles',
        'type'     => 'checkbox',
        'description' => __('Agrega una línea decorativa debajo de los títulos de sección', 'mjpropiedades'),
    ));
    
    // Color de la línea decorativa
    $wp_customize->add_setting('mjpropiedades_section_title_line_color', array(
        'default'           => '#1e40af',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_section_title_line_color', array(
        'label'    => __('Color de Línea Decorativa', 'mjpropiedades'),
        'section'  => 'mjpropiedades_section_titles',
    )));
    // ===== CONFIGURACIÓN DE INFORMACIÓN DE CONTACTO =====
    
    // Sección de información de contacto
    $wp_customize->add_section('mjpropiedades_contact_info', array(
        'title'    => __('Información de Contacto', 'mjpropiedades'),
        'priority' => 39,
        'active_callback' => '__return_true',
    ));
    
    // Email de contacto
    $wp_customize->add_setting('mjpropiedades_contact_email', array(
        'default' => 'consultas@homeisa.cl',
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('mjpropiedades_contact_email', array(
        'label' => __('Email', 'mjpropiedades'),
        'section' => 'mjpropiedades_contact_info',
        'type' => 'email',
    ));
    
    // Teléfono de contacto
    $wp_customize->add_setting('mjpropiedades_contact_phone', array(
        'default' => '+56 9 4927 6448',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_contact_phone', array(
        'label' => __('Teléfono', 'mjpropiedades'),
        'section' => 'mjpropiedades_contact_info',
        'type' => 'text',
    ));
    
    // Dirección de contacto
    $wp_customize->add_setting('mjpropiedades_contact_address', array(
        'default' => 'La Serena, Chile',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_contact_address', array(
        'label' => __('Dirección', 'mjpropiedades'),
        'section' => 'mjpropiedades_contact_info',
        'type' => 'text',
    ));
    
    // Horarios de atención
    $wp_customize->add_setting('mjpropiedades_contact_hours', array(
        'default' => 'Lunes a Viernes: 9:00 - 18:00<br>Sábados: 9:00 - 14:00',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('mjpropiedades_contact_hours', array(
        'label' => __('Horarios', 'mjpropiedades'),
        'section' => 'mjpropiedades_contact_info',
        'type' => 'textarea',
    ));
}
add_action('customize_register', 'mjpropiedades_customize_register');

// Agregar CSS dinámico para tipografía
function mjpropiedades_typography_css() {
    $h1_size = get_theme_mod('mjpropiedades_h1_font_size', '2.25rem');
    $h2_size = get_theme_mod('mjpropiedades_h2_font_size', '1.5rem');
    $h3_size = get_theme_mod('mjpropiedades_h3_font_size', '1.25rem');
    $body_size = get_theme_mod('mjpropiedades_body_font_size', '1rem');
    $small_size = get_theme_mod('mjpropiedades_small_font_size', '0.875rem');
    $button_size = get_theme_mod('mjpropiedades_button_font_size', '1rem');
    $price_size = get_theme_mod('mjpropiedades_price_font_size', '2rem');
    $tag_size = get_theme_mod('mjpropiedades_tag_font_size', '0.75rem');
    $nav_size = get_theme_mod('mjpropiedades_nav_font_size', '1rem');
    $feature_size = get_theme_mod('mjpropiedades_feature_font_size', '1.5rem');
    $form_size = get_theme_mod('mjpropiedades_form_font_size', '1rem');
    $stats_size = get_theme_mod('mjpropiedades_stats_font_size', '2rem');
    $property_card_title_size = get_theme_mod('mjpropiedades_property_card_title_font_size', '1.25rem');
    
    ?>
    <style type="text/css">
        /* Títulos principales (H1) */
        h1, .hero-title, .property-title, .section-title h1 {
            font-size: <?php echo esc_attr($h1_size); ?> !important;
        }
        
        /* Subtítulos (H2) */
        h2, .hero-subtitle, .section-title h2, .about-title, .contact-title, .cta-title {
            font-size: <?php echo esc_attr($h2_size); ?> !important;
        }
        
        /* Títulos de sección (H3) */
        h3, .service-title, .property-card-title, .feature-title, .info-title {
            font-size: <?php echo esc_attr($h3_size); ?> !important;
        }
        
        /* Texto normal */
        body, p, .hero-description, .about-description, .service-description, 
        .property-description, .contact-description, .cta-description {
            font-size: <?php echo esc_attr($body_size); ?> !important;
        }
        
        /* Texto pequeño */
        .small-text, .property-meta, .contact-meta, .footer-text, 
        .property-address, .price-uf, .feature-label {
            font-size: <?php echo esc_attr($small_size); ?> !important;
        }
        
        /* Botones */
        .btn, .cta-button, .contact-btn, .hero-btn, .action-btn, 
        .gallery-close, .more-images {
            font-size: <?php echo esc_attr($button_size); ?> !important;
        }
        
        /* Precios */
        .price, .price-main, .property-price, .hero-price {
            font-size: <?php echo esc_attr($price_size); ?> !important;
        }
        
        /* Etiquetas/Tags */
        .tag, .property-tag, .hero-tag, .status-tag {
            font-size: <?php echo esc_attr($tag_size); ?> !important;
        }
        
        /* Navegación */
        .nav-menu a, .menu-item a, .footer-menu a {
            font-size: <?php echo esc_attr($nav_size); ?> !important;
        }
        
        /* Características de propiedades */
        .feature-number, .property-features .feature-number {
            font-size: <?php echo esc_attr($feature_size); ?> !important;
        }
        
        /* Formularios */
        input, textarea, select, .form-control, .contact-form input, 
        .contact-form textarea, .contact-form select {
            font-size: <?php echo esc_attr($form_size); ?> !important;
        }
        
        /* Estadísticas */
        .stats-number, .about-stats .stat-number {
            font-size: <?php echo esc_attr($stats_size); ?> !important;
        }
        
        /* Títulos de tarjetas de propiedades - Específico para página de inicio */
        .property-card-title, .property-card h3, .property-card .card-title, 
        .properties-grid .property-title, .properties-grid h3 {
            font-size: <?php echo esc_attr($property_card_title_size); ?> !important;
        }
        
        /* Títulos de secciones - Control completo desde WordPress */
        .section-title {
            text-align: <?php echo esc_attr(get_theme_mod('mjpropiedades_section_title_alignment', 'center')); ?> !important;
            font-size: <?php echo esc_attr(get_theme_mod('mjpropiedades_section_title_size', '2.5rem')); ?> !important;
            color: <?php echo esc_attr(get_theme_mod('mjpropiedades_section_title_color', '#1e40af')); ?> !important;
            font-weight: <?php echo esc_attr(get_theme_mod('mjpropiedades_section_title_weight', '700')); ?> !important;
            letter-spacing: <?php echo esc_attr(get_theme_mod('mjpropiedades_section_title_letter_spacing', '0')); ?> !important;
            margin-bottom: <?php echo esc_attr(get_theme_mod('mjpropiedades_section_title_margin_bottom', '1rem')); ?> !important;
        }
        
        /* Línea decorativa debajo de títulos de sección */
        <?php if (get_theme_mod('mjpropiedades_section_title_show_line', false)) : ?>
        .section-title::after {
            content: '';
            display: block;
            width: 60px;
            height: 3px;
            background-color: <?php echo esc_attr(get_theme_mod('mjpropiedades_section_title_line_color', '#1e40af')); ?>;
            margin: 0.5rem auto 0;
            border-radius: 2px;
        }
        
        .section-title[style*="text-align: left"]::after {
            margin: 0.5rem 0 0 0;
        }
        
        .section-title[style*="text-align: right"]::after {
            margin: 0.5rem 0 0 auto;
        }
        <?php endif; ?>
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            h1, .hero-title, .property-title, .section-title h1 {
                font-size: calc(<?php echo esc_attr($h1_size); ?> * 0.8) !important;
            }
            
            h2, .hero-subtitle, .section-title h2, .about-title, .contact-title, .cta-title {
                font-size: calc(<?php echo esc_attr($h2_size); ?> * 0.85) !important;
            }
            
            h3, .service-title, .property-card-title, .feature-title, .info-title {
                font-size: calc(<?php echo esc_attr($h3_size); ?> * 0.9) !important;
            }
            
            /* Títulos de tarjetas de propiedades - Responsive */
            .property-card-title, .property-card h3, .property-card .card-title,
            .properties-grid .property-title, .properties-grid h3 {
                font-size: calc(<?php echo esc_attr($property_card_title_size); ?> * 0.9) !important;
            }
            
            /* Títulos de secciones - Responsive tablet */
            .section-title {
                font-size: calc(<?php echo esc_attr(get_theme_mod('mjpropiedades_section_title_size', '2.5rem')); ?> * 0.8) !important;
            }
        }
        
        @media (max-width: 480px) {
            h1, .hero-title, .property-title, .section-title h1 {
                font-size: calc(<?php echo esc_attr($h1_size); ?> * 0.7) !important;
            }
            
            h2, .hero-subtitle, .section-title h2, .about-title, .contact-title, .cta-title {
                font-size: calc(<?php echo esc_attr($h2_size); ?> * 0.75) !important;
            }
            
            h3, .service-title, .property-card-title, .feature-title, .info-title {
                font-size: calc(<?php echo esc_attr($h3_size); ?> * 0.8) !important;
            }
            
            /* Títulos de tarjetas de propiedades - Responsive móvil */
            .property-card-title, .property-card h3, .property-card .card-title,
            .properties-grid .property-title, .properties-grid h3 {
                font-size: calc(<?php echo esc_attr($property_card_title_size); ?> * 0.8) !important;
            }
            
            /* Títulos de secciones - Responsive móvil */
            .section-title {
                font-size: calc(<?php echo esc_attr(get_theme_mod('mjpropiedades_section_title_size', '2.5rem')); ?> * 0.7) !important;
            }
        }
    </style>
    <?php
}
add_action('wp_head', 'mjpropiedades_typography_css');

// Agregar CSS dinámico para colores del menú
function mjpropiedades_menu_colors_css() {
    $header_bg_color = get_theme_mod('mjpropiedades_header_background_color', '#ffffff');
    $menu_text_color = get_theme_mod('mjpropiedades_menu_text_color', '#333333');
    $menu_hover_color = get_theme_mod('mjpropiedades_menu_hover_color', '#1e40af');
    $contact_button_color = get_theme_mod('mjpropiedades_contact_button_color', '#1e40af');
    $contact_button_text_color = get_theme_mod('mjpropiedades_contact_button_text_color', '#ffffff');
    
    ?>
    <style type="text/css">
        /* Color de fondo de la barra superior */
        .header {
            background-color: <?php echo esc_attr($header_bg_color); ?> !important;
        }
        
        /* Color del texto del menú */
        .nav-menu a {
            color: <?php echo esc_attr($menu_text_color); ?> !important;
        }
        
        /* Color del menú al pasar el mouse */
        .nav-menu a:hover {
            color: <?php echo esc_attr($menu_hover_color); ?> !important;
        }
        
        /* Color del botón "Contactar" */
        .contact-btn {
            background-color: <?php echo esc_attr($contact_button_color); ?> !important;
            color: <?php echo esc_attr($contact_button_text_color); ?> !important;
        }
        
        /* Color del botón "Contactar" al pasar el mouse */
        .contact-btn:hover {
            background-color: <?php echo esc_attr($contact_button_color); ?> !important;
            opacity: 0.9;
        }
        
        /* Aplicar colores también al menú móvil */
        .mobile-nav-menu a {
            color: <?php echo esc_attr($menu_text_color); ?> !important;
        }
        
        .mobile-nav-menu a:hover {
            color: <?php echo esc_attr($menu_hover_color); ?> !important;
        }
        
        .mobile-contact-btn {
            background-color: <?php echo esc_attr($contact_button_color); ?> !important;
            color: <?php echo esc_attr($contact_button_text_color); ?> !important;
        }
        
        .mobile-contact-btn:hover {
            background-color: <?php echo esc_attr($contact_button_color); ?> !important;
            opacity: 0.9;
        }
        
        /* Ajustar la sombra si el fondo es muy claro */
        <?php if (in_array($header_bg_color, ['#ffffff', '#fff', 'white', '#f8f9fa'])): ?>
        .header {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        <?php else: ?>
        .header {
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        <?php endif; ?>
        
        /* Asegurar contraste adecuado para el logo */
        .header .custom-logo-link,
        .header .logo-plus-propiedades {
            filter: none;
        }
        
        /* Ajustar el logo si el fondo es oscuro */
        <?php 
        // Función para determinar si un color es oscuro
        $rgb = array_map('hexdec', str_split(ltrim($header_bg_color, '#'), 2));
        $brightness = ($rgb[0] * 299 + $rgb[1] * 587 + $rgb[2] * 114) / 1000;
        if ($brightness < 128): // Color oscuro
        ?>
        .header .custom-logo-link,
        .header .logo-plus-propiedades {
            filter: brightness(1.2);
        }
        <?php endif; ?>
    </style>
    <?php
}
add_action('wp_head', 'mjpropiedades_menu_colors_css');

// Agregar CSS dinámico para tamaños del logo
function mjpropiedades_logo_sizes_css() {
    $logo_height_desktop = get_theme_mod('mjpropiedades_logo_height_desktop', '50');
    $logo_height_tablet = get_theme_mod('mjpropiedades_logo_height_tablet', '45');
    $logo_height_mobile = get_theme_mod('mjpropiedades_logo_height_mobile', '40');
    $logo_max_width = get_theme_mod('mjpropiedades_logo_max_width', '200');
    
    ?>
    <style type="text/css">
        /* Tamaño del logo en desktop */
        .header .custom-logo {
            max-height: <?php echo esc_attr($logo_height_desktop); ?>px !important;
            width: auto !important;
            max-width: <?php echo esc_attr($logo_max_width); ?>px !important;
            height: auto !important;
        }
        
        /* Tamaño del logo en tablet */
        @media (max-width: 1024px) {
            .header .custom-logo {
                max-height: <?php echo esc_attr($logo_height_tablet); ?>px !important;
                max-width: <?php echo esc_attr($logo_max_width); ?>px !important;
            }
        }
        
        /* Tamaño del logo en móvil */
        @media (max-width: 768px) {
            .header .custom-logo {
                max-height: <?php echo esc_attr($logo_height_mobile); ?>px !important;
                max-width: calc(100vw - 80px) !important;
            }
        }
        
        /* Ajustes para el contenedor del logo */
        .header .custom-logo-link {
            max-width: <?php echo esc_attr($logo_max_width); ?>px !important;
            height: auto !important;
            display: flex !important;
            align-items: center !important;
        }
        
        /* Asegurar que el logo se centre verticalmente */
        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        /* Ajustes responsive para el contenedor del logo */
        @media (max-width: 1024px) {
            .header .custom-logo-link {
                max-width: <?php echo esc_attr($logo_max_width); ?>px !important;
            }
        }
        
        @media (max-width: 768px) {
            .header .custom-logo-link {
                max-width: calc(100vw - 80px) !important;
            }
        }
        
        /* Ajustes para móviles pequeños */
        @media (max-width: 480px) {
            .header .custom-logo {
                max-height: <?php echo esc_attr($logo_height_mobile - 5); ?>px !important;
                max-width: calc(100vw - 60px) !important;
            }
            
            .header .custom-logo-link {
                max-width: calc(100vw - 60px) !important;
            }
        }
        
        /* Asegurar que el logo mantenga su proporción */
        .header .custom-logo {
            object-fit: contain !important;
            object-position: left center !important;
        }
        
        /* Fallback para el logo de texto (PLUS PROPIEDADES) */
        .header .logo-plus-propiedades {
            font-size: calc(<?php echo esc_attr($logo_height_desktop); ?>px * 0.6) !important;
            line-height: 1 !important;
            height: auto !important;
            max-width: <?php echo esc_attr($logo_max_width); ?>px !important;
        }
        
        @media (max-width: 1024px) {
            .header .logo-plus-propiedades {
                font-size: calc(<?php echo esc_attr($logo_height_tablet); ?>px * 0.6) !important;
            }
        }
        
        @media (max-width: 768px) {
            .header .logo-plus-propiedades {
                font-size: calc(<?php echo esc_attr($logo_height_mobile); ?>px * 0.6) !important;
                max-width: calc(100vw - 80px) !important;
            }
        }
        
        @media (max-width: 480px) {
            .header .logo-plus-propiedades {
                font-size: calc(<?php echo esc_attr($logo_height_mobile - 5); ?>px * 0.6) !important;
                max-width: calc(100vw - 60px) !important;
            }
        }
    </style>
    <?php
}
add_action('wp_head', 'mjpropiedades_logo_sizes_css');

// Agregar CSS dinámico para el logo del footer
function mjpropiedades_footer_logo_css() {
    $footer_logo_size = get_theme_mod('mjpropiedades_footer_logo_size', 'medium');
    $footer_logo_custom_size = get_theme_mod('mjpropiedades_footer_logo_custom_size', '80');
    $footer_logo_position = get_theme_mod('mjpropiedades_footer_logo_position', 'left');
    $footer_logo_show_text = get_theme_mod('mjpropiedades_footer_logo_show_text', true);
    
    // Determinar el tamaño final
    $final_size = $footer_logo_size === 'custom' ? $footer_logo_custom_size : 
                  ($footer_logo_size === 'small' ? 60 : 
                   ($footer_logo_size === 'large' ? 120 : 80));
    
    ?>
    <style type="text/css">
        /* Estilos para el logo del footer */
        .footer-brand {
            text-align: <?php echo esc_attr($footer_logo_position); ?> !important;
        }
        
        .footer-logo img,
        .footer-logo-text {
            max-height: <?php echo esc_attr($final_size); ?>px !important;
            width: auto !important;
            max-width: <?php echo esc_attr($final_size * 2); ?>px !important;
            height: auto !important;
            object-fit: contain !important;
        }
        
        /* Ajustes para el texto descriptivo */
        <?php if (!$footer_logo_show_text): ?>
        .footer-brand p {
            display: none !important;
        }
        <?php endif; ?>
        
        /* Centrado del contenido del footer-brand cuando está centrado */
        <?php if ($footer_logo_position === 'center'): ?>
        .footer-brand {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
        }
        
        .footer-brand .social-icons {
            justify-content: center !important;
        }
        <?php elseif ($footer_logo_position === 'right'): ?>
        .footer-brand {
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-end !important;
        }
        
        .footer-brand .social-icons {
            justify-content: flex-end !important;
        }
        <?php else: ?>
        .footer-brand {
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-start !important;
        }
        
        .footer-brand .social-icons {
            justify-content: flex-start !important;
        }
        <?php endif; ?>
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .footer-logo img,
            .footer-logo-text {
                max-height: <?php echo esc_attr($final_size * 0.8); ?>px !important;
                max-width: <?php echo esc_attr($final_size * 1.6); ?>px !important;
            }
        }
        
        @media (max-width: 480px) {
            .footer-logo img,
            .footer-logo-text {
                max-height: <?php echo esc_attr($final_size * 0.7); ?>px !important;
                max-width: <?php echo esc_attr($final_size * 1.4); ?>px !important;
            }
            
            /* En móviles, siempre centrar el logo */
            .footer-brand {
                text-align: center !important;
                align-items: center !important;
            }
            
            .footer-brand .social-icons {
                justify-content: center !important;
            }
        }
        
        /* Asegurar que el logo mantenga proporciones */
        .footer-logo img {
            object-fit: contain !important;
            object-position: <?php echo esc_attr($footer_logo_position); ?> center !important;
        }
        
        /* Estilos para el fallback de texto */
        .footer-logo-text .logo-plus-propiedades {
            font-size: <?php echo esc_attr($final_size * 0.3); ?>px !important;
            line-height: 1 !important;
            height: auto !important;
            max-width: <?php echo esc_attr($final_size * 2); ?>px !important;
            display: inline-block !important;
        }
        
        @media (max-width: 768px) {
            .footer-logo-text .logo-plus-propiedades {
                font-size: <?php echo esc_attr($final_size * 0.25); ?>px !important;
            }
        }
        
        @media (max-width: 480px) {
            .footer-logo-text .logo-plus-propiedades {
                font-size: <?php echo esc_attr($final_size * 0.2); ?>px !important;
            }
        }
    </style>
    <?php
}
add_action('wp_head', 'mjpropiedades_footer_logo_css');

// Agregar JavaScript para vista previa en tiempo real en el Customizer
function mjpropiedades_customizer_preview_js() {
    ?>
    <script type="text/javascript">
    (function($) {
        'use strict';
        
        // Vista previa de tamaños de logo
        wp.customize('mjpropiedades_logo_height_desktop', function(value) {
            value.bind(function(newval) {
                $('.header .custom-logo').css('max-height', newval + 'px');
                $('.header .logo-plus-propiedades').css('font-size', 'calc(' + newval + 'px * 0.6)');
            });
        });
        
        wp.customize('mjpropiedades_logo_height_tablet', function(value) {
            value.bind(function(newval) {
                if ($(window).width() <= 1024) {
                    $('.header .custom-logo').css('max-height', newval + 'px');
                    $('.header .logo-plus-propiedades').css('font-size', 'calc(' + newval + 'px * 0.6)');
                }
            });
        });
        
        wp.customize('mjpropiedades_logo_height_mobile', function(value) {
            value.bind(function(newval) {
                if ($(window).width() <= 768) {
                    $('.header .custom-logo').css('max-height', newval + 'px');
                    $('.header .logo-plus-propiedades').css('font-size', 'calc(' + newval + 'px * 0.6)');
                }
            });
        });
        
        wp.customize('mjpropiedades_logo_max_width', function(value) {
            value.bind(function(newval) {
                $('.header .custom-logo, .header .custom-logo-link, .header .logo-plus-propiedades').css('max-width', newval + 'px');
            });
        });
        
        // Vista previa de colores del menú
        wp.customize('mjpropiedades_header_background_color', function(value) {
            value.bind(function(newval) {
                $('.header').css('background-color', newval);
            });
        });
        
        wp.customize('mjpropiedades_menu_text_color', function(value) {
            value.bind(function(newval) {
                $('.nav-menu a, .mobile-nav-menu a').css('color', newval);
            });
        });
        
        wp.customize('mjpropiedades_menu_hover_color', function(value) {
            value.bind(function(newval) {
                $('.nav-menu a:hover, .mobile-nav-menu a:hover').css('color', newval);
            });
        });
        
        wp.customize('mjpropiedades_contact_button_color', function(value) {
            value.bind(function(newval) {
                $('.contact-btn, .mobile-contact-btn').css('background-color', newval);
            });
        });
        
        wp.customize('mjpropiedades_contact_button_text_color', function(value) {
            value.bind(function(newval) {
                $('.contact-btn, .mobile-contact-btn').css('color', newval);
            });
        });
        
        // Vista previa del logo del footer
        wp.customize('mjpropiedades_footer_logo_image', function(value) {
            value.bind(function(newval) {
                if (newval) {
                    // Si hay una imagen seleccionada, actualizar el logo del footer
                    var logoUrl = wp.media.attachment(newval).get('url');
                    $('.footer-logo img').attr('src', logoUrl);
                    $('.footer-logo').show();
                    $('.footer-logo-text').hide();
                } else {
                    // Si no hay imagen, mostrar el texto del logo
                    $('.footer-logo').hide();
                    $('.footer-logo-text').show();
                }
            });
        });
        
        wp.customize('mjpropiedades_footer_logo_position', function(value) {
            value.bind(function(newval) {
                $('.footer-brand').css('text-align', newval);
                if (newval === 'center') {
                    $('.footer-brand').css({
                        'display': 'flex',
                        'flex-direction': 'column',
                        'align-items': 'center'
                    });
                    $('.footer-brand .social-icons').css('justify-content', 'center');
                } else if (newval === 'right') {
                    $('.footer-brand').css({
                        'display': 'flex',
                        'flex-direction': 'column',
                        'align-items': 'flex-end'
                    });
                    $('.footer-brand .social-icons').css('justify-content', 'flex-end');
                } else {
                    $('.footer-brand').css({
                        'display': 'flex',
                        'flex-direction': 'column',
                        'align-items': 'flex-start'
                    });
                    $('.footer-brand .social-icons').css('justify-content', 'flex-start');
                }
            });
        });
        
        wp.customize('mjpropiedades_footer_logo_size', function(value) {
            value.bind(function(newval) {
                var size = newval === 'small' ? 60 : newval === 'large' ? 120 : 80;
                $('.footer-logo img, .footer-logo-text').css({
                    'max-height': size + 'px',
                    'max-width': (size * 2) + 'px'
                });
                $('.footer-logo-text .logo-plus-propiedades').css('font-size', (size * 0.3) + 'px');
            });
        });
        
        wp.customize('mjpropiedades_footer_logo_custom_size', function(value) {
            value.bind(function(newval) {
                var size = parseInt(newval);
                $('.footer-logo img, .footer-logo-text').css({
                    'max-height': size + 'px',
                    'max-width': (size * 2) + 'px'
                });
                $('.footer-logo-text .logo-plus-propiedades').css('font-size', (size * 0.3) + 'px');
            });
        });
        
        wp.customize('mjpropiedades_footer_logo_show_text', function(value) {
            value.bind(function(newval) {
                if (newval) {
                    $('.footer-brand p').show();
                } else {
                    $('.footer-brand p').hide();
                }
            });
        });
        
        wp.customize('mjpropiedades_footer_logo_text', function(value) {
            value.bind(function(newval) {
                $('.footer-brand p').text(newval);
            });
        });
        
    })(jQuery);
    </script>
    <?php
}
add_action('customize_preview_init', 'mjpropiedades_customizer_preview_js');


// Función de sanitización para la alineación del menú
function mjpropiedades_sanitize_menu_alignment($input) {
    $valid = array('left', 'center', 'right');
    return in_array($input, $valid) ? $input : 'right';
}

// Función de sanitización para la alineación de texto
function mjpropiedades_sanitize_text_alignment($input) {
    $valid = array('left', 'center', 'right');
    return in_array($input, $valid) ? $input : 'center';
}

// Función de sanitización para los presets de colores
function mjpropiedades_sanitize_color_preset($input) {
    $valid = array('default', 'blue', 'dark', 'light', 'green', 'purple', 'orange');
    return in_array($input, $valid) ? $input : 'default';
}

// Función de sanitización para los presets de tamaño de logo
function mjpropiedades_sanitize_logo_size_preset($input) {
    $valid = array('small', 'medium', 'large', 'custom');
    return in_array($input, $valid) ? $input : 'medium';
}

// Función de sanitización para la posición del logo del footer
function mjpropiedades_sanitize_logo_position($input) {
    $valid = array('left', 'center', 'right');
    return in_array($input, $valid) ? $input : 'left';
}

// Función de sanitización para el tamaño del logo del footer
function mjpropiedades_sanitize_footer_logo_size($input) {
    $valid = array('small', 'medium', 'large', 'custom');
    return in_array($input, $valid) ? $input : 'medium';
}

// Función para aplicar presets de colores automáticamente
function mjpropiedades_apply_color_preset() {
    $preset = get_theme_mod('mjpropiedades_menu_color_preset', 'default');
    
    if ($preset === 'default') {
        return; // No aplicar cambios si es personalizado
    }
    
    $color_schemes = array(
        'blue' => array(
            'header_bg' => '#1e40af',
            'menu_text' => '#ffffff',
            'menu_hover' => '#93c5fd',
            'button_bg' => '#3b82f6',
            'button_text' => '#ffffff'
        ),
        'dark' => array(
            'header_bg' => '#1f2937',
            'menu_text' => '#ffffff',
            'menu_hover' => '#60a5fa',
            'button_bg' => '#374151',
            'button_text' => '#ffffff'
        ),
        'light' => array(
            'header_bg' => '#f8fafc',
            'menu_text' => '#475569',
            'menu_hover' => '#1e40af',
            'button_bg' => '#64748b',
            'button_text' => '#ffffff'
        ),
        'green' => array(
            'header_bg' => '#059669',
            'menu_text' => '#ffffff',
            'menu_hover' => '#a7f3d0',
            'button_bg' => '#10b981',
            'button_text' => '#ffffff'
        ),
        'purple' => array(
            'header_bg' => '#7c3aed',
            'menu_text' => '#ffffff',
            'menu_hover' => '#c4b5fd',
            'button_bg' => '#8b5cf6',
            'button_text' => '#ffffff'
        ),
        'orange' => array(
            'header_bg' => '#ea580c',
            'menu_text' => '#ffffff',
            'menu_hover' => '#fed7aa',
            'button_bg' => '#f97316',
            'button_text' => '#ffffff'
        )
    );
    
    if (isset($color_schemes[$preset])) {
        $scheme = $color_schemes[$preset];
        set_theme_mod('mjpropiedades_header_background_color', $scheme['header_bg']);
        set_theme_mod('mjpropiedades_menu_text_color', $scheme['menu_text']);
        set_theme_mod('mjpropiedades_menu_hover_color', $scheme['menu_hover']);
        set_theme_mod('mjpropiedades_contact_button_color', $scheme['button_bg']);
        set_theme_mod('mjpropiedades_contact_button_text_color', $scheme['button_text']);
    }
}
add_action('customize_save_after', 'mjpropiedades_apply_color_preset');

// Función para aplicar presets de tamaño de logo automáticamente
function mjpropiedades_apply_logo_size_preset() {
    $preset = get_theme_mod('mjpropiedades_logo_size_preset', 'medium');
    
    if ($preset === 'custom') {
        return; // No aplicar cambios si es personalizado
    }
    
    $size_schemes = array(
        'small' => array(
            'desktop' => 30,
            'tablet'  => 28,
            'mobile'  => 25,
            'width'   => 120
        ),
        'medium' => array(
            'desktop' => 50,
            'tablet'  => 45,
            'mobile'  => 40,
            'width'   => 200
        ),
        'large' => array(
            'desktop' => 70,
            'tablet'  => 60,
            'mobile'  => 50,
            'width'   => 280
        )
    );
    
    if (isset($size_schemes[$preset])) {
        $scheme = $size_schemes[$preset];
        set_theme_mod('mjpropiedades_logo_height_desktop', $scheme['desktop']);
        set_theme_mod('mjpropiedades_logo_height_tablet', $scheme['tablet']);
        set_theme_mod('mjpropiedades_logo_height_mobile', $scheme['mobile']);
        set_theme_mod('mjpropiedades_logo_max_width', $scheme['width']);
    }
}
add_action('customize_save_after', 'mjpropiedades_apply_logo_size_preset');

// Función para aplicar presets de tamaño del logo del footer automáticamente
function mjpropiedades_apply_footer_logo_size_preset() {
    $preset = get_theme_mod('mjpropiedades_footer_logo_size', 'medium');
    
    if ($preset === 'custom') {
        return; // No aplicar cambios si es personalizado
    }
    
    $size_schemes = array(
        'small' => 60,
        'medium' => 80,
        'large' => 120
    );
    
    if (isset($size_schemes[$preset])) {
        set_theme_mod('mjpropiedades_footer_logo_custom_size', $size_schemes[$preset]);
    }
}
add_action('customize_save_after', 'mjpropiedades_apply_footer_logo_size_preset');

// Función para manejar plantillas de página
function mjpropiedades_page_template($template) {
    // Removido el manejo de página 'propiedades' para evitar conflictos con archive
    if (is_page('inicio')) {
        $new_template = locate_template(array('page-inicio.php'));
        if (!empty($new_template)) {
            return $new_template;
        }
    }
    return $template;
}

// Agregar opciones de plantilla en el editor de páginas
function mjpropiedades_add_page_template_metabox() {
    add_meta_box(
        'page_template_selector',
        'Plantilla de Página',
        'mjpropiedades_page_template_metabox_callback',
        'page',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'mjpropiedades_add_page_template_metabox');

function mjpropiedades_page_template_metabox_callback($post) {
    $templates = array(
        'default' => 'Plantilla por defecto',
        'inicio' => 'Página de Inicio'
        // Removido 'propiedades' para evitar conflictos
    );
    
    $current_template = get_post_meta($post->ID, '_wp_page_template', true);
    if (empty($current_template)) {
        $current_template = 'default';
    }
    
    echo '<select name="page_template_select">';
    foreach ($templates as $value => $label) {
        $selected = ($current_template === $value) ? 'selected' : '';
        echo '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
    }
    echo '</select>';
}

// Guardar selección de plantilla
function mjpropiedades_save_page_template($post_id) {
    if (isset($_POST['page_template_select'])) {
        update_post_meta($post_id, '_wp_page_template', sanitize_text_field($_POST['page_template_select']));
    }
}
add_action('save_post', 'mjpropiedades_save_page_template');


// Generar sitemap XML
function mjpropiedades_generate_sitemap() {
    if (isset($_GET['sitemap']) && $_GET['sitemap'] == 'xml') {
        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        ?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
            <url>
                <loc><?php echo home_url('/'); ?></loc>
                <lastmod><?php echo date('Y-m-d'); ?></lastmod>
                <changefreq>daily</changefreq>
                <priority>1.0</priority>
            </url>
            <url>
                <loc><?php echo home_url('/propiedades/'); ?></loc>
                <lastmod><?php echo date('Y-m-d'); ?></lastmod>
                <changefreq>daily</changefreq>
                <priority>0.8</priority>
            </url>
            <url>
                <loc><?php echo home_url('/propiedades/?operacion=venta'); ?></loc>
                <lastmod><?php echo date('Y-m-d'); ?></lastmod>
                <changefreq>daily</changefreq>
                <priority>0.7</priority>
            </url>
            <url>
                <loc><?php echo home_url('/propiedades/?operacion=arriendo'); ?></loc>
                <lastmod><?php echo date('Y-m-d'); ?></lastmod>
                <changefreq>daily</changefreq>
                <priority>0.7</priority>
            </url>
            <url>
                <loc><?php echo home_url('/contacto/'); ?></loc>
                <lastmod><?php echo date('Y-m-d'); ?></lastmod>
                <changefreq>monthly</changefreq>
                <priority>0.6</priority>
            </url>
            <url>
                <loc><?php echo home_url('/sobre-nosotros/'); ?></loc>
                <lastmod><?php echo date('Y-m-d'); ?></lastmod>
                <changefreq>monthly</changefreq>
                <priority>0.5</priority>
            </url>
            <?php
            // Agregar propiedades dinámicamente
            $properties = get_posts(array(
                'post_type' => 'propiedad',
                'posts_per_page' => -1,
                'post_status' => 'publish'
            ));
            
            foreach ($properties as $property) {
                $lastmod = get_the_modified_date('Y-m-d', $property->ID);
                echo '<url>';
                echo '<loc>' . get_permalink($property->ID) . '</loc>';
                echo '<lastmod>' . $lastmod . '</lastmod>';
                echo '<changefreq>weekly</changefreq>';
                echo '<priority>0.6</priority>';
                echo '</url>';
            }
            ?>
        </urlset>
        <?php
        exit;
    }
}
add_action('init', 'mjpropiedades_generate_sitemap');

// Crear página de propiedades automáticamente
function mjpropiedades_create_properties_page() {
    $page_title = 'Propiedades';
    $page_slug = 'propiedades';
    
    // Verificar si la página ya existe
    $existing_page = get_page_by_path($page_slug);
    
    if (!$existing_page) {
        // Crear la página
        $page_data = array(
            'post_title' => $page_title,
            'post_name' => $page_slug,
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1,
            'page_template' => 'page-propiedades.php'
        );
        
        $page_id = wp_insert_post($page_data);
        
        if ($page_id) {
            // Configurar la página para usar el template personalizado
            update_post_meta($page_id, '_wp_page_template', 'page-propiedades.php');
        }
    }
}
// add_action('after_setup_theme', 'mjpropiedades_create_properties_page'); // Desactivado para evitar conflictos

// Función para eliminar la página "propiedades" si existe (para evitar conflictos)
function mjpropiedades_cleanup_properties_page() {
    $propiedades_page = get_page_by_path('propiedades');
    if ($propiedades_page) {
        // Eliminar la página para evitar conflictos con el archive
        wp_delete_post($propiedades_page->ID, true);
    }
}
add_action('after_setup_theme', 'mjpropiedades_cleanup_properties_page');

// Redirección para asegurar que todo vaya al archive de propiedades
function mjpropiedades_redirect_to_archive() {
    // Si alguien intenta acceder a /propiedades/, redirigir a /propiedad/ preservando parámetros
    if (is_page('propiedades')) {
        $archive_url = get_post_type_archive_link('propiedad');
        
        // Preservar todos los parámetros GET
        $query_params = $_GET;
        if (!empty($query_params)) {
            $archive_url = add_query_arg($query_params, $archive_url);
        }
        
        wp_redirect($archive_url, 301);
        exit;
    }
}
add_action('template_redirect', 'mjpropiedades_redirect_to_archive');

// Función para limpiar parámetros duplicados de paginación
function mjpropiedades_clean_pagination_params() {
    // Si tenemos tanto 'paged' como 'page', mantener solo 'page'
    if (isset($_GET['paged']) && isset($_GET['page'])) {
        $current_url = remove_query_arg('paged');
        wp_redirect($current_url, 301);
        exit;
    }
}
add_action('template_redirect', 'mjpropiedades_clean_pagination_params');

// Crear propiedades de prueba (solo si no existen)
function mjpropiedades_create_sample_properties() {
    // Verificar si ya existen propiedades
    $existing_properties = get_posts(array(
        'post_type' => 'propiedad',
        'posts_per_page' => 1,
        'post_status' => 'publish'
    ));
    
    if (!empty($existing_properties)) {
        return; // Ya existen propiedades, no crear más
    }
    
    // Crear propiedades de prueba
    $sample_properties = array(
        array(
            'title' => 'Casa en La Serena',
            'precio' => 150000000,
            'dormitorios' => 3,
            'banos' => 2,
            'comuna' => 'La Serena',
            'tipo' => 'casa',
            'operacion' => 'venta'
        ),
        array(
            'title' => 'Departamento en Coquimbo',
            'precio' => 120000000,
            'dormitorios' => 2,
            'banos' => 2,
            'comuna' => 'Coquimbo',
            'tipo' => 'departamento',
            'operacion' => 'venta'
        ),
        array(
            'title' => 'Casa en Ovalle',
            'precio' => 80000000,
            'dormitorios' => 4,
            'banos' => 3,
            'comuna' => 'Ovalle',
            'tipo' => 'casa',
            'operacion' => 'venta'
        ),
        array(
            'title' => 'Departamento en La Serena Centro',
            'precio' => 180000000,
            'dormitorios' => 3,
            'banos' => 2,
            'comuna' => 'La Serena',
            'tipo' => 'departamento',
            'operacion' => 'venta'
        )
    );
    
    foreach ($sample_properties as $property) {
        $post_id = wp_insert_post(array(
            'post_title' => $property['title'],
            'post_type' => 'propiedad',
            'post_status' => 'publish',
            'post_content' => 'Propiedad de prueba creada automáticamente.'
        ));
        
        if ($post_id) {
            // Agregar metadatos
            update_post_meta($post_id, '_propiedad_precio', $property['precio']);
            update_post_meta($post_id, '_propiedad_dormitorios', $property['dormitorios']);
            update_post_meta($post_id, '_propiedad_banos', $property['banos']);
            update_post_meta($post_id, '_propiedad_comuna', $property['comuna']);
            update_post_meta($post_id, '_propiedad_tipo', $property['tipo']);
            update_post_meta($post_id, '_propiedad_operacion', $property['operacion']);
        }
    }
}
add_action('after_setup_theme', 'mjpropiedades_create_sample_properties');

// Configurar opciones por defecto para búsqueda
function mjpropiedades_set_default_search_options() {
    // Tipos de propiedad por defecto
    if (!get_option('mjpropiedades_tipos_propiedad')) {
        $tipos_propiedad = array(
            'casa' => 'Casa',
            'departamento' => 'Departamento',
            'terreno' => 'Terreno',
            'local' => 'Local Comercial',
            'oficina' => 'Oficina'
        );
        update_option('mjpropiedades_tipos_propiedad', $tipos_propiedad);
    }
    
    // Comunas por defecto
    if (!get_option('mjpropiedades_comunas')) {
        $comunas = array(
            'la-serena' => 'La Serena',
            'coquimbo' => 'Coquimbo',
            'ovalle' => 'Ovalle',
            'vicuna' => 'Vicuña',
            'andacollo' => 'Andacollo',
            'combarbala' => 'Combarbalá',
            'monte-patria' => 'Monte Patria',
            'punitaqui' => 'Punitaqui',
            'rio-hurtado' => 'Río Hurtado',
            'salamanca' => 'Salamanca'
        );
        update_option('mjpropiedades_comunas', $comunas);
    }
    
    // Dormitorios por defecto
    if (!get_option('mjpropiedades_dormitorios')) {
        $dormitorios = array(
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5+' => '5+'
        );
        update_option('mjpropiedades_dormitorios', $dormitorios);
    }
    
    // Baños por defecto
    if (!get_option('mjpropiedades_banos')) {
        $banos = array(
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5+' => '5+'
        );
        update_option('mjpropiedades_banos', $banos);
    }
}
add_action('after_setup_theme', 'mjpropiedades_set_default_search_options');

// Función para manejar búsqueda de propiedades
function mjpropiedades_search_properties() {
    // Detectar si es mobile usando User Agent
    $is_mobile = wp_is_mobile();
    
    // Ajustar número de propiedades por página según el dispositivo
    $posts_per_page = $is_mobile ? 4 : 12;
    
    $args = array(
        'post_type' => 'propiedad',
        'posts_per_page' => $posts_per_page,
        'paged' => isset($_GET['page']) ? intval($_GET['page']) : 1,
        'meta_query' => array()
    );
    
    // Aplicar ordenamiento
    if (isset($_GET['sort']) && !empty($_GET['sort'])) {
        $sort = sanitize_text_field($_GET['sort']);
        
        switch ($sort) {
            case 'price-asc':
                $args['meta_key'] = '_propiedad_precio';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'ASC';
                break;
            case 'price-desc':
                $args['meta_key'] = '_propiedad_precio';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
            case 'title-asc':
                $args['orderby'] = 'title';
                $args['order'] = 'ASC';
                break;
            case 'date-desc':
            default:
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
                break;
        }
    } else {
        // Ordenamiento por defecto: más recientes primero
        $args['orderby'] = 'date';
        $args['order'] = 'DESC';
    }
    
    // Aplicar filtros
    if (isset($_GET['tipo_propiedad']) && !empty($_GET['tipo_propiedad'])) {
        $args['meta_query'][] = array(
            'key' => '_propiedad_tipo',
            'value' => sanitize_text_field($_GET['tipo_propiedad']),
            'compare' => '='
        );
    }
    
    if (isset($_GET['ubicacion']) && !empty($_GET['ubicacion'])) {
        // Convertir el valor de ubicación a formato de comuna
        $comunas = get_option('mjpropiedades_comunas', array());
        $comuna_label = isset($comunas[$_GET['ubicacion']]) ? $comunas[$_GET['ubicacion']] : $_GET['ubicacion'];
        
        $args['meta_query'][] = array(
            'key' => '_propiedad_comuna',
            'value' => sanitize_text_field($comuna_label),
            'compare' => 'LIKE'
        );
    }
    
    if (isset($_GET['dormitorios']) && !empty($_GET['dormitorios'])) {
        $dormitorios_value = sanitize_text_field($_GET['dormitorios']);
        if ($dormitorios_value === '5+') {
            $args['meta_query'][] = array(
                'key' => '_propiedad_dormitorios',
                'value' => 5,
                'compare' => '>=',
                'type' => 'NUMERIC'
            );
        } else {
            $args['meta_query'][] = array(
                'key' => '_propiedad_dormitorios',
                'value' => intval($dormitorios_value),
                'compare' => '>=',
                'type' => 'NUMERIC'
            );
        }
    }
    
    if (isset($_GET['banos']) && !empty($_GET['banos'])) {
        $banos_value = sanitize_text_field($_GET['banos']);
        if ($banos_value === '5+') {
            $args['meta_query'][] = array(
                'key' => '_propiedad_banos',
                'value' => 5,
                'compare' => '>=',
                'type' => 'NUMERIC'
            );
        } else {
            $args['meta_query'][] = array(
                'key' => '_propiedad_banos',
                'value' => intval($banos_value),
                'compare' => '>=',
                'type' => 'NUMERIC'
            );
        }
    }
    
    // Filtro de precio: manejar rangos correctamente
    $precio_min = isset($_GET['precio_min']) && !empty($_GET['precio_min']) ? intval($_GET['precio_min']) : 0;
    $precio_max = isset($_GET['precio_max']) && !empty($_GET['precio_max']) ? intval($_GET['precio_max']) : 0;
    
    if ($precio_min > 0 && $precio_max > 0) {
        // Si hay ambos valores, usar BETWEEN
        $args['meta_query'][] = array(
            'key' => '_propiedad_precio',
            'value' => array($precio_min, $precio_max),
            'compare' => 'BETWEEN',
            'type' => 'NUMERIC'
        );
    } elseif ($precio_min > 0) {
        // Solo precio mínimo
        $args['meta_query'][] = array(
            'key' => '_propiedad_precio',
            'value' => $precio_min,
            'compare' => '>=',
            'type' => 'NUMERIC'
        );
    } elseif ($precio_max > 0) {
        // Solo precio máximo
        $args['meta_query'][] = array(
            'key' => '_propiedad_precio',
            'value' => $precio_max,
            'compare' => '<=',
            'type' => 'NUMERIC'
        );
    }
    
    return new WP_Query($args);
}

// ===== FORMULARIO DE BÚSQUEDA REUTILIZABLE =====

/**
 * Genera el formulario de búsqueda de propiedades
 * @param array $args Argumentos de configuración del formulario
 * @return string HTML del formulario
 */
function mjpropiedades_get_search_form($args = array()) {
    // Valores por defecto
    $defaults = array(
        'action' => home_url('/propiedades/'),
        'method' => 'get',
        'class' => 'search-form',
        'show_title' => true,
        'title' => 'Encuentra tu Propiedad Ideal',
        'button_text' => 'Buscar Propiedades',
        'preserve_values' => true,
        'form_id' => 'search-form-' . uniqid()
    );
    
    $args = wp_parse_args($args, $defaults);
    
    // Obtener valores actuales si se deben preservar
    $current_values = array();
    if ($args['preserve_values']) {
        $current_values = array(
            'tipo_propiedad' => isset($_GET['tipo_propiedad']) ? $_GET['tipo_propiedad'] : '',
            'ubicacion' => isset($_GET['ubicacion']) ? $_GET['ubicacion'] : '',
            'dormitorios' => isset($_GET['dormitorios']) ? $_GET['dormitorios'] : '',
            'banos' => isset($_GET['banos']) ? $_GET['banos'] : '',
            'precio_min' => isset($_GET['precio_min']) ? $_GET['precio_min'] : '0',
            'precio_max' => isset($_GET['precio_max']) ? $_GET['precio_max'] : '1000000000'
        );
    }
    
    // Obtener opciones de configuración
    $tipos_propiedad = get_option('mjpropiedades_tipos_propiedad', array(
        'casa' => 'Casa',
        'departamento' => 'Departamento',
        'oficina' => 'Oficina',
        'local' => 'Local Comercial',
        'terreno' => 'Terreno'
    ));
    
    $comunas = get_option('mjpropiedades_comunas', array(
        'la-serena' => 'La Serena',
        'coquimbo' => 'Coquimbo',
        'ovalle' => 'Ovalle',
        'vicuna' => 'Vicuña',
        'paihuano' => 'Paihuano',
        'andacollo' => 'Andacollo',
        'combarbala' => 'Combarbalá',
        'monte-patri' => 'Monte Patria',
        'punitaqui' => 'Punitaqui',
        'rio-hurtado' => 'Río Hurtado'
    ));
    
    $dormitorios_options = get_option('mjpropiedades_dormitorios', array(
        '1' => '1 dormitorio',
        '2' => '2 dormitorios',
        '3' => '3 dormitorios',
        '4' => '4 dormitorios',
        '5+' => '5+ dormitorios'
    ));
    
    $banos_options = get_option('mjpropiedades_banos', array(
        '1' => '1 baño',
        '2' => '2 baños',
        '3' => '3 baños',
        '4' => '4 baños',
        '5+' => '5+ baños'
    ));
    
    ob_start();
    ?>
        <div class="search-form-container <?php echo (isset($args['form_id']) && $args['form_id'] === 'properties-search-form') ? 'search-form-clean' : ''; ?>">
            <?php if ($args['show_title']): ?>
            <div class="search-header">
                <h2 class="section-title"><?php echo esc_html($args['title']); ?></h2>
            </div>
            <?php endif; ?>
        
        <form id="<?php echo esc_attr($args['form_id']); ?>" class="<?php echo esc_attr($args['class']); ?>" method="<?php echo esc_attr($args['method']); ?>" action="<?php echo esc_url($args['action']); ?>">
            <!-- Primera fila: Todos los select en una fila -->
            <div class="search-form-row select-row">
                <div class="search-group">
                    <label for="<?php echo esc_attr($args['form_id']); ?>-tipo-propiedad" class="search-label">Tipo de Propiedad</label>
                    <select id="<?php echo esc_attr($args['form_id']); ?>-tipo-propiedad" name="tipo_propiedad" class="search-select">
                        <option value="">Todos los tipos</option>
                        <?php foreach ($tipos_propiedad as $value => $label): ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($current_values['tipo_propiedad'], $value); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="search-group">
                    <label for="<?php echo esc_attr($args['form_id']); ?>-ubicacion" class="search-label">Ubicación</label>
                    <select id="<?php echo esc_attr($args['form_id']); ?>-ubicacion" name="ubicacion" class="search-select">
                        <option value="">Seleccionar comuna</option>
                        <?php foreach ($comunas as $value => $label): ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($current_values['ubicacion'], $value); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="search-group">
                    <label for="<?php echo esc_attr($args['form_id']); ?>-dormitorios" class="search-label">Dormitorios</label>
                    <select id="<?php echo esc_attr($args['form_id']); ?>-dormitorios" name="dormitorios" class="search-select">
                        <option value="">Cualquier cantidad</option>
                        <?php foreach ($dormitorios_options as $value => $label): ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($current_values['dormitorios'], $value); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="search-group">
                    <label for="<?php echo esc_attr($args['form_id']); ?>-banos" class="search-label">Baños</label>
                    <select id="<?php echo esc_attr($args['form_id']); ?>-banos" name="banos" class="search-select">
                        <option value="">Cualquier cantidad</option>
                        <?php foreach ($banos_options as $value => $label): ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($current_values['banos'], $value); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <!-- Segunda fila: Sliders de precio -->
            <div class="search-form-row price-row">
                <div class="search-group price-group">
                    <label for="<?php echo esc_attr($args['form_id']); ?>-precio-min" class="search-label">Precio Mínimo (CLP)</label>
                    <div class="price-slider-container">
                        <input type="range" id="<?php echo esc_attr($args['form_id']); ?>-precio-min" name="precio_min" class="price-slider" min="0" max="1000000000" value="<?php echo esc_attr($current_values['precio_min']); ?>" step="100000">
                        <div class="price-display">
                            <span class="price-value" id="<?php echo esc_attr($args['form_id']); ?>-precio-min-value">$<?php echo number_format(floatval($current_values['precio_min']), 0, ',', '.'); ?></span>
                            <span class="price-max">$1.000.000.000</span>
                        </div>
                    </div>
                </div>
                
                <div class="search-group price-group">
                    <label for="<?php echo esc_attr($args['form_id']); ?>-precio-max" class="search-label">Precio Máximo (CLP)</label>
                    <div class="price-slider-container">
                        <input type="range" id="<?php echo esc_attr($args['form_id']); ?>-precio-max" name="precio_max" class="price-slider" min="0" max="1000000000" value="<?php echo esc_attr($current_values['precio_max']); ?>" step="100000">
                        <div class="price-display">
                            <span class="price-min">$0</span>
                            <span class="price-value" id="<?php echo esc_attr($args['form_id']); ?>-precio-max-value">$<?php echo number_format(floatval($current_values['precio_max']), 0, ',', '.'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Botón de búsqueda -->
            <div class="search-actions">
                <button type="submit" class="search-btn">
                    <?php echo esc_html($args['button_text']); ?>
                </button>
            </div>
        </form>
    </div>
    
    <script>
    // Script para actualizar los valores de precio en tiempo real
    document.addEventListener('DOMContentLoaded', function() {
        const formId = '<?php echo esc_js($args['form_id']); ?>';
        const minSlider = document.getElementById(formId + '-precio-min');
        const maxSlider = document.getElementById(formId + '-precio-max');
        const minValue = document.getElementById(formId + '-precio-min-value');
        const maxValue = document.getElementById(formId + '-precio-max-value');
        
        function formatPrice(price) {
            return '$' + new Intl.NumberFormat('es-CL').format(price);
        }
        
        if (minSlider && minValue) {
            minSlider.addEventListener('input', function() {
                minValue.textContent = formatPrice(this.value);
                // Asegurar que el mínimo no sea mayor que el máximo
                if (parseInt(this.value) > parseInt(maxSlider.value)) {
                    maxSlider.value = this.value;
                    maxValue.textContent = formatPrice(this.value);
                }
            });
        }
        
        if (maxSlider && maxValue) {
            maxSlider.addEventListener('input', function() {
                maxValue.textContent = formatPrice(this.value);
                // Asegurar que el máximo no sea menor que el mínimo
                if (parseInt(this.value) < parseInt(minSlider.value)) {
                    minSlider.value = this.value;
                    minValue.textContent = formatPrice(this.value);
                }
            });
        }
    });
    </script>
    <?php
    
    return ob_get_clean();
}

// ===== CONFIGURACIÓN DE COLORES PARA TARJETAS DE PROPIEDADES =====

// Agregar sección de colores de tarjetas al Customizer
function mjpropiedades_property_cards_customizer($wp_customize) {
    
    // Sección para colores de tarjetas de propiedades
    $wp_customize->add_section('mjpropiedades_property_cards_colors', array(
        'title' => __('Colores de Tarjetas de Propiedades', 'mjpropiedades'),
        'description' => __('Configura los colores para las tarjetas de propiedades', 'mjpropiedades'),
        'priority' => 36,
        'capability' => 'edit_theme_options',
        'active_callback' => '__return_true',
    ));
    
    // Color de fondo de la tarjeta
    $wp_customize->add_setting('mjpropiedades_card_background', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_card_background', array(
        'label' => __('Color de Fondo de la Tarjeta', 'mjpropiedades'),
        'description' => __('Color de fondo principal de las tarjetas de propiedades', 'mjpropiedades'),
        'section' => 'mjpropiedades_property_cards_colors',
        'settings' => 'mjpropiedades_card_background',
    )));
    
    // Color del título de la propiedad
    $wp_customize->add_setting('mjpropiedades_card_title_color', array(
        'default' => '#333333',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_card_title_color', array(
        'label' => __('Color del Título', 'mjpropiedades'),
        'description' => __('Color del título de la propiedad', 'mjpropiedades'),
        'section' => 'mjpropiedades_property_cards_colors',
        'settings' => 'mjpropiedades_card_title_color',
    )));
    
    // Color de la ubicación
    $wp_customize->add_setting('mjpropiedades_card_location_color', array(
        'default' => '#666666',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_card_location_color', array(
        'label' => __('Color de Ubicación', 'mjpropiedades'),
        'description' => __('Color del texto de ubicación', 'mjpropiedades'),
        'section' => 'mjpropiedades_property_cards_colors',
        'settings' => 'mjpropiedades_card_location_color',
    )));
    
    // Color del precio
    $wp_customize->add_setting('mjpropiedades_card_price_color', array(
        'default' => '#ff6b35',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_card_price_color', array(
        'label' => __('Color del Precio', 'mjpropiedades'),
        'description' => __('Color del precio de la propiedad', 'mjpropiedades'),
        'section' => 'mjpropiedades_property_cards_colors',
        'settings' => 'mjpropiedades_card_price_color',
    )));
    
    // Color del botón "Ver Detalles"
    $wp_customize->add_setting('mjpropiedades_card_button_bg', array(
        'default' => '#FFC107',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_card_button_bg', array(
        'label' => __('Color de Fondo del Botón', 'mjpropiedades'),
        'description' => __('Color de fondo del botón "Ver Detalles"', 'mjpropiedades'),
        'section' => 'mjpropiedades_property_cards_colors',
        'settings' => 'mjpropiedades_card_button_bg',
    )));
    
    // Color del texto del botón
    $wp_customize->add_setting('mjpropiedades_card_button_text', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_card_button_text', array(
        'label' => __('Color del Texto del Botón', 'mjpropiedades'),
        'description' => __('Color del texto del botón "Ver Detalles"', 'mjpropiedades'),
        'section' => 'mjpropiedades_property_cards_colors',
        'settings' => 'mjpropiedades_card_button_text',
    )));
    
    // Color del botón al hacer hover
    $wp_customize->add_setting('mjpropiedades_card_button_hover', array(
        'default' => '#ff6b35',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_card_button_hover', array(
        'label' => __('Color del Botón al Hover', 'mjpropiedades'),
        'description' => __('Color del botón cuando se pasa el mouse por encima', 'mjpropiedades'),
        'section' => 'mjpropiedades_property_cards_colors',
        'settings' => 'mjpropiedades_card_button_hover',
    )));
    
    // Color del texto del botón al hacer hover
    $wp_customize->add_setting('mjpropiedades_card_button_text_hover', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_card_button_text_hover', array(
        'label' => __('Color del Texto del Botón al Hover', 'mjpropiedades'),
        'description' => __('Color del texto del botón al hacer hover', 'mjpropiedades'),
        'section' => 'mjpropiedades_property_cards_colors',
        'settings' => 'mjpropiedades_card_button_text_hover',
    )));
    
    // Color de los detalles (dormitorios, baños, etc.)
    $wp_customize->add_setting('mjpropiedades_card_details_color', array(
        'default' => '#666666',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_card_details_color', array(
        'label' => __('Color de los Detalles', 'mjpropiedades'),
        'description' => __('Color del texto de dormitorios, baños, metros cuadrados', 'mjpropiedades'),
        'section' => 'mjpropiedades_property_cards_colors',
        'settings' => 'mjpropiedades_card_details_color',
    )));
    
    // Color de los iconos de detalles
    $wp_customize->add_setting('mjpropiedades_card_icons_color', array(
        'default' => '#ff6b35',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_card_icons_color', array(
        'label' => __('Color de los Iconos', 'mjpropiedades'),
        'description' => __('Color de los iconos de dormitorios, baños, metros cuadrados', 'mjpropiedades'),
        'section' => 'mjpropiedades_property_cards_colors',
        'settings' => 'mjpropiedades_card_icons_color',
    )));
    
    // Color de la etiqueta de operación (VENTA)
    $wp_customize->add_setting('mjpropiedades_card_tag_bg', array(
        'default' => '#00d4aa',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_card_tag_bg', array(
        'label' => __('Color de Fondo de la Etiqueta VENTA', 'mjpropiedades'),
        'description' => __('Color de fondo de la etiqueta VENTA', 'mjpropiedades'),
        'section' => 'mjpropiedades_property_cards_colors',
        'settings' => 'mjpropiedades_card_tag_bg',
    )));
    
    // Color de la etiqueta de operación (ARRIENDO)
    $wp_customize->add_setting('mjpropiedades_card_arriendo_tag_bg', array(
        'default' => '#4285F4',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_card_arriendo_tag_bg', array(
        'label' => __('Color de Fondo de la Etiqueta ARRIENDO', 'mjpropiedades'),
        'description' => __('Color de fondo de la etiqueta ARRIENDO', 'mjpropiedades'),
        'section' => 'mjpropiedades_property_cards_colors',
        'settings' => 'mjpropiedades_card_arriendo_tag_bg',
    )));
    
    // Color del texto de la etiqueta
    $wp_customize->add_setting('mjpropiedades_card_tag_text', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_card_tag_text', array(
        'label' => __('Color del Texto de la Etiqueta', 'mjpropiedades'),
        'description' => __('Color del texto de la etiqueta VENTA/ARRIENDO', 'mjpropiedades'),
        'section' => 'mjpropiedades_property_cards_colors',
        'settings' => 'mjpropiedades_card_tag_text',
    )));
    
    // Color de la sombra de la tarjeta
    $wp_customize->add_setting('mjpropiedades_card_shadow', array(
        'default' => 'rgba(0, 0, 0, 0.1)',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));
    
    $wp_customize->add_control('mjpropiedades_card_shadow', array(
        'label' => __('Color de la Sombra', 'mjpropiedades'),
        'description' => __('Color de la sombra de las tarjetas (formato: rgba(0, 0, 0, 0.1))', 'mjpropiedades'),
        'section' => 'mjpropiedades_property_cards_colors',
        'type' => 'text',
    ));
    
    // Color de la sombra al hacer hover
    $wp_customize->add_setting('mjpropiedades_card_shadow_hover', array(
        'default' => 'rgba(0, 0, 0, 0.15)',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));
    
    $wp_customize->add_control('mjpropiedades_card_shadow_hover', array(
        'label' => __('Color de la Sombra al Hover', 'mjpropiedades'),
        'description' => __('Color de la sombra cuando se pasa el mouse por encima', 'mjpropiedades'),
        'section' => 'mjpropiedades_property_cards_colors',
        'type' => 'text',
    ));
}
add_action('customize_register', 'mjpropiedades_property_cards_customizer');

// Generar CSS dinámico para los colores de las tarjetas
function mjpropiedades_property_cards_dynamic_css() {
    // Obtener valores con fallback a valores por defecto
    $card_bg = get_theme_mod('mjpropiedades_card_background');
    if (empty($card_bg)) $card_bg = '#ffffff';
    
    $card_title = get_theme_mod('mjpropiedades_card_title_color');
    if (empty($card_title)) $card_title = '#333333';
    
    $card_location = get_theme_mod('mjpropiedades_card_location_color');
    if (empty($card_location)) $card_location = '#666666';
    
    $card_price = get_theme_mod('mjpropiedades_card_price_color');
    if (empty($card_price)) $card_price = '#ff6b35';
    
    $card_button_bg = get_theme_mod('mjpropiedades_card_button_bg');
    if (empty($card_button_bg)) $card_button_bg = '#FFC107';
    
    $card_button_text = get_theme_mod('mjpropiedades_card_button_text');
    if (empty($card_button_text)) $card_button_text = '#ffffff';
    
    $card_button_hover = get_theme_mod('mjpropiedades_card_button_hover');
    if (empty($card_button_hover)) $card_button_hover = '#ff6b35';
    
    $card_button_text_hover = get_theme_mod('mjpropiedades_card_button_text_hover');
    if (empty($card_button_text_hover)) $card_button_text_hover = '#ffffff';
    
    $card_details = get_theme_mod('mjpropiedades_card_details_color');
    if (empty($card_details)) $card_details = '#666666';
    
    $card_icons = get_theme_mod('mjpropiedades_card_icons_color');
    if (empty($card_icons)) $card_icons = '#ff6b35';
    
    $card_tag_bg = get_theme_mod('mjpropiedades_card_tag_bg');
    if (empty($card_tag_bg)) $card_tag_bg = '#00d4aa';
    
    $card_arriendo_tag_bg = get_theme_mod('mjpropiedades_card_arriendo_tag_bg');
    if (empty($card_arriendo_tag_bg)) $card_arriendo_tag_bg = '#4285F4';
    
    $card_tag_text = get_theme_mod('mjpropiedades_card_tag_text');
    if (empty($card_tag_text)) $card_tag_text = '#ffffff';
    
    $card_shadow = get_theme_mod('mjpropiedades_card_shadow');
    if (empty($card_shadow)) $card_shadow = 'rgba(0, 0, 0, 0.1)';
    
    $card_shadow_hover = get_theme_mod('mjpropiedades_card_shadow_hover');
    if (empty($card_shadow_hover)) $card_shadow_hover = 'rgba(0, 0, 0, 0.15)';
    
    $css = "
    /* Colores dinámicos para tarjetas de propiedades */
    .property-card {
        background: {$card_bg} !important;
        box-shadow: 0 2px 8px {$card_shadow} !important;
    }
    
    .property-card:hover {
        box-shadow: 0 8px 25px {$card_shadow_hover} !important;
    }
    
    .property-title a {
        color: {$card_title} !important;
    }
    
    .property-title a:hover {
        color: {$card_price} !important;
    }
    
    .property-location {
        color: {$card_location} !important;
    }
    
    .property-price {
        color: {$card_price} !important;
    }
    
    .property-btn {
        background: {$card_button_bg} !important;
        color: {$card_button_text} !important;
    }
    
    .property-btn:hover {
        background: {$card_button_hover} !important;
        color: {$card_button_text_hover} !important;
    }
    
    .detail-item {
        color: {$card_details} !important;
    }
    
    .detail-item svg {
        color: {$card_icons} !important;
    }
    
    .property-tag {
        color: {$card_tag_text} !important;
    }
    
    /* Etiqueta específica para VENTA */
    .property-tag.venta {
        background: {$card_tag_bg} !important;
        box-shadow: 0 1px 3px {$card_tag_bg}66 !important;
    }
    
    /* Etiqueta específica para ARRIENDO */
    .property-tag.arriendo {
        background: {$card_arriendo_tag_bg} !important;
        box-shadow: 0 1px 3px {$card_arriendo_tag_bg}66 !important;
    }
    
    /* Selectores adicionales para compatibilidad */
    .property-status-tag {
        color: {$card_tag_text} !important;
    }
    
    .property-status-tag.venta {
        background: {$card_tag_bg} !important;
        box-shadow: 0 1px 3px {$card_tag_bg}66 !important;
    }
    
    .property-status-tag.arriendo {
        background: {$card_arriendo_tag_bg} !important;
        box-shadow: 0 1px 3px {$card_arriendo_tag_bg}66 !important;
    }
    
    .property-details {
        color: {$card_details} !important;
    }
    
    .property-feature {
        color: {$card_details} !important;
    }
    
    .property-feature svg {
        color: {$card_icons} !important;
    }
    
    .property-button {
        background: {$card_button_bg} !important;
        color: {$card_button_text} !important;
    }
    
    .property-button:hover {
        background: {$card_button_hover} !important;
        color: {$card_button_text_hover} !important;
    }
    
    /* Selectores para archivos específicos */
    .featured-properties-grid .property-card {
        background: {$card_bg} !important;
        box-shadow: 0 2px 8px {$card_shadow} !important;
    }
    
    .featured-properties-grid .property-card:hover {
        box-shadow: 0 8px 25px {$card_shadow_hover} !important;
    }
    
    .properties-grid .property-card {
        background: {$card_bg} !important;
        box-shadow: 0 2px 8px {$card_shadow} !important;
    }
    
    .properties-grid .property-card:hover {
        box-shadow: 0 8px 25px {$card_shadow_hover} !important;
    }
    ";
    
    return $css;
}

// Reemplazar la función anterior con la nueva que incluye versión
add_action('wp_head', 'mjpropiedades_property_cards_css_with_version');

// Función adicional para cargar CSS específicamente en el template de propiedades
function mjpropiedades_load_property_cards_css_in_template() {
    // Solo ejecutar si estamos usando el template de propiedades
    if (is_page_template('page-propiedades.php')) {
        echo '<style type="text/css" id="mjpropiedades-property-cards-colors-template">' . mjpropiedades_property_cards_dynamic_css() . '</style>';
    }
}
add_action('wp_head', 'mjpropiedades_load_property_cards_css_in_template', 5);

// Agregar CSS dinámico para el Customizer (preview en tiempo real)
function mjpropiedades_property_cards_customizer_css() {
    wp_add_inline_style('customize-preview', mjpropiedades_property_cards_dynamic_css());
}
add_action('customize_preview_init', function() {
    add_action('wp_enqueue_scripts', 'mjpropiedades_property_cards_customizer_css');
});

// JavaScript para preview en tiempo real en el Customizer
function mjpropiedades_property_cards_customizer_js() {
    ?>
    <script type="text/javascript">
    (function($) {
        // Preview en tiempo real para colores de tarjetas
        wp.customize('mjpropiedades_card_background', function(value) {
            value.bind(function(newval) {
                $('.property-card').css('background', newval);
            });
        });
        
        wp.customize('mjpropiedades_card_title_color', function(value) {
            value.bind(function(newval) {
                $('.property-title a').css('color', newval);
            });
        });
        
        wp.customize('mjpropiedades_card_location_color', function(value) {
            value.bind(function(newval) {
                $('.property-location').css('color', newval);
            });
        });
        
        wp.customize('mjpropiedades_card_price_color', function(value) {
            value.bind(function(newval) {
                $('.property-price').css('color', newval);
                $('.property-title a:hover').css('color', newval);
                $('.property-tag[style*="background: #ff6b35"]').css('background', newval);
            });
        });
        
        wp.customize('mjpropiedades_card_button_bg', function(value) {
            value.bind(function(newval) {
                $('.property-btn, .property-button').css('background', newval);
            });
        });
        
        wp.customize('mjpropiedades_card_button_text', function(value) {
            value.bind(function(newval) {
                $('.property-btn, .property-button').css('color', newval);
            });
        });
        
        wp.customize('mjpropiedades_card_button_hover', function(value) {
            value.bind(function(newval) {
                $('.property-btn:hover, .property-button:hover').css('background', newval);
            });
        });
        
        wp.customize('mjpropiedades_card_button_text_hover', function(value) {
            value.bind(function(newval) {
                $('.property-btn:hover, .property-button:hover').css('color', newval);
            });
        });
        
        wp.customize('mjpropiedades_card_details_color', function(value) {
            value.bind(function(newval) {
                $('.detail-item, .property-details, .property-feature').css('color', newval);
            });
        });
        
        wp.customize('mjpropiedades_card_icons_color', function(value) {
            value.bind(function(newval) {
                $('.detail-item svg, .property-feature svg').css('color', newval);
            });
        });
        
        wp.customize('mjpropiedades_card_tag_bg', function(value) {
            value.bind(function(newval) {
                $('.property-tag.venta, .property-status-tag.venta').css('background', newval);
            });
        });
        
        wp.customize('mjpropiedades_card_arriendo_tag_bg', function(value) {
            value.bind(function(newval) {
                $('.property-tag.arriendo, .property-status-tag.arriendo').css('background', newval);
            });
        });
        
        wp.customize('mjpropiedades_card_tag_text', function(value) {
            value.bind(function(newval) {
                $('.property-tag, .property-status-tag').css('color', newval);
            });
        });
        
        wp.customize('mjpropiedades_card_shadow', function(value) {
            value.bind(function(newval) {
                $('.property-card').css('box-shadow', '0 2px 8px ' + newval);
            });
        });
        
        wp.customize('mjpropiedades_card_shadow_hover', function(value) {
            value.bind(function(newval) {
                $('.property-card:hover').css('box-shadow', '0 8px 25px ' + newval);
            });
        });
        
    })(jQuery);
    </script>
    <?php
}
add_action('customize_preview_init', 'mjpropiedades_property_cards_customizer_js');

// Limpiar caché cuando se cambien los colores de las tarjetas
function mjpropiedades_clear_cache_on_property_cards_color_change() {
    // Limpiar caché de WordPress si existe
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
    
    // Limpiar caché de objetos específico
    if (function_exists('wp_cache_delete')) {
        wp_cache_delete('mjpropiedades_property_cards_colors', 'theme_mods');
    }
}
add_action('customize_save_after', 'mjpropiedades_clear_cache_on_property_cards_color_change');

// Agregar parámetros de versión para evitar caché en el CSS de tarjetas
function mjpropiedades_add_version_to_property_cards_css() {
    return time(); // Cambia cada vez que se carga la página
}

// Modificar la función que carga el CSS para agregar versión
function mjpropiedades_property_cards_css_with_version() {
    // Verificar si estamos en la página de propiedades o en cualquier página que muestre propiedades
    $should_load = false;
    
    // Verificar página específica por slug
    if (is_page('propiedades')) {
        $should_load = true;
    }
    
    // Verificar por ID de página
    $propiedades_page = get_page_by_path('propiedades');
    if ($propiedades_page && is_page($propiedades_page->ID)) {
        $should_load = true;
    }
    
    // Verificar si estamos en home o front page (donde también pueden aparecer propiedades)
    if (is_home() || is_front_page()) {
        $should_load = true;
    }
    
    // Verificar si hay propiedades en la consulta actual
    global $wp_query;
    if ($wp_query && $wp_query->get('post_type') === 'propiedad') {
        $should_load = true;
    }
    
    // Verificar si estamos en cualquier página que contenga el template de propiedades
    // Comentado porque page-propiedades.php ya no existe
    // if (is_page_template('page-propiedades.php')) {
    //     $should_load = true;
    // }
    
    // Verificar si estamos usando el template de inicio (page-inicio.php)
    if (is_page_template('page-inicio.php')) {
        $should_load = true;
    }
    
    // Verificar si estamos en la página de inicio por slug
    if (is_page('inicio')) {
        $should_load = true;
    }
    
    // Verificar si estamos en el archivo de propiedades (archive-propiedad.php)
    if (is_post_type_archive('propiedad')) {
        $should_load = true;
    }
    
    // Verificar si estamos en una página individual de propiedad (single-propiedad.php)
    if (is_singular('propiedad')) {
        $should_load = true;
    }
    
    // Verificar si estamos en cualquier página que use el template de inicio
    if (is_page() && get_page_template_slug() === 'page-inicio.php') {
        $should_load = true;
    }
    
    // Verificar si estamos en cualquier página que use el template de propiedades
    // Comentado porque page-propiedades.php ya no existe
    // if (is_page() && get_page_template_slug() === 'page-propiedades.php') {
    //     $should_load = true;
    // }
    
    // Verificar si el contenido de la página contiene tarjetas de propiedades
    if (is_page() && (strpos(get_post_field('post_content', get_the_ID()), 'property-card') !== false)) {
        $should_load = true;
    }
    
    if ($should_load) {
        $version = mjpropiedades_add_version_to_property_cards_css();
        echo '<style type="text/css" id="mjpropiedades-property-cards-colors" data-version="' . $version . '">' . mjpropiedades_property_cards_dynamic_css() . '</style>';
    }
}

// Agregar página de administración para opciones de búsqueda
function mjpropiedades_add_search_options_page() {
    add_options_page(
        'Opciones de Búsqueda',
        'Opciones de Búsqueda',
        'manage_options',
        'mjpropiedades-search-options',
        'mjpropiedades_search_options_page'
    );
}
add_action('admin_menu', 'mjpropiedades_add_search_options_page');

// Página de opciones de búsqueda
function mjpropiedades_search_options_page() {
    // Guardar opciones si se envió el formulario
    if (isset($_POST['submit']) && wp_verify_nonce($_POST['search_options_nonce'], 'save_search_options')) {
        // Guardar tipos de propiedad
        if (isset($_POST['tipos_propiedad'])) {
            $tipos = array();
            foreach ($_POST['tipos_propiedad'] as $tipo) {
                if (!empty($tipo['value']) && !empty($tipo['label'])) {
                    $tipos[sanitize_text_field($tipo['value'])] = sanitize_text_field($tipo['label']);
                }
            }
            update_option('mjpropiedades_tipos_propiedad', $tipos);
        }
        
        // Guardar comunas
        if (isset($_POST['comunas'])) {
            $comunas = array();
            foreach ($_POST['comunas'] as $comuna) {
                if (!empty($comuna['value']) && !empty($comuna['label'])) {
                    $comunas[sanitize_text_field($comuna['value'])] = sanitize_text_field($comuna['label']);
                }
            }
            update_option('mjpropiedades_comunas', $comunas);
        }
        
        // Guardar opciones de dormitorios
        if (isset($_POST['dormitorios'])) {
            $dormitorios = array();
            foreach ($_POST['dormitorios'] as $dormitorio) {
                if (!empty($dormitorio['value']) && !empty($dormitorio['label'])) {
                    $dormitorios[sanitize_text_field($dormitorio['value'])] = sanitize_text_field($dormitorio['label']);
                }
            }
            update_option('mjpropiedades_dormitorios', $dormitorios);
        }
        
        // Guardar opciones de baños
        if (isset($_POST['banos'])) {
            $banos = array();
            foreach ($_POST['banos'] as $bano) {
                if (!empty($bano['value']) && !empty($bano['label'])) {
                    $banos[sanitize_text_field($bano['value'])] = sanitize_text_field($bano['label']);
                }
            }
            update_option('mjpropiedades_banos', $banos);
        }
        
        echo '<div class="notice notice-success"><p>Opciones guardadas correctamente.</p></div>';
    }
    
    // Obtener opciones actuales
    $tipos_propiedad = get_option('mjpropiedades_tipos_propiedad', array());
    $comunas = get_option('mjpropiedades_comunas', array());
    $dormitorios = get_option('mjpropiedades_dormitorios', array());
    $banos = get_option('mjpropiedades_banos', array());
    
    ?>
    <div class="wrap">
        <h1>Opciones de Búsqueda de Propiedades</h1>
        <p>Gestiona las opciones disponibles en los formularios de búsqueda.</p>
        
        <form method="post" action="">
            <?php wp_nonce_field('save_search_options', 'search_options_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Tipos de Propiedad</th>
                    <td>
                        <div id="tipos-propiedad-container">
                            <?php
                            $counter = 0;
                            foreach ($tipos_propiedad as $value => $label) {
                                echo '<div class="option-row">';
                                echo '<input type="text" name="tipos_propiedad[' . $counter . '][value]" value="' . esc_attr($value) . '" placeholder="Valor (ej: casa)" style="width: 150px; margin-right: 10px;">';
                                echo '<input type="text" name="tipos_propiedad[' . $counter . '][label]" value="' . esc_attr($label) . '" placeholder="Etiqueta (ej: Casa)" style="width: 200px; margin-right: 10px;">';
                                echo '<button type="button" class="button remove-option">Eliminar</button>';
                                echo '</div>';
                                $counter++;
                            }
                            ?>
                        </div>
                        <button type="button" id="add-tipo-propiedad" class="button">Agregar Tipo de Propiedad</button>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Comunas</th>
                    <td>
                        <div id="comunas-container">
                            <?php
                            $counter = 0;
                            foreach ($comunas as $value => $label) {
                                echo '<div class="option-row">';
                                echo '<input type="text" name="comunas[' . $counter . '][value]" value="' . esc_attr($value) . '" placeholder="Valor (ej: la-serena)" style="width: 150px; margin-right: 10px;">';
                                echo '<input type="text" name="comunas[' . $counter . '][label]" value="' . esc_attr($label) . '" placeholder="Etiqueta (ej: La Serena)" style="width: 200px; margin-right: 10px;">';
                                echo '<button type="button" class="button remove-option">Eliminar</button>';
                                echo '</div>';
                                $counter++;
                            }
                            ?>
                        </div>
                        <button type="button" id="add-comuna" class="button">Agregar Comuna</button>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Dormitorios</th>
                    <td>
                        <div id="dormitorios-container">
                            <?php
                            $counter = 0;
                            foreach ($dormitorios as $value => $label) {
                                echo '<div class="option-row">';
                                echo '<input type="text" name="dormitorios[' . $counter . '][value]" value="' . esc_attr($value) . '" placeholder="Valor (ej: 2)" style="width: 150px; margin-right: 10px;">';
                                echo '<input type="text" name="dormitorios[' . $counter . '][label]" value="' . esc_attr($label) . '" placeholder="Etiqueta (ej: 2 dormitorios)" style="width: 200px; margin-right: 10px;">';
                                echo '<button type="button" class="button remove-option">Eliminar</button>';
                                echo '</div>';
                                $counter++;
                            }
                            ?>
                        </div>
                        <button type="button" id="add-dormitorio" class="button">Agregar Opción de Dormitorios</button>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Baños</th>
                    <td>
                        <div id="banos-container">
                            <?php
                            $counter = 0;
                            foreach ($banos as $value => $label) {
                                echo '<div class="option-row">';
                                echo '<input type="text" name="banos[' . $counter . '][value]" value="' . esc_attr($value) . '" placeholder="Valor (ej: 2)" style="width: 150px; margin-right: 10px;">';
                                echo '<input type="text" name="banos[' . $counter . '][label]" value="' . esc_attr($label) . '" placeholder="Etiqueta (ej: 2 baños)" style="width: 200px; margin-right: 10px;">';
                                echo '<button type="button" class="button remove-option">Eliminar</button>';
                                echo '</div>';
                                $counter++;
                            }
                            ?>
                        </div>
                        <button type="button" id="add-bano" class="button">Agregar Opción de Baños</button>
                    </td>
                </tr>
            </table>
            
            <?php submit_button('Guardar Opciones'); ?>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        let tipoCounter = <?php echo count($tipos_propiedad); ?>;
        let comunaCounter = <?php echo count($comunas); ?>;
        let dormitorioCounter = <?php echo count($dormitorios); ?>;
        let banoCounter = <?php echo count($banos); ?>;
        
        // Agregar tipo de propiedad
        $('#add-tipo-propiedad').click(function() {
            const html = '<div class="option-row">' +
                '<input type="text" name="tipos_propiedad[' + tipoCounter + '][value]" placeholder="Valor (ej: casa)" style="width: 150px; margin-right: 10px;">' +
                '<input type="text" name="tipos_propiedad[' + tipoCounter + '][label]" placeholder="Etiqueta (ej: Casa)" style="width: 200px; margin-right: 10px;">' +
                '<button type="button" class="button remove-option">Eliminar</button>' +
                '</div>';
            $('#tipos-propiedad-container').append(html);
            tipoCounter++;
        });
        
        // Agregar comuna
        $('#add-comuna').click(function() {
            const html = '<div class="option-row">' +
                '<input type="text" name="comunas[' + comunaCounter + '][value]" placeholder="Valor (ej: la-serena)" style="width: 150px; margin-right: 10px;">' +
                '<input type="text" name="comunas[' + comunaCounter + '][label]" placeholder="Etiqueta (ej: La Serena)" style="width: 200px; margin-right: 10px;">' +
                '<button type="button" class="button remove-option">Eliminar</button>' +
                '</div>';
            $('#comunas-container').append(html);
            comunaCounter++;
        });
        
        // Agregar dormitorio
        $('#add-dormitorio').click(function() {
            const html = '<div class="option-row">' +
                '<input type="text" name="dormitorios[' + dormitorioCounter + '][value]" placeholder="Valor (ej: 2)" style="width: 150px; margin-right: 10px;">' +
                '<input type="text" name="dormitorios[' + dormitorioCounter + '][label]" placeholder="Etiqueta (ej: 2 dormitorios)" style="width: 200px; margin-right: 10px;">' +
                '<button type="button" class="button remove-option">Eliminar</button>' +
                '</div>';
            $('#dormitorios-container').append(html);
            dormitorioCounter++;
        });
        
        // Agregar baño
        $('#add-bano').click(function() {
            const html = '<div class="option-row">' +
                '<input type="text" name="banos[' + banoCounter + '][value]" placeholder="Valor (ej: 2)" style="width: 150px; margin-right: 10px;">' +
                '<input type="text" name="banos[' + banoCounter + '][label]" placeholder="Etiqueta (ej: 2 baños)" style="width: 200px; margin-right: 10px;">' +
                '<button type="button" class="button remove-option">Eliminar</button>' +
                '</div>';
            $('#banos-container').append(html);
            banoCounter++;
        });
        
        // Eliminar opción
        $(document).on('click', '.remove-option', function() {
            $(this).closest('.option-row').remove();
        });
    });
    </script>
    
    <style>
    .option-row {
        margin-bottom: 10px;
        padding: 10px;
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    </style>
    <?php
}

// ========================================
// SISTEMA DE AGENTES INMOBILIARIOS
// ========================================

// 1. CREAR CUSTOM POST TYPE PARA AGENTES
function mjpropiedades_create_agente_post_type() {
    $labels = array(
        'name' => 'Agentes',
        'singular_name' => 'Agente',
        'menu_name' => 'Agentes',
        'add_new' => 'Agregar Agente',
        'add_new_item' => 'Agregar Nuevo Agente',
        'edit_item' => 'Editar Agente',
        'new_item' => 'Nuevo Agente',
        'view_item' => 'Ver Agente',
        'search_items' => 'Buscar Agentes',
        'not_found' => 'No se encontraron agentes',
        'not_found_in_trash' => 'No se encontraron agentes en la papelera'
    );

    $args = array(
        'labels' => $labels,
        'public' => false,
        'publicly_queryable' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => false,
        'capability_type' => 'post',
        'has_archive' => false,
        'hierarchical' => false,
        'menu_position' => 25,
        'menu_icon' => 'dashicons-businessman',
        'supports' => array('title', 'thumbnail', 'editor'),
        'show_in_rest' => false
    );

    register_post_type('agente', $args);
}
add_action('init', 'mjpropiedades_create_agente_post_type');

// 2. CREAR META BOXES PARA AGENTES
function mjpropiedades_add_agente_meta_boxes() {
    add_meta_box(
        'agente_contact_info',
        'Información de Contacto',
        'mjpropiedades_agente_contact_info_callback',
        'agente',
        'normal',
        'high'
    );

    add_meta_box(
        'agente_specializations',
        'Especializaciones',
        'mjpropiedades_agente_specializations_callback',
        'agente',
        'normal',
        'high'
    );

    add_meta_box(
        'agente_stats',
        'Estadísticas',
        'mjpropiedades_agente_stats_callback',
        'agente',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'mjpropiedades_add_agente_meta_boxes');

// Callback para información de contacto
function mjpropiedades_agente_contact_info_callback($post) {
    wp_nonce_field('mjpropiedades_agente_contact_info', 'mjpropiedades_agente_contact_info_nonce');
    
    $telefono = get_post_meta($post->ID, '_agente_telefono', true);
    $whatsapp = get_post_meta($post->ID, '_agente_whatsapp', true);
    $email = get_post_meta($post->ID, '_agente_email', true);
    $email_alt = get_post_meta($post->ID, '_agente_email_alt', true);
    ?>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="agente_telefono">Teléfono Principal</label></th>
            <td><input type="tel" id="agente_telefono" name="agente_telefono" value="<?php echo esc_attr($telefono); ?>" class="regular-text" placeholder="+56 9 1234 5678" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="agente_whatsapp">WhatsApp</label></th>
            <td><input type="tel" id="agente_whatsapp" name="agente_whatsapp" value="<?php echo esc_attr($whatsapp); ?>" class="regular-text" placeholder="+56 9 1234 5678" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="agente_email">Email Principal</label></th>
            <td><input type="email" id="agente_email" name="agente_email" value="<?php echo esc_attr($email); ?>" class="regular-text" placeholder="agente@propiedades.com" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="agente_email_alt">Email Alternativo</label></th>
            <td><input type="email" id="agente_email_alt" name="agente_email_alt" value="<?php echo esc_attr($email_alt); ?>" class="regular-text" placeholder="alternativo@propiedades.com" /></td>
        </tr>
    </table>
    <?php
}

// Callback para especializaciones
function mjpropiedades_agente_specializations_callback($post) {
    wp_nonce_field('mjpropiedades_agente_specializations', 'mjpropiedades_agente_specializations_nonce');
    
    $cargo = get_post_meta($post->ID, '_agente_cargo', true);
    $comunas = get_post_meta($post->ID, '_agente_comunas', true);
    $tipos_propiedad = get_post_meta($post->ID, '_agente_tipos_propiedad', true);
    $operaciones = get_post_meta($post->ID, '_agente_operaciones', true);
    $experiencia = get_post_meta($post->ID, '_agente_experiencia', true);
    
    if (!is_array($comunas)) $comunas = array();
    if (!is_array($tipos_propiedad)) $tipos_propiedad = array();
    if (!is_array($operaciones)) $operaciones = array();
    
    $comunas_disponibles = array('La Serena', 'Coquimbo', 'Ovalle', 'Vicuña', 'Illapel', 'Salamanca', 'Los Vilos', 'Andacollo', 'Punitaqui', 'Monte Patria', 'Combarbalá', 'Canela', 'Paiguano');
    $tipos_disponibles = array('Casa', 'Departamento', 'Terreno', 'Local Comercial', 'Oficina', 'Bodega', 'Parcela', 'Edificio');
    $operaciones_disponibles = array('Venta', 'Arriendo', 'Ambas');
    ?>
    
    <table class="form-table">
        <tr>
            <th scope="row"><label for="agente_cargo">Cargo/Especialización</label></th>
            <td><input type="text" id="agente_cargo" name="agente_cargo" value="<?php echo esc_attr($cargo); ?>" class="regular-text" placeholder="Especialista en La Serena" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="agente_experiencia">Años de Experiencia</label></th>
            <td><input type="number" id="agente_experiencia" name="agente_experiencia" value="<?php echo esc_attr($experiencia); ?>" class="small-text" min="0" max="50" /></td>
        </tr>
        <tr>
            <th scope="row">Comunas de Especialización</th>
            <td>
                <?php foreach ($comunas_disponibles as $comuna) : ?>
                    <label style="display: block; margin-bottom: 5px;">
                        <input type="checkbox" name="agente_comunas[]" value="<?php echo esc_attr($comuna); ?>" <?php checked(in_array($comuna, $comunas)); ?> />
                        <?php echo esc_html($comuna); ?>
                    </label>
                <?php endforeach; ?>
            </td>
        </tr>
        <tr>
            <th scope="row">Tipos de Propiedad</th>
            <td>
                <?php foreach ($tipos_disponibles as $tipo) : ?>
                    <label style="display: block; margin-bottom: 5px;">
                        <input type="checkbox" name="agente_tipos_propiedad[]" value="<?php echo esc_attr($tipo); ?>" <?php checked(in_array($tipo, $tipos_propiedad)); ?> />
                        <?php echo esc_html($tipo); ?>
                    </label>
                <?php endforeach; ?>
            </td>
        </tr>
        <tr>
            <th scope="row">Operaciones</th>
            <td>
                <?php foreach ($operaciones_disponibles as $operacion) : ?>
                    <label style="display: block; margin-bottom: 5px;">
                        <input type="checkbox" name="agente_operaciones[]" value="<?php echo esc_attr($operacion); ?>" <?php checked(in_array($operacion, $operaciones)); ?> />
                        <?php echo esc_html($operacion); ?>
                    </label>
                <?php endforeach; ?>
            </td>
        </tr>
    </table>
    <?php
}

// Callback para estadísticas
function mjpropiedades_agente_stats_callback($post) {
    wp_nonce_field('mjpropiedades_agente_stats', 'mjpropiedades_agente_stats_nonce');
    
    $rating = get_post_meta($post->ID, '_agente_rating', true);
    $resenas = get_post_meta($post->ID, '_agente_resenas', true);
    $propiedades_vendidas = get_post_meta($post->ID, '_agente_propiedades_vendidas', true);
    $activo = get_post_meta($post->ID, '_agente_activo', true);
    ?>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="agente_rating">Rating (0-5)</label></th>
            <td><input type="number" id="agente_rating" name="agente_rating" value="<?php echo esc_attr($rating); ?>" class="small-text" min="0" max="5" step="0.1" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="agente_resenas">Número de Reseñas</label></th>
            <td><input type="number" id="agente_resenas" name="agente_resenas" value="<?php echo esc_attr($resenas); ?>" class="small-text" min="0" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="agente_propiedades_vendidas">Propiedades Vendidas</label></th>
            <td><input type="number" id="agente_propiedades_vendidas" name="agente_propiedades_vendidas" value="<?php echo esc_attr($propiedades_vendidas); ?>" class="small-text" min="0" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="agente_activo">Agente Activo</label></th>
            <td><input type="checkbox" id="agente_activo" name="agente_activo" value="1" <?php checked($activo, '1'); ?> /> <label for="agente_activo">Activo</label></td>
        </tr>
    </table>
    <?php
}

// 3. GUARDAR META BOXES DE AGENTES
function mjpropiedades_save_agente_meta_boxes($post_id) {
    // Verificar nonces y permisos
    if (!isset($_POST['mjpropiedades_agente_contact_info_nonce']) || !wp_verify_nonce($_POST['mjpropiedades_agente_contact_info_nonce'], 'mjpropiedades_agente_contact_info')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Guardar información de contacto
    $fields = array('telefono', 'whatsapp', 'email', 'email_alt');
    foreach ($fields as $field) {
        if (isset($_POST['agente_' . $field])) {
            update_post_meta($post_id, '_agente_' . $field, sanitize_text_field($_POST['agente_' . $field]));
        }
    }

    // Guardar especializaciones
    if (isset($_POST['agente_cargo'])) {
        update_post_meta($post_id, '_agente_cargo', sanitize_text_field($_POST['agente_cargo']));
    }
    
    if (isset($_POST['agente_experiencia'])) {
        update_post_meta($post_id, '_agente_experiencia', intval($_POST['agente_experiencia']));
    }

    if (isset($_POST['agente_comunas'])) {
        update_post_meta($post_id, '_agente_comunas', array_map('sanitize_text_field', $_POST['agente_comunas']));
    } else {
        update_post_meta($post_id, '_agente_comunas', array());
    }

    if (isset($_POST['agente_tipos_propiedad'])) {
        update_post_meta($post_id, '_agente_tipos_propiedad', array_map('sanitize_text_field', $_POST['agente_tipos_propiedad']));
    } else {
        update_post_meta($post_id, '_agente_tipos_propiedad', array());
    }

    if (isset($_POST['agente_operaciones'])) {
        update_post_meta($post_id, '_agente_operaciones', array_map('sanitize_text_field', $_POST['agente_operaciones']));
    } else {
        update_post_meta($post_id, '_agente_operaciones', array());
    }

    // Guardar estadísticas
    if (isset($_POST['agente_rating'])) {
        update_post_meta($post_id, '_agente_rating', floatval($_POST['agente_rating']));
    }
    
    if (isset($_POST['agente_resenas'])) {
        update_post_meta($post_id, '_agente_resenas', intval($_POST['agente_resenas']));
    }
    
    if (isset($_POST['agente_propiedades_vendidas'])) {
        update_post_meta($post_id, '_agente_propiedades_vendidas', intval($_POST['agente_propiedades_vendidas']));
    }
    
    update_post_meta($post_id, '_agente_activo', isset($_POST['agente_activo']) ? '1' : '0');
}
add_action('save_post', 'mjpropiedades_save_agente_meta_boxes');

// 4. CREAR META BOX PARA ASIGNAR AGENTE A PROPIEDAD
function mjpropiedades_add_property_agent_meta_box() {
    add_meta_box(
        'property_agent_assignment',
        'Asignar Agente',
        'mjpropiedades_property_agent_assignment_callback',
        'propiedad',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'mjpropiedades_add_property_agent_meta_box');

function mjpropiedades_property_agent_assignment_callback($post) {
    wp_nonce_field('mjpropiedades_property_agent_assignment', 'mjpropiedades_property_agent_assignment_nonce');
    
    $agente_asignado = get_post_meta($post->ID, '_propiedad_agente', true);
    
    // Obtener agentes activos
    $agentes = get_posts(array(
        'post_type' => 'agente',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_agente_activo',
                'value' => '1',
                'compare' => '='
            )
        )
    ));
    
    echo '<div id="agent-assignment-container">';
    echo '<p><strong>Seleccionar Agente:</strong></p>';
    echo '<select name="propiedad_agente" id="propiedad_agente" style="width: 100%; margin-bottom: 15px;">';
    echo '<option value="">Sin agente asignado</option>';
    
    foreach ($agentes as $agente) {
        $selected = selected($agente_asignado, $agente->ID, false);
        $cargo = get_post_meta($agente->ID, '_agente_cargo', true);
        $rating = get_post_meta($agente->ID, '_agente_rating', true);
        $resenas = get_post_meta($agente->ID, '_agente_resenas', true);
        
        $display_name = $agente->post_title;
        if ($cargo) {
            $display_name .= ' - ' . $cargo;
        }
        if ($rating) {
            $display_name .= ' (' . $rating . '⭐)';
        }
        
        echo '<option value="' . $agente->ID . '" ' . $selected . '>' . esc_html($display_name) . '</option>';
    }
    
    echo '</select>';
    
    // Vista previa del agente seleccionado
    if ($agente_asignado) {
        $agente = get_post($agente_asignado);
        if ($agente) {
            echo '<div id="agent-preview" style="background: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 4px;">';
            echo '<h4>Vista Previa del Agente:</h4>';
            
            $avatar = get_the_post_thumbnail($agente->ID, 'thumbnail');
            if ($avatar) {
                echo '<div style="float: left; margin-right: 10px;">' . $avatar . '</div>';
            }
            
            echo '<div>';
            echo '<strong>' . esc_html($agente->post_title) . '</strong><br>';
            
            $cargo = get_post_meta($agente->ID, '_agente_cargo', true);
            if ($cargo) {
                echo '<em>' . esc_html($cargo) . '</em><br>';
            }
            
            $rating = get_post_meta($agente->ID, '_agente_rating', true);
            $resenas = get_post_meta($agente->ID, '_agente_resenas', true);
            if ($rating && $resenas) {
                echo '⭐ ' . $rating . ' (' . $resenas . ' reseñas)<br>';
            }
            
            $telefono = get_post_meta($agente->ID, '_agente_telefono', true);
            if ($telefono) {
                echo '📞 ' . esc_html($telefono) . '<br>';
            }
            
            $email = get_post_meta($agente->ID, '_agente_email', true);
            if ($email) {
                echo '✉️ ' . esc_html($email);
            }
            
            echo '</div>';
            echo '<div style="clear: both;"></div>';
            echo '</div>';
        }
    } else {
        echo '<div id="agent-preview" style="display: none;"></div>';
    }
    
    echo '<p style="margin-top: 15px;">';
    echo '<a href="' . admin_url('post-new.php?post_type=agente') . '" class="button" target="_blank">Agregar Nuevo Agente</a> ';
    echo '<a href="' . admin_url('edit.php?post_type=agente') . '" class="button" target="_blank">Gestionar Agentes</a>';
    echo '</p>';
    
    echo '</div>';
    
    // JavaScript para actualizar vista previa
    ?>
    <script>
    jQuery(document).ready(function($) {
        $('#propiedad_agente').on('change', function() {
            var agentId = $(this).val();
            var preview = $('#agent-preview');
            
            if (agentId) {
                // Aquí podrías hacer una llamada AJAX para obtener los datos del agente
                // Por simplicidad, recargamos la página para mostrar la vista previa
                preview.show();
            } else {
                preview.hide();
            }
        });
    });
    </script>
    <?php
}

// Guardar asignación de agente
function mjpropiedades_save_property_agent_assignment($post_id) {
    if (!isset($_POST['mjpropiedades_property_agent_assignment_nonce']) || !wp_verify_nonce($_POST['mjpropiedades_property_agent_assignment_nonce'], 'mjpropiedades_property_agent_assignment')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['propiedad_agente'])) {
        update_post_meta($post_id, '_propiedad_agente', intval($_POST['propiedad_agente']));
    }
}
add_action('save_post', 'mjpropiedades_save_property_agent_assignment');

// 5. AGREGAR CONFIGURACIÓN AL CUSTOMIZER
function mjpropiedades_agent_customizer_settings($wp_customize) {
    // Sección de agentes
    $wp_customize->add_section('mjpropiedades_agents', array(
        'title' => 'Configuración de Agentes',
        'priority' => 37,
        'description' => 'Configuración global para el sistema de agentes inmobiliarios',
        'active_callback' => '__return_true',
    ));

    // Agente por defecto
    $wp_customize->add_setting('mjpropiedades_default_agent', array(
        'default' => '',
        'sanitize_callback' => 'absint'
    ));

    $wp_customize->add_control('mjpropiedades_default_agent', array(
        'label' => 'Agente por Defecto',
        'description' => 'Agente que se mostrará cuando una propiedad no tenga agente asignado',
        'section' => 'mjpropiedades_agents',
        'type' => 'select',
        'choices' => mjpropiedades_get_agents_choices()
    ));

    // Mostrar rating
    $wp_customize->add_setting('mjpropiedades_show_rating', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean'
    ));

    $wp_customize->add_control('mjpropiedades_show_rating', array(
        'label' => 'Mostrar Rating',
        'description' => 'Mostrar el rating y número de reseñas',
        'section' => 'mjpropiedades_agents',
        'type' => 'checkbox'
    ));

    // Mostrar estadísticas
    $wp_customize->add_setting('mjpropiedades_show_stats', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean'
    ));

    $wp_customize->add_control('mjpropiedades_show_stats', array(
        'label' => 'Mostrar Estadísticas',
        'description' => 'Mostrar propiedades vendidas y años de experiencia',
        'section' => 'mjpropiedades_agents',
        'type' => 'checkbox'
    ));

    // Mensaje cuando no hay agente
    $wp_customize->add_setting('mjpropiedades_no_agent_message', array(
        'default' => 'Contactar con nuestro equipo',
        'sanitize_callback' => 'sanitize_text_field'
    ));

    $wp_customize->add_control('mjpropiedades_no_agent_message', array(
        'label' => 'Mensaje sin Agente',
        'description' => 'Mensaje que se muestra cuando no hay agente disponible',
        'section' => 'mjpropiedades_agents',
        'type' => 'text'
    ));
}
add_action('customize_register', 'mjpropiedades_agent_customizer_settings');

// Función helper para obtener opciones de agentes
function mjpropiedades_get_agents_choices() {
    $choices = array('' => 'Sin agente por defecto');
    
    $agentes = get_posts(array(
        'post_type' => 'agente',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_agente_activo',
                'value' => '1',
                'compare' => '='
            )
        )
    ));

    foreach ($agentes as $agente) {
        $choices[$agente->ID] = $agente->post_title;
    }

    return $choices;
}

// 6. FUNCIONES HELPER PARA OBTENER DATOS DEL AGENTE
function mjpropiedades_get_property_agent($property_id = null) {
    if (!$property_id) {
        global $post;
        $property_id = $post->ID;
    }

    // 1. Buscar agente asignado directamente a la propiedad
    $agente_id = get_post_meta($property_id, '_propiedad_agente', true);
    
    if ($agente_id) {
        $agente = get_post($agente_id);
        if ($agente && $agente->post_type === 'agente') {
            return $agente;
        }
    }

    // 2. Solo usar agente por defecto si está configurado en el tema
    $default_agent_id = get_theme_mod('mjpropiedades_default_agent', '');
    if ($default_agent_id) {
        $agente = get_post($default_agent_id);
        if ($agente && $agente->post_type === 'agente') {
            return $agente;
        }
    }

    // 3. Si no hay agente por defecto configurado, retornar null
    // El template mostrará información de contacto de la empresa
    return null;
}

function mjpropiedades_get_agent_by_comuna($comuna) {
    $agentes = get_posts(array(
        'post_type' => 'agente',
        'posts_per_page' => 1,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => '_agente_activo',
                'value' => '1',
                'compare' => '='
            ),
            array(
                'key' => '_agente_comunas',
                'value' => $comuna,
                'compare' => 'LIKE'
            )
        )
    ));

    return !empty($agentes) ? $agentes[0] : null;
}

function mjpropiedades_get_agent_data($agente_id) {
    if (!$agente_id) return null;

    $agente = get_post($agente_id);
    if (!$agente || $agente->post_type !== 'agente') return null;

    return array(
        'id' => $agente->ID,
        'nombre' => $agente->post_title,
        'cargo' => get_post_meta($agente->ID, '_agente_cargo', true),
        'telefono' => get_post_meta($agente->ID, '_agente_telefono', true),
        'whatsapp' => get_post_meta($agente->ID, '_agente_whatsapp', true),
        'email' => get_post_meta($agente->ID, '_agente_email', true),
        'email_alt' => get_post_meta($agente->ID, '_agente_email_alt', true),
        'rating' => get_post_meta($agente->ID, '_agente_rating', true),
        'resenas' => get_post_meta($agente->ID, '_agente_resenas', true),
        'experiencia' => get_post_meta($agente->ID, '_agente_experiencia', true),
        'propiedades_vendidas' => get_post_meta($agente->ID, '_agente_propiedades_vendidas', true),
        'comunas' => get_post_meta($agente->ID, '_agente_comunas', true),
        'tipos_propiedad' => get_post_meta($agente->ID, '_agente_tipos_propiedad', true),
        'operaciones' => get_post_meta($agente->ID, '_agente_operaciones', true),
        'avatar' => get_the_post_thumbnail_url($agente->ID, 'thumbnail'),
        'bio' => $agente->post_content
    );
}

// 7. ACTUALIZAR EL CUSTOMIZER DINÁMICAMENTE
function mjpropiedades_refresh_agent_choices() {
    if (is_customize_preview()) {
        $wp_customize = new WP_Customize_Manager();
        $control = $wp_customize->get_control('mjpropiedades_default_agent');
        if ($control) {
            $control->choices = mjpropiedades_get_agents_choices();
        }
    }
}
add_action('wp_loaded', 'mjpropiedades_refresh_agent_choices');

// ===== PANEL DE ADMINISTRACIÓN PERSONALIZADO DEL TEMA =====
// Crear menú de administración para las opciones del tema
function mjpropiedades_admin_menu() {
    add_theme_page(
        'Configuración del Tema',
        'Configuración del Tema',
        'edit_theme_options',
        'mjpropiedades-settings',
        'mjpropiedades_admin_page'
    );
}
add_action('admin_menu', 'mjpropiedades_admin_menu');

// Cargar scripts necesarios para el selector de medios
function mjpropiedades_admin_scripts($hook) {
    // Solo cargar en nuestra página de administración
    if ($hook != 'appearance_page_mjpropiedades-settings') {
        return;
    }
    
    wp_enqueue_media();
    wp_enqueue_script('jquery');
}
add_action('admin_enqueue_scripts', 'mjpropiedades_admin_scripts');

// Página de administración personalizada
function mjpropiedades_admin_page() {
    // Guardar configuraciones si se envió el formulario
    if (isset($_POST['submit']) && wp_verify_nonce($_POST['mjpropiedades_nonce'], 'mjpropiedades_settings')) {
        // Guardar todas las configuraciones
        $settings = array(
            // Hero Slider - Imágenes de fondo
            'mjpropiedades_hero_1',
            'mjpropiedades_hero_2',
            'mjpropiedades_hero_3',
            
            // Hero Slider - Diapositiva 1
            'mjpropiedades_slide_1_tag',
            'mjpropiedades_slide_1_title',
            'mjpropiedades_slide_1_description',
            'mjpropiedades_slide_1_btn_primary',
            'mjpropiedades_slide_1_btn_secondary',
            
            // Hero Slider - Diapositiva 2
            'mjpropiedades_slide_2_tag',
            'mjpropiedades_slide_2_title',
            'mjpropiedades_slide_2_description',
            'mjpropiedades_slide_2_btn_primary',
            'mjpropiedades_slide_2_btn_secondary',
            
            // Hero Slider - Diapositiva 3
            'mjpropiedades_slide_3_tag',
            'mjpropiedades_slide_3_title',
            'mjpropiedades_slide_3_description',
            'mjpropiedades_slide_3_btn_primary',
            'mjpropiedades_slide_3_btn_secondary',
            
            // About Section
            'mjpropiedades_about_text_1',
            'mjpropiedades_about_text_2',
            'mjpropiedades_about_stat_1_number',
            'mjpropiedades_about_stat_1_label',
            'mjpropiedades_about_stat_2_number',
            'mjpropiedades_about_stat_2_label',
            
            // Services Section
            'mjpropiedades_services_tag',
            'mjpropiedades_services_title',
            'mjpropiedades_services_subtitle',
            
            // Testimonials
            'mjpropiedades_testimonials_title',
            'mjpropiedades_testimonials_subtitle',
            
            // Menu Configuration
            'mjpropiedades_menu_alignment',
            
            // Typography
            'mjpropiedades_h1_font_size',
            'mjpropiedades_h2_font_size',
            'mjpropiedades_h3_font_size',
            'mjpropiedades_body_font_size',
            'mjpropiedades_button_font_size',
            
            // Section Titles
            'mjpropiedades_section_title_alignment',
            
            // Contact Info
            'mjpropiedades_contact_email',
            'mjpropiedades_contact_phone',
            'mjpropiedades_contact_address',
            'mjpropiedades_contact_hours',
            
            // Colors
            'mjpropiedades_hero_tag_color',
            'mjpropiedades_hero_tag_text_color',
            'mjpropiedades_hero_title_color',
            'mjpropiedades_hero_description_color',
            
            // Property Cards Colors
            'mjpropiedades_card_background',
            'mjpropiedades_card_title_color',
            'mjpropiedades_card_location_color',
            'mjpropiedades_card_price_color',
            'mjpropiedades_card_button_bg',
            'mjpropiedades_card_button_text',
            
            // Agents
            'mjpropiedades_default_agent',
            'mjpropiedades_show_rating',
            
            // Site Identity
            'mjpropiedades_logo_height_desktop',
            'mjpropiedades_logo_height_tablet',
            'mjpropiedades_logo_height_mobile',
            'mjpropiedades_logo_max_width',
            'mjpropiedades_logo_size_preset',
            
            // Footer Logo
            'mjpropiedades_footer_logo_image',
            'mjpropiedades_footer_logo_position',
            'mjpropiedades_footer_logo_size',
            'mjpropiedades_footer_logo_custom_size',
            'mjpropiedades_footer_logo_show_text',
            'mjpropiedades_footer_logo_text'
        );
        
        foreach ($settings as $setting) {
            if (isset($_POST[$setting])) {
                set_theme_mod($setting, sanitize_text_field($_POST[$setting]));
            }
        }
        
        // Guardar opciones del sitio (blogname y blogdescription)
        if (isset($_POST['blogname'])) {
            update_option('blogname', sanitize_text_field($_POST['blogname']));
        }
        if (isset($_POST['blogdescription'])) {
            update_option('blogdescription', sanitize_text_field($_POST['blogdescription']));
        }
        
        // Guardar logo personalizado
        if (isset($_POST['custom_logo'])) {
            set_theme_mod('custom_logo', intval($_POST['custom_logo']));
        }
        
        // Guardar imágenes de fondo del hero
        if (isset($_POST['mjpropiedades_hero_1'])) {
            set_theme_mod('mjpropiedades_hero_1', intval($_POST['mjpropiedades_hero_1']));
        }
        if (isset($_POST['mjpropiedades_hero_2'])) {
            set_theme_mod('mjpropiedades_hero_2', intval($_POST['mjpropiedades_hero_2']));
        }
        if (isset($_POST['mjpropiedades_hero_3'])) {
            set_theme_mod('mjpropiedades_hero_3', intval($_POST['mjpropiedades_hero_3']));
        }
        
        // Guardar imagen del footer logo
        if (isset($_POST['mjpropiedades_footer_logo_image'])) {
            set_theme_mod('mjpropiedades_footer_logo_image', intval($_POST['mjpropiedades_footer_logo_image']));
        }
        
        echo '<div class="notice notice-success"><p>Configuraciones guardadas exitosamente.</p></div>';
    }
    
    ?>
    <style>
    .mjpropiedades-accordion {
        margin: 20px 0;
    }
    
    .mjpropiedades-accordion-header {
        background: #f1f1f1;
        border: 1px solid #ddd;
        padding: 12px 15px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 600;
        margin: 0;
        position: relative;
        transition: background-color 0.3s ease;
    }
    
    .mjpropiedades-accordion-header:hover {
        background: #e8e8e8;
    }
    
    .mjpropiedades-accordion-header:after {
        content: '+';
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 20px;
        font-weight: bold;
        transition: transform 0.3s ease;
    }
    
    .mjpropiedades-accordion-header.active:after {
        transform: translateY(-50%) rotate(45deg);
    }
    
    .mjpropiedades-accordion-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        border-left: 1px solid #ddd;
        border-right: 1px solid #ddd;
        border-bottom: 1px solid #ddd;
        background: #fff;
    }
    
    .mjpropiedades-accordion-content.active {
        max-height: 2000px; /* Valor suficientemente grande */
    }
    
    .mjpropiedades-accordion-content-inner {
        padding: 20px;
    }
    </style>
    
    <div class="wrap">
        <h1>Configuración del Tema - MJ Propiedades</h1>
        <p>Desde aquí puedes configurar todas las opciones del tema sin usar el personalizador. Haz clic en cada sección para expandir o contraer su contenido.</p>
        
        <form method="post" action="">
            <?php wp_nonce_field('mjpropiedades_settings', 'mjpropiedades_nonce'); ?>
            
            <!-- Identidad del Sitio -->
            <div class="mjpropiedades-accordion">
                <h2 class="mjpropiedades-accordion-header">Identidad del Sitio</h2>
                <div class="mjpropiedades-accordion-content">
                    <div class="mjpropiedades-accordion-content-inner">
                        <table class="form-table">
                <tr>
                    <th scope="row">Logo del Sitio</th>
                    <td>
                        <?php
                        $logo_id = get_theme_mod('custom_logo');
                        if ($logo_id) {
                            $logo_url = wp_get_attachment_image_url($logo_id, 'full');
                            echo '<div style="margin-bottom: 10px;">';
                            echo '<img src="' . esc_url($logo_url) . '" style="max-height: 60px; max-width: 200px;" id="logo-preview" />';
                            echo '</div>';
                        } else {
                            echo '<div style="margin-bottom: 10px; display: none;" id="logo-preview-container">';
                            echo '<img id="logo-preview" style="max-height: 60px; max-width: 200px;" />';
                            echo '</div>';
                        }
                        ?>
                        
                        <input type="hidden" name="custom_logo" id="custom_logo" value="<?php echo esc_attr($logo_id); ?>" />
                        <button type="button" class="button" id="upload-logo-button">
                            <?php echo $logo_id ? 'Cambiar Logo' : 'Seleccionar Logo'; ?>
                        </button>
                        <?php if ($logo_id): ?>
                        <button type="button" class="button" id="remove-logo-button">Eliminar Logo</button>
                        <?php endif; ?>
                        
                        <p class="description">Selecciona una imagen para usar como logo del sitio. Formatos recomendados: PNG, JPG, SVG. Tamaño recomendado: máximo 300x100 píxeles.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Título del Sitio</th>
                    <td>
                        <input type="text" name="blogname" value="<?php echo esc_attr(get_option('blogname')); ?>" class="regular-text" />
                        <p class="description">Título que aparece en el navegador y en los resultados de búsqueda.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Lema del Sitio</th>
                    <td>
                        <input type="text" name="blogdescription" value="<?php echo esc_attr(get_option('blogdescription')); ?>" class="regular-text" />
                        <p class="description">Descripción breve del sitio que aparece bajo el título.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Preset de Tamaño del Logo</th>
                    <td>
                        <select name="mjpropiedades_logo_size_preset">
                            <option value="small" <?php selected(get_theme_mod('mjpropiedades_logo_size_preset', 'medium'), 'small'); ?>>Pequeño (30px)</option>
                            <option value="medium" <?php selected(get_theme_mod('mjpropiedades_logo_size_preset', 'medium'), 'medium'); ?>>Mediano (50px)</option>
                            <option value="large" <?php selected(get_theme_mod('mjpropiedades_logo_size_preset', 'medium'), 'large'); ?>>Grande (70px)</option>
                            <option value="custom" <?php selected(get_theme_mod('mjpropiedades_logo_size_preset', 'medium'), 'custom'); ?>>Personalizado</option>
                        </select>
                        <p class="description">Selecciona un tamaño predefinido o personalizado para el logo.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Altura del Logo en Desktop (px)</th>
                    <td>
                        <input type="number" name="mjpropiedades_logo_height_desktop" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_logo_height_desktop', '50')); ?>" min="20" max="150" step="5" />
                        <p class="description">Altura máxima del logo en pantallas de escritorio (20-150px).</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Altura del Logo en Tablet (px)</th>
                    <td>
                        <input type="number" name="mjpropiedades_logo_height_tablet" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_logo_height_tablet', '45')); ?>" min="15" max="120" step="5" />
                        <p class="description">Altura máxima del logo en tablets (15-120px).</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Altura del Logo en Móvil (px)</th>
                    <td>
                        <input type="number" name="mjpropiedades_logo_height_mobile" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_logo_height_mobile', '40')); ?>" min="15" max="100" step="5" />
                        <p class="description">Altura máxima del logo en dispositivos móviles (15-100px).</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Ancho Máximo del Logo (px)</th>
                    <td>
                        <input type="number" name="mjpropiedades_logo_max_width" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_logo_max_width', '200')); ?>" min="100" max="400" step="10" />
                        <p class="description">Ancho máximo del logo en píxeles (100-400px).</p>
                    </td>
                </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Logo del Footer -->
            <div class="mjpropiedades-accordion">
                <h2 class="mjpropiedades-accordion-header">Logo del Footer</h2>
                <div class="mjpropiedades-accordion-content">
                    <div class="mjpropiedades-accordion-content-inner">
                        <table class="form-table">
                <tr>
                    <th scope="row">Logo del Footer</th>
                    <td>
                        <?php
                        $footer_logo_id = get_theme_mod('mjpropiedades_footer_logo_image');
                        if ($footer_logo_id) {
                            $footer_logo_url = wp_get_attachment_image_url($footer_logo_id, 'full');
                            echo '<div style="margin-bottom: 10px;">';
                            echo '<img src="' . esc_url($footer_logo_url) . '" style="max-height: 60px; max-width: 200px;" id="footer-logo-preview" />';
                            echo '</div>';
                        } else {
                            echo '<div style="margin-bottom: 10px; display: none;" id="footer-logo-preview-container">';
                            echo '<img id="footer-logo-preview" style="max-height: 60px; max-width: 200px;" />';
                            echo '</div>';
                        }
                        ?>
                        
                        <input type="hidden" name="mjpropiedades_footer_logo_image" id="mjpropiedades_footer_logo_image" value="<?php echo esc_attr($footer_logo_id); ?>" />
                        <button type="button" class="button" id="upload-footer-logo-button">
                            <?php echo $footer_logo_id ? 'Cambiar Imagen' : 'Seleccionar Imagen'; ?>
                        </button>
                        <?php if ($footer_logo_id): ?>
                        <button type="button" class="button" id="remove-footer-logo-button">Eliminar</button>
                        <?php endif; ?>
                        <p class="description">Selecciona una imagen para el logo del footer. Si no se selecciona, se usará el logo del header.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Posición del Logo en el Footer</th>
                    <td>
                        <select name="mjpropiedades_footer_logo_position">
                            <option value="left" <?php selected(get_theme_mod('mjpropiedades_footer_logo_position', 'left'), 'left'); ?>>Izquierda</option>
                            <option value="center" <?php selected(get_theme_mod('mjpropiedades_footer_logo_position', 'left'), 'center'); ?>>Centro</option>
                            <option value="right" <?php selected(get_theme_mod('mjpropiedades_footer_logo_position', 'left'), 'right'); ?>>Derecha</option>
                        </select>
                        <p class="description">Selecciona dónde quieres que aparezca el logo en el footer.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Tamaño del Logo del Footer</th>
                    <td>
                        <select name="mjpropiedades_footer_logo_size">
                            <option value="small" <?php selected(get_theme_mod('mjpropiedades_footer_logo_size', 'medium'), 'small'); ?>>Pequeño (60px)</option>
                            <option value="medium" <?php selected(get_theme_mod('mjpropiedades_footer_logo_size', 'medium'), 'medium'); ?>>Mediano (80px)</option>
                            <option value="large" <?php selected(get_theme_mod('mjpropiedades_footer_logo_size', 'medium'), 'large'); ?>>Grande (100px)</option>
                            <option value="custom" <?php selected(get_theme_mod('mjpropiedades_footer_logo_size', 'medium'), 'custom'); ?>>Personalizado</option>
                        </select>
                        <p class="description">Selecciona el tamaño del logo en el footer.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Tamaño Personalizado (px)</th>
                    <td>
                        <input type="number" name="mjpropiedades_footer_logo_custom_size" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_footer_logo_custom_size', '80')); ?>" min="30" max="200" step="5" />
                        <p class="description">Especifica el tamaño exacto del logo en píxeles (30-200px).</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Mostrar Texto Descriptivo</th>
                    <td>
                        <label>
                            <input type="checkbox" name="mjpropiedades_footer_logo_show_text" value="1" <?php checked(get_theme_mod('mjpropiedades_footer_logo_show_text', true), true); ?> />
                            Muestra el texto descriptivo debajo del logo
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Texto Descriptivo del Footer</th>
                    <td>
                        <textarea name="mjpropiedades_footer_logo_text" rows="3" class="large-text"><?php echo esc_textarea(get_theme_mod('mjpropiedades_footer_logo_text', 'Tu corredora de confianza especializada en la Cuarta Región de Chile.')); ?></textarea>
                        <p class="description">Texto que aparece debajo del logo en el footer.</p>
                    </td>
                </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Hero Slider -->
            <div class="mjpropiedades-accordion">
                <h2 class="mjpropiedades-accordion-header">Hero Slider - Diapositivas</h2>
                <div class="mjpropiedades-accordion-content">
                    <div class="mjpropiedades-accordion-content-inner">
            
            <!-- Diapositiva 1 -->
            <h3>Diapositiva 1</h3>
            <table class="form-table">
                <tr>
                    <th scope="row">Imagen de Fondo</th>
                    <td>
                        <?php
                        $hero_1_id = get_theme_mod('mjpropiedades_hero_1');
                        if ($hero_1_id) {
                            $hero_1_url = wp_get_attachment_image_url($hero_1_id, 'medium');
                            echo '<div style="margin-bottom: 10px;">';
                            echo '<img src="' . esc_url($hero_1_url) . '" style="max-height: 100px; max-width: 200px;" id="hero-1-preview" />';
                            echo '</div>';
                        } else {
                            echo '<div style="margin-bottom: 10px; display: none;" id="hero-1-preview-container">';
                            echo '<img id="hero-1-preview" style="max-height: 100px; max-width: 200px;" />';
                            echo '</div>';
                        }
                        ?>
                        <input type="hidden" name="mjpropiedades_hero_1" id="mjpropiedades_hero_1" value="<?php echo esc_attr($hero_1_id); ?>" />
                        <button type="button" class="button" id="upload-hero-1-button">
                            <?php echo $hero_1_id ? 'Cambiar Imagen' : 'Seleccionar Imagen'; ?>
                        </button>
                        <?php if ($hero_1_id): ?>
                        <button type="button" class="button" id="remove-hero-1-button">Eliminar</button>
                        <?php endif; ?>
                        <p class="description">Imagen de fondo para la primera diapositiva del slider.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Tag</th>
                    <td><input type="text" name="mjpropiedades_slide_1_tag" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_slide_1_tag', 'Vende tu propiedad al mejor precio')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Título</th>
                    <td><input type="text" name="mjpropiedades_slide_1_title" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_slide_1_title', 'Encuentra tu Hogar Ideal')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Descripción</th>
                    <td><textarea name="mjpropiedades_slide_1_description" rows="3" class="large-text"><?php echo esc_textarea(get_theme_mod('mjpropiedades_slide_1_description', 'Atendemos en Copiapó, Viña del Mar, La Serena y nos expandimos a más ciudades. Descubre propiedades exclusivas con asesoría personalizada y certificada en todo el proceso de compra.')); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row">Botón Primario</th>
                    <td><input type="text" name="mjpropiedades_slide_1_btn_primary" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_slide_1_btn_primary', 'Ver Propiedades')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Botón Secundario</th>
                    <td><input type="text" name="mjpropiedades_slide_1_btn_secondary" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_slide_1_btn_secondary', 'Solicitar Tasación')); ?>" class="regular-text" /></td>
                </tr>
            </table>
            
            <!-- Diapositiva 2 -->
            <h3>Diapositiva 2</h3>
            <table class="form-table">
                <tr>
                    <th scope="row">Imagen de Fondo</th>
                    <td>
                        <?php
                        $hero_2_id = get_theme_mod('mjpropiedades_hero_2');
                        if ($hero_2_id) {
                            $hero_2_url = wp_get_attachment_image_url($hero_2_id, 'medium');
                            echo '<div style="margin-bottom: 10px;">';
                            echo '<img src="' . esc_url($hero_2_url) . '" style="max-height: 100px; max-width: 200px;" id="hero-2-preview" />';
                            echo '</div>';
                        } else {
                            echo '<div style="margin-bottom: 10px; display: none;" id="hero-2-preview-container">';
                            echo '<img id="hero-2-preview" style="max-height: 100px; max-width: 200px;" />';
                            echo '</div>';
                        }
                        ?>
                        <input type="hidden" name="mjpropiedades_hero_2" id="mjpropiedades_hero_2" value="<?php echo esc_attr($hero_2_id); ?>" />
                        <button type="button" class="button" id="upload-hero-2-button">
                            <?php echo $hero_2_id ? 'Cambiar Imagen' : 'Seleccionar Imagen'; ?>
                        </button>
                        <?php if ($hero_2_id): ?>
                        <button type="button" class="button" id="remove-hero-2-button">Eliminar</button>
                        <?php endif; ?>
                        <p class="description">Imagen de fondo para la segunda diapositiva del slider.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Tag</th>
                    <td><input type="text" name="mjpropiedades_slide_2_tag" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_slide_2_tag', 'Arrienda tu próxima casa')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Título</th>
                    <td><input type="text" name="mjpropiedades_slide_2_title" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_slide_2_title', 'Tu Nuevo Hogar Te Espera')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Descripción</th>
                    <td><textarea name="mjpropiedades_slide_2_description" rows="3" class="large-text"><?php echo esc_textarea(get_theme_mod('mjpropiedades_slide_2_description', 'Encuentra la propiedad perfecta para arrendar. Amplia variedad de opciones en las mejores ubicaciones de la región.')); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row">Botón Primario</th>
                    <td><input type="text" name="mjpropiedades_slide_2_btn_primary" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_slide_2_btn_primary', 'Ver Arriendos')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Botón Secundario</th>
                    <td><input type="text" name="mjpropiedades_slide_2_btn_secondary" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_slide_2_btn_secondary', 'Contactar')); ?>" class="regular-text" /></td>
                </tr>
            </table>
            
            <!-- Diapositiva 3 -->
            <h3>Diapositiva 3</h3>
            <table class="form-table">
                <tr>
                    <th scope="row">Imagen de Fondo</th>
                    <td>
                        <?php
                        $hero_3_id = get_theme_mod('mjpropiedades_hero_3');
                        if ($hero_3_id) {
                            $hero_3_url = wp_get_attachment_image_url($hero_3_id, 'medium');
                            echo '<div style="margin-bottom: 10px;">';
                            echo '<img src="' . esc_url($hero_3_url) . '" style="max-height: 100px; max-width: 200px;" id="hero-3-preview" />';
                            echo '</div>';
                        } else {
                            echo '<div style="margin-bottom: 10px; display: none;" id="hero-3-preview-container">';
                            echo '<img id="hero-3-preview" style="max-height: 100px; max-width: 200px;" />';
                            echo '</div>';
                        }
                        ?>
                        <input type="hidden" name="mjpropiedades_hero_3" id="mjpropiedades_hero_3" value="<?php echo esc_attr($hero_3_id); ?>" />
                        <button type="button" class="button" id="upload-hero-3-button">
                            <?php echo $hero_3_id ? 'Cambiar Imagen' : 'Seleccionar Imagen'; ?>
                        </button>
                        <?php if ($hero_3_id): ?>
                        <button type="button" class="button" id="remove-hero-3-button">Eliminar</button>
                        <?php endif; ?>
                        <p class="description">Imagen de fondo para la tercera diapositiva del slider.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Tag</th>
                    <td><input type="text" name="mjpropiedades_slide_3_tag" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_slide_3_tag', 'Asesoría profesional')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Título</th>
                    <td><input type="text" name="mjpropiedades_slide_3_title" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_slide_3_title', 'Expertos en Bienes Raíces')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Descripción</th>
                    <td><textarea name="mjpropiedades_slide_3_description" rows="3" class="large-text"><?php echo esc_textarea(get_theme_mod('mjpropiedades_slide_3_description', 'Más de 10 años de experiencia en el mercado inmobiliario. Te acompañamos en cada paso del proceso con profesionalismo y dedicación.')); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row">Botón Primario</th>
                    <td><input type="text" name="mjpropiedades_slide_3_btn_primary" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_slide_3_btn_primary', 'Nuestros Servicios')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Botón Secundario</th>
                    <td><input type="text" name="mjpropiedades_slide_3_btn_secondary" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_slide_3_btn_secondary', 'Conócenos')); ?>" class="regular-text" /></td>
                </tr>
            </table>
            
            <!-- Colores del Hero Slider -->
            <h3>Colores del Hero Slider</h3>
            <table class="form-table">
                <tr>
                    <th scope="row">Color de las Viñetas (Tags)</th>
                    <td>
                        <input type="color" name="mjpropiedades_hero_tag_color" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_hero_tag_color', '#007cba')); ?>" />
                        <p class="description">Selecciona el color de fondo de las viñetas que aparecen arriba de los títulos.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Color del Texto de las Viñetas</th>
                    <td>
                        <input type="color" name="mjpropiedades_hero_tag_text_color" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_hero_tag_text_color', '#ffffff')); ?>" />
                        <p class="description">Selecciona el color del texto dentro de las viñetas.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Color de los Títulos</th>
                    <td>
                        <input type="color" name="mjpropiedades_hero_title_color" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_hero_title_color', '#333333')); ?>" />
                        <p class="description">Selecciona el color de los títulos principales del slider.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Color de las Descripciones</th>
                    <td>
                        <input type="color" name="mjpropiedades_hero_description_color" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_hero_description_color', '#666666')); ?>" />
                        <p class="description">Selecciona el color del texto de las descripciones.</p>
                    </td>
                </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Sección About -->
            <div class="mjpropiedades-accordion">
                <h2 class="mjpropiedades-accordion-header">Sección Quiénes Somos</h2>
                <div class="mjpropiedades-accordion-content">
                    <div class="mjpropiedades-accordion-content-inner">
                        <table class="form-table">
                <tr>
                    <th scope="row">Texto Principal</th>
                    <td><textarea name="mjpropiedades_about_text_1" rows="3" class="large-text"><?php echo esc_textarea(get_theme_mod('mjpropiedades_about_text_1', 'Somos una empresa especializada en el mercado inmobiliario con más de 10 años de experiencia.')); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row">Texto Secundario</th>
                    <td><textarea name="mjpropiedades_about_text_2" rows="3" class="large-text"><?php echo esc_textarea(get_theme_mod('mjpropiedades_about_text_2', 'Nuestro equipo de profesionales certificados te brinda asesoría personalizada en todo el proceso.')); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row">Estadística 1 - Número</th>
                    <td><input type="text" name="mjpropiedades_about_stat_1_number" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_about_stat_1_number', '500+')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Estadística 1 - Etiqueta</th>
                    <td><input type="text" name="mjpropiedades_about_stat_1_label" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_about_stat_1_label', 'Propiedades Vendidas')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Estadística 2 - Número</th>
                    <td><input type="text" name="mjpropiedades_about_stat_2_number" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_about_stat_2_number', '100+')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Estadística 2 - Etiqueta</th>
                    <td><input type="text" name="mjpropiedades_about_stat_2_label" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_about_stat_2_label', 'Clientes Satisfechos')); ?>" class="regular-text" />                    </td>
                </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Sección Servicios -->
            <div class="mjpropiedades-accordion">
                <h2 class="mjpropiedades-accordion-header">Sección Nuestros Servicios</h2>
                <div class="mjpropiedades-accordion-content">
                    <div class="mjpropiedades-accordion-content-inner">
                        <table class="form-table">
                <tr>
                    <th scope="row">Tag de Servicios</th>
                    <td><input type="text" name="mjpropiedades_services_tag" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_services_tag', 'Nuestros Servicios')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Título de Servicios</th>
                    <td><input type="text" name="mjpropiedades_services_title" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_services_title', '¿Por qué elegirnos?')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Subtítulo de Servicios</th>
                    <td><textarea name="mjpropiedades_services_subtitle" rows="2" class="large-text"><?php echo esc_textarea(get_theme_mod('mjpropiedades_services_subtitle', 'Ofrecemos servicios integrales para satisfacer todas tus necesidades inmobiliarias')); ?></textarea></td>
                </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Sección Testimonios -->
            <div class="mjpropiedades-accordion">
                <h2 class="mjpropiedades-accordion-header">Sección Testimonios</h2>
                <div class="mjpropiedades-accordion-content">
                    <div class="mjpropiedades-accordion-content-inner">
                        <table class="form-table">
                <tr>
                    <th scope="row">Título de Testimonios</th>
                    <td><input type="text" name="mjpropiedades_testimonials_title" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_testimonials_title', 'Lo que dicen nuestros clientes')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Subtítulo de Testimonios</th>
                    <td><textarea name="mjpropiedades_testimonials_subtitle" rows="2" class="large-text"><?php echo esc_textarea(get_theme_mod('mjpropiedades_testimonials_subtitle', 'La satisfacción de nuestros clientes es nuestra mayor recompensa')); ?></textarea></td>
                </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Configuración del Menú -->
            <div class="mjpropiedades-accordion">
                <h2 class="mjpropiedades-accordion-header">Configuración del Menú</h2>
                <div class="mjpropiedades-accordion-content">
                    <div class="mjpropiedades-accordion-content-inner">
                        <table class="form-table">
                <tr>
                    <th scope="row">Alineación del Menú</th>
                    <td>
                        <select name="mjpropiedades_menu_alignment">
                            <option value="left" <?php selected(get_theme_mod('mjpropiedades_menu_alignment', 'right'), 'left'); ?>>Izquierda</option>
                            <option value="center" <?php selected(get_theme_mod('mjpropiedades_menu_alignment', 'right'), 'center'); ?>>Centro</option>
                            <option value="right" <?php selected(get_theme_mod('mjpropiedades_menu_alignment', 'right'), 'right'); ?>>Derecha</option>
                        </select>
                    </td>
                </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Tipografía -->
            <div class="mjpropiedades-accordion">
                <h2 class="mjpropiedades-accordion-header">Tipografía</h2>
                <div class="mjpropiedades-accordion-content">
                    <div class="mjpropiedades-accordion-content-inner">
                        <table class="form-table">
                <tr>
                    <th scope="row">Tamaño H1</th>
                    <td><input type="text" name="mjpropiedades_h1_font_size" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_h1_font_size', '2.25rem')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Tamaño H2</th>
                    <td><input type="text" name="mjpropiedades_h2_font_size" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_h2_font_size', '1.5rem')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Tamaño H3</th>
                    <td><input type="text" name="mjpropiedades_h3_font_size" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_h3_font_size', '1.25rem')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Tamaño Texto Principal</th>
                    <td><input type="text" name="mjpropiedades_body_font_size" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_body_font_size', '1rem')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Tamaño Botones</th>
                    <td><input type="text" name="mjpropiedades_button_font_size" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_button_font_size', '1rem')); ?>" class="regular-text" /></td>
                </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Títulos de Secciones -->
            <div class="mjpropiedades-accordion">
                <h2 class="mjpropiedades-accordion-header">Títulos de Secciones</h2>
                <div class="mjpropiedades-accordion-content">
                    <div class="mjpropiedades-accordion-content-inner">
                        <table class="form-table">
                <tr>
                    <th scope="row">Alineación de Títulos</th>
                    <td>
                        <select name="mjpropiedades_section_title_alignment">
                            <option value="left" <?php selected(get_theme_mod('mjpropiedades_section_title_alignment', 'center'), 'left'); ?>>Izquierda</option>
                            <option value="center" <?php selected(get_theme_mod('mjpropiedades_section_title_alignment', 'center'), 'center'); ?>>Centro</option>
                            <option value="right" <?php selected(get_theme_mod('mjpropiedades_section_title_alignment', 'center'), 'right'); ?>>Derecha</option>
                        </select>
                    </td>
                </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Información de Contacto -->
            <div class="mjpropiedades-accordion">
                <h2 class="mjpropiedades-accordion-header">Información de Contacto</h2>
                <div class="mjpropiedades-accordion-content">
                    <div class="mjpropiedades-accordion-content-inner">
                        <table class="form-table">
                <tr>
                    <th scope="row">Email de Contacto</th>
                    <td><input type="email" name="mjpropiedades_contact_email" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_contact_email', 'consultas@homeisa.cl')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Teléfono de Contacto</th>
                    <td><input type="text" name="mjpropiedades_contact_phone" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_contact_phone', '+56 9 1234 5678')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Dirección</th>
                    <td><textarea name="mjpropiedades_contact_address" rows="2" class="large-text"><?php echo esc_textarea(get_theme_mod('mjpropiedades_contact_address', 'La Serena, Región de Coquimbo, Chile')); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row">Horarios</th>
                    <td><textarea name="mjpropiedades_contact_hours" rows="2" class="large-text"><?php echo esc_textarea(get_theme_mod('mjpropiedades_contact_hours', 'Lunes a Viernes: 9:00 - 18:00\nSábados: 9:00 - 14:00')); ?></textarea></td>
                </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Colores del Hero -->
            <div class="mjpropiedades-accordion">
                <h2 class="mjpropiedades-accordion-header">Colores del Hero</h2>
                <div class="mjpropiedades-accordion-content">
                    <div class="mjpropiedades-accordion-content-inner">
                        <table class="form-table">
                <tr>
                    <th scope="row">Color del Tag</th>
                    <td><input type="color" name="mjpropiedades_hero_tag_color" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_hero_tag_color', '#007cba')); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Color del Texto del Tag</th>
                    <td><input type="color" name="mjpropiedades_hero_tag_text_color" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_hero_tag_text_color', '#ffffff')); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Color del Título</th>
                    <td><input type="color" name="mjpropiedades_hero_title_color" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_hero_title_color', '#333333')); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Color de la Descripción</th>
                    <td><input type="color" name="mjpropiedades_hero_description_color" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_hero_description_color', '#666666')); ?>" /></td>
                </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Colores de Tarjetas de Propiedades -->
            <div class="mjpropiedades-accordion">
                <h2 class="mjpropiedades-accordion-header">Colores de Tarjetas de Propiedades</h2>
                <div class="mjpropiedades-accordion-content">
                    <div class="mjpropiedades-accordion-content-inner">
                        <table class="form-table">
                <tr>
                    <th scope="row">Color de Fondo de Tarjeta</th>
                    <td><input type="color" name="mjpropiedades_card_background" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_card_background', '#ffffff')); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Color del Título de Propiedad</th>
                    <td><input type="color" name="mjpropiedades_card_title_color" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_card_title_color', '#333333')); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Color de Ubicación</th>
                    <td><input type="color" name="mjpropiedades_card_location_color" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_card_location_color', '#666666')); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Color del Precio</th>
                    <td><input type="color" name="mjpropiedades_card_price_color" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_card_price_color', '#007cba')); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Color de Fondo del Botón</th>
                    <td><input type="color" name="mjpropiedades_card_button_bg" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_card_button_bg', '#007cba')); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Color del Texto del Botón</th>
                    <td><input type="color" name="mjpropiedades_card_button_text" value="<?php echo esc_attr(get_theme_mod('mjpropiedades_card_button_text', '#ffffff')); ?>" /></td>
                </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Configuración de Agentes -->
            <div class="mjpropiedades-accordion">
                <h2 class="mjpropiedades-accordion-header">Configuración de Agentes</h2>
                <div class="mjpropiedades-accordion-content">
                    <div class="mjpropiedades-accordion-content-inner">
                        <table class="form-table">
                <tr>
                    <th scope="row">Agente por Defecto</th>
                    <td>
                        <select name="mjpropiedades_default_agent">
                            <option value="">Sin agente por defecto</option>
                            <?php
                            $agentes = get_posts(array(
                                'post_type' => 'agente',
                                'posts_per_page' => -1,
                                'meta_query' => array(
                                    array(
                                        'key' => '_agente_activo',
                                        'value' => '1',
                                        'compare' => '='
                                    )
                                )
                            ));
                            foreach ($agentes as $agente) {
                                echo '<option value="' . $agente->ID . '" ' . selected(get_theme_mod('mjpropiedades_default_agent', ''), $agente->ID, false) . '>' . $agente->post_title . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Mostrar Rating</th>
                    <td>
                        <label>
                            <input type="checkbox" name="mjpropiedades_show_rating" value="1" <?php checked(get_theme_mod('mjpropiedades_show_rating', true), true); ?> />
                            Mostrar rating de agentes
                        </label>
                    </td>
                </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <p class="submit">
                <input type="submit" name="submit" class="button-primary" value="Guardar Configuración" />
            </p>
        </form>
    </div>
    
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var mediaUploader;
        
        // Funcionalidad del acordeón
        $('.mjpropiedades-accordion-header').click(function() {
            var content = $(this).next('.mjpropiedades-accordion-content');
            var isActive = $(this).hasClass('active');
            
            // Cerrar todos los otros acordeones
            $('.mjpropiedades-accordion-header').removeClass('active');
            $('.mjpropiedades-accordion-content').removeClass('active');
            
            // Si este acordeón no estaba activo, lo abrimos
            if (!isActive) {
                $(this).addClass('active');
                content.addClass('active');
            }
        });
        
        // Función genérica para manejar selectores de medios
        function createMediaUploader(buttonId, inputId, previewId, previewContainerId, removeButtonId, title) {
            $(buttonId).click(function(e) {
                e.preventDefault();
                
                // Si el uploader ya existe, lo reutilizamos
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                
                // Creamos el uploader
                mediaUploader = wp.media.frames.file_frame = wp.media({
                    title: title,
                    button: {
                        text: 'Usar como Imagen'
                    },
                    multiple: false,
                    library: {
                        type: 'image'
                    }
                });
                
                // Cuando se selecciona una imagen
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    
                    // Actualizar el campo hidden
                    $(inputId).val(attachment.id);
                    
                    // Mostrar la imagen seleccionada
                    $(previewId).attr('src', attachment.url);
                    $(previewContainerId).show();
                    
                    // Cambiar el texto del botón
                    $(buttonId).text('Cambiar Imagen');
                    
                    // Mostrar el botón de eliminar si no está visible
                    if ($(removeButtonId).length === 0) {
                        $(buttonId).after(' <button type="button" class="button" id="' + removeButtonId.replace('#', '') + '">Eliminar</button>');
                    } else {
                        $(removeButtonId).show();
                    }
                });
                
                // Abrir el uploader
                mediaUploader.open();
            });
        }
        
        // Función genérica para eliminar imágenes
        function createRemoveHandler(inputId, previewContainerId, buttonId, removeButtonId, buttonText) {
            $(document).on('click', removeButtonId, function(e) {
                e.preventDefault();
                
                // Limpiar el campo
                $(inputId).val('');
                
                // Ocultar la imagen
                $(previewContainerId).hide();
                
                // Cambiar el texto del botón
                $(buttonId).text(buttonText);
                
                // Ocultar el botón de eliminar
                $(removeButtonId).hide();
            });
        }
        
        // Logo del sitio
        createMediaUploader('#upload-logo-button', '#custom_logo', '#logo-preview', '#logo-preview-container', '#remove-logo-button', 'Seleccionar Logo');
        createRemoveHandler('#custom_logo', '#logo-preview-container', '#upload-logo-button', '#remove-logo-button', 'Seleccionar Logo');
        
        // Logo del footer
        createMediaUploader('#upload-footer-logo-button', '#mjpropiedades_footer_logo_image', '#footer-logo-preview', '#footer-logo-preview-container', '#remove-footer-logo-button', 'Seleccionar Logo del Footer');
        createRemoveHandler('#mjpropiedades_footer_logo_image', '#footer-logo-preview-container', '#upload-footer-logo-button', '#remove-footer-logo-button', 'Seleccionar Imagen');
        
        // Hero 1
        createMediaUploader('#upload-hero-1-button', '#mjpropiedades_hero_1', '#hero-1-preview', '#hero-1-preview-container', '#remove-hero-1-button', 'Seleccionar Imagen Hero 1');
        createRemoveHandler('#mjpropiedades_hero_1', '#hero-1-preview-container', '#upload-hero-1-button', '#remove-hero-1-button', 'Seleccionar Imagen');
        
        // Hero 2
        createMediaUploader('#upload-hero-2-button', '#mjpropiedades_hero_2', '#hero-2-preview', '#hero-2-preview-container', '#remove-hero-2-button', 'Seleccionar Imagen Hero 2');
        createRemoveHandler('#mjpropiedades_hero_2', '#hero-2-preview-container', '#upload-hero-2-button', '#remove-hero-2-button', 'Seleccionar Imagen');
        
        // Hero 3
        createMediaUploader('#upload-hero-3-button', '#mjpropiedades_hero_3', '#hero-3-preview', '#hero-3-preview-container', '#remove-hero-3-button', 'Seleccionar Imagen Hero 3');
        createRemoveHandler('#mjpropiedades_hero_3', '#hero-3-preview-container', '#upload-hero-3-button', '#remove-hero-3-button', 'Seleccionar Imagen');
    });
    </script>
    <?php
}

?>
