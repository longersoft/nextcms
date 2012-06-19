/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		core
 * @subpackage	js
 * @since		1.0
 * @version		2011-12-11
 */

dojo.provide("core.js.base.I18N");

// This class provides the simillar API with dojo.i18n package.
// dojo.i18n supports the language file in *.js. Meanwhile, I need a file
// extension that supports both JS (which the translated items can be used in my widgets), 
// and PHP (which the translated items can be read by using a Zend Translate adapter).

// The simple scenario is as follow:
// - The language file is a text file located at /modules/nameOfModule/languages/localeString.json
// where:
// 		nameOfModule is the name of your module (in lowercase),
// 		localeString is the locale in the format of languagename_COUNTRYNAME (en_US, vi_VN, etc)
// Note that it differs from the format defined by Dojo (In Dojo, they are
// en-us, vi-vn, etc).
// 
// - Put the required script and locale setting at the top of the view script:
// |	<?php
// |	$this->headScript()->appendFile($this->APP_STATIC_URL . '/modules/core/js/base/I18N.js');
// |	?>
// | 	<script type="text/javascript">
// |	core.js.base.I18N.setLocale("<?php echo $this->APP_LANGUAGE; ?>");
// |	</script>
//
// - Then, get the object that consists of all translated items:
// |	dojo.addOnLoad(function() {
// |		core.js.base.I18N.requireLocalization("yourModuleName/languages");
// |		var items = core.js.base.I18N.getLocalization("yourModuleName/languages");
// |		console.log(items);
// |	});
//
// If content of the language file (/modules/core/languages/en_US.json) looks like:
// |	{
// |		...
// |		"_global": {
// |			"loadingAction": "Loading",
// |			"addAction": "Add"
// |		},
// |		"auth": {
// |			...
// |		}
// |		...
// |	}
// then you can access the translated items as follow:
// |	var loadingString = core.js.base.I18N.getLocalization("core/languages")._global.loadingAction;

core.js.base.I18N.requireLocalization = function(/*String*/ path, /*String?*/ locale) {
	// summary:
	//		Loads the language file
	// path:
	//		Path to the language file. The popular cases are:
	//		- moduleName/languages
	//		- moduleName/plugins/pluginName
	//		- moduleName/hooks/hookName
	//		- moduleName/widgets/widgetName
	if (!locale) {
		locale = core.js.base.I18N._locale;
	}
	var paths = path.split("/");
	var bundlePackage = paths.join(".") + "." + locale;

	// Check if the package is loaded or not
	// The package is defined as appModuleName.languages
	// DOJO LESSON: Use dojo._loadedModules to see the list of modules that have
	// been loaded:
	// 		console.log(dojo._loadedModules);
	var bundle = dojo._loadedModules[bundlePackage];
	if (!bundle) {
		bundle = dojo["provide"](bundlePackage);
		var moduleName = paths.splice(0, 1)[0];
		var syms = dojo._getModuleSymbols(moduleName);
		
		// Path to language file
		var file = syms.join("/") + "/" + paths.join("/") + "/" + locale + ".json";

		var loaded = false;
		loaded = dojo._loadPath(file, null, function(hash) {
			hash = hash.root || hash;
//			var clazz = function() {
//			};
//			clazz.prototype = parent;
//			bundle[locale] = new clazz();
			bundle[locale] = {};
			for ( var j in hash) {
				bundle[locale][j] = hash[j];
			}
		});
	}
};

core.js.base.I18N.getLocalization = function(/*String*/ path, /*String?*/ locale) {
	if (!locale) {
		locale = core.js.base.I18N._locale;
	}
	var paths = path.split("/");
	var bundlePackage = paths.join(".") + "." + locale;
	var bundle = dojo._loadedModules[bundlePackage];

	if (bundle) {
		var localization = bundle[locale];

		if (localization) {
//			var clazz = function() {
//			};
//			clazz.prototype = localization;
//			return new clazz(); // Object
			return localization; // Object
		}
	}

	throw new Error("Locale not found: path=" + path + ", locale=" + locale);
};

// The locale
// It should be set at the top of page
core.js.base.I18N._locale = "en_US";

core.js.base.I18N.setLocale = function(/*String*/ locale) {
	// summary:
	// 		Sets the locale (en_US, vi_VN, etc)
	core.js.base.I18N._locale = locale;
};

core.js.base.I18N.getLocale = function() {
	// summary:
	//		Gets the locale
	return core.js.base.I18N._locale;	// String
};
