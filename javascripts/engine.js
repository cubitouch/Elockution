//tynicone
var focusFlag = 1;
var msgMissed = 0;
//iScroll
var scrollContent;
//timeouts
var aliveTimeout = false;
var checkTimeout = false;

$(function () {
	$(window).bind("focus", function(event)
	{
		focusFlag = 1;
	});

	$(window).bind("blur", function(event)
	{
		focusFlag = 0;
	});

	/*$('#btnEnvoi').click( function(event) {
		preventHandler(event);
		post();
		return false;
	} );*/
	/*$('#btnReinit').click( function(event) {
		preventHandler(event);
		clear();
		return false;
	} );*/

	$('#msg').keypress( function(event) {
		if (event.which == 13) {
			preventHandler(event);
			$('#btnEnvoi').click();
		}
	} );
	$('#channel, #pseudo, #key').keypress( function(event) {
		if (event.which == 13) {
			preventHandler(event);
			$('#btnReinit').click();
		}
	} );


	Tinycon.setOptions({
		width: 7,
		height: 9,
		font: '10px arial',
		colour: '#ffffff',
		background: '#549A2F',
		fallback: true
	});

	$('#key').pwdstr('#keyeval');
	$('#btnParam').click();

	
	$(document).bind('touchmove', function(e){ e.preventDefault(); });
	setTimeout(loadScroll,200);

	// Check screen size on orientation change
	window.addEventListener('onorientationchange' in window ? 'orientationchange' : 'resize', setHeight, false);
});

function loadScroll() {
	scrollContent = new iScroll('content', {snap: true, momentum: true});
	setHeight();
}

function setHeight() {
	var headerH = $('header').innerHeight(),
		footerH = $('footer').innerHeight(),
		wrapperH = window.innerHeight - headerH - footerH;
	$('#content').height(wrapperH-6 + 'px');
}

function setChannelActive(val) { $('#channelActive').val(val); }
function setKeyActive(val) { $('#keyActive').val(val); }
function setPseudoActive(val) { $('#pseudoActive').val(val); }

function getChannelActive() { return $('#channelActive').val().trim(); }
function getKeyActive() { return $('#keyActive').val().trim(); }
function getPseudoActive() { return $('#pseudoActive').val().trim(); }


function getChannel() { return $('#channel').val().trim(); }
function getKey() { return $('#key').val().trim(); }
function getPseudo() { return $('#pseudo').val().trim(); }

function getPseudoId() { return $('#pseudoId').val().trim(); }
function setPseudoId(id) { $('#pseudoId').val(id); }

function getMessage() { return $('#msg').val().trim(); }
function clearMessage() { return $('#msg').val(''); }

function disableMessage() {
	$('#msg').attr('disabled', '');
}
function enableMessage() {
	$('#msg').removeAttr('disabled');
}
function disableLogin() {
	$('#channel').attr('disabled', '');
	$('#pseudo').attr('disabled', '');
	$('#key').attr('disabled', '');
}
function enableLogin() {
	$('#channel').removeAttr('disabled');
	$('#pseudo').removeAttr('disabled');
	$('#key').removeAttr('disabled');
}

function encode(str) {  return Aes.Ctr.encrypt(str, getKey(), 256); }
function decode(str) {  return Aes.Ctr.decrypt(str, getKey(), 256); }

function displayLoader(display) {
	if (display) {
		$('.loader').show();
	} else {
		$('.loader').hide();
	}
}
function displayLoginLoader(display) {
	if (display) {
		$('.loginLoader').show();
	} else {
		$('.loginLoader').hide();
	}
}
function displayLoginMessage(display) {
	if (display) {
		$('.loginMessage').show();
	} else {
		$('.loginMessage').hide();
	}
}


function api(params, callback) {
	$.post("api.php",
		params,
		callback
	);
}

function clear() {
	$('#msgList .row.msg').each(function () {
		$(this).remove();
	});
	$('#channellabel').text(getChannelActive());
	displayLoader(true);
	api({
		action: 'post',
		msg: encode('**CONNEXION**'),
		channel: getChannelActive(),
		pseudo: encode(getPseudoActive()),
		temoin: encode('temoin')
	}, function(data) {
		if (data == 'WAIT') {
			setTimeout(post,1000);
			return false;
		}
		clearMessage();
		enableMessage();
		displayLoader(false);
		
		if (checkTimeout == false) {
			checkTimeout = true;
			check();
		}
		if (aliveTimeout == false) {
			aliveTimeout = true;
			alive();
		}
	});
}

function login() {
	if (getPseudo() != '') {
		displayLoginLoader(true);
		api({
			action: 'attempt',
			channel: getChannel()
		}, function(data) {
			if (data == 'WAIT') {
				setTimeout(attempt,1000);
				return false;
			}
		
			var json = $.parseJSON(data);
			for (i = 0; i < json.users.length; i++) {
				if (getPseudo() == decode(json.users[i].pseudo)) {
					displayLoginLoader(false);
					displayLoginMessage(true);
					return false;
				}
			}

			disableLogin();
			api({
				action: 'login',
				channel: getChannel(),
				pseudo: encode(getPseudo()),
				temoin: encode('temoin')
			}, function(data) {
				if (data == 'WAIT') {
					setTimeout(post,1000);
					return false;
				}
				//affecter Active
				setChannelActive(getChannel());
				setPseudoActive(getPseudo());
				setKeyActive(getKey());
				
				setPseudoId(data);
				enableLogin();
				displayLoginLoader(false);
				displayLoginMessage(false);
				clear();
				$('.close-modal-param').click();
				return true;
			});

		});
	}
}

function alive() {
	api({
		action: 'alive',
		id: getPseudoId()
	 }, function (data) {
		if (data != 'WAIT') {
			setPseudoId(data);
			setTimeout(alive,1000);
			return true;
		}
		setTimeout(alive,10000);
		return false;
	});
}


function check() {
	displayLoader(true);
	var exclude = 0;
	$('.msg').each(function () {
		if (exclude < parseInt($(this).attr('id'))) {
			exclude =  parseInt($(this).attr('id'));
		}
	});

	api({
		action: 'get',
		exclude: exclude,
		channel: getChannelActive()
	 }, function (data) {
		var json = jQuery.parseJSON(data);
		var msg = "";
		var user = "";
		var date = null;
		var item = "";

		var last = null;
		var notif = false;
		if ($('.alert-box').length == 0)
			msgMissed = 0;
		if (json != null) {
			// MESSAGES
			for (i=0; i < json.messages.length; i++) {
				if (decode(json.messages[i].temoin) == 'temoin') {
					date = new Date(json.messages[i].id / 1000);
					user = "<strong>" + decode(json.messages[i].pseudo) + "</strong> (" + date.format("dd/mm/yyyy HH:MM:ss") + ") : ";
					msg = replaceURLWithHTMLLinks(decode(json.messages[i].msg));

					item = "<div id='" + json.messages[i].id + "' class='row msg";
					if (focusFlag && $('.alert-box').length == 0) {
						last = '#' + json.messages[i].id;
					} else  {
						item += " hidden";
						msgMissed++;
					}
					item += "'><div class='messageitem";
					if (msg.match(/^\*\*.+\*\*$/))
						item += " emphase";
					item += "'><div class='twelve columns'>" + user + "" + msg.replace(/^\*\*(.+)\*\*$/,'<i>$1</i>') + "</div><hr class='clearSep'/></div></div>";
					$('#msgList').append(item);
				} else {
					$('#msgList').append("<div id='" + json.messages[i].id + "' class='row msg' style='display: none;'></div>");
				}
			}
			if (!focusFlag || msgMissed > 0) {
				if ($('.alert-box').length == 0 && msgMissed > 0) {
					notif = true;
					$('#msgList').append('<div class="alert-box success" onclick="appear();">' + msgMissed + ' Nouveau(x) message(s)</div>');
				}
				else {
					$('#msgList .alert-box.success').html(msgMissed + ' Nouveau(x) message(s)');
				}
				Tinycon.setBubble(msgMissed);
			} else {
				//desactiver favicon numérique
			}
			// UTILISATEURS
			$('#usersList li').remove();
			var couleur = '';
			var now = new Date();
			for (i=0; i < json.users.length; i++) {
				if (decode(json.users[i].temoin) == 'temoin') {
					date = new Date(json.users[i].id / 1000);
					//console.log(now.getTime() - date.getTime());
					if (decode(json.users[i].pseudo) == getPseudoActive())
						couleur = 'voyantBleu';
					else if ((now.getTime() - date.getTime())/1000 > 2*60) // au bout de 2 minutes d'inactivite
						couleur = 'voyantRouge';
					else
						couleur = 'voyantVert';
					$('#usersList').append("<li><span class='" + couleur + "'></span>" + decode(json.users[i].pseudo) + "</li>");
				}
			}
			displayLoader(false);
		} else {
			//TEST DE LA VALEUR DE RETOUR
		}

		scrollContent.refresh();
		if (last != null) {
			scrollTo(last);
		}
		if (notif == true) {
			scrollTo('.alert-box.success');
		}

		setTimeout(check,2000);
	});
}

function scrollTo(selecteur) {
	if ($(selecteur).offset().top > $('#content').height())
		scrollContent.scrollTo($(selecteur).offset().left, $(selecteur).offset().top - $('#content').height() , 200, true);
}

function appear() {
	var first = $('#msgList .msg.hidden:first').attr('id');

	$('#msgList .alert-box.success').fadeOut(200, function ()  {
		$('#msgList .alert-box.success').remove();
		msgMissed = 0;
		Tinycon.setBubble(msgMissed);
		$('#msgList .msg.hidden').removeClass('hidden');
		setTimeout(function () {
			scrollContent.refresh();
			scrollTo('#' + first);
		}, 0);
	});	
}

function post() {
	if (getMessage() != '') {
		disableMessage();
		displayLoader(true);
		api({
			action: 'post',
			msg: encode(getMessage()),
			channel: getChannelActive(),
			pseudo: encode(getPseudoActive()),
			temoin: encode('temoin')
		}, function(data) {
			if (data == 'WAIT') {
				setTimeout(post,1000);
				return false;
			}
			clearMessage();
			enableMessage();
			displayLoader(false);
		});

	}
}

function preventHandler(event) {
	if(event.preventDefault)
		event.preventDefault();
	else
		event.stop();

	event.returnValue = false;
	event.stopPropagation(); 
}

function replaceURLWithHTMLLinks(text) {
    var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
    return text.replace(exp,"<a href='$1' target='_blank'>$1</a>"); 
}
function rawurlencode (str) {
    // URL-encodes string  
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/rawurlencode
    str = (str + '').toString();
 
    return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').
    replace(/\)/g, '%29').replace(/\*/g, '%2A');
}
