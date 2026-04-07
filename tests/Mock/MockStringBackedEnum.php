<?php
declare(strict_types=1);

namespace DR\Utils\Tests\Mock;

enum MockStringBackedEnum: string
{
    case Foo = 'foo';
    case Bar = 'bar';
}
