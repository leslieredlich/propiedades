<?php
/**
 * Template para página de Propiedades
 */

get_header(); ?>

<div class="properties-page">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title">Propiedades</h1>
            <p class="page-description">Encuentra la propiedad perfecta para ti</p>
        </header>
        
        <!-- Filtros mejorados con búsqueda -->
        <div class="property-filters">
            <?php 
            // Mostrar resultados de búsqueda si vienen parámetros del buscador
            $search_params = array();
            if ((isset($_GET['comuna']) && !empty($_GET['comuna'])) || (isset($_GET['ciudad']) && !empty($_GET['ciudad']))) {
                $search_params['comuna'] = isset($_GET['comuna']) ? sanitize_text_field($_GET['comuna']) : sanitize_text_field($_GET['ciudad']);
            }
            if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
                $search_params['busqueda'] = sanitize_text_field($_GET['busqueda']);
            }
            if ((isset($_GET['operacion']) && !empty($_GET['operacion'])) || (isset($_GET['tipo']) && !empty($_GET['tipo']))) {
                $search_params['operacion'] = isset($_GET['operacion']) ? sanitize_text_field($_GET['operacion']) : sanitize_text_field($_GET['tipo']);
            }
            
            if (!empty($search_params)) : ?>
                <div class="search-results-info">
                    <div class="search-summary">
                        <h3>Resultados de búsqueda</h3>
                        <div class="search-terms">
                            <?php if (isset($search_params['busqueda'])) : ?>
                                <span class="search-term">
                                    <strong>Búsqueda:</strong> "<?php echo esc_html($search_params['busqueda']); ?>"
                                </span>
                            <?php endif; ?>
                            <?php if (isset($search_params['comuna'])) : ?>
                                <span class="search-term">
                                    <strong>Comuna:</strong> <?php echo ucfirst(str_replace('-', ' ', esc_html($search_params['comuna']))); ?>
                                </span>
                            <?php endif; ?>
                            <?php if (isset($search_params['operacion'])) : ?>
                                <span class="search-term">
                                    <strong>Operación:</strong> <?php echo ucfirst(esc_html($search_params['operacion'])); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <form class="filter-form" method="get">
                <!-- Mantener parámetros de búsqueda -->
                <?php if (isset($_GET['comuna']) || isset($_GET['ciudad'])) : ?>
                    <input type="hidden" name="comuna" value="<?php echo esc_attr(isset($_GET['comuna']) ? $_GET['comuna'] : $_GET['ciudad']); ?>">
                <?php endif; ?>
                <?php if (isset($_GET['busqueda'])) : ?>
                    <input type="hidden" name="busqueda" value="<?php echo esc_attr($_GET['busqueda']); ?>">
                <?php endif; ?>
                
                <div class="filter-group">
                    <label for="operacion">Operación:</label>
                    <select name="operacion" id="operacion">
                        <option value="">Todas</option>
                        <option value="venta" <?php selected(isset($_GET['operacion']) ? $_GET['operacion'] : (isset($_GET['tipo']) ? $_GET['tipo'] : ''), 'venta'); ?>>Venta</option>
                        <option value="arriendo" <?php selected(isset($_GET['operacion']) ? $_GET['operacion'] : (isset($_GET['tipo']) ? $_GET['tipo'] : ''), 'arriendo'); ?>>Arriendo</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="tipo_propiedad">Tipo de Propiedad:</label>
                    <select name="tipo_propiedad" id="tipo_propiedad">
                        <option value="">Todos</option>
                        <option value="casa" <?php selected(isset($_GET['tipo_propiedad']) ? $_GET['tipo_propiedad'] : '', 'casa'); ?>>Casa</option>
                        <option value="departamento" <?php selected(isset($_GET['tipo_propiedad']) ? $_GET['tipo_propiedad'] : '', 'departamento'); ?>>Departamento</option>
                        <option value="oficina" <?php selected(isset($_GET['tipo_propiedad']) ? $_GET['tipo_propiedad'] : '', 'oficina'); ?>>Oficina</option>
                        <option value="local" <?php selected(isset($_GET['tipo_propiedad']) ? $_GET['tipo_propiedad'] : '', 'local'); ?>>Local Comercial</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="comuna">Comuna/Ciudad:</label>
                    <input type="text" name="comuna" id="comuna" placeholder="Buscar comuna..." value="<?php echo isset($_GET['comuna']) ? esc_attr($_GET['comuna']) : (isset($_GET['ciudad']) ? esc_attr(str_replace('-', ' ', $_GET['ciudad'])) : ''); ?>">
                </div>
                
                <div class="filter-group">
                    <label for="precio_min">Precio mínimo:</label>
                    <input type="number" name="precio_min" id="precio_min" placeholder="0" value="<?php echo isset($_GET['precio_min']) ? esc_attr($_GET['precio_min']) : ''; ?>">
                </div>
                
                <div class="filter-group">
                    <label for="precio_max">Precio máximo:</label>
                    <input type="number" name="precio_max" id="precio_max" placeholder="Sin límite" value="<?php echo isset($_GET['precio_max']) ? esc_attr($_GET['precio_max']) : ''; ?>">
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        Filtrar
                    </button>
                    <a href="<?php echo get_permalink(); ?>" class="btn-clear">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        Limpiar
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Debug Info para Administradores -->
        <?php if (current_user_can('administrator')) : ?>
            <div style="background: #e7f3ff; border: 1px solid #bee5eb; border-radius: 5px; padding: 1rem; margin-bottom: 2rem; font-family: monospace; font-size: 12px;">
                <strong>🔧 Debug Info (solo administradores):</strong><br>
                <strong>Parámetros de búsqueda:</strong><br>
                Comuna: <?php echo isset($_GET['comuna']) ? $_GET['comuna'] : (isset($_GET['ciudad']) ? $_GET['ciudad'] : 'ninguna'); ?><br>
                Búsqueda: <?php echo isset($_GET['busqueda']) ? $_GET['busqueda'] : 'ninguna'; ?><br>
                Operación: <?php echo isset($_GET['operacion']) ? $_GET['operacion'] : (isset($_GET['tipo']) ? $_GET['tipo'] : 'ninguna'); ?><br>
                <br>
                <?php 
                // Mostrar todas las propiedades existentes con sus datos
                $debug_all_query = new WP_Query(array('post_type' => 'propiedad', 'post_status' => 'publish', 'posts_per_page' => -1));
                echo "<strong>Total propiedades en BD: " . $debug_all_query->found_posts . "</strong><br>";
                
                if ($debug_all_query->have_posts()) :
                    echo "Todas las propiedades con sus datos:<br>";
                    while ($debug_all_query->have_posts()) : $debug_all_query->the_post();
                        $debug_precio = get_post_meta(get_the_ID(), '_propiedad_precio', true);
                        $debug_operacion = get_post_meta(get_the_ID(), '_propiedad_operacion', true);
                        $debug_comuna = get_post_meta(get_the_ID(), '_propiedad_comuna', true);
                        $debug_direccion = get_post_meta(get_the_ID(), '_propiedad_direccion', true);
                        $debug_tipo = get_post_meta(get_the_ID(), '_propiedad_tipo', true);
                        $debug_operacion_calculada = (!$debug_operacion) ? (($debug_precio && $debug_precio < 1000000) ? 'arriendo' : 'venta') : $debug_operacion;
                        
                        echo "- <strong>" . get_the_title() . "</strong><br>";
                        echo "&nbsp;&nbsp;Precio: $" . number_format((int)$debug_precio) . "<br>";
                        echo "&nbsp;&nbsp;Operación BD: '" . $debug_operacion . "' | Calculada: '" . $debug_operacion_calculada . "'<br>";
                        echo "&nbsp;&nbsp;Comuna: '" . $debug_comuna . "'<br>";
                        echo "&nbsp;&nbsp;Dirección: '" . $debug_direccion . "'<br>";
                        echo "&nbsp;&nbsp;Tipo: '" . $debug_tipo . "'<br><br>";
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        <?php endif; ?>
        
        <!-- Lista de propiedades -->
        <div class="properties-grid">
            <?php
            // Configurar query con filtros mejorados
            $args = array(
                'post_type' => 'propiedad',
                'posts_per_page' => 12,
                'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
                'meta_query' => array('relation' => 'AND')
            );
            
            // Búsqueda por texto libre (del buscador principal)
            if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
                $search_text = sanitize_text_field($_GET['busqueda']);
                
                // Buscar en título del post y en metadatos relevantes
                $args['meta_query'][] = array(
                    'relation' => 'OR',
                    array(
                        'key' => '_propiedad_descripcion',
                        'value' => $search_text,
                        'compare' => 'LIKE'
                    ),
                    array(
                        'key' => '_propiedad_comuna',
                        'value' => $search_text,
                        'compare' => 'LIKE'
                    ),
                    array(
                        'key' => '_propiedad_tipo',
                        'value' => $search_text,
                        'compare' => 'LIKE'
                    )
                );
                
                // También buscar en el título del post
                $args['s'] = $search_text;
            }
            
            // Filtro por comuna (del buscador principal y filtros locales)
            $comuna_filter = '';
            if (isset($_GET['comuna']) && !empty($_GET['comuna'])) {
                $comuna_filter = sanitize_text_field($_GET['comuna']);
            } elseif (isset($_GET['ciudad']) && !empty($_GET['ciudad'])) {
                // Compatibilidad con el parámetro anterior 'ciudad'
                $comuna_filter = sanitize_text_field($_GET['ciudad']);
                $comuna_filter = str_replace('-', ' ', $comuna_filter);
            }
            
            if (!empty($comuna_filter)) {
                
                // Mapeo de ciudades a comunas/sectores conocidos
                $ciudad_to_comunas = array(
                    'santiago' => array('santiago', 'providencia', 'las condes', 'vitacura', 'ñuñoa', 'la reina', 'macul', 'peñalolen', 'san miguel', 'maipu', 'pudahuel', 'quilicura', 'independencia', 'recoleta', 'huechuraba', 'conchalí', 'renca', 'cerro navia', 'lo prado', 'estación central', 'quinta normal', 'cerrillos', 'pedro aguirre cerda', 'san joaquín', 'san ramón', 'la cisterna', 'el bosque', 'la granja', 'san bernardo', 'calera de tango', 'buin', 'paine', 'melipilla', 'talagante', 'isla de maipo', 'curacaví', 'maría pinto', 'alhué', 'padre hurtado', 'peñaflor'),
                    'valparaiso' => array('valparaíso', 'viña del mar', 'concón', 'quintero', 'puchuncaví'),
                    'vina-del-mar' => array('viña del mar', 'concón', 'reñaca'),
                    'concepcion' => array('concepción', 'talcahuano', 'hualpén', 'chiguayante', 'san pedro de la paz'),
                    'la-serena' => array('la serena', 'coquimbo'),
                    'antofagasta' => array('antofagasta'),
                    'temuco' => array('temuco', 'padre las casas'),
                    'rancagua' => array('rancagua', 'machalí'),
                    'talca' => array('talca'),
                    'arica' => array('arica'),
                    'iquique' => array('iquique', 'alto hospicio'),
                    'chillan' => array('chillán', 'chillán viejo'),
                    'puerto-montt' => array('puerto montt', 'puerto varas'),
                    'osorno' => array('osorno'),
                    'valdivia' => array('valdivia')
                );
                
                // Construir query de búsqueda más flexible
                $comuna_query = array('relation' => 'OR');
                
                // Buscar por nombre de comuna directamente
                $comuna_query[] = array(
                    'key' => '_propiedad_comuna',
                    'value' => $comuna_filter,
                    'compare' => 'LIKE'
                );
                
                // Buscar en dirección también
                $comuna_query[] = array(
                    'key' => '_propiedad_direccion',
                    'value' => $comuna_filter,
                    'compare' => 'LIKE'
                );
                
                // Si tenemos un mapeo para esta ciudad, buscar en todas sus comunas
                $normalized_filter = strtolower($comuna_filter);
                if (isset($ciudad_to_comunas[$normalized_filter])) {
                    foreach ($ciudad_to_comunas[$normalized_filter] as $comuna) {
                        $comuna_query[] = array(
                            'key' => '_propiedad_comuna',
                            'value' => $comuna,
                            'compare' => 'LIKE'
                        );
                        $comuna_query[] = array(
                            'key' => '_propiedad_direccion',
                            'value' => $comuna,
                            'compare' => 'LIKE'
                        );
                    }
                }
                
                $args['meta_query'][] = $comuna_query;
            }
            
            // Filtro por operación (tanto del buscador como de filtros)
            $operacion_filter = '';
            if (isset($_GET['operacion']) && !empty($_GET['operacion'])) {
                $operacion_filter = sanitize_text_field($_GET['operacion']);
            } elseif (isset($_GET['tipo']) && !empty($_GET['tipo'])) {
                // Compatibilidad con el parámetro anterior 'tipo'
                $operacion_filter = sanitize_text_field($_GET['tipo']);
            }
            
            // Solo aplicar filtro si hay una operación específica seleccionada
            // No filtrar si no se ha especificado operación (mostrar todas)
            if (!empty($operacion_filter)) {
                $args['meta_query'][] = array(
                    'key' => '_propiedad_operacion',
                    'value' => $operacion_filter,
                    'compare' => '='
                );
            }
            
            // Filtro por tipo de propiedad (usar el campo correcto)
            if (isset($_GET['tipo_propiedad']) && !empty($_GET['tipo_propiedad'])) {
                $args['meta_query'][] = array(
                    'key' => '_propiedad_tipo',
                    'value' => sanitize_text_field($_GET['tipo_propiedad']),
                    'compare' => '='
                );
            }
            
            // Filtro por comuna ya se maneja arriba - eliminado para evitar duplicación
            
            // Filtro por precio mínimo
            if (isset($_GET['precio_min']) && !empty($_GET['precio_min'])) {
                $args['meta_query'][] = array(
                    'key' => '_propiedad_precio',
                    'value' => intval($_GET['precio_min']),
                    'compare' => '>=',
                    'type' => 'NUMERIC'
                );
            }
            
            // Filtro por precio máximo
            if (isset($_GET['precio_max']) && !empty($_GET['precio_max'])) {
                $args['meta_query'][] = array(
                    'key' => '_propiedad_precio',
                    'value' => intval($_GET['precio_max']),
                    'compare' => '<=',
                    'type' => 'NUMERIC'
                );
            }
            
            $properties_query = new WP_Query($args);
            
            // Si no hay propiedades con el filtro aplicado, intentar búsquedas más amplias
            if (!$properties_query->have_posts()) {
                $fallback_attempted = false;
                
                // Primera alternativa: si hay filtros de ciudad y operación, probar solo con operación
                if (!empty($operacion_filter) && isset($_GET['ciudad']) && !empty($_GET['ciudad'])) {
                    $args_fallback = array(
                        'post_type' => 'propiedad',
                        'posts_per_page' => 12,
                        'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
                        'post_status' => 'publish',
                        'meta_query' => array('relation' => 'AND')
                    );
                    
                    // Solo mantener filtro de operación y otros filtros no geográficos
                    foreach ($args['meta_query'] as $meta_query) {
                        if (is_array($meta_query) && isset($meta_query['key'])) {
                            // Excluir filtros de ciudad pero mantener operación y otros
                            if ($meta_query['key'] === '_propiedad_operacion' || 
                                ($meta_query['key'] !== '_propiedad_comuna' && $meta_query['key'] !== '_propiedad_direccion')) {
                                $args_fallback['meta_query'][] = $meta_query;
                            }
                        }
                    }
                    
                    // Mantener búsqueda de texto
                    if (isset($args['s'])) {
                        $args_fallback['s'] = $args['s'];
                    }
                    
                    $properties_query = new WP_Query($args_fallback);
                    $fallback_attempted = true;
                }
                
                // Segunda alternativa: si aún no hay resultados y hay operación, buscar propiedades sin operación definida
                if (!$properties_query->have_posts() && !empty($operacion_filter)) {
                    $args_fallback = array(
                        'post_type' => 'propiedad',
                        'posts_per_page' => 12,
                        'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
                        'post_status' => 'publish',
                        'meta_query' => array(
                            'relation' => 'AND'
                        )
                    );
                    
                    // Copiar filtros no relacionados con operación
                    foreach ($args['meta_query'] as $meta_query) {
                        if (is_array($meta_query) && isset($meta_query['key']) && $meta_query['key'] !== '_propiedad_operacion') {
                            $args_fallback['meta_query'][] = $meta_query;
                        }
                    }
                    
                    // Mantener búsqueda de texto
                    if (isset($args['s'])) {
                        $args_fallback['s'] = $args['s'];
                    }
                    
                    $properties_query = new WP_Query($args_fallback);
                }
            }
            
            // Si aún no hay propiedades y no hay filtros aplicados, mostrar todas las propiedades
            if (!$properties_query->have_posts() && empty(array_filter($_GET))) {
                $args_all = array(
                    'post_type' => 'propiedad',
                    'posts_per_page' => 12,
                    'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
                    'post_status' => 'publish'
                );
                $properties_query = new WP_Query($args_all);
            }
            
            if ($properties_query->have_posts()) :
                while ($properties_query->have_posts()) : $properties_query->the_post();
                    $precio = get_post_meta(get_the_ID(), '_propiedad_precio', true);
                    $operacion = get_post_meta(get_the_ID(), '_propiedad_operacion', true);
                    $dormitorios = get_post_meta(get_the_ID(), '_propiedad_dormitorios', true);
                    $banos = get_post_meta(get_the_ID(), '_propiedad_banos', true);
                    $metros = get_post_meta(get_the_ID(), '_propiedad_metros', true);
                    $comuna = get_post_meta(get_the_ID(), '_propiedad_comuna', true);
                    $tipo = get_post_meta(get_the_ID(), '_propiedad_tipo', true);
                    
                    // Si no hay operación configurada, determinar por precio (igual que en la función principal)
                    if (!$operacion) {
                        $operacion = ($precio && $precio < 1000000) ? 'arriendo' : 'venta';
                    }
                    
                    // Filtro post-procesamiento: si se filtró por operación, verificar que coincida
                    if (!empty($operacion_filter) && $operacion !== $operacion_filter) {
                        continue; // Saltar esta propiedad si no coincide con el filtro
                    }
                    
                    $tag_class = ($operacion === 'arriendo') ? 'style="background: var(--orange);"' : '';
                    $tag_text = ucfirst($operacion);
                    $precio_text = $precio ? '$' . number_format($precio, 0, ',', '.') : 'Consultar';
                    if ($operacion === 'arriendo') {
                        $precio_text .= '/mes';
                    }
                    ?>
                    
                    <div class="property-card">
                        <div class="property-image">
                            <div class="property-tag <?php echo $operacion; ?>" <?php echo $tag_class; ?>>
                                <?php echo $tag_text; ?>
                            </div>
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
                    
                <?php endwhile; ?>
                
                <!-- Paginación Profesional -->
                <?php if ($properties_query->max_num_pages > 1) : 
                    $current_page = max(1, get_query_var('paged'));
                    $total_pages = $properties_query->max_num_pages;
                    $total_properties = $properties_query->found_posts;
                    $properties_per_page = 12;
                    $start_property = (($current_page - 1) * $properties_per_page) + 1;
                    $end_property = min($current_page * $properties_per_page, $total_properties);
                ?>
                
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        <span class="results-info">
                            Mostrando <strong><?php echo $start_property; ?>-<?php echo $end_property; ?></strong> 
                            de <strong><?php echo $total_properties; ?></strong> propiedades
                        </span>
                    </div>
                    
                    <div class="pagination-nav">
                        <?php 
                        $pagination_args = array(
                            'total' => $total_pages,
                            'current' => $current_page,
                            'format' => '?paged=%#%',
                            'show_all' => false,
                            'end_size' => 2,
                            'mid_size' => 2,
                            'prev_next' => true,
                            'prev_text' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="2"/></svg><span>Anterior</span>',
                            'next_text' => '<span>Siguiente</span><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2"/></svg>',
                            'before_page_number' => '',
                            'after_page_number' => '',
                            'type' => 'array'
                        );
                        
                        // Construir URL base manteniendo parámetros de búsqueda
                        $base_url = get_permalink();
                        $url_params = array();
                        
                        foreach ($_GET as $key => $value) {
                            if ($key !== 'paged' && !empty($value)) {
                                $url_params[] = urlencode($key) . '=' . urlencode($value);
                            }
                        }
                        
                        if (!empty($url_params)) {
                            $pagination_args['base'] = $base_url . '%_%' . '&' . implode('&', $url_params);
                            $pagination_args['format'] = '?paged=%#%';
                        }
                        
                        $pagination_links = paginate_links($pagination_args);
                        
                        if ($pagination_links) :
                        ?>
                            <nav class="pagination-nav-container" role="navigation" aria-label="Navegación de páginas">
                                <ul class="pagination-list">
                                    <?php foreach ($pagination_links as $link) : 
                                        $link = str_replace('page-numbers', 'page-number', $link);
                                        $link = str_replace('current', 'page-number current', $link);
                                        $link = str_replace('prev', 'page-number prev', $link);
                                        $link = str_replace('next', 'page-number next', $link);
                                        $link = str_replace('dots', 'page-number dots', $link);
                                    ?>
                                        <li class="pagination-item"><?php echo $link; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                        
                        <!-- Ir a página específica -->
                        <?php if ($total_pages > 5) : ?>
                            <div class="goto-page">
                                <form method="get" class="goto-form">
                                    <?php foreach ($_GET as $key => $value) : 
                                        if ($key !== 'paged' && !empty($value)) : ?>
                                            <input type="hidden" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($value); ?>">
                                        <?php endif; 
                                    endforeach; ?>
                                    <label for="goto-page-input">Ir a página:</label>
                                    <input type="number" id="goto-page-input" name="paged" min="1" max="<?php echo $total_pages; ?>" value="<?php echo $current_page; ?>" class="goto-input">
                                    <button type="submit" class="goto-btn">Ir</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php endif; ?>
                
            <?php else : ?>
                <div class="no-properties">
                    <h3>No se encontraron propiedades</h3>
                    <p>Intenta ajustar los filtros de búsqueda o <a href="<?php echo get_permalink(); ?>">ver todas las propiedades</a>.</p>
                    
                    <?php if (current_user_can('administrator')) : ?>
                        <div style="margin-top: 2rem; padding: 1rem; background: #f0f0f0; border-radius: 5px; text-align: left; font-family: monospace; font-size: 12px;">
                            <strong>Debug Info (solo administradores):</strong><br>
                            Operación filtrada: <?php echo !empty($operacion_filter) ? $operacion_filter : 'ninguna'; ?><br>
                            Total de propiedades publicadas: <?php 
                                $debug_query = new WP_Query(array('post_type' => 'propiedad', 'post_status' => 'publish', 'posts_per_page' => -1));
                                echo $debug_query->found_posts;
                            ?><br>
                            Parámetros GET: <?php echo json_encode($_GET); ?><br>
                            Meta query aplicada: <?php echo json_encode($args['meta_query']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php wp_reset_postdata(); ?>
        </div>
    </div>
</div>

<style>
.properties-page {
    padding: 120px 0 80px;
    background: var(--light-bg);
}

.page-header {
    text-align: center;
    margin-bottom: 3rem;
}

.page-title {
    font-size: 3rem;
    font-weight: 700;
    color: var(--dark-gray);
    margin-bottom: 1rem;
}

.page-description {
    font-size: 1.2rem;
    color: var(--light-gray);
}

/* Search Results Info */
.search-results-info {
    background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
    color: white;
    padding: 1.5rem 2rem;
    border-radius: 15px;
    margin-bottom: 1.5rem;
    box-shadow: 0 8px 25px rgba(63, 66, 147, 0.2);
}

.search-summary h3 {
    margin: 0 0 1rem 0;
    font-size: 1.3rem;
    font-weight: 700;
}

.search-terms {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

.search-term {
    background: rgba(255, 255, 255, 0.2);
    padding: 6px 12px;
    border-radius: 12px;
    font-size: 0.9rem;
    backdrop-filter: blur(10px);
}

.search-term strong {
    font-weight: 600;
}

/* Property Filters */
.property-filters {
    background: white;
    padding: 2rem;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(63, 66, 147, 0.1);
    margin-bottom: 3rem;
    border: 1px solid rgba(63, 66, 147, 0.08);
    position: relative;
    overflow: hidden;
}

.property-filters::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-blue), var(--orange));
}

.filter-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.25rem;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-group label {
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--dark-gray);
    font-size: 0.95rem;
}

.filter-group input,
.filter-group select {
    padding: 12px 14px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
}

.filter-group input:focus,
.filter-group select:focus {
    outline: none;
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 3px rgba(63, 66, 147, 0.1);
}

.filter-actions {
    display: flex;
    gap: 1rem;
    grid-column: 1 / -1;
    justify-content: center;
    margin-top: 1rem;
}

.btn-filter,
.btn-clear {
    padding: 12px 24px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1rem;
}

.btn-filter {
    background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
    color: white;
    box-shadow: 0 6px 15px rgba(63, 66, 147, 0.3);
}

.btn-clear {
    background: transparent;
    color: var(--light-gray);
    border: 2px solid #e9ecef;
}

.btn-filter:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(63, 66, 147, 0.4);
}

.btn-clear:hover {
    border-color: var(--light-gray);
    color: var(--dark-gray);
    background: rgba(108, 117, 125, 0.05);
}

.properties-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.property-location {
    color: var(--light-gray);
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.property-details {
    display: flex;
    gap: 1rem;
    color: var(--light-gray);
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.property-details span {
    background: #f8f9fa;
    padding: 4px 8px;
    border-radius: 12px;
}

.property-tag.venta {
    background: var(--primary-blue);
}

.property-tag.arriendo {
    background: var(--orange);
}

/* Professional Pagination */
.pagination-wrapper {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(63, 66, 147, 0.1);
    margin-top: 3rem;
    border: 1px solid rgba(63, 66, 147, 0.08);
}

.pagination-info {
    text-align: center;
    margin-bottom: 2rem;
}

.results-info {
    color: var(--light-gray);
    font-size: 1rem;
}

.results-info strong {
    color: var(--primary-blue);
    font-weight: 700;
}

.pagination-nav {
    text-align: center;
}

.pagination-nav-container {
    margin-bottom: 1.5rem;
}

.pagination-list {
    display: inline-flex;
    list-style: none;
    gap: 8px;
    padding: 0;
    margin: 0;
    flex-wrap: wrap;
    justify-content: center;
}

.pagination-item {
    display: flex;
}

.page-number {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 16px;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    text-decoration: none;
    color: var(--dark-gray);
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: white;
    min-width: 44px;
    justify-content: center;
}

.page-number:hover {
    background: var(--primary-blue);
    color: white;
    border-color: var(--primary-blue);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(63, 66, 147, 0.3);
}

.page-number.current {
    background: var(--primary-blue);
    color: white;
    border-color: var(--primary-blue);
    box-shadow: 0 4px 15px rgba(63, 66, 147, 0.3);
}

.page-number.dots {
    border: none;
    background: none;
    color: var(--light-gray);
    cursor: default;
}

.page-number.dots:hover {
    background: none;
    color: var(--light-gray);
    transform: none;
    box-shadow: none;
}

.page-number.prev,
.page-number.next {
    padding: 10px 20px;
}

.page-number.prev svg,
.page-number.next svg {
    flex-shrink: 0;
}

/* Goto Page */
.goto-page {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e9ecef;
}

.goto-form {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.goto-form label {
    font-weight: 600;
    color: var(--dark-gray);
    font-size: 0.9rem;
}

.goto-input {
    padding: 8px 12px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 0.9rem;
    width: 80px;
    text-align: center;
}

.goto-input:focus {
    outline: none;
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 3px rgba(63, 66, 147, 0.1);
}

.goto-btn {
    padding: 8px 16px;
    background: var(--primary-blue);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.goto-btn:hover {
    background: var(--secondary-blue);
    transform: translateY(-1px);
}

.no-properties {
    text-align: center;
    padding: 3rem;
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    grid-column: 1 / -1;
}

.no-properties h3 {
    color: var(--dark-gray);
    margin-bottom: 1rem;
}

.no-properties a {
    color: var(--primary-blue);
    text-decoration: none;
}

.no-properties a:hover {
    text-decoration: underline;
}

@media (max-width: 1024px) {
    .filter-form {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }
    
    .pagination-list {
        gap: 6px;
    }
    
    .page-number {
        padding: 8px 12px;
        font-size: 0.9rem;
    }
}

@media (max-width: 768px) {
    .filter-form {
        grid-template-columns: 1fr;
    }
    
    .filter-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .btn-filter,
    .btn-clear {
        width: 100%;
        max-width: 250px;
        justify-content: center;
    }
    
    .properties-grid {
        grid-template-columns: 1fr;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .search-terms {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .pagination-wrapper {
        padding: 1.5rem;
    }
    
    .pagination-list {
        gap: 4px;
    }
    
    .page-number {
        padding: 8px 10px;
        min-width: 40px;
        font-size: 0.85rem;
    }
    
    .page-number.prev span,
    .page-number.next span {
        display: none;
    }
    
    .goto-page {
        flex-direction: column;
        gap: 1rem;
    }
    
    .goto-form {
        flex-direction: column;
        text-align: center;
    }
}

@media (max-width: 480px) {
    .pagination-list {
        flex-wrap: wrap;
        gap: 3px;
    }
    
    .page-number {
        padding: 6px 8px;
        min-width: 36px;
        font-size: 0.8rem;
    }
}
</style>

<?php get_footer(); ?>
