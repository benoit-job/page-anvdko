<?php
/**
 * Hachage et vérification des mots de passe (bcrypt).
 * Compatibilité : anciens mots de passe en clair (non reconnus par password_get_info).
 */

if (!function_exists('anvdko_password_hash')) {
    function anvdko_password_hash(string $plain): string
    {
        return password_hash($plain, PASSWORD_DEFAULT);
    }

    function anvdko_stored_password_is_hashed(?string $stored): bool
    {
        if ($stored === null || $stored === '') {
            return false;
        }
        $info = password_get_info($stored);
        return isset($info['algo']) && (int) $info['algo'] !== 0;
    }

    /**
     * @param string $plain
     * @param string|null $stored valeur en base (hash bcrypt ou ancien clair)
     */
    function anvdko_password_verify(string $plain, ?string $stored): bool
    {
        if ($stored === null || $stored === '') {
            return false;
        }
        if (anvdko_stored_password_is_hashed($stored)) {
            return password_verify($plain, $stored);
        }
        return hash_equals((string) $stored, $plain);
    }

    /**
     * Après login réussi avec mot de passe legacy, migrer vers bcrypt.
     */
    function anvdko_password_maybe_upgrade_mysqli($mysqli, string $table, string $idColumn, int $id, string $plain, string $stored): void
    {
        if (anvdko_stored_password_is_hashed($stored)) {
            return;
        }
        if (!hash_equals((string) $stored, $plain)) {
            return;
        }
        $hash = anvdko_password_hash($plain);
        $id = (int) $id;
        $t = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        $c = preg_replace('/[^a-zA-Z0-9_]/', '', $idColumn);
        if ($t === '' || $c === '') {
            return;
        }
        $stmt = mysqli_prepare($mysqli, "UPDATE `$t` SET password = ? WHERE `$c` = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'si', $hash, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}
