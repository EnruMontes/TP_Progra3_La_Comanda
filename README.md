# La Comanda — Sistema de gestión de restaurante

API REST para la gestión completa de un restaurante: mesas, pedidos, productos, empleados y encuestas de satisfacción. Desarrollada en PHP con Slim Framework.

## Tecnologías

- **PHP** con **Slim Framework 4**
- **JWT** para autenticación (Bearer token)
- **MySQL** como base de datos
- **Postman** para testing (colección incluida)

## Funcionalidades

- Autenticación con JWT y sistema de roles (admin, mozo, cocinero, bartender, cervecero)
- CRUD completo de usuarios, productos, mesas y pedidos
- Flujo de estados de pedidos según rol del empleado
- Encuestas de satisfacción por mesa
- Exportación e importación de datos en CSV
- Subida de imágenes para productos y usuarios

## Estructura

```
app/
├── controllers/     # Lógica de cada entidad
├── models/          # Acceso a base de datos
├── middlewares/     # Autenticación JWT y verificación de roles
├── interfaces/      # Contratos comunes
├── db/              # Conexión a la base de datos
└── utils/           # Helpers (JWT, imágenes, CSV)
tp_comanda.sql       # Estructura de la base de datos
TP_Comanda.postman_collection.json  # Colección Postman para probar la API
```

## Instalación

```bash
git clone https://github.com/EnruMontes/TP_Progra3_La_Comanda.git
cd TP_Progra3_La_Comanda
composer install
```

Importar `tp_comanda.sql` en MySQL y configurar la conexión en `app/db/`.

## Autor

Enrico Montes — Trabajo Final Programación III
