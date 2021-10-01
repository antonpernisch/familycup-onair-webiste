function Visual() {
    return;
}

Visual.prototype = {
    ChangeContent: function( id, newcontent ) {
        id = "#" + id;
        if( $(id).length ) {
            $(id).html( newcontent );
            return true;
        } else {
            console.warn(`VISUAL: Provided ID (${id}) wasn't found in the document`);
            return false;
        }
    },

    HideID: function( id ) {
        id = "#" + id;
        if( $(id).length ) {
            $(id).hide();
            return true;
        } else {
            console.warn(`VISUAL: Provided ID (${id}) wasn't found in the document`);
            return false;
        }
    },

    ShowID: function( id ) {
        id = "#" + id;
        if( $(id).length ) {
            $(id).show();
            return true;
        } else {
            console.warn(`VISUAL: Provided ID (${id}) wasn't found in the document`);
            return false;
        }
    },
};

var Visual = new Visual();