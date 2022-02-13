<?php include_once('include/config.php'); ?>

<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Stuur een bericht naar de BarryBox.">
	<meta name="author" content="Jos Zuijderwijk">
	<link rel="icon" type="image/x-icon" href="favicon.svg">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

	<title>BarryBox</title>

	<!-- Bootstrap core CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

	<!-- Form validation CSS -->
	<link href="css/form-validation.css" rel="stylesheet">

	<!-- Custom styles for this template -->
	<link href="css/sticky-footer.css" rel="stylesheet">

	<!-- jQuery and Popper -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

	<!-- Set the default user before including custom scripts -->
	<script type="text/javascript">
		var default_user = "<?php echo DEFAULT_USER; ?>";
	</script>

	<!-- Custom scripts -->
	<script src="js/get_status.js"></script>
	<script src="js/form.js"></script>
</head>

<body>
	<div class="loader">
		<div class="spinner">
			<div class="spinner-border text-primary" role="status">
				<span class="sr-only center-screen">Loading...</span>
			</div>
		</div>
	</div>

	<!-- Begin page content -->
	<main role="main" class="container">
		<div style="margin-top: 3rem!important;margin-bottom:0.5rem;">

			<div class="alert alert-danger d-flex align-items-center" role="alert" id="alert" style="display:none !important;">
				<i class="fa fa-exclamation-circle fa-2x" aria-hidden="true"></i>
				<span id="alert-text">
					De gevraagde gebruiker bestaat niet.
				</span>
			</div>

			<h1 class="mt-5" style="display: inline"> BarryBox </span></h1> <span id="aliasBadge" class="badge badge-secondary">@Berenhuis</span> <span id="statusBadge" class="badge bg-success">Online</span>
		</div>
		<p class="lead">De <a href="#">BarryBox</a> is een apparaat dat alles opleest wat je 'm stuurt.</p>
		<p>Stuur iets (max. 130 tekens) zodat het hardop wordt voorgelezen! Je kunt ook de ingebouwde <a href="#soundboard" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">soundboard</a> gebruiken. Je kunt maximaal één bericht of één geluidseffect per drie seconden sturen.</p><br>

		<div class="small-container">
			<h4>Text-to-Speech</h4>
			<form method="post" action="include/process.php">

				<div class="form-row row g-2" id="firstrow">
					<div class="form-group col-md-10 mb-3" id="message-group">
						<input class="form-control form-control-lg" id="message" name="message" type="text" maxlength="130" placeholder="Iets liefs...">
					</div>

					<div class="form-group col-md-2 mb-3" id="lang-group">
						<select id="language" name="language" class="form-control" style="height:48px;">
							<optgroup label="Populair">
								<option>nl</option>
								<option>en</option>
								<option>de</option>
								<option>fr</option>							
								<option>it</option>
							</optgroup>
							<optgroup label="Alles">
								<option>af</option>
								<option>sq</option>
								<option>ar</option>
								<option>hy</option>
								<option>az</option>
								<option>eu</option>
								<option>bg</option>
								<option>ca</option>
								<option>hr</option>
								<option>cs</option>
								<option>da</option>
								<option>nl</option>
								<option>en</option>
								<option>et</option>
								<option>tl</option>
								<option>fi</option>
								<option>fr</option>
								<option>gl</option>
								<option>ka</option>
								<option>de</option>
								<option>el</option>
								<option>ht</option>
								<option>iw</option>
								<option>hi</option>
								<option>hu</option>
								<option>is</option>
								<option>id</option>
								<option>ga</option>
								<option>it</option>
								<option>ja</option>
								<option>ko</option>
								<option>lv</option>
								<option>lt</option>
								<option>mk</option>
								<option>ms</option>
								<option>mt</option>
								<option>no</option>
								<option>fa</option>
								<option>pl</option>
								<option>pt</option>
								<option>ro</option>
								<option>ru</option>
								<option>sr</option>
								<option>sk</option>
								<option>sl</option>
								<option>es</option>
								<option>sw</option>
								<option>sv</option>
								<option>th</option>
								<option>tr</option>
								<option>uk</option>
								<option>ur</option>
								<option>vi</option>
								<option>cy</option>
								<option>yi</option>
							</optgroup>
						</select>
					</div>
				</div>

				<div class="form-group row">
					<div class="col-auto" style="width: 100%; text-align:right;">
						<button type="submit" id="submit" name="submit" class="btn btn-primary" style="width:150px;">Verstuur</button>
					</div>
				</div>
			</form>
		</div>

		<div class="small-container">
			<h4>Geluidseffecten</h4>
			Hieronder staan alle ingebouwde geluidseffecten.

			<div class="accordion accordion" id="soundboard">
				<div class="accordion-item">
					<h2 class="accordion-header" id="headingOne">
						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
							Soundboard
						</button>
					</h2>
					<div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#soundboard">
						<div class="accordion-body">
							<div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-mdb-parent="#soundboard">
								<div class="soundcontainer">
									<?php
									$sPath = 'soundboard/*.mp3';
									foreach (glob($sPath) as $mp3) :
										$name = basename($mp3, ".mp3"); ?>
										<div class="sound">
											<h6><?= $name ?></h6>
											<audio controls>
												<source src="<?= $mp3 ?>" type="audio/mpeg">
											</audio>
											<br>
											<button class="btn sound-btn" value='<?= $name ?>'><i class="fa fa-play"></i> Verstuur</button>
										</div>
										<br>
									<?php endforeach ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</main>

	<footer class="footer">
		<div class="container">
			<span class="text-muted">Een project van <a href="https://joszuijderwijk.nl">Jos Zuijderwijk</a></span>
			<span class="pull-right"><a href="https://github.com/iovidius/BarryBox"><i class="fa fa-github fa-2x" aria-hidden="true"></i></a></span>
		</div>
	</footer>
</body>

</html>