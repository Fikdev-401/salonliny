<?php

if (!function_exists('rupiah')) {
    function rupiah(float|int|string|null $amount, bool $withPrefix = true): string
    {
        $numericAmount = (float) ($amount ?? 0);
        $formatted = number_format(abs($numericAmount), 0, ',', '.');

        if ($numericAmount < 0) {
            return ($withPrefix ? '-Rp ' : '-') . $formatted;
        }

        return ($withPrefix ? 'Rp ' : '') . $formatted;
    }
}
