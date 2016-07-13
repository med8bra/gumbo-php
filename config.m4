dnl #
dnl # Flags for compilation
dnl #

CFLAGS="$CFLAGS -std=c99"

dnl #
dnl # Extension config
dnl #

PHP_ARG_WITH(gumbo, for gumbo support,
[  --with-gumbo[[=DIR]]      Include gumbo support])

if test "$PHP_GUMBO" != "no"; then
  dnl #
  dnl # Testing for gumbo support
  dnl #

  SEARCH_PATH="/usr/local /usr"
  SEARCH_FOR="/include/gumbo.h"

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
  PHP_CHECK_LIBRARY(gumbo, gumbo_destroy_output,
  [
    PHP_ADD_LIBRARY_WITH_PATH(gumbo, $GUMBO_DIR/lib, GUMBO_SHARED_LIBADD)
  ],[
    AC_MSG_ERROR([wrong version of gumbo of it's not found])
  ], [
    -L$GUMBO_DIR/lib -lm
  ])

  dnl #
  dnl # Checks for php-libxml support
  dnl #

  if test "$PHP_LIBXML" = "no"; then
    AC_MSG_ERROR([Gumbo extension requires LIBXML extension])
  else
    PHP_SETUP_LIBXML(GUMBO_SHARED_LIBADD, [
      PHP_NEW_EXTENSION(gumbo, src/gumbo.c src/parser.c, $ext_shared)
      PHP_ADD_BUILD_DIR([$ext_builddir/src/])
      PHP_ADD_EXTENSION_DEP(dom, libxml)
      PHP_SUBST(GUMBO_SHARED_LIBADD)
    ], [
      AC_MSG_ERROR([xml2-config not found. Please check your libxml2 installation.])
    ])
  fi
fi
