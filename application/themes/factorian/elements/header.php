<!DOCTYPE html>
<html lang="fr">

<head>
  <?php Loader::element('header_required');?>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Factorian</title>

  <link rel="stylesheet" href="<?php echo $view->getThemePath(); ?>/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo $view->getThemePath(); ?>/css/style.css">
  <script src="<?php echo $view->getThemePath(); ?>/js/bootstrap.min.js"></script>
</head>

<body>
<div class="<?= $c->getPageWrapperClass()?>">
  <div class="container" id="menu">
    <div class="row">
      <nav class="navbar">
        <div class="container-fluid">
          <div class="navbar-header">
            <a class="navbar-brand" href="#" style="border: 4px solid; border-color: black; color: black; font-weight:bold">FACTORIAN</a>
          </div>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="#" style="color: black; text-transform: uppercase; font-weight:bold" > Home</a></li>
            <li><a href="#" style="color: black; text-transform: uppercase; font-weight:bold"> About Us</a></li>
            <li><a href="#" style="color: black; text-transform: uppercase; font-weight:bold"> Services</a></li>
            <li><a href="#" style="color: black; text-transform: uppercase; font-weight:bold"> Works</a></li>
            <li><a href="#" style="color: black; text-transform: uppercase; font-weight:bold"> Blog</a></li>
            <li><a href="#" style="color: black; text-transform: uppercase; font-weight:bold"> Pages</a></li>
            <li><a href="#" style="color: black; text-transform: uppercase; font-weight:bold"> Contact Us</a></li>
            <li><a href="#" style="color: black; text-transform: uppercase; font-weight:bold"><span class="glyphicon glyphicon-search"></span></a></li>
          </ul>
        </div>
      </nav>
    </div>
  </div>
