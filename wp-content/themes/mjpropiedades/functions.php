<?php
/**
 * Home Isa Propiedades Theme Functions
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

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
            $precio_text = $precio ? '$' . number_format($precio, 0, ',', '.') : 'Consultar';
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
    ));
    
    // Sección Servicios
    $wp_customize->add_section('mjpropiedades_services', array(
        'title'    => __('Sección Nuestros Servicios', 'mjpropiedades'),
        'priority' => 35,
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
        'priority' => 32,
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
        'priority' => 35,
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
        'priority' => 30,
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
        'priority' => 45,
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
    if (is_page('propiedades')) {
        $new_template = locate_template(array('page-propiedades.php'));
        if (!empty($new_template)) {
            return $new_template;
        }
    }
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
        'inicio' => 'Página de Inicio',
        'propiedades' => 'Página de Propiedades'
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

// Agregar sección de colores para propiedades destacadas
function mjpropiedades_add_property_colors_section($wp_customize) {
    // Sección de colores para propiedades
    $wp_customize->add_section('mjpropiedades_property_colors', array(
        'title'    => __('Colores de Tarjetas de Propiedades', 'mjpropiedades'),
        'priority' => 40,
    ));
    
    // Color del precio
    $wp_customize->add_setting('mjpropiedades_property_price_color', array(
        'default' => '#8b4513',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_property_price_color', array(
        'label' => __('Color del Precio', 'mjpropiedades'),
        'section' => 'mjpropiedades_property_colors',
    )));
    
    // Color del título
    $wp_customize->add_setting('mjpropiedades_property_title_color', array(
        'default' => '#1f2937',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_property_title_color', array(
        'label' => __('Color del Título', 'mjpropiedades'),
        'section' => 'mjpropiedades_property_colors',
    )));
    
    // Color de la ubicación
    $wp_customize->add_setting('mjpropiedades_property_location_color', array(
        'default' => '#6b7280',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_property_location_color', array(
        'label' => __('Color de la Ubicación', 'mjpropiedades'),
        'section' => 'mjpropiedades_property_colors',
    )));
    
    // Color de las características
    $wp_customize->add_setting('mjpropiedades_property_features_color', array(
        'default' => '#6b7280',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_property_features_color', array(
        'label' => __('Color de las Características', 'mjpropiedades'),
        'section' => 'mjpropiedades_property_colors',
    )));
    
    // Color del botón
    $wp_customize->add_setting('mjpropiedades_property_button_color', array(
        'default' => '#8b4513',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_property_button_color', array(
        'label' => __('Color del Botón', 'mjpropiedades'),
        'section' => 'mjpropiedades_property_colors',
    )));
    
    // Color del botón al hacer hover
    $wp_customize->add_setting('mjpropiedades_property_button_hover_color', array(
        'default' => '#6d3410',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_property_button_hover_color', array(
        'label' => __('Color del Botón (Hover)', 'mjpropiedades'),
        'section' => 'mjpropiedades_property_colors',
    )));
    
    // Color del tag de venta
    $wp_customize->add_setting('mjpropiedades_property_tag_venta_color', array(
        'default' => '#10b981',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_property_tag_venta_color', array(
        'label' => __('Color del Tag "Venta"', 'mjpropiedades'),
        'section' => 'mjpropiedades_property_colors',
    )));
    
    // Color del tag de arriendo
    $wp_customize->add_setting('mjpropiedades_property_tag_arriendo_color', array(
        'default' => '#3b82f6',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mjpropiedades_property_tag_arriendo_color', array(
        'label' => __('Color del Tag "Arriendo"', 'mjpropiedades'),
        'section' => 'mjpropiedades_property_colors',
    )));
}
add_action('customize_register', 'mjpropiedades_add_property_colors_section');

// Agregar CSS dinámico para colores de propiedades
function mjpropiedades_property_colors_css() {
    $price_color = get_theme_mod('mjpropiedades_property_price_color', '#8b4513');
    $title_color = get_theme_mod('mjpropiedades_property_title_color', '#1f2937');
    $location_color = get_theme_mod('mjpropiedades_property_location_color', '#6b7280');
    $features_color = get_theme_mod('mjpropiedades_property_features_color', '#6b7280');
    $button_color = get_theme_mod('mjpropiedades_property_button_color', '#8b4513');
    $button_hover_color = get_theme_mod('mjpropiedades_property_button_hover_color', '#6d3410');
    $tag_venta_color = get_theme_mod('mjpropiedades_property_tag_venta_color', '#10b981');
    $tag_arriendo_color = get_theme_mod('mjpropiedades_property_tag_arriendo_color', '#3b82f6');
    
    echo '<style type="text/css">';
    echo ':root {';
    echo '--property-price-color: ' . esc_attr($price_color) . ';';
    echo '--property-title-color: ' . esc_attr($title_color) . ';';
    echo '--property-location-color: ' . esc_attr($location_color) . ';';
    echo '--property-features-color: ' . esc_attr($features_color) . ';';
    echo '--property-button-color: ' . esc_attr($button_color) . ';';
    echo '--property-button-hover-color: ' . esc_attr($button_hover_color) . ';';
    echo '--property-tag-venta-color: ' . esc_attr($tag_venta_color) . ';';
    echo '--property-tag-arriendo-color: ' . esc_attr($tag_arriendo_color) . ';';
    echo '}';
    echo '</style>';
}
add_action('wp_head', 'mjpropiedades_property_colors_css');

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
add_action('after_setup_theme', 'mjpropiedades_create_properties_page');

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
    $args = array(
        'post_type' => 'propiedad',
        'posts_per_page' => 12,
        'paged' => isset($_GET['paged']) ? intval($_GET['paged']) : 1,
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
    
    if (isset($_GET['precio_min']) && !empty($_GET['precio_min']) && $_GET['precio_min'] > 0) {
        $args['meta_query'][] = array(
            'key' => '_propiedad_precio',
            'value' => intval($_GET['precio_min']),
            'compare' => '>=',
            'type' => 'NUMERIC'
        );
    }
    
    if (isset($_GET['precio_max']) && !empty($_GET['precio_max']) && $_GET['precio_max'] < 1000000000) {
        $args['meta_query'][] = array(
            'key' => '_propiedad_precio',
            'value' => intval($_GET['precio_max']),
            'compare' => '<=',
            'type' => 'NUMERIC'
        );
    }
    
    return new WP_Query($args);
}

// ===== CONFIGURACIÓN DE COLORES PARA TARJETAS DE PROPIEDADES =====

// Agregar sección de colores de tarjetas al Customizer
function mjpropiedades_property_cards_customizer($wp_customize) {
    
    // Sección para colores de tarjetas de propiedades
    $wp_customize->add_section('mjpropiedades_property_cards_colors', array(
        'title' => __('Colores de Tarjetas de Propiedades', 'mjpropiedades'),
        'description' => __('Configura los colores para las tarjetas de propiedades', 'mjpropiedades'),
        'priority' => 30,
        'capability' => 'edit_theme_options',
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
    ";
    
    return $css;
}

// Agregar CSS dinámico al head
function mjpropiedades_property_cards_css() {
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
    if (is_page_template('page-propiedades.php')) {
        $should_load = true;
    }
    
    // Verificar si estamos usando el template de inicio (page-inicio.php)
    if (is_page_template('page-inicio.php')) {
        $should_load = true;
    }
    
    // Verificar si estamos en la página de inicio por slug
    if (is_page('inicio')) {
        $should_load = true;
    }
    
    if ($should_load) {
        echo '<style type="text/css" id="mjpropiedades-property-cards-colors">' . mjpropiedades_property_cards_dynamic_css() . '</style>';
    }
}
add_action('wp_head', 'mjpropiedades_property_cards_css');

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
                $('.property-btn').css('background', newval);
            });
        });
        
        wp.customize('mjpropiedades_card_button_text', function(value) {
            value.bind(function(newval) {
                $('.property-btn').css('color', newval);
            });
        });
        
        wp.customize('mjpropiedades_card_button_hover', function(value) {
            value.bind(function(newval) {
                $('.property-btn:hover').css('background', newval);
            });
        });
        
        wp.customize('mjpropiedades_card_button_text_hover', function(value) {
            value.bind(function(newval) {
                $('.property-btn:hover').css('color', newval);
            });
        });
        
        wp.customize('mjpropiedades_card_details_color', function(value) {
            value.bind(function(newval) {
                $('.detail-item').css('color', newval);
            });
        });
        
        wp.customize('mjpropiedades_card_icons_color', function(value) {
            value.bind(function(newval) {
                $('.detail-item svg').css('color', newval);
            });
        });
        
        wp.customize('mjpropiedades_card_tag_bg', function(value) {
            value.bind(function(newval) {
                $('.property-tag').css('background', newval);
            });
        });
        
        wp.customize('mjpropiedades_card_tag_text', function(value) {
            value.bind(function(newval) {
                $('.property-tag').css('color', newval);
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
add_action('customize_controls_print_scripts', 'mjpropiedades_property_cards_customizer_js');

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
?>
