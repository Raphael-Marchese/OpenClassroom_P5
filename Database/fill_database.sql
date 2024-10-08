-- Création des utilisateurs
INSERT INTO `user` (`first_name`, `last_name`, `username`, `email`, `password`, `role`)
    #plainPassword = test
VALUES
    ('Admin', 'Admin', 'admin', 'admin@example.com', '$2y$10$ZRmhB4oIQxlGjHZyHes14OpYZ3ZBLgDNvtGv90/zKModVsSRi2OFm', 'ROLE_ADMIN'),
    ('Utilisateur', 'Normal', 'user', 'user@example.com', '$2y$10$ZRmhB4oIQxlGjHZyHes14OpYZ3ZBLgDNvtGv90/zKModVsSRi2OFm', 'ROLE_USER');

-- Création des articles de blog
INSERT INTO `blog_post` (`title`, `chapo`, `created_at`, `updated_at`, `content`, `status`, `author`)
VALUES
    ('Titre article 1', 'Chapo article 1', DATE_ADD(NOW(), INTERVAL -10 DAY), NOW(), 'Contenu de l\'article 1', 'published', 1),
    ('Titre article 2', 'Chapo article 2', DATE_ADD(NOW(), INTERVAL -9 DAY), NOW(), 'Contenu de l\'article 2', 'published', 1),
    ('Titre article 3', 'Chapo article 3', DATE_ADD(NOW(), INTERVAL -8 DAY), NOW(), 'Contenu de l\'article 3', 'published', 1),
    ('Titre article 4', 'Chapo article 4', DATE_ADD(NOW(), INTERVAL -7 DAY), NOW(), 'Contenu de l\'article 4', 'published', 1),
    ('Titre article 5', 'Chapo article 5', DATE_ADD(NOW(), INTERVAL -6 DAY), NOW(), 'Contenu de l\'article 5', 'draft', 1),
    ('Titre article 6', 'Chapo article 6', DATE_ADD(NOW(), INTERVAL -5 DAY), NOW(), 'Contenu de l\'article 6', 'published', 1),
    ('Titre article 7', 'Chapo article 7', DATE_ADD(NOW(), INTERVAL -4 DAY), NOW(), 'Contenu de l\'article 7', 'published', 1),
    ('Titre article 8', 'Chapo article 8', DATE_ADD(NOW(), INTERVAL -3 DAY), NOW(), 'Contenu de l\'article 8', 'draft', 1),
    ('Titre article 9', 'Chapo article 9', DATE_ADD(NOW(), INTERVAL -2 DAY), NOW(), 'Contenu de l\'article 9', 'draft', 1),
    ('Titre article 10', 'Chapo article 10', DATE_ADD(NOW(), INTERVAL -1 DAY), NOW(), 'Contenu de l\'article 10', 'draft', 1);

-- Commentaires sur chaque article par l'utilisateur classique
INSERT INTO `comment` (`content`, `created_at`, `updated_at`, `status`,`author`, `post`)
SELECT
    CONCAT('Commentaire sur l\'article ', bp.id, ' par l\'utilisateur normal'),
    DATE_ADD(NOW(), INTERVAL -RAND() * 10 DAY), NOW(),
    CASE
        WHEN bp.id % 2 = 0 THEN 'published'
        ELSE 'pending'
        END,
    2, bp.id
FROM
    `blog_post` bp;
