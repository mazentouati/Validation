--CREDITS--
Henrique Moody <henriquemoody@gmail.com>
--FILE--
<?php
require 'vendor/autoload.php';

use Respect\Validation\Exceptions\OptionalException;
use Respect\Validation\Validator as v;

try {
    v::not(v::optional(v::equals('foo')))->check(null);
} catch (OptionalException $e) {
    echo $e->getMessage().PHP_EOL;
}
?>
--EXPECT--
The value must not be optional
