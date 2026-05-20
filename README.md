# 🏠 Propiedades - WordPress Theme

## 📋 Descripción del Proyecto

**Propiedades** es un tema WordPress profesional desarrollado específicamente para corredoras de propiedades inmobiliarias. El tema está optimizado para la **Cuarta Región de Chile** y ofrece una experiencia completa de gestión inmobiliaria con diseño moderno y responsivo.

## ✨ Características Principales

### 🎨 **Diseño Profesional**
- **Interfaz moderna** con colores corporativos
- **Diseño responsivo** optimizado para móviles, tablets y desktop
- **Tipografía profesional** con fuentes Inter y Dancing Script
- **Animaciones suaves** y transiciones elegantes

### 📱 **Optimización Móvil**
- **Menú hamburguesa** clásico para navegación móvil
- **Sliders adaptativos** con controles táctiles optimizados
- **Cards responsivas** para propiedades
- **Formularios móvil-friendly**

### 🏘️ **Funcionalidades Inmobiliarias**
- **Gestión de propiedades** con Custom Post Types
- **Búsqueda avanzada** por comuna, tipo y precio
- **Sliders de propiedades** en venta y arriendo
- **Formulario de contacto** integrado
- **Sección de servicios** especializados

### 🎯 **Optimización de Conversión**
- **Estructura persuasiva** de secciones
- **Call-to-Actions** estratégicamente ubicados
- **Formulario de contacto** al final del funnel
- **Servicios destacados** con características específicas

## 🛠️ Tecnologías Utilizadas

### **Frontend**
- **HTML5** semántico y accesible
- **CSS3** con variables personalizadas y Grid/Flexbox
- **JavaScript ES6+** para interactividad
- **Swiper.js** para sliders de propiedades
- **Google Fonts** (Inter + Dancing Script)

### **Backend**
- **WordPress 6.0+** como CMS
- **PHP 7.4+** para funcionalidades del tema
- **MySQL 5.7+** para base de datos
- **WordPress Customizer** para personalización

### **Responsive Design**
- **Mobile First** approach
- **Breakpoints:** 320px, 480px, 768px, 1024px, 1200px
- **Touch-friendly** interfaces
- **Performance optimizado** para móviles

## 📁 Estructura del Proyecto

```
wp-content/themes/mjpropiedades/
├── style.css                 # Estilos principales del tema
├── index.php                 # Template principal
├── page-inicio.php           # Página de inicio personalizada
├── header.php                # Header con menú móvil
├── footer.php                 # Footer con scripts
├── functions.php              # Funciones del tema
├── screenshot.png             # Captura del tema
└── README.md                  # Documentación
```

## 🚀 Instalación

### **Requisitos del Sistema**
- **WordPress:** 6.0 o superior
- **PHP:** 7.4 o superior
- **MySQL:** 5.7 o superior
- **Apache/Nginx** con mod_rewrite habilitado

### **Pasos de Instalación**

1. **Descargar el tema**
   ```bash
   # Clonar o descargar el repositorio
   git clone [URL_DEL_REPOSITORIO]
   ```

2. **Subir al servidor**
   ```bash
   # Copiar a la carpeta de temas de WordPress
   cp -r mjpropiedades/ /ruta/wordpress/wp-content/themes/
   ```

3. **Activar el tema**
   - Ir a **Apariencia > Temas** en WordPress Admin
   - Activar **Propiedades**

4. **Configurar personalización**
   - Ir a **Apariencia > Personalizar**
   - Configurar logo, colores y contenido

## ⚙️ Configuración

### **Personalización del Tema**

#### **1. Logo y Branding**
```php
// En WordPress Customizer
- Logo personalizado
- Colores corporativos
- Información de contacto
```

#### **2. Contenido Principal**
```php
// Secciones configurables
- Hero Section (3 imágenes)
- Servicios destacados
- Información de contacto
- Redes sociales
```

#### **3. Propiedades**
```php
// Custom Post Type: 'propiedad'
- Campos personalizados
- Galería de imágenes
- Ubicación por comuna
- Precio y características
```

### **Menús de Navegación**

#### **Menú Principal**
- Inicio
- Quiénes Somos
- Servicios
- Propiedades
- Venta
- Arriendo
- Contacto

#### **Menú Móvil**
- **Hamburger menu** con animación
- **Overlay** semi-transparente
- **Navegación táctil** optimizada

## 📱 Características Móviles

### **Header Responsive**
```css
/* Breakpoints móviles */
@media (max-width: 768px) {
    - Header fijo con hamburger menu
    - Logo adaptativo
    - Navegación oculta
}

@media (max-width: 480px) {
    - Header ultra-compacto
    - Elementos táctiles optimizados
    - Sin desbordamiento horizontal
}
```

### **Sliders Móviles**
```javascript
// Configuración Swiper para móvil
breakpoints: {
    320: { slidesPerView: 1.2, centeredSlides: true },
    480: { slidesPerView: 1.5, spaceBetween: 20 },
    768: { slidesPerView: 2, spaceBetween: 25 },
    1024: { slidesPerView: 3, spaceBetween: 30 }
}
```

## 🎯 Optimización de Conversión

### **Estructura de Página**
1. **Hero Section** - Impacto visual inicial
2. **Búsqueda** - Captura de leads
3. **Propiedades en Venta** - Productos principales
4. **CTA Mid-page** - Conversión intermedia
5. **Quiénes Somos** - Construcción de confianza
6. **Propiedades en Arriendo** - Productos secundarios
7. **Servicios** - Valor agregado
8. **Formulario de Contacto** - Conversión final

### **Elementos de Conversión**
- **Títulos persuasivos** en cada sección
- **Call-to-Actions** estratégicos
- **Formularios optimizados** para captura de leads
- **Testimonios** y elementos de confianza

## 🏘️ Especialización Regional

### **Cuarta Región de Chile**
- **Comunas incluidas:** La Serena, Coquimbo, Ovalle, Vicuña
- **Búsqueda localizada** por comuna
- **Contenido regional** en footer y formularios
- **Optimización SEO** para términos locales

### **Servicios Especializados**
- **Venta** - Estrategias de marketing efectivas
- **Arriendo** - Gestión con inquilinos verificados
- **Tasaciones** - Análisis del mercado actual
- **Garantía Total** - Servicio integral
- **Gestión de Documentos** - Trámites legales
- **Atención Personalizada** - Horario 09:00-18:00 hrs

## 🔧 Personalización Avanzada

### **Variables CSS Personalizables**
```css
:root {
    --primary-blue: #3F4293;
    --secondary-orange: #FF6B35;
    --accent-green: #25D366;
    --text-dark: #333333;
    --text-light: #666666;
    --white: #FFFFFF;
    --light-gray: #F8F9FA;
}
```

### **Funciones PHP Personalizadas**
```php
// Funciones principales del tema
mjpropiedades_get_properties()     // Obtener propiedades
mjpropiedades_display_properties() // Mostrar propiedades
mjpropiedades_fallback_menu()      // Menú de respaldo
```

## 📊 Performance y SEO

### **Optimizaciones Implementadas**
- **CSS minificado** y optimizado
- **JavaScript lazy loading** para sliders
- **Imágenes responsivas** con srcset
- **Meta tags** optimizados para SEO
- **Schema markup** para propiedades

### **Métricas de Performance**
- **Lighthouse Score:** 90+ en móvil
- **Core Web Vitals** optimizados
- **Tiempo de carga:** < 3 segundos
- **Mobile-friendly** certificado

## 🐛 Solución de Problemas

### **Problemas Comunes**

#### **1. Menú móvil no funciona**
```javascript
// Verificar que jQuery esté cargado
// Revisar console para errores JavaScript
```

#### **2. Sliders no se muestran**
```php
// Verificar que Swiper.js esté incluido
// Revisar configuración de propiedades
```

#### **3. Estilos no se aplican**
```css
// Limpiar caché del navegador
// Verificar que style.css esté cargado
```

### **Debug Mode**
```php
// Habilitar debug en wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## 📈 Próximas Mejoras

### **Funcionalidades Planificadas**
- [ ] **Integración con CRM** inmobiliario
- [ ] **Sistema de citas** online
- [ ] **Chat en vivo** para consultas
- [ ] **Calculadora de crédito** hipotecario
- [ ] **Mapas interactivos** con propiedades
- [ ] **Sistema de favoritos** para usuarios

### **Optimizaciones Técnicas**
- [ ] **PWA** (Progressive Web App)
- [ ] **Lazy loading** avanzado
- [ ] **CDN** para imágenes
- [ ] **Caché** optimizado
- [ ] **Compresión** de assets

## 📞 Soporte y Contacto

### **Desarrollo**
- **Desarrollador:** LR
- **Versión:** 1.0
- **Última actualización:** 2024

### **Documentación**
- **WordPress Codex:** [developer.wordpress.org](https://developer.wordpress.org/)
- **Swiper.js:** [swiperjs.com](https://swiperjs.com/)
- **CSS Grid:** [css-tricks.com](https://css-tricks.com/)

## 📄 Licencia

Este tema está desarrollado específicamente para **Propiedades** y está protegido por derechos de autor. No está disponible para distribución pública.

---

*Tema WordPress profesional optimizado para corredoras inmobiliarias de la Cuarta Región de Chile.*
