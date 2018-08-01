(window.webpackJsonp=window.webpackJsonp||[]).push([[108],{347:function(t,s,a){"use strict";a.r(s);var e=a(0),n=Object(e.a)({},function(){this.$createElement;this._self._c;return this._m(0)},[function(){var t=this,s=t.$createElement,a=t._self._c||s;return a("div",{staticClass:"content"},[a("h1",{attrs:{id:"upgrading-from-php-parser-2-x-to-3-0"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#upgrading-from-php-parser-2-x-to-3-0","aria-hidden":"true"}},[t._v("#")]),t._v(" Upgrading from PHP-Parser 2.x to 3.0")]),t._v(" "),a("p",[t._v("The backwards-incompatible changes in this release may be summarized as follows:")]),t._v(" "),a("ul",[a("li",[t._v("The specific details of the node representation have changed in some cases, primarily to\naccomodate new PHP 7.1 features.")]),t._v(" "),a("li",[t._v("There have been significant changes to the error recovery implementation. This may affect you,\nif you used the error recovery mode or have a custom lexer implementation.")]),t._v(" "),a("li",[t._v("A number of deprecated methods were removed.")])]),t._v(" "),a("h3",{attrs:{id:"php-version-requirements"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#php-version-requirements","aria-hidden":"true"}},[t._v("#")]),t._v(" PHP version requirements")]),t._v(" "),a("p",[t._v("PHP-Parser now requires PHP 5.5 or newer to run. It is however still possible to "),a("em",[t._v("parse")]),t._v(" PHP 5.2,\n5.3 and 5.4 source code, while running on a newer version.")]),t._v(" "),a("h3",{attrs:{id:"changes-to-the-node-structure"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#changes-to-the-node-structure","aria-hidden":"true"}},[t._v("#")]),t._v(" Changes to the node structure")]),t._v(" "),a("p",[t._v("The following changes are likely to require code changes if the respective nodes are used:")]),t._v(" "),a("ul",[a("li",[t._v("The "),a("code",[t._v("List")]),t._v(" subnode "),a("code",[t._v("vars")]),t._v(" has been renamed to "),a("code",[t._v("items")]),t._v(" and now contains "),a("code",[t._v("ArrayItem")]),t._v("s instead of\nplain variables.")]),t._v(" "),a("li",[t._v("The "),a("code",[t._v("Catch")]),t._v(" subnode "),a("code",[t._v("type")]),t._v(" has been renamed to "),a("code",[t._v("types")]),t._v(" and is now an array of "),a("code",[t._v("Name")]),t._v("s.")]),t._v(" "),a("li",[t._v("The "),a("code",[t._v("TryCatch")]),t._v(" subnode "),a("code",[t._v("finallyStmts")]),t._v(" has been replaced with a "),a("code",[t._v("finally")]),t._v(" subnode that holds an\nexplicit "),a("code",[t._v("Finally")]),t._v(" node.")]),t._v(" "),a("li",[t._v("The "),a("code",[t._v("type")]),t._v(" subnode on "),a("code",[t._v("Class")]),t._v(", "),a("code",[t._v("ClassMethod")]),t._v(" and "),a("code",[t._v("Property")]),t._v(" has been renamed to "),a("code",[t._v("flags")]),t._v(". The\n"),a("code",[t._v("type")]),t._v(" subnode has retained for backwards compatibility and is populated to the same value as\n"),a("code",[t._v("flags")]),t._v(". However, writes to "),a("code",[t._v("type")]),t._v(" will not update "),a("code",[t._v("flags")]),t._v(" and use of "),a("code",[t._v("type")]),t._v(" is discouraged.")])]),t._v(" "),a("p",[t._v("The following changes are unlikely to require code changes:")]),t._v(" "),a("ul",[a("li",[t._v("The "),a("code",[t._v("ClassConst")]),t._v(" constructor changed to accept an additional "),a("code",[t._v("flags")]),t._v(" subnode.")]),t._v(" "),a("li",[t._v("The "),a("code",[t._v("Trait")]),t._v(" constructor now has the same form as the "),a("code",[t._v("Class")]),t._v(" and "),a("code",[t._v("Interface")]),t._v(" constructors: It\ntakes an array of subnodes. Unlike classes/interfaces, traits can only have a "),a("code",[t._v("stmts")]),t._v(" subnode.")]),t._v(" "),a("li",[t._v("The "),a("code",[t._v("Array")]),t._v(" subnode "),a("code",[t._v("items")]),t._v(" may now contain "),a("code",[t._v("null")]),t._v(" elements (due to destructuring).")]),t._v(" "),a("li",[a("code",[t._v("void")]),t._v(" and "),a("code",[t._v("iterable")]),t._v(" types are now stored as strings if the PHP 7 parser is used. Previously\nthese would have been represented as "),a("code",[t._v("Name")]),t._v(" instances.")])]),t._v(" "),a("h3",{attrs:{id:"changes-to-error-recovery-mode"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#changes-to-error-recovery-mode","aria-hidden":"true"}},[t._v("#")]),t._v(" Changes to error recovery mode")]),t._v(" "),a("p",[t._v("Previously, error recovery mode was enabled by setting the "),a("code",[t._v("throwOnError")]),t._v(" option to "),a("code",[t._v("false")]),t._v(" when\ncreating the parser, while collected errors were retrieved using the "),a("code",[t._v("getErrors()")]),t._v(" method:")]),t._v(" "),a("div",{staticClass:"language-php extra-class"},[a("pre",{pre:!0,attrs:{class:"language-php"}},[a("code",[a("span",{attrs:{class:"token variable"}},[t._v("$lexer")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token variable"}},[t._v("$parser")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("ParserFactory")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token operator"}},[t._v("-")]),a("span",{attrs:{class:"token operator"}},[t._v(">")]),a("span",{attrs:{class:"token function"}},[t._v("create")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),t._v("ParserFactor"),a("span",{attrs:{class:"token punctuation"}},[t._v(":")]),a("span",{attrs:{class:"token punctuation"}},[t._v(":")]),a("span",{attrs:{class:"token constant"}},[t._v("ONLY_PHP7")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{attrs:{class:"token variable"}},[t._v("$lexer")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{attrs:{class:"token punctuation"}},[t._v("[")]),t._v("\n    "),a("span",{attrs:{class:"token single-quoted-string string"}},[t._v("'throwOnError'")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),a("span",{attrs:{class:"token operator"}},[t._v(">")]),t._v(" "),a("span",{attrs:{class:"token boolean"}},[t._v("true")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v("\n"),a("span",{attrs:{class:"token punctuation"}},[t._v("]")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n"),a("span",{attrs:{class:"token variable"}},[t._v("$stmts")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token variable"}},[t._v("$parser")]),a("span",{attrs:{class:"token operator"}},[t._v("-")]),a("span",{attrs:{class:"token operator"}},[t._v(">")]),a("span",{attrs:{class:"token function"}},[t._v("parse")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$code")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token variable"}},[t._v("$errors")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token variable"}},[t._v("$parser")]),a("span",{attrs:{class:"token operator"}},[t._v("-")]),a("span",{attrs:{class:"token operator"}},[t._v(">")]),a("span",{attrs:{class:"token function"}},[t._v("getErrors")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token keyword"}},[t._v("if")]),t._v(" "),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$errors")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),t._v(" "),a("span",{attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n    "),a("span",{attrs:{class:"token function"}},[t._v("handleErrors")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$errors")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n"),a("span",{attrs:{class:"token function"}},[t._v("processAst")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$stmts")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n")])])]),a("p",[t._v("Both the "),a("code",[t._v("throwOnError")]),t._v(" option and the "),a("code",[t._v("getErrors()")]),t._v(" method have been removed in PHP-Parser 3.0.\nInstead an instance of "),a("code",[t._v("ErrorHandler\\Collecting")]),t._v(" should be passed to the "),a("code",[t._v("parse()")]),t._v(" method:")]),t._v(" "),a("div",{staticClass:"language-php extra-class"},[a("pre",{pre:!0,attrs:{class:"language-php"}},[a("code",[a("span",{attrs:{class:"token variable"}},[t._v("$lexer")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token variable"}},[t._v("$parser")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("ParserFactory")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token operator"}},[t._v("-")]),a("span",{attrs:{class:"token operator"}},[t._v(">")]),a("span",{attrs:{class:"token function"}},[t._v("create")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),t._v("ParserFactor"),a("span",{attrs:{class:"token punctuation"}},[t._v(":")]),a("span",{attrs:{class:"token punctuation"}},[t._v(":")]),a("span",{attrs:{class:"token constant"}},[t._v("ONLY_PHP7")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{attrs:{class:"token variable"}},[t._v("$lexer")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n"),a("span",{attrs:{class:"token variable"}},[t._v("$errorHandler")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("ErrorHandler"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Collecting")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token variable"}},[t._v("$stmts")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token variable"}},[t._v("$parser")]),a("span",{attrs:{class:"token operator"}},[t._v("-")]),a("span",{attrs:{class:"token operator"}},[t._v(">")]),a("span",{attrs:{class:"token function"}},[t._v("parse")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$code")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{attrs:{class:"token variable"}},[t._v("$errorHandler")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token keyword"}},[t._v("if")]),t._v(" "),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$errorHandler")]),a("span",{attrs:{class:"token operator"}},[t._v("-")]),a("span",{attrs:{class:"token operator"}},[t._v(">")]),a("span",{attrs:{class:"token function"}},[t._v("hasErrors")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),t._v(" "),a("span",{attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n    "),a("span",{attrs:{class:"token function"}},[t._v("handleErrors")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$errorHandler")]),a("span",{attrs:{class:"token operator"}},[t._v("-")]),a("span",{attrs:{class:"token operator"}},[t._v(">")]),a("span",{attrs:{class:"token function"}},[t._v("getErrors")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n"),a("span",{attrs:{class:"token function"}},[t._v("processAst")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$stmts")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n")])])]),a("h4",{attrs:{id:"multiple-parser-fallback-in-error-recovery-mode"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#multiple-parser-fallback-in-error-recovery-mode","aria-hidden":"true"}},[t._v("#")]),t._v(" Multiple parser fallback in error recovery mode")]),t._v(" "),a("p",[t._v("As a result of this change, if a "),a("code",[t._v("Multiple")]),t._v(" parser is used (e.g. through the "),a("code",[t._v("ParserFactory")]),t._v(" using\n"),a("code",[t._v("PREFER_PHP7")]),t._v(" or "),a("code",[t._v("PREFER_PHP5")]),t._v("), it will now return the result of the first "),a("em",[t._v("non-throwing")]),t._v(" parse. As\nparsing never throws in error recovery mode, the result from the first parser will always be\nreturned.")]),t._v(" "),a("p",[t._v("The PHP 7 parser is a superset of the PHP 5 parser, with the exceptions that "),a("code",[t._v("=& new")]),t._v(" and\n"),a("code",[t._v("global $$foo->bar")]),t._v(" are not supported (other differences are in representation only). The PHP 7\nparser will be able to recover from the error in both cases. For this reason, this change will\nlikely pass unnoticed if you do not specifically test for this syntax.")]),t._v(" "),a("p",[t._v("It is possible to restore the precise previous behavior with the following code:")]),t._v(" "),a("div",{staticClass:"language-php extra-class"},[a("pre",{pre:!0,attrs:{class:"language-php"}},[a("code",[a("span",{attrs:{class:"token variable"}},[t._v("$lexer")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token variable"}},[t._v("$parser7")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Parser"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Php7")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$lexer")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token variable"}},[t._v("$parser5")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Parser"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Php5")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$lexer")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n"),a("span",{attrs:{class:"token variable"}},[t._v("$errors7")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("ErrorHandler"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Collecting")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token variable"}},[t._v("$stmts7")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token variable"}},[t._v("$parser7")]),a("span",{attrs:{class:"token operator"}},[t._v("-")]),a("span",{attrs:{class:"token operator"}},[t._v(">")]),a("span",{attrs:{class:"token function"}},[t._v("parse")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$code")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{attrs:{class:"token variable"}},[t._v("$errors7")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token keyword"}},[t._v("if")]),t._v(" "),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$errors7")]),a("span",{attrs:{class:"token operator"}},[t._v("-")]),a("span",{attrs:{class:"token operator"}},[t._v(">")]),a("span",{attrs:{class:"token function"}},[t._v("hasErrors")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),t._v(" "),a("span",{attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n    "),a("span",{attrs:{class:"token variable"}},[t._v("$errors5")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("ErrorHandler"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Collecting")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n    "),a("span",{attrs:{class:"token variable"}},[t._v("$stmts5")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token variable"}},[t._v("$parser5")]),a("span",{attrs:{class:"token operator"}},[t._v("-")]),a("span",{attrs:{class:"token operator"}},[t._v(">")]),a("span",{attrs:{class:"token function"}},[t._v("parse")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$code")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{attrs:{class:"token variable"}},[t._v("$errors5")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n    "),a("span",{attrs:{class:"token keyword"}},[t._v("if")]),t._v(" "),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token operator"}},[t._v("!")]),a("span",{attrs:{class:"token variable"}},[t._v("$errors5")]),a("span",{attrs:{class:"token operator"}},[t._v("-")]),a("span",{attrs:{class:"token operator"}},[t._v(">")]),a("span",{attrs:{class:"token function"}},[t._v("hasErrors")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),t._v(" "),a("span",{attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n        "),a("span",{attrs:{class:"token comment"}},[t._v("// If PHP 7 parse has errors but PHP 5 parse has no errors, use PHP 5 result")]),t._v("\n        "),a("span",{attrs:{class:"token keyword"}},[t._v("return")]),t._v(" "),a("span",{attrs:{class:"token punctuation"}},[t._v("[")]),a("span",{attrs:{class:"token variable"}},[t._v("$stmts5")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{attrs:{class:"token variable"}},[t._v("$errors5")]),a("span",{attrs:{class:"token punctuation"}},[t._v("]")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n    "),a("span",{attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n"),a("span",{attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n"),a("span",{attrs:{class:"token comment"}},[t._v("// If PHP 7 succeeds or both fail use PHP 7 result")]),t._v("\n"),a("span",{attrs:{class:"token keyword"}},[t._v("return")]),t._v(" "),a("span",{attrs:{class:"token punctuation"}},[t._v("[")]),a("span",{attrs:{class:"token variable"}},[t._v("$stmts7")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{attrs:{class:"token variable"}},[t._v("$errors7")]),a("span",{attrs:{class:"token punctuation"}},[t._v("]")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n")])])]),a("h4",{attrs:{id:"error-handling-in-the-lexer"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#error-handling-in-the-lexer","aria-hidden":"true"}},[t._v("#")]),t._v(" Error handling in the lexer")]),t._v(" "),a("p",[t._v("In order to support recovery from lexer errors, the signature of the "),a("code",[t._v("startLexing()")]),t._v(" method changed\nto optionally accept an "),a("code",[t._v("ErrorHandler")]),t._v(":")]),t._v(" "),a("div",{staticClass:"language-php extra-class"},[a("pre",{pre:!0,attrs:{class:"language-php"}},[a("code",[a("span",{attrs:{class:"token comment"}},[t._v("// OLD")]),t._v("\n"),a("span",{attrs:{class:"token keyword"}},[t._v("public")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("function")]),t._v(" "),a("span",{attrs:{class:"token function"}},[t._v("startLexing")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$code")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token comment"}},[t._v("// NEW")]),t._v("\n"),a("span",{attrs:{class:"token keyword"}},[t._v("public")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("function")]),t._v(" "),a("span",{attrs:{class:"token function"}},[t._v("startLexing")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$code")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" ErrorHandler "),a("span",{attrs:{class:"token variable"}},[t._v("$errorHandler")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("null")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n")])])]),a("p",[t._v("If you use a custom lexer with overriden "),a("code",[t._v("startLexing()")]),t._v(" method, it needs to be changed to accept\nthe extra parameter. The value should be passed on to the parent method.")]),t._v(" "),a("h4",{attrs:{id:"error-checks-in-node-constructors"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#error-checks-in-node-constructors","aria-hidden":"true"}},[t._v("#")]),t._v(" Error checks in node constructors")]),t._v(" "),a("p",[t._v("The constructors of certain nodes used to contain additional checks for semantic errors, such as\ncreating a try block without either catch or finally. These checks have been moved from the node\nconstructors into the parser. This allows recovery from such errors, as well as representing the\nresulting (invalid) AST.")]),t._v(" "),a("p",[t._v("This means that certain error conditions are no longer checked for manually constructed nodes.")]),t._v(" "),a("h3",{attrs:{id:"removed-methods-arguments-options"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#removed-methods-arguments-options","aria-hidden":"true"}},[t._v("#")]),t._v(" Removed methods, arguments, options")]),t._v(" "),a("p",[t._v("The following methods, arguments or options have been removed:")]),t._v(" "),a("ul",[a("li",[a("code",[t._v("Comment::setLine()")]),t._v(", "),a("code",[t._v("Comment::setText()")]),t._v(": Create new "),a("code",[t._v("Comment")]),t._v(" instances instead.")]),t._v(" "),a("li",[a("code",[t._v("Name::set()")]),t._v(", "),a("code",[t._v("Name::setFirst()")]),t._v(", "),a("code",[t._v("Name::setLast()")]),t._v(", "),a("code",[t._v("Name::append()")]),t._v(", "),a("code",[t._v("Name::prepend()")]),t._v(":\nUse "),a("code",[t._v("Name::concat()")]),t._v(" in combination with "),a("code",[t._v("Name::slice()")]),t._v(" instead.")]),t._v(" "),a("li",[a("code",[t._v("Error::getRawLine()")]),t._v(", "),a("code",[t._v("Error::setRawLine()")]),t._v(". Use "),a("code",[t._v("Error::getStartLine()")]),t._v(" and\n"),a("code",[t._v("Error::setStartLine()")]),t._v(" instead.")]),t._v(" "),a("li",[a("code",[t._v("Parser::getErrors()")]),t._v(". Use "),a("code",[t._v("ErrorHandler\\Collecting")]),t._v(" instead.")]),t._v(" "),a("li",[a("code",[t._v("$separator")]),t._v(" argument of "),a("code",[t._v("Name::toString()")]),t._v(". Use "),a("code",[t._v("strtr()")]),t._v(" instead, if you really need it.")]),t._v(" "),a("li",[a("code",[t._v("$cloneNodes")]),t._v(" argument of "),a("code",[t._v("NodeTraverser::__construct()")]),t._v(". Explicitly clone nodes in the visitor\ninstead.")]),t._v(" "),a("li",[a("code",[t._v("throwOnError")]),t._v(" parser option. Use "),a("code",[t._v("ErrorHandler\\Collecting")]),t._v(" instead.")])]),t._v(" "),a("h3",{attrs:{id:"miscellaneous"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#miscellaneous","aria-hidden":"true"}},[t._v("#")]),t._v(" Miscellaneous")]),t._v(" "),a("ul",[a("li",[t._v("The "),a("code",[t._v("NameResolver")]),t._v(" will now resolve unqualified function and constant names in the global\nnamespace into fully qualified names. For example "),a("code",[t._v("foo()")]),t._v(" in the global namespace resolves to\n"),a("code",[t._v("\\foo()")]),t._v(". For names where no static resolution is possible, a "),a("code",[t._v("namespacedName")]),t._v(" attribute is\nadded now, containing the namespaced variant of the name.")]),t._v(" "),a("li",[t._v("All methods on "),a("code",[t._v("PrettyPrinter\\Standard")]),t._v(" are now protected. Previoulsy most of them were public.\nThe pretty printer should only be invoked using the "),a("code",[t._v("prettyPrint()")]),t._v(", "),a("code",[t._v("prettyPrintFile()")]),t._v(" and\n"),a("code",[t._v("prettyPrintExpr()")]),t._v(" methods.")]),t._v(" "),a("li",[t._v("The node dumper now prints numeric values that act as enums/flags in a string representation.\nIf node dumper results are used in tests, updates may be needed to account for this.")]),t._v(" "),a("li",[t._v("The constants on "),a("code",[t._v("NameTraverserInterface")]),t._v(" have been moved into the "),a("code",[t._v("NameTraverser")]),t._v(" class.")]),t._v(" "),a("li",[t._v("The emulative lexer now directly postprocesses tokens, instead of using "),a("code",[t._v("~__EMU__~")]),t._v(" sequences.\nThis changes the protected API of the emulative lexer.")]),t._v(" "),a("li",[t._v("The "),a("code",[t._v("Name::slice()")]),t._v(" method now returns "),a("code",[t._v("null")]),t._v(" for empty slices, previously "),a("code",[t._v("new Name([])")]),t._v(" was\nused. "),a("code",[t._v("Name::concat()")]),t._v(" now also supports concatenation with "),a("code",[t._v("null")]),t._v(".")])])])}],!1,null,null,null);s.default=n.exports}}]);