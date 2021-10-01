function ModalManager() {
    return;
}

ModalManager.prototype = {
    LoadJSON: function (recordID, recordType, tableSel, assocID, callback, preloading = (x) => { }, onerror = () => { }) {
        preloading();
        $.ajax({
            type: 'GET',
            url: 'https://onair.family-cup.sk/api/get/data.php',
            data: { id: recordID, table: tableSel, assocID: assocID, noEmpty: "true" },
            dataType: 'json',
            success: function (data) {
                if (data.type == recordType) {
                    callback(data);
                } else {
                    onerror();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                const staticInstance = mdb.Toast.getInstance(document.getElementById('toast-unableToConnect'));
                staticInstance.show();
            }
        });
    },

    ApplyChanges: function (data, modalId = "TYPE-modal") {
        modalId = modalId.replace("TYPE", data.type);
        $("#" + modalId + "-categ").html(data.categ);
        $("#" + modalId + "-startType").html(data.startType);
        $("#" + modalId + "-startTime").html(data.startTime);
        $("#" + modalId + "-startNo").html(data.startNo);
        $("#" + modalId + "-podtab").html("");
        $("#" + modalId + "-tabulka").html("");
        $("#" + modalId + "-nadtab").html("");
        $("#" + modalId + "-badges").html(`<span class='h5'><span class='badge rounded-pill bg-light text-dark'>Publikované ${data.published}</span></span>`);
        if (data.scored == "true") $("#" + modalId + "-badges").append(`<span class="h5"><span class="badge rounded-pill bg-success"><i class="far fa-star me-2"></i>Bodované</span></span>`);
        var counter = 0;
        var firstTime = "";
        var difference = "";
        var prevTime = "";
        const special_times = ["DSQ", "DNS", "DNF"];
        for (let thisData of data["0"]) {
            counter += 1;
            if (data.type == "vysledky") {
                let timeDifferenceToFirst;
                let timeDifference;
                if (counter == 1) {
                    firstTime = thisData.time;
                    timeDifferenceToFirst = `<span class="text-muted">0:0.00</span>`;
                    timeDifference = `<span class="text-muted">0:0.00</span>`;
                } else {
                    differenceToFirst = TimeUtilities.ConvertToReadable(TimeUtilities.GetDifference(firstTime, thisData.time));
                    timeDifferenceToFirst = special_times.includes(thisData.time) ? `<span class="text-muted"></span>` : `<span class="text-danger"><span class="me-1"><b>+</b></span>${differenceToFirst}</span>`;
                    difference = TimeUtilities.ConvertToReadable(TimeUtilities.GetDifference(prevTime, thisData.time));
                    timeDifference = special_times.includes(thisData.time) ? `<span class="text-muted"></span>` : `<span class="text-danger"><span class="me-1"><b>+</b></span>${difference}</span>`;
                }
                prevTime = thisData.time;
                let posadka = thisData.posadka.replace(" & ", "<br />");
                let dataToApped = `
                    <tr>
                        <th scope="row">${counter}.</th>
                        <td>${thisData.num}</td>
                        <td>${posadka}</td>
                        <td>${special_times.includes(thisData.time) ? `<span class="fw-bold">${thisData.time}</span>` : thisData.time}</td>
                        <td>${timeDifference}</td>
                        <td>${timeDifferenceToFirst}</td>
                    </tr>
                `;
                $("#" + modalId + "-tabulka").append(dataToApped);
            } else if (data.type == "rozpis") {
                let posadka = thisData.posadka.replace(" & ", "<br />");
                let dataToApped = `
                    <tr>
                        <th scope="row">${thisData.num}</th>
                        <td>${posadka}</td>
                    </tr>
                `;
                $("#" + modalId + "-tabulka").append(dataToApped);
            }
        }
    },

    Open: function (recordID, recordTable, loadTable, assocID = "null", modalId = "TYPE-modal", canceled = false, started = false) {
        modalId = modalId.replace("TYPE", recordTable);
        ModalManager.LoadJSON(recordID, recordTable, loadTable, assocID, (data) => {
            data.startNo == "" || data["0"] == "" ? this.ShowEmpty(data, modalId) : ModalManager.ApplyChanges(data);
            if (canceled) this.ShowCanceled(modalId);
            if (started !== false) this.ShowStarted(modalId, started);
        }, preloading = () => {
            $("#" + modalId + "-categ").html("<span class='spinner-border spinner-border-sm text-primary' role='status'><span class='visually-hidden'>Načítavam...</span></span>");
            $("#" + modalId + "-startType").html("");
            $("#" + modalId + "-startTime").html("<span class='spinner-border spinner-border-sm text-light' role='status'><span class='visually-hidden'>Načítavam...</span></span>");
            $("#" + modalId + "-startNo").html("<span class='spinner-border spinner-border-sm text-primary' role='status'><span class='visually-hidden'>Načítavam...</span></span>");
            $("#" + modalId + "-tabulka").html("");
            $("#" + modalId + "-podtab").html("<span class='spinner-border text-primary text-center' role='status'><span class='visually-hidden'>Načítavam...</span></span>");
            $("#" + modalId + "-nadtab").html("");
            $("#" + modalId + "-badges").html("<span class='spinner-border spinner-border-sm text-primary text-center' role='status'><span class='visually-hidden'>Načítavam...</span></span>");
            const modal = new mdb.Modal(document.getElementById(modalId));
            modal.show()
        }, onerror = () => {
            this.ShowError(modalId);
        });
    },

    ShowError: function (modalId) {
        $("#" + modalId + "-categ").html(`<span class='text-danger'><i class="far fa-times-circle"></i></span>`);
        $("#" + modalId + "-startType").html(``);
        $("#" + modalId + "-startTime").html(`<span class='text-danger'><i class="far fa-times-circle"></i></span>`);
        $("#" + modalId + "-startNo").html(`<span class='text-danger'><i class="far fa-times-circle"></i></span>`);
        $("#" + modalId + "-podtab").html(`<span class='text-danger text-center'>Požadované dáta sa nenašli. Toto by ste nemali vidieť, kontaktujte oranizátorov.</span>`);
        $("#" + modalId + "-nadtab").html("");
        $("#" + modalId + "-tabulka").html(``);
        $("#" + modalId + "-badges").html(`<span class='text-danger'><i class="far fa-times-circle"></i></span>`);
    },

    ShowEmpty: function (data, modalId) {
        $("#" + modalId + "-categ").html(data.categ);
        $("#" + modalId + "-startType").html(data.startType);
        $("#" + modalId + "-startTime").html(data.startTime);
        $("#" + modalId + "-startNo").html(data.startNo);
        $("#" + modalId + "-tabulka").html("");
        $("#" + modalId + "-podtab").html(`<div class="text-center h4"><span class="badge rounded-pill bg-info"><i class="fas fa-info-circle me-2"></i>Listina prázdna</span></div>`);
        $("#" + modalId + "-badges").html(`<span class='h5'><span class='badge rounded-pill bg-light text-dark'>Publikované ${data.published}</span></span>`);
        if (data.scored == "true") $("#" + modalId + "-badges").append(`<span class="h5"><span class="badge rounded-pill bg-success"><i class="far fa-star me-2"></i>Bodované</span></span>`);
    },

    ShowCanceled: function (modalId) {
        $("#" + modalId + "-nadtab").html(`<div class="text-center h5"><span class="badge rounded-pill bg-danger"><i class="fas fa-info-circle me-2"></i>Jazda zrušená</span></div>`);
    },

    ShowStarted: function (modalId, startedTime) {
        $("#" + modalId + "-nadtab").html(`<div class="text-center h5"><span class="badge rounded-pill bg-success"><i class="fas fa-info-circle me-2"></i>Jazda odštartovaná ${startedTime}</span></div>`);
    }
};