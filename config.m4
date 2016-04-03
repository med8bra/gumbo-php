PHP_ARG_WITH(gumbo, for gumbo support,
[  --with-gumbo[[=DIR]]      Include gumbo support])

CFLAGS="$CFLAGS -std=c99"

dnl

if test "$PHP_GUMBO" != "no"; then
  SEARCH_PATH="/usr/local /usr"
  SEARCH_FOR="/include/gumbo.h"

  dnl

  if test -r $PHP_GUMBO/$SEARCH_FOR; then
    GUMBO_DIR=$PHP_GUMBO
  else
    AC_MSG_CHECKING([for gumbo files in default path])
    for i in $SEARCH_PATH ; do
      if test -r $i/$SEARCH_FOR; then
        GUMBO_DIR=$i
        AC_MSG_RESULT(found in $i)
      fi
    done
  fi

  PHP_ADD_INCLUDE($GUMBO_DIR/include)

  dnl

  LIBNAME=gumbo
  LIBSYMBOL=gumbo_destroy_output

  PHP_CHECK_LIBRARY($LIBNAME,$LIBSYMBOL,
  [
    PHP_ADD_LIBRARY_WITH_PATH($LIBNAME, $GUMBO_DIR/lib, GUMBO_SHARED_LIBADD)
    AC_DEFINE(HAVE_GUMBOLIB,1,[ ])
  ],[
    AC_MSG_ERROR([wrong gumbo lib version or lib not found])
  ],[
    -L$GUMBO_DIR/lib -lm
  ])

  dnl

  PHP_SUBST(GUMBO_SHARED_LIBADD)
fi

dnl # Checks for libxml2

if test -z "$PHP_LIBXML_DIR"; then
  PHP_ARG_WITH(libxml-dir, libxml2 install dir,
  [  --with-libxml-dir=DIR   Libxml2 install prefix], no, no)
fi

if test "$PHP_LIBXML" = "no"; then
  AC_MSG_ERROR([Gumbo extension requires LIBXML extension])
fi

PHP_SETUP_LIBXML(GUMBO_SHARED_LIBADD, [
  AC_DEFINE(HAVE_GUMBO,1,[ ])
  PHP_NEW_EXTENSION(gumbo, gumbo.c, $ext_shared)
  PHP_SUBST(GUMBO_SHARED_LIBADD)
], [
  AC_MSG_ERROR([xml2-config not found. Please check your libxml2 installation.])
])

PHP_ADD_EXTENSION_DEP(gumbo, libxml)
PHP_ADD_EXTENSION_DEP(gumbo, dom)
