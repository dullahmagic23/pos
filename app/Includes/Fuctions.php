<?php
function formatNumber($number) {
    return str_pad($number, 5, '0', STR_PAD_LEFT);
}

function formatShillings($amount)
{
    $formatter = new NumberFormatter('sw_TZ', NumberFormatter::CURRENCY);
    return $formatter->formatCurrency($amount, 'TZS');
}
