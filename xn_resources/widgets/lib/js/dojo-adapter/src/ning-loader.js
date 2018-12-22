dojo.provide("ning.loader");
(function() {
	var compressModuleHash = function(node, isSubcall) {
		var modules = [];
		var isModule = node["__module__"];
		delete node["__module__"];
		for (var i in node) {
			modules.push(i + compressModuleHash(node[i], true));
		}
		if (modules.length > (isModule ? 0 : 1)) {
			return ((isModule ? '{' : '(') + modules.join(',') + (isModule ? '}' : ')')).replace(/(\)|}),/g, '$1');
		}
		return modules[0] ? ((isSubcall ? '.' : '') + modules[0]) : '';
	}
	var addToModuleHash = function(hash, module) {
		dojo._setObj(module+".__module__", true, hash)
	}

    var prefixPatterns = {};
    var compiledPrefixPatterns = [];
    var have = {}, provide = dojo.provide;

    // Import existing modules into "have"
	for (var i in dojo._modules) {
		addToModuleHash(have, i);
	}
	// Overload dojo.provide() to catch new modules
	dojo.provide = function(mod) {
		addToModuleHash(have, mod);
		provide.apply(dojo,arguments);
	}

	ning.loader = {
        _pending: {},

		version: null, // prepended to URL to bust caches

		/**
         * Sets a prefix pattern which will be consulted if the server doesn't find a matching prefix
         * (Used to reduce minimum URL length)
         *
         * @param pattern the regular expression to match (entire module string must match), e.g.: /xg\.([^.]+)/
         *
         * @param path a path to replace with on match, e.g.: "/xn_resources/widgets/$1/js"
         */
		setPrefixPattern: function(pattern, path) {
			var patternKernel = String(pattern).replace(/^\/(.*)\/$/, "$1");
			if (patternKernel == String(pattern)) {
				dojo.raise("Bogus parameter " + pattern + " passed to ning.loader.setPrefixPattern");
			}
			prefixPatterns[patternKernel] = path;
			compiledPrefixPatterns.push(new RegExp("^" + patternKernel + "$"));
		},

        require: function() {
            var maxLength = 800;
			var url = "http://" + window.location.host + "/xn/loader?v=" + "6.11.8.1_" + (ning.loader.version || (new Date()).valueOf());

			var asyncFunc = arguments[arguments.length-1];
			if ("function" != typeof asyncFunc) {
				dojo.raise("last argument of ning.loader.require() must be a function");
			}

            var require = undefined;
            for (var i = 0; i < arguments.length-1; i++) {
				if ( !(arguments[i] in dojo._modules)) {
					if (!require) require = {};
                    addToModuleHash(require, arguments[i]);
                }
            }
            if (!require) {
				asyncFunc();
                return;
            }
			url += "&r="+compressModuleHash(require);
            url += "&p=";
            for (var i in dojo._modulePrefixes) {
                var matchesPattern = false;
                for (var p = 0; p < compiledPrefixPatterns.length; ++p) {
                    var pattern = compiledPrefixPatterns[p];
                    if (pattern.test(i)) {
						matchesPattern = true;
	                    break;
					}
				}
				if (!matchesPattern) {
                    url += encodeURIComponent(i) + ":" + encodeURIComponent(dojo._modulePrefixes[i]) + ",";
				}
            }
			for (var pattern in prefixPatterns) {
				url += encodeURIComponent(pattern) + "=>" + encodeURIComponent(prefixPatterns[pattern]);
			}

            if (url.length > maxLength) {
				dojo.raise("Cannot load JS files. URL is too long");
            }

            url += "&h=" + compressModuleHash(have);
            if (url.length > maxLength) {
                // okay to truncate the "have" list -- just means fetching more code that won't be run
                url = url.substring(0, maxLength);
            }

			x$.get(url, {}, function(contents) {
				try {
					x$.globalEval(contents);
					asyncFunc();
				}
				catch (e) {
					// someone is swallowing exceptions!
					// HACK: use setTimeout to make sure they get through
					setTimeout(function() { dojo.raise(e.message); }, 0);
				}
			});
		}
    }
})();
