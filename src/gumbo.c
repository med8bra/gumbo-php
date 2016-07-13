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

#include "parser.h"
#include "php_gumbo.h"

extern zend_class_entry *gumbo_parser_class_entry;
extern zend_function_entry gumbo_parser_methods[];

//
// MINIT function of module.
//
PHP_MINIT_FUNCTION(gumbo)
{
    // Init: \LayerShifter\Gumbo\Parser
    
    zend_class_entry parser_ce;
    INIT_CLASS_ENTRY(parser_ce, "Layershifter\\Gumbo\\Parser", gumbo_parser_methods);
    
    gumbo_parser_class_entry = zend_register_internal_class(&parser_ce);
            
    return SUCCESS;
}

//
// Module declaration.
//
zend_module_entry gumbo_module_entry =
{
	STANDARD_MODULE_HEADER,
	PHP_GUMBO_EXTNAME,
	NULL,		
	PHP_MINIT(gumbo),
	NULL,		
	NULL,		
	NULL,		
	NULL,		
	PHP_GUMBO_VERSION,
	STANDARD_MODULE_PROPERTIES
};

ZEND_GET_MODULE(gumbo)
