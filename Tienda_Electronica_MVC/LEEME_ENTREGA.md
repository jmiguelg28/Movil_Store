# Movill Store — entrega mejorada · 03/11/2026

Aplicación **PHP + MySQL, arquitectura MVC**, basada directamente en la versión consolidada que se recibió. Se preservaron la organización de controladores, modelos, vistas y las tablas originales.

## Qué se mejoró

- Portada comercial de tecnología con nuevas secciones, categorías dinámicas, tarjetas de productos y una interfaz adaptable a teléfonos, tabletas y PC.
- Nuevo carrito visual, donde puedes revisar, aumentar, reducir y eliminar productos. La información se conserva localmente en el navegador hasta registrar la venta.
- Barra de navegación, menú, formularios de productos, ventas, compras, clientes, proveedores y catálogos, más consistentes visualmente.
- Panel de administración con accesos rápidos, tema claro/oscuro, búsquedas y tablas más fáciles de consultar en pantallas pequeñas.
- Exportación CSV de productos, ventas y compras para Excel y opción de imprimir un comprobante de cada operación.
- Ajuste del borrador: no se elimina antes de que el servidor confirme que guardó el documento. El catálogo verifica disponibilidad; el stock real lo valida MySQL en una transacción.
- La aplicación **ya no realiza ALTER TABLE automáticamente al visitar productos**. Se incluye una migración opcional e idempotente para columnas antiguas.
- Configuración opcional de MySQL mediante variables de entorno, sin perder los valores típicos de XAMPP.

## Cómo instalar en XAMPP

1. **Haz una copia de seguridad** de la carpeta original y exporta la base real en MySQL Workbench: `Server > Data Export` o desde phpMyAdmin > Exportar. Comprueba que el archivo de respaldo no esté vacío.
2. Cierra la aplicación y copia esta carpeta `Tienda_Electronica_MVC` dentro de `C:\xampp\htdocs\` (puedes renombrar la anterior a `Tienda_Electronica_MVC_respaldo`).
3. Inicia **Apache** y **MySQL** en XAMPP. MySQL Workbench es un cliente para administrar la base, no sustituye al servidor MySQL.
4. **Si ya tienes la base de datos:** NO importes `01_estructura.sql` ni `02_datos_demo.sql`. Esos archivos son para una base nueva/datos de práctica.
5. En caso de que tu tabla `producto` no tenga `imagen` o `descripcion`, ejecuta `database/04_migracion_segura.sql` **después de respaldar**. Si las dos columnas existen, no hay que ejecutar migración.
6. Revisa `config/conexion.php` si tu usuario, contraseña o nombre de base de datos son distintos. Por defecto se usa `localhost`, `tienda_electronica`, usuario `root` y contraseña vacía.
7. Abre `http://localhost/Tienda_Electronica_MVC/` en el navegador: ahora se inicia en la **Tienda** después de entrar; el panel general sigue disponible desde el menú. Presiona `Ctrl+F5` para actualizar los estilos.
8. Ingresa con el usuario que ya tengas. En una instalación nueva (tabla `usuarios` vacía) el proyecto original crea `admin / admin123`; **cambia esa contraseña inmediatamente** en Mi cuenta.

### Instalación nueva sin datos

Crea una base desde `database/01_estructura.sql`, luego importa `database/02_datos_demo.sql` únicamente si necesitas información ficticia para una demostración. `03_imagenes_demo.sql` solo se usa si quieres vincular ilustraciones a esos productos de demostración. Nunca importes demos sobre una base de producción.

## Circuito de venta

**Tienda → agregar al carrito → Mi carrito → Continuar a la venta → elegir cliente, fecha y forma de pago → Guardar → comprobante**. Si todavía no existe un cliente, crea uno en **Clientes** y vuelve a la venta. El servidor recalcula el stock dentro de una transacción y rechaza cantidades que dejarían stock negativo.

> **Importante:** este es un sistema de gestión interna, no una tienda de checkout público. No incluye pasarela de pagos, cobro con tarjetas en línea, facturación electrónica DIAN, envíos, registro de compradores externos ni pago/entrega automático. La vista de carrito solo prepara una venta interna. No se presentan estos servicios como existentes.

## Comprobaciones recomendadas

1. Inicio de sesión y cierre de sesión con contraseña válida/incorrecta.
2. Productos: crear, editar, subir foto, quitar foto, buscar, verificar stock.
3. Tienda: usar filtros, cambiar orden, navegar por páginas, abrir detalles.
4. Carrito: añadir varios artículos, cambiar cantidades, eliminar, recargar y volver a abrir; comprobar que el valor se mantiene.
5. Ventas: registrar una venta con cliente y forma de pago válidos; comprobar que el stock baja, aparece en el histórico, se imprime y se exporta a CSV.
6. Ventas: intentar vender más de lo disponible, confirmar que muestra error y no descuenta inventario.
7. Compras: registrar y editar una compra; comprobar que el stock aumenta y el total coincide.
8. Clientes y proveedores: crear, editar, buscar, impedir eliminar registros asociados.
9. Editar/eliminar una venta o compra y verificar que el stock vuelve a calcularse correctamente.
10. Revisar en móvil y PC el menú, el carrito, tablas largas y tema oscuro.

**Comprobaciones que se realizaron al construir la entrega:** análisis de archivos, validación de sintaxis PHP y JavaScript, renderizado de plantillas con datos simulados. **No fue posible conectar o ejecutar el sistema contra tu base real MySQL desde este entorno**, por lo que no puede certificarse una prueba end-to-end de todos los formularios y consultas.

## Cómo regresar a la versión anterior

Cierra Apache, restaura la carpeta respaldada y, **solo si ejecutaste migraciones y necesitas deshacer cambios**, restaura el respaldo SQL según tus procedimientos habituales. La migración `04_migracion_segura.sql` es aditiva y no borra columnas ni datos; en general no requiere revertir la base.

## Seguridad de publicación

Para pruebas de universidad funciona en XAMPP local. **No publiques el proyecto directamente en un servidor abierto a internet** sin preparar HTTPS, credenciales privadas de base de datos, gestión de usuarios/roles, copias de seguridad automatizadas, configuración del servidor y revisión de seguridad. El acceso público o pagos reales requieren desarrollo adicional.
