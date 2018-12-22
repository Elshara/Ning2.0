dojo.provide('xg.photo.album.edit');

dojo.require('dojo.dnd.*');
dojo.require('dojo.animation.*');
dojo.require('xg.photo.index._shared');

/**
 * Behavior for the Edit Album page.
 *
 * @param availablePhotosDiv  the div containing the photos available to add to the album
 * @param albumDiv  the div containing the photos in the album
 * @param numPages  the maximum number of pages for the album
 * @param numRows  the number of rows on each page
 * @param numColumns  the number of columns on each page
 * @param slotWidth  the width of each thumbnail slot in the album, in pixels
 * @param slotHeight  the height of each thumbnail slot in the album, in pixels
 * @param insertDivWidth  the size of the space between each column, in pixels
 * @param rowDistance  the size of the space between each row, in pixels
 * @param albumId  the contentId of the Album object
 * @param titleInput  the title of the album
 * @param descriptionInput  the album description
 * @param albumCoverDiv  the div that will hold the album cover
 * @param submitButton  the submit-button node
 * @param submitUrl  the URL of the endpoint to submit the form to
 * @param targetUrl  the URL to redirect to after submitting the form
 */
xg.photo.album.edit.AlbumEditor = function(availablePhotosDiv, albumDiv, numPages, numRows, numColumns,
        slotWidth, slotHeight, insertDivWidth, rowDistance, albumId, titleInput, descriptionInput,
        albumCoverDiv, submitButton, submitUrl, targetUrl) {
    // TODO: Split into properties and init method (which then can use the other methods)
    this.availablePhotosDiv = availablePhotosDiv;
    this.albumDiv = albumDiv;
    this.albumId = albumId;
    this.titleInput = titleInput;
    this.descriptionInput = descriptionInput;
    this.submitUrl = submitUrl;
    this.targetUrl = targetUrl;
    this.availablePhotosSources = new Array();
    this.availableSourcesById = new Object();
    this.slotSources = new Array();
    this.slotTargets = new Array();
    this.insertSlotTargets = new Array();
    this.idBySlotIdx = new Array();
    this.slotIdxById = new Object();
    this.coverPhotoId = null;
    this.curPage = 0;
    this.photoCount = 0;
    this.numPages = numPages;
    this.numRows = numRows;
    this.numColumns = numColumns;
    this.numSlotsPerPage = numRows * numColumns;
    this.numAlbumSlots = numPages * this.numSlotsPerPage;
    this.insertDivWidth = insertDivWidth;
    this.rowDistance = rowDistance;
    this.slotWidth = slotWidth;
    this.slotHeight = slotHeight;
    // Setting up available photos
    var availablePhotos = dojo.html.getElementsByClass('available', availablePhotosDiv);
    for (var idx = 0; idx < availablePhotos.length; idx++) {
        var dragSource = new xg.photo.album.edit.DragSource(this, availablePhotos[idx]);
        dragSource.disable();
        this.availablePhotosSources.push(dragSource);
    }
    new xg.photo.album.edit.RemoveDropTarget(this, availablePhotosDiv);
    // Setting up the album slots
    var slots = dojo.html.getElementsByClass('slot', this.albumDiv);
    var insertSlots = dojo.html.getElementsByClass('insertSlot', this.albumDiv);
    for (var slotIdx = 0; slotIdx < slots.length; slotIdx++) {
        this.idBySlotIdx[slotIdx] = null;
        var dragSource = new xg.photo.album.edit.DragSource(this, slots[slotIdx]);
        dragSource.disable();
        this.slotSources.push(dragSource);
        this.slotTargets[slotIdx] = new xg.photo.album.edit.SlotDropTarget(this, slots[slotIdx]);
        this.slotTargets[slotIdx].disable();
    }
    for (var slotIdx = 0; slotIdx < insertSlots.length; slotIdx++) {
        this.insertSlotTargets[slotIdx] = new xg.photo.album.edit.InsertSlotDropTarget(this, insertSlots[slotIdx]);
        this.insertSlotTargets[slotIdx].disable();
    }
    // Setting up the album cover slot
    this.albumCoverSource = new xg.photo.album.edit.DragSource(this, albumCoverDiv);
    this.albumCoverSource.disable();
    new xg.photo.album.edit.AlbumCoverDropTarget(this, albumCoverDiv);
    dojo.event.connect(submitButton, 'onclick', dojo.lang.hitch(this, function(event) {
        dojo.event.browser.stopEvent(event);
        this.submitAlbum();
    }));
    // PHO-396 [ David Sklar 2006-09-22 ]
    dojo.lang.forEach([titleInput, descriptionInput], function(input) {
        dojo.event.connect(input, 'onkeydown', function(event) {
            if (event.keyCode == dojo.event.browser.keys.KEY_ENTER) {
                dojo.event.browser.stopEvent(event);
                submitButton.click();
            }
        })
    }, true);
};
dojo.lang.extend(xg.photo.album.edit.AlbumEditor, {

    /**
     * Returns a div for containing a new thumbnail image.
     *
     * @param imgId  ID to use for the <img>
     * @param imgUrl  src for the <img>
     * @param title  (optional) title for the <img>
     * @param width  width of the <img>
     * @param height  height of the <img>
     * @return  a new div containing the thumbnail image
     */
    createImageObj: function(imgId, imgUrl, title, width, height) {
        var newDivNode = document.createElement('div');
        var newImgNode = document.createElement('img');
        newImgNode.id = imgId;
        newImgNode.src = imgUrl;
        if (title) { newImgNode.title = title; }
        newImgNode.width = width;
        newImgNode.height = height;
        newDivNode.appendChild(newImgNode);
        return newDivNode;
    },

    /**
     * Initializes the Previous Album Page link.
     *
     * @param prevPageLinkObj  the link
     */
    setPrevPageLink: function(prevPageLinkObj) {
        if (prevPageLinkObj.tagName.toUpperCase() == 'A') {
            this.prevPageLink = prevPageLinkObj;
        } else {
            this.prevPageLink = prevPageLinkObj.getElementsByTagName('a')[0];
        }
        this.updatePageLinks();
    },

    /**
     * Initializes the Next Album Page link.
     *
     * @param nextPageLinkObj  the link
     */
    setNextPageLink: function(nextPageLinkObj) {
        if (nextPageLinkObj.tagName.toUpperCase() == 'A') {
            this.nextPageLink = nextPageLinkObj;
        } else {
            this.nextPageLink = nextPageLinkObj.getElementsByTagName('a')[0];
        }
        this.updatePageLinks();
    },

    /**
     * Initializes the Next Album Page and Previous Album Page links.
     */
    updatePageLinks: function() {
        if (this.prevPageLink) {
            dojo.event.disconnect(this.prevPageLink, 'onclick', this, 'prevPage');
            if (this.curPage > 0) {
                dojo.html.removeClass(this.prevPageLink, 'disabled');
                dojo.event.connect(this.prevPageLink, 'onclick', this, 'prevPage');
            } else {
                dojo.html.addClass(this.prevPageLink, 'disabled');
            }
        }
        if (this.nextPageLink) {
            dojo.event.disconnect(this.nextPageLink, 'onclick', this, 'nextPage');
            // PHO-351 - Only show the next page link if the number of photos in the
            // album is >= the number of photos that fill up the pages up to and including
            // the current page. Since this.curPage is 0-based, we have to add 1 first [ David Sklar 2006-09-21 ]
            if ((this.curPage + 1 < this.numPages) && (this.photoCount >= ((this.curPage+1) * this.numSlotsPerPage))) {
                dojo.html.removeClass(this.nextPageLink, 'disabled');
                dojo.event.connect(this.nextPageLink, 'onclick', this, 'nextPage');
            } else {
                dojo.html.addClass(this.nextPageLink, 'disabled');
            }
        }
    },

    /**
     * Displays the next page in the album.
     */
    nextPage: function() {
        this.showPage(this.curPage + 1);
    },

    /**
     * Displays the previous page in the album.
     */
    prevPage: function() {
        this.showPage(this.curPage - 1);
    },

    /**
     * Displays the specified album page.
     *
     * @param pageIdx  the page number
     */
    showPage: function(pageIdx) {
        this.curPage = pageIdx;
        for (var pageIdx = 0; pageIdx < this.numPages; pageIdx++) {
            var pageObj = dojo.byId('albumPage' + pageIdx);
            dojo.html.setStyle(pageObj, 'display', pageIdx == this.curPage ? 'block' : 'none');
        }
        this.updatePageLinks();
    },

    /**
     * Displays the given images in the Available Photos section.
     *
     * @param imgObjs  array of <img> nodes to display
     * @param idPrefix  (optional) prefix used in the imgObjs IDs
     */
    setAvailablePhotos: function(imgObjs, idPrefix) {
        for (var idx = 0; idx < this.availablePhotosSources.length; idx++) {
            this.availablePhotosSources[idx].disable();
            dojo.dom.removeChildren(this.availablePhotosSources[idx].domNode);
        }
        this.availableSourcesById = new Object();
        for (var idx = 0; idx < imgObjs.length; idx++) {
            if (idx >= this.availablePhotosSources.length) { break; }
            var dragSource = this.availablePhotosSources[idx];
            dragSource.domNode.appendChild(imgObjs[idx]);
            xg.photo.fixImagesInIE(imgObjs[idx], false);
            var imgId = dragSource.domNode.getElementsByTagName('img')[0].id
            if (idPrefix != null) { imgId = imgId.substring(idPrefix.length); }
            this.availableSourcesById[imgId] = dragSource;
            if (this.slotIdxById[imgId] == null) {
                dragSource.enable();
            } else {
                // already in the album
                dragSource.disable();
            }
        }
    },

    enableSlot: function(slotIdx) {
        this.slotSources[slotIdx].enable();
        this.slotTargets[slotIdx].enable();
        this.insertSlotTargets[slotIdx].enable();
    },

    disableSlot: function(slotIdx) {
        this.slotSources[slotIdx].disable();
        this.slotTargets[slotIdx].disable();
        this.insertSlotTargets[slotIdx].disable();
    },

    /**
     * Adds the photo to the album, if it is not full.
     *
     * @param imgObj  an <img> node
     */
    addNewPhoto: function(imgObj) {
        if (this.photoCount < 100) { this.addPhoto(null, imgObj); }
    },

    /**
     * Adds the photo to the album.
     *
     * @param slotIdx  position at which to insert the photo, or null to add it to the end
     * @param imgObj  an <img> node, possibly one of the Available Photos
     * @param idPrefix  (optional) prefix used in the imgObj ID
     * @return  the position at which the photo was inserted
     */
    addPhoto: function(slotIdx, imgObj, idPrefix) {
        var realSlotIdx = slotIdx;
        if (realSlotIdx == null) { realSlotIdx = this.photoCount; }
        var targetDivNode = this.slotSources[realSlotIdx].domNode;
        // we add the new image object first
        targetDivNode.appendChild(imgObj);
        // and now we can search for the img object (which may be the imgObj but that is not guaranteed)
        var imgNode = targetDivNode.getElementsByTagName('img')[0];
        var imgId = imgNode.id
        if (idPrefix != null) {
            imgId = imgId.substring(idPrefix.length);
            imgNode.id = imgId;
        }
        this.enableSlot(realSlotIdx);
        this.slotIdxById[imgId] = realSlotIdx;
        this.idBySlotIdx[realSlotIdx] = imgId;
        this.photoCount++;
        var availableSource = this.availableSourcesById[imgId];
        if (availableSource != null) { availableSource.disable(); }
        // PHO-351 -- Make sure next page/prev page links are correct [ David Sklar 2006-09-22 ]
        this.updatePageLinks()
        return realSlotIdx;
    },

    /**
     * Inserts the photo into the album at the specified position.
     *
     * @param slotIdx  position at which to insert the photo, or null to add it to the end
     * @param imgObj  an <img> node
     * @param idPrefix  (optional) prefix used in the imgObj ID
     */
    insertPhoto: function(slotIdx, imgObj, idPrefix) {
        if (this.photoCount == this.numAlbumSlots) {
            // we're removing the content of the last slot
            this.removePhoto(this.photoCount - 1);
        }
        // next we're moving all photos after the slot away
        this.moveSlotsBackward(slotIdx, this.photoCount);
        // and finally we're moving the new photo to the slot at slotIdx
        this.addPhoto(slotIdx, imgObj, idPrefix);
    },

    /**
     * Removes the photo from the album at the specified position.
     *
     * @param slotIdx  position of the photo to remove from the album
     */
    removePhoto: function(slotIdx) {
        var imgId = this.idBySlotIdx[slotIdx];
        if (imgId != null) {
            var availableSource = this.availableSourcesById[imgId];
            if (availableSource != null) { availableSource.enable(); }
            if (this.coverPhotoId == imgId) { this.removeCover(); }
            dojo.dom.removeChildren(this.slotSources[slotIdx].domNode);
            this.photoCount--;
        }
        this.disableSlot(slotIdx);
        this.slotIdxById[imgId] = null;
        this.idBySlotIdx[slotIdx] = null;
        // now we're advancing all photos after the removed one
        this.moveSlotsForward(slotIdx, this.photoCount);
        // PHO-351 -- Make sure next page/prev page links are correct [ David Sklar 2006-09-22 ]
        this.updatePageLinks()
    },

    /**
     * Swaps two photos in the album
     *
     * @param slotIdxA  position of the first photo
     * @param slotIdxB  position of the second photo
     */
    swapPhotos: function(slotIdxA, slotIdxB) {
        var imgIdA = this.idBySlotIdx[slotIdxA];
        var imgIdB = this.idBySlotIdx[slotIdxB];
        if (imgIdA != null) {
            var slotSourceA = this.slotSources[slotIdxA];
            var imgObjA = slotSourceA.domNode.childNodes[0];
            if (imgIdB != null) {
                // swapping
                var slotSourceB = this.slotSources[slotIdxB];
                var imgObjB = slotSourceB.domNode.childNodes[0];
                slotSourceA.domNode.appendChild(imgObjB);
                slotSourceB.domNode.appendChild(imgObjA);
                this.slotIdxById[imgIdB] = slotIdxA;
                this.idBySlotIdx[slotIdxA] = imgIdB;
                this.slotIdxById[imgIdA] = slotIdxB;
                this.idBySlotIdx[slotIdxB] = imgIdA;
            } else {
                // move to end of album
                dojo.dom.removeChildren(slotSourceA.domNode);
                this.photoCount--;
                this.moveSlotsForward(slotIdxA, this.photoCount);
                this.addPhoto(null, imgObjA);
            }
        }
    },

    /**
     * Moves a photo from one position to another.
     *
     * @param sourceSlotIdx  the initial position
     * @param targetSlotIdx  the final position
     */
    movePhoto: function(sourceSlotIdx, targetSlotIdx) {
        if (sourceSlotIdx != targetSlotIdx) {
            var imgNode = this.slotSources[sourceSlotIdx].domNode.childNodes[0];
            var id = this.idBySlotIdx[sourceSlotIdx];
            var realTargetSlotIdx = targetSlotIdx;
            dojo.dom.removeChildren(this.slotSources[sourceSlotIdx].domNode);
            this.idBySlotIdx[sourceSlotIdx] = null;
            if (sourceSlotIdx < targetSlotIdx) {
                // moving back => we need to move nodes forward, though one less as in this
                // case we don't want to move the node in the slot at targetSlotIdx
                realTargetSlotIdx = targetSlotIdx - 1;
                this.moveSlotsForward(sourceSlotIdx, realTargetSlotIdx);
            } else {
                // moving forward => we need to move nodes back
                this.moveSlotsBackward(realTargetSlotIdx, sourceSlotIdx);
            }
            this.slotSources[realTargetSlotIdx].domNode.appendChild(imgNode);
            this.idBySlotIdx[realTargetSlotIdx] = id;
            this.slotIdxById[id] = realTargetSlotIdx;
            this.enableSlot(realTargetSlotIdx);
        }
    },

    /**
     * Moves the slots startSlotIdx + 1 to endSlotIdx forward by one slot. In this process the
     * slot startSlotIdx will be overwritten and the slot at endSlotIdx will become empty.
     */
    moveSlotsForward: function(startSlotIdx, endSlotIdx) {
        if (startSlotIdx >= endSlotIdx) { return; }
        var sourceSlotSource = null;
        var targetSlotIdx = startSlotIdx;
        var targetSlotSource = this.slotSources[targetSlotIdx];
        var tmpNode = null;
        var imgId = null;
        for (var sourceSlotIdx = startSlotIdx + 1; sourceSlotIdx <= endSlotIdx; sourceSlotIdx++) {
            sourceSlotSource = this.slotSources[sourceSlotIdx];
            // we move everything from source to target
            if (sourceSlotSource.domNode.hasChildNodes()) {
                tmpNode = sourceSlotSource.domNode.childNodes[0];
                dojo.dom.removeChildren(sourceSlotSource.domNode);
                targetSlotSource.domNode.appendChild(tmpNode);
                imgId = this.idBySlotIdx[sourceSlotIdx];
                this.idBySlotIdx[targetSlotIdx] = imgId;
                this.slotIdxById[imgId] = targetSlotIdx;
                this.enableSlot(targetSlotIdx);
            } else {
                this.idBySlotIdx[targetSlotIdx] = null;
                this.disableSlot(targetSlotIdx);
            }
            targetSlotSource = sourceSlotSource;
            targetSlotIdx = sourceSlotIdx;
        }
        this.idBySlotIdx[endSlotIdx] = null;
        this.disableSlot(endSlotIdx);
    },

    /**
     * Advances the slots from startSlotIdx to endSlotIdx - 1 by one. In this process the
     * slot endSlotIdx will be overwritten and the slot at startSlotIdx will become empty.
     */
    moveSlotsBackward: function(startSlotIdx, endSlotIdx) {
        if (startSlotIdx >= endSlotIdx) { return; }
        var sourceSlotSource = null;
        var targetSlotIdx = endSlotIdx;
        var targetSlotSource = this.slotSources[targetSlotIdx];
        var imgId = null;
        var tmpNode = null;
        for (var sourceSlotIdx = endSlotIdx - 1; sourceSlotIdx >= startSlotIdx; sourceSlotIdx--) {
            sourceSlotSource = this.slotSources[sourceSlotIdx];
            // we move everything from source to target
            if (sourceSlotSource.domNode.hasChildNodes()) {
                tmpNode = sourceSlotSource.domNode.childNodes[0];
                dojo.dom.removeChildren(sourceSlotSource.domNode);
                targetSlotSource.domNode.appendChild(tmpNode);
                imgId = this.idBySlotIdx[sourceSlotIdx];
                this.idBySlotIdx[targetSlotIdx] = imgId;
                this.slotIdxById[imgId] = targetSlotIdx;
                this.enableSlot(targetSlotIdx);
            } else {
                this.idBySlotIdx[targetSlotIdx] = null;
                this.disableSlot(targetSlotIdx);
            }
            targetSlotSource = sourceSlotSource;
            targetSlotIdx = sourceSlotIdx;
        }
        this.idBySlotIdx[startSlotIdx] = null;
        this.disableSlot(startSlotIdx);
    },

    /**
     * Sets the current cover photo.
     *
     * @param id  the content ID of the photo
     * @param coverImgObj  the <img> node
     */
    setCoverImg: function(id, coverImgObj) {
        this.coverPhotoId = id;
        dojo.dom.removeChildren(this.albumCoverSource.domNode);
        this.albumCoverSource.domNode.appendChild(coverImgObj);
        this.albumCoverSource.enable();
    },

    /**
     * Sets the current cover photo.
     *
     * @param slotIdx  the position of the album photo to use for the cover
     */
    setCover: function(slotIdx) {
        var slotSource= this.slotSources[slotIdx];
        var coverImgObj = slotSource.domNode.childNodes[0].cloneNode(true);
        this.setCoverImg(this.idBySlotIdx[slotIdx], coverImgObj);
    },

    /**
     * Adds the photo to the album and sets it as the cover.
     *
     * @param slotIdx  position at which to insert the photo, or null to add it to the end
     * @param imgObj  an <img> node, possibly one of the Available Photos
     * @param idPrefix  (optional) prefix used in the imgObj ID
     */
    addAndSetCover: function(slotIdx, imgObj, idPrefix) {
        var realSlotIdx = this.addPhoto(slotIdx, imgObj, idPrefix);
        this.setCover(realSlotIdx);
    },

    /**
     * Adds the photo to the album and, if no cover has been set, sets it as the cover.
     *
     * @param slotIdx  position at which to insert the photo, or null to add it to the end
     * @param imgObj  an <img> node, possibly one of the Available Photos
     * @param idPrefix  (optional) prefix used in the imgObj ID
     */
    addAndSetCoverIfEmpty: function(slotIdx, imgObj, idPrefix) {
        var realSlotIdx = this.addPhoto(slotIdx, imgObj, idPrefix);
        if (! this.coverPhotoId) { this.setCover(realSlotIdx); }
    },

    /**
     * Clears the album cover.
     */
    removeCover: function() {
        this.coverPhotoId = null;
        dojo.dom.removeChildren(this.albumCoverSource.domNode);
        this.albumCoverSource.disable();
    },

    /**
     * Clears the album cover and removes the photo from the album.
     */
    removeCoverCompletely: function() {
        if (this.coverPhotoId) { this.removePhoto(this.slotIdxById[this.coverPhotoId]); }
    },

    /**
     * Returns the content IDs of the photos in the album
     *
     * @return array  the content-object IDs, in order of appearance
     */
    getPhotoIds: function() {
        var result = new Array();
        for (var slotIdx = 0; slotIdx < this.idBySlotIdx.length; slotIdx++) {
            var id = this.idBySlotIdx[slotIdx];
            if (id != null) {
                result.push(id);
            }
        }
        return result;
    },

    /**
     * Saves the album.
     */
    submitAlbum: function() {
        dojo.byId('submitAlbumButton').disabled = true;
        dojo.io.bind({
            url: this.submitUrl,
            method: 'post',
            mimetype: 'text/javascript',
            encoding: 'utf-8',
            preventCache: true,
            content: {
                albumId: this.albumId ? this.albumId : '',
                title: dojo.string.trim(this.titleInput.value).length > 0 ? this.titleInput.value : xg.photo.nls.text('untitled'),
                description: this.descriptionInput.value,
                photos: this.getPhotoIds().join(' '),
                coverPhotoId: this.coverPhotoId ? this.coverPhotoId : ''
            },
            load: dojo.lang.hitch(this, function(type, data, event) {
                document.location.href = data.target;
            }),
            error: function(type, error) {
                // TODO
            }
        });
    }
});

/**
 * Extension of dojo's html drag source that is album editor aware, and that can be disabled
 */
xg.photo.album.edit.DragSource = function(editor, node) {
    this.editor = editor;
    this.isSlot = dojo.html.hasClass(node, 'slot');
    this.isAvailable = !this.isSlot && dojo.html.hasClass(node, 'available');
    this.isCover = !this.isSlot && !this.isAvailable && dojo.html.hasClass(node, 'albumCover');
    dojo.dnd.HtmlDragSource.call(this, node);
};
dojo.inherits(xg.photo.album.edit.DragSource, dojo.dnd.HtmlDragSource);
dojo.lang.extend(xg.photo.album.edit.DragSource, {

    isEnabled: true,

    enable: function() {
        this.isEnabled = true;
        dojo.html.removeClass(this.domNode, 'disabled');
        dojo.html.addClass(this.domNode, 'enabled');
    },

    disable: function() {
        this.isEnabled = false;
        dojo.html.removeClass(this.domNode, 'enabled');
        dojo.html.addClass(this.domNode, 'disabled');
    },

    onDragStart: function(event) {
        if (this.isEnabled) {
            return dojo.dnd.HtmlDragSource.prototype.onDragStart.apply(this, [ event ]);
        } else {
            return null;
        }
    }
});

/**
 * Extensions of dojo's drop target that are album editor aware
 */
xg.photo.album.edit.DropTarget = function(editor, node) {
    this.editor = editor;
    this.domNode = node;
    this.acceptedTypes = ['div'];
    dojo.dnd.DropTarget.call(this);
};
dojo.inherits(xg.photo.album.edit.DropTarget, dojo.dnd.DropTarget);
dojo.lang.extend(xg.photo.album.edit.DropTarget, {

    isEnabled: true,

    enable: function() {
        this.isEnabled = true;
    },

    disable: function() {
        this.isEnabled = false;
    },

    highlightOn: function() {
        if (this.isEnabled && this.shouldHighlight) {
            dojo.html.addClass(this.domNode, 'highlight');
        }
    },

    highlightOff: function() {
        if (this.isEnabled && this.shouldHighlight) {
            dojo.html.addClass(this.domNode, 'highlight');
        }
    },

    onDragOver: function(event) {
        this.highlightOn();
        return true;
    },

    onDragOut: function(event) {
        this.highlightOff();
        return true;
    },

    /**
     * Adds the photo to the album.
     */
    addToAlbum: function(sourceDomNode, targetDomNode) {
        if ((targetDomNode != null) && targetDomNode.hasChildNodes()) {
            var slotIdx = parseInt(targetDomNode.id.substring(4)); // removing 'slot'
            // we replace an image with another one from the list of available ones
            // TODO: use a replacePhoto function that avoids the node moving
            this.editor.removePhoto(slotIdx);
            this.editor.insertPhoto(slotIdx, sourceDomNode.childNodes[0].cloneNode(true), 'available');
        } else {
            this.editor.addAndSetCoverIfEmpty(null, sourceDomNode.childNodes[0].cloneNode(true), 'available');
        }
    },

    /**
     * Swaps two photos in the album
     */
    swapSlots: function(sourceDomNode, targetDomNode) {
        var slotIdxA = parseInt(sourceDomNode.id.substring(4)); // removing 'slot'
        var slotIdxB = !targetDomNode ? null : parseInt(targetDomNode.id.substring(4)); // removing 'slot'
        this.editor.swapPhotos(slotIdxA, slotIdxB);
    },

    /**
     * Clears the album cover.
     */
    removeCover: function() {
        this.editor.removeCover();
    }
});

xg.photo.album.edit.SlotDropTarget = function(editor, node) {
    xg.photo.album.edit.DropTarget.call(this, editor, node);
};
dojo.inherits(xg.photo.album.edit.SlotDropTarget, xg.photo.album.edit.DropTarget);
dojo.lang.extend(xg.photo.album.edit.SlotDropTarget, {

    onDrop: function(event) {
        this.highlightOff();
        if (this.isEnabled) {
            if (this.domNode.id == event.dragSource.domNode.id) {
                return false;
            }
        }
        if (event.dragSource.isSlot) {
            this.swapSlots(event.dragSource.domNode, this.domNode);
            return true;
        } else if (event.dragSource.isAvailable) {
            this.addToAlbum(event.dragSource.domNode, this.domNode);
            return true;
        } else if (event.dragSource.isCover) {
            this.removeCover();
            return true;
        } else {
            return false;
        }
    }
});

xg.photo.album.edit.InsertSlotDropTarget = function(editor, node) {
    xg.photo.album.edit.DropTarget.call(this, editor, node);
};
dojo.inherits(xg.photo.album.edit.InsertSlotDropTarget, xg.photo.album.edit.DropTarget);
dojo.lang.extend(xg.photo.album.edit.InsertSlotDropTarget, {

    onDragOver: function(event) {
        if (this.isEnabled) {
            if (this.anim) {
                this.anim.stop();
            }
            this.anim = new dojo.animation.Animation(new dojo.math.curves.Line([this.editor.insertDivWidth], [this.editor.insertDivWidth + this.editor.insertDivWidth + this.editor.slotWidth]), 75, false, 0, 15);
            dojo.event.connect(this.anim, "onAnimate", dojo.lang.hitch(this, function(e) {
                this.domNode.style.width = e.x + "px";
                this.domNode.parentNode.style.width = e.x + this.editor.slotWidth + "px";
            }));
            this.anim.play();
        }
        return xg.photo.album.edit.DropTarget.prototype.onDragOver.apply(this, [ event ]);
    },

    onDragOut: function(event) {
        if (this.isEnabled) {
            this.reset();
        }
        return xg.photo.album.edit.DropTarget.prototype.onDragOut.apply(this, [ event ]);
    },

    onDrop: function(event) {
        this.highlightOff();
        if (event.dragSource.isAvailable) {
            if (this.isEnabled) {
                this.insertIntoAlbum(event.dragSource.domNode, this.domNode);
            } else {
                this.addToAlbum(event.dragSource.domNode, null);
            }
            this.reset();
            return true;
        } else if (event.dragSource.isSlot) {
            if (this.isEnabled) {
                this.move(event.dragSource.domNode, this.domNode);
            } else {
                this.swapSlots(event.dragSource.domNode, null);
            }
            this.reset();
            return true;
        } else if (event.dragSource.isCover) {
            this.removeCover();
            this.reset();
            return true;
        } else {
            return false;
        }
    },

    reset: function() {
        if (this.anim) {
            this.anim.stop();
            this.anim = null;
        }
        this.domNode.style.width = this.editor.insertDivWidth + "px";
        this.domNode.parentNode.style.width = this.editor.insertDivWidth + this.editor.slotWidth + "px";
    },

    /**
     * Inserts the photo into the album at the specified position.
     */
    insertIntoAlbum: function(sourceDomNode, targetDomNode) {
        var slotIdx = parseInt(targetDomNode.id.substring(10)); // removing 'insertSlot'
        this.editor.insertPhoto(slotIdx, sourceDomNode.childNodes[0].cloneNode(true), 'available');
    },

    /**
     * Moves a photo from one position to another.
     */
    move: function(sourceDomNode, targetDomNode) {
        var sourceSlotIdx = parseInt(sourceDomNode.id.substring(4));  // removing 'slot'
        var targetSlotIdx = parseInt(targetDomNode.id.substring(10)); // removing 'insertSlot'
        this.editor.movePhoto(sourceSlotIdx, targetSlotIdx);
    }
});

xg.photo.album.edit.RemoveDropTarget = function(editor, node) {
    xg.photo.album.edit.DropTarget.call(this, editor, node);
};
dojo.inherits(xg.photo.album.edit.RemoveDropTarget, xg.photo.album.edit.DropTarget);
dojo.lang.extend(xg.photo.album.edit.RemoveDropTarget, {

    onDrop: function(event) {
        this.highlightOff();
        if (event.dragSource.isSlot) {
            this.removeFromAlbum(event.dragSource.domNode);
            return true;
        } else if (event.dragSource.isCover) {
            this.removeCoverFromAlbum();
            return true;
        } else {
            return false;
        }
    },

    /**
    * Removes the photo from the album at the specified position.
    */
    removeFromAlbum: function(sourceDomNode) {
        var slotIdx = parseInt(sourceDomNode.id.substring(4)); // removing 'slot'
        this.editor.removePhoto(slotIdx);
    },

    /**
     * Clears the album cover and removes the photo from the album.
     */
    removeCoverFromAlbum: function() {
        this.editor.removeCoverCompletely();
    }
});

xg.photo.album.edit.AlbumCoverDropTarget = function(editor, node) {
    xg.photo.album.edit.DropTarget.call(this, editor, node);
};
dojo.inherits(xg.photo.album.edit.AlbumCoverDropTarget, xg.photo.album.edit.DropTarget);
dojo.lang.extend(xg.photo.album.edit.AlbumCoverDropTarget, {

    onDrop: function(event) {
        this.highlightOff();
        if (event.dragSource.isSlot) {
            this.setCover(event.dragSource.domNode);
            return true;
        } else if (event.dragSource.isAvailable) {
            this.addAndSetCover(event.dragSource.domNode);
            return true;
        } else {
            return false;
        }
    },

    /**
     * Sets the current cover photo.
     */
    setCover: function(sourceDomNode) {
        var slotIdx = parseInt(sourceDomNode.id.substring(4)); // removing 'slot'
        this.editor.setCover(slotIdx);
    },

    /**
     * Adds the photo to the album and sets it as the cover.
     */
    addAndSetCover: function(sourceDomNode) {
        this.editor.addAndSetCover(null, sourceDomNode.childNodes[0].cloneNode(true), 'available');
    }

});

/**
 * Behavior for the Available Photos section.
 *
 * @param searchForAvailablePhotosUrl  the URL for the endpoint for searching the available photos
 * @param searchForm  the form element for submitting search queries
 * @param myPhotosRadioButton  the My Photos radio button
 * @param allPhotosRadioButton  the Everyone's Photos radio button
 * @param tagsInput  the Tags text field
 * @param newerPhotosButton  the Newer Available Photos link
 * @param olderPhotosButton  the Older Available Photos link
 * @param albumEditor  the AlbumEditor for the page
 */
xg.photo.album.edit.AvailablePhotosHandler = function(searchForAvailablePhotosUrl, searchForm,
        myPhotosRadioButton, allPhotosRadioButton, tagsInput, newerPhotosButton, olderPhotosButton, albumEditor) {
    this.url = searchForAvailablePhotosUrl;
    this.myPhotosRadioButton = myPhotosRadioButton;
    this.allPhotosRadioButton = allPhotosRadioButton;
    this.tagsInput = tagsInput;
    this.newerPhotosButton = newerPhotosButton;
    this.olderPhotosButton = olderPhotosButton;
    this.albumEditor = albumEditor;
    this.curPage = 1;
    this.numPages = 1;
    dojo.event.connect(searchForm, 'onsubmit', dojo.lang.hitch(this, function(event) {
        dojo.event.browser.stopEvent(event);
        this.doSearch(1, albumEditor);
    }));
    dojo.event.connect(newerPhotosButton, 'onclick', dojo.lang.hitch(this, function(event) {
        dojo.event.browser.stopEvent(event);
        if (this.curPage > 1) { this.doSearch(this.curPage - 1, albumEditor); }
    }));
    dojo.event.connect(olderPhotosButton, 'onclick', dojo.lang.hitch(this, function(event) {
        dojo.event.browser.stopEvent(event);
        if (this.curPage < this.numPages) { this.doSearch(this.curPage + 1, albumEditor); }
    }));
    dojo.event.connect(myPhotosRadioButton, 'onclick', dojo.lang.hitch(this, function(event) {
        this.doSearch(1, albumEditor);
    }));
    dojo.event.connect(allPhotosRadioButton, 'onclick', dojo.lang.hitch(this, function(event) {
        this.doSearch(1, albumEditor);
    }));
};
dojo.lang.extend(xg.photo.album.edit.AvailablePhotosHandler, {

    /**
     * Initializes the Newer Available Photos and Older Available Photos links.
     */
    setPageInfo: function(curPage, numPages) {
        this.curPage = curPage;
        this.numPages = numPages;
        if (curPage == 1) {
            dojo.html.addClass(this.newerPhotosButton, 'disabled');
        } else {
            dojo.html.removeClass(this.newerPhotosButton, 'disabled');
        }
        if (curPage == numPages) {
            dojo.html.addClass(this.olderPhotosButton, 'disabled');
        } else {
            dojo.html.removeClass(this.olderPhotosButton, 'disabled');
        }
    },

    /**
     * Retrieves photos from the server, possibly filtered by search terms.
     *
     * @param targetPage  the page number of the page to retrieve
     * @param albumEditor  the AlbumEditor for the page
     */
    doSearch: function(targetPage, albumEditor) {
        dojo.html.setStyle(document.body, 'cursor', 'wait');
        dojo.io.bind({
            url: this.url,
            preventCache: true,
            encoding: 'utf-8',
            mimetype: 'text/javascript',
            content: {
                origin: this.myPhotosRadioButton.checked ? 'my' : 'all',
                tags: this.tagsInput.value,
                page: targetPage
            },
            load: dojo.lang.hitch(this, function(type, data, event) {
                var availablePhotoObjs = new Array();
                var enableNewerLink = false;
                var enableOlderLink = false;
                this.setPageInfo(1, 1);
                if (data && data.photoUrlsById) {
                    for (photoId in data.photoUrlsById) {
                        var imgUrl = data.photoUrlsById[photoId];
                        var title = data.photoTitlesById[photoId];
                        var lastSlashPos = imgUrl.lastIndexOf('/');
                        var lastParamPos = imgUrl.lastIndexOf('?');
                        var concatChar = '?';
                        if ((lastParamPos != -1) && ((lastSlashPos == -1) || (lastParamPos > lastSlashPos))) { concatChar = '&'; }
                        imgUrl = imgUrl + concatChar + 'width=' + albumEditor.slotWidth + '&height=' + albumEditor.slotHeight;
                        availablePhotoObjs.push(albumEditor.createImageObj('available' + photoId, imgUrl, title, albumEditor.slotWidth, albumEditor.slotHeight));
                    }
                    if (data.page && data.numPages) { this.setPageInfo(data.page, data.numPages); }
                }
                albumEditor.setAvailablePhotos(availablePhotoObjs, 'available');
                dojo.html.setStyle(document.body, 'cursor', 'auto');
            }),
            error: function(type, error) {
                dojo.html.setStyle(document.body, 'cursor', 'auto');
            }
        });
    }
});
