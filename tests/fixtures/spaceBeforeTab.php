<?php

/**
 * Space before tab fixture files for vfsStream.
 */

declare(strict_types=1);

$structure = [
    'initial' => [
        'space-before-tab' => [],
    ],
    'fixed' => [
        'space-before-tab' => [],
    ],
];

$structure['initial']['space-before-tab']['spaceBeforeTab.php'] = "<?php\n\nclass Foo\n{\n  \tpublic function bar()\n\t{\n        \treturn true;\n    \t}\n}\n";
$structure['fixed']['space-before-tab']['spaceBeforeTab.php'] = "<?php\n\nclass Foo\n{\n\tpublic function bar()\n\t{\n\treturn true;\n\t}\n}\n";

return $structure;
