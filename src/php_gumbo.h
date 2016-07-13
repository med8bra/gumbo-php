#ifndef PHP_GUMBO_H
#define PHP_GUMBO_H
#include "php.h"

// PHP: Extension constants and declarations.

PHP_MINIT_FUNCTION(gumbo);

#define PHP_GUMBO_EXTNAME "gumbo"
#define PHP_GUMBO_VERSION "0.1"

extern zend_module_entry gumbo_module_entry;
#define phpext_gumbo_ptr &gumbo_module_entry

#endif
