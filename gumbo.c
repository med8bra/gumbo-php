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

#include "php5_gumbo.h"

//
// Main parser function. Parses HTML with Gumbo and runs recursive parsing for elements.
//
xmlDocPtr gumboParseString(zval* zvHtml) {
    // Run Gumbo Parser.

    GumboOptions gumboOptions = kGumboDefaultOptions;
    GumboOutput* gumboOutput = gumbo_parse_with_options(&gumboOptions, Z_STRVAL_P(zvHtml), Z_STRLEN_P(zvHtml));

    // Create new xmlDoc.

    xmlDocPtr xmlDoc = xmlNewDoc(BAD_CAST "1.0");

    // If HTML page has DTD (Document Type Definition) then add it to xmlDoc.

    GumboDocument* gumboDocument = &gumboOutput->document->v.document;

    if(gumboDocument->has_doctype) {
        xmlNewDtd(
            xmlDoc,
            BAD_CAST gumboDocument->name,
            BAD_CAST gumboDocument->public_identifier,
            BAD_CAST gumboDocument->system_identifier
        );
    }

    // Main parsing process.

    gumboRecursiveParse(xmlDoc, (xmlNodePtr)xmlDoc, gumboOutput->root);

    // Clearing gumbo and returning xmlDoc.

    // TODO: Errors to libXML

    gumbo_destroy_output(&gumboOptions, gumboOutput);

    return xmlDoc;
}

//
// Recursive parser function.
//
void gumboRecursiveParse(xmlDocPtr xmlDoc, xmlNodePtr parentNode, GumboNode* gumboNode) {

    if(gumboNode->parse_flags & GUMBO_INSERTION_BY_PARSER) {
        gumboSkipElement(xmlDoc, parentNode, gumboNode);

        return;
    }

    switch(gumboNode->type) {
        case GUMBO_NODE_DOCUMENT:
            // @TODO: Throw exception
        break;

        case GUMBO_NODE_ELEMENT:
        case GUMBO_NODE_TEMPLATE:
            gumboParseElement(xmlDoc, parentNode, gumboNode);
        break;

        case GUMBO_NODE_TEXT:
        case GUMBO_NODE_WHITESPACE:
        case GUMBO_NODE_CDATA:
            xmlAddChild(parentNode, xmlNewText(BAD_CAST gumboNode->v.text.text));
        break;

        case GUMBO_NODE_COMMENT:
            xmlAddChild(parentNode, xmlNewComment(BAD_CAST gumboNode->v.text.text));
        break;
    }
}

//
// Function for skipping elements that Gumbo generated automatically.
//
void gumboSkipElement(xmlDocPtr xmlDoc, xmlNodePtr parentNode, GumboNode* gumboNode) {
    GumboVector gumboChildren = gumboNode->v.element.children;

    // Skip processing if node doesn't have children.

    if(gumboChildren.length == 0) {
        return;
    }

    // If node has only one child, loop isn't need.

    if(gumboChildren.length == 1) {
        GumboNode* gumboChildNode = gumboChildren.data[0];
        gumboRecursiveParse(xmlDoc, parentNode, gumboChildNode);

        return;
    }

    // Run loop for children elements.

    for (int i = 0; i < gumboChildren.length; i++) {
        GumboNode* gumboChildNode = gumboChildren.data[i];
        gumboRecursiveParse(xmlDoc, parentNode, gumboChildNode);
    }
}

//
// Function for processing elements.
//
void gumboParseElement(xmlDocPtr xmlDoc, xmlNodePtr parentNode, GumboNode* gumboNode) {
    GumboElement* gumboElement = &gumboNode->v.element;
    int i;

    // Creating XML node.

    xmlNodePtr resultNode = xmlNewNode(NULL, gumboGetTagName(gumboElement));

    // Processing namespaces.

    if (gumboNode->parent->type != GUMBO_NODE_DOCUMENT
        && gumboElement->tag_namespace != gumboNode->parent->v.element.tag_namespace
    ) {
        xmlNsPtr namespace = xmlNewNs(
            resultNode,
            BAD_CAST gumboXmlns[gumboElement->tag_namespace], NULL
        );
        xmlSetNs(resultNode, namespace);
    }

    // Processing attributes.

    GumboVector gumboAttributes = gumboElement->attributes;

    if(gumboAttributes.length > 0) {
        for (i = 0; i < gumboAttributes.length; ++i) {
            GumboAttribute* gumboAttribute = gumboAttributes.data[i];

            xmlNewProp(
                resultNode,
                BAD_CAST gumboAttribute->name,
                BAD_CAST gumboAttribute->value
            );
        }
    }

    // Processing children.

    GumboVector gumboChildren = gumboElement->children;

    if(gumboChildren.length > 0) {
        for (i = 0; i < gumboChildren.length; ++i) {
            gumboRecursiveParse(xmlDoc, resultNode, gumboChildren.data[i]);
        }
    }

    xmlAddChild(parentNode, resultNode);
}

//
// Function that returns pointer to real tag name.
//
xmlChar* gumboGetTagName(GumboElement* gumboElement) {
    if(gumboElement->tag == GUMBO_TAG_UNKNOWN) {
        GumboStringPiece originalTag = gumboElement->original_tag;
        gumbo_tag_from_original_text(&originalTag);

        char * tagName = NULL;
        tagName = malloc(sizeof(char) * originalTag.length);

        memcpy(tagName, originalTag.data, originalTag.length);
        tagName[originalTag.length]= '\0';

        return BAD_CAST tagName;
    }

    return BAD_CAST gumbo_normalized_tagname(gumboElement->tag);
}

//
// Zend declarations.
//

zend_class_entry *gumbo_class_entry;

ZEND_BEGIN_ARG_INFO_EX(arginfo_gumbo_load, 0, 0, 1)
    ZEND_ARG_INFO(0, html)
ZEND_END_ARG_INFO()

static zend_function_entry gumbo_functions[] = {
    PHP_FE_END
};

static zend_function_entry gumbo_class_methods[] =
{
    PHP_ME(gumbo_class, load, arginfo_gumbo_load, ZEND_ACC_PUBLIC | ZEND_ACC_STATIC)
    PHP_FE_END
};

//
// Method for parsing HTML into DOMDocument.
// \DomDocument Layershifter\Gumbo::load(string $html);
//
PHP_METHOD(gumbo_class, load) {
    zval *zvHtml;

    // Parsing input values

    if (zend_parse_parameters(ZEND_NUM_ARGS(), "z", &zvHtml) == FAILURE) {
        // @TODO: Throw exception

        RETURN_BOOL(false);
    }

    convert_to_string(zvHtml);

    // Parsing document and return DOMDocument

    int ret;
    dom_object *intern;
    xmlDocPtr xmlDoc = gumboParseString(zvHtml);

    DOM_RET_OBJ((xmlNodePtr)xmlDoc, &ret, intern);
}

//
// Register extension.
//
PHP_MINIT_FUNCTION(gumbo)
{
    zend_class_entry ce;

    INIT_CLASS_ENTRY(ce, "Layershifter\\Gumbo", gumbo_class_methods);
    zend_register_internal_class(&ce);

    return SUCCESS;
}

//
// Module declarations.
//
zend_module_entry gumbo_module_entry =
{
	STANDARD_MODULE_HEADER,		/* Standard module header */
	PHP_GUMBO_EXTNAME,			/* Extension name */
	gumbo_functions,			/* Functions */
	PHP_MINIT(gumbo),           /* MINIT */
	NULL,						/* MSHUTDOWN */
	NULL,						/* RINIT */
	NULL,						/* RSHUTDOWN */
	NULL,						/* MINFO */
	PHP_GUMBO_VERSION,			/* Version */
	STANDARD_MODULE_PROPERTIES	/* Standard properties */
};

ZEND_GET_MODULE(gumbo)