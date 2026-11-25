<?php

/**
 * Funções de formatação usadas nas Views e nos Controllers.
 * Os dados ficam salvos sem pontuação no banco e são formatados na tela.
 */

if (! function_exists('somente_numeros')) {
    function somente_numeros(?string $valor): string
    {
        return preg_replace('/\D/', '', (string) $valor);
    }
}

if (! function_exists('normalizar_cnpj')) {
    /**
     * Deixa o CNPJ no formato armazenado: sem pontuação e com letras maiúsculas.
     * Vale para o formato antigo (só números) e para o alfanumérico.
     */
    function normalizar_cnpj(?string $cnpj): string
    {
        return preg_replace('/[^A-Z0-9]/', '', strtoupper((string) $cnpj));
    }
}

if (! function_exists('formatar_cnpj')) {
    function formatar_cnpj(?string $cnpj): string
    {
        $n = normalizar_cnpj($cnpj);

        if (preg_match('/^[A-Z0-9]{12}\d{2}$/', $n) !== 1) {
            return (string) $cnpj;
        }

        return sprintf('%s.%s.%s/%s-%s', substr($n, 0, 2), substr($n, 2, 3), substr($n, 5, 3), substr($n, 8, 4), substr($n, 12, 2));
    }
}

if (! function_exists('formatar_cpf')) {
    function formatar_cpf(?string $cpf): string
    {
        $n = somente_numeros($cpf);

        if (strlen($n) !== 11) {
            return (string) $cpf;
        }

        return sprintf('%s.%s.%s-%s', substr($n, 0, 3), substr($n, 3, 3), substr($n, 6, 3), substr($n, 9, 2));
    }
}

if (! function_exists('formatar_telefone')) {
    function formatar_telefone(?string $telefone): string
    {
        $n = somente_numeros($telefone);

        if (strlen($n) === 11) {
            return sprintf('(%s) %s-%s', substr($n, 0, 2), substr($n, 2, 5), substr($n, 7, 4));
        }

        if (strlen($n) === 10) {
            return sprintf('(%s) %s-%s', substr($n, 0, 2), substr($n, 2, 4), substr($n, 6, 4));
        }

        return (string) $telefone;
    }
}
