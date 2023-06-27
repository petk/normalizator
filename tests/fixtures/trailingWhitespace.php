<?php

/**
 * Trailing whitespace fixture files for vfsStream.
 */

declare(strict_types=1);

$structure = [
    'initial' => [
        'trailing-whitespace' => [],
    ],
    'fixed' => [
        'trailing-whitespace' => [],
    ],
];

// Add generic trailing whitespace file.
$structure['initial']['trailing-whitespace']['fileWithTrailingWhitespace.php'] = "\n\n<?php\n\n\nclass Foo {\n    public function bar()\n    { \n        \$foo = \"bar\"; \n\n        return \$foo;\n    }\n    \t\n     \n    \n    \n    public function baz()\n    {\n        return false;    \n    }\n\n\tpublic function foobar()  \n    {\t\n        return true;\t\n    }\n}\n\n\n";
$structure['fixed']['trailing-whitespace']['fileWithTrailingWhitespace.php'] = "\n\n<?php\n\n\nclass Foo {\n    public function bar()\n    {\n        \$foo = \"bar\";\n\n        return \$foo;\n    }\n\n\n\n\n    public function baz()\n    {\n        return false;\n    }\n\n\tpublic function foobar()\n    {\n        return true;\n    }\n}\n\n\n";

// Add no-break space.
$structure['initial']['trailing-whitespace']['no-break-space.txt'] = "\u{00A0}";
$structure['fixed']['trailing-whitespace']['no-break-space.txt'] = '';

// Add Mongolian vowel separator.
$structure['initial']['trailing-whitespace']['mongolian-vowel-separator.txt'] = "\u{180E}";
$structure['fixed']['trailing-whitespace']['mongolian-vowel-separator.txt'] = '';

// Add en quad.
$structure['initial']['trailing-whitespace']['en-quad.txt'] = "\u{2000}\n\u{2000}\u{2000}\n";
$structure['fixed']['trailing-whitespace']['en-quad.txt'] = "\n\n";

// Add em quad.
$structure['initial']['trailing-whitespace']['em-quad.txt'] = "\u{2001}\n\u{2001}\u{2001}\n";
$structure['fixed']['trailing-whitespace']['em-quad.txt'] = "\n\n";

// Add en space.
$structure['initial']['trailing-whitespace']['en-space.txt'] = "\u{2002}\n\u{2002}\u{2002}\n";
$structure['fixed']['trailing-whitespace']['en-space.txt'] = "\n\n";

// Add em space.
$structure['initial']['trailing-whitespace']['em-space.txt'] = "\u{2003}\n\u{2003}\u{2003}\n";
$structure['fixed']['trailing-whitespace']['em-space.txt'] = "\n\n";

// Add three-per-em space.
$structure['initial']['trailing-whitespace']['three-per-em-space.txt'] = "\u{2004}\n\u{2004}\u{2004}\n";
$structure['fixed']['trailing-whitespace']['three-per-em-space.txt'] = "\n\n";

// Add four-per-em space.
$structure['initial']['trailing-whitespace']['four-per-em-space.txt'] = "\u{2005}\n\u{2005}\u{2005}\n";
$structure['fixed']['trailing-whitespace']['four-per-em-space.txt'] = "\n\n";

// Add six-per-em space.
$structure['initial']['trailing-whitespace']['six-per-em-space.txt'] = "\u{2006}\n\u{2006}\u{2006}\n";
$structure['fixed']['trailing-whitespace']['six-per-em-space.txt'] = "\n\n";

// Add figure space.
$structure['initial']['trailing-whitespace']['figure-space.txt'] = "\u{2007}\n\u{2007}\u{2007}\n";
$structure['fixed']['trailing-whitespace']['figure-space.txt'] = "\n\n";

// Add punctuation space.
$structure['initial']['trailing-whitespace']['punctuation-space.txt'] = "\u{2008}\n\u{2008}\u{2008}\n";
$structure['fixed']['trailing-whitespace']['punctuation-space.txt'] = "\n\n";

// Add thin space.
$structure['initial']['trailing-whitespace']['thin-space.txt'] = "\u{2009}\n\u{2009}\u{2009}\n";
$structure['fixed']['trailing-whitespace']['thin-space.txt'] = "\n\n";

// Add hair space.
$structure['initial']['trailing-whitespace']['hair-space.txt'] = "\u{200A}\n\u{200A}\u{200A}\n";
$structure['fixed']['trailing-whitespace']['hair-space.txt'] = "\n\n";

// Add narrow no-break space.
$structure['initial']['trailing-whitespace']['narrow-no-break-space.txt'] = "\u{202F}\n\u{202F}\u{202F}\n";
$structure['fixed']['trailing-whitespace']['narrow-no-break-space.txt'] = "\n\n";

// Add medium mathematical space.
$structure['initial']['trailing-whitespace']['medium-mathematical-space.txt'] = "\u{205F}\n\u{205F}\u{205F}\n";
$structure['fixed']['trailing-whitespace']['medium-mathematical-space.txt'] = "\n\n";

// Add ideographic space.
$structure['initial']['trailing-whitespace']['ideographic-space.txt'] = "\u{3000}\n\u{3000}\u{3000}\n";
$structure['fixed']['trailing-whitespace']['ideographic-space.txt'] = "\n\n";

// Add zero width space.
$structure['initial']['trailing-whitespace']['zero-width-space.txt'] = "\u{200B}\n\u{200B}\u{200B}\n";
$structure['fixed']['trailing-whitespace']['zero-width-space.txt'] = "\n\n";

// Add zero width no-break space.
$structure['initial']['trailing-whitespace']['zero-width-no-break-space.txt'] = "\u{FEFF}\n\u{FEFF}\u{FEFF}\n";
$structure['fixed']['trailing-whitespace']['zero-width-no-break-space.txt'] = "\n\n";

// Whitespace various.
$structure['initial']['trailing-whitespace']['various.txt'] = "README\u{00A0}\u{200B}\u{202F}\u{FEFF}\u{180E}\u{3000}\u{205F}\u{2008}\n\nLorem ipsum\u{2009}\u{00A0}\u{200A}\u{2001}\ndolor sit amet  \n\n\u{00A0}\n\t\t \nfoobar\u{00A0}\u{180E}  \n\u{0080}\n";
$structure['fixed']['trailing-whitespace']['various.txt'] = "README\n\nLorem ipsum\ndolor sit amet\n\n\n\nfoobar\n\u{0080}\n";

// Whitespace various with CRLF.
$structure['initial']['trailing-whitespace']['various-crlf.txt'] = "README\u{00A0}\u{200B}\u{202F}\u{FEFF}\u{180E}\u{3000}\u{205F}\u{2008}\r\n\r\nLorem ipsum\u{2009}\u{00A0}\u{200A}\u{2001}\r\ndolor sit amet  \r\n\r\n\u{00A0}\r\n\t\t \r\nfoobar\u{00A0}\u{180E}  \r\n\u{0080}\r\n";
$structure['fixed']['trailing-whitespace']['various-crlf.txt'] = "README\r\n\r\nLorem ipsum\r\ndolor sit amet\r\n\r\n\r\n\r\nfoobar\r\n\u{0080}\r\n";

// Whitespace various with CR.
$structure['initial']['trailing-whitespace']['various-cr.txt'] = "README\u{00A0}\u{200B}\u{202F}\u{FEFF}\u{180E}\u{3000}\u{205F}\u{2008}\r\rLorem ipsum\u{2009}\u{00A0}\u{200A}\u{2001}\rdolor sit amet  \r\r\u{00A0}\r\t\t \rfoobar\u{00A0}\u{180E}  \r\u{0080}\r";
$structure['fixed']['trailing-whitespace']['various-cr.txt'] = "README\r\rLorem ipsum\rdolor sit amet\r\r\r\rfoobar\r\u{0080}\r";

// Whitespace various with mixed EOL.
$structure['initial']['trailing-whitespace']['various-mixed-eol.txt'] = "README\u{00A0}\u{200B}\u{202F}\u{FEFF}\u{180E}\u{3000}\u{205F}\u{2008}\r\r\n\nLorem ipsum\u{2009}\u{00A0}\u{200A}\u{2001}\n\n\rdolor sit amet  \r\n\r\u{00A0}\r\n\n\r\r\n\t\t \r\nfoobar\u{00A0}\u{180E}  \r\n\u{0080}\n";
$structure['fixed']['trailing-whitespace']['various-mixed-eol.txt'] = "README\r\r\n\nLorem ipsum\n\n\rdolor sit amet\r\n\r\r\n\n\r\r\n\r\nfoobar\r\n\u{0080}\n";

return $structure;
