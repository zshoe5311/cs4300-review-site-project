CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `isAdmin` tinyint(1) NOT NULL,
  `isBanned` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`id`, `username`, `password`, `created_at`, `isAdmin`, `isBanned`) VALUES
(1, 'guest', '$2y$10$aXxb56cZbh5KK0Szc.wnWuEn425dtCD4xaTJHXVsWul43QnfmLcze', '2022-04-24 17:24:56', 0, 0),
(4, 'zeke', '$2y$10$j.Mg0eDkqza475as.ALkYOoGf3mA5hMx3jVYVMK.PDEIUcuIGhShy', '2022-04-18 17:05:40', 1, 0),
(5, 'matthew', '$2y$10$NitMEwGCXdkWbF13TwsjtuHuDaMR4lKemI3FKpjcrUUHK8lwOI.m.', '2022-04-19 18:05:08', 1, 0),
(6, 'patrick', '$2y$10$14DIp0.7aWmZkec3OzjBHe1Af2XYUFN0oc3yetp23QP.vcH.Vm9ju', '2022-04-19 18:05:32', 1, 0),
(7, 'ellen', '$2y$10$3OuRLgWjcXiJnwzLfVA0PeRQG/P81x1lTtPWqPbJCS08WNctGObmS', '2022-04-19 18:05:43', 1, 0),
(8, 'sam', '$2y$10$FiTUOa7j42uY7SG2tcGjC.dLJL8mdzm0gHkERPnUuKACAao4gg5ca', '2022-04-19 18:05:53', 1, 0);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

