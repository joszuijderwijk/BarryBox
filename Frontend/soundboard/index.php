<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="De ingebouwde soundboard van de BarryBox.">
    <meta name="author" content="Jos Zuijderwijk">
	<link rel="icon" type="image/x-icon" href="../favicon.svg">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	
    <title>BarryBox Soundboard</title>


    <!-- Bootstrap core CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../css/sticky-footer.css" rel="stylesheet">
	<link href="../css/soundboard.css" rel="stylesheet">
	
  </head>

  <body>

    <!-- Begin page content -->
    <main role="main" class="container">
      <h1 class="mt-5">BarryBox Soundboard</h1>
	  <p class="lead">De <a href="#">BarryBox</a> kan (naast Text-to-Speech) ook ingebouwde geluidseffecten afspelen.</p>
      <p>Stuur daarvoor de naam van het geluidseffect, bijv. "wah", tussen vierkante haakjes: <code>[wah]</code>.</p><br>
	  
	  <h4>Beschikbare geluidseffecten</h4>
	  <div class="soundcontainer">
	  	<?php
			$sPath = 'soundboard/*.mp3';
			foreach (glob($sPath) as $mp3) {
				$name = basename($mp3, ".mp3");
				echo '<div class="sound">';
				echo '<h6>' . $name .'</h6>';
				echo '<audio controls>';
					echo '<source src="'.$mp3.'" type="audio/mpeg">';
				echo '</audio>';
				echo '<br>';
				echo '<button class="btn sound-btn" value='. $name . '><i class="fa fa-play"></i> Verstuur</button>';
				echo '</div>';
				echo '<br>';
			}
		?>
		</div>
    </main>

    <footer class="footer">
      <div class="container">
        <span class="text-muted">Een project van <a href="https://joszuijderwijk.nl">Jos Zuijderwijk</a></span>
      </div>
    </footer>
  </body>
</html>
