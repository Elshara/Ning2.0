dojo.provide('xg.photo.flickr.import');


(function() {

    var dataset = Array();

    // The ningbar may also contain forms
    var form = dojo.byId('xg_body').getElementsByTagName('form')[0];

    var forwardToCleanup = function() {
        document.location.href = '/photo/flickr/postFlickr/';
    }

    var finishAndForward = function() {
        dojo.style.hide(dojo.byId('importSpinner'));
        dojo.style.hide(dojo.byId('importingProgress'));
        dojo.style.hide(dojo.byId('importWarning'));
        dojo.byId('importMessage').innerHTML = "<p><b>" + xg.photo.nls.done + "</b></p><p>" + xg.photo.nls.takingYou + "</p>";
        // TODO: fix possible race condition with postFlickr and saving of last flickr image to content store [ywh 2008-04-23]
        window.setTimeout(forwardToCleanup,1000);
    }

    dojo.event.connect(form, 'onsubmit', function(event) {
        button = dojo.html.getElementsByClass('button', form)[0];
        button.disabled = true;
        dojo.byId('importMessage').innerHTML = "<p style='padding-bottom:30px;'><b>" + xg.photo.nls.starting + "</b></p>";
        dojo.style.show(dojo.byId('importingProgress'));
        dojo.style.show(dojo.byId('importWarning'));

        /**
         * Retrieves photos from Flickr. This method calls itself to process each photo.
         *
         * @param Array photoPackage  Pages of data retrieved from Flickr
         * @param integer current  Index of the current photo to retrieve
         * @param string importSet  Format of the data: "search" or "set"
         */
        function slurpPhotos(photoPackage,current,importSet,pageSize,getDescriptions,getOriginals,totalPhotos,auth_token) {
            if (importSet == "search") {
                if (photoPackage.length > 1) {
                    var currentDataset = Math.floor(current / pageSize);
                    var adjustedP = current - (currentDataset * pageSize);
                    var photo = photoPackage[currentDataset].photos.photo[adjustedP];
                } else {
                    var photo = photoPackage[0].photos.photo[current];
                }
            } else {
                if (photoPackage.length > 1) {
                    var currentDataset = Math.floor(current / pageSize);
                    var adjustedP = current - (currentDataset * pageSize);
                    var photo = photoPackage[currentDataset].photoset.photo[adjustedP];
                } else {
                    var photo = photoPackage[0].photoset.photo[current];
                }
            }
            var url = "http://farm" + photo['farm'] + ".static.flickr.com/" + photo['server'] + "/" + photo['id'] + "_" + photo['secret'] + ".jpg";
            var tags = photo['tags'];
            var lat = photo['latitude'];
            var lng = photo['longitude'];
            var title = photo['title'];
            var photoid = photo['id'];
            
            // first one; no call to self
            dojo.byId('importMessage').innerHTML = "<p><b>" + xg.photo.nls.importingNofMPhotos(totalPhotos - current,totalPhotos) + "</b></p>";
            dojo.io.bind({
                url: '/photo/flickr/importPhoto/',
                method: 'post',
                mimetype: 'text/json',
                encoding: 'utf-8',
                content: {  url: url, tags: tags, lat: lat, lng: lng, title: title, 
                            id: photoid, auth_token: auth_token, desc: getDescriptions, 
                            orig: getOriginals },
                error: function(type, error){
                        // pass
                     },
                load: function(type, data, evt){
                    if (data.url) {
                        dojo.byId('importingProgress').src = data.url;
                    }
                }
            });
            // second one, calls self
            if (current > 0) {
                current -= 1;
                if (importSet == "search") {
                    if (photoPackage.length > 1) {
                        var currentDataset = Math.floor(current / pageSize);
                        var adjustedP = current - (currentDataset * pageSize);
                        var photo = photoPackage[currentDataset].photos.photo[adjustedP];
                    } else {
                        var photo = photoPackage[0].photos.photo[current];
                    }
                } else {
                    if (photoPackage.length > 1) {
                        var currentDataset = Math.floor(current / pageSize);
                        var adjustedP = current - (currentDataset * pageSize);
                        var photo = photoPackage[currentDataset].photoset.photo[adjustedP];
                    } else {
                        var photo = photoPackage[0].photoset.photo[current];
                    }
                }
                var url = "http://farm" + photo['farm'] + ".static.flickr.com/" + photo['server'] + "/" + photo['id'] + "_" + photo['secret'] + ".jpg";
                var tags = photo['tags'];
                var lat = photo['latitude'];
                var lng = photo['longitude'];
                var title = photo['title'];
                var photoid = photo['id'];
                dojo.io.bind({
                    url: '/photo/flickr/importPhoto/',
                    method: 'post',
                    mimetype: 'text/json',
                    encoding: 'utf-8',
                    content: {  url: url, tags: tags, lat: lat, lng: lng, title: title, 
                                id: photoid, auth_token: auth_token, desc: getDescriptions, 
                                orig: getOriginals },
                    error: function(type, error){
                        if (current > 0) {
                            current -= 1;
                            dojo.byId('importMessage').innerHTML = "<p><b>" + xg.photo.nls.importingNofMPhotos(totalPhotos - current,totalPhotos) + "</b></p>";
                            slurpPhotos(photoPackage,current,importSet,pageSize,getDescriptions,getOriginals,totalPhotos,auth_token);
                        } else {
                            finishAndForward();
                        }
                         },
                    load: function(type, data, evt){
                        if (data.url) {
                            dojo.byId('importMessage').innerHTML = "<p><b>" + xg.photo.nls.importingNofMPhotos(totalPhotos - current,totalPhotos) + "</b></p>";
                            dojo.byId('importingProgress').src = data.url;
                            if (current > 0) {
                                current -= 1;
                                slurpPhotos(photoPackage,current,importSet,pageSize,getDescriptions,getOriginals,totalPhotos,auth_token);
                            } else {
                                finishAndForward();
                            }
                        } else {
                            if (current > 0) {
                                current -= 1;
                                dojo.byId('importMessage').innerHTML = "<p><b>" + xg.photo.nls.importingNofMPhotos(totalPhotos - current,totalPhotos) + "</b></p>";
                                slurpPhotos(photoPackage,current,importSet,pageSize,getDescriptions,getOriginals,totalPhotos,auth_token);
                            } else {
                                finishAndForward();
                            }
                        }
                    }
                });
            } else {
                finishAndForward();
            }
        } 

        var displayError = function() {
            dojo.byId('importMessage').innerHTML = "<p><b>" + xg.photo.nls.anErrorOccurred + "</b></p>";
            button = dojo.html.getElementsByClass('button', form)[0];
            button.disabled = false;
            dojo.style.hide(dojo.byId('importSpinner'));
            dojo.style.hide(dojo.byId('importWarning'));
        }

        var displayEmptySet = function() {
            dojo.byId('importMessage').innerHTML = "<p>" + xg.photo.nls.weCouldntFind + "</p>";
            button = dojo.html.getElementsByClass('button', form)[0];
            button.disabled = false;
            dojo.style.hide(dojo.byId('importSpinner'));
            dojo.style.hide(dojo.byId('importWarning'));
        }

        var returnNewPhotoSet = function(auth_token, nsid, page, type, extras) {
            dojo.io.bind({
                url: '/photo/flickr/runImport/',
                method: 'post',
                mimetype: 'text/json',
                sync: true,
                encoding: 'utf-8',
                content: { type: type, extras: extras, auth_token: auth_token, nsid: nsid, page: page },
                load: function(type, data, evt) {
                        dataset.push(data);
                }
            });
        }

        dojo.style.show(dojo.byId('importSpinner'));
        var importType = null;
        for (var c = 0; c < form.importoption.length; c++) {
            if (form.importoption[c].checked == true) {
                var importType = form.importoption[c].value;
            }
        }
        var extraVars = null
        if (importType == "recentX") {
            extraVars = dojo.byId('numRecent')[dojo.byId('numRecent').selectedIndex].value;
        }
        if (importType == "gettagged") {
            extraVars = dojo.byId('flickrTagged').value;
        }
        if (dojo.byId('setChooser')) {
            if (importType == "chosenset") {
                extraVars = dojo.byId('setChooser')[dojo.byId('setChooser').selectedIndex].value;
            }
        }
        var auth_token = form.token.value;
        var nsid = form.nsid.value;
        var getDescriptions = dojo.byId('getdescriptions').checked;
        var getOriginals = dojo.byId('getoriginals').checked;

        dojo.io.bind({
            url: form.action,
            method: 'post',
            mimetype: 'text/json',
            encoding: 'utf-8',
            content: { type: importType, extras: extraVars, nsid: nsid, auth_token: auth_token },
            load: function(type, data, evt){
                // See http://www.flickr.com/services/api/response.json.html
                // and http://www.flickr.com/services/api/flickr.photos.search.html  [Jon Aquino 2007-02-09]
                if (data.stat == 'ok') {
                    var totalPhotos;
                    var importSet;
                    var totalPages = 1;
                    var photoset;
                    var pageSize = 500;
                    sampleArray = new Array();
                    // First step is to grab all the pages (which we store in the "dataset" variable). [Jon Aquino 2007-02-09]
                    if (typeof(data.photoset) == typeof(sampleArray)) {
                        if (data.photoset['per_page'] == 500 && data.photoset['total'] > 500) {
                            totalPhotos = data.photoset['total'];
                            totalPages = data.photoset['pages'];
                            dataset[0] = data;
                            for (var i = 2; i <= totalPages; i++) {
                                returnNewPhotoSet(auth_token, nsid, i, importType, extraVars);
                            }
                        } else {
                            totalPhotos = data.photoset.photo.length;
                            dataset[0] = data;
                        }
                        photoset = data.photoset.photo.length;
                        importSet = "set";
                    } else {
                        if (data.photos['perpage'] == 500 && data.photos['total'] > 500) {
                            if (importType == "gettagged") {
                                extraVars = dojo.byId('flickrTagged').value;
                            } else {
                                extraVars = '';
                            }
                            totalPhotos = data.photos['total'];
                            totalPages = data.photos['pages'];
                            dataset[0] = data;
                            for (var i = 2; i <= totalPages; i++) {
                                returnNewPhotoSet(auth_token, nsid, i, importType, extraVars);
                            }
                        } else {
                            totalPhotos = data.photos.photo.length;
                            dataset[0] = data;
                        }
                        photoset = data.photos.photo.length;
                        importSet = "search";
                    }
                    if (totalPhotos > 0) {
                        dojo.byId('importMessage').innerHTML = "<p><b>" + xg.photo.nls.importingNofMPhotos(1,totalPhotos) + "</b></p>";
                        slurpPhotos(dataset,totalPhotos-1,importSet,pageSize,getDescriptions,getOriginals,totalPhotos,auth_token);
                    } else {
                        displayEmptySet();
                    }
                } else {
                    displayError();
                }
            }
        });

        dojo.event.browser.stopEvent(event);
    });

    // wire up select events to the appropriate radio button
    dojo.event.connect(dojo.byId('numRecent'), 'onclick', function(event) {
        dojo.byId('import_recent').checked = true;
    });
    dojo.event.connect(dojo.byId('flickrTagged'), 'onfocus', function(event) {
        dojo.byId('import_tagged').checked = true;
    });
    if (dojo.byId('setChooser')) {
        dojo.event.connect(dojo.byId('setChooser'), 'onclick', function(event) {
            dojo.byId('import_set').checked = true;
        });
    }

}());
