<?php
/**
 * Template para mostrar el archivo de propiedades
 */

get_header(); ?>

<div class="properties-archive">
    <div class="container">
        <header class="archive-header">
            <h1 class="archive-title">Propiedades</h1>
            <p class="archive-description">Encuentra la propiedad perfecta para ti</p>
        </header>
        
        <!-- Filtros -->
        <div class="property-filters">
            <form class="filter-form" method="get">
                <div class="filter-group">
                    <label for="operacion">Operación:</label>
                    <select name="operacion" id="operacion">
                        <option value="">Todas</option>
                        <option value="venta" <?php selected(isset($_GET['operacion']) ? $_GET['operacion'] : '', 'venta'); ?>>Venta</option>
                        <option value="arriendo" <?php selected(isset($_GET['operacion']) ? $_GET['operacion'] : '', 'arriendo'); ?>>Arriendo</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="tipo">Tipo:</label>
                    <select name="tipo" id="tipo">
                        <option value="">Todos</option>
                        <option value="casa" <?php selected(isset($_GET['tipo']) ? $_GET['tipo'] : (isset($_GET['tipo_propiedad']) ? $_GET['tipo_propiedad'] : ''), 'casa'); ?>>Casa</option>
                        <option value="departamento" <?php selected(isset($_GET['tipo']) ? $_GET['tipo'] : (isset($_GET['tipo_propiedad']) ? $_GET['tipo_propiedad'] : ''), 'departamento'); ?>>Departamento</option>
                        <option value="oficina" <?php selected(isset($_GET['tipo']) ? $_GET['tipo'] : (isset($_GET['tipo_propiedad']) ? $_GET['tipo_propiedad'] : ''), 'oficina'); ?>>Oficina</option>
                        <option value="local" <?php selected(isset($_GET['tipo']) ? $_GET['tipo'] : (isset($_GET['tipo_propiedad']) ? $_GET['tipo_propiedad'] : ''), 'local'); ?>>Local Comercial</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="comuna">Comuna:</label>
                    <?php 
                    // Obtener el valor de comuna desde ubicación si viene del formulario de inicio
                    $comuna_value = '';
                    if (isset($_GET['comuna']) && !empty($_GET['comuna'])) {
                        $comuna_value = $_GET['comuna'];
                    } elseif (isset($_GET['ubicacion']) && !empty($_GET['ubicacion'])) {
                        // Convertir ubicación a comuna
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
                        $comuna_value = isset($comunas[$_GET['ubicacion']]) ? $comunas[$_GET['ubicacion']] : $_GET['ubicacion'];
                    }
                    ?>
                    <input type="text" name="comuna" id="comuna" placeholder="Buscar comuna..." value="<?php echo esc_attr($comuna_value); ?>">
                </div>
                
                <div class="filter-group">
                    <label for="precio_min">Precio mínimo:</label>
                    <input type="number" name="precio_min" id="precio_min" placeholder="0" value="<?php echo isset($_GET['precio_min']) ? esc_attr($_GET['precio_min']) : ''; ?>">
                </div>
                
                <div class="filter-group">
                    <label for="precio_max">Precio máximo:</label>
                    <input type="number" name="precio_max" id="precio_max" placeholder="Sin límite" value="<?php echo isset($_GET['precio_max']) ? esc_attr($_GET['precio_max']) : ''; ?>">
                </div>
                
                <div class="filter-group">
                    <label for="dormitorios">Dormitorios:</label>
                    <select name="dormitorios" id="dormitorios">
                        <option value="">Cualquier cantidad</option>
                        <option value="1" <?php selected(isset($_GET['dormitorios']) ? $_GET['dormitorios'] : '', '1'); ?>>1 dormitorio</option>
                        <option value="2" <?php selected(isset($_GET['dormitorios']) ? $_GET['dormitorios'] : '', '2'); ?>>2 dormitorios</option>
                        <option value="3" <?php selected(isset($_GET['dormitorios']) ? $_GET['dormitorios'] : '', '3'); ?>>3 dormitorios</option>
                        <option value="4" <?php selected(isset($_GET['dormitorios']) ? $_GET['dormitorios'] : '', '4'); ?>>4 dormitorios</option>
                        <option value="5+" <?php selected(isset($_GET['dormitorios']) ? $_GET['dormitorios'] : '', '5+'); ?>>5+ dormitorios</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="banos">Baños:</label>
                    <select name="banos" id="banos">
                        <option value="">Cualquier cantidad</option>
                        <option value="1" <?php selected(isset($_GET['banos']) ? $_GET['banos'] : '', '1'); ?>>1 baño</option>
                        <option value="2" <?php selected(isset($_GET['banos']) ? $_GET['banos'] : '', '2'); ?>>2 baños</option>
                        <option value="3" <?php selected(isset($_GET['banos']) ? $_GET['banos'] : '', '3'); ?>>3 baños</option>
                        <option value="4" <?php selected(isset($_GET['banos']) ? $_GET['banos'] : '', '4'); ?>>4 baños</option>
                        <option value="5+" <?php selected(isset($_GET['banos']) ? $_GET['banos'] : '', '5+'); ?>>5+ baños</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-filter">Filtrar</button>
                <a href="<?php echo get_post_type_archive_link('propiedad'); ?>" class="btn-clear">Limpiar</a>
            </form>
        </div>
        
        <!-- Lista de propiedades -->
        <div class="properties-grid">
            <?php
            // Configurar query con filtros
            $args = array(
                'post_type' => 'propiedad',
                'posts_per_page' => 12,
                'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
                'meta_query' => array()
            );
            
            // Filtro por operación
            if (isset($_GET['operacion']) && !empty($_GET['operacion'])) {
                $args['meta_query'][] = array(
                    'key' => '_propiedad_operacion',
                    'value' => sanitize_text_field($_GET['operacion']),
                    'compare' => '='
                );
            }
            
            // Filtro por tipo de propiedad (compatible con formulario de inicio)
            if (isset($_GET['tipo']) && !empty($_GET['tipo'])) {
                $args['meta_query'][] = array(
                    'key' => '_propiedad_tipo',
                    'value' => sanitize_text_field($_GET['tipo']),
                    'compare' => '='
                );
            }
            
            // Filtro por tipo de propiedad desde formulario de inicio
            if (isset($_GET['tipo_propiedad']) && !empty($_GET['tipo_propiedad'])) {
                $args['meta_query'][] = array(
                    'key' => '_propiedad_tipo',
                    'value' => sanitize_text_field($_GET['tipo_propiedad']),
                    'compare' => '='
                );
            }
            
            // Filtro por comuna (compatible con formulario de inicio)
            if (isset($_GET['comuna']) && !empty($_GET['comuna'])) {
                $args['meta_query'][] = array(
                    'key' => '_propiedad_comuna',
                    'value' => sanitize_text_field($_GET['comuna']),
                    'compare' => 'LIKE'
                );
            }
            
            // Filtro por ubicación desde formulario de inicio
            if (isset($_GET['ubicacion']) && !empty($_GET['ubicacion'])) {
                // Convertir el valor de ubicación a formato de comuna
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
                $comuna_label = isset($comunas[$_GET['ubicacion']]) ? $comunas[$_GET['ubicacion']] : $_GET['ubicacion'];
                
                $args['meta_query'][] = array(
                    'key' => '_propiedad_comuna',
                    'value' => sanitize_text_field($comuna_label),
                    'compare' => 'LIKE'
                );
            }
            
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
            
            // Filtro por dormitorios (desde formulario de inicio)
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
            
            // Filtro por baños (desde formulario de inicio)
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
            
            $properties_query = new WP_Query($args);
            
            if ($properties_query->have_posts()) :
                while ($properties_query->have_posts()) : $properties_query->the_post();
                    $precio = get_post_meta(get_the_ID(), '_propiedad_precio', true);
                    $operacion = get_post_meta(get_the_ID(), '_propiedad_operacion', true);
                    $dormitorios = get_post_meta(get_the_ID(), '_propiedad_dormitorios', true);
                    $banos = get_post_meta(get_the_ID(), '_propiedad_banos', true);
                    $metros = get_post_meta(get_the_ID(), '_propiedad_metros', true);
                    $comuna = get_post_meta(get_the_ID(), '_propiedad_comuna', true);
                    ?>
                    
                    <div class="property-card">
                        <div class="property-image">
                            <div class="property-tag <?php echo $operacion; ?>">
                                <?php echo ucfirst($operacion); ?>
                            </div>
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                            <?php else : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 250'><rect fill='%23f0f0f0' width='400' height='250'/><text x='50%' y='50%' text-anchor='middle' dy='.3em' fill='%23999'><?php echo get_the_title(); ?></text></svg>" alt="<?php the_title(); ?>">
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
                                    $<?php echo number_format($precio, 0, ',', '.'); ?>
                                    <?php if ($operacion === 'arriendo') : ?>
                                        /mes
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <a href="<?php the_permalink(); ?>" class="property-btn">
                                Ver Detalles
                            </a>
                        </div>
                    </div>
                    
                <?php endwhile; ?>
                
                <!-- Paginación -->
                <div class="pagination">
                    <?php
                    echo paginate_links(array(
                        'total' => $properties_query->max_num_pages,
                        'current' => max(1, get_query_var('paged')),
                        'format' => '?paged=%#%',
                        'show_all' => false,
                        'type' => 'list',
                        'end_size' => 2,
                        'mid_size' => 1,
                        'prev_text' => '« Anterior',
                        'next_text' => 'Siguiente »',
                        'add_args' => false,
                        'add_fragment' => '',
                    ));
                    ?>
                </div>
                
            <?php else : ?>
                <div class="no-properties">
                    <h3>No se encontraron propiedades</h3>
                    <p>Intenta ajustar los filtros de búsqueda o <a href="<?php echo get_post_type_archive_link('propiedad'); ?>">ver todas las propiedades</a>.</p>
                </div>
            <?php endif; ?>
            
            <?php wp_reset_postdata(); ?>
        </div>
    </div>
</div>

<style>
.properties-archive {
    padding: 120px 0 80px;
    background: #f8f9fa;
}

.archive-header {
    text-align: center;
    margin-bottom: 3rem;
}

.archive-title {
    font-size: 3rem;
    font-weight: 700;
    color: var(--dark-gray);
    margin-bottom: 1rem;
}

.archive-description {
    font-size: 1.2rem;
    color: var(--light-gray);
}

.property-filters {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    margin-bottom: 3rem;
}

.filter-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
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
}

.filter-group input,
.filter-group select {
    padding: 10px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 1rem;
}

.filter-group input:focus,
.filter-group select:focus {
    outline: none;
    border-color: var(--primary-blue);
}

.btn-filter,
.btn-clear {
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-filter {
    background: var(--primary-blue);
    color: white;
}

.btn-clear {
    background: #6c757d;
    color: white;
}

.btn-filter:hover,
.btn-clear:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
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
    padding: 4px 8px;
    border-radius: 12px;
}

.property-tag.venta {
    background: var(--primary-blue);
}

.property-tag.arriendo {
    background: var(--orange);
}

.pagination {
    text-align: center;
    margin-top: 3rem;
}

.pagination ul {
    display: inline-flex;
    list-style: none;
    gap: 0.5rem;
}

.pagination li a,
.pagination li span {
    padding: 10px 15px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    text-decoration: none;
    color: var(--dark-gray);
    transition: all 0.3s ease;
}

.pagination li a:hover,
.pagination .current {
    background: var(--primary-blue);
    color: white;
    border-color: var(--primary-blue);
}

.no-properties {
    text-align: center;
    padding: 3rem;
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
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

@media (max-width: 768px) {
    .filter-form {
        grid-template-columns: 1fr;
    }
    
    .properties-grid {
        grid-template-columns: 1fr;
    }
    
    .archive-title {
        font-size: 2rem;
    }
}
</style>

<?php get_footer(); ?>
