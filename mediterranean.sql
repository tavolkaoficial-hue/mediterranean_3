-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2025 at 04:36 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mediterranean`
--

-- --------------------------------------------------------

--
-- Table structure for table `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`) VALUES
(1, 'Ropa'),
(2, 'Comida'),
(3, 'Accesorios'),
(4, 'Mascotas'),
(5, 'Aseo'),
(6, 'Ropa'),
(7, 'Comida'),
(8, 'Accesorios'),
(9, 'Mascotas'),
(10, 'Herramienta'),
(11, 'Hogar'),
(12, 'Tornilleria'),
(13, 'Navidad'),
(14, 'Aseo');

-- --------------------------------------------------------

--
-- Table structure for table `movimientos`
--

CREATE TABLE `movimientos` (
  `id` int(11) NOT NULL,
  `productos` int(11) NOT NULL,
  `tipo` enum('Entrada','Salida') NOT NULL,
  `cantidad` bigint(20) NOT NULL,
  `comentario` text DEFAULT NULL,
  `sucursal` varchar(50) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movimientos`
--

INSERT INTO `movimientos` (`id`, `productos`, `tipo`, `cantidad`, `comentario`, `sucursal`, `fecha`) VALUES
(1, 14, 'Entrada', 1232122, 'hola mundo ', '', '2025-10-26 18:01:11'),
(2, 15, 'Entrada', 10, 'Hola como estas. ', '', '2025-10-26 18:08:18'),
(3, 15, 'Entrada', 1231, 'taste', '', '2025-10-26 18:15:40'),
(4, 15, 'Entrada', 2147483647, 'emma', '', '2025-10-26 18:43:40'),
(5, 13, 'Entrada', 1233212, 'ema ', '', '2025-10-26 18:44:11'),
(6, 20, 'Entrada', 12312, 'regstio unico', '', '2025-10-26 19:56:37'),
(7, 20, 'Entrada', 1, 'w', '', '2025-10-26 20:16:03'),
(8, 20, 'Entrada', 12, 'factura 23', '', '2025-10-29 18:49:58'),
(9, 20, 'Entrada', 10, 'trato de sucursal.', '', '2025-10-29 20:37:42'),
(10, 20, 'Salida', 6, 'j', '', '2025-10-29 20:41:18'),
(11, 20, 'Entrada', 7765, 'jjh', 'Centro', '2025-10-29 21:02:34'),
(12, 20, 'Entrada', 2000, 'hola', 'Centro', '2025-10-29 21:03:12'),
(13, 20, 'Entrada', 20000, 'holis', 'Centro', '2025-10-29 21:03:56'),
(14, 20, 'Salida', 12, 'gf', 'Kennedy', '2025-10-29 21:12:03'),
(15, 20, 'Salida', 10, 'bubu', 'Kennedy', '2025-10-29 21:17:09'),
(16, 20, 'Entrada', 11, 'ema', 'Norte', '2025-10-29 21:44:31'),
(17, 19, 'Entrada', 11, 'ee', 'Norte', '2025-10-29 21:47:50'),
(18, 19, 'Entrada', 1121, 'ema', '', '2025-10-29 21:49:06'),
(19, 20, 'Entrada', 122123, 'lulu', 'Norte', '2025-10-29 22:07:00'),
(20, 21, 'Entrada', 19, 'se mebarazo', 'Kennedy', '2025-10-29 22:15:59'),
(21, 21, 'Entrada', 11, 'bebe', 'Centro', '2025-10-30 18:09:16'),
(22, 22, 'Salida', 500, 'venta oportuna a estados unidos. ', 'Kennedy', '2025-10-30 18:25:37'),
(23, 20, 'Entrada', 1000, 'lolo', 'Kennedy', '2025-10-30 18:45:22'),
(24, 22, 'Entrada', 100, 'sebastian es mi bebe', 'Centro', '2025-11-01 13:44:13'),
(25, 21, 'Entrada', 2147483647, 'peter practica ', 'Centro', '2025-11-04 21:15:27'),
(26, 23, 'Entrada', 10, 'mi familia', 'Centro', '2025-11-04 21:25:43'),
(27, 24, 'Entrada', 11, 'mi mejor jugada', 'Centro', '2025-11-04 21:50:47'),
(28, 24, 'Entrada', 1111, 'iphone 16', 'Centro', '2025-11-04 22:00:55'),
(29, 25, 'Entrada', 30, 'compra mayor', 'Norte', '2025-11-04 22:58:16'),
(30, 28, 'Entrada', 13, 'lele ', 'Norte', '2025-11-05 19:18:13'),
(31, 30, 'Salida', 10, 'buena onda', 'Centro', '2025-11-05 20:01:45'),
(32, 21, 'Salida', 2147483600, 'gran venta', 'Centro', '2025-11-05 20:33:20'),
(33, 15, 'Salida', 2147483600, 'segura venta. ', 'Centro', '2025-11-05 20:35:50'),
(34, 31, 'Salida', 5, 'segunda prueba. ', 'Kennedy', '2025-11-05 20:45:39'),
(35, 31, 'Entrada', 10, 'buena practica', 'Kenneddy', '2025-11-05 21:27:01'),
(36, 32, 'Entrada', 10, 'actualización.', 'Kenneddy', '2025-11-05 21:31:12'),
(37, 32, 'Salida', 35, 'rocio', 'Kenneddy', '2025-11-05 21:48:41'),
(38, 32, 'Entrada', 20, 'lelele', 'Kenneddy', '2025-11-05 21:59:16'),
(39, 32, 'Entrada', 20, 'pollo', 'Kenneddy', '2025-11-05 22:09:14'),
(40, 32, 'Entrada', 12, 'familia', 'Centro', '2025-11-05 22:09:45'),
(41, 32, 'Entrada', 10, 'felices', 'Norte', '2025-11-05 22:10:04'),
(42, 32, 'Entrada', 12, 'hijo', 'Kenneddy', '2025-11-05 22:27:07'),
(43, 33, 'Entrada', 12, 'rico', 'Kennedy', '2025-11-19 19:49:47'),
(44, 33, 'Salida', 12, 'salida generañ', 'Kennedy', '2025-11-19 20:24:51'),
(45, 33, 'Entrada', 15, 'venta del dia', 'Kennedy', '2025-11-19 20:28:01'),
(46, 33, 'Salida', 200, 'ventas', 'Kennedy', '2025-11-19 20:29:20'),
(47, 34, 'Entrada', 199, 'tv', 'Kennedy', '2025-11-19 21:21:27'),
(48, 33, 'Entrada', 11, 'pepeprooepore', 'Norte', '2025-11-25 22:09:37'),
(49, 33, 'Salida', 25, 'mut', 'Kennedy', '2025-11-25 22:11:22'),
(50, 41, 'Entrada', 12, 'dese', 'Centro', '2025-11-26 20:10:18'),
(51, 42, 'Entrada', 13, 'martes', 'Kennedy', '2025-11-26 20:18:25'),
(52, 48, 'Entrada', 12, 'ppp', 'Kennedy', '2025-11-26 21:32:52'),
(53, 48, 'Entrada', 12234, 'mejorotor', 'Kennedy', '2025-11-26 21:34:08');

-- --------------------------------------------------------

--
-- Table structure for table `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `categorias` varchar(100) DEFAULT NULL,
  `proveedores` varchar(100) DEFAULT NULL,
  `precio_compra` decimal(10,2) DEFAULT NULL,
  `precio_venta` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `sucursal` varchar(50) DEFAULT NULL,
  `img` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `margen_calculado` decimal(5,2) GENERATED ALWAYS AS ((`precio_venta` - `precio_compra`) / `precio_compra` * 100) STORED,
  `estado` enum('Activo','Inactivo','Descontinuado') DEFAULT 'Activo',
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `categorias`, `proveedores`, `precio_compra`, `precio_venta`, `stock`, `sucursal`, `img`, `descripcion`, `estado`, `fecha_actualizacion`) VALUES
(4, 'Framing tonillo', NULL, NULL, NULL, NULL, 43321, 'Centro', 'uploads/1759721368_framing.jpeg', 'Tornillo general de framing', 'Activo', '2025-11-05 03:30:49'),
(6, 'LAMINA', NULL, NULL, NULL, NULL, 34443442, 'Centro', 'uploads/1759892402_lamina.jpeg', 'fgfdgdfg', 'Activo', '2025-11-05 03:30:49'),
(7, 'estrucrua ', NULL, NULL, NULL, NULL, 777, 'Centro', 'uploads/1759921428_drywall.jpeg', 'rosca fina ', 'Activo', '2025-11-05 03:30:49'),
(11, 'LAMINA', NULL, NULL, NULL, NULL, 45, 'Centro', 'uploads/1761010319_chatbot.png', 'fgdfgdfgf', 'Activo', '2025-11-05 03:30:49'),
(12, 'PedroGuevara', NULL, NULL, NULL, NULL, 5454, 'Centro', 'uploads/1761012030_chatbot.png', '434tertertrter', 'Activo', '2025-11-05 03:30:49'),
(13, 'trtyryt', NULL, NULL, NULL, NULL, 1233246, 'Centro', 'uploads/1761357158_peter.jpg', 'hghghghghgh', 'Activo', '2025-11-05 03:30:49'),
(14, 'vaso', NULL, NULL, NULL, NULL, 1232155, 'Centro', NULL, NULL, 'Activo', '2025-11-05 03:30:49'),
(15, 'tinto', NULL, NULL, NULL, NULL, 47, 'Centro', 'uploads/1761520037_descarga.jfif', 'esto se toma caliente. ', 'Activo', '2025-11-06 01:35:50'),
(16, 'torra ', NULL, NULL, NULL, NULL, 4, 'Centro', 'uploads/1761522759_descarga.jfif', 'rica', 'Activo', '2025-11-05 03:30:49'),
(17, 'pedro antonio guevara rojas ', NULL, NULL, NULL, NULL, 1, 'Centro', 'uploads/1761523550_peter.jpg', 'economia', 'Activo', '2025-11-05 03:30:49'),
(18, 'salome castillo ', NULL, NULL, NULL, NULL, 1, 'Centro', 'uploads/1761525299_descarga.jfif', 'hola', 'Activo', '2025-11-05 03:30:49'),
(19, 'sebastian calvo Rojas', 'liquindo', 'jenny', 100.00, 200.00, 1232, 'Kennedy', 'uploads/1761526070_peter.jpg', 'bebe', 'Activo', '2025-11-05 03:22:27'),
(20, 'pedro antonio guevara rojas ', 'comer ', 'mezcla ', 100.00, 220.00, 165226, 'Kennedy', 'uploads/1761526562_descarga.jfif', 'yo', 'Activo', '2025-11-05 03:22:27'),
(21, 'lola', 'perro', 'vete', 1200.00, 2300.00, 47, 'Kennedy', 'uploads/1761794135_chatbot.png', 'mejor amigo del hombre', 'Activo', '2025-11-06 01:33:20'),
(22, 'you tube ', 'canal', 'internet ', 2300.00, 3100.00, 2600, 'Kennedy', 'uploads/1761866675_you tobe.png', 'plataforma para ver videos. ', 'Activo', '2025-11-05 03:22:27'),
(23, 'Rosario', 'playa', 'avianca', 300.00, 500.00, 11, 'Kennedy', 'uploads/1762309522_playa.jpg', 'la mejor playa del mundo. ', 'Activo', '2025-11-05 03:22:27'),
(24, 'acacias', 'mansion', 'ferre', 200.00, NULL, 1123, 'Centro', 'uploads/1762311028_casa.jpg', 'mi casa en acacias.', 'Activo', '2025-11-05 03:00:55'),
(25, 'Crib', 'navidad', 'valleta glass', 200.00, 300.00, 32, '0', 'uploads/1762315070_navidad.jpg', 'mejor navidad ', 'Activo', '2025-11-05 03:58:16'),
(26, 'luluz', 'mata', 'pota', 100.00, 200.00, 12, '0', 'uploads/1762315342_descarga.jfif', 'ssss', 'Activo', '2025-11-05 04:02:22'),
(27, 'lamina ', 'comer ', 'nestle', 1222.00, 2555.00, 23, '0', 'uploads/1762315419_peter123.png', 'lulu', 'Activo', '2025-11-05 04:03:39'),
(28, 'luluz', 'comer ', 'valleta glass', 1000.00, 2000.00, 25, '0', 'uploads/1762388255_playa.jpg', 'mejor', 'Activo', '2025-11-06 00:18:13'),
(29, 'nene', 'tyrtyrtyyu', 'mami', 1200.00, 3000.00, 30, '0', 'uploads/1762389983_casa.jpg', 'navidad', 'Activo', '2025-11-06 00:46:23'),
(30, 'casa', 'madera', 'nestle', 1200.00, 2000.00, 10, 'Centro', 'uploads/1762390861_navidad.jpg', 'serte', 'Activo', '2025-11-06 01:01:45'),
(31, 'nieve ', 'navidad', 'arbol', 200.00, 300.00, 20, 'Kenneddy', 'uploads/1762393510_casa.jpg', 'prueba kennedy', 'Activo', '2025-11-06 02:27:01'),
(32, 'lamborgini', 'carro', 'italia', 200.00, 300.00, 149, 'Kenneddy', 'uploads/1762396219_carros.jpg', 'mejor de todos. ', 'Activo', '2025-11-06 03:27:07'),
(33, 'planta', 'madera', 'valleta glass', 500.00, 700.00, 1, 'Kennedy', 'uploads/1762401841_carros.jpg', 'pequeña', 'Activo', '2025-11-26 03:11:22'),
(34, 'audifonos', 'tegnologia', 'jab', 200.00, 300.00, 299, 'Kennedy', 'uploads/1763602259_aaaaaaaaa.png', 'azules', 'Activo', '2025-11-20 02:21:27'),
(41, 'dede', 'Navidad', 'Bimbo', 333.00, 333.00, 15, 'Centro', 'uploads/1764205791_amenaza.png', 'fff', 'Activo', '2025-11-27 01:10:18'),
(42, 'perfume', 'Ropa', 'Ferretería XYZ', 200.00, 300.00, 413, 'Centro', 'uploads/1764206033_aaaa.jpg', 'test', 'Activo', '2025-11-27 01:18:25'),
(43, 'payasol', 'Mascotas', 'Bimbo', 1919.00, 2020.00, 100, 'Kennedy', 'uploads/1764207633_asxa.png', 'tetsretrs', 'Activo', '2025-11-27 01:40:33'),
(44, 'sal', 'Hogar', 'Nestle', 100.00, 200.00, 300, 'Centro', 'uploads/1764208176_bbbbb.jpg', 'teleorjsoje', 'Activo', '2025-11-27 01:49:36'),
(45, 'arbolito', 'Aseo', 'Coca Cola', 787.00, 87777.00, 12, 'Norte', 'uploads/1764208791_lululu.png', 'mnnbbvc', 'Activo', '2025-11-27 01:59:51'),
(46, 'swsw', 'Ropa', 'Ferretería XYZ', 1212.00, 323.00, 444, 'Centro', 'uploads/1764208937_mumu5.jpg', 'fff', 'Activo', '2025-11-27 02:02:17'),
(47, 'www', 'Ropa', 'Nestle', 332.00, 444.00, 555, 'Kennedy', 'uploads/1764209039_tete4.jpg', 'ggfgg', 'Activo', '2025-11-27 02:03:59'),
(48, 'ppppp', 'Hogar', 'Ferretería XYZ', 66654.00, 7776656.00, 12258, 'Kennedy', 'uploads/1764210752_tete1.jpg', 'ppppppp', 'Activo', '2025-11-27 02:34:08'),
(49, 'hoja', 'Aseo', 'Nestle', 100.00, 200.00, 30, 'Centro', 'uploads/1764212300_recursos claves.jpg', 'blnaco', 'Activo', '2025-11-27 02:58:20');

-- --------------------------------------------------------

--
-- Table structure for table `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `proveedores`
--

INSERT INTO `proveedores` (`id`, `nombre`) VALUES
(1, 'Nestle'),
(2, 'Bimbo'),
(3, 'Coca Cola'),
(4, 'Avianca'),
(5, 'Tornillo loco'),
(6, 'Staley'),
(7, 'Home Center'),
(8, 'Truper'),
(9, 'Ferretería XYZ');

-- --------------------------------------------------------

--
-- Table structure for table `reportes`
--

CREATE TABLE `reportes` (
  `id` int(11) NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `fecha` date NOT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `id_producto` int(11) NOT NULL,
  `id_sucursal` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `Id` varchar(100) CHARACTER SET armscii8 COLLATE armscii8_general_nopad_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sucursales`
--

CREATE TABLE `sucursales` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `codigo_sucursal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sucursales`
--

INSERT INTO `sucursales` (`id`, `nombre`, `codigo_sucursal`) VALUES
(1, 'Kennedy', 101),
(2, 'Norte', 102),
(4, 'Centro', 103);

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuarios` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(100) NOT NULL,
  `rol` enum('Administrador','Editor','Invitado') NOT NULL DEFAULT 'Invitado',
  `descripcion` text DEFAULT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `foto` varchar(255) DEFAULT NULL,
  `cv` varchar(255) DEFAULT NULL,
  `permisos` varchar(500) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuarios`, `password`, `correo`, `telefono`, `rol`, `descripcion`, `estado`, `foto`, `cv`, `permisos`) VALUES
(23, 'PedroGuevara', '$2y$10$VeCF8LqaMEecIIZjLD1gn.mdjeUQayndhAGXChrj.D2FcZFEdxdPe', 'tavolkaoficial@gmail.com', '', 'Administrador', NULL, 'activo', 'uploads/usr_690a6ce483bea.png', NULL, ''),
(25, 'SebasRojas', '$2y$10$p2g1hYhvId00kAO/RIbeR.uSmB9siPFW4LHb/Es7VBQxdo6uqzwSK', 'juan2010calvo@gmail.com', '', 'Invitado', NULL, 'activo', 'uploads/690a6abd3c748.jpeg', NULL, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`productos`);

--
-- Indexes for table `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reportes`
--
ALTER TABLE `reportes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_sucursal` (`id_sucursal`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_sucursal` (`id_sucursal`);

--
-- Indexes for table `sucursales`
--
ALTER TABLE `sucursales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_sucursal` (`codigo_sucursal`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuarios`),
  ADD UNIQUE KEY `idx_usuarios_correo` (`correo`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `reportes`
--
ALTER TABLE `reportes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sucursales`
--
ALTER TABLE `sucursales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `movimientos`
--
ALTER TABLE `movimientos`
  ADD CONSTRAINT `movimientos_ibfk_1` FOREIGN KEY (`productos`) REFERENCES `productos` (`id`);

--
-- Constraints for table `reportes`
--
ALTER TABLE `reportes`
  ADD CONSTRAINT `reportes_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_ibfk_2` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
