//
// This is the main app for the petadoptions module
//
function ciniki_petadoptions_main() {
    //
    // The panel to list the animal
    //
    this.menu = new M.panel('animal', 'ciniki_petadoptions_main', 'menu', 'mc', 'medium', 'sectioned', 'ciniki.petadoptions.main.menu');
    this.menu.data = {};
    this.menu.nplist = [];
    this.menu.sections = {
        '_tabs':{'label':'', 'type':'paneltabs', 'selected':'10', 'tabs':{
            '10':{'label':'Available', 'fn':'M.ciniki_petadoptions_main.menu.switchTab("10");'},
            '50':{'label':'Adopted', 'fn':'M.ciniki_petadoptions_main.menu.switchTab("50");'},
            }},
        'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':1,
            'cellClasses':[''],
            'hint':'Search animals',
            'noData':'No animal found',
            },
        'animals':{'label':'Animals', 'type':'simplegrid', 'num_cols':1,
            'noData':'No animal',
            'addTxt':'Add Animal',
            'addFn':'M.ciniki_petadoptions_main.animal.open(\'M.ciniki_petadoptions_main.menu.open();\',0,null);'
            },
    }
    this.menu.liveSearchCb = function(s, i, v) {
        if( s == 'search' && v != '' ) {
            M.api.getJSONBgCb('ciniki.petadoptions.animalSearch', {'business_id':M.curBusinessID, 'start_needle':v, 'limit':'25'}, function(rsp) {
                M.ciniki_petadoptions_main.menu.liveSearchShow('search',null,M.gE(M.ciniki_petadoptions_main.menu.panelUID + '_' + s), rsp.animals);
                });
        }
    }
    this.menu.liveSearchResultValue = function(s, f, i, j, d) {
        return d.name;
    }
    this.menu.liveSearchResultRowFn = function(s, f, i, j, d) {
        return 'M.ciniki_petadoptions_main.animal.open(\'M.ciniki_petadoptions_main.menu.open();\',\'' + d.id + '\');';
    }
    this.menu.cellValue = function(s, i, j, d) {
        if( s == 'animals' ) {
            switch(j) {
                case 0: return d.name;
            }
        }
    }
    this.menu.rowFn = function(s, i, d) {
        if( s == 'animals' ) {
            return 'M.ciniki_petadoptions_main.animal.open(\'M.ciniki_petadoptions_main.menu.open();\',\'' + d.id + '\',M.ciniki_petadoptions_main.animal.nplist);';
        }
    }
    this.menu.switchTab = function(t) {
        this.sections._tabs.selected = t;
        this.open();
    }
    this.menu.noData = function(s) { return this.sections[s].noData; }
    this.menu.open = function(cb) {
        M.api.getJSONCb('ciniki.petadoptions.animalList', {'business_id':M.curBusinessID, 'status':this.sections._tabs.selected}, function(rsp) {
            if( rsp.stat != 'ok' ) {
                M.api.err(rsp);
                return false;
            }
            var p = M.ciniki_petadoptions_main.menu;
            p.data = rsp;
            p.nplist = (rsp.nplist != null ? rsp.nplist : null);
            p.refresh();
            p.show(cb);
        });
    }
    this.menu.addClose('Back');

    //
    // The panel to edit Animals
    //
    this.animal = new M.panel('Animals', 'ciniki_petadoptions_main', 'animal', 'mc', 'medium mediumaside', 'sectioned', 'ciniki.petadoptions.main.animal');
    this.animal.data = null;
    this.animal.animal_id = 0;
    this.animal.nplist = [];
    this.animal.sections = {
        '_primary_image_id':{'label':'Image', 'type':'imageform', 'aside':'yes', 'fields':{
            'primary_image_id':{'label':'', 'type':'image_id', 'hidelabel':'yes', 'controls':'all', 'history':'no',
                'addDropImage':function(iid) {
                    M.ciniki_petadoptions_main.animal.setFieldValue('primary_image_id', iid);
                    return true;
                    },
                'addDropImageRefresh':'',
                'removeImage':function(fid) {
                    M.ciniki_petadoptions_main.animal.setFieldValue(fid,0);
                    return true;
                 },
             },
        }},
        'general':{'label':'', 'aside':'yes', 'fields':{
            'name':{'label':'Name', 'required':'yes', 'type':'text'},
            'flags':{'label':'Options', 'type':'flags', 'flags':{'1':{'name':'Visible'}}},
            'status':{'label':'Status', 'type':'toggle', 'toggles':{'10':'Available', '50':'Adopted'}},
            'category':{'label':'Category', 'type':'text', 'livesearch':'yes', 'livesearchempty':'yes',
                'visible':function() { return M.modFlagSet('ciniki.petadoptions', 0x01); },
            },
            'breed':{'label':'Breed', 'type':'text', 'livesearch':'yes', 'livesearchempty':'yes'},
            'sex':{'label':'Sex', 'type':'text', 'livesearch':'yes', 'livesearchempty':'yes'},
            'years':{'label':'Age', 'type':'text', 'livesearch':'yes', 'livesearchempty':'yes'},
            'color':{'label':'Color', 'type':'text', 'livesearch':'yes', 'livesearchempty':'yes'},
            'size':{'label':'Size', 'type':'text', 'livesearch':'yes', 'livesearchempty':'yes'},
            'location':{'label':'Location', 'type':'text'},
            }},
        '_synopsis':{'label':'Synopsis', 'fields':{
            'synopsis':{'label':'', 'hidelabel':'yes', 'type':'textarea', 'size':'small'},
            }},
        '_description':{'label':'Description', 'fields':{
            'description':{'label':'', 'hidelabel':'yes', 'type':'textarea', 'size':'large'},
            }},
        'images':{'label':'Additional Images', 'type':'simplethumbs'},
        '_images':{'label':'', 'type':'simplegrid', 'num_cols':1,
            'addTxt':'Add Additional Image',
            'addFn':'M.ciniki_petadoptions_main.animal.save("M.ciniki_petadoptions_main.image.open(\'M.ciniki_petadoptions_main.animal.refreshImages();\',0,M.ciniki_petadoptions_main.animal.animal_id);");',
            },
        '_buttons':{'label':'', 'buttons':{
            'save':{'label':'Save', 'fn':'M.ciniki_petadoptions_main.animal.save();'},
            'delete':{'label':'Delete', 
                'visible':function() {return M.ciniki_petadoptions_main.animal.animal_id > 0 ? 'yes' : 'no'; },
                'fn':'M.ciniki_petadoptions_main.animal.remove();'},
            }},
        };
    this.animal.liveSearchCb = function(s, i, v) {
        M.api.getJSONBgCb('ciniki.petadoptions.animalFieldSearch', {'business_id':M.curBusinessID, 'start_needle':v, 'limit':'25', 'field':i}, function(rsp) {
            M.ciniki_petadoptions_main.animal.liveSearchShow(s,i,M.gE(M.ciniki_petadoptions_main.animal.panelUID + '_' + s), rsp.results);
            });
    }
    this.animal.liveSearchResultValue = function(s, f, i, j, d) {
        return d.name;
    }
    this.animal.liveSearchResultRowFn = function(s, f, i, j, d) {
        return 'M.ciniki_petadoptions_main.animal.setField(\'' + s + '\',\'' + f + '\',\'' + M.eU(d.name) + '\');';
    }
    this.animal.setField = function(s, f, v) { 
        M.gE(this.panelUID + '_' + f).value = M.dU(v);
        this.removeLiveSearch(s, f);
    }
    this.animal.fieldValue = function(s, i, d) { return this.data[i]; }
    this.animal.fieldHistoryArgs = function(s, i) {
        return {'method':'ciniki.petadoptions.animalHistory', 'args':{'business_id':M.curBusinessID, 'animal_id':this.animal_id, 'field':i}};
    }
    this.animal.addDropImage = function(iid) {
        if( this.animal_id == 0 ) {
            var c = this.serializeForm('yes');
            M.api.postJSONCb('ciniki.petadoptions.animalAdd', {'business_id':M.curBusinessID, 'animal_id':this.animal_id, 'image_id':iid}, c, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    } 
                    M.ciniki_petadoptions_main.animal.animal_id = rsp.id;
                    M.ciniki_petadoptions_main.animal.refreshImages();
                });
        } else {
            M.api.getJSONCb('ciniki.petadoptions.imageAdd', {'business_id':M.curBusinessID, 'image_id':iid, 'name':'', 'animal_id':this.animal_id, 'flags':1}, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                M.ciniki_petadoptions_main.animal.refreshImages();
            });
        }
        return true;
    };
    this.animal.thumbFn = function(s, i, d) {
        return 'M.ciniki_petadoptions_main.image.open(\'M.ciniki_petadoptions_main.animal.refreshImages();\',\'' + d.id + '\');';
    };
    this.animal.refreshImages = function() {
        if( M.ciniki_petadoptions_main.animal.animal_id > 0 ) {
            M.api.getJSONCb('ciniki.petadoptions.animalGet', {'business_id':M.curBusinessID, 'animal_id':this.animal_id, 'images':'yes'}, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                var p = M.ciniki_petadoptions_main.animal;
                p.data.images = rsp.animal.images;
                p.refreshSection('images');
                p.show();
            });
        }
    }
    this.animal.open = function(cb, aid, list) {
        if( aid != null ) { this.animal_id = aid; }
        if( list != null ) { this.nplist = list; }
        M.api.getJSONCb('ciniki.petadoptions.animalGet', {'business_id':M.curBusinessID, 'animal_id':this.animal_id, 'images':'yes'}, function(rsp) {
            if( rsp.stat != 'ok' ) {
                M.api.err(rsp);
                return false;
            }
            var p = M.ciniki_petadoptions_main.animal;
            p.data = rsp.animal;
            p.refresh();
            p.show(cb);
        });
    }
    this.animal.save = function(cb) {
        if( cb == null ) { cb = 'M.ciniki_petadoptions_main.animal.close();'; }
        if( !this.checkForm() ) { return false; }
        if( this.animal_id > 0 ) {
            var c = this.serializeForm('no');
            if( c != '' ) {
                M.api.postJSONCb('ciniki.petadoptions.animalUpdate', {'business_id':M.curBusinessID, 'animal_id':this.animal_id}, c, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    }
                    eval(cb);
                });
            } else {
                eval(cb);
            }
        } else {
            var c = this.serializeForm('yes');
            M.api.postJSONCb('ciniki.petadoptions.animalAdd', {'business_id':M.curBusinessID}, c, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                M.ciniki_petadoptions_main.animal.animal_id = rsp.id;
                eval(cb);
            });
        }
    }
    this.animal.remove = function() {
        if( confirm('Are you sure you want to remove animal?') ) {
            M.api.getJSONCb('ciniki.petadoptions.animalDelete', {'business_id':M.curBusinessID, 'animal_id':this.animal_id}, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                M.ciniki_petadoptions_main.animal.close();
            });
        }
    }
    this.animal.nextButtonFn = function() {
        if( this.nplist != null && this.nplist.indexOf('' + this.animal_id) < (this.nplist.length - 1) ) {
            return 'M.ciniki_petadoptions_main.animal.save(\'M.ciniki_petadoptions_main.animal.open(null,' + this.nplist[this.nplist.indexOf('' + this.animal_id) + 1] + ');\');';
        }
        return null;
    }
    this.animal.prevButtonFn = function() {
        if( this.nplist != null && this.nplist.indexOf('' + this.animal_id) > 0 ) {
            return 'M.ciniki_petadoptions_main.animal.save(\'M.ciniki_petadoptions_main.animal_id.open(null,' + this.nplist[this.nplist.indexOf('' + this.animal_id) - 1] + ');\');';
        }
        return null;
    }
    this.animal.addButton('save', 'Save', 'M.ciniki_petadoptions_main.animal.save();');
    this.animal.addClose('Cancel');
    this.animal.addButton('next', 'Next');
    this.animal.addLeftButton('prev', 'Prev');

    //
    // The panel to display the edit image form
    //
    this.image = new M.panel('Edit Image', 'ciniki_petadoptions_main', 'image', 'mc', 'medium mediumaside', 'sectioned', 'ciniki.petadoptions.main.image');
    this.image.default_data = {};
    this.image.data = {};
    this.image.animal_id = 0;
    this.image.sections = {
        '_image':{'label':'Photo', 'type':'imageform', 'aside':'yes', 'fields':{
            'image_id':{'label':'', 'type':'image_id', 'hidelabel':'yes', 'controls':'all', 'history':'no'},
        }},
        'info':{'label':'Information', 'type':'simpleform', 'fields':{
            'title':{'label':'Title', 'type':'text'},
            'flags':{'label':'Website', 'type':'flags', 'join':'yes', 'flags':{'1':{'name':'Visible'}}},
        }},
        '_description':{'label':'Description', 'type':'simpleform', 'fields':{
            'description':{'label':'', 'type':'textarea', 'size':'small', 'hidelabel':'yes'},
        }},
        '_save':{'label':'', 'buttons':{
            'save':{'label':'Save', 'fn':'M.ciniki_petadoptions_main.image.save();'},
            'delete':{'label':'Delete', 'fn':'M.ciniki_petadoptions_main.image.remove();'},
        }},
    };
    this.image.fieldValue = function(s, i, d) { 
        if( this.data[i] != null ) {
            return this.data[i]; 
        } 
        return ''; 
    };
    this.image.fieldHistoryArgs = function(s, i) {
        return {'method':'ciniki.petadoptions.imageHistory', 'args':{'business_id':M.curBusinessID, 
            'animal_image_id':this.animal_image_id, 'field':i}};
    };
    this.image.addDropImage = function(iid) {
        M.ciniki_petadoptions_main.image.setFieldValue('image_id', iid, null, null);
        return true;
    };
    this.image.open = function(cb, iid, aid) {
        if( iid != null ) { this.animal_image_id = iid; }
        if( aid != null ) { this.animal_id = aid; }
        if( this.animal_image_id > 0 ) {
            M.api.getJSONCb('ciniki.petadoptions.imageGet', {'business_id':M.curBusinessID, 'animal_image_id':this.animal_image_id}, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                var p = M.ciniki_petadoptions_main.image;
                p.data = rsp.image;
                p.refresh();
                p.show(cb);
            });
        } else {
            this.reset();
            this.data = {'flags':1};
            this.refresh();
            this.show(cb);
        }
    };
    this.image.save = function() {
        if( this.animal_image_id > 0 ) {
            var c = this.serializeFormData('no');
            if( c != '' ) {
                M.api.postJSONFormData('ciniki.petadoptions.imageUpdate', {'business_id':M.curBusinessID, 'animal_image_id':this.animal_image_id}, c, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    } else {
                        M.ciniki_petadoptions_main.image.close();
                    }
                });
            } else {
                this.close();
            }
        } else {
            var c = this.serializeFormData('yes');
            var rsp = M.api.postJSONFormData('ciniki.petadoptions.imageAdd', {'business_id':M.curBusinessID, 'animal_id':this.animal_id}, c, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                } else {
                    M.ciniki_petadoptions_main.image.close();
                }
            });
        }
    };
    this.image.remove = function() {
        if( confirm('Are you sure you want to delete this image?') ) {
            M.api.getJSONCb('ciniki.petadoptions.imageDelete', {'business_id':M.curBusinessID, 'animal_image_id':this.animal_image_id}, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                M.ciniki_petadoptions_main.image.close();
            });
        }
    };
    this.image.addButton('save', 'Save', 'M.ciniki_petadoptions_main.image.save();');
    this.image.addClose('Cancel');

    //
    // Start the app
    // cb - The callback to run when the user leaves the main panel in the app.
    // ap - The application prefix.
    // ag - The app arguments.
    //
    this.start = function(cb, ap, ag) {
        args = {};
        if( ag != null ) {
            args = eval(ag);
        }
        
        //
        // Create the app container
        //
        var ac = M.createContainer(ap, 'ciniki_petadoptions_main', 'yes');
        if( ac == null ) {
            alert('App Error');
            return false;
        }
        
        this.menu.open(cb);
    }
}
