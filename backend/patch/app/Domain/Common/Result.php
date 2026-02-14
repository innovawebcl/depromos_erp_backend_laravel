
<?php

namespace App\Domain\Common;

class Result
{
    public function __construct(
        public readonly bool $ok,
        public readonly mixed $data = null,
        public readonly ?string $error = null,
        public readonly int $code = 400
    ) {}

    public static function ok(mixed $data = null): self
    {
        return new self(true, $data, null, 200);
    }

    public static function fail(string $message, int $code = 400): self
    {
        return new self(false, null, $message, $code);
    }
}
