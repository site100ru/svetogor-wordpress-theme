/* Функция "Прилипало" */
/*onscroll = function prilipalo() {
	var prokrutka = window.pageYOffset;
	if ( window.screen.width >= 769 ) {
		if ( prokrutka > 50 ) {
			document.getElementById('top-menu-2').classList.add('fixed-top');
			document.getElementById('top-menu-2').style.position = 'fixed';
			document.getElementById('top-menu-2').style.top = 0;
		} else {
			document.getElementById('top-menu-2').classList.remove('fixed-top');
			document.getElementById('top-menu-2').style.position = 'absolute';
			document.getElementById('top-menu-2').style.top = '59px';
		}
	} else {
		document.getElementById('top-menu-2').style.position = '';
		document.getElementById('top-menu-2').style.top = 0;
		document.getElementById('top-menu-2').classList.add('fixed-top');
	}
}*/


/* Parallax home section */
$(window).scroll(function (e) {
	var scrolled = $(window).scrollTop();
	$('.parallax-home-section').css('top', (-(scrolled * .35)) + 'px'); // 35 - скорость прокрутки
});
/* End parallax home section */


/* Функция "Выезжало */
function vyezjalo() {
	onscroll = function () {
		var prokrutka = window.pageYOffset;
		if (window.screen.width >= 992) {
			if (prokrutka > 300) {
				document.getElementById('sliding-header').style.top = '0px';
			} else if (prokrutka <= 300) {
				document.getElementById('sliding-header').style.top = '-100px';
			}
		}
	}
}


/* Функция "Прилипало" */
function prilipalo() {
	onscroll = function () {
		var prokrutka = window.pageYOffset;
		if (window.screen.width >= 769) {
			if (prokrutka > 57) {
				document.getElementById('header-2-bottom').classList.add('fixed-top');
				document.getElementById('header-2-bottom').style.position = 'fixed';
				document.getElementById('header-2-bottom').style.top = 0;
			} else {
				document.getElementById('header-2-bottom').classList.remove('fixed-top');
				document.getElementById('header-2-bottom').style.position = 'absolute';
				//document.getElementById('header-2-bottom').style.width = '100%';
				document.getElementById('header-2-bottom').style.top = '57px';
			}
		} else {
			document.getElementById('header-2-bottom').style.position = '';
			document.getElementById('header-2-bottom').style.top = 0;
			document.getElementById('header-2-bottom').classList.add('fixed-top');
		}
	}
}


/* Убираем сообщение об успешной отправки */
function modalClose() {
	document.getElementById('background-msg').style.display = 'none';
	document.getElementById('message').style.display = 'none';
	document.getElementById('btn-close').style.display = 'none';
}
