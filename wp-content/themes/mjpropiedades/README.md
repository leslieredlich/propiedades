# Home Isa Propiedades - Tema de WordPress

Un tema profesional y moderno para corredores de propiedades, diseñado específicamente para Home Isa Propiedades.

## Características

### Diseño y UX
- ✅ Diseño completamente responsivo (móvil, tablet, desktop)
- ✅ Interfaz moderna y profesional
- ✅ Navegación suave entre secciones
- ✅ Carrusel interactivo en la página principal
- ✅ Formularios de contacto funcionales
- ✅ Botón flotante de WhatsApp

### Funcionalidades
- ✅ Custom Post Type para propiedades
- ✅ Sistema de filtros avanzado
- ✅ Meta boxes para detalles de propiedades
- ✅ Formulario de contacto con validación
- ✅ Integración con WhatsApp
- ✅ SEO optimizado
- ✅ Carga rápida y optimizada

### Secciones Incluidas
1. **Header** - Navegación principal con logo y menú
2. **Hero** - Sección principal con carrusel y call-to-action
3. **Propiedades en Venta** - Grid de propiedades para venta
4. **Propiedades en Arriendo** - Grid de propiedades para arriendo
5. **Sobre Home Isa** - Sección personal con estadísticas
6. **Formulario de Contacto** - Formulario completo de consultas
7. **Call-to-Action** - Sección de conversión
8. **Footer** - Enlaces, información de contacto y redes sociales

## Instalación

1. **Subir el tema:**
   - Comprimir la carpeta del tema en un archivo ZIP
   - Ir a WordPress Admin > Apariencia > Temas
   - Hacer clic en "Añadir nuevo" > "Subir tema"
   - Seleccionar el archivo ZIP y activar

2. **Configurar el tema:**
   - Ir a Personalizar > Información de Contacto
   - Configurar teléfono, email y dirección
   - Ir a Apariencia > Menús para configurar la navegación

3. **Crear propiedades:**
   - Ir a Propiedades > Agregar Nueva
   - Completar los detalles de la propiedad
   - Subir imágenes y publicar

## Personalización

### Colores
Los colores principales se pueden modificar en el archivo `style.css` en las variables CSS:

```css
:root {
    --primary-blue: #3F4293;
    --secondary-blue: #4242a0;
    --orange: #F7B500;
    --dark-gray: #2c2c2c;
    --light-gray: #6c757d;
    --white: #ffffff;
    --light-bg: #f8f9fa;
    --whatsapp-green: #25D366;
}
```

### Información de Contacto
- Ir a Personalizar > Información de Contacto
- Modificar teléfono, email y dirección
- Los cambios se reflejan automáticamente en todo el sitio

### Menús
- Ir a Apariencia > Menús
- Crear menú principal y asignar ubicación
- Agregar enlaces personalizados para secciones internas

## Estructura de Archivos

```
mjpropiedades/
├── style.css              # Estilos principales
├── index.php              # Página principal
├── functions.php          # Funciones del tema
├── header.php             # Header del sitio
├── footer.php             # Footer del sitio
├── single-propiedad.php   # Página individual de propiedad
├── archive-propiedad.php  # Archivo de propiedades
├── js/
│   └── main.js           # JavaScript principal
└── README.md             # Este archivo
```

## Custom Post Type: Propiedades

El tema incluye un Custom Post Type para propiedades con los siguientes campos:

- **Precio** - Precio de la propiedad
- **Tipo** - Casa, Departamento, Oficina, Local Comercial
- **Dormitorios** - Número de dormitorios
- **Baños** - Número de baños
- **Metros cuadrados** - Superficie total
- **Comuna** - Ubicación
- **Operación** - Venta o Arriendo

## Formulario de Contacto

El formulario incluye:
- Validación en tiempo real
- Envío por AJAX
- Campos obligatorios: Nombre, Email, Teléfono
- Campos opcionales: Tipo de consulta, Tipo de propiedad, Comuna, Mensaje

## Integración WhatsApp

- Botón flotante en todas las páginas
- Enlaces directos en propiedades
- Número configurable desde el Customizer

## Optimizaciones

- **Rendimiento:** Carga optimizada de recursos
- **SEO:** Estructura semántica y meta tags
- **Accesibilidad:** Navegación por teclado y screen readers
- **Móvil:** Diseño responsive completo

## Soporte

Para soporte técnico o personalizaciones adicionales, contactar al desarrollador.

## Licencia

Este tema está desarrollado específicamente para Home Isa Propiedades. Todos los derechos reservados.

---

**Desarrollado con ❤️ para Home Isa Propiedades**
