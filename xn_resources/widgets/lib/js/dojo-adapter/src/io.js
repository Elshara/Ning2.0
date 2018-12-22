dojo.provide('dojo.io');

(function(){
    var _allowedParams = {url:1, method:1, preventCache:1, encoding:1, content:1, formNode:1, sync:1, mimetype:1, load:1, error:1};
    dojo.io = {
        /**
        *	Performs XML HTTP request. The argument is a hash:
        *		url: string
        *		method: string (GET|POST|...)
        *		preventCache: bool
        *		encoding: string (usually utf-8) -- ignored
        *		content: hash
        *		formNode: form node
        *		sync: bool
        *		mimetype: string /javascript|json/ triggers JSON evaluation
        *		load: function("load", data, xhr)
        *		error: function("error", error, xhr)
        *  any other item causes an error. Use the original dojo.io.bind() instead.
        *
        *  @return     XMLRequest object
        */
        bind: function(conf) {
            var opts = this.prepareBind(conf);
            var xhr;
            if (conf.load) opts.success = function(data, status) { conf.load("load", data, xhr) };
            if (conf.error) opts.error = function(xhr, status, error) { conf.error("error", error, xhr) };
            return xhr = x$.ajax(opts);
        },
        /**
         * Adjusts the configuration object, and returns an options object.
         *
         * @param conf  a configuration object for dojo.io.bind
         * @return  an options object for jQuery's .ajax
         */
        prepareBind: function(conf) {
            // This logic is extracted from bind to make it easier to test. [Jon Aquino 2008-08-27]
            var o = {};
            for (var i in conf) {
                if ( !(i in o) && !_allowedParams[i]) dojo.raise("Unknown parameter `"+i+"'. Use dojo.js instead.");
            }
            if ( !("content" in conf) ) {
                conf.content = {};
            } else if ("object" != typeof conf.content) {
                dojo.raise("Content must be a hash. Use dojo.js instead.");
            }
            if (conf.formNode) {
                // process formNode
                if (!conf.url) conf.url = conf.formNode.getAttribute('action');
                if (!conf.method) conf.method = conf.formNode.getAttribute('method');
                x$.map(x$(conf.formNode).serializeArray(), function(parameter) { conf.content[parameter.name] = parameter.value; });
            }
            if (!conf.url.match(/\/xn\//)) {
                conf.content['xg_token'] = xg.token;
            }
            var opts = {
                url: conf.url,
                type: conf.method,
                cache: !conf.preventCache,
                data: conf.content,
                async: !conf.sync
            };

            switch(conf.mimetype) {
                case "text/javascript":
                case "text/json":		opts.dataType = 'json'; break;
                case "application/xml":
                case "text/xml":		opts.dataType = 'xml'; break;
                case 'text/html':
                case 'text/plain': 		opts.dataType = 'text'; break;
                default:				break;
            }
            return opts;
        }
    }
})();
