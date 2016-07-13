/*
 * Copyright 2016 Alexander Fedyashov
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
#ifndef GUMBO_PARSER_H
#define GUMBO_PARSER_H

#include "gumbo.h"
#include "php_gumbo.h"
#include "ext/dom/xml_common.h"

//
// Methods declarations.
//

PHP_METHOD(GumboParser, load);

//
// Wrappers for Gumbo.
//

xmlChar* gumbo_get_tag_name(GumboElement* element);
xmlChar* gumbo_get_text(GumboNode* node);
xmlDocPtr gumbo_parse_string(zval* html);
void gumbo_parse_element(xmlDocPtr doc, xmlNodePtr parent_node, GumboNode* node);
void gumbo_recursive_parse(xmlDocPtr doc, xmlNodePtr parent_node, GumboNode* node);
void gumbo_skip_element(xmlDocPtr doc, xmlNodePtr parent_node, GumboNode* node);

#endif /* GUMBO_PARSER_H */

