# 🛒 Tiendita de Don Pepe - Portal Administrativo y Servicios REST

Este repositorio contiene el backend (portal administrativo web y servicios REST en PHP/MySQL) desarrollado para el proyecto **Tiendita de Don Pepe**, una solución de gestión de ventas e inventario integrada con una aplicación móvil Android.

## 📋 Descripción
- **Curso:** Desarrollo de Aplicaciones Móviles I (5390)
- **Coordinador:** Jeanpierre Ricardo Delgadillo Valderrama
- **Avance:** 50% (Semana 5)

---

## 🛠️ Tecnologías Utilizadas
- **Lenguaje:** PHP 8
- **Base de Datos:** MySQL (PDO con consultas preparadas)
- **Formato de datos:** JSON (Servicios REST)
- **Servidor Local / Hosting:** XAMPP / Hosting Gratuito

---

## 🗄️ Estructura de la Base de Datos
El proyecto cuenta con 6 tablas principales:
1. `usuario` (Administradores y Vendedores)
2. `categoria` (Categorías de productos)
3. `producto` (Catálogo, precios y stock)
4. `cliente` (Clientes de la bodega)
5. `venta` (Cabecera de ventas)
6. `detalle_venta` (Detalle de productos vendidos)

---

## 🚀 Servicios REST Disponibles (`/ws/`)
- `POST /ws/login.php` - Validación de usuarios.
- `GET /ws/categorias.php` - Listado de categorías.
- `GET /ws/productos.php` - Listado de productos activos.
- `GET /ws/clientes.php` - Listado de clientes.
- `POST /ws/venta_registrar.php` - Registro de venta desde la app móvil.
