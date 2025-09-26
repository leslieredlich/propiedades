<?php
/**
 * María José Propiedades Theme Functions
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
    // Sección de información de contacto
    $wp_customize->add_section('mjpropiedades_contact', array(
        'title'    => __('Información de Contacto', 'mjpropiedades'),
        'priority' => 30,
    ));
    
    // Sección de imágenes del hero
    $wp_customize->add_section('mjpropiedades_hero', array(
        'title'    => __('Imágenes del Hero', 'mjpropiedades'),
        'priority' => 25,
    ));
    
    // Hero Image 1
    $wp_customize->add_setting('mjpropiedades_hero_1', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'mjpropiedades_hero_1', array(
        'label'   => __('Imagen Hero 1', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'mime_type' => 'image',
    )));
    
    // Hero Image 2
    $wp_customize->add_setting('mjpropiedades_hero_2', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'mjpropiedades_hero_2', array(
        'label'   => __('Imagen Hero 2', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'mime_type' => 'image',
    )));
    
    // Hero Image 3
    $wp_customize->add_setting('mjpropiedades_hero_3', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'mjpropiedades_hero_3', array(
        'label'   => __('Imagen Hero 3', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'mime_type' => 'image',
    )));
    
    // Contenido del Hero
    $wp_customize->add_setting('mjpropiedades_hero_tag', array(
        'default' => 'Compra de Propiedades',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_hero_tag', array(
        'label' => __('Tag del Hero', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('mjpropiedades_hero_title', array(
        'default' => 'Encuentra el Hogar de tus Sueños',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_hero_title', array(
        'label' => __('Título del Hero', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('mjpropiedades_hero_description', array(
        'default' => 'Descubre propiedades exclusivas que se ajustan a tu estilo de vida. Asesoría personalizada en todo el proceso de compra.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('mjpropiedades_hero_description', array(
        'label' => __('Descripción del Hero', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'type' => 'textarea',
    ));
    
    $wp_customize->add_setting('mjpropiedades_hero_button', array(
        'default' => 'Buscar Propiedades',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('mjpropiedades_hero_button', array(
        'label' => __('Texto del Botón', 'mjpropiedades'),
        'section' => 'mjpropiedades_hero',
        'type' => 'text',
    ));
    
    // Sección About
    $wp_customize->add_section('mjpropiedades_about', array(
        'title'    => __('Sección Quiénes Somos', 'mjpropiedades'),
        'priority' => 30,
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
        'default' => 'Especialistas N°1 en la Cuarta Región. Con más de 8 años de experiencia, me especializo en inversión en La Serena, arriendos en Coquimbo y propiedades en Ovalle, ayudando a familias a encontrar su hogar ideal.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('mjpropiedades_about_text_1', array(
        'label' => __('Primer Párrafo', 'mjpropiedades'),
        'section' => 'mjpropiedades_about',
        'type' => 'textarea',
    ));
    
    $wp_customize->add_setting('mjpropiedades_about_text_2', array(
        'default' => 'Mi compromiso es brindarte un servicio personalizado, transparente y profesional en cada paso del proceso. Desde la primera consulta hasta la firma del contrato, estaré contigo para hacer realidad tus objetivos inmobiliarios en La Serena, Coquimbo y Ovalle.',
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
        'default' => 'Vendí mi casa en Peñuelas, Coquimbo, en menos de 30 días. María José fue increíble, muy profesional y siempre disponible para resolver mis dudas.',
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
        'default' => 'Encontré el departamento perfecto en La Serena gracias a María José. Su conocimiento de la zona es excepcional y el proceso fue muy transparente.',
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
        'default' => 'Arrendé mi casa en Ovalle con María José. El servicio fue impecable, desde la tasación hasta la entrega de llaves. Totalmente recomendable.',
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
        'default' => 'Excelente asesoría para mi inversión en Coquimbo. María José me ayudó a encontrar la propiedad ideal con el mejor retorno. Muy satisfecho.',
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
        'default' => 'Compré mi primera casa en La Serena con María José. Su paciencia y dedicación hicieron que todo el proceso fuera muy fácil para mí.',
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
        'default' => 'Vendí mi terreno en Ovalle rápidamente gracias a la estrategia de marketing de María José. Su experiencia en la región es invaluable.',
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
    
    // Teléfono
    $wp_customize->add_setting('mjpropiedades_phone', array(
        'default'           => '+56 9 4927 6448',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('mjpropiedades_phone', array(
        'label'   => __('Teléfono', 'mjpropiedades'),
        'section' => 'mjpropiedades_contact',
        'type'    => 'text',
    ));
    
    // Email
    $wp_customize->add_setting('mjpropiedades_email', array(
        'default'           => 'homeisaspa@gmail.com',
        'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control('mjpropiedades_email', array(
        'label'   => __('Email', 'mjpropiedades'),
        'section' => 'mjpropiedades_contact',
        'type'    => 'email',
    ));
    
    // Dirección
    $wp_customize->add_setting('mjpropiedades_address', array(
        'default'           => 'Santiago, Chile',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('mjpropiedades_address', array(
        'label'   => __('Dirección', 'mjpropiedades'),
        'section' => 'mjpropiedades_contact',
        'type'    => 'text',
    ));
    
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
?>
