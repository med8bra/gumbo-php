# GumboPHP

GumboPHP is low-level extension for Gumbo HTML5 Parser (https://github.com/google/gumbo-parser).

Main idea is use Gumbo Parser for building DOM Document (http://php.net/manual/en/class.domdocument.php) instead of PHP's `DOMDocument::load()`. PHP's built-in loader has many problems with loading HTML5 pages and pages with inline JavaScript.

__NOT ready for production.__

# Installation

```bash
git clone https://github.com/layershifter/GumboPHP.git
cd gumbo
phpize
./configure
make
make install
```

This will build a 'gumbo.so' shared extension, load it in php.ini using:

```ini
[gumbo]
extension = gumbo.so
```

#License

Copyright (c) 2016 Alexander Fedyashov. Licensed under the Apache License.
