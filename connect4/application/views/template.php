<!doctype html>
<html>
<head>
  <meta charset='utf-8'/>
  <title><?php echo $title; ?></title>
  <link rel="stylesheet" href="/connect4/css/style.css" type="text/css">
  <link rel='stylesheet' href='/connect4/css/bootstrap.min.css' type='text/css'>
  <script src='/connect4/js/jquery-2.1.1.min.js'></script>
  <script src='/connect4/js/bootstrap.min.js'></script>
</head>
<body>
  <div class='container'>
    <?php $this->load->view('header'); ?>
  </div>
  <?php $this->load->view($main); ?>
  <div class='container'>
    <?php $this->load->view('footer'); ?>
  </div>
</body>
</html>
