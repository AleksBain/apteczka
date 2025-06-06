<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Apteczka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/apteczka/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
  <a class="navbar-brand" href="/apteczka/index.php">Apteczka</a>


  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent"
    aria-controls="navContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navContent">
    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
      <li class="nav-item"><a class="nav-link" href="/apteczka/apteczka.php">Moja apteczka</a></li>
      <li class="nav-item"><a class="nav-link" href="/apteczka/rodzina.php">Rodzina</a></li>
      <li class="nav-item"><a class="nav-link" href="/apteczka/historia_lekow.php">Historia</a></li>
      <li class="nav-item"><a class="nav-link" href="/apteczka/transakcje.php">Raporty</a></li>

    </ul>

    <div class="d-flex align-items-center">
      <span class="datetime me-3"><?php echo date("Y-m-d H:i:s"); ?></span>
      <a class="btn btn-outline-light" href="/apteczka/scripts/wylogowanie.php">🚪 Wyloguj</a>
    </div>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
