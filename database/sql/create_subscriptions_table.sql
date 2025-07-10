-- Créer la table des abonnements
CREATE TABLE IF NOT EXISTS `subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insérer des données de test
INSERT INTO `subscriptions` (`name`, `type`, `price`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
('اشتراك أسبوعي', 'weekly', 29.99, 'اشتراك أسبوعي للوصول إلى جميع القوالب والميزات', 1, NOW(), NOW()),
('اشتراك شهري', 'monthly', 99.99, 'اشتراك شهري للوصول إلى جميع القوالب والميزات مع خصم 15%', 1, NOW(), NOW()),
('اشتراك سنوي', 'annual', 999.99, 'اشتراك سنوي للوصول إلى جميع القوالب والميزات مع خصم 30%', 1, NOW(), NOW());
