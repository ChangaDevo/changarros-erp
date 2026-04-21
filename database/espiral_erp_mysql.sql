-- ============================================================
--  ESPIRAL ERP — Schema completo para MySQL
--  Ejecutar en orden en tu base de datos del servidor
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- USERS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`              VARCHAR(255) NOT NULL,
  `email`             VARCHAR(255) NOT NULL UNIQUE,
  `email_verified_at` TIMESTAMP NULL,
  `foto_perfil`       VARCHAR(255) NULL,
  `telefono`          VARCHAR(30) NULL,
  `cargo`             VARCHAR(100) NULL,
  `bio`               TEXT NULL,
  `role`              ENUM('admin','client','superadmin') NOT NULL DEFAULT 'admin',
  `cliente_id`        BIGINT UNSIGNED NULL,
  `activo`            TINYINT(1) NOT NULL DEFAULT 1,
  `password`          VARCHAR(255) NOT NULL,
  `remember_token`    VARCHAR(100) NULL,
  `created_at`        TIMESTAMP NULL,
  `updated_at`        TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- PASSWORD RESET TOKENS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email`      VARCHAR(255) NOT NULL,
  `token`      VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- SESSIONS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessions` (
  `id`            VARCHAR(255) NOT NULL,
  `user_id`       BIGINT UNSIGNED NULL,
  `ip_address`    VARCHAR(45) NULL,
  `user_agent`    TEXT NULL,
  `payload`       LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `sessions_user_id_index` (`user_id`),
  INDEX `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- CACHE
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cache` (
  `key`        VARCHAR(255) NOT NULL,
  `value`      MEDIUMTEXT NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key`        VARCHAR(255) NOT NULL,
  `owner`      VARCHAR(255) NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- JOBS / QUEUE
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `jobs` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue`        VARCHAR(255) NOT NULL,
  `payload`      LONGTEXT NOT NULL,
  `attempts`     TINYINT UNSIGNED NOT NULL,
  `reserved_at`  INT UNSIGNED NULL,
  `available_at` INT UNSIGNED NOT NULL,
  `created_at`   INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `job_batches` (
  `id`             VARCHAR(255) NOT NULL,
  `name`           VARCHAR(255) NOT NULL,
  `total_jobs`     INT NOT NULL,
  `pending_jobs`   INT NOT NULL,
  `failed_jobs`    INT NOT NULL,
  `failed_job_ids` LONGTEXT NOT NULL,
  `options`        MEDIUMTEXT NULL,
  `cancelled_at`   INT NULL,
  `created_at`     INT NOT NULL,
  `finished_at`    INT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid`       VARCHAR(255) NOT NULL UNIQUE,
  `connection` TEXT NOT NULL,
  `queue`      TEXT NOT NULL,
  `payload`    LONGTEXT NOT NULL,
  `exception`  LONGTEXT NOT NULL,
  `failed_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- CLIENTES
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `clientes` (
  `id`                        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre_empresa`            VARCHAR(255) NOT NULL,
  `nombre_contacto`           VARCHAR(255) NOT NULL,
  `email`                     VARCHAR(255) NOT NULL,
  `telefono`                  VARCHAR(255) NULL,
  `rfc`                       VARCHAR(255) NULL,
  `direccion`                 TEXT NULL,
  `notas`                     TEXT NULL,
  `activo`                    TINYINT(1) NOT NULL DEFAULT 1,
  `es_cliente_interno`        TINYINT(1) NOT NULL DEFAULT 0,
  `dias_minimos_publicacion`  TINYINT UNSIGNED NOT NULL DEFAULT 2,
  `creado_por`                BIGINT UNSIGNED NULL,
  `created_at`                TIMESTAMP NULL,
  `updated_at`                TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_clientes_creado_por` FOREIGN KEY (`creado_por`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- PROYECTOS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `proyectos` (
  `id`                      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cliente_id`              BIGINT UNSIGNED NOT NULL,
  `nombre`                  VARCHAR(255) NOT NULL,
  `descripcion`             TEXT NULL,
  `estado`                  ENUM('cotizando','en_desarrollo','en_revision','aprobado','finalizado') NOT NULL DEFAULT 'cotizando',
  `monto_total`             DECIMAL(10,2) NULL,
  `fecha_inicio`            DATE NULL,
  `fecha_entrega_estimada`  DATE NULL,
  `fecha_entrega_real`      DATE NULL,
  `notas`                   TEXT NULL,
  `creado_por`              BIGINT UNSIGNED NOT NULL,
  `carpeta_drive`           VARCHAR(255) NULL,
  `created_at`              TIMESTAMP NULL,
  `updated_at`              TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_proyectos_cliente`    FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_proyectos_creado_por` FOREIGN KEY (`creado_por`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- PROYECTO_USUARIOS (compartir proyectos entre admins)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `proyecto_usuarios` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `proyecto_id` BIGINT UNSIGNED NOT NULL,
  `user_id`     BIGINT UNSIGNED NOT NULL,
  `rol`         VARCHAR(255) NOT NULL DEFAULT 'colaborador',
  `created_at`  TIMESTAMP NULL,
  `updated_at`  TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `proyecto_usuarios_unique` (`proyecto_id`, `user_id`),
  CONSTRAINT `fk_pu_proyecto` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pu_user`     FOREIGN KEY (`user_id`)     REFERENCES `users`(`id`)     ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- DOCUMENTOS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `documentos` (
  `id`                      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `proyecto_id`             BIGINT UNSIGNED NOT NULL,
  `nombre`                  VARCHAR(255) NOT NULL,
  `tipo`                    ENUM('contrato','cotizacion','avance','entrega','otro') NOT NULL DEFAULT 'otro',
  `archivo_path`            VARCHAR(255) NOT NULL,
  `archivo_nombre_original` VARCHAR(255) NOT NULL,
  `archivo_mime`            VARCHAR(255) NULL,
  `archivo_tamanio`         BIGINT UNSIGNED NULL,
  `estado`                  ENUM('borrador','enviado','aprobado','sellado') NOT NULL DEFAULT 'borrador',
  `es_sellado`              TINYINT(1) NOT NULL DEFAULT 0,
  `sellado_at`              TIMESTAMP NULL,
  `sellado_por`             BIGINT UNSIGNED NULL,
  `subido_por`              BIGINT UNSIGNED NOT NULL,
  `visible_cliente`         TINYINT(1) NOT NULL DEFAULT 0,
  `notas`                   TEXT NULL,
  `created_at`              TIMESTAMP NULL,
  `updated_at`              TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_doc_proyecto`    FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_doc_sellado_por` FOREIGN KEY (`sellado_por`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_doc_subido_por`  FOREIGN KEY (`subido_por`)  REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- APROBACIONES
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `aprobaciones` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `documento_id` BIGINT UNSIGNED NOT NULL,
  `usuario_id`  BIGINT UNSIGNED NOT NULL,
  `accion`      ENUM('aprobado','rechazado','cambios_solicitados') NOT NULL,
  `comentario`  TEXT NULL,
  `ip_address`  VARCHAR(255) NULL,
  `user_agent`  VARCHAR(255) NULL,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_apr_documento` FOREIGN KEY (`documento_id`) REFERENCES `documentos`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_apr_usuario`   FOREIGN KEY (`usuario_id`)   REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- ENTREGAS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `entregas` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `proyecto_id`   BIGINT UNSIGNED NOT NULL,
  `titulo`        VARCHAR(255) NOT NULL,
  `descripcion`   TEXT NULL,
  `tipo`          ENUM('diseno_inicial','avance','revision','entrega_final') NOT NULL DEFAULT 'avance',
  `estado`        ENUM('pendiente','enviado','aprobado','rechazado','cambios_solicitados') NOT NULL DEFAULT 'pendiente',
  `fecha_entrega` DATE NULL,
  `notas_cliente` TEXT NULL,
  `orden`         INT NOT NULL DEFAULT 0,
  `created_at`    TIMESTAMP NULL,
  `updated_at`    TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_ent_proyecto` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- ARCHIVOS_ENTREGA
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `archivos_entrega` (
  `id`                      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `entrega_id`              BIGINT UNSIGNED NOT NULL,
  `nombre`                  VARCHAR(255) NOT NULL,
  `archivo_path`            VARCHAR(255) NOT NULL,
  `archivo_nombre_original` VARCHAR(255) NOT NULL,
  `tipo_archivo`            ENUM('pdf','imagen','video_url','video_archivo','otro') NOT NULL DEFAULT 'otro',
  `video_url`               VARCHAR(255) NULL,
  `archivo_tamanio`         BIGINT UNSIGNED NULL,
  `created_at`              TIMESTAMP NULL,
  `updated_at`              TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_ae_entrega` FOREIGN KEY (`entrega_id`) REFERENCES `entregas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- PAGOS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pagos` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `proyecto_id`       BIGINT UNSIGNED NOT NULL,
  `entrega_id`        BIGINT UNSIGNED NULL,
  `concepto`          VARCHAR(255) NOT NULL,
  `monto`             DECIMAL(10,2) NOT NULL,
  `estado`            ENUM('pendiente','pagado','vencido','cancelado') NOT NULL DEFAULT 'pendiente',
  `fecha_vencimiento` DATE NULL,
  `fecha_pago`        TIMESTAMP NULL,
  `metodo_pago`       VARCHAR(255) NULL,
  `referencia_codi`   VARCHAR(255) NULL,
  `qr_codigo_path`    VARCHAR(255) NULL,
  `comprobante_path`  VARCHAR(255) NULL,
  `notas`             TEXT NULL,
  `created_at`        TIMESTAMP NULL,
  `updated_at`        TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_pagos_proyecto` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pagos_entrega`  FOREIGN KEY (`entrega_id`)  REFERENCES `entregas`(`id`)  ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- ACTIVIDAD_LOG
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `actividad_log` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     BIGINT UNSIGNED NULL,
  `accion`      VARCHAR(255) NOT NULL,
  `modelo_tipo` VARCHAR(255) NULL,
  `modelo_id`   BIGINT UNSIGNED NULL,
  `descripcion` TEXT NULL,
  `ip_address`  VARCHAR(255) NULL,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_al_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- COTIZACIONES
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cotizaciones` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cliente_id`       BIGINT UNSIGNED NOT NULL,
  `proyecto_id`      BIGINT UNSIGNED NULL,
  `nombre`           VARCHAR(255) NOT NULL,
  `estado`           ENUM('borrador','enviada','vista','aprobada','rechazada','vencida') NOT NULL DEFAULT 'borrador',
  `iva_porcentaje`   DECIMAL(5,2) NOT NULL DEFAULT 16.00,
  `subtotal`         DECIMAL(12,2) NOT NULL DEFAULT 0,
  `iva_monto`        DECIMAL(12,2) NOT NULL DEFAULT 0,
  `total`            DECIMAL(12,2) NOT NULL DEFAULT 0,
  `notas`            TEXT NULL,
  `token`            VARCHAR(64) NOT NULL UNIQUE,
  `fecha_vencimiento` DATE NULL,
  `visto_at`         TIMESTAMP NULL,
  `aprobado_at`      TIMESTAMP NULL,
  `aprobado_ip`      VARCHAR(255) NULL,
  `aprobado_nombre`  VARCHAR(255) NULL,
  `rechazado_at`     TIMESTAMP NULL,
  `razon_rechazo`    TEXT NULL,
  `creado_por`       BIGINT UNSIGNED NOT NULL,
  `created_at`       TIMESTAMP NULL,
  `updated_at`       TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_cot_cliente`    FOREIGN KEY (`cliente_id`)  REFERENCES `clientes`(`id`)   ON DELETE CASCADE,
  CONSTRAINT `fk_cot_proyecto`   FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos`(`id`)  ON DELETE SET NULL,
  CONSTRAINT `fk_cot_creado_por` FOREIGN KEY (`creado_por`)  REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- COTIZACION_ITEMS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cotizacion_items` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cotizacion_id`   BIGINT UNSIGNED NOT NULL,
  `descripcion`     VARCHAR(255) NOT NULL,
  `cantidad`        DECIMAL(10,2) NOT NULL DEFAULT 1,
  `precio_unitario` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `total`           DECIMAL(12,2) NOT NULL DEFAULT 0,
  `orden`           INT NOT NULL DEFAULT 0,
  `created_at`      TIMESTAMP NULL,
  `updated_at`      TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_ci_cotizacion` FOREIGN KEY (`cotizacion_id`) REFERENCES `cotizaciones`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- PUBLICACIONES
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `publicaciones` (
  `id`                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cliente_id`         BIGINT UNSIGNED NOT NULL,
  `proyecto_id`        BIGINT UNSIGNED NULL,
  `created_by`         BIGINT UNSIGNED NOT NULL,
  `red_social`         VARCHAR(255) NOT NULL,
  `titulo`             VARCHAR(255) NOT NULL,
  `descripcion`        TEXT NOT NULL,
  `archivo_path`       VARCHAR(255) NULL,
  `fecha_programada`   DATETIME NOT NULL,
  `estado`             VARCHAR(255) NOT NULL DEFAULT 'borrador',
  `nota_cliente`       TEXT NULL,
  `audiencia_sugerida` TEXT NULL,
  `error_publicacion`  TEXT NULL,
  `created_at`         TIMESTAMP NULL,
  `updated_at`         TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_pub_cliente`     FOREIGN KEY (`cliente_id`)  REFERENCES `clientes`(`id`)  ON DELETE CASCADE,
  CONSTRAINT `fk_pub_proyecto`    FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_pub_created_by`  FOREIGN KEY (`created_by`)  REFERENCES `users`(`id`)     ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- META_TOKENS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `meta_tokens` (
  `id`                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cliente_id`            BIGINT UNSIGNED NOT NULL,
  `nombre`                VARCHAR(255) NOT NULL,
  `plataforma`            ENUM('facebook','instagram') NOT NULL,
  `page_id`               VARCHAR(255) NOT NULL,
  `ig_account_id`         VARCHAR(255) NULL,
  `access_token`          TEXT NOT NULL,
  `activo`                TINYINT(1) NOT NULL DEFAULT 1,
  `expires_at`            TIMESTAMP NULL,
  `ultima_verificacion`   TIMESTAMP NULL,
  `estado_verificacion`   VARCHAR(255) NULL,
  `created_at`            TIMESTAMP NULL,
  `updated_at`            TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_mt_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- BRIEFS_CREATIVOS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `briefs_creativos` (
  `id`                     BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `proyecto_id`            BIGINT UNSIGNED NOT NULL UNIQUE,
  `objetivo_campana`       TEXT NULL,
  `publico_objetivo`       TEXT NULL,
  `tono_voz`               VARCHAR(255) NULL,
  `colores_marca`          VARCHAR(255) NULL,
  `competencia`            TEXT NULL,
  `referencias`            TEXT NULL,
  `entregables_esperados`  TEXT NULL,
  `presupuesto_referencial` DECIMAL(12,2) NULL,
  `observaciones`          TEXT NULL,
  `creado_por`             BIGINT UNSIGNED NULL,
  `actualizado_por`        BIGINT UNSIGNED NULL,
  `created_at`             TIMESTAMP NULL,
  `updated_at`             TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_bc_proyecto`       FOREIGN KEY (`proyecto_id`)   REFERENCES `proyectos`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_bc_creado_por`     FOREIGN KEY (`creado_por`)    REFERENCES `users`(`id`)     ON DELETE SET NULL,
  CONSTRAINT `fk_bc_actualizado_por` FOREIGN KEY (`actualizado_por`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- COMENTARIOS (polimórfico)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `comentarios` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `comentable_type`  VARCHAR(255) NOT NULL,
  `comentable_id`    BIGINT UNSIGNED NOT NULL,
  `user_id`          BIGINT UNSIGNED NOT NULL,
  `contenido`        TEXT NOT NULL,
  `created_at`       TIMESTAMP NULL,
  `updated_at`       TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  INDEX `comentarios_comentable_index` (`comentable_type`, `comentable_id`),
  CONSTRAINT `fk_com_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- PLANTILLAS_COTIZACION
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `plantillas_cotizacion` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre`      VARCHAR(255) NOT NULL,
  `descripcion` TEXT NULL,
  `creado_por`  BIGINT UNSIGNED NULL,
  `created_at`  TIMESTAMP NULL,
  `updated_at`  TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_pc_creado_por` FOREIGN KEY (`creado_por`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `plantilla_cotizacion_items` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `plantilla_id`    BIGINT UNSIGNED NOT NULL,
  `descripcion`     VARCHAR(255) NOT NULL,
  `cantidad`        DECIMAL(10,2) NOT NULL DEFAULT 1,
  `precio_unitario` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `orden`           INT NOT NULL DEFAULT 0,
  `created_at`      TIMESTAMP NULL,
  `updated_at`      TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_pci_plantilla` FOREIGN KEY (`plantilla_id`) REFERENCES `plantillas_cotizacion`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- NOTIFICACIONES (polimórfico)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `notificaciones` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`          BIGINT UNSIGNED NULL,
  `cliente_id`       BIGINT UNSIGNED NULL,
  `tipo`             VARCHAR(255) NOT NULL,
  `titulo`           VARCHAR(255) NOT NULL,
  `mensaje`          TEXT NULL,
  `url`              VARCHAR(255) NULL,
  `leida_at`         TIMESTAMP NULL,
  `notificable_type` VARCHAR(255) NOT NULL,
  `notificable_id`   BIGINT UNSIGNED NOT NULL,
  `created_at`       TIMESTAMP NULL,
  `updated_at`       TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  INDEX `notificaciones_notificable_index` (`notificable_type`, `notificable_id`),
  CONSTRAINT `fk_not_user`    FOREIGN KEY (`user_id`)    REFERENCES `users`(`id`)    ON DELETE CASCADE,
  CONSTRAINT `fk_not_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- MIGRATIONS (tabla de control de Laravel)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `migrations` (
  `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch`     INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Registrar todas las migraciones como ejecutadas
INSERT INTO `migrations` (`migration`, `batch`) VALUES
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2024_01_01_000001_add_fields_to_users_table', 1),
('2024_01_01_000002_create_clientes_table', 1),
('2024_01_01_000003_create_proyectos_table', 1),
('2024_01_01_000004_create_documentos_table', 1),
('2024_01_01_000005_create_aprobaciones_table', 1),
('2024_01_01_000006_create_entregas_table', 1),
('2024_01_01_000007_create_archivos_entrega_table', 1),
('2024_01_01_000008_create_pagos_table', 1),
('2024_01_01_000009_create_actividad_log_table', 1),
('2024_01_02_000001_create_cotizaciones_table', 1),
('2024_01_02_000002_create_cotizacion_items_table', 1),
('2024_01_03_000001_create_publicaciones_table', 1),
('2024_01_03_000002_update_publicaciones_add_ai_fields', 1),
('2024_01_03_000003_create_meta_tokens_table', 1),
('2024_01_04_000001_update_users_add_superadmin_role', 1),
('2024_01_05_000001_add_cliente_interno_to_clientes_table', 1),
('2024_01_05_000002_create_proyecto_usuarios_table', 1),
('2026_04_14_000001_add_carpeta_drive_to_proyectos_table', 1),
('2026_04_14_000002_create_briefs_creativos_table', 1),
('2026_04_14_000003_create_comentarios_table', 1),
('2026_04_14_000004_create_plantillas_cotizacion_table', 1),
('2026_04_14_000005_create_notificaciones_table', 1),
('2026_04_15_144751_add_profile_fields_to_users_table', 1),
('2026_04_21_170514_add_creado_por_to_clientes_table', 1);

-- ------------------------------------------------------------
-- SUPERADMIN INICIAL
-- ------------------------------------------------------------
INSERT INTO `users` (`name`, `email`, `password`, `role`, `activo`, `created_at`, `updated_at`)
VALUES (
  'Comunicación Participa Juárez',
  'comunicacion@espiraljuarez.com',
  '$2y$12$fVtzLT24seMXA2klvAKr1.TsBKsYi0C4BhDRf1avtNlX/I1gLrGby',  -- password: #Espiral1504
  'superadmin',
  1,
  NOW(),
  NOW()
);

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- Hash generado con bcrypt cost=12. Contraseña: #Espiral1504
-- ============================================================
