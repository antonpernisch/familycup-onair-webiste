var prevNextId = 0;
var recentInterval;
$(document).ready(function () {
    $.ajaxSetup({ cache: false });
    var prevstate = "";
    var prevData = undefined;
    var prevPage = undefined;

    setInterval(function () {
        $.ajax({
            type: 'GET',
            url: 'https://onair.family-cup.sk/api/get/state.php',
            dataType: 'json',
            success: function (data) {
                if (data.state != prevstate) {
                    $("#live_state_container").load(`${PATH_TO_HOME}blocks/state/` + data.state + ".html");
                    data.state == "inprogress" ? Visual.ChangeContent("next-ride-label", "Práve štartuje") : Visual.ChangeContent("next-ride-label", "Následujúca jazda");
                    data.state == "noprogress" || data.state == "maintenance" ? Visual.HideID("next-ride-container") : Visual.ShowID("next-ride-container");
                    prevstate = data.state;
                }
                if (data.next != prevNextId) {
                    if (data.next == "" || data.state == "noprogress") {
                        Visual.HideID("next-ride-container");
                    } else {
                        Visual.ShowID("next-ride-container");
                        DataManager.GetData(data.next, "rozpis", (result) => {
                            CardWorker.Generate(result.type, result.categ, result.startType, result.startNo, result.startTime, result.recordId, result.assocID, (out) => {
                                $('#next-ride-content').html(out);
                            }, preloading = () => {
                                $('#next-ride-content').html("<div class='text-center my-5' id='to-be-removed-next'><div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Načítavam...</span></div></div>");
                            }, tableSel = "rozpis", `${PATH_TO_HOME}blocks/cards/`);
                        }, preloading = () => {
                            $('#next-ride-content').html("<div class='text-center my-5' id='to-be-removed-next'><div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Načítavam...</span></div></div>");
                        });
                    }
                    prevNextId = data.next;
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                const staticInstance = mdb.Toast.getInstance(document.getElementById('toast-unableToConnect'));
                staticInstance.show();
            }
        });
    }, 5000);

    recentInterval = setInterval(function () {
        $.ajax({
            type: 'GET',
            url: `https://onair.family-cup.sk/api/get/recent.php?page=${Pagination.current}`,
            dataType: 'json',
            success: function (data) {
                if (typeof prevData == "undefined") { prevData = data; prevPage = Pagination.current; return; }
                if (prevData.length == 0 && data.length > 0) {
                    $(".info-alert-box").hide();
                    $("#main-content-label").show();
                }
                if (!data.equals(prevData) && prevPage == Pagination.current && Pagination.current != 1) {
                    Pagination.ReloadBtns(removeLastCard = true);
                    const staticInstance = mdb.Toast.getInstance(document.getElementById('toast-newDataAvailable'));
                    staticInstance.show();
                } else if (!data.equals(prevData) && prevPage == Pagination.current && Pagination.current == 1) {
                    DataManager.GetData(data[data.length - 1], "recent", (result) => {
                        Pagination.ReloadBtns();
                        $('#to-be-removed').first().remove();
                        CardWorker.Generate(result.type, result.categ, result.startType, result.startNo, result.startTime, result.recordId, result.assocID, (out) => {
                            $('#to-be-removed').first().remove();
                            $('#main-content').prepend(out);
                        }, preloading = () => {
                            $('#main-content').prepend("<div class='text-center my-5' id='to-be-removed'><div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Načítavam...</span></div></div>");
                        }, undefined, `${PATH_TO_HOME}blocks/cards/`);
                    }, preloading = () => {
                        $('#main-content').prepend("<div class='text-center my-5' id='to-be-removed'><div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Načítavam...</span></div></div>");
                    });
                } else {
                    prevPage = Pagination.current;
                }
                prevData = data;
            },
            error: function (xhr, ajaxOptions, thrownError) {
                const staticInstance = mdb.Toast.getInstance(document.getElementById('toast-unableToConnect'));
                staticInstance.show();
            }
        });
    }, 2000);
});

function LoadNextRide(state) {
    if (state == "noprogress" || state == "maintenance") {
        Visual.HideID("next-ride-container");
        return;
    };
    $.ajax({
        type: 'GET',
        url: 'https://onair.family-cup.sk/api/get/state.php',
        dataType: 'json',
        success: function (data) {
            if (typeof prevNextId == "undefined") prevNextId = data.next;
            if (data.next == "") {
                Visual.HideID("next-ride-container");
            } else {
                Visual.ShowID("next-ride-container");
                DataManager.GetData(data.next, "rozpis", (result) => {
                    CardWorker.Generate(result.type, result.categ, result.startType, result.startNo, result.startTime, result.recordId, result.assocID, (out) => {
                        $('#next-ride-content').html(out);
                    }, preloading = () => {
                        $('#next-ride-content').html("<div class='text-center my-5' id='to-be-removed-next'><div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Načítavam...</span></div></div>");
                    }, tableSel = "rozpis", `${PATH_TO_HOME}blocks/cards/`);
                }, preloading = () => {
                    $('#next-ride-content').html("<div class='text-center my-5' id='to-be-removed-next'><div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Načítavam...</span></div></div>");
                });
            }
            prevNextId = data.next;
        },
        error: function (xhr, ajaxOptions, thrownError) {
            const staticInstance = mdb.Toast.getInstance(document.getElementById('toast-unableToConnect'));
            staticInstance.show();
        }
    });
}