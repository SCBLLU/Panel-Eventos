-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-08-2024 a las 06:13:33
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `corporate events`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `rol` enum('administrador','empleado') NOT NULL,
  `contraseña` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `nombre`, `correo`, `rol`, `contraseña`) VALUES
(1, 'Rafael\r\n', 'scbllu.200@gmail.com', 'empleado', '1234');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `fecha_fin` date NOT NULL,
  `hora_fin` time NOT NULL,
  `lugar` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `estado` enum('Pendiente','Confirmado','En Progreso','Finalizado','Cancelado') NOT NULL DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id`, `nombre`, `fecha_inicio`, `hora_inicio`, `fecha_fin`, `hora_fin`, `lugar`, `descripcion`, `estado`) VALUES
(1, 'Reunión de Negocios', '2024-08-01', '09:00:00', '2024-08-01', '12:00:00', 'San Salvador', 'Reunión para discutir nuevas estrategias de negocio.', 'Pendiente'),
(2, 'Cumpleaños', '2024-08-15', '20:39:00', '2024-08-15', '23:39:00', 'Zaragoza', 'Se puede o no', 'Pendiente'),
(3, 'Compras de año nuevo', '2024-08-07', '07:44:00', '2024-08-07', '10:40:00', 'Santa Tecla', 'Compras', 'Pendiente'),
(4, 'Fiesta', '2024-08-13', '23:00:00', '2024-08-15', '10:00:00', 'Sonsonate', 'Fiesta de aniversario', 'Pendiente'),
(5, 'Taller de Fotografía', '2024-08-05', '10:00:00', '2024-08-05', '16:00:00', 'San Salvador', 'Taller para aprender técnicas de fotografía.', 'Pendiente'),
(6, 'Exposición de Moda', '2024-08-10', '18:00:00', '2024-08-10', '22:00:00', 'Santa Tecla', 'Exposición de las últimas tendencias en moda.', 'Pendiente'),
(7, 'Concierto de Música Clásica', '2024-08-12', '20:00:00', '2024-08-12', '22:00:00', 'San Salvador', 'Concierto de música clásica en el teatro nacional.', 'Pendiente'),
(8, 'Mercado de Agricultores', '2024-08-17', '08:00:00', '2024-08-17', '14:00:00', 'Antiguo Cuscatlán', 'Mercado con productos frescos de agricultores locales.', 'Pendiente'),
(9, 'Festival de Cine', '2024-08-13', '19:00:00', '2024-08-13', '23:00:00', 'San Miguel', 'Festival con proyecciones de películas independientes.', 'Pendiente'),
(10, 'Jornada de Voluntariado', '2024-08-25', '07:00:00', '2024-08-25', '14:00:00', 'San Salvador', 'Jornada para trabajar en proyectos comunitarios.', 'Pendiente');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
