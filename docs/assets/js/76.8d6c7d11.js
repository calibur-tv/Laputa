(window.webpackJsonp=window.webpackJsonp||[]).push([[76],{395:function(t,s,a){"use strict";a.r(s);var n=a(0),e=Object(n.a)({},function(){var t=this,s=t.$createElement,a=t._self._c||s;return a("div",{staticClass:"content"},[t._m(0),t._v(" "),a("p",[t._v("File system I/O is slow, so Flysystem uses cached file system meta-data to boost performance. When your application needs to scale you can also choose to use a (shared) persistent caching solution for this.\nOr enable a per request caching (recommended).")]),t._v(" "),t._m(1),t._v(" "),t._m(2),a("p",[t._v("This package supplies an Adapter decorator which acts as a caching proxy.")]),t._v(" "),a("p",[t._v("The CachedAdapter (the decorator) caches anything but the file contents. This keeps the cache small enough to be beneficial and covers all the file system inspection operations.")]),t._v(" "),t._m(3),t._v(" "),a("p",[t._v("The easiest way to boost the performance of Flysystem is to add Memory caching.\nThis type of caching will cache everything in the lifetime of the current process (cli-job or http-request).\nSetting it up is easy:")]),t._v(" "),t._m(4),a("p",[t._v("You can now use the file system as you would have before, but caching will be done for you on the fly.")]),t._v(" "),t._m(5),t._v(" "),a("p",[t._v("The following examples demonstrate how you can setup persistent meta-data caching:")]),t._v(" "),t._m(6),t._v(" "),t._m(7),t._m(8),t._v(" "),t._m(9),t._m(10),t._v(" "),t._m(11),t._m(12),t._v(" "),t._m(13),a("p",[t._v("For list of drivers and configuration options check their "),a("a",{attrs:{href:"http://www.stashphp.com/Drivers.html",target:"_blank",rel:"noopener noreferrer"}},[t._v("documentation"),a("OutboundLink")],1),t._v(".")])])},[function(){var t=this.$createElement,s=this._self._c||t;return s("h1",{attrs:{id:"caching"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#caching","aria-hidden":"true"}},[this._v("#")]),this._v(" Caching")])},function(){var t=this.$createElement,s=this._self._c||t;return s("h2",{attrs:{id:"installing-the-adapter-cache-decorator"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#installing-the-adapter-cache-decorator","aria-hidden":"true"}},[this._v("#")]),this._v(" Installing the adapter cache decorator")])},function(){var t=this.$createElement,s=this._self._c||t;return s("div",{staticClass:"language-bash extra-class"},[s("pre",{pre:!0,attrs:{class:"language-bash"}},[s("code",[this._v("composer require league/flysystem-cached-adapter\n")])])])},function(){var t=this.$createElement,s=this._self._c||t;return s("h2",{attrs:{id:"memory-caching"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#memory-caching","aria-hidden":"true"}},[this._v("#")]),this._v(" Memory Caching")])},function(){var t=this,s=t.$createElement,a=t._self._c||s;return a("div",{staticClass:"language-php extra-class"},[a("pre",{pre:!0,attrs:{class:"language-php"}},[a("code",[a("span",{attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),a("span",{attrs:{class:"token package"}},[t._v("League"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Flysystem"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Filesystem")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),a("span",{attrs:{class:"token package"}},[t._v("League"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Flysystem"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Adapter"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Local")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("as")]),t._v(" Adapter"),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),a("span",{attrs:{class:"token package"}},[t._v("League"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Flysystem"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Cached"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("CachedAdapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),a("span",{attrs:{class:"token package"}},[t._v("League"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Flysystem"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Cached"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Storage"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Memory")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("as")]),t._v(" CacheStore"),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n"),a("span",{attrs:{class:"token comment"}},[t._v("// Create the adapter")]),t._v("\n"),a("span",{attrs:{class:"token variable"}},[t._v("$localAdapter")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Local")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token single-quoted-string string"}},[t._v("'/path/to/root'")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n"),a("span",{attrs:{class:"token comment"}},[t._v("// Create the cache store")]),t._v("\n"),a("span",{attrs:{class:"token variable"}},[t._v("$cacheStore")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("CacheStore")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n"),a("span",{attrs:{class:"token comment"}},[t._v("// Decorate the adapter")]),t._v("\n"),a("span",{attrs:{class:"token variable"}},[t._v("$adapter")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("CachedAdapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$localAdapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{attrs:{class:"token variable"}},[t._v("$cacheStore")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n"),a("span",{attrs:{class:"token comment"}},[t._v("// And use that to create the file system")]),t._v("\n"),a("span",{attrs:{class:"token variable"}},[t._v("$filesystem")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Filesystem")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$adapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n")])])])},function(){var t=this.$createElement,s=this._self._c||t;return s("h2",{attrs:{id:"persistent-caching"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#persistent-caching","aria-hidden":"true"}},[this._v("#")]),this._v(" Persistent Caching")])},function(){var t=this.$createElement,s=this._self._c||t;return s("h2",{attrs:{id:"predis-caching-setup"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#predis-caching-setup","aria-hidden":"true"}},[this._v("#")]),this._v(" Predis Caching Setup")])},function(){var t=this,s=t.$createElement,a=t._self._c||s;return a("div",{staticClass:"language-php extra-class"},[a("pre",{pre:!0,attrs:{class:"language-php"}},[a("code",[a("span",{attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),a("span",{attrs:{class:"token package"}},[t._v("League"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Flysystem"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Filesystem")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),a("span",{attrs:{class:"token package"}},[t._v("League"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Flysystem"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Adapter"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Local")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("as")]),t._v(" Adapter"),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),a("span",{attrs:{class:"token package"}},[t._v("League"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Flysystem"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Cached"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("CachedAdapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),a("span",{attrs:{class:"token package"}},[t._v("League"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Flysystem"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Cached"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Storage"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Predis")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("as")]),t._v(" Cache"),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n"),a("span",{attrs:{class:"token variable"}},[t._v("$adapter")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("CachedAdapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Adapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token constant"}},[t._v("__DIR__")]),a("span",{attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{attrs:{class:"token single-quoted-string string"}},[t._v("'/path/to/root'")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Cache")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token variable"}},[t._v("$filesystem")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Filesystem")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$adapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n"),a("span",{attrs:{class:"token comment"}},[t._v("// Or supply a client")]),t._v("\n"),a("span",{attrs:{class:"token variable"}},[t._v("$client")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Predis"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Client")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token variable"}},[t._v("$adapter")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("CachedAdapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Adapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token constant"}},[t._v("__DIR__")]),a("span",{attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{attrs:{class:"token single-quoted-string string"}},[t._v("'/path/to/root'")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Cache")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$client")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token variable"}},[t._v("$filesystem")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Filesystem")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$adapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n")])])])},function(){var t=this.$createElement,s=this._self._c||t;return s("h2",{attrs:{id:"memcached-caching-setup"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#memcached-caching-setup","aria-hidden":"true"}},[this._v("#")]),this._v(" Memcached Caching Setup")])},function(){var t=this,s=t.$createElement,a=t._self._c||s;return a("div",{staticClass:"language-php extra-class"},[a("pre",{pre:!0,attrs:{class:"language-php"}},[a("code",[a("span",{attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),a("span",{attrs:{class:"token package"}},[t._v("League"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Flysystem"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Filesystem")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),a("span",{attrs:{class:"token package"}},[t._v("League"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Flysystem"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Adapter"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Local")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("as")]),t._v(" Adapter"),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),a("span",{attrs:{class:"token package"}},[t._v("League"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Flysystem"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Cached"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("CachedAdapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),a("span",{attrs:{class:"token package"}},[t._v("League"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Flysystem"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Cached"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Storage"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Memcached")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("as")]),t._v(" Cache"),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n"),a("span",{attrs:{class:"token variable"}},[t._v("$memcached")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Memcached")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token variable"}},[t._v("$memcached")]),a("span",{attrs:{class:"token operator"}},[t._v("-")]),a("span",{attrs:{class:"token operator"}},[t._v(">")]),a("span",{attrs:{class:"token function"}},[t._v("addServer")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token single-quoted-string string"}},[t._v("'localhost'")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{attrs:{class:"token number"}},[t._v("11211")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n"),a("span",{attrs:{class:"token variable"}},[t._v("$adapter")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("CachedAdapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),t._v("\n    "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Adapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token constant"}},[t._v("__DIR__")]),a("span",{attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{attrs:{class:"token single-quoted-string string"}},[t._v("'/path/to/root'")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v("\n    "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Cache")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$memcached")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{attrs:{class:"token single-quoted-string string"}},[t._v("'storageKey'")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{attrs:{class:"token number"}},[t._v("300")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n"),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token variable"}},[t._v("$filesystem")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Filesystem")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$adapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token comment"}},[t._v("// Storage Key and expire time are optional")]),t._v("\n")])])])},function(){var t=this.$createElement,s=this._self._c||t;return s("h2",{attrs:{id:"adapter-caching-setup"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#adapter-caching-setup","aria-hidden":"true"}},[this._v("#")]),this._v(" Adapter Caching Setup")])},function(){var t=this,s=t.$createElement,a=t._self._c||s;return a("div",{staticClass:"language-php extra-class"},[a("pre",{pre:!0,attrs:{class:"language-php"}},[a("code",[a("span",{attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),a("span",{attrs:{class:"token package"}},[t._v("Dropbox"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Client")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),a("span",{attrs:{class:"token package"}},[t._v("League"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Flysystem"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Filesystem")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),a("span",{attrs:{class:"token package"}},[t._v("League"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Flysystem"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Adapter"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Dropbox")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),a("span",{attrs:{class:"token package"}},[t._v("League"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Flysystem"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Adapter"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Local")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),a("span",{attrs:{class:"token package"}},[t._v("League"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Flysystem"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Cached"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("CachedAdapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),a("span",{attrs:{class:"token package"}},[t._v("League"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Flysystem"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Cached"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Storage"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Adapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n"),a("span",{attrs:{class:"token variable"}},[t._v("$client")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Client")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token single-quoted-string string"}},[t._v("'token'")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{attrs:{class:"token single-quoted-string string"}},[t._v("'app'")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token variable"}},[t._v("$dropbox")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Dropbox")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$client")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{attrs:{class:"token single-quoted-string string"}},[t._v("'prefix'")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n"),a("span",{attrs:{class:"token variable"}},[t._v("$local")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Local")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token single-quoted-string string"}},[t._v("'path'")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token variable"}},[t._v("$cache")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Adapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$local")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{attrs:{class:"token single-quoted-string string"}},[t._v("'file'")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{attrs:{class:"token number"}},[t._v("300")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token variable"}},[t._v("$adapter")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("CachedAdapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$dropbox")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{attrs:{class:"token variable"}},[t._v("$cache")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token variable"}},[t._v("$filesystem")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Filesystem")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$adapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n")])])])},function(){var t=this.$createElement,s=this._self._c||t;return s("h2",{attrs:{id:"stash-caching-setup"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#stash-caching-setup","aria-hidden":"true"}},[this._v("#")]),this._v(" Stash Caching Setup")])},function(){var t=this,s=t.$createElement,a=t._self._c||s;return a("div",{staticClass:"language-php extra-class"},[a("pre",{pre:!0,attrs:{class:"language-php"}},[a("code",[a("span",{attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),a("span",{attrs:{class:"token package"}},[t._v("Stash"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Pool")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),a("span",{attrs:{class:"token package"}},[t._v("League"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Flysystem"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Adapter"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Local")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("as")]),t._v(" Adapter"),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),a("span",{attrs:{class:"token package"}},[t._v("League"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Flysystem"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Cached"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("CachedAdapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),a("span",{attrs:{class:"token package"}},[t._v("League"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Flysystem"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Cached"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Storage"),a("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Stash")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("as")]),t._v(" Cache"),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n"),a("span",{attrs:{class:"token variable"}},[t._v("$pool")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Pool")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token comment"}},[t._v("// you can optionally pass a driver (recommended, default: in-memory driver)")]),t._v("\n\n"),a("span",{attrs:{class:"token variable"}},[t._v("$cache")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Cache")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$pool")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{attrs:{class:"token single-quoted-string string"}},[t._v("'storageKey'")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{attrs:{class:"token number"}},[t._v("300")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token comment"}},[t._v("// Storage Key and expire time are optional")]),t._v("\n\n"),a("span",{attrs:{class:"token variable"}},[t._v("$adapter")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("CachedAdapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Adapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token constant"}},[t._v("__DIR__")]),a("span",{attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{attrs:{class:"token single-quoted-string string"}},[t._v("'/path/to/root'")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{attrs:{class:"token variable"}},[t._v("$cache")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{attrs:{class:"token variable"}},[t._v("$filesystem")]),t._v(" "),a("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),a("span",{attrs:{class:"token keyword"}},[t._v("new")]),t._v(" "),a("span",{attrs:{class:"token class-name"}},[t._v("Filesystem")]),a("span",{attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{attrs:{class:"token variable"}},[t._v("$adapter")]),a("span",{attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n")])])])}],!1,null,null,null);s.default=e.exports}}]);