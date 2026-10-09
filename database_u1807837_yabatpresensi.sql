SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `attendances`;
CREATE TABLE `attendances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `institution_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `status` enum('hadir','terlambat','izin','sakit','alpa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hadir',
  `latitude_in` decimal(10,8) DEFAULT NULL,
  `longitude_in` decimal(11,8) DEFAULT NULL,
  `latitude_out` decimal(10,8) DEFAULT NULL,
  `longitude_out` decimal(11,8) DEFAULT NULL,
  `photo_in` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo_out` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_info` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendances_employee_id_date_unique` (`employee_id`,`date`),
  KEY `attendances_institution_id_foreign` (`institution_id`),
  CONSTRAINT `attendances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `employees`;
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `institution_id` bigint unsigned NOT NULL,
  `nip_nidn` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `employment_status` enum('tetap','kontrak','honorer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tetap',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `address` text COLLATE utf8mb4_unicode_ci,
  `join_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_nip_nidn_unique` (`nip_nidn`),
  KEY `employees_user_id_foreign` (`user_id`),
  KEY `employees_institution_id_foreign` (`institution_id`),
  CONSTRAINT `employees_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `employees` (`id`, `user_id`, `institution_id`, `nip_nidn`, `name`, `email`, `phone`, `gender`, `position`, `employment_status`, `is_active`, `address`, `join_date`, `created_at`, `updated_at`) VALUES ('8', '3', '1', '0112088501', 'Dr. Rahmat Hidayat, M.Pd', 'rahmat@stkip-usmansafri.ac.id', '0812-6482-9901', 'L', 'Dosen Tetap (Kaprodi)', 'tetap', '1', NULL, '2019-03-01', '2026-10-09 15:34:02', '2026-10-09 15:40:12');
INSERT INTO `employees` (`id`, `user_id`, `institution_id`, `nip_nidn`, `name`, `email`, `phone`, `gender`, `position`, `employment_status`, `is_active`, `address`, `join_date`, `created_at`, `updated_at`) VALUES ('9', '4', '1', '0125048902', 'Nurhalimah, S.Pd., M.Hum', 'nurhalimah@stkip-usmansafri.ac.id', '0852-9923-4412', 'P', 'Dosen Pendidikan Bahasa', 'tetap', '1', NULL, '2021-08-15', '2026-10-09 15:34:02', '2026-10-09 15:40:12');
INSERT INTO `employees` (`id`, `user_id`, `institution_id`, `nip_nidn`, `name`, `email`, `phone`, `gender`, `position`, `employment_status`, `is_active`, `address`, `join_date`, `created_at`, `updated_at`) VALUES ('10', '5', '2', 'THW-2022-042', 'Ust. Faisal Akbar, Lc', 'faisal@thawalib.sch.id', '0821-7734-1189', 'L', 'Ustadz Pengasuh Asrama', 'tetap', '1', NULL, '2020-01-10', '2026-10-09 15:34:02', '2026-10-09 15:40:12');
INSERT INTO `employees` (`id`, `user_id`, `institution_id`, `nip_nidn`, `name`, `email`, `phone`, `gender`, `position`, `employment_status`, `is_active`, `address`, `join_date`, `created_at`, `updated_at`) VALUES ('11', '6', '2', 'THW-2023-088', 'Ustazah Siti Sarah, S.Ag', 'siti.sarah@thawalib.sch.id', '0813-9002-3312', 'P', 'Pengajar Tahfidz Quran', 'tetap', '1', NULL, '2022-06-01', '2026-10-09 15:34:02', '2026-10-09 15:40:12');
INSERT INTO `employees` (`id`, `user_id`, `institution_id`, `nip_nidn`, `name`, `email`, `phone`, `gender`, `position`, `employment_status`, `is_active`, `address`, `join_date`, `created_at`, `updated_at`) VALUES ('12', '7', '3', '198405102008011003', 'Budi Pratama, S.Kom, Gr.', 'budi@smkanakbangsa.sch.id', '0853-6112-7880', 'L', 'Guru Produktif RPL / IT', 'tetap', '1', NULL, '2018-07-15', '2026-10-09 15:34:02', '2026-10-09 15:40:12');
INSERT INTO `employees` (`id`, `user_id`, `institution_id`, `nip_nidn`, `name`, `email`, `phone`, `gender`, `position`, `employment_status`, `is_active`, `address`, `join_date`, `created_at`, `updated_at`) VALUES ('13', '8', '3', '199203142019032004', 'Cut Mutia, S.Pd', 'cutmutia@smkanakbangsa.sch.id', '0822-4412-9988', 'P', 'Guru Matematika', 'kontrak', '1', NULL, '2023-01-05', '2026-10-09 15:34:02', '2026-10-09 15:40:12');

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `institutions`;
CREATE TABLE `institutions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `radius_meters` int NOT NULL DEFAULT '100',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `head_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `institutions_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `institutions` (`id`, `name`, `code`, `category`, `description`, `latitude`, `longitude`, `radius_meters`, `address`, `phone`, `email`, `head_name`, `is_active`, `created_at`, `updated_at`) VALUES ('1', 'STKIP Usman Safri', 'STKIP', 'Perguruan Tinggi', 'Sekolah Tinggi Keguruan & Ilmu Pendidikan sarjana terakreditasi.', '3.48830000', '97.80850000', '100', 'Pulonas Baru, Kec. Lawe Bulan, Kabupaten Aceh Tenggara, Aceh', '0812-6482-9901', 'info@stkip-usmansafri.ac.id', 'Dr. Rahmat Hidayat, M.Pd', '1', '2026-10-09 14:31:04', '2026-10-09 14:45:56');
INSERT INTO `institutions` (`id`, `name`, `code`, `category`, `description`, `latitude`, `longitude`, `radius_meters`, `address`, `phone`, `email`, `head_name`, `is_active`, `created_at`, `updated_at`) VALUES ('2', 'Pesantren Thawalib', 'THAWALIB', 'Pesantren Asrama', 'Pondok pesantren terpadu dengan sistem asrama dan kajian kitab kuning.', '3.48910000', '97.80990000', '150', 'Pulonas Baru, Kec. Lawe Bulan, Kabupaten Aceh Tenggara, Aceh', '0821-7734-1189', 'sekretariat@thawalib.sch.id', 'Ust. Faisal Akbar, Lc', '1', '2026-10-09 14:31:04', '2026-10-09 14:45:56');
INSERT INTO `institutions` (`id`, `name`, `code`, `category`, `description`, `latitude`, `longitude`, `radius_meters`, `address`, `phone`, `email`, `head_name`, `is_active`, `created_at`, `updated_at`) VALUES ('3', 'SMK Swasta Anak Bangsa', 'SMK-AB', 'Sekolah Kejuruan', 'Sekolah Menengah Kejuruan fokus pada keahlian teknologi informatika.', '3.48750000', '97.80720000', '120', 'Pulonas Baru, Kec. Lawe Bulan, Kabupaten Aceh Tenggara, Aceh', '0853-6112-7880', 'admin@smkanakbangsa.sch.id', 'Drs. Usman Safri, M.M', '1', '2026-10-09 14:31:04', '2026-10-09 14:45:56');

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('1', '2014_10_12_000000_create_users_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('2', '2014_10_12_100000_create_password_resets_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('3', '2019_08_19_000000_create_failed_jobs_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('4', '2019_12_14_000001_create_personal_access_tokens_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('5', '2026_10_09_142717_create_institutions_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('6', '2026_10_09_142728_create_employees_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('7', '2026_10_09_142733_create_work_schedules_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('8', '2026_10_09_142739_create_attendances_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('9', '2026_10_09_142744_create_settings_table', '1');

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`id`, `key`, `value`, `group`, `created_at`, `updated_at`) VALUES ('1', 'app_name', 'YABAT PRESENSI', 'general', '2026-10-09 14:31:04', '2026-10-09 14:31:04');
INSERT INTO `settings` (`id`, `key`, `value`, `group`, `created_at`, `updated_at`) VALUES ('2', 'institution_name', 'Yayasan Anak Bangsa Aceh Tenggara', 'general', '2026-10-09 14:31:04', '2026-10-09 14:31:04');
INSERT INTO `settings` (`id`, `key`, `value`, `group`, `created_at`, `updated_at`) VALUES ('3', 'foundation_address', 'Jl. Kutacane - Blangkejeren, Aceh Tenggara, Aceh', 'general', '2026-10-09 14:31:04', '2026-10-09 14:31:04');
INSERT INTO `settings` (`id`, `key`, `value`, `group`, `created_at`, `updated_at`) VALUES ('4', 'gps_tolerance_meters', '100', 'general', '2026-10-09 14:31:04', '2026-10-09 14:31:04');
INSERT INTO `settings` (`id`, `key`, `value`, `group`, `created_at`, `updated_at`) VALUES ('5', 'allow_mock_location', 'false', 'general', '2026-10-09 14:31:04', '2026-10-09 14:31:04');
INSERT INTO `settings` (`id`, `key`, `value`, `group`, `created_at`, `updated_at`) VALUES ('7', 'auto_alpha_cutoff', '12:00', 'general', '2026-10-09 14:31:04', '2026-10-09 14:31:04');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','pegawai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `phone`, `avatar`, `remember_token`, `created_at`, `updated_at`) VALUES ('2', 'Administrator YABAT', 'admin@stkip-us.ac.id', NULL, '$2y$10$AWpKz.S/yXcqYsKp73qjTexNfi80vYQtw1lWBb9ij6nfRn6WCJEqe', 'admin', '0812-6482-9901', NULL, NULL, '2026-10-09 15:27:36', '2026-10-09 15:40:12');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `phone`, `avatar`, `remember_token`, `created_at`, `updated_at`) VALUES ('3', 'Dr. Rahmat Hidayat, M.Pd', 'rahmat@stkip-usmansafri.ac.id', NULL, '$2y$10$Zl8eY9hRcTASWD45esNhcOvkuEtZ9nhI90dkJ3y5zL9zbppbx1t/S', 'pegawai', '0812-6482-9901', NULL, NULL, '2026-10-09 15:40:12', '2026-10-09 15:40:12');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `phone`, `avatar`, `remember_token`, `created_at`, `updated_at`) VALUES ('4', 'Nurhalimah, S.Pd., M.Hum', 'nurhalimah@stkip-usmansafri.ac.id', NULL, '$2y$10$L8m391tNXEG23OPLO78H.eGquKCYvMJJHRRkJ3jjWqRnSHZWjRH5C', 'pegawai', '0852-9923-4412', NULL, NULL, '2026-10-09 15:40:12', '2026-10-09 15:40:12');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `phone`, `avatar`, `remember_token`, `created_at`, `updated_at`) VALUES ('5', 'Ust. Faisal Akbar, Lc', 'faisal@thawalib.sch.id', NULL, '$2y$10$61k06jTU0qHIeaUDZV0nGOPRjeHBFsNVrSoZfUYpnRW86vHgbUp.i', 'pegawai', '0821-7734-1189', NULL, NULL, '2026-10-09 15:40:12', '2026-10-09 15:40:12');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `phone`, `avatar`, `remember_token`, `created_at`, `updated_at`) VALUES ('6', 'Ustazah Siti Sarah, S.Ag', 'siti.sarah@thawalib.sch.id', NULL, '$2y$10$XQ6.xCl2OEMb/I13syEXTe0NnEQmauiB26Bb4zztM6HEJWdmqrHZW', 'pegawai', '0813-9002-3312', NULL, NULL, '2026-10-09 15:40:12', '2026-10-09 15:40:12');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `phone`, `avatar`, `remember_token`, `created_at`, `updated_at`) VALUES ('7', 'Budi Pratama, S.Kom, Gr.', 'budi@smkanakbangsa.sch.id', NULL, '$2y$10$phUhEFgg0hVsSWhpnOZ/xOVWghOmMuqAqE14w6lgt0vQ7B5678xFO', 'pegawai', '0853-6112-7880', NULL, NULL, '2026-10-09 15:40:12', '2026-10-09 15:40:12');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `phone`, `avatar`, `remember_token`, `created_at`, `updated_at`) VALUES ('8', 'Cut Mutia, S.Pd', 'cutmutia@smkanakbangsa.sch.id', NULL, '$2y$10$H7Eyf9.dJpKI8UipKM.GJeuhPgp9TXOBIs20Bz9eFqlozk/NcYc2a', 'pegawai', '0822-4412-9988', NULL, NULL, '2026-10-09 15:40:12', '2026-10-09 15:40:12');

DROP TABLE IF EXISTS `work_schedules`;
CREATE TABLE `work_schedules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `day_of_week` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time_in` time NOT NULL,
  `time_out` time NOT NULL,
  `late_tolerance_minutes` int NOT NULL DEFAULT '15',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `work_schedules_institution_id_foreign` (`institution_id`),
  CONSTRAINT `work_schedules_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `work_schedules` (`id`, `institution_id`, `name`, `day_of_week`, `time_in`, `time_out`, `late_tolerance_minutes`, `is_active`, `created_at`, `updated_at`) VALUES ('1', '1', 'Jadwal Reguler STKIP', 'Senin - Sabtu', '07:45:00', '16:00:00', '15', '1', '2026-10-09 14:31:04', '2026-10-09 14:31:04');
INSERT INTO `work_schedules` (`id`, `institution_id`, `name`, `day_of_week`, `time_in`, `time_out`, `late_tolerance_minutes`, `is_active`, `created_at`, `updated_at`) VALUES ('2', '2', 'Shif Asrama Thawalib', 'Setiap Hari', '07:00:00', '15:30:00', '10', '1', '2026-10-09 14:31:04', '2026-10-09 14:31:04');
INSERT INTO `work_schedules` (`id`, `institution_id`, `name`, `day_of_week`, `time_in`, `time_out`, `late_tolerance_minutes`, `is_active`, `created_at`, `updated_at`) VALUES ('3', '3', 'Guru SMK Full Day', 'Senin - Jumat', '07:30:00', '16:15:00', '15', '1', '2026-10-09 14:31:04', '2026-10-09 14:31:04');

SET FOREIGN_KEY_CHECKS=1;
