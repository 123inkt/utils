<?php
declare(strict_types=1);

namespace DR\Utils\Tests;

use DR\Utils\Arrays;

class Test
{
    public function foo(): void {

        $data = ['foo' => 'bar', 'foz' => 'baz', 'number' => 123];
        $result =  Arrays::removeTypes($data, ['string']);

    }

}
