<?php
namespace App\Helpers;

/**
 * Strong-password validation utilities.
 */
class SenhaHelper
{
    /**
     * Validate password against the strong-password policy:
     *   - min 8 characters
     *   - at least 1 uppercase letter
     *   - at least 1 lowercase letter
     *   - at least 1 digit
     *   - at least 1 special character  (!@#$%^&*()-_=+[]{}|;':",.<>?/`~)
     *
     * @return array ['valid' => bool, 'erro' => string|null]
     */
    public static function validar(string $senha): array
    {
        if (strlen($senha) < 8) {
            return ['valid' => false, 'erro' => 'A senha deve ter no mínimo 8 caracteres.'];
        }
        if (!preg_match('/[A-Z]/', $senha)) {
            return ['valid' => false, 'erro' => 'A senha deve conter pelo menos uma letra maiúscula.'];
        }
        if (!preg_match('/[a-z]/', $senha)) {
            return ['valid' => false, 'erro' => 'A senha deve conter pelo menos uma letra minúscula.'];
        }
        if (!preg_match('/[0-9]/', $senha)) {
            return ['valid' => false, 'erro' => 'A senha deve conter pelo menos um número.'];
        }
        if (!preg_match('/[^A-Za-z0-9]/', $senha)) {
            return ['valid' => false, 'erro' => 'A senha deve conter pelo menos um caractere especial (!@#$%...).'];
        }
        return ['valid' => true, 'erro' => null];
    }

    /**
     * Score 0-4 (for the UI strength bar).
     */
    public static function pontuacao(string $senha): int
    {
        $score = 0;
        if (strlen($senha) >= 8)             $score++;
        if (preg_match('/[A-Z]/', $senha))   $score++;
        if (preg_match('/[0-9]/', $senha))   $score++;
        if (preg_match('/[^A-Za-z0-9]/', $senha)) $score++;
        return $score;
    }
}
