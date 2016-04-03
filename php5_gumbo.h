#ifndef PHP_GUMBO_H
#define PHP_GUMBO_H

#include "php.h"
#include "ext/dom/xml_common.h"
#include "gumbo.h"

// C: Helper functions

const char* gumboXmlns[] = {
  "http://www.w3.org/1999/xhtml",
  "http://www.w3.org/2000/svg",
  "http://www.w3.org/1998/Math/MathML"
};

xmlDocPtr gumboParseString(zval* zvHtml);

void gumboRecursiveParse(xmlDocPtr xmlDoc, xmlNodePtr parentNode, GumboNode* gumboNode);
void gumboSkipElement(xmlDocPtr xmlDoc, xmlNodePtr parentNode, GumboNode* gumboNode);
void gumboParseElement(xmlDocPtr xmlDoc, xmlNodePtr parentNode, GumboNode* gumboNode);
xmlChar* gumboGetTagName(GumboElement* gumboElement);

// PHP: Class entries

extern zend_class_entry *gumbo_class_entry;

// PHP: Forward declarations

PHP_METHOD(gumbo_class, load);

// PHP: Extension constants and declarations

#define PHP_GUMBO_EXTNAME "gumbo"
#define PHP_GUMBO_VERSION "0.1"

extern zend_module_entry gumbo_module_entry;
#define phpext_gumbo_ptr &gumbo_module_entry
#endif
