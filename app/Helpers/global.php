<?php

use Carbon\Carbon;

function normalizeDate(?string $value)
{
    if (!$value) {
        return null;
    }

    return Carbon::createFromFormat(
        'M d Y h:i:s A',
        str_replace([':AM', ':PM'], [' AM', ' PM'], $value)
    )->format('F j, Y');
}