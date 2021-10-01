var prevData;

function DataSubscriber() {
    return;
}

DataSubscriber.prototype = {
    Request: function() {
        $.ajax({
            type: 'GET',
            url: 'https://onair.family-cup.sk/api/get/schedule_hash.php?include_info&include_state',
            dataType: 'json',
            success: function (data) {
                let hash = data.hash;
                if(hash !== prevData && prevData !== undefined) {
                    DataLoader.Load();
                }
                prevData = hash;
            },
            error: function (xhr, ajaxOptions, thrownError) {
                const staticInstance = mdb.Toast.getInstance(document.getElementById('toast-unableToConnect'));
                staticInstance.show();
            }
        });
    }
};

var DataSubscriber = new DataSubscriber();

$(document).ready(function() {
    setInterval(() => DataSubscriber.Request(), 2000);
});