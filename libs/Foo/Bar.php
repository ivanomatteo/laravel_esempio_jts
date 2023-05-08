<?php

namespace Foo;

class Bar
{
    public string $created_at;

    public function __construct(public string $arg)
    {
        $this->created_at = now()->format('Y-m-d H:i:s');
    }
}
