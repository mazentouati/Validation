--CREDITS--
Edson Lima <dddwebdeveloper@gmail.com>
Henrique Moody <henriquemoody@gmail.com>
--FILE--
<?php
require 'vendor/autoload.php';

use Respect\Validation\Validator as v;

$data = [
    'password' => '123',
    'invalid_passwords' => ['123', 'secreta'],
];

try {
    v::not(v::keyValue('password', 'in', 'invalid_passwords'))->check($data);
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
--EXPECT--
Key `{ "password": "123", "invalid_passwords": { "123", "secreta" } }` must not be present
