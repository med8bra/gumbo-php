#ifndef PHP_GUMBO_H
#define PHP_GUMBO_H

#include "php.h"
#include "ext/dom/xml_common.h"

/* Class entries */
extern zend_class_entry *gumbo_class_entry;

/* Forward declarations */
PHP_METHOD(gumbo_class, load);

/* Define Extension Properties */
#define PHP_GUMBO_EXTNAME "gumbo"
#define PHP_GUMBO_VERSION "0.1"

extern zend_module_entry gumbo_module_entry;
#define phpext_gumbo_ptr &gumbo_module_entry
#endif
