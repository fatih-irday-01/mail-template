<?php

if (! function_exists('isRtlLang')) {
    function isRtlLang(string $languageCode = null): bool {
        $languageCode ??= 'en';

        return in_array($languageCode, ['ar', 'he']);
    }
}
