<?php

namespace App\Validation;

/**
 * Regras de validação de documentos brasileiros.
 *
 * Diferente de "numeric|exact_length", estas regras conferem o dígito
 * verificador, então CPFs/CNPJs inventados (ex.: 111.111.111-11) são rejeitados.
 * O valor pode chegar com ou sem pontuação.
 *
 * O CNPJ aceita o novo formato alfanumérico (12 caracteres + 2 dígitos
 * verificadores), válido desde julho de 2026.
 */
class DocumentoRules
{
    /**
     * Valida um CPF pelo dígito verificador.
     */
    public function cpf_valido($valor, ?string &$error = null): bool
    {
        if ($this->cpfValido($valor)) {
            return true;
        }

        $error = 'CPF inválido. Confira os números digitados.';

        return false;
    }

    /**
     * Valida um CNPJ pelo dígito verificador.
     */
    public function cnpj_valido($valor, ?string &$error = null): bool
    {
        if ($this->cnpjValido($valor)) {
            return true;
        }

        $error = 'CNPJ inválido. Confira os dados digitados.';

        return false;
    }

    private function cpfValido($valor): bool
    {
        $cpf = preg_replace('/\D/', '', (string) $valor);

        // Precisa ter 11 dígitos e não pode ser uma sequência repetida.
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf) === 1) {
            return false;
        }

        // Confere os dois dígitos verificadores.
        for ($posicao = 9; $posicao < 11; $posicao++) {
            $soma = 0;

            for ($i = 0; $i < $posicao; $i++) {
                $soma += (int) $cpf[$i] * (($posicao + 1) - $i);
            }

            $digito = (($soma * 10) % 11) % 10;

            if ((int) $cpf[$posicao] !== $digito) {
                return false;
            }
        }

        return true;
    }

    private function cnpjValido($valor): bool
    {
        helper('formatacao');

        $cnpj = normalizar_cnpj($valor);

        // 12 caracteres alfanuméricos + 2 dígitos verificadores (sempre numéricos).
        if (preg_match('/^[A-Z0-9]{12}\d{2}$/', $cnpj) !== 1) {
            return false;
        }

        // Um CNPJ real não tem a raiz toda igual (ex.: 00000000000000).
        if (preg_match('/^(.)\1{11}/', $cnpj) === 1) {
            return false;
        }

        $pesosPorDigito = [
            [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2],
            [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2],
        ];

        // Confere os dois dígitos verificadores.
        foreach ($pesosPorDigito as $indice => $pesos) {
            $soma = 0;

            foreach ($pesos as $i => $peso) {
                $soma += $this->valorDoCaractere($cnpj[$i]) * $peso;
            }

            $resto  = $soma % 11;
            $digito = $resto < 2 ? 0 : 11 - $resto;

            if ((int) $cnpj[12 + $indice] !== $digito) {
                return false;
            }
        }

        return true;
    }

    /**
     * Valor numérico de cada caractere no cálculo do DV: código ASCII - 48.
     * Assim "0".."9" valem 0..9 e "A".."Z" valem 17..42.
     */
    private function valorDoCaractere(string $caractere): int
    {
        return ord($caractere) - 48;
    }
}
