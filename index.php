<!DOCTYPE html>

<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en"> <!--<![endif]-->

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />

	<!-- Set the viewport width to device width for mobile -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">

	<title>elockution</title>
  
	<link rel="icon" type="image/png" href="favicon.ico" /> 
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />

	<!-- Included CSS Files -->
	<link rel="stylesheet" href="stylesheets/foundation.css">
	<link rel="stylesheet" href="stylesheets/app.css">

	<!--[if lt IE 9]>
		<link rel="stylesheet" href="stylesheets/ie.css">
	<![endif]-->


	<!-- IE Fix for HTML5 Tags -->
	<!--[if lt IE 9]>
		<script src="javascripts/html5.js"></script>
	<![endif]-->

	<link rel="stylesheet" href="stylesheets/engine.css">
</head>
<body>
	<!-- header -->
	<header class="container">
		<div class="row">
			<div class="twelve columns">
				<span class="title hide-on-phones">
					e<span class="light">lock</span>ution
					<img class="loader" src="./images/ajax-loader.gif" alt="loader"/>
				</span>
				<span class="title phone hide-on-desktops hide-on-tablets">
					e<span class="light">lock</span>ution
					<img class="loader" src="./images/ajax-loader.gif" alt="loader"/>
				</span>
				<span id="menu">
						<a href="#" id="btnParam" class="nice small white button radius" data-reveal-id="modalParametre" data-dismiss-modal-class="close-modal-param">
							<img src="./images/engine.png" alt="icone prametres" style="width: 16px; height: 16px;"/>
							<span class="hide-on-phones">Paramètres</span>
						</a>&nbsp;
						<a href="#" id="btnApropos" data-reveal-id="modalApropos" class="nice small white button radius hide-on-phones">
							<img src="./images/star.png" alt="icone a propos" style="width: 16px; height: 16px;"/>
							<span class="hide-on-phones">A propos</span>
						</a>
				</span>
			</div>
		</div>

	</header>
	<div id="modalApropos" class="reveal-modal">
			<div class="row">
				<div class="twelve columns">
					<h2>A propos :</h2>
					<p class="lead">
						Outil réalisé par CARNICELLI Hugo.
						<br/>
						<br/>
						Veuillez visiter le <a href="http://cubitouch.fr/elockution">Site Officiel</a> pour plus d'informations.
					</p>
				</div>
			</div>
			<a class="close-reveal-modal">&#215;</a>
	</div>
	<div id="modalParametre" class="reveal-modal">
		<form class="nice custom">
			<div class="row">
				<div class="twelve columns">
					<h2>Param&egrave;tres :</h2>
					<!--<p class="lead">Veuillez choisir un canal de discution, un pseudonyme (ainsi qu'une clé si vous souhaitez sécuriser vos échanges).</p>-->
				</div>
			</div>
			<div class="row">
				<div class="twelve columns">
					<label for="channel">Canal :</label>
					<input id="channel" type="text" class="expand input-text mask-alphanum" id="expandedNiceInput" value="general"  maxlength="100"/>
					<input id="channelActive" type="hidden" class="expand input-text mask-alphanum" id="expandedNiceInput" value="general"  maxlength="100"/>
				</div>
			</div>
			<div class="row">
				<div class="twelve columns">
					<label for="pseudo">Pseudo :</label>
					<input id="pseudo" type="text" class="expand input-text" id="expandedNiceInput" value="unknown" maxlength="100"/>
					<input id="pseudoActive" type="hidden" class="expand input-text" id="expandedNiceInput" value="unknown" maxlength="100"/>
					<input id="pseudoId" type="hidden"/>
				</div>
			</div>
			<div class="row">
				<div class="twelve columns">
					<label for="key">Clé :</label>
					<input id="key" type="password" class="expand input-text" id="expandedNiceInput" value=""  maxlength="100"/>
					<input id="keyActive" type="hidden" class="expand input-text" id="expandedNiceInput" value=""  maxlength="100"/>
					<span id="keyeval"></span>
				</div>
			</div>
			<div class="row">
				<div class="twelve columns">
					<img class="loginLoader" src="./images/ajax-loader-2.gif" alt="loader" style="display: none;"/>
					<span class="loginMessage" style="display: none;">Pseudo déjà utilisé.</span>
				</div>
				<div class="twelve columns" style="text-align: right;">
					<a href="#" id="btnReinit" class="expand nice small black button radius" onclick="login();">Dialoguer</a>
				</div>
			</div>
			<a class="close-reveal-modal close-modal-param">&#215;</a>
		</form>
	</div>
	<!-- header -->

	<!-- content -->
	<div id="content">
		<div class="container">
			<div class="row">
				<div class="twelve columns">
					<h2 id="channellabel">general</h2>
				</div>
			</div>
			<div class="row">
				<div class="three columns">
					<div class="panel">
						<h5>Utilisateurs :</h5><br/>
						<ul id="usersList">
						</ul>
					</div>
				</div>
				<div class="nine columns">
					<div id="msgList">
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- content -->

	<!-- footer -->
	<footer>
		<div class="row">
			<form class="nice custom">
				<div class="ten columns">
					<input id="msg" type="text" class="expand input-text" id="expandedNiceInput" maxlength="512">
				</div>
				<div class="two columns">
					<a id="btnEnvoi" class="expand nice small white button radius" onclick="post(); return false;">Envoyer</a>
				</div>
			</form>
		</div>
		<hr class='clearSep'/>
	</footer>
	<!-- footer -->

	<!-- FONDATION -->
	<script src="javascripts/jquery.min.js"></script>
	<script src="javascripts/modernizr.foundation.js"></script>
	<script src="javascripts/foundation.js"></script>
	<script src="javascripts/app.js"></script>
	<!-- AES -->
	<script src="javascripts/aes.js"></script>
	<script src="javascripts/aes-ctr.js"></script>
	<script src="javascripts/utf8.js"></script>
	<script src="javascripts/base64.js"></script>
	<!-- INPUT MASK -->
	<script src="javascripts/jquery.keyfilter.js"></script>
	<!-- DATE FORMAT -->
	<script src="javascripts/date-format.js"></script>
	<!-- PASSWORD EVAL -->
	<script src="javascripts/jquery.pwdstr-1.0.source.js"></script>
	<!-- TYNICONE -->
	<script src="javascripts/tinycon.min.js"></script>
	<!-- ISCROLL -->
	<script src="javascripts/iscroll.js"></script>
	<!-- ENGINE -->
	<script src="javascripts/engine.js"></script>

</body>
</html>
