<?php
$PATH_TO_HOME = "../";
require_once "{$PATH_TO_HOME}php/load_recent.php";
require_once "{$PATH_TO_HOME}php/current_state.php";
require_once "{$PATH_TO_HOME}php/visual_helper.php";

$recentLoader = new RecentLoader($PATH_TO_HOME);
$currentState = new CurrentState($PATH_TO_HOME);
$visual = new VisualHelper("{$PATH_TO_HOME}blocks");
$db = new DB(DB_SERVER, DB_NAME, DB_USER, DB_PASS);
?>
<!doctype html>
<html>

<head>
  <title>FamilyCup OnAir Timing</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="publisher" content="esec">
  <meta name="copyright" content="esec development services, esec, SKKAJAK">
  <meta name="application-name" content="FamilyCup OnAir systém" />
  <meta name="description" content="OnAir systém na živé sledovanie výsledkov jázd." />
  <meta name="keywords" content="skkajak, familycup, skkajak familycup, skkajak 2021, 2021, familycup 2021, skkajak familycup 2021, sk kajak familycup, sk kajak familycup 2021, familycup onair, onair familycup" />
  <meta name="identifier-url" content="https://onair.family-cup.sk/" />
  <meta name="googlebot" content="index" />
  <meta name="dcterms.dateCopyrighted" content="2021" />
  <link rel="icon" type="image/png" href="../inc/fav_onair.png">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
  <link href="https://storage.esec.sk/lib/mdb5/css/mdb.min.css" rel="stylesheet">
  <script type="text/javascript" src="https://storage.esec.sk/lib/mdb-updated/js/jquery.min.js"></script>
  <script type="text/javascript" src="../js/visual-helper.js"></script>
  <style>
    th {
      font-weight: 600 !important;
      border-top-color: transparent !important;
    }

    body {
      overflow-x: hidden !important;
    }
  </style>
</head>

<body>

  <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand ms-5" href="https://onair.family-cup.sk/"><i class="fas fa-broadcast-tower me-2"></i>FamilyCup OnAir</a>
    <button class="navbar-toggler" type="button" data-mdb-toggle="collapse" data-mdb-target="#navbarSupportedContent3" aria-controls="navbarSupportedContent3" aria-expanded="false" aria-label="Zobraziť menu">
      <span class="navbar-toggler-icon"><i class="fas fa-bars"></i></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent3">
      <ul class="navbar-nav me-auto">
        <li class="nav-item ms-5 ms-lg-0" style="cursor: pointer;">
          <a class="nav-link" href="../">Harmonogram <i class="far fa-clock ms-1"></i>
            <span class="sr-only">Harmonogram</span>
          </a>
        </li>
        <li class="nav-item ms-5 ms-lg-0" style="cursor: default;">
          <a class="nav-link active">Najnovšie <i class="far fa-newspaper ms-1"></i>
            <span class="sr-only">Najnovšie</span>
          </a>
        </li>
        <!-- <li class="nav-item ms-5 ms-lg-0">
        <a class="nav-link">Výsledky <i class="fas fa-trophy ms-1"></i>
          <span class="sr-only">Výsledky</span>
        </a>
      </li> -->
        <li class="nav-item ms-5 mt-1 my-3 my-lg-0">
          <a class="btn btn-outline-light text-light" data-mdb-ripple-color="dark" href="https://family-cup.sk">Naspäť
            na FamilyCup stránku <i class="fas fa-chevron-circle-left"></i></a>
        </li>

      </ul>
    </div>
  </nav>

  <div class="shadow-1" style="padding-top: 60px;">
    <div class="p-3 p-sm-5 bg-light">
      <div class="container text-center" id="live_state_container">
        <? echo $currentState->GetDisplay(); ?>
      </div>
    </div>

    <div class="container-fluid pt-3 pt-sm-5 pb-2 bg-light" id="next-ride-container">
      <div class="container">
        <div class="row"><span class="h2"><span id="next-ride-label">Následujúca jazda</span><span class="badge rounded-pill bg-primary ms-2"><i class="fas fa-chevron-circle-right" aria-hidden="true"></i></span></span></div>
        <div class="row mt-4" id="next-ride-content"></div>
      </div>
    </div>
  </div>

  <div class="container mt-5 mb-4" id="main-content-label">
    <div class="row">
      <div class="row"><span class="h2">Posledne zverejnené<span class="badge rounded-pill bg-dark text-light ms-2"><i class="fas fa-ellipsis-h" aria-hidden="true"></i></span></span></div>
    </div>
  </div>
  <div class="container" id="main-content">
    <? if ($recentLoader->LoadJS(1) === null) {
      echo $visual->Info("Žiadne dáta", "Rozhodci zatiaľ nepublikovali žiadne dáta.");
      $visual->HideID("next-ride-container");
      $visual->HideID("main-content-label");
    }
    if ($db->GetOption("live_nextID") == "") $visual->HideId("next-ride-container");
    if ($db->GetOption("live_state") == "inprogress") $visual->ChangeContent("next-ride-label", "Práve štartuje");
    if ($db->GetOption("live_state") == "noprogress") $visual->HideId("next-ride-container");
    ?>
  </div>
  <div class="container mt-3 mb-5" id="pagi-main-cotainer">
    <div class="row">
      <nav aria-label="...">
        <ul class="pagination pagination-circle justify-content-center" id="pagi-btns">
          <li class="page-item" id="pagi-prev-btn">
            <a class="page-link" href="javascript:Pagination.PrevPage()" tabindex="-1" aria-disabled="true">Predchádzajúca</a>
          </li>
          <!-- <li class="page-item active" aria-current="page"><a class="page-link" href="#">1</a></li>
        <li class="page-item pagi-btn"><a class="page-link" href="#">2</a></li>
        <li class="page-item pagi-btn"><a class="page-link" href="#">3</a></li> -->
          <li class="page-item" id="pagi-next-btn">
            <a class="page-link" href="javascript:Pagination.NextPage()">Ďalšia</a>
          </li>
        </ul>
      </nav>
    </div>
  </div>
  <div class="cotainer">
    <div class="row my-3" id="pagi-loader-wrapper"></div>
  </div>

  <div class="container my-5">
    <div class="row mx-2">
      <p class="note note-primary">
        <strong>Poznámka:</strong> Prehľadnejšie zobrazenie podľa času nájdete na <a href="https://onair.family-cup.sk/">OnAir harmonograme</a>.
      </p>
    </div>
  </div>

  <div id="container-modals">
    <? echo $visual->GetBlock("modals/vysledky"); ?>
    <? echo $visual->GetBlock("modals/rozpis"); ?>
  </div>

  <div id="toasters-container">
    <?php echo $visual->GetBlock("toasts/unableToConn"); ?>
    <?php echo $visual->GetBlock("toasts/newDataAvailable"); ?>
  </div>

  <footer class="bg-light text-center text-lg-start">
    <div class="container p-4">
      <div class="row">
        <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
          <h5 class="text-uppercase">Vývoj OnAir</h5>
          <p>
            Systém OnAir je systém špeciálne vyvinutý pre použitie na FamilyCup.
            Celý systém stojí na viac ako 4000 riadkoch kódu napísaného v 5 rôznych jazykoch.
            Vývoj trval približne mesiac. Toto všetko sme spravili, aby ste mali maximálnu
            pohodu a mali prístup k informáciam, či už keď práve pijete pri bare, alebo sa opalujete.
          </p>
        </div>
        <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
          <h5 class="text-uppercase">Kontakt</h5>
          <ul class="list-unstyled">
            <li>Všeobecný: <span class="text-muted">kancelaria@family-cup.sk</span></li>
            <li>Richard Jahoda (riaditeľ): <span class="text-muted">riaditel@family-cup.sk</span></li>
            <li>Emma Bednárová (hl. rozhodca): <span class="text-muted">hl.rozhodca@family-cup.sk</span></li>
            <li>Anton Pernisch (vývoj): <span class="text-muted">vyvoj@family-cup.sk</span></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
      <i class="far fa-copyright"></i> 2021 Copyright:
      <a class="text-dark" href="https://pernisch.dev/" id="myname">Anton Pernisch</a>
    </div>
  </footer>

  <script>
    <?php echo "const PATH_TO_HOME = \"{$PATH_TO_HOME}\";"; ?>
  </script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
  <script type="text/javascript" src="https://storage.esec.sk/lib/mdb5/js/mdb.min.js"></script>
  <script type="text/javascript" src="../js/prototype-additions.js"></script>
  <script type="text/javascript" src="../js/card-worker.js"></script>
  <script type="text/javascript" src="../js/modal-manager.js"></script>
  <script type="text/javascript" src="../js/data-manager.js"></script>
  <script type="text/javascript" src="../js/lib/time.js"></script>
  <script type="text/javascript" src="../js/time-utilities.js"></script>
  <script type="text/javascript" src="../js/pagination.js"></script>
  <script>
    var CardWorker = new CardWorker();
    var ModalManager = new ModalManager();
    var DataManager = new DataManager();
    var TimeUtilities = new TimeUtilities();
    var Pagination = new Pagination();
    Pagination.ReloadBtns();
    <? echo $recentLoader->LoadJS(1); ?>
  </script>
  <script type="text/javascript" src="../js/recent_subscriber.js"></script>
  <script>
    LoadNextRide("<? echo $db->GetOption("live_state"); ?>");
  </script>
</body>

</html>