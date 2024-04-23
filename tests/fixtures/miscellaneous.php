<?php

/**
 * Miscellaneous fixtures.
 */

declare(strict_types=1);

$structure = [
    'initial' => [
        'miscellaneous' => [],
    ],
    'fixed' => [
        'miscellaneous' => [],
    ],
];

$patch_1 = <<<'EOL'
diff --git a/README.md b/README.md
index 802d433..dde8419 100644
--- a/README.md
+++ b/README.md
@@ -1,7 +1,25 @@
 # Hello, World
 
-Lorem ipsum dolor sit amet.
+Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas pharetra dui
+euismod posuere fermentum. Etiam vitae neque ut justo venenatis interdum sit
+amet sed nulla. Etiam a auctor quam. Vivamus cursus augue purus, vitae euismod
+nunc porta nec. Praesent condimentum arcu ac facilisis sodales. In tincidunt
+pretium malesuada. Nunc ut erat quis dolor aliquet tristique in ut nisi.
+
+
+Suspendisse a ultrices neque. Pellentesque faucibus, sapien non lobortis
+venenatis, diam turpis eleifend orci, ut congue odio ligula a lectus. In
+finibus, quam non volutpat sagittis, arcu sem sodales ligula, a aliquam turpis
+urna eget sapien. 
 
 ```sh
 echo "Hello, World
 ```
+
+Proin congue, augue auctor maximus facilisis, libero purus bibendum dolor, non
+pulvinar sapien metus condimentum ante. Nulla rhoncus molestie condimentum.
+
+```Makefile
+build:
+	gcc		src.c		
+```
EOL;

$structure['initial']['miscellaneous']['file_1.patch'] = $patch_1;
$structure['fixed']['miscellaneous']['file_1.patch'] = $patch_1;

return $structure;
