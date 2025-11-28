"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["resources_js_Pages_Dashboard_vue"],{

/***/ "./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Layouts/AppLayout.vue?vue&type=style&index=0&id=5663af57&lang=css":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Layouts/AppLayout.vue?vue&type=style&index=0&id=5663af57&lang=css ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__);
// Imports

var ___CSS_LOADER_EXPORT___ = _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default()(function(i){return i[1]});
___CSS_LOADER_EXPORT___.push([module.id, "@import url(https://cdn.lineicons.com/4.0/lineicons.css);"]);
___CSS_LOADER_EXPORT___.push([module.id, "@import url(https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css);"]);
// Module
___CSS_LOADER_EXPORT___.push([module.id, "\n/* Incluir LineIcons y FontAwesome desde CDN */\n\n/* Estilos del Layout - Copiados de la versión anterior */\n* {\n    margin: 0;\n    padding: 0;\n    box-sizing: border-box;\n}\nbody {\n    font-family: 'Inter', sans-serif;\n    background-color: #f8f9fa;\n}\n.app-layout {\n    display: flex;\n    min-height: 100vh;\n}\n\n/* Estilos del Sidebar */\n.sidebar-nav-wrapper {\n    position: fixed;\n    top: 0;\n    left: 0;\n    width: 280px;\n    height: 100vh;\n    background-color: #4a1c1c !important;\n    color: white;\n    transition: all 0.3s ease;\n    z-index: 1000;\n    overflow-y: auto;\n}\n.sidebar-nav-wrapper.collapsed {\n    transform: translateX(-280px);\n}\n.navbar-logo {\n    padding: 20px;\n    text-align: center;\n    border-bottom: 1px solid rgba(255, 255, 255, 0.1);\n}\n.navbar-logo .logo-text {\n    display: flex;\n    align-items: center;\n    justify-content: center;\n    text-decoration: none;\n    padding: 10px;\n    transition: all 0.3s ease;\n}\n.navbar-logo .logo-icon {\n    font-size: 24px;\n    margin-right: 10px;\n}\n.navbar-logo .brand-text {\n    color: #ff6633;\n    font-size: 24px;\n    font-weight: 700;\n    text-transform: uppercase;\n    letter-spacing: 1px;\n    font-family: 'Arial', sans-serif;\n    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);\n}\n.navbar-logo .logo-text:hover .brand-text {\n    color: #ff8855;\n    transform: scale(1.05);\n}\n.navbar-logo .logo-text:hover .logo-icon {\n    transform: rotate(15deg);\n}\n.sidebar-nav ul {\n    list-style: none;\n    padding: 0;\n    margin: 0;\n}\n.sidebar-nav ul li a {\n    color: rgba(255, 255, 255, 0.8) !important;\n    padding: 12px 20px;\n    transition: all 0.3s ease;\n    display: flex;\n    align-items: center;\n    text-decoration: none;\n    border-bottom: 1px solid rgba(255, 255, 255, 0.05);\n    cursor: pointer;\n}\n.sidebar-nav ul li a:hover,\n.sidebar-nav ul li.active a {\n    background: rgba(255, 255, 255, 0.1);\n    color: #ff6633 !important;\n    border-radius: 0;\n}\n.sidebar-nav ul li a .icon {\n    width: 20px;\n    margin-right: 10px;\n    display: flex;\n    align-items: center;\n}\n.sidebar-nav ul li a .icon i {\n    color: rgba(255, 255, 255, 0.8);\n    transition: all 0.3s ease;\n    font-size: 16px;\n}\n.sidebar-nav ul li a:hover .icon i,\n.sidebar-nav ul li.active a .icon i {\n    color: #ff6633;\n}\n.sidebar-nav ul li a .text {\n    font-weight: 500;\n    font-size: 14px;\n}\n\n/* Dropdown navigation */\n.dropdown-nav {\n    background: rgba(0, 0, 0, 0.2);\n    max-height: 0;\n    overflow: hidden;\n    transition: max-height 0.3s ease;\n}\n.dropdown-nav.show {\n    max-height: 500px;\n}\n.dropdown-nav li a {\n    padding-left: 50px;\n    font-size: 13px;\n    border-bottom: 1px solid rgba(255, 255, 255, 0.05);\n}\n\n/* Nav item with children */\n.nav-item-has-children > a::after {\n    content: '⌄';\n    margin-left: auto;\n    transition: transform 0.3s ease;\n    font-size: 12px;\n}\n.nav-item-has-children > a[aria-expanded=\"true\"]::after {\n    transform: rotate(180deg);\n}\n\n/* Main wrapper */\n.main-wrapper {\n    margin-left: 280px;\n    width: calc(100% - 280px);\n    transition: all 0.3s ease;\n    display: flex;\n    flex-direction: column;\n    min-height: 100vh;\n}\n.main-wrapper.sidebar-collapsed {\n    margin-left: 0;\n    width: 100%;\n}\n\n/* Header */\n.header {\n    background: #4a1c1c !important;\n    padding: 15px 0;\n    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);\n    position: sticky;\n    top: 0;\n    z-index: 999;\n}\n.header .container-fluid {\n    padding: 0 30px;\n    max-width: none;\n}\n.header .row {\n    display: flex;\n    align-items: center;\n    justify-content: space-between;\n    margin: 0;\n}\n.header .col-lg-5, .header .col-md-5, .header .col-6 {\n    padding: 0;\n}\n.header .col-lg-7, .header .col-md-7 {\n    padding: 0;\n}\n.header-left {\n    display: flex;\n    align-items: center;\n}\n.menu-toggle-btn {\n    margin-right: 20px;\n}\n.main-btn {\n    background: rgba(255, 255, 255, 0.1) !important;\n    border: none !important;\n    color: white !important;\n    padding: 8px 16px !important;\n    border-radius: 6px !important;\n    font-weight: 500 !important;\n    transition: all 0.3s ease !important;\n    font-size: 14px;\n    cursor: pointer;\n}\n.main-btn:hover {\n    background: rgba(255, 255, 255, 0.2) !important;\n    transform: translateY(-1px);\n    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;\n}\n\n/* Profile dropdown */\n.header-right {\n    display: flex;\n    justify-content: flex-end;\n    align-items: center;\n}\n.profile-box {\n    position: relative;\n    margin-left: 15px;\n}\n.profile-box button {\n    color: white !important;\n    background: transparent !important;\n    border: none !important;\n    display: flex;\n    align-items: center;\n    padding: 5px 10px;\n    border-radius: 6px;\n    transition: all 0.3s ease;\n    cursor: pointer;\n}\n.profile-box button:hover {\n    background: rgba(255, 255, 255, 0.1) !important;\n}\n.profile-box .info h6 {\n    color: white !important;\n    margin: 0;\n    font-size: 14px;\n    font-weight: 500;\n}\n.profile-box .profile-info {\n    margin-right: 8px;\n}\n.dropdown-menu {\n    background: white;\n    border: none;\n    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);\n    border-radius: 8px;\n    padding: 8px 0;\n    position: absolute;\n    top: 100%;\n    right: 0;\n    min-width: 180px;\n    opacity: 0;\n    visibility: hidden;\n    transform: translateY(-10px);\n    transition: all 0.3s ease;\n    list-style: none;\n    margin: 0;\n}\n.dropdown-menu.show {\n    opacity: 1;\n    visibility: visible;\n    transform: translateY(5px);\n}\n.dropdown-menu li {\n    margin: 0;\n}\n.dropdown-menu li a {\n    color: #4a1c1c !important;\n    transition: all 0.3s ease;\n    padding: 10px 20px;\n    display: flex;\n    align-items: center;\n    text-decoration: none;\n    font-size: 14px;\n    width: 100%;\n}\n.dropdown-menu li a:hover {\n    background: #ff6633;\n    color: white !important;\n}\n.dropdown-menu li a i {\n    color: #4a1c1c;\n    margin-right: 8px;\n    font-size: 16px;\n    width: 20px;\n}\n.dropdown-menu li a:hover i {\n    color: white;\n}\n\n/* Section */\n.section {\n    background-color: #f8f9fa;\n    flex: 1;\n    padding: 30px 0;\n}\n.section .container-fluid {\n    padding: 0 30px;\n    max-width: none;\n}\n\n/* Footer */\n.footer {\n    background: white;\n    border-top: 1px solid #e5e7eb;\n    padding: 20px 0;\n    margin-top: auto;\n}\n.footer .container-fluid {\n    padding: 0 30px;\n    max-width: none;\n}\n.footer .row {\n    display: flex;\n    align-items: center;\n    margin: 0;\n}\n.footer .copyright {\n    margin: 0;\n}\n.footer .text-sm {\n    color: #6b7280;\n    margin: 0;\n    font-size: 13px;\n}\n\n/* Overlay for mobile */\n.overlay {\n    position: fixed;\n    top: 0;\n    left: 0;\n    width: 100%;\n    height: 100%;\n    background: rgba(0, 0, 0, 0.5);\n    z-index: 999;\n}\n\n/* Alert styles */\n.alert {\n    padding: 12px 16px;\n    margin-bottom: 20px;\n    border: 1px solid transparent;\n    border-radius: 6px;\n    font-size: 14px;\n}\n.alert-success {\n    background-color: #d1fae5;\n    border-color: #10b981;\n    color: #065f46;\n}\n.alert-danger {\n    background-color: #fee2e2;\n    border-color: #ef4444;\n    color: #991b1b;\n}\n.alert-warning {\n    background-color: #fef3c7;\n    border-color: #f59e0b;\n    color: #92400e;\n}\n.alert-info {\n    background-color: #dbeafe;\n    border-color: #3b82f6;\n    color: #1e40af;\n}\n\n/* Responsive */\n@media (max-width: 768px) {\n.sidebar-nav-wrapper {\n        transform: translateX(-280px);\n}\n.sidebar-nav-wrapper:not(.collapsed) {\n        transform: translateX(0);\n}\n.main-wrapper {\n        margin-left: 0;\n        width: 100%;\n}\n.header .container-fluid {\n        padding: 0 15px;\n}\n.section .container-fluid {\n        padding: 0 15px;\n}\n.footer .container-fluid {\n        padding: 0 15px;\n}\n.section {\n        padding: 20px 0;\n}\n}\n\n/* Utility classes */\n.d-flex {\n    display: flex !important;\n}\n.align-items-center {\n    align-items: center !important;\n}\n.justify-content-between {\n    justify-content: space-between !important;\n}\n.justify-content-end {\n    justify-content: flex-end !important;\n}\n.mr-20 {\n    margin-right: 20px !important;\n}\n.ml-15 {\n    margin-left: 15px !important;\n}\n.me-2 {\n    margin-right: 8px !important;\n}\n.mb-4 {\n    margin-bottom: 1.5rem !important;\n}\n.text-md-start {\n    text-align: start !important;\n}\n.order-last {\n    order: 9999 !important;\n}\n.order-md-first {\n    order: -1 !important;\n}\n\n/* Bootstrap grid simulation */\n.col-md-6 {\n    width: 50%;\n}\n@media (max-width: 768px) {\n.col-md-6 {\n        width: 100%;\n}\n.order-last {\n        order: 9999 !important;\n}\n}\n", ""]);
// Exports
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (___CSS_LOADER_EXPORT___);


/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Pages/Dashboard.vue?vue&type=style&index=0&id=097ba13b&scoped=true&lang=css":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Pages/Dashboard.vue?vue&type=style&index=0&id=097ba13b&scoped=true&lang=css ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__);
// Imports

var ___CSS_LOADER_EXPORT___ = _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default()(function(i){return i[1]});
// Module
___CSS_LOADER_EXPORT___.push([module.id, "\n.page-title[data-v-097ba13b] {\n    color: #4a1c1c;\n    font-size: 28px;\n    font-weight: 600;\n    margin-bottom: 30px;\n}\n.pt-30[data-v-097ba13b] {\n    padding-top: 30px;\n}\n.stat-widget[data-v-097ba13b] {\n    border: none;\n    border-radius: 10px;\n    margin-bottom: 20px;\n    position: relative;\n    overflow: hidden;\n}\n.stat-widget.bg-primary[data-v-097ba13b] {\n    background: linear-gradient(135deg, #4a1c1c 0%, #6d2b2b 100%) !important;\n}\n.stat-widget.bg-success[data-v-097ba13b] {\n    background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;\n}\n.stat-widget.bg-warning[data-v-097ba13b] {\n    background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%) !important;\n}\n.stat-widget .card-body[data-v-097ba13b] {\n    padding: 25px;\n    position: relative;\n}\n.stat-widget h2[data-v-097ba13b] {\n    font-size: 2.5rem;\n    font-weight: 700;\n    margin: 10px 0;\n}\n.stat-widget h5[data-v-097ba13b] {\n    font-size: 1rem;\n    font-weight: 500;\n    margin-bottom: 5px;\n}\n.stat-icon[data-v-097ba13b] {\n    position: absolute;\n    right: 20px;\n    top: 50%;\n    transform: translateY(-50%);\n    font-size: 3rem;\n    opacity: 0.3;\n}\n.card[data-v-097ba13b] {\n    background: white;\n    border-radius: 10px;\n    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);\n    margin-bottom: 20px;\n    border: none;\n}\n.card-header[data-v-097ba13b] {\n    background: white !important;\n    border-bottom: 1px solid #e5e7eb;\n    padding: 20px;\n    font-weight: 600;\n    color: #4a1c1c;\n    border-radius: 10px 10px 0 0 !important;\n}\n.card-body[data-v-097ba13b] {\n    padding: 20px;\n}\n.quick-action-card[data-v-097ba13b] {\n    background: #f8f9fa;\n    border-radius: 10px;\n    transition: all 0.3s ease;\n    text-decoration: none;\n    color: inherit;\n    border: 1px solid #e9ecef;\n    height: 100%;\n    min-height: 140px;\n}\n.quick-action-card[data-v-097ba13b]:hover {\n    background: #4a1c1c;\n    color: white !important;\n    transform: translateY(-2px);\n    box-shadow: 0 8px 25px rgba(74, 28, 28, 0.15);\n    text-decoration: none;\n}\n.action-icon[data-v-097ba13b] {\n    width: 60px;\n    height: 60px;\n    background: rgba(74, 28, 28, 0.1);\n    border-radius: 50%;\n    display: flex;\n    align-items: center;\n    justify-content: center;\n    transition: all 0.3s ease;\n}\n.action-icon i[data-v-097ba13b] {\n    font-size: 24px;\n    color: #4a1c1c;\n    transition: all 0.3s ease;\n}\n.quick-action-card:hover .action-icon[data-v-097ba13b] {\n    background: rgba(255, 255, 255, 0.2);\n}\n.quick-action-card:hover .action-icon i[data-v-097ba13b] {\n    color: white;\n}\n.quick-action-card h6[data-v-097ba13b] {\n    font-weight: 600;\n    margin-bottom: 5px;\n    transition: all 0.3s ease;\n}\n.quick-action-card small[data-v-097ba13b] {\n    font-size: 12px;\n    opacity: 0.8;\n}\n\n/* Bootstrap grid classes */\n.container-fluid[data-v-097ba13b] {\n    width: 100%;\n    padding: 0;\n}\n.row[data-v-097ba13b] {\n    display: flex;\n    flex-wrap: wrap;\n    margin: 0 -15px;\n}\n.col-12[data-v-097ba13b] {\n    width: 100%;\n    padding: 0 15px;\n}\n.col-xl-4[data-v-097ba13b] {\n    width: 33.333333%;\n    padding: 0 15px;\n}\n.col-lg-6[data-v-097ba13b] {\n    width: 50%;\n    padding: 0 15px;\n}\n.col-sm-6[data-v-097ba13b] {\n    width: 50%;\n    padding: 0 15px;\n}\n.col-md-3[data-v-097ba13b] {\n    width: 25%;\n    padding: 0 15px;\n}\n@media (max-width: 768px) {\n.col-sm-6[data-v-097ba13b] {\n        width: 100%;\n        margin-bottom: 15px;\n}\n.col-md-3[data-v-097ba13b] {\n        width: 50%;\n        margin-bottom: 15px;\n}\n}\n@media (max-width: 576px) {\n.col-md-3[data-v-097ba13b] {\n        width: 100%;\n}\n}\n\n/* Utility classes */\n.text-center[data-v-097ba13b] {\n    text-align: center !important;\n}\n.text-white[data-v-097ba13b] {\n    color: white !important;\n}\n.text-muted[data-v-097ba13b] {\n    color: #6c757d !important;\n}\n.mb-0[data-v-097ba13b] {\n    margin-bottom: 0 !important;\n}\n.mb-3[data-v-097ba13b] {\n    margin-bottom: 1rem !important;\n}\n.mb-4[data-v-097ba13b] {\n    margin-bottom: 1.5rem !important;\n}\n.p-4[data-v-097ba13b] {\n    padding: 1.5rem !important;\n}\n.flex-1[data-v-097ba13b] {\n    flex: 1 1 0% !important;\n}\n.d-flex[data-v-097ba13b] {\n    display: flex !important;\n}\n.flex-column[data-v-097ba13b] {\n    flex-direction: column !important;\n}\n.align-items-center[data-v-097ba13b] {\n    align-items: center !important;\n}\n.justify-content-center[data-v-097ba13b] {\n    justify-content: center !important;\n}\n", ""]);
// Exports
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (___CSS_LOADER_EXPORT___);


/***/ }),

/***/ "./node_modules/css-loader/dist/runtime/api.js":
/*!*****************************************************!*\
  !*** ./node_modules/css-loader/dist/runtime/api.js ***!
  \*****************************************************/
/***/ ((module) => {



/*
  MIT License http://www.opensource.org/licenses/mit-license.php
  Author Tobias Koppers @sokra
*/
// css base code, injected by the css-loader
// eslint-disable-next-line func-names
module.exports = function (cssWithMappingToString) {
  var list = []; // return the list of modules as css string

  list.toString = function toString() {
    return this.map(function (item) {
      var content = cssWithMappingToString(item);

      if (item[2]) {
        return "@media ".concat(item[2], " {").concat(content, "}");
      }

      return content;
    }).join("");
  }; // import a list of modules into the list
  // eslint-disable-next-line func-names


  list.i = function (modules, mediaQuery, dedupe) {
    if (typeof modules === "string") {
      // eslint-disable-next-line no-param-reassign
      modules = [[null, modules, ""]];
    }

    var alreadyImportedModules = {};

    if (dedupe) {
      for (var i = 0; i < this.length; i++) {
        // eslint-disable-next-line prefer-destructuring
        var id = this[i][0];

        if (id != null) {
          alreadyImportedModules[id] = true;
        }
      }
    }

    for (var _i = 0; _i < modules.length; _i++) {
      var item = [].concat(modules[_i]);

      if (dedupe && alreadyImportedModules[item[0]]) {
        // eslint-disable-next-line no-continue
        continue;
      }

      if (mediaQuery) {
        if (!item[2]) {
          item[2] = mediaQuery;
        } else {
          item[2] = "".concat(mediaQuery, " and ").concat(item[2]);
        }
      }

      list.push(item);
    }
  };

  return list;
};

/***/ }),

/***/ "./node_modules/laravel-mix/node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Layouts/AppLayout.vue?vue&type=script&setup=true&lang=js":
/*!************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/laravel-mix/node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Layouts/AppLayout.vue?vue&type=script&setup=true&lang=js ***!
  \************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _inertiajs_vue3__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @inertiajs/vue3 */ "./node_modules/@inertiajs/vue3/dist/index.esm.js");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  __name: 'AppLayout',
  setup: function setup(__props, _ref) {
    var __expose = _ref.expose;
    __expose();
    var page = (0,_inertiajs_vue3__WEBPACK_IMPORTED_MODULE_0__.usePage)();
    var sidebarOpen = (0,vue__WEBPACK_IMPORTED_MODULE_1__.ref)(true);
    var profileDropdownOpen = (0,vue__WEBPACK_IMPORTED_MODULE_1__.ref)(false);
    var activeSubmenu = (0,vue__WEBPACK_IMPORTED_MODULE_1__.ref)(null);
    var isMobile = (0,vue__WEBPACK_IMPORTED_MODULE_1__.ref)(false);
    var toggleSidebar = function toggleSidebar() {
      sidebarOpen.value = !sidebarOpen.value;
    };
    var toggleSubmenu = function toggleSubmenu(submenu) {
      if (activeSubmenu.value === submenu) {
        activeSubmenu.value = null;
      } else {
        activeSubmenu.value = submenu;
      }
    };
    var isUrlActive = function isUrlActive(url) {
      if (!url) return false;
      var currentPath = window.location.pathname;
      if (url === '/home' && currentPath === '/home') return true;
      if (url !== '/home' && currentPath.startsWith(url)) return true;
      return false;
    };
    var isMenuActive = function isMenuActive(menu) {
      if (!menu.hijos || menu.hijos.length === 0) {
        return isUrlActive(menu.url);
      }
      return menu.hijos.some(function (submenu) {
        return isUrlActive(submenu.url);
      });
    };
    var checkScreenSize = function checkScreenSize() {
      isMobile.value = window.innerWidth <= 768;
      if (isMobile.value) {
        sidebarOpen.value = false;
      }
    };
    var handleClickOutside = function handleClickOutside(event) {
      if (!event.target.closest('.profile-box')) {
        profileDropdownOpen.value = false;
      }
    };
    (0,vue__WEBPACK_IMPORTED_MODULE_1__.onMounted)(function () {
      checkScreenSize();
      window.addEventListener('resize', checkScreenSize);
      document.addEventListener('click', handleClickOutside);

      // Auto-expandir menú activo basado en la URL actual
      var currentUrl = window.location.pathname;
      var menus = page.props.menus;
      if (menus) {
        menus.forEach(function (menu) {
          if (menu.hijos && menu.hijos.length > 0) {
            var hasActiveChild = menu.hijos.some(function (submenu) {
              return isUrlActive(submenu.url);
            });
            if (hasActiveChild) {
              activeSubmenu.value = menu.id_menu;
            }
          }
        });
      }
    });
    (0,vue__WEBPACK_IMPORTED_MODULE_1__.onUnmounted)(function () {
      window.removeEventListener('resize', checkScreenSize);
      document.removeEventListener('click', handleClickOutside);
    });
    var __returned__ = {
      page: page,
      sidebarOpen: sidebarOpen,
      profileDropdownOpen: profileDropdownOpen,
      activeSubmenu: activeSubmenu,
      isMobile: isMobile,
      toggleSidebar: toggleSidebar,
      toggleSubmenu: toggleSubmenu,
      isUrlActive: isUrlActive,
      isMenuActive: isMenuActive,
      checkScreenSize: checkScreenSize,
      handleClickOutside: handleClickOutside,
      get Link() {
        return _inertiajs_vue3__WEBPACK_IMPORTED_MODULE_0__.Link;
      },
      ref: vue__WEBPACK_IMPORTED_MODULE_1__.ref,
      onMounted: vue__WEBPACK_IMPORTED_MODULE_1__.onMounted,
      onUnmounted: vue__WEBPACK_IMPORTED_MODULE_1__.onUnmounted,
      get usePage() {
        return _inertiajs_vue3__WEBPACK_IMPORTED_MODULE_0__.usePage;
      }
    };
    Object.defineProperty(__returned__, '__isScriptSetup', {
      enumerable: false,
      value: true
    });
    return __returned__;
  }
});

/***/ }),

/***/ "./node_modules/laravel-mix/node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Pages/Dashboard.vue?vue&type=script&setup=true&lang=js":
/*!**********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/laravel-mix/node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Pages/Dashboard.vue?vue&type=script&setup=true&lang=js ***!
  \**********************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _inertiajs_vue3__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @inertiajs/vue3 */ "./node_modules/@inertiajs/vue3/dist/index.esm.js");
/* harmony import */ var _Layouts_AppLayout_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @/Layouts/AppLayout.vue */ "./resources/js/Layouts/AppLayout.vue");


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  __name: 'Dashboard',
  props: {
    stats: {
      type: Object,
      "default": function _default() {
        return {};
      }
    }
  },
  setup: function setup(__props, _ref) {
    var __expose = _ref.expose;
    __expose();
    var props = __props;
    var __returned__ = {
      props: props,
      get Link() {
        return _inertiajs_vue3__WEBPACK_IMPORTED_MODULE_0__.Link;
      },
      AppLayout: _Layouts_AppLayout_vue__WEBPACK_IMPORTED_MODULE_1__["default"]
    };
    Object.defineProperty(__returned__, '__isScriptSetup', {
      enumerable: false,
      value: true
    });
    return __returned__;
  }
});

/***/ }),

/***/ "./node_modules/laravel-mix/node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Layouts/AppLayout.vue?vue&type=template&id=5663af57":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/laravel-mix/node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Layouts/AppLayout.vue?vue&type=template&id=5663af57 ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }

var _hoisted_1 = {
  "class": "app-layout"
};
var _hoisted_2 = {
  "class": "navbar-logo"
};
var _hoisted_3 = {
  "class": "sidebar-nav"
};
var _hoisted_4 = ["onClick", "aria-expanded"];
var _hoisted_5 = {
  "class": "icon"
};
var _hoisted_6 = {
  "class": "text"
};
var _hoisted_7 = {
  "class": "icon"
};
var _hoisted_8 = {
  "class": "text"
};
var _hoisted_9 = {
  "class": "header"
};
var _hoisted_10 = {
  "class": "container-fluid"
};
var _hoisted_11 = {
  "class": "row"
};
var _hoisted_12 = {
  "class": "col-lg-7 col-md-7 col-6"
};
var _hoisted_13 = {
  "class": "header-right"
};
var _hoisted_14 = {
  "class": "profile-box ml-15"
};
var _hoisted_15 = {
  "class": "profile-info"
};
var _hoisted_16 = {
  "class": "info"
};
var _hoisted_17 = {
  key: 0
};
var _hoisted_18 = {
  "class": "section"
};
var _hoisted_19 = {
  "class": "container-fluid"
};
var _hoisted_20 = {
  key: 0,
  "class": "alert alert-success alert-dismissible mb-4"
};
var _hoisted_21 = {
  key: 1,
  "class": "alert alert-danger alert-dismissible mb-4"
};
var _hoisted_22 = {
  key: 2,
  "class": "alert alert-warning alert-dismissible mb-4"
};
var _hoisted_23 = {
  key: 3,
  "class": "alert alert-info alert-dismissible mb-4"
};
var _hoisted_24 = {
  "class": "footer"
};
var _hoisted_25 = {
  "class": "container-fluid"
};
var _hoisted_26 = {
  "class": "row"
};
var _hoisted_27 = {
  "class": "col-md-6 order-last order-md-first"
};
var _hoisted_28 = {
  "class": "copyright text-md-start"
};
var _hoisted_29 = {
  "class": "text-sm"
};
function render(_ctx, _cache, $props, $setup, $data, $options) {
  var _ctx$$page$props$auth, _ctx$$page$props$flas, _ctx$$page$props$flas2, _ctx$$page$props$flas3, _ctx$$page$props$flas4;
  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_1, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" ======== sidebar-nav start =========== "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("aside", {
    "class": (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)(["sidebar-nav-wrapper", {
      'collapsed': !$setup.sidebarOpen
    }])
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_2, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["Link"], {
    href: "/home",
    "class": "logo-text"
  }, {
    "default": (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(function () {
      return _toConsumableArray(_cache[4] || (_cache[4] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", {
        "class": "logo-icon"
      }, "🔥", -1 /* CACHED */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", {
        "class": "brand-text"
      }, "CHURRASQUERIA ROBERTO", -1 /* CACHED */)]));
    }),
    _: 1 /* STABLE */
  })]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("nav", _hoisted_3, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("ul", null, [((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(_ctx.$page.props.menus, function (menu) {
    return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, {
      key: menu.id_menu
    }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" Menú con submenús "), menu.hijos && menu.hijos.length > 0 ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("li", {
      key: 0,
      "class": (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)(["nav-item nav-item-has-children", {
        'active': $setup.isMenuActive(menu)
      }])
    }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
      href: "#",
      onClick: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withModifiers)(function ($event) {
        return $setup.toggleSubmenu(menu.id_menu);
      }, ["prevent"]),
      "aria-expanded": $setup.activeSubmenu === menu.id_menu
    }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", _hoisted_5, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
      "class": (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)(menu.icono || 'lni lni-circle')
    }, null, 2 /* CLASS */)]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", _hoisted_6, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(menu.nombre), 1 /* TEXT */)], 8 /* PROPS */, _hoisted_4), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("ul", {
      "class": (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)(["dropdown-nav", {
        'show': $setup.activeSubmenu === menu.id_menu
      }])
    }, [((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(menu.hijos, function (submenu) {
      return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("li", {
        key: submenu.id_menu
      }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["Link"], {
        href: submenu.url || '#',
        "class": (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)({
          'active': $setup.isUrlActive(submenu.url)
        })
      }, {
        "default": (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(function () {
          return [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
            "class": (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)([submenu.icono || 'lni lni-circle', "me-2"])
          }, null, 2 /* CLASS */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(" " + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(submenu.nombre), 1 /* TEXT */)];
        }),
        _: 2 /* DYNAMIC */
      }, 1032 /* PROPS, DYNAMIC_SLOTS */, ["href", "class"])]);
    }), 128 /* KEYED_FRAGMENT */))], 2 /* CLASS */)], 2 /* CLASS */)) : ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, {
      key: 1
    }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" Menú simple sin submenús "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("li", {
      "class": (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)(["nav-item", {
        'active': $setup.isUrlActive(menu.url)
      }])
    }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["Link"], {
      href: menu.url || '#'
    }, {
      "default": (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(function () {
        return [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", _hoisted_7, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
          "class": (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)(menu.icono || 'lni lni-circle')
        }, null, 2 /* CLASS */)]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", _hoisted_8, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(menu.nombre), 1 /* TEXT */)];
      }),
      _: 2 /* DYNAMIC */
    }, 1032 /* PROPS, DYNAMIC_SLOTS */, ["href"])], 2 /* CLASS */)], 2112 /* STABLE_FRAGMENT, DEV_ROOT_FRAGMENT */))], 64 /* STABLE_FRAGMENT */);
  }), 128 /* KEYED_FRAGMENT */))])])], 2 /* CLASS */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
    "class": "overlay",
    onClick: $setup.toggleSidebar
  }, null, 512 /* NEED_PATCH */), [[vue__WEBPACK_IMPORTED_MODULE_0__.vShow, $setup.sidebarOpen && $setup.isMobile]]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" ======== sidebar-nav end =========== "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" ======== main-wrapper start =========== "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("main", {
    "class": (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)(["main-wrapper", {
      'sidebar-collapsed': !$setup.sidebarOpen
    }])
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" ========== header start ========== "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("header", _hoisted_9, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_10, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_11, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
    "class": "col-lg-5 col-md-5 col-6"
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
    "class": "header-left d-flex align-items-center"
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
    "class": "menu-toggle-btn mr-20"
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("button", {
    onClick: $setup.toggleSidebar,
    "class": "main-btn primary-btn btn-hover"
  }, _toConsumableArray(_cache[5] || (_cache[5] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": "lni lni-chevron-left me-2"
  }, null, -1 /* CACHED */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(" Menú ", -1 /* CACHED */)])))])])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_12, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_13, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" profile start "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_14, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("button", {
    "class": "dropdown-toggle bg-transparent border-0",
    type: "button",
    onClick: _cache[0] || (_cache[0] = function ($event) {
      return $setup.profileDropdownOpen = !$setup.profileDropdownOpen;
    })
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_15, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_16, [(_ctx$$page$props$auth = _ctx.$page.props.auth) !== null && _ctx$$page$props$auth !== void 0 && _ctx$$page$props$auth.user ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("h6", _hoisted_17, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(_ctx.$page.props.auth.user.nombre) + " " + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(_ctx.$page.props.auth.user.apellido), 1 /* TEXT */)) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)])]), _cache[6] || (_cache[6] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": "lni lni-chevron-down"
  }, null, -1 /* CACHED */))]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("ul", {
    "class": (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)(["dropdown-menu dropdown-menu-end", {
      'show': $setup.profileDropdownOpen
    }]),
    onClick: _cache[3] || (_cache[3] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.withModifiers)(function () {}, ["stop"]))
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("li", null, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["Link"], {
    href: "/profile",
    onClick: _cache[1] || (_cache[1] = function ($event) {
      return $setup.profileDropdownOpen = false;
    })
  }, {
    "default": (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(function () {
      return _toConsumableArray(_cache[7] || (_cache[7] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
        "class": "lni lni-user"
      }, null, -1 /* CACHED */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(" Mi Perfil ", -1 /* CACHED */)]));
    }),
    _: 1 /* STABLE */
  })]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("li", null, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["Link"], {
    href: "/logout",
    method: "post",
    onClick: _cache[2] || (_cache[2] = function ($event) {
      return $setup.profileDropdownOpen = false;
    })
  }, {
    "default": (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(function () {
      return _toConsumableArray(_cache[8] || (_cache[8] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
        "class": "lni lni-exit"
      }, null, -1 /* CACHED */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(" Cerrar Sesión ", -1 /* CACHED */)]));
    }),
    _: 1 /* STABLE */
  })])], 2 /* CLASS */)]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" profile end ")])])])])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" ========== header end ========== "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" ========== section start ========== "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("section", _hoisted_18, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_19, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" Flash Messages "), (_ctx$$page$props$flas = _ctx.$page.props.flash) !== null && _ctx$$page$props$flas !== void 0 && _ctx$$page$props$flas.success ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_20, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(_ctx.$page.props.flash.success), 1 /* TEXT */)) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), (_ctx$$page$props$flas2 = _ctx.$page.props.flash) !== null && _ctx$$page$props$flas2 !== void 0 && _ctx$$page$props$flas2.error ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_21, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(_ctx.$page.props.flash.error), 1 /* TEXT */)) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), (_ctx$$page$props$flas3 = _ctx.$page.props.flash) !== null && _ctx$$page$props$flas3 !== void 0 && _ctx$$page$props$flas3.warning ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_22, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(_ctx.$page.props.flash.warning), 1 /* TEXT */)) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), (_ctx$$page$props$flas4 = _ctx.$page.props.flash) !== null && _ctx$$page$props$flas4 !== void 0 && _ctx$$page$props$flas4.info ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_23, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(_ctx.$page.props.flash.info), 1 /* TEXT */)) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderSlot)(_ctx.$slots, "default")])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" ========== section end ========== "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" ========== footer start =========== "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("footer", _hoisted_24, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_25, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_26, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_27, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_28, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("p", _hoisted_29, " Sistema de Gestión - CHURRASQUERIA ROBERTO Restaurant © " + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(new Date().getFullYear()), 1 /* TEXT */)])])])])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" ========== footer end =========== ")], 2 /* CLASS */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" ======== main-wrapper end =========== ")]);
}

/***/ }),

/***/ "./node_modules/laravel-mix/node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Pages/Dashboard.vue?vue&type=template&id=097ba13b&scoped=true":
/*!***************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/laravel-mix/node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Pages/Dashboard.vue?vue&type=template&id=097ba13b&scoped=true ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }

var _hoisted_1 = {
  "class": "container-fluid"
};
var _hoisted_2 = {
  "class": "row"
};
var _hoisted_3 = {
  "class": "col-12"
};
var _hoisted_4 = {
  "class": "card"
};
var _hoisted_5 = {
  "class": "card-body"
};
var _hoisted_6 = {
  key: 0,
  "class": "mt-4"
};
var _hoisted_7 = {
  "class": "text-muted"
};
var _hoisted_8 = {
  key: 0,
  "class": "text-muted"
};
var _hoisted_9 = {
  "class": "row"
};
var _hoisted_10 = {
  "class": "col-xl-4 col-lg-6 col-sm-6"
};
var _hoisted_11 = {
  "class": "card stat-widget bg-primary text-white"
};
var _hoisted_12 = {
  "class": "card-body"
};
var _hoisted_13 = {
  "class": "d-flex align-items-center"
};
var _hoisted_14 = {
  "class": "flex-1"
};
var _hoisted_15 = {
  "class": "text-white mb-0"
};
var _hoisted_16 = {
  "class": "col-xl-4 col-lg-6 col-sm-6"
};
var _hoisted_17 = {
  "class": "card stat-widget bg-success text-white"
};
var _hoisted_18 = {
  "class": "card-body"
};
var _hoisted_19 = {
  "class": "d-flex align-items-center"
};
var _hoisted_20 = {
  "class": "flex-1"
};
var _hoisted_21 = {
  "class": "text-white mb-0"
};
var _hoisted_22 = {
  "class": "col-xl-4 col-lg-6 col-sm-6"
};
var _hoisted_23 = {
  "class": "card stat-widget bg-warning text-white"
};
var _hoisted_24 = {
  "class": "card-body"
};
var _hoisted_25 = {
  "class": "d-flex align-items-center"
};
var _hoisted_26 = {
  "class": "flex-1"
};
var _hoisted_27 = {
  "class": "text-white mb-0"
};
var _hoisted_28 = {
  "class": "row"
};
var _hoisted_29 = {
  "class": "col-12"
};
var _hoisted_30 = {
  "class": "card"
};
var _hoisted_31 = {
  "class": "card-body"
};
var _hoisted_32 = {
  "class": "row"
};
var _hoisted_33 = {
  "class": "col-md-3 col-sm-6 mb-3"
};
var _hoisted_34 = {
  "class": "col-md-3 col-sm-6 mb-3"
};
var _hoisted_35 = {
  "class": "col-md-3 col-sm-6 mb-3"
};
var _hoisted_36 = {
  "class": "col-md-3 col-sm-6 mb-3"
};
function render(_ctx, _cache, $props, $setup, $data, $options) {
  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)($setup["AppLayout"], null, {
    "default": (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(function () {
      var _ctx$$page$props$auth;
      return [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" ========== title-wrapper start ========== "), _cache[15] || (_cache[15] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
        "class": "title-wrapper pt-30 text-center"
      }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h2", {
        "class": "page-title"
      }, "Panel de Control")], -1 /* CACHED */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" ========== title-wrapper end ========== "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_1, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_2, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" Welcome Section "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_3, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_4, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_5, [_cache[2] || (_cache[2] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h3", {
        "class": "mb-4"
      }, "¡Bienvenido al Sistema de Gestión!", -1 /* CACHED */)), _cache[3] || (_cache[3] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("p", {
        "class": "text-muted"
      }, " Gestiona tu restaurante de manera eficiente con nuestro sistema integral. ", -1 /* CACHED */)), (_ctx$$page$props$auth = _ctx.$page.props.auth) !== null && _ctx$$page$props$auth !== void 0 && _ctx$$page$props$auth.user ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_6, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("p", _hoisted_7, [_cache[0] || (_cache[0] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(" Usuario: ", -1 /* CACHED */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("strong", null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(_ctx.$page.props.auth.user.nombre) + " " + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(_ctx.$page.props.auth.user.apellido), 1 /* TEXT */)]), _ctx.$page.props.auth.user.rol ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("p", _hoisted_8, [_cache[1] || (_cache[1] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(" Rol: ", -1 /* CACHED */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("strong", null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(_ctx.$page.props.auth.user.rol.nombre), 1 /* TEXT */)])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)])])])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" Stats Cards "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_9, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_10, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_11, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_12, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_13, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_14, [_cache[4] || (_cache[4] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h5", {
        "class": "text-white"
      }, "Insumos", -1 /* CACHED */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h2", _hoisted_15, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.stats.insumos || 0), 1 /* TEXT */)]), _cache[5] || (_cache[5] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
        "class": "stat-icon"
      }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
        "class": "lni lni-package"
      })], -1 /* CACHED */))])])])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_16, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_17, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_18, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_19, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_20, [_cache[6] || (_cache[6] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h5", {
        "class": "text-white"
      }, "Categorías", -1 /* CACHED */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h2", _hoisted_21, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.stats.categorias || 0), 1 /* TEXT */)]), _cache[7] || (_cache[7] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
        "class": "stat-icon"
      }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
        "class": "lni lni-tag"
      })], -1 /* CACHED */))])])])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_22, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_23, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_24, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_25, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_26, [_cache[8] || (_cache[8] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h5", {
        "class": "text-white"
      }, "Mesas", -1 /* CACHED */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h2", _hoisted_27, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.stats.mesas || 0), 1 /* TEXT */)]), _cache[9] || (_cache[9] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
        "class": "stat-icon"
      }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
        "class": "lni lni-restaurant"
      })], -1 /* CACHED */))])])])])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" Quick Actions "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_28, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_29, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_30, [_cache[14] || (_cache[14] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
        "class": "card-header"
      }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h5", {
        "class": "mb-0"
      }, "Acciones Rápidas")], -1 /* CACHED */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_31, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_32, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_33, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["Link"], {
        href: "/categorias",
        "class": "quick-action-card d-flex flex-column align-items-center text-center p-4"
      }, {
        "default": (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(function () {
          return _toConsumableArray(_cache[10] || (_cache[10] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
            "class": "action-icon mb-3"
          }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
            "class": "lni lni-tag"
          })], -1 /* CACHED */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h6", {
            "class": "mb-0"
          }, "Categorías", -1 /* CACHED */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("small", {
            "class": "text-muted"
          }, "Gestionar categorías", -1 /* CACHED */)]));
        }),
        _: 1 /* STABLE */
      })]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_34, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["Link"], {
        href: "/insumos",
        "class": "quick-action-card d-flex flex-column align-items-center text-center p-4"
      }, {
        "default": (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(function () {
          return _toConsumableArray(_cache[11] || (_cache[11] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
            "class": "action-icon mb-3"
          }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
            "class": "lni lni-package"
          })], -1 /* CACHED */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h6", {
            "class": "mb-0"
          }, "Insumos", -1 /* CACHED */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("small", {
            "class": "text-muted"
          }, "Gestionar inventario", -1 /* CACHED */)]));
        }),
        _: 1 /* STABLE */
      })]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_35, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["Link"], {
        href: "/recetas",
        "class": "quick-action-card d-flex flex-column align-items-center text-center p-4"
      }, {
        "default": (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(function () {
          return _toConsumableArray(_cache[12] || (_cache[12] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
            "class": "action-icon mb-3"
          }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
            "class": "lni lni-restaurant"
          })], -1 /* CACHED */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h6", {
            "class": "mb-0"
          }, "Recetas", -1 /* CACHED */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("small", {
            "class": "text-muted"
          }, "Gestionar recetas", -1 /* CACHED */)]));
        }),
        _: 1 /* STABLE */
      })]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_36, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["Link"], {
        href: "/ventas",
        "class": "quick-action-card d-flex flex-column align-items-center text-center p-4"
      }, {
        "default": (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(function () {
          return _toConsumableArray(_cache[13] || (_cache[13] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
            "class": "action-icon mb-3"
          }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
            "class": "lni lni-cart"
          })], -1 /* CACHED */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h6", {
            "class": "mb-0"
          }, "Ventas", -1 /* CACHED */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("small", {
            "class": "text-muted"
          }, "Registrar ventas", -1 /* CACHED */)]));
        }),
        _: 1 /* STABLE */
      })])])])])])])])];
    }),
    _: 1 /* STABLE */
  });
}

/***/ }),

/***/ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Layouts/AppLayout.vue?vue&type=style&index=0&id=5663af57&lang=css":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Layouts/AppLayout.vue?vue&type=style&index=0&id=5663af57&lang=css ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !../../../node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js */ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_AppLayout_vue_vue_type_style_index_0_id_5663af57_lang_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !!../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./AppLayout.vue?vue&type=style&index=0&id=5663af57&lang=css */ "./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Layouts/AppLayout.vue?vue&type=style&index=0&id=5663af57&lang=css");

            

var options = {};

options.insert = "head";
options.singleton = false;

var update = _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default()(_node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_AppLayout_vue_vue_type_style_index_0_id_5663af57_lang_css__WEBPACK_IMPORTED_MODULE_1__["default"], options);



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_AppLayout_vue_vue_type_style_index_0_id_5663af57_lang_css__WEBPACK_IMPORTED_MODULE_1__["default"].locals || {});

/***/ }),

/***/ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Pages/Dashboard.vue?vue&type=style&index=0&id=097ba13b&scoped=true&lang=css":
/*!***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Pages/Dashboard.vue?vue&type=style&index=0&id=097ba13b&scoped=true&lang=css ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !../../../node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js */ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Dashboard_vue_vue_type_style_index_0_id_097ba13b_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !!../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./Dashboard.vue?vue&type=style&index=0&id=097ba13b&scoped=true&lang=css */ "./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Pages/Dashboard.vue?vue&type=style&index=0&id=097ba13b&scoped=true&lang=css");

            

var options = {};

options.insert = "head";
options.singleton = false;

var update = _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default()(_node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Dashboard_vue_vue_type_style_index_0_id_097ba13b_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_1__["default"], options);



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Dashboard_vue_vue_type_style_index_0_id_097ba13b_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_1__["default"].locals || {});

/***/ }),

/***/ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js":
/*!****************************************************************************!*\
  !*** ./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js ***!
  \****************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {



var isOldIE = function isOldIE() {
  var memo;
  return function memorize() {
    if (typeof memo === 'undefined') {
      // Test for IE <= 9 as proposed by Browserhacks
      // @see http://browserhacks.com/#hack-e71d8692f65334173fee715c222cb805
      // Tests for existence of standard globals is to allow style-loader
      // to operate correctly into non-standard environments
      // @see https://github.com/webpack-contrib/style-loader/issues/177
      memo = Boolean(window && document && document.all && !window.atob);
    }

    return memo;
  };
}();

var getTarget = function getTarget() {
  var memo = {};
  return function memorize(target) {
    if (typeof memo[target] === 'undefined') {
      var styleTarget = document.querySelector(target); // Special case to return head of iframe instead of iframe itself

      if (window.HTMLIFrameElement && styleTarget instanceof window.HTMLIFrameElement) {
        try {
          // This will throw an exception if access to iframe is blocked
          // due to cross-origin restrictions
          styleTarget = styleTarget.contentDocument.head;
        } catch (e) {
          // istanbul ignore next
          styleTarget = null;
        }
      }

      memo[target] = styleTarget;
    }

    return memo[target];
  };
}();

var stylesInDom = [];

function getIndexByIdentifier(identifier) {
  var result = -1;

  for (var i = 0; i < stylesInDom.length; i++) {
    if (stylesInDom[i].identifier === identifier) {
      result = i;
      break;
    }
  }

  return result;
}

function modulesToDom(list, options) {
  var idCountMap = {};
  var identifiers = [];

  for (var i = 0; i < list.length; i++) {
    var item = list[i];
    var id = options.base ? item[0] + options.base : item[0];
    var count = idCountMap[id] || 0;
    var identifier = "".concat(id, " ").concat(count);
    idCountMap[id] = count + 1;
    var index = getIndexByIdentifier(identifier);
    var obj = {
      css: item[1],
      media: item[2],
      sourceMap: item[3]
    };

    if (index !== -1) {
      stylesInDom[index].references++;
      stylesInDom[index].updater(obj);
    } else {
      stylesInDom.push({
        identifier: identifier,
        updater: addStyle(obj, options),
        references: 1
      });
    }

    identifiers.push(identifier);
  }

  return identifiers;
}

function insertStyleElement(options) {
  var style = document.createElement('style');
  var attributes = options.attributes || {};

  if (typeof attributes.nonce === 'undefined') {
    var nonce =  true ? __webpack_require__.nc : 0;

    if (nonce) {
      attributes.nonce = nonce;
    }
  }

  Object.keys(attributes).forEach(function (key) {
    style.setAttribute(key, attributes[key]);
  });

  if (typeof options.insert === 'function') {
    options.insert(style);
  } else {
    var target = getTarget(options.insert || 'head');

    if (!target) {
      throw new Error("Couldn't find a style target. This probably means that the value for the 'insert' parameter is invalid.");
    }

    target.appendChild(style);
  }

  return style;
}

function removeStyleElement(style) {
  // istanbul ignore if
  if (style.parentNode === null) {
    return false;
  }

  style.parentNode.removeChild(style);
}
/* istanbul ignore next  */


var replaceText = function replaceText() {
  var textStore = [];
  return function replace(index, replacement) {
    textStore[index] = replacement;
    return textStore.filter(Boolean).join('\n');
  };
}();

function applyToSingletonTag(style, index, remove, obj) {
  var css = remove ? '' : obj.media ? "@media ".concat(obj.media, " {").concat(obj.css, "}") : obj.css; // For old IE

  /* istanbul ignore if  */

  if (style.styleSheet) {
    style.styleSheet.cssText = replaceText(index, css);
  } else {
    var cssNode = document.createTextNode(css);
    var childNodes = style.childNodes;

    if (childNodes[index]) {
      style.removeChild(childNodes[index]);
    }

    if (childNodes.length) {
      style.insertBefore(cssNode, childNodes[index]);
    } else {
      style.appendChild(cssNode);
    }
  }
}

function applyToTag(style, options, obj) {
  var css = obj.css;
  var media = obj.media;
  var sourceMap = obj.sourceMap;

  if (media) {
    style.setAttribute('media', media);
  } else {
    style.removeAttribute('media');
  }

  if (sourceMap && typeof btoa !== 'undefined') {
    css += "\n/*# sourceMappingURL=data:application/json;base64,".concat(btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))), " */");
  } // For old IE

  /* istanbul ignore if  */


  if (style.styleSheet) {
    style.styleSheet.cssText = css;
  } else {
    while (style.firstChild) {
      style.removeChild(style.firstChild);
    }

    style.appendChild(document.createTextNode(css));
  }
}

var singleton = null;
var singletonCounter = 0;

function addStyle(obj, options) {
  var style;
  var update;
  var remove;

  if (options.singleton) {
    var styleIndex = singletonCounter++;
    style = singleton || (singleton = insertStyleElement(options));
    update = applyToSingletonTag.bind(null, style, styleIndex, false);
    remove = applyToSingletonTag.bind(null, style, styleIndex, true);
  } else {
    style = insertStyleElement(options);
    update = applyToTag.bind(null, style, options);

    remove = function remove() {
      removeStyleElement(style);
    };
  }

  update(obj);
  return function updateStyle(newObj) {
    if (newObj) {
      if (newObj.css === obj.css && newObj.media === obj.media && newObj.sourceMap === obj.sourceMap) {
        return;
      }

      update(obj = newObj);
    } else {
      remove();
    }
  };
}

module.exports = function (list, options) {
  options = options || {}; // Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
  // tags it will allow on a page

  if (!options.singleton && typeof options.singleton !== 'boolean') {
    options.singleton = isOldIE();
  }

  list = list || [];
  var lastIdentifiers = modulesToDom(list, options);
  return function update(newList) {
    newList = newList || [];

    if (Object.prototype.toString.call(newList) !== '[object Array]') {
      return;
    }

    for (var i = 0; i < lastIdentifiers.length; i++) {
      var identifier = lastIdentifiers[i];
      var index = getIndexByIdentifier(identifier);
      stylesInDom[index].references--;
    }

    var newLastIdentifiers = modulesToDom(newList, options);

    for (var _i = 0; _i < lastIdentifiers.length; _i++) {
      var _identifier = lastIdentifiers[_i];

      var _index = getIndexByIdentifier(_identifier);

      if (stylesInDom[_index].references === 0) {
        stylesInDom[_index].updater();

        stylesInDom.splice(_index, 1);
      }
    }

    lastIdentifiers = newLastIdentifiers;
  };
};

/***/ }),

/***/ "./node_modules/vue-loader/dist/exportHelper.js":
/*!******************************************************!*\
  !*** ./node_modules/vue-loader/dist/exportHelper.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, exports) => {


Object.defineProperty(exports, "__esModule", ({ value: true }));
// runtime helper for setting properties on components
// in a tree-shakable way
exports["default"] = (sfc, props) => {
    const target = sfc.__vccOpts || sfc;
    for (const [key, val] of props) {
        target[key] = val;
    }
    return target;
};


/***/ }),

/***/ "./resources/js/Layouts/AppLayout.vue":
/*!********************************************!*\
  !*** ./resources/js/Layouts/AppLayout.vue ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _AppLayout_vue_vue_type_template_id_5663af57__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./AppLayout.vue?vue&type=template&id=5663af57 */ "./resources/js/Layouts/AppLayout.vue?vue&type=template&id=5663af57");
/* harmony import */ var _AppLayout_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./AppLayout.vue?vue&type=script&setup=true&lang=js */ "./resources/js/Layouts/AppLayout.vue?vue&type=script&setup=true&lang=js");
/* harmony import */ var _AppLayout_vue_vue_type_style_index_0_id_5663af57_lang_css__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./AppLayout.vue?vue&type=style&index=0&id=5663af57&lang=css */ "./resources/js/Layouts/AppLayout.vue?vue&type=style&index=0&id=5663af57&lang=css");
/* harmony import */ var _node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;


const __exports__ = /*#__PURE__*/(0,_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__["default"])(_AppLayout_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_AppLayout_vue_vue_type_template_id_5663af57__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"resources/js/Layouts/AppLayout.vue"]])
/* hot reload */
if (false) // removed by dead control flow
{}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./resources/js/Layouts/AppLayout.vue?vue&type=script&setup=true&lang=js":
/*!*******************************************************************************!*\
  !*** ./resources/js/Layouts/AppLayout.vue?vue&type=script&setup=true&lang=js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_laravel_mix_node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_AppLayout_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_laravel_mix_node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_AppLayout_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/laravel-mix/node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./AppLayout.vue?vue&type=script&setup=true&lang=js */ "./node_modules/laravel-mix/node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Layouts/AppLayout.vue?vue&type=script&setup=true&lang=js");
 

/***/ }),

/***/ "./resources/js/Layouts/AppLayout.vue?vue&type=style&index=0&id=5663af57&lang=css":
/*!****************************************************************************************!*\
  !*** ./resources/js/Layouts/AppLayout.vue?vue&type=style&index=0&id=5663af57&lang=css ***!
  \****************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_dist_cjs_js_node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_AppLayout_vue_vue_type_style_index_0_id_5663af57_lang_css__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/style-loader/dist/cjs.js!../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./AppLayout.vue?vue&type=style&index=0&id=5663af57&lang=css */ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Layouts/AppLayout.vue?vue&type=style&index=0&id=5663af57&lang=css");


/***/ }),

/***/ "./resources/js/Layouts/AppLayout.vue?vue&type=template&id=5663af57":
/*!**************************************************************************!*\
  !*** ./resources/js/Layouts/AppLayout.vue?vue&type=template&id=5663af57 ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _node_modules_laravel_mix_node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_AppLayout_vue_vue_type_template_id_5663af57__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_laravel_mix_node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_AppLayout_vue_vue_type_template_id_5663af57__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/laravel-mix/node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./AppLayout.vue?vue&type=template&id=5663af57 */ "./node_modules/laravel-mix/node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Layouts/AppLayout.vue?vue&type=template&id=5663af57");


/***/ }),

/***/ "./resources/js/Pages/Dashboard.vue":
/*!******************************************!*\
  !*** ./resources/js/Pages/Dashboard.vue ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Dashboard_vue_vue_type_template_id_097ba13b_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Dashboard.vue?vue&type=template&id=097ba13b&scoped=true */ "./resources/js/Pages/Dashboard.vue?vue&type=template&id=097ba13b&scoped=true");
/* harmony import */ var _Dashboard_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Dashboard.vue?vue&type=script&setup=true&lang=js */ "./resources/js/Pages/Dashboard.vue?vue&type=script&setup=true&lang=js");
/* harmony import */ var _Dashboard_vue_vue_type_style_index_0_id_097ba13b_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Dashboard.vue?vue&type=style&index=0&id=097ba13b&scoped=true&lang=css */ "./resources/js/Pages/Dashboard.vue?vue&type=style&index=0&id=097ba13b&scoped=true&lang=css");
/* harmony import */ var _node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;


const __exports__ = /*#__PURE__*/(0,_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__["default"])(_Dashboard_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_Dashboard_vue_vue_type_template_id_097ba13b_scoped_true__WEBPACK_IMPORTED_MODULE_0__.render],['__scopeId',"data-v-097ba13b"],['__file',"resources/js/Pages/Dashboard.vue"]])
/* hot reload */
if (false) // removed by dead control flow
{}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./resources/js/Pages/Dashboard.vue?vue&type=script&setup=true&lang=js":
/*!*****************************************************************************!*\
  !*** ./resources/js/Pages/Dashboard.vue?vue&type=script&setup=true&lang=js ***!
  \*****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_laravel_mix_node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Dashboard_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_laravel_mix_node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Dashboard_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/laravel-mix/node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./Dashboard.vue?vue&type=script&setup=true&lang=js */ "./node_modules/laravel-mix/node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Pages/Dashboard.vue?vue&type=script&setup=true&lang=js");
 

/***/ }),

/***/ "./resources/js/Pages/Dashboard.vue?vue&type=style&index=0&id=097ba13b&scoped=true&lang=css":
/*!**************************************************************************************************!*\
  !*** ./resources/js/Pages/Dashboard.vue?vue&type=style&index=0&id=097ba13b&scoped=true&lang=css ***!
  \**************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_dist_cjs_js_node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Dashboard_vue_vue_type_style_index_0_id_097ba13b_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/style-loader/dist/cjs.js!../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./Dashboard.vue?vue&type=style&index=0&id=097ba13b&scoped=true&lang=css */ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Pages/Dashboard.vue?vue&type=style&index=0&id=097ba13b&scoped=true&lang=css");


/***/ }),

/***/ "./resources/js/Pages/Dashboard.vue?vue&type=template&id=097ba13b&scoped=true":
/*!************************************************************************************!*\
  !*** ./resources/js/Pages/Dashboard.vue?vue&type=template&id=097ba13b&scoped=true ***!
  \************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _node_modules_laravel_mix_node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Dashboard_vue_vue_type_template_id_097ba13b_scoped_true__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_laravel_mix_node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Dashboard_vue_vue_type_template_id_097ba13b_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/laravel-mix/node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./Dashboard.vue?vue&type=template&id=097ba13b&scoped=true */ "./node_modules/laravel-mix/node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/Pages/Dashboard.vue?vue&type=template&id=097ba13b&scoped=true");


/***/ })

}]);