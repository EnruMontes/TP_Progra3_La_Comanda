-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-07-2024 a las 08:36:46
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tp_comanda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuestas`
--

CREATE TABLE `encuestas` (
  `id` int(11) NOT NULL,
  `codigoMesa` varchar(5) NOT NULL,
  `codigoPedido` varchar(5) NOT NULL,
  `puntajeMozo` int(11) NOT NULL,
  `puntajeMesa` int(11) NOT NULL,
  `puntajeRestaurante` int(11) NOT NULL,
  `puntajeCocinero` int(11) NOT NULL,
  `promedio` float NOT NULL,
  `descripcion` varchar(66) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `encuestas`
--

INSERT INTO `encuestas` (`id`, `codigoMesa`, `codigoPedido`, `puntajeMozo`, `puntajeMesa`, `puntajeRestaurante`, `puntajeCocinero`, `promedio`, `descripcion`) VALUES
(1, 'GGAki', 'Y7g6X', 9, 10, 7, 9, 8.75, 'todo muy lindo'),
(3, 'k00U5', 'Kk26Y', 4, 5, 3, 6, 4.5, 'medio pelo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `id` int(11) NOT NULL,
  `codigo` varchar(5) NOT NULL,
  `estado` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`id`, `codigo`, `estado`) VALUES
(1, 'vHVms', 'cerrada'),
(2, 'GGAki', 'cerrada'),
(3, 'k00U5', 'cerrada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `codigoMesa` varchar(5) NOT NULL,
  `codigoPedido` varchar(5) NOT NULL,
  `nombreCliente` varchar(25) NOT NULL,
  `precioFinal` int(11) NOT NULL,
  `rutaFoto` varchar(60) DEFAULT NULL,
  `tiempoEstimadoPedido` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `codigoMesa`, `codigoPedido`, `nombreCliente`, `precioFinal`, `rutaFoto`, `tiempoEstimadoPedido`) VALUES
(1, 'vHVms', 'UYHUf', 'Jose', 0, NULL, NULL),
(2, 'GGAki', 'CshvG', 'Luis', 0, NULL, NULL),
(4, 'k00U5', 'Kk26Y', 'Ines', 31500, NULL, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos_productos`
--

CREATE TABLE `pedidos_productos` (
  `id` int(11) NOT NULL,
  `codigoMesa` varchar(5) NOT NULL,
  `codigoPedido` varchar(5) NOT NULL,
  `nombreProducto` varchar(25) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precioTotal` int(11) NOT NULL,
  `encargado` varchar(20) NOT NULL,
  `estado` varchar(25) NOT NULL,
  `tiempoPreparacion` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos_productos`
--

INSERT INTO `pedidos_productos` (`id`, `codigoMesa`, `codigoPedido`, `nombreProducto`, `cantidad`, `precioTotal`, `encargado`, `estado`, `tiempoPreparacion`) VALUES
(39, 'k00U5', 'Kk26Y', 'Milanesa a caballo', 1, 8800, 'cocinero', 'pendiente', NULL),
(40, 'k00U5', 'Kk26Y', 'Hamburguesa de garbanzos', 2, 15200, 'cocinero', 'pendiente', NULL),
(41, 'k00U5', 'Kk26Y', 'Cerveza Corona', 1, 3500, 'cervecero', 'listo para servir', 0),
(42, 'k00U5', 'Kk26Y', 'Daikiri', 1, 4000, 'bartender', 'pendiente', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `precio` int(11) NOT NULL,
  `tiempoEstimado` int(11) NOT NULL,
  `encargado` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `precio`, `tiempoEstimado`, `encargado`) VALUES
(1, 'Pizza', 8000, 4, 'cocinero'),
(2, 'Cerveza', 2000, 1, 'cervecero'),
(3, 'Brownie', 5000, 5, 'cocinero'),
(4, 'Vino Rutini', 7000, 1, 'bartender'),
(5, 'Milanesa', 4500, 8, 'cocinero'),
(6, 'Flan', 3200, 2, 'cocinero'),
(7, 'Daikiri', 4000, 3, 'bartender'),
(8, 'Milanesa a caballo', 8800, 12, 'cocinero'),
(9, 'Hamburguesa de garbanzos', 7600, 15, 'cocinero'),
(10, 'Cerveza Corona', 3500, 1, 'cervecero');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `sector` varchar(20) NOT NULL,
  `fechaIngreso` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
  `nombre` varchar(20) NOT NULL,
  `clave` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `sector`, `fechaIngreso`, `fechaBaja`, `nombre`, `clave`) VALUES
(1, 'admin', '2022-06-20', NULL, 'Enrico', 'admin123'),
(2, 'mozo', '2024-06-18', NULL, 'Martin', 'mozo123'),
(3, 'cocinero', '2023-05-20', NULL, 'Jorge', 'cocina123'),
(4, 'bartender', '2023-08-10', NULL, 'Luisa', 'bar123'),
(6, 'cervecero', '2024-07-07', NULL, 'Ines', 'cerveza123');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos_productos`
--
ALTER TABLE `pedidos_productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `pedidos_productos`
--
ALTER TABLE `pedidos_productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
