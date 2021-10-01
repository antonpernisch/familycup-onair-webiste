function DataManager() {
    return;
}

DataManager.prototype = {
    GetData: function (assocId, tableSel, callback, preloading = (x) => { }) {
        preloading();
        $.ajax({
            type: 'GET',
            url: 'https://onair.family-cup.sk/api/get/data.php',
            dataType: 'json',
            data: { assocID: assocId, table: tableSel, noEmpty: "true" },
            success: (result) => {
                callback(result);
            }
        });
    },

    GetDataSpecial: function (recordId, callback, dataLen, preloading = (x) => { }) {
        preloading();
        $.ajax({
            type: 'GET',
            url: 'https://onair.family-cup.sk/api/get/data.php',
            dataType: 'json',
            data: { id: recordId, table: "recent", assocID: "null", noEmpty: "true" },
            success: (result) => {
                callback(result, dataLen);
            }
        });
    },

    GetAllRecent: function (callback, preloading = (x) => { }) {
        preloading();
        $.ajax({
            type: 'GET',
            url: 'https://onair.family-cup.sk/api/get/recent.php?page=all',
            dataType: 'json',
            success: (result) => {
                callback(result);
            }
        });
    },

    GetPageRecent: function (pageN, callback, preloading = (x) => { }) {
        preloading();
        $.ajax({
            type: 'GET',
            url: 'https://onair.family-cup.sk/api/get/recent.php',
            data: { page: pageN, want: "record" },
            dataType: 'json',
            success: (result) => {
                callback(result);
            }
        });
    }
};