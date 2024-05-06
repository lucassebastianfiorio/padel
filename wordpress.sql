-- phpMyAdmin SQL Dump
-- version 5.2.1deb1ubuntu1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 06-05-2024 a las 23:19:22
-- Versión del servidor: 8.0.36-0ubuntu0.23.10.1
-- Versión de PHP: 8.2.10-2ubuntu2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `wordpress`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wp_pa_categorias`
--

CREATE TABLE `wp_pa_categorias` (
  `id` mediumint NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Volcado de datos para la tabla `wp_pa_categorias`
--

INSERT INTO `wp_pa_categorias` (`id`, `nombre`) VALUES
(1, '1ra Caballeros'),
(2, '2da Caballeros'),
(3, '3ra Caballeros'),
(4, '4ta Caballeros'),
(5, '5ta Caballeros'),
(6, '6ta Caballeros'),
(7, '7ma Caballeros'),
(8, '8va Caballeros'),
(9, '1ra Damas'),
(10, '2da Damas'),
(11, '3ra Damas'),
(12, '4ta Damas'),
(13, '5ta Damas'),
(14, '6ta Damas'),
(15, '7ma Damas'),
(16, '8va Damas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wp_pa_inscritos`
--

CREATE TABLE `wp_pa_inscritos` (
  `id` mediumint NOT NULL,
  `torneo_id` mediumint NOT NULL,
  `pareja_id` mediumint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Volcado de datos para la tabla `wp_pa_inscritos`
--

INSERT INTO `wp_pa_inscritos` (`id`, `torneo_id`, `pareja_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 9),
(4, 1, 10),
(5, 1, 3),
(6, 1, 4),
(7, 1, 5),
(8, 1, 6),
(9, 1, 7),
(10, 1, 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wp_pa_jugadores`
--

CREATE TABLE `wp_pa_jugadores` (
  `id` mediumint NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `apellido` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `dni` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `categoria_id` mediumint NOT NULL,
  `partidos_jugados` int NOT NULL DEFAULT '0',
  `partidos_ganados` int NOT NULL DEFAULT '0',
  `foto_perfil` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `estado` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Volcado de datos para la tabla `wp_pa_jugadores`
--

INSERT INTO `wp_pa_jugadores` (`id`, `nombre`, `apellido`, `dni`, `email`, `telefono`, `categoria_id`, `partidos_jugados`, `partidos_ganados`, `foto_perfil`, `estado`) VALUES
(1, 'Juan', 'Gómez', '12345678A', 'juan@example.com', '123456789', 1, 10, 8, NULL, 'Pendiente'),
(2, 'María', 'López', '23456789B', 'maria@example.com', '987654321', 2, 8, 6, NULL, 'Confirmado'),
(3, 'Pedro', 'Martínez', '34567890C', 'pedro@example.com', '654321987', 3, 9, 7, NULL, 'Confirmado'),
(4, 'Ana', 'García', '45678901D', 'ana@example.com', '321987654', 4, 7, 5, NULL, 'Confirmado'),
(5, 'Carlos', 'Rodríguez', '56789012E', 'carlos@example.com', '987654123', 5, 6, 4, NULL, 'Confirmado'),
(6, 'Laura', 'Pérez', '67890123F', 'laura@example.com', '654123987', 9, 8, 6, NULL, 'Confirmado'),
(7, 'Pabloss', 'Sánchez', '78901234G', 'pablo@example.com', '321987456', 7, 5, 3, NULL, 'Confirmado'),
(8, 'Sofía', 'Fernández', '89012345H', 'sofia@example.com', '987456321', 8, 7, 7, NULL, 'Pendiente'),
(9, 'Diego', 'Hernández', '90123456I', 'diego@example.com', '654321789', 9, 6, 4, NULL, 'Confirmado'),
(10, 'Lucía', 'Gutiérrez', '01234567J', 'lucia@example.com', '321789654', 12, 9, 7, NULL, 'Confirmado'),
(11, 'Lucas', 'Fiorio', '32178947', 'lucasfiorio@hotmail.com', '123456', 1, 2, 1, NULL, 'Confirmado'),
(12, 'Lucas', 'Gutiérrez', '3217894', 'lala@lala.com', '123', 1, 0, 1, NULL, 'Confirmado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wp_pa_parejas`
--

CREATE TABLE `wp_pa_parejas` (
  `id` mediumint NOT NULL,
  `jugador1_id` mediumint NOT NULL,
  `jugador2_id` mediumint NOT NULL,
  `torneo_id` mediumint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Volcado de datos para la tabla `wp_pa_parejas`
--

INSERT INTO `wp_pa_parejas` (`id`, `jugador1_id`, `jugador2_id`, `torneo_id`) VALUES
(1, 1, 2, 1),
(2, 3, 4, 1),
(3, 5, 6, 1),
(4, 7, 8, 1),
(5, 9, 10, 1),
(6, 2, 3, 2),
(7, 4, 5, 2),
(8, 6, 7, 2),
(9, 8, 9, 2),
(10, 10, 1, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wp_pa_partidos`
--

CREATE TABLE `wp_pa_partidos` (
  `id` mediumint NOT NULL,
  `torneo_id` mediumint NOT NULL,
  `ronda` int NOT NULL,
  `pareja_local_id` mediumint NOT NULL,
  `pareja_visitante_id` mediumint NOT NULL,
  `ganador_id` mediumint DEFAULT NULL,
  `resultado` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Volcado de datos para la tabla `wp_pa_partidos`
--

INSERT INTO `wp_pa_partidos` (`id`, `torneo_id`, `ronda`, `pareja_local_id`, `pareja_visitante_id`, `ganador_id`, `resultado`) VALUES
(20, 1, 1, 1, 2, NULL, NULL),
(21, 1, 1, 9, 10, 10, NULL),
(22, 1, 1, 3, 4, 3, NULL),
(23, 1, 1, 5, 6, 6, NULL),
(24, 1, 1, 7, 8, 7, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wp_pa_torneos`
--

CREATE TABLE `wp_pa_torneos` (
  `id` mediumint NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `categoria_damas` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `categoria_caballeros` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `mixto` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Volcado de datos para la tabla `wp_pa_torneos`
--

INSERT INTO `wp_pa_torneos` (`id`, `nombre`, `fecha_inicio`, `fecha_fin`, `categoria_damas`, `categoria_caballeros`, `mixto`) VALUES
(1, 'Torneo de Verano', '2024-07-01', '2024-07-15', '1ra Damas', '1ra Caballeros', 1),
(2, 'Torneo de Otoño', '2024-09-01', '2024-09-15', '2da Damas', '2da Caballeros', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `wp_pa_categorias`
--
ALTER TABLE `wp_pa_categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `wp_pa_inscritos`
--
ALTER TABLE `wp_pa_inscritos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `torneo_id` (`torneo_id`),
  ADD KEY `pareja_id` (`pareja_id`);

--
-- Indices de la tabla `wp_pa_jugadores`
--
ALTER TABLE `wp_pa_jugadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni_unique` (`dni`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `wp_pa_parejas`
--
ALTER TABLE `wp_pa_parejas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jugador1_id` (`jugador1_id`),
  ADD KEY `jugador2_id` (`jugador2_id`),
  ADD KEY `torneo_id` (`torneo_id`);

--
-- Indices de la tabla `wp_pa_partidos`
--
ALTER TABLE `wp_pa_partidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `torneo_id` (`torneo_id`),
  ADD KEY `pareja_local_id` (`pareja_local_id`),
  ADD KEY `pareja_visitante_id` (`pareja_visitante_id`),
  ADD KEY `ganador_id` (`ganador_id`);

--
-- Indices de la tabla `wp_pa_torneos`
--
ALTER TABLE `wp_pa_torneos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `wp_pa_categorias`
--
ALTER TABLE `wp_pa_categorias`
  MODIFY `id` mediumint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `wp_pa_inscritos`
--
ALTER TABLE `wp_pa_inscritos`
  MODIFY `id` mediumint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `wp_pa_jugadores`
--
ALTER TABLE `wp_pa_jugadores`
  MODIFY `id` mediumint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `wp_pa_parejas`
--
ALTER TABLE `wp_pa_parejas`
  MODIFY `id` mediumint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `wp_pa_partidos`
--
ALTER TABLE `wp_pa_partidos`
  MODIFY `id` mediumint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `wp_pa_torneos`
--
ALTER TABLE `wp_pa_torneos`
  MODIFY `id` mediumint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `wp_pa_inscritos`
--
ALTER TABLE `wp_pa_inscritos`
  ADD CONSTRAINT `wp_pa_inscritos_ibfk_1` FOREIGN KEY (`torneo_id`) REFERENCES `wp_pa_torneos` (`id`),
  ADD CONSTRAINT `wp_pa_inscritos_ibfk_2` FOREIGN KEY (`pareja_id`) REFERENCES `wp_pa_parejas` (`id`);

--
-- Filtros para la tabla `wp_pa_jugadores`
--
ALTER TABLE `wp_pa_jugadores`
  ADD CONSTRAINT `wp_pa_jugadores_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `wp_pa_categorias` (`id`);

--
-- Filtros para la tabla `wp_pa_parejas`
--
ALTER TABLE `wp_pa_parejas`
  ADD CONSTRAINT `wp_pa_parejas_ibfk_1` FOREIGN KEY (`jugador1_id`) REFERENCES `wp_pa_jugadores` (`id`),
  ADD CONSTRAINT `wp_pa_parejas_ibfk_2` FOREIGN KEY (`jugador2_id`) REFERENCES `wp_pa_jugadores` (`id`),
  ADD CONSTRAINT `wp_pa_parejas_ibfk_3` FOREIGN KEY (`torneo_id`) REFERENCES `wp_pa_torneos` (`id`);

--
-- Filtros para la tabla `wp_pa_partidos`
--
ALTER TABLE `wp_pa_partidos`
  ADD CONSTRAINT `wp_pa_partidos_ibfk_1` FOREIGN KEY (`torneo_id`) REFERENCES `wp_pa_torneos` (`id`),
  ADD CONSTRAINT `wp_pa_partidos_ibfk_2` FOREIGN KEY (`pareja_local_id`) REFERENCES `wp_pa_inscritos` (`pareja_id`),
  ADD CONSTRAINT `wp_pa_partidos_ibfk_3` FOREIGN KEY (`pareja_visitante_id`) REFERENCES `wp_pa_inscritos` (`pareja_id`),
  ADD CONSTRAINT `wp_pa_partidos_ibfk_4` FOREIGN KEY (`ganador_id`) REFERENCES `wp_pa_inscritos` (`pareja_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
