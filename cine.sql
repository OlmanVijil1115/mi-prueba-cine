-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-07-2025 a las 22:50:44
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cine`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asientos`
--

CREATE TABLE `asientos` (
  `id` int(11) NOT NULL,
  `sala` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `estado` tinyint(4) DEFAULT 0,
  `pelicula_id` int(11) DEFAULT NULL
) ;

--
-- Volcado de datos para la tabla `asientos`
--

INSERT INTO `asientos` (`id`, `sala`, `numero`, `estado`, `pelicula_id`) VALUES
(51, 1, 1, 0, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `pelicula_id` int(11) NOT NULL,
  `asiento_id` int(11) NOT NULL,
  `golosina_id` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras_golosinas`
--

CREATE TABLE `compras_golosinas` (
  `id` int(11) NOT NULL,
  `golosina_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_final` decimal(10,2) NOT NULL,
  `fecha_compra` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compras_golosinas`
--

INSERT INTO `compras_golosinas` (`id`, `golosina_id`, `usuario_id`, `cantidad`, `precio_final`, `fecha_compra`) VALUES
(1, 2, 3, 2, 20.00, '2025-07-30 15:11:04'),
(2, 1, 1, 1, 15.00, '2025-07-30 21:57:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `golosinas`
--

CREATE TABLE `golosinas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `proveedor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `golosinas`
--

INSERT INTO `golosinas` (`id`, `nombre`, `precio`, `stock`, `proveedor_id`) VALUES
(1, 'Palomitas', 15.00, 99, 1),
(2, 'Refresco', 10.00, 148, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `peliculas`
--

CREATE TABLE `peliculas` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `sala` int(11) NOT NULL,
  `horario` datetime NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `proveedor_id` int(11) NOT NULL
) ;

--
-- Volcado de datos para la tabla `peliculas`
--

INSERT INTO `peliculas` (`id`, `titulo`, `sala`, `horario`, `precio`, `proveedor_id`) VALUES
(4, 'Spy x Family', 1, '2025-08-07 16:13:00', 100.00, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id`, `nombre`) VALUES
(1, 'Proveedor 1'),
(2, 'Proveedor 2');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `membresia` enum('basica','premium') DEFAULT 'basica',
  `rol` varchar(50) DEFAULT 'usuario',
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `membresia`, `rol`, `fecha_registro`) VALUES
(1, 'Admin', 'admin@correo.com', 'admin123', 'premium', 'admin', '2025-07-30 14:34:49'),
(2, 'Usuario', 'user@correo.com', 'user123', 'basica', 'usuario', '2025-07-30 14:34:49'),
(3, 'Olman', 'Jafet@gmail.com', '151199', 'premium', 'usuario', '2025-07-30 15:06:18');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asientos`
--
ALTER TABLE `asientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pelicula_id` (`pelicula_id`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `pelicula_id` (`pelicula_id`),
  ADD KEY `asiento_id` (`asiento_id`),
  ADD KEY `golosina_id` (`golosina_id`);

--
-- Indices de la tabla `compras_golosinas`
--
ALTER TABLE `compras_golosinas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `golosina_id` (`golosina_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `golosinas`
--
ALTER TABLE `golosinas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proveedor_id` (`proveedor_id`);

--
-- Indices de la tabla `peliculas`
--
ALTER TABLE `peliculas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proveedor_id` (`proveedor_id`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asientos`
--
ALTER TABLE `asientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compras_golosinas`
--
ALTER TABLE `compras_golosinas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `golosinas`
--
ALTER TABLE `golosinas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `peliculas`
--
ALTER TABLE `peliculas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asientos`
--
ALTER TABLE `asientos`
  ADD CONSTRAINT `asientos_ibfk_1` FOREIGN KEY (`pelicula_id`) REFERENCES `peliculas` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `compras_ibfk_2` FOREIGN KEY (`pelicula_id`) REFERENCES `peliculas` (`id`),
  ADD CONSTRAINT `compras_ibfk_3` FOREIGN KEY (`asiento_id`) REFERENCES `asientos` (`id`),
  ADD CONSTRAINT `compras_ibfk_4` FOREIGN KEY (`golosina_id`) REFERENCES `golosinas` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `compras_golosinas`
--
ALTER TABLE `compras_golosinas`
  ADD CONSTRAINT `compras_golosinas_ibfk_1` FOREIGN KEY (`golosina_id`) REFERENCES `golosinas` (`id`),
  ADD CONSTRAINT `compras_golosinas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `golosinas`
--
ALTER TABLE `golosinas`
  ADD CONSTRAINT `golosinas_ibfk_1` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`);

--
-- Filtros para la tabla `peliculas`
--
ALTER TABLE `peliculas`
  ADD CONSTRAINT `peliculas_ibfk_1` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
