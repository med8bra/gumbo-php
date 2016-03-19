/* Define Extension Properties */
#define PHP_GUMBO_EXTNAME "gumbo"
#define PHP_GUMBO_VERSION "0.1"

/* Include some required php headers */
#define ZEND_DEBUG false
#define ZEND_WIN32 true
#define PHP_WIN32 true

#define PHP_COMPILER_ID "VC14"

#include "zend_config.w32.h"
#include "php.h"

extern zend_module_entry gumbo_module_entry;
#define phpext_gmagick_ptr &gumbo_module_entry

/* Class entries */
extern zend_class_entry *gumbo_class_entry;

/* Forward declarations */
PHP_METHOD(gumbo_class, load);
//bui