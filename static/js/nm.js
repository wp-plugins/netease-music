var q = "Microsoft Internet Explorer" == window.navigator.appName || "Netscape" == window.navigator.appName && /trident\/\d\.\d/i.test(window.navigator.userAgent) ? "flash, html": "html, flash",
r = nm_ajax_url.swfurl,
myPlaylist;
jQuery(document).on("click", ".album--nice",
function() {
	var a = jQuery(this),
	type = a.data('type') ? a.data('type') : 'album',
	thumbnail = a.data('thumbnail'),
	title = a.data('title'),
	type = a.data('type'),
	tworow = nm_ajax_url.tworow ? ' tworow': '',
	mod = '<div class="audio-jplayer album--wrapper" id="jp_container_N"><div class="album--title">' + a.data('tooltip') + '</div><div class="content-with-thumb"><div class="jp-type-playlist"><div id="jquery_jplayer_N"></div><div class="jp-playlist' + tworow + '"><ul class=""><li></li></ul></div></div><img class="play-thumb" src="' + a.data('thumbnail') + '"></div></div>',
	author = a.data('author');
	d = a.data("id");
	$('.nmplaybar').addClass('appear');
	if (a.hasClass('is-active')) {
		if (a.hasClass('paused')) {
			a.removeClass('paused');
			$(".audio-jplayer").slideDown();

		} else {
			a.addClass('paused');
			$(".audio-jplayer").slideUp();
		}
		return false;

	} else {
		var page = {
			"itemId": d,
			"thumbnail": thumbnail,
			"title": title,
			"author": author
		};

		if (jQuery('#jp_container_N').length > 0) jQuery('#jp_container_N').remove();
		a.parent().after(mod);
		jQuery(".album--nice").removeClass('is-active');
		a.addClass('is-active');
		jQuery.ajax({
			type: "get",
			dataType: "json",
			jsonp: "callback",
			url: nm_ajax_url.ajax_url,
			data: {
				action: "nmjson",
				id: d,
				type: type

			},
			async: !1,
			beforeSend: function() {},
			success: function(b) {
				if (200 == b.msg) {
					var d = a.children(".audio-jplayer");
					b = b.song;
					songs = b.songs;
					myPlaylist = new jPlayerPlaylist({
						jPlayer: "#jquery_jplayer_N",
						cssSelectorAncestor: "#jp_container_N"

					},
					[], {
						playlistOptions: {
							autoPlay: true

						},
						timeupdate: function(c) {
							var b;
							b = c.jPlayer.status.currentTime;
							if (!isFinite(b) || 0 > b) b = "--:--";
							else {
								var d = Math.floor(b / 60);
								b = Math.floor(b) % 60;
								b = (10 > d ? "0" + d: d) + ":" + (10 > b ? "0" + b: b)

							}
							var fuck = myPlaylist,
							holder = jQuery('.nmplayer-lrc');
							current = myPlaylist.current,
							playlist = myPlaylist.playlist,
							lrc = playlist[current].lrc;

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
					jQuery.each(songs,
					function(i, item) {
						myPlaylist.add({
							title: item.song_title,
							artist: item.song_author,
							mp3: item.song_src,
							id: item.song_id,
							lrc: item.song_lrc

						});

					});

				}

			}

		})

	}

});
$("#nmplayer-next").click(function() {
	myPlaylist.next();

});
$("#nmplayer-prev").click(function() {
	myPlaylist.previous();

});
$("#nmplayer-button").click(function() {
var $this = $(this);
if($this.hasClass('paused')){
myPlaylist.play();
			$(this).removeClass('paused');
}else{
	myPlaylist.pause();
			$(this).addClass('paused');
}
});
