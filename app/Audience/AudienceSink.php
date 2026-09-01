<?php

namespace App\Audience;

interface AudienceSink
{
    /**
     * @param  array<string, string|null>  $event
     */
    public function record(array $event): void;
}
