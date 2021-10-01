function CardWorker() {
    return;
}

CardWorker.prototype = {
    Generate: function(type, categ, startType, startNo, startTime, recordId, assocId, callback, preloading=(x)=>{}, tableSel="recent", templatePath="../blocks/cards/") {
        preloading();
        $.ajax({
            url: templatePath + type + ".html",
            success: (result) => {
                callback(result.replaceAll("$$kategoria$$", categ).replaceAll("$$typStartu$$", startType).replaceAll("$$startN$$", startNo).replaceAll("$$cas$$", startTime).replaceAll("$$recordID$$", recordId).replaceAll("$$tableType$$", type).replaceAll("$$assocID$$", assocId).replaceAll("$$tableSel$$", tableSel));
            }
        });
    }
};