<?php

namespace Rohama\Koin;

interface Redactable
{
    /**
     * Payload keys whose values are masked by redacted().
     */
    public function sensitiveKeys(): array;

    /**
     * Payload with sensitive values masked, safe to log.
     */
    public function redacted(): array;
}
