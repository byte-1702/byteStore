# ByteStore Academy - Norte del proyecto

Este proyecto evoluciona hacia una plataforma de venta de videojuegos tipo Steam.

## Secciones base (ya conectadas)
- Inicio
- Entra
- Registrate
- Productos
- Carrito
- Terminos y Condiciones
- Politica de Privacidad
- Manual de Usuario
- Preguntas Frecuentes
- Acerca de Nosotros

## Orden recomendado de desarrollo
1. **Autenticacion**
   - Registro en base de datos
   - Login con sesion
   - Cerrar sesion y roles basicos
2. **Catalogo**
   - Tabla `productos`
   - Listado dinamico con filtros
   - Vista detalle de producto
3. **Carrito**
   - Sesion de carrito por usuario
   - Agregar, quitar y actualizar cantidades
   - Calculo de subtotal/total
4. **Checkout**
   - Confirmacion de compra
   - Tabla de `ordenes` y `orden_detalle`
   - Historial de compras
5. **Calidad**
   - Validaciones del lado servidor
   - Manejo de errores
   - Separacion por capas (`control`, `modelo`, `vista`)

## Estructura sugerida de carpetas
- `nbproject/`: vistas principales y rutas php
- `include/`: header, nav y footer reutilizables
- `CSS/`: estilos por modulo
- `control/`: logica de negocio y controladores
- `assets/`: imagenes y recursos estaticos
