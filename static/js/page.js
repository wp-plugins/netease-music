var q = "Microsoft Internet Explorer" == window.navigator.appName || "Netscape" == window.navigator.appName && /trident\/\d\.\d/i.test(window.navigator.userAgent) ? "flash, html" : "html, flash",
	myPlaylist = new jPlayerPlaylist({
		jPlayer: "#jquery_jplayer_N",
		cssSelectorAncestor: "#jp_container_N"
	}, [], {
		playlistOptions: {
			autoPlay: false
		},
		timeupdate: function(c) {
			var b;
			b = c.jPlayer.status.currentTime;
			b = formatTime(b);
			var fuck = myPlaylist,
				holder = jQuery('.nmplayer-lrc');
			current = myPlaylist.current, playlist = myPlaylist.playlist, lrc = playlist[current].lrc;
			l = parseInt(c.jPlayer.status.currentTime);
			lrc[l] != undefined && (holder.html(lrc[l]));
			$(".nmplayer-time").text(b);
			$(".nmplayer-prosess").width(c.jPlayer.status.currentPercentAbsolute + "%")

		},
		supplied: "mp3",
		smoothPlayBar: true,
		keyEnabled: true,
		audioFullScreen: true
	});
var formatTime = function(b) {
		if (!isFinite(b) || 0 > b) b = "--:--";
		else {
			var d = Math.floor(b / 60);
			b = Math.floor(b) % 60;
			b = (10 > d ? "0" + d : d) + ":" + (10 > b ? "0" + b : b)
		}
		return b;
	}
jQuery(document).on("click", ".album--nice", function() {
	var a = jQuery(this),
		type = a.data('type') ? a.data('type') : 'album',
		thumbnail = a.data('thumbnail'),
		title = a.data('title'),
		tworow = nm_ajax_url.tworow ? ' tworow' : '',
		mod = '<div class="audio-jplayer album--wrapper"><div class="album--title">' + a.data('tooltip') + '</div><div class="content-with-thumb"><ul id="sbplaylist" class="nmplaylist' + tworow + '"></ul><img class="play-thumb" src="' + a.data('thumbnail') + '"></div></div>',
		author = a.data('author');
	d = a.data("id");
	$('.nmplaybar').addClass('appear');
	if (a.hasClass('is-active')) {
		if (a.hasClass('paused')) {
			a.removeClass('paused');
			myPlaylist.play();
			$(".audio-jplayer").slideDown();
			$("#nmplayer-button").removeClass('paused');

		} else {
			a.addClass('paused');
			myPlaylist.pause();
			$(".audio-jplayer").slideUp();
			$("#nmplayer-button").addClass('paused');
		}
		return false;

	} else {
		var page = {
			"itemId": d,
			"thumbnail": thumbnail,
			"title": title,
			"author": author
		};
		$("#nmplayer-button").removeClass('paused');
		$(".album--wrapper").remove();
		myPlaylist.remove();
		a.parent().after(mod);
		jQuery(".album--nice").removeClass('is-active');
		a.addClass('is-active');
		jQuery.ajax({
			type: "post",
			dataType: "json",
			jsonp: "callback",
			url: nm_ajax_url.ajax_url,
			data: {
				action: "nmjson",
				id: d
			},
			async: !1,
			beforeSend: function() {},
			success: function(b) {
				if (200 == b.msg) {
					var listT = '';
					b = b.song;
					songs = b.songs;
					jQuery.each(songs, function(i, item) {
						listT += '<li id="track' + item.id + '" class="sb-list">' + item.title + ' - ' + item.artist + '<span class="song-time">' + formatTime(item.duration / 1000) + '</span></li>';
					});
					jQuery('.nmplaylist').html(listT);
					myPlaylist.setPlaylist(songs);
					myPlaylist.play(0);

				}

			}

		})
	}

});
jQuery(document).on($.jPlayer.event.play, function() {
	var trackid = myPlaylist.playlist[myPlaylist.current].id,
		$track = $("#track" + trackid);
	$(".sb-list").data("status", "ready");
	$track.data("status", "play");
	jQuery(".sb-list").removeClass('nmplaylist-current');
	$track.addClass('nmplaylist-current');
	$('.nmplayer-lrc').empty();
	$('.nmplayer-title').html(myPlaylist.playlist[myPlaylist.current].title + ' - ' + myPlaylist.playlist[myPlaylist.current].artist);
})
jQuery(document).on("click", "#nmplayer-next", function() {
	myPlaylist.next();

});
jQuery(document).on("click", "#nmplayer-prev", function() {
	myPlaylist.previous();

});
jQuery(document).on("click", "#nmplayer-button", function() {
	var $this = $(this);
	if ($this.hasClass('paused')) {
		myPlaylist.play();
		$(this).removeClass('paused');
	} else {
		myPlaylist.pause();
		$(this).addClass('paused');
	}
});
jQuery(document).on("click", ".sb-list", function() {
	var a = $(this).index(),
		button = jQuery('#nmplayer-button');
	"play" == $(this).data("status") ? ($(this).data("status", "pause"), myPlaylist.pause(), button.addClass('paused')) : myPlaylist.current == a ? ($(this).data("status", "play"), myPlaylist.play(), button.removeClass('paused')) : ($(this).data("status", "play"), myPlaylist.play(a), button.removeClass('paused'))

});

jQuery(document).on("click", ".nm-loadmore", function() {
	var $this = $(this),
		paged = $this.data("paged"),
		max = $this.data("max");
	var ajax_data = {
		action: "get_music",
		max: max,
		paged: paged
	};
	jQuery('.music-page-navi').remove();
	jQuery.ajax({
		url: nm_ajax_url.ajax_url,
		type: "POST",
		data: ajax_data,
		dataType: "json",
		success: function(data) {
			if (data.status == 200) {
				jQuery("#nm-wrapper").append(data.data);

				if (data.nav) {
					jQuery("#nm-wrapper").after(data.nav);
				}
			}
		}
	});
});