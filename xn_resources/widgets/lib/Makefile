DA_DIR=js/dojo-adapter/src
JQ_DIR=js/jquery

JQ_SRC = $(JQ_DIR)/jquery.min.js $(JQ_DIR)/jquery.json.min.js
DA_SRC = $(DA_DIR)/core.js $(DA_DIR)/core-post.js $(DA_DIR)/ning-loader.js $(DA_DIR)/lang.js \
	$(DA_DIR)/json.js $(DA_DIR)/event.js $(DA_DIR)/io.js $(DA_DIR)/string.js $(DA_DIR)/graphics.js \
	$(DA_DIR)/html.js $(DA_DIR)/widget.js $(DA_DIR)/lfx.js

all: core.min.js

core.min.js: $(JQ_SRC) $(DA_SRC)
	@echo "/* This file is auto-generated. Do not edit it directly. What's inside: jquery (as x\$$()), jquery.json, dojo-adapter */" > $@
	cat $(JQ_SRC) >> $@
	echo ";window.x\$$ = jQuery.noConflict();" >> $@
	cat $(DA_SRC) | jsmin | sed "s/\r//g" >> $@
	
.PHONY: all
