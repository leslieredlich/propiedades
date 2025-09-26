# 🏠 **Control de Títulos de Tarjetas de Propiedades**

## 🎯 **Nueva Opción Agregada**

He agregado una opción específica en el WordPress Customizer para controlar el tamaño de los títulos de las tarjetas de propiedades en la página de inicio.

## 📍 **Ubicación en WordPress**

### **Cómo Acceder:**
1. **WordPress Admin** → **Apariencia** → **Personalizar**
2. Buscar **"Tipografía"** en el menú lateral
3. Buscar **"Tamaño de Títulos de Tarjetas de Propiedades"**

## ⚙️ **Configuración**

### **Campo:**
- **Nombre**: "Tamaño de Títulos de Tarjetas de Propiedades"
- **Valor por defecto**: `1.25rem` (20px)
- **Descripción**: "Específico para las tarjetas en la página de inicio"

### **Ejemplos de Valores:**
```
1rem     = 16px  (Pequeño)
1.25rem  = 20px  (Mediano - Recomendado)
1.5rem   = 24px  (Grande)
1.75rem  = 28px  (Muy grande)
2rem     = 32px  (Extra grande)
```

## 🎨 **Qué Controla**

### **Elementos Afectados:**
- ✅ **Títulos de tarjetas** en "Propiedades en Venta"
- ✅ **Títulos de tarjetas** en "Propiedades en Arriendo"
- ✅ **Títulos de tarjetas** en la página de propiedades
- ✅ **Solo afecta** las tarjetas de la página de inicio

### **Elementos NO Afectados:**
- ❌ Títulos de páginas de detalle de propiedad
- ❌ Títulos de secciones principales
- ❌ Títulos del hero
- ❌ Otros títulos del sitio

## 📱 **Responsive Automático**

### **Tablet (768px):**
- Se reduce automáticamente un **10%**
- Ejemplo: `1.25rem` → `1.125rem`

### **Móvil (480px):**
- Se reduce automáticamente un **20%**
- Ejemplo: `1.25rem` → `1rem`

## 🎯 **Casos de Uso**

### **Para un diseño más elegante:**
```
Tamaño: 1rem (16px)
Resultado: Títulos más discretos y elegantes
```

### **Para mayor impacto visual:**
```
Tamaño: 1.5rem (24px)
Resultado: Títulos más prominentes y llamativos
```

### **Para mejor legibilidad:**
```
Tamaño: 1.25rem (20px)
Resultado: Tamaño equilibrado y legible
```

## 🔧 **Implementación Técnica**

### **Clases CSS Aplicadas:**
```css
.properties-grid .property-title
.properties-grid h3
.property-card-title
.property-card h3
.property-card .card-title
```

### **CSS Dinámico:**
- Se genera automáticamente
- Se aplica con `!important`
- Incluye responsive automático

## ✅ **Ventajas de esta Opción**

### **Control Específico:**
- ✅ **Solo afecta** títulos de tarjetas
- ✅ **No interfiere** con otros títulos
- ✅ **Control independiente** de otros elementos

### **Fácil de Usar:**
- ✅ **Interfaz simple** en WordPress
- ✅ **Cambios en tiempo real**
- ✅ **Valores por defecto** optimizados

### **Profesional:**
- ✅ **Responsive automático**
- ✅ **Consistencia visual**
- ✅ **Fácil mantenimiento**

## 🚀 **Cómo Usar**

### **Paso 1: Acceder al Panel**
1. Ve a **WordPress Admin**
2. **Apariencia** → **Personalizar**
3. Buscar **"Tipografía"**

### **Paso 2: Ajustar el Tamaño**
1. Buscar **"Tamaño de Títulos de Tarjetas de Propiedades"**
2. Cambiar el valor (ej: `1.5rem`)
3. **Ver el cambio** en tiempo real

### **Paso 3: Guardar**
1. Hacer clic en **"Publicar"**
2. **¡Listo!** Los títulos se actualizarán

## 💡 **Consejos Profesionales**

### **Tamaños Recomendados:**
- **Mínimo**: `1rem` (16px) - Para diseño minimalista
- **Recomendado**: `1.25rem` (20px) - Equilibrado y legible
- **Máximo**: `1.75rem` (28px) - Para máximo impacto

### **Consideraciones:**
- **No excedas** `2rem` (32px) - Puede verse desproporcionado
- **Prueba en móvil** después de cada cambio
- **Mantén consistencia** con otros elementos del sitio

---

**🎉 ¡Ahora tienes control total sobre los títulos de las tarjetas de propiedades!**

















