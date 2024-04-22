<?php

/**
 * Permissions fixture files for vfsStream.
 */

declare(strict_types=1);

$structure = [
    'initial' => [
        'permissions' => [],
    ],
    'fixed' => [
        'permissions' => [],
    ],
];

$structure['initial']['permissions']['Rakefile'] = <<<'EOL'
namespace :book do

  def check_contrib
  end

  desc 'build HTML format'
  task :build => 'book/contributors.txt' do
      check_contrib()

      puts 'Converting to HTML...'
  end
end

task :default => "book:build"
EOL;

$structure['initial']['permissions']['not-a-script.sh'] = "echo 'foobar';\n";

$structure['initial']['permissions']['php-script'] = <<<'EOL'
#!/usr/bin/env php
<?php

echo 'foobar';
EOL;

$structure['initial']['permissions']['shell-script'] = <<<'EOL'
#!/bin/sh

echo 'foobar';
EOL;

$structure['initial']['permissions']['shell-script_2'] = <<<'EOL'
#! /bin/sh

echo 'foobar';
EOL;

$structure['initial']['permissions']['shell-script_3'] = <<<EOL
#! \t \t/bin/bash\t

echo 'foobar';
EOL;

$structure['initial']['permissions']['shell-script_4'] = <<<EOL
#! \t \t/usr/bin/bash\t

echo 'foobar';
EOL;

$structure['initial']['permissions']['shell-script_5'] = <<<EOL
#! \t \t/usr/local/bin/foobar\t

echo 'foobar';
EOL;

$structure['initial']['permissions']['shell-script_6'] = <<<EOL
#! \t \t/usr/bin/env \t -S cmake -P

echo 'foobar';
EOL;

$structure['initial']['permissions']['shell-script_7'] = <<<EOL
#! \t \t/etc/

echo 'foobar';
EOL;

return $structure;
