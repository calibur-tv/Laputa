(window.webpackJsonp=window.webpackJsonp||[]).push([[13],{165:function(t,a,s){"use strict";s.r(a);var e=s(0),n=Object(e.a)({},function(){var t=this,a=t.$createElement,s=t._self._c||a;return s("div",{staticClass:"content"},[s("p",[s("router-link",{attrs:{to:"/"}},[t._v("产品需求文档")])],1),t._v(" "),t._m(0),t._v(" "),t._m(1),t._v(" "),s("ol",[s("li",[t._v("接口的"),s("code",[t._v("host")]),t._v("在 staging 环境是："),s("a",{attrs:{href:"https://t-api.calibur.tv",target:"_blank",rel:"noopener noreferrer"}},[t._v("https://t-api.calibur.tv"),s("OutboundLink")],1),t._v("，production 下是："),s("a",{attrs:{href:"https://api.calibur.tv",target:"_blank",rel:"noopener noreferrer"}},[t._v("https://api.calibur.tv"),s("OutboundLink")],1)]),t._v(" "),s("li",[t._v("用户认证使用 "),s("a",{attrs:{href:"https://auth0.com/docs/jwt",target:"_blank",rel:"noopener noreferrer"}},[t._v("JWT-Auth"),s("OutboundLink")],1),t._v("，需要用户认证的接口必须携带指定的 header 字段，例："),t._m(2)]),t._v(" "),t._m(3),t._v(" "),s("li",[t._v("接口的返回统一如下："),t._m(4),t._v("无论请求成功或失败都会返回上述结构的数据，特别说明：\n"),s("blockquote",[s("ol",[s("li",[t._v("请求成功时，code 是 0，其它失败时，是 "),s("a",{attrs:{href:"https://baike.baidu.com/item/HTTP%E7%8A%B6%E6%80%81%E7%A0%81/5053660?fr=aladdin&fromid=11296236&fromtitle=HTTP+Status+Code",target:"_blank",rel:"noopener noreferrer"}},[t._v("http status code"),s("OutboundLink")],1),t._v(" "),s("br")]),t._v(" "),t._m(5),t._v(" "),s("li",[t._v("data 在返回成功时，会是任意可能的类型，返回失败时，是错误信息字符串")])])])]),t._v(" "),t._m(6)]),t._v(" "),t._m(7),t._v(" "),t._m(8)])},[function(){var t=this.$createElement,a=this._self._c||t;return a("h4",{attrs:{id:"calibur-tv-接口文档"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#calibur-tv-接口文档","aria-hidden":"true"}},[this._v("#")]),this._v(" calibur.tv 接口文档")])},function(){var t=this.$createElement,a=this._self._c||t;return a("blockquote",[a("h5",{attrs:{id:"全局配置"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#全局配置","aria-hidden":"true"}},[this._v("#")]),this._v(" 全局配置")])])},function(){var t=this,a=t.$createElement,s=t._self._c||a;return s("div",{staticClass:"language-javascript extra-class"},[s("pre",{pre:!0,attrs:{class:"language-javascript"}},[s("code",[t._v("requestHeader "),s("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),s("span",{attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n  "),s("span",{attrs:{class:"token string"}},[t._v("'Authorization'")]),s("span",{attrs:{class:"token punctuation"}},[t._v(":")]),t._v(" "),s("span",{attrs:{class:"token string"}},[t._v("'Bearer '")]),t._v(" "),s("span",{attrs:{class:"token operator"}},[t._v("+")]),t._v(" "),s("span",{attrs:{class:"token string"}},[t._v("'userTokenString'")]),t._v("\n"),s("span",{attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n")])])])},function(){var t=this,a=t.$createElement,s=t._self._c||a;return s("li",[t._v("移动端在发请求的时候，要额外携带以下参数做校验："),s("div",{staticClass:"language-javascript extra-class"},[s("pre",{pre:!0,attrs:{class:"language-javascript"}},[s("code",[t._v("requestHeader "),s("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),s("span",{attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n  "),s("span",{attrs:{class:"token string"}},[t._v("'Accept'")]),s("span",{attrs:{class:"token punctuation"}},[t._v(":")]),t._v(" "),s("span",{attrs:{class:"token string"}},[t._v("'application/x.api.latest+json'")]),t._v("\n"),s("span",{attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n")])])]),s("code",[t._v("Accept")]),t._v("中的"),s("code",[t._v("latest")]),t._v("要替换为当前 API 的版本号，如 v1，v2...")])},function(){var t=this,a=t.$createElement,s=t._self._c||a;return s("div",{staticClass:"language-javascript extra-class"},[s("pre",{pre:!0,attrs:{class:"language-javascript"}},[s("code",[t._v("response "),s("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),s("span",{attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n code"),s("span",{attrs:{class:"token punctuation"}},[t._v(":")]),t._v(" "),s("span",{attrs:{class:"token function"}},[t._v("responseCode")]),s("span",{attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{attrs:{class:"token string"}},[t._v("'type:integer'")]),s("span",{attrs:{class:"token punctuation"}},[t._v(")")]),s("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v("\n data"),s("span",{attrs:{class:"token punctuation"}},[t._v(":")]),t._v(" "),s("span",{attrs:{class:"token function"}},[t._v("responseData")]),s("span",{attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{attrs:{class:"token string"}},[t._v("'type:any'")]),s("span",{attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n"),s("span",{attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n")])])])},function(){var t=this.$createElement,a=this._self._c||t;return a("li",[this._v("response 的 status code 在 code === 0 时是 200，其它时候与 response.code 一致 "),a("br")])},function(){var t=this.$createElement,a=this._self._c||t;return a("li",[this._v("接口超时设置为"),a("code",[this._v("10s")]),this._v("，如果超时则显示预定义的错误")])},function(){var t=this.$createElement,a=this._self._c||t;return a("h4",{attrs:{id:"接口列表"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#接口列表","aria-hidden":"true"}},[this._v("#")]),this._v(" 接口列表")])},function(){var t=this.$createElement,a=this._self._c||t;return a("h5",{attrs:{id:"v1版接口"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#v1版接口","aria-hidden":"true"}},[this._v("#")]),this._v(" "),a("a",{attrs:{href:"/api/v1/index"}},[this._v("v1版接口")])])}],!1,null,null,null);a.default=n.exports}}]);