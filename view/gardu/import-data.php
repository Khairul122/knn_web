<?php include('view/template/header.php'); ?>

<body class="with-welcome-text">
  <div class="container-scroller">
    <?php include 'view/template/navbar.php'; ?>
    <div class="container-fluid page-body-wrapper">
      <?php include 'view/template/setting_panel.php'; ?>
      <?php include 'view/template/sidebar.php'; ?>
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-sm-12">

              <div class="card">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Import Data Gardu dari Excel</h4>
                    <a href="index.php?page=data-gardu" class="btn btn-secondary btn-sm">Kembali</a>
                  </div>

                  <form action="index.php?page=import-gardu" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                      <label>Upload File Excel (.xlsx)</label>
                      <input type="file" name="file_excel" class="form-control" accept=".xlsx" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Import</button>
                  </form>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include 'view/template/script.php'; ?>
</body>

</html>
