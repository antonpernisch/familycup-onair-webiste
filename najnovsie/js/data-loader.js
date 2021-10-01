function DataLoader() {
  return;
}

DataLoader.prototype = {
  Load: function () {
    $('#page-loader-space').html(`
      <div class='d-flex align-items-center justify-content-center my-5'><div class='spinner-border text-primary me-3' role='status'><span class='visually-hidden'>Načítavam...</span></div>Obnovujem a sťahujem dáta...</div>
    `);
    $.ajax({
      type: 'GET',
      url: 'https://onair.family-cup.sk/api/get/schedule_data.php?include_info&include_state',
      dataType: 'json',
      success: function (data) {
        live_state = data[0];
        data.splice(0, 1);
        // Zorad data podla casu (startTime v minutach od 00:00)
        data.sort((a, b) => (a.startTime > b.startTime) ? 1 : ((b.startTime > a.startTime) ? -1 : 0));
        if (data.length == 0) {
          $('#page-loader-space').html('');
          $('#main-content-label').hide();
          $('#main-content').html(`
            <div class="mx-3 mx-md-0 ps-5 p-4 border-start border-info rounded-start bg-light my-5 info-alert-box" style="border-width: 6px !important;">
              <h2 class="text-info"><strong>Harmonogram prázdny</strong></h2>
              <p>Organizátori zatiaľ nepublikovali žiaden časový harmonogram. Nebude to však trvať už dlho.</p>
            </div>
          `);
        } else {
          let innerHtml;
          for (const event of data) {
            const assocID = event.assocID;
            const categ = event.data[1];
            const startType = event.data[2];
            const startTime = TimeUtilities.ConvertToReadableHM(event.startTime);
            const canceled = event.canceled;
            const evaluated = event.evaluated;
            const started = event.started;
            if (assocID != 0) {
              if (canceled == 1) {
                innerHtml += `
                <tr class="table-danger">
                  <th scope="row">${categ}</th>
                  <td>${startType}</td>
                  <td class="fw-bold" style="font-weight: 600 !important;">${startTime}</td>
                  <td><button type="button" class="btn btn-outline-danger more-btn" data-mdb-ripple-color="danger" onclick="ModalManager.Open(1, 'rozpis', 'rozpis', ${assocID}, undefined, true);"><i class="fas fa-times"></i></button></td>
                </tr>
              `;
              } else if (evaluated) {
                innerHtml += `
                <tr class="table-success">
                  <th scope="row">${categ}</th>
                  <td>${startType}</td>
                  <td class="fw-bold" style="font-weight: 600 !important;">${startTime}</td>
                  <td><button type="button" class="btn btn-outline-success more-btn" data-mdb-ripple-color="success" onclick="ModalManager.Open(1, 'vysledky', 'vysledky', ${assocID});"><i class="fas fa-check"></i></button></td>
                </tr>
              `;
              } else if (started !== false && live_state == "inprogress") {
                innerHtml += `
                <tr>
                  <th scope="row">${categ}</th>
                  <td>${startType}</td>
                  <td class="fw-bold" style="font-weight: 600 !important;">${startTime}</td>
                  <td><button type="button" class="btn btn-outline-dark more-btn" data-mdb-ripple-color="dark" onclick="ModalManager.Open(1, 'rozpis', 'rozpis', ${assocID}, undefined, undefined, '${started}');"><span class="spinner-grow text-success spinner-grow-sm"></span></button></td>
                </tr>
              `;
              } else {
                innerHtml += `
                  <tr>
                    <th scope="row">${categ}</th>
                    <td>${startType}</td>
                    <td class="fw-bold" style="font-weight: 600 !important;">${startTime}</td>
                    <td><button type="button" class="btn btn-outline-dark more-btn" data-mdb-ripple-color="dark" onclick="ModalManager.Open(1, 'rozpis', 'rozpis', ${assocID});"><i class="fas fa-chevron-right"></i></button></td>
                  </tr>
                `;
              }
            } else {
              innerHtml += `
                <tr class="table-primary">
                  <th scope="row" style="font-weight: 600 !important;">${categ}</th>
                  <td></td>
                  <td class="fw-bold" style="font-weight: 600 !important;">${startTime}</td>
                  <td></td>
                </tr>
              `;
            }
          }
          $('#page-loader-space').html('');
          $('#main-content-innerTable').html(innerHtml);
        }
      },
      error: function (xhr, ajaxOptions, thrownError) {
        const staticInstance = mdb.Toast.getInstance(document.getElementById('toast-unableToConnect'));
        staticInstance.show();
      }
    });
  }
};