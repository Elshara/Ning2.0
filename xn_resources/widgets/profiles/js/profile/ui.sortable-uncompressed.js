/*
 * jQuery UI Sortable
 *
 * Copyright (c) 2008 Paul Bakaus
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 * 
 * http://docs.jquery.com/UI/Sortables
 *
 * Depends:
 *	ui.core.js
 *
 * Revision: $Id: ui.sortable.js 5452 2008-05-05 16:42:08Z rdworth $
 */
;(function($) {
	
	function contains(a, b) { 
	    var safari2 = $.browser.safari && $.browser.version < 522; 
	    if (a.contains && !safari2) { 
	        return a.contains(b); 
	    } 
	    if (a.compareDocumentPosition) 
	        return !!(a.compareDocumentPosition(b) & 16); 
	    while (b = b.parentNode) 
	          if (b == a) return true; 
	    return false; 
	};
	
	$.widget("ui.sortable", {
		init: function() {

			var o = this.options;
			this.containerCache = {};
			this.element.addClass("ui-sortable");
		
			//Get the items
			this.refresh();
	
			//Let's determine if the items are floating
			this.floating = this.items.length ? (/left|right/).test(this.items[0].item.css('float')) : false;
			
			//Let's determine the parent's offset
			if(!(/(relative|absolute|fixed)/).test(this.element.css('position'))) this.element.css('position', 'relative');
			this.offset = this.element.offset();
	
			//Initialize mouse events for interaction
			this.element.mouse({
				executor: this,
				delay: o.delay,
				distance: o.distance || 1,
				dragPrevention: o.prevention ? o.prevention.toLowerCase().split(',') : ['input','textarea','button','select','option'],
				start: this.start,
				stop: this.stop,
				drag: this.drag,
				condition: function(e) {
	
					if(this.options.disabled || this.options.type == 'static') return false;
	
					//Find out if the clicked node (or one of its parents) is a actual item in this.items
					var currentItem = null, nodes = $(e.target).parents().each(function() {	
						if($.data(this, 'sortable-item')) {
							currentItem = $(this);
							return false;
						}
					});
					if($.data(e.target, 'sortable-item')) currentItem = $(e.target);
					
					if(!currentItem) return false;	
					if(this.options.handle) {
						var validHandle = false;
						$(this.options.handle, currentItem).each(function() { if(this == e.target) validHandle = true; });
						if(!validHandle) return false;
					}
						
					this.currentItem = currentItem;
					return true;
	
				}
			});
			
		},
		plugins: {},
		ui: function(inst) {
			return {
				helper: (inst || this)["helper"],
				placeholder: (inst || this)["placeholder"] || $([]),
				position: (inst || this)["position"].current,
				absolutePosition: (inst || this)["position"].absolute,
                                hideElement: (inst || this).hideElement, // added for BAZ-7706
				instance: this,
				options: this.options,
				element: this.element,
				moduleMargin: this.moduleMargin, // added by huy to fix placeholder width overlap issue
				item: (inst || this)["currentItem"],
				sender: inst ? inst.element : null
			};		
		},
		propagate: function(n,e,inst) {
			$.ui.plugin.call(this, n, [e, this.ui(inst)]);
			this.element.triggerHandler(n == "sort" ? n : "sort"+n, [e, this.ui(inst)], this.options[n]);
		},
		serialize: function(o) {
			
			var items = $(this.options.items, this.element).not('.ui-sortable-helper'); //Only the items of the sortable itself
			var str = []; o = o || {};
			
			items.each(function() {
				var res = ($(this).attr(o.attribute || 'id') || '').match(o.expression || (/(.+)[-=_](.+)/));
				if(res) str.push((o.key || res[1])+'[]='+(o.key ? res[1] : res[2]));
			});
			
			return str.join('&');
			
		},
		toArray: function(attr) {
			var items = $(this.options.items, this.element).not('.ui-sortable-helper'); //Only the items of the sortable itself
			var ret = [];

			items.each(function() { ret.push($(this).attr(attr || 'id')); });
			return ret;
		},
		enable: function() {
			this.element.removeClass("ui-sortable-disabled");
			this.options.disabled = false;
		},
		disable: function() {
			this.element.addClass("ui-sortable-disabled");
			this.options.disabled = true;
		},
		/* Be careful with the following core functions */
		intersectsWith: function(item) {
			
			var x1 = this.position.absolute.left, x2 = x1 + this.helperProportions.width,
			y1 = this.position.absolute.top, y2 = y1 + this.helperProportions.height;
			var l = item.left, r = l + item.width, 
			t = item.top, b = t + item.height;

			if(this.options.tolerance == "pointer") {
				return (y1 + this.clickOffset.top > t && y1 + this.clickOffset.top < b && x1 + this.clickOffset.left > l && x1 + this.clickOffset.left < r);
			} else {
			
				return (l < x1 + (this.helperProportions.width / 2) // Right Half
					&& x2 - (this.helperProportions.width / 2) < r // Left Half
					&& t < y1 + (this.helperProportions.height / 2) // Bottom Half
					&& y2 - (this.helperProportions.height / 2) < b ); // Top Half
			
			}
			
		},
		intersectsWithEdge: function(item) {	
			var x1 = this.position.absolute.left, x2 = x1 + this.helperProportions.width,
				y1 = this.position.absolute.top, y2 = y1 + this.helperProportions.height;
			var l = item.left, r = l + item.width, 
				t = item.top, b = t + item.height;

			if(this.options.tolerance == "pointer") {

				if(!(y1 + this.clickOffset.top > t && y1 + this.clickOffset.top < b && x1 + this.clickOffset.left > l && x1 + this.clickOffset.left < r)) return false;
				
				if(this.floating) {
					if(x1 + this.clickOffset.left > l && x1 + this.clickOffset.left < l + item.width/2) return 2;
					if(x1 + this.clickOffset.left > l+item.width/2 && x1 + this.clickOffset.left < r) return 1;
				} else {
					if(y1 + this.clickOffset.top > t && y1 + this.clickOffset.top < t + item.height/2) return 2;
					if(y1 + this.clickOffset.top > t+item.height/2 && y1 + this.clickOffset.top < b) return 1;
				}

			} else {
			
				if (!(l < x1 + (this.helperProportions.width / 2) // Right Half
					&& x2 - (this.helperProportions.width / 2) < r // Left Half
					&& t < y1 + (this.helperProportions.height / 2) // Bottom Half
					&& y2 - (this.helperProportions.height / 2) < b )) return false; // Top Half
				
				if(this.floating) {
					if(x2 > l && x1 < l) return 2; //Crosses left edge
					if(x1 < r && x2 > r) return 1; //Crosses right edge
				} else {
					if(y2 > t && y1 < t) return 1; //Crosses top edge
					if(y1 < b && y2 > b) return 2; //Crosses bottom edge
				}
			
			}
			
			return false;
			
		},
		inEmptyZone: function(container) {
                        var x = this.position.absolute.left + Math.round(this.helperProportions.width / 2);
                        var y = this.position.absolute.top + Math.round(this.helperProportions.height / 2);
                        var cx1 = container.containerCache.left, cx2 = cx1 + container.containerCache.width - 1,
                                cy1 = container.containerCache.top, cy2 = cy1 + container.containerCache.height - 1;

                        var emptyZoneHeight = 200;
                        var padding = 25;
                        var topy1 = (cy1 > emptyZoneHeight) ? cy1 - emptyZoneHeight : 0;
                        var boty2 = cy2 + emptyZoneHeight;

                        // flanks side to side
                        if ((x > cx1 + padding) && (x < cx2 - padding)) {
                            // flanks top
                            if ((y > topy1) && (y < cy1)) {
                                return true;
                            }
                            // flanks bottom
                            if ((y > cy2) && (y < boty2)) {
                                return true;
                            }
                        }

                        return false;
		},
		refresh: function() {
			this.refreshItems();
			this.refreshPositions();
		},
		refreshItems: function() {
			
			this.items = [];
			this.containers = [this];
			var items = this.items;
			var queries = [$(this.options.items, this.element)];
			
			if(this.options.connectWith) {
				for (var i = this.options.connectWith.length - 1; i >= 0; i--){
					var cur = $(this.options.connectWith[i]);
					for (var j = cur.length - 1; j >= 0; j--){
						var inst = $.data(cur[j], 'sortable');
						if(inst && !inst.options.disabled) {
							queries.push($(inst.options.items, inst.element));
							this.containers.push(inst);
						}
					};
				};
			}

			for (var i = queries.length - 1; i >= 0; i--){
				queries[i].each(function() {
					$.data(this, 'sortable-item', true); // Data for target checking (mouse manager)
					items.push({
						item: $(this),
						width: 0, height: 0,
						left: 0, top: 0
					});
				});
			};

		},
		refreshPositions: function(fast) {
			for (var i = this.items.length - 1; i >= 0; i--){
				var t = this.items[i].item;
				if(!fast) this.items[i].width = (this.options.toleranceElement ? $(this.options.toleranceElement, t) : t).outerWidth();
				if(!fast) this.items[i].height = (this.options.toleranceElement ? $(this.options.toleranceElement, t) : t).outerHeight();
				var p = (this.options.toleranceElement ? $(this.options.toleranceElement, t) : t).offset();
				this.items[i].left = p.left;
				this.items[i].top = p.top;
			};
			for (var i = this.containers.length - 1; i >= 0; i--){
				var p =this.containers[i].element.offset();
				this.containers[i].containerCache.left = p.left;
				this.containers[i].containerCache.top = p.top;
				this.containers[i].containerCache.width	= this.containers[i].element.outerWidth();
				this.containers[i].containerCache.height = this.containers[i].element.outerHeight();
			};
		},
		destroy: function() {
			this.element
				.removeClass("ui-sortable ui-sortable-disabled")
				.removeData("sortable")
				.unbind(".sortable")
				.mouse("destroy");
			
			for ( var i = this.items.length - 1; i >= 0; i-- )
				this.items[i].item.removeData("sortable-item");
		},
		createPlaceholder: function(that) {
			(that || this).placeholderElement = this.options.placeholderElement ? $(this.options.placeholderElement, (that || this).currentItem) : (that || this).currentItem;
                        (that || this).placeholder = $('<div></div>')
				.addClass(this.options.placeholder)
				.appendTo('body')
				.css({ position: 'absolute' })
				.css((that || this).placeholderElement.offset())
				.css({ width: ((that || this).placeholderElement[0].offsetWidth - this.options.moduleMargin), height: ((that || this).placeholderElement[0].offsetHeight - this.options.moduleMargin) })
				;
		},
                getNearestItemAndDistanceByContainer: function(container, considerBottom) {
                        //Find and return the item in the specified container nearest the current mouse cursor position
                        var dist = 10000; var itemWithLeastDistance = null; var base = this.position.absolute[container.floating ? 'left' : 'top'];
                        var direction = 'down'; // which way to shift item
                        for (var j = this.items.length - 1; j >= 0; j--) {
                                if(!contains(container.element[0], this.items[j].item[0])) continue;
                                var curTL = this.items[j][container.floating ? 'left' : 'top'];
                                var curBR = curTL + this.items[j][container.floating ? 'width' : 'height'];
                                if(Math.abs(curTL - base) < dist) {
                                        dist = Math.abs(curTL - base); itemWithLeastDistance = this.items[j];
                                        direction = (base < curTL) ? 'down' : 'up';
                                }
                                if (considerBottom && (Math.abs(curBR - base) < dist)) {
                                        dist = Math.abs(curBR - base); itemWithLeastDistance = this.items[j];
                                        direction = (base < curBR) ? 'down' : 'up';
                                }
                        }
                        return { item: itemWithLeastDistance, dist: dist, direction: direction };
                },
                updatePlaceholderByContainerItem: function(container, item, e) {
                        //exchange the placeholder
                        if(this.placeholder) this.placeholder.remove();
                        if(container.options.placeholder) {
                                container.createPlaceholder(this);
                        } else {
                                this.placeholder = null; this.placeholderElement = null;
                        }

                        item ? this.rearrange(e, item) : this.rearrange(e, null, container.element);
                        this.propagate("change", e); //Call plugins and callbacks
                        container.propagate("change", e, this); //Call plugins and callbacks
                        this.currentContainer = container;
                },
                updateBoundingBoxes: function() {
                    function sortItems(a, b) {
                        return a.top - b.top;
                    }

                    // bounding box extension threshold in pixels
                    var extendThresh = 20;
                    // side padding in pixels
                    var sidePad = 20;
                    // empty zone height in pixels
                    var emptyZoneHeightTop = 136;
                    var emptyZoneHeightBottom = 220;

                    this.boundingBoxes = [];

                    for (var i = 0; i < this.containers.length; i++) {
                        var container = this.containers[i];
                        var containerItems = [];
                        for (var j = this.items.length - 1; j >= 0; j--) {
				if (contains(container.element[0], this.items[j].item[0])) {
				        containerItems.push(this.items[j]);
				}
                        }
			if (containerItems.length > 0) {
				containerItems = containerItems.sort(sortItems);
				var currentBox = { x1: containerItems[0].left >= sidePad ? containerItems[0].left - sidePad + 1 : 0,
						   y1: containerItems[0].top >= emptyZoneHeightTop ? containerItems[0].top - emptyZoneHeightTop + 1 : 0,
						   x2: containerItems[0].left + containerItems[0].width - 1 + sidePad,
						   y2: containerItems[0].top + containerItems[0].height - 1 };
				var boxes = [];
				for (var j = 1; j < containerItems.length; j++) {
				    var item = containerItems[j];
				    if (item.top + item.height - 1 > currentBox.y2) {
					if (item.top - currentBox.y2 <= extendThresh) {
					    // extend
					    currentBox.y2 = item.top + item.height - 1;
					} else {
					    // add bounding box and create a new one
					    currentBox.y2 += emptyZoneHeightBottom - 1;
					    boxes.push(currentBox);
					    currentBox = { x1: item.left >= sidePad ? item.left - sidePad + 1 : 0, y1: item.top >= emptyZoneHeightTop ? item.top - emptyZoneHeightTop + 1 : 0, x2: item.left + item.width - 1 + sidePad, y2: item.top + item.height - 1 };
					}
				    }
				}
				currentBox.y2 += emptyZoneHeightBottom - 1;
				boxes.push(currentBox);
				
				this.boundingBoxes.push(boxes);
			} else {
				// container has no items - special case - we just need to emptyZone area
				var ex1 = container.containerCache.left >= sidePad ? container.containerCache.left - sidePad + 1 : 0,
				    ex2 = container.containerCache.left + container.containerCache.width - 1 + sidePad,
				    ey1 = container.containerCache.top + container.containerCache.height - 1,
				    ey2 = container.containerCache.top + container.containerCache.height - 1;
				this.boundingBoxes.push([{ x1: ex1, y1: ey1 >= emptyZoneHeightTop ? ey1 - emptyZoneHeightTop + 1 : 0, x2: ex2, y2: ey2 + emptyZoneHeightBottom - 1 }]);
			}
                    }
                },
		cursorDistanceFromBottomOfContainer: function(container) {
			var cy2 = container.containerCache.top + container.containerCache.height - 1;
			var base = this.position.absolute[container.floating ? 'left' : 'top'];
			return Math.abs(cy2 - base);
		},
		contactContainers: function(e) {
			var emptyZoneHeight = 200; // should probably make this a sortable option since it's used in multiple places [ywh 2008-05-29]
			for (var i = this.containers.length - 1; i >= 0; i--){

				if(this.intersectsWith(this.containers[i].containerCache)) {
					if(!this.containers[i].containerCache.over) {
						

						if(this.currentContainer != this.containers[i]) {

                                                        //When entering a new container, we will find the item with the least distance and append our item near it
                                                        var nearest = this.getNearestItemAndDistanceByContainer(this.containers[i], false);

							if (nearest.item) {
								if (nearest.dist > emptyZoneHeight) continue;
							} else {
								if (this.cursorDistanceFromBottomOfContainer(this.containers[i]) > emptyZoneHeight) continue;
							}
							
                                                        this.direction = nearest.direction;
                                                        this.updatePlaceholderByContainerItem(this.containers[i], nearest.item, e);
                                                        this.updateBoundingBoxes();

						}
						
						this.containers[i].propagate("over", e, this);
						this.containers[i].containerCache.over = 1;
					}
                                } else if (this.inEmptyZone(this.containers[i])) {
                                        var nearest = this.getNearestItemAndDistanceByContainer(this.containers[i], true);

					if (nearest.item) {
						if (nearest.dist > emptyZoneHeight) continue;
					} else {
						if (this.cursorDistanceFromBottomOfContainer(this.containers[i]) > emptyZoneHeight) continue;
					}

                                        this.direction = nearest.direction;
                                        this.updatePlaceholderByContainerItem(this.containers[i], nearest.item, e);
                                        this.updateBoundingBoxes();
                                        // do we call container "over" callbacks? does the empty zone count?
				} else {
					if(this.containers[i].containerCache.over) {
						this.containers[i].propagate("out", e, this);
						this.containers[i].containerCache.over = 0;
					}
				}
				
			};			
		},
		start: function(e,el) {

			var o = this.options;
			this.currentContainer = this;
			this.refresh();

                        //compute/update draggable area bounding boxes
                        this.updateBoundingBoxes();

			//Create and append the visible helper
			this.helper = typeof o.helper == 'function' ? $(o.helper.apply(this.element[0], [e, this.currentItem])) : this.currentItem.clone();
			if(!this.helper.parents('body').length) this.helper.appendTo(o.appendTo || this.currentItem[0].parentNode); //Add the helper to the DOM if that didn't happen already
			this.helper.css({ position: 'absolute', clear: 'both' }).addClass('ui-sortable-helper'); //Position it absolutely and add a helper class
                        this.helperInner = $("div.sortable-inner");
			
			//Prepare variables for position generation
			$.extend(this, {
				offsetParent: this.helper.offsetParent(),
				offsets: {
					absolute: this.currentItem.offset()
				},
				mouse: {
					start: { top: e.pageY, left: e.pageX }
				},
				margins: {
					top: parseInt(this.currentItem.css("marginTop")) || 0,
					left: parseInt(this.currentItem.css("marginLeft")) || 0
				}
			});
			
			//The relative click offset
			this.offsets.parent = this.offsetParent.offset();
			this.clickOffset = { left: e.pageX - this.offsets.absolute.left, top: e.pageY - this.offsets.absolute.top };
			
			this.originalPosition = {
				left: this.offsets.absolute.left - this.offsets.parent.left - this.margins.left,
				top: this.offsets.absolute.top - this.offsets.parent.top - this.margins.top
			}
			
			//Generate a flexible offset that will later be subtracted from e.pageX/Y
			//I hate margins - they need to be removed before positioning the element absolutely..
			this.offset = {
				left: e.pageX - this.originalPosition.left,
				top: e.pageY - this.originalPosition.top
			};

			//Save the first time position
			$.extend(this, {
				position: {
					current: { top: e.pageY - this.offset.top, left: e.pageX - this.offset.left },
					absolute: { left: e.pageX - this.clickOffset.left, top: e.pageY - this.clickOffset.top },
					dom: this.currentItem.prev()[0]
				}
			});

			//If o.placeholder is used, create a new element at the given position with the class
                        if(o.placeholder) this.createPlaceholder();

			this.propagate("start", e); //Call plugins and callbacks
			this.helperProportions = { width: this.helper.outerWidth(), height: this.helper.outerHeight() }; //Save and store the helper proportions

			//If we have something in cursorAt, we'll use it
			if(o.cursorAt) {
				if(o.cursorAt.top != undefined || o.cursorAt.bottom != undefined) {
					this.offset.top -= this.clickOffset.top - (o.cursorAt.top != undefined ? o.cursorAt.top : (this.helperProportions.height - o.cursorAt.bottom));
					this.clickOffset.top = (o.cursorAt.top != undefined ? o.cursorAt.top : (this.helperProportions.height - o.cursorAt.bottom));
				}
				if(o.cursorAt.left != undefined || o.cursorAt.right != undefined) {
					this.offset.left -= this.clickOffset.left - (o.cursorAt.left != undefined ? o.cursorAt.left : (this.helperProportions.width - o.cursorAt.right));
					this.clickOffset.left = (o.cursorAt.left != undefined ? o.cursorAt.left : (this.helperProportions.width - o.cursorAt.right));
				}
			}

			if(this.options.placeholder != 'clone' && this.options.hideElement) $(this.currentItem).css('visibility', 'hidden'); //Set the original element visibility to hidden to still fill out the white space; unless hideElement is false (BAZ-7706)
			for (var i = this.containers.length - 1; i >= 0; i--) { this.containers[i].propagate("activate", e, this); } //Post 'activate' events to possible containers
			
			//Prepare possible droppables
			if($.ui.ddmanager) $.ui.ddmanager.current = this;
			if ($.ui.ddmanager && !o.dropBehaviour) $.ui.ddmanager.prepareOffsets(this, e);

			this.dragging = true;
			return false;
			
		},
		stop: function(e) {

			this.propagate("stop", e); //Call plugins and trigger callbacks
			if(this.position.dom != this.currentItem.prev()[0]) this.propagate("update", e); //Trigger update callback if the DOM position has changed
			if(!contains(this.element[0], this.currentItem[0])) { //Node was moved out of the current element
				this.propagate("remove", e);
				for (var i = this.containers.length - 1; i >= 0; i--){
					if(contains(this.containers[i].element[0], this.currentItem[0])) {
						this.containers[i].propagate("update", e, this);
						this.containers[i].propagate("receive", e, this);
					}
				};
			};
			
			//Post events to containers
			for (var i = this.containers.length - 1; i >= 0; i--){
				this.containers[i].propagate("deactivate", e, this);
				if(this.containers[i].containerCache.over) {
					this.containers[i].propagate("out", e, this);
					this.containers[i].containerCache.over = 0;
				}
			}
			
			//If we are using droppables, inform the manager about the drop
			if ($.ui.ddmanager && !this.options.dropBehaviour) $.ui.ddmanager.drop(this, e);
			
			this.dragging = false;
			if(this.cancelHelperRemoval) return false;
			if (this.options.hideElement) $(this.currentItem).css('visibility', ''); // BAZ-7706
			if(this.placeholder) this.placeholder.remove();
			this.helper.remove();

			return false;
			
		},
		//returns true if item1 and item2 are both in the same container as identified by the
		//containers' id attributes.  if either item has no container or their container has
		//no id attribute, this will return false.
		sameContainer: function(item1, item2) {
			if (item1 && item2 && item1[0] && item2[0] &&
			    item1[0].parentNode && item2[0].parentNode &&
			    item1[0].parentNode.id && item2[0].parentNode.id &&
			    item1[0].parentNode.id == item2[0].parentNode.id)
				return true;
			return false;
		},
                cursorInBoundingBox: function(cursor) {
                    var boxes = this.boundingBoxes;
                    for (var i = 0; i < boxes.length; i++) {
                        for (var j = 0; j < boxes[i].length; j++) {
                            var box = boxes[i][j];
                            if ((cursor.x >= box.x1) && (cursor.x <= box.x2) && (cursor.y >= box.y1) && (cursor.y <= box.y2)) {
                                return true;
                            }
                        }
                    }
                    return false;
                },
                cursorInContainerBoundingBox: function(cursor, item) {
                    var i = -1;
                    // find the item's container - we should create a secondary data struct of containerItems
                    // and a reverse pointer from item -> container since we use this information a lot [ywh 2008-05-28]
                    for (var j = 0; j < this.containers.length; j++) {
                        if (contains(this.containers[j].element[0], item[0])) {
                            i = j;
                            j = this.containers.length; //break
                        }
                    }
                    if (i >= this.boundingBoxes.length || i < 0) return false;
                    for (var j = 0; j < this.boundingBoxes[i].length; j++) {
                        var box = this.boundingBoxes[i][j];
                        if ((cursor.x >= box.x1) && (cursor.x <= box.x2) && (cursor.y >= box.y1) && (cursor.y <= box.y2)) {
                            return true;
                        }
                    }
                    return false;
                },
		drag: function(e) {

			//Compute the helpers position
			this.position.current = { top: e.pageY - this.offset.top, left: e.pageX - this.offset.left };
			this.position.absolute = { left: e.pageX - this.clickOffset.left, top: e.pageY - this.clickOffset.top };

                        //where is the hand cursor?
                        var cursor = { x: this.position.absolute.left + Math.round(this.helperProportions.width / 2),
                                       y: this.position.absolute.top + Math.round(this.helperProportions.height / 2) };
                        if (! this.currentItem.hasClass('no_cross_container')) {
                            if (this.cursorInBoundingBox(cursor)) {
                                this.helperInner.removeClass('sortable-nodrop');
                            } else {
                                this.helperInner.addClass('sortable-nodrop');
                            }
                        } else {
                            if (this.cursorInContainerBoundingBox(cursor, this.currentItem)) {
                                this.helperInner.removeClass('sortable-nodrop');
                            } else {
                                this.helperInner.addClass('sortable-nodrop');
                            }
                        }

			//Rearrange
			for (var i = this.items.length - 1; i >= 0; i--) {
				var intersection = this.intersectsWithEdge(this.items[i], this.currentContainer);
				if(!intersection) continue;

				//items with the 'no_cross_container' class can only be moved within the same container
				//this relies on the containers having an id= attribute
				if (this.currentItem.hasClass('no_cross_container') && !this.sameContainer(this.currentItem, this.items[i].item))
					continue;

				if(this.items[i].item[0] != this.currentItem[0] //cannot intersect with itself
					&&	this.currentItem[intersection == 1 ? "next" : "prev"]()[0] != this.items[i].item[0] //no useless actions that have been done before
					&&	!contains(this.currentItem[0], this.items[i].item[0]) //no action if the item moved is the parent of the item checked
					&& (this.options.type == 'semi-dynamic' ? !contains(this.element[0], this.items[i].item[0]) : true)
				) {
					
					this.direction = intersection == 1 ? "down" : "up";
					this.rearrange(e, this.items[i]);
					this.propagate("change", e); //Call plugins and callbacks
					break;
				}
			}

			//Post events to containers unless this item has 'no_cross_container' class
			if (! this.currentItem.hasClass('no_cross_container'))
				this.contactContainers(e);

			//Interconnect with droppables
			if($.ui.ddmanager) $.ui.ddmanager.drag(this, e);

			this.propagate("sort", e); //Call plugins and callbacks
			this.helper.css({ left: this.position.absolute.left+'px', top: this.position.absolute.top+'px' }); // Stick the helper to the cursor
			return false;
			
		},
		rearrange: function(e, i, a) {
			a ? a.append(this.currentItem) : i.item[this.direction == 'down' ? 'before' : 'after'](this.currentItem);
			this.refreshPositions(true); //Precompute after each DOM insertion, NOT on mousemove
			if(this.placeholderElement) this.placeholder.css(this.placeholderElement.offset());
                        if(this.placeholderElement) this.placeholder.css({ width: (this.placeholderElement[0].offsetWidth - this.options.moduleMargin), height: (this.placeholderElement[0].offsetHeight - this.options.moduleMargin) });
		}
	});
	
	$.extend($.ui.sortable, {
		getter: "serialize toArray",
		defaults: {
			items: '> *',
			zIndex: 1000
		}
	});

	
/*
 * Sortable Extensions
 */

	$.ui.plugin.add("sortable", "cursor", {
		start: function(e, ui) {
			var t = $('body');
			if (t.css("cursor")) ui.options._cursor = t.css("cursor");
			t.css("cursor", ui.options.cursor);
		},
		stop: function(e, ui) {
			if (ui.options._cursor) $('body').css("cursor", ui.options._cursor);
		}
	});

	$.ui.plugin.add("sortable", "zIndex", {
		start: function(e, ui) {
			var t = ui.helper;
			if(t.css("zIndex")) ui.options._zIndex = t.css("zIndex");
			t.css('zIndex', ui.options.zIndex);
		},
		stop: function(e, ui) {
			if(ui.options._zIndex) $(ui.helper).css('zIndex', ui.options._zIndex);
		}
	});

	$.ui.plugin.add("sortable", "opacity", {
		start: function(e, ui) {
			var t = ui.helper;
			if(t.css("opacity")) ui.options._opacity = t.css("opacity");
			t.css('opacity', ui.options.opacity);
		},
		stop: function(e, ui) {
			if(ui.options._opacity) $(ui.helper).css('opacity', ui.options._opacity);
		}
	});


	$.ui.plugin.add("sortable", "revert", {
		stop: function(e, ui) {
			var self = ui.instance;
			self.cancelHelperRemoval = true;
			var cur = self.currentItem.offset();
			var op = self.helper.offsetParent().offset();
			if(ui.instance.options.zIndex) ui.helper.css('zIndex', ui.instance.options.zIndex); //Do the zIndex again because it already was resetted by the plugin above on stop

			//Also animate the placeholder if we have one
			if(ui.instance.placeholder) ui.instance.placeholder.animate({ opacity: 'hide' }, parseInt(ui.options.revert, 10) || 500);
			
			
			ui.helper.animate({
				left: cur.left - op.left - self.margins.left,
				top: cur.top - op.top - self.margins.top
			}, parseInt(ui.options.revert, 10) || 500, function() {
				self.currentItem.css('visibility', 'visible');
				window.setTimeout(function() {
					if(self.placeholder) self.placeholder.remove();
					self.helper.remove();
					if(ui.options._zIndex) ui.helper.css('zIndex', ui.options._zIndex);
				}, 50);
			});
		}
	});

	
	$.ui.plugin.add("sortable", "containment", {
		start: function(e, ui) {

			var o = ui.options;
			if((o.containment.left != undefined || o.containment.constructor == Array) && !o._containment) return;
			if(!o._containment) o._containment = o.containment;

			if(o._containment == 'parent') o._containment = this[0].parentNode;
			if(o._containment == 'sortable') o._containment = this[0];
			if(o._containment == 'document') {
				o.containment = [
					0,
					0,
					$(document).width(),
					($(document).height() || document.body.parentNode.scrollHeight)
				];
			} else { //I'm a node, so compute top/left/right/bottom

				var ce = $(o._containment);
				var co = ce.offset();

				o.containment = [
					co.left,
					co.top,
					co.left+(ce.outerWidth() || ce[0].scrollWidth),
					co.top+(ce.outerHeight() || ce[0].scrollHeight)
				];
			}

		},
		sort: function(e, ui) {

			var o = ui.options;
			var h = ui.helper;
			var c = o.containment;
			var self = ui.instance;
			var borderLeft = (parseInt(self.offsetParent.css("borderLeftWidth"), 10) || 0);
			var borderRight = (parseInt(self.offsetParent.css("borderRightWidth"), 10) || 0);
			var borderTop = (parseInt(self.offsetParent.css("borderTopWidth"), 10) || 0);
			var borderBottom = (parseInt(self.offsetParent.css("borderBottomWidth"), 10) || 0);
			
			if(c.constructor == Array) {
				if((self.position.absolute.left < c[0])) self.position.current.left = c[0] - self.offsets.parent.left - self.margins.left;
				if((self.position.absolute.top < c[1])) self.position.current.top = c[1] - self.offsets.parent.top - self.margins.top;
				if(self.position.absolute.left - c[2] + self.helperProportions.width >= 0) self.position.current.left = c[2] - self.offsets.parent.left - self.helperProportions.width - self.margins.left - borderLeft - borderRight;
				if(self.position.absolute.top - c[3] + self.helperProportions.height >= 0) self.position.current.top = c[3] - self.offsets.parent.top - self.helperProportions.height - self.margins.top - borderTop - borderBottom;
			} else {
				if((ui.position.left < c.left)) self.position.current.left = c.left;
				if((ui.position.top < c.top)) self.position.current.top = c.top;
				if(ui.position.left - self.offsetParent.innerWidth() + self.helperProportions.width + c.right + borderLeft + borderRight >= 0) self.position.current.left = self.offsetParent.innerWidth() - self.helperProportions.width - c.right - borderLeft - borderRight;
				if(ui.position.top - self.offsetParent.innerHeight() + self.helperProportions.height + c.bottom + borderTop + borderBottom >= 0) self.position.current.top = self.offsetParent.innerHeight() - self.helperProportions.height - c.bottom - borderTop - borderBottom;
			}

		}
	});

	$.ui.plugin.add("sortable", "axis", {
		sort: function(e, ui) {
			var o = ui.options;
			if(o.constraint) o.axis = o.constraint; //Legacy check
			o.axis == 'x' ? ui.instance.position.current.top = ui.instance.originalPosition.top : ui.instance.position.current.left = ui.instance.originalPosition.left;
		}
	});

	$.ui.plugin.add("sortable", "scroll", {
		start: function(e, ui) {
			var o = ui.options;
			o.scrollSensitivity	= o.scrollSensitivity || 20;
			o.scrollSpeed		= o.scrollSpeed || 20;

			ui.instance.overflowY = function(el) {
				do { if((/auto|scroll/).test(el.css('overflow')) || (/auto|scroll/).test(el.css('overflow-y'))) return el; el = el.parent(); } while (el[0].parentNode);
				return $(document);
			}(this);
			ui.instance.overflowX = function(el) {
				do { if((/auto|scroll/).test(el.css('overflow')) || (/auto|scroll/).test(el.css('overflow-x'))) return el; el = el.parent(); } while (el[0].parentNode);
				return $(document);
			}(this);
			
			if(ui.instance.overflowY[0] != document && ui.instance.overflowY[0].tagName != 'HTML') ui.instance.overflowYstart = ui.instance.overflowY[0].scrollTop;
			if(ui.instance.overflowX[0] != document && ui.instance.overflowX[0].tagName != 'HTML') ui.instance.overflowXstart = ui.instance.overflowX[0].scrollLeft;
			
		},
		sort: function(e, ui) {
			
			var o = ui.options;
			var i = ui.instance;

			if(i.overflowY[0] != document && i.overflowY[0].tagName != 'HTML') {
				if(i.overflowY[0].offsetHeight - (ui.position.top - i.overflowY[0].scrollTop + i.clickOffset.top) < o.scrollSensitivity)
					i.overflowY[0].scrollTop = i.overflowY[0].scrollTop + o.scrollSpeed;
				if((ui.position.top - i.overflowY[0].scrollTop + i.clickOffset.top) < o.scrollSensitivity)
					i.overflowY[0].scrollTop = i.overflowY[0].scrollTop - o.scrollSpeed;				
			} else {
				//$(document.body).append('<p>'+(e.pageY - $(document).scrollTop())+'</p>');
				if(e.pageY - $(document).scrollTop() < o.scrollSensitivity)
					$(document).scrollTop($(document).scrollTop() - o.scrollSpeed);
				if($(window).height() - (e.pageY - $(document).scrollTop()) < o.scrollSensitivity)
					$(document).scrollTop($(document).scrollTop() + o.scrollSpeed);
			}
			
			if(i.overflowX[0] != document && i.overflowX[0].tagName != 'HTML') {
				if(i.overflowX[0].offsetWidth - (ui.position.left - i.overflowX[0].scrollLeft + i.clickOffset.left) < o.scrollSensitivity)
					i.overflowX[0].scrollLeft = i.overflowX[0].scrollLeft + o.scrollSpeed;
				if((ui.position.top - i.overflowX[0].scrollLeft + i.clickOffset.left) < o.scrollSensitivity)
					i.overflowX[0].scrollLeft = i.overflowX[0].scrollLeft - o.scrollSpeed;				
			} else {
				if(e.pageX - $(document).scrollLeft() < o.scrollSensitivity)
					$(document).scrollLeft($(document).scrollLeft() - o.scrollSpeed);
				if($(window).width() - (e.pageX - $(document).scrollLeft()) < o.scrollSensitivity)
					$(document).scrollLeft($(document).scrollLeft() + o.scrollSpeed);
			}
			
			//ui.instance.recallOffset(e);
			i.offset = {
				left: i.mouse.start.left - i.originalPosition.left + (i.overflowXstart !== undefined ? i.overflowXstart - i.overflowX[0].scrollLeft : 0),
				top: i.mouse.start.top - i.originalPosition.top + (i.overflowYstart !== undefined ? i.overflowYstart - i.overflowX[0].scrollTop : 0)
			};

		}
	});

})(jQuery);
