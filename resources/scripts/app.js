(function ($) {
	if ($(".index_carousel").length > 0) {
		tns({
			container: ".index_carousel",
			slideBy: "page",
			autoplay: true,
			autoplayHoverPause: true,
			autoplayButton: false,
			nav: false,
			autoplayButtonOutput: false,
			controlsContainer: "#homepage_events_controls",
			speed: 600,
		});
	}

  if ($(".index_carousel").length > 0) {
		tns({
			container: ".index_carousel",
			slideBy: "page",
			autoplay: true,
			autoplayHoverPause: true,
			autoplayButton: false,
			nav: false,
			autoplayButtonOutput: false,
			controlsContainer: "#homepage_events_controls",
			speed: 600,
		});
	}

	if ($(".latest_items_carousel").length > 0) {
		tns({
			container: ".latest_items_carousel",
      responsive: {
        0: {
          items: 1,
        },
        640: {
          items: 2,
        },
        900: {
          items: 3,
        },
        1200: {
          items: 5,
        },
      },
			slideBy: "page",
			autoplay: false,
			autoplayHoverPause: true,
			autoplayButton: false,
			nav: false,
			autoplayButtonOutput: false,
			controlsContainer: "#latest_items_controls",
			speed: 600,
		});
	}

  if ($(".mr_fisher_ajanlasa_carousel").length > 0) {
		tns({
			container: ".mr_fisher_ajanlasa_carousel",
      responsive: {
        0: {
          items: 1,
        },
        640: {
          items: 2,
        },
        900: {
          items: 3,
        },
        1200: {
          items: 5,
        },
      },
			slideBy: "page",
			autoplay: false,
			autoplayHoverPause: true,
			autoplayButton: false,
			nav: false,
			autoplayButtonOutput: false,
			controlsContainer: "#mr_fisher_ajanlasa_controls",
			speed: 600,
		});
	}

  function toggleFavourite(btn) {
    const $btn = $(btn);
    const productId = $btn.data('product-id');

    $btn.prop('disabled', true);

    $.ajax({
      url: "/wp-admin/admin-ajax.php",
      type: 'POST',
      data: {
        action:     'addFavouriteProducts',
        nonce:      FF_Favourites.nonce,
        product_id: productId,
      },
      success: function (response) {
        if (response.success) {
          const isFav = response.data.is_favourite;

          $btn.toggleClass('active', isFav);
          $btn.attr('aria-label', isFav ? 'Remove from favourites' : 'Add to favourites');

          // If on the favourites page and item was removed, hide it
          if (!isFav && $btn.hasClass('ff-remove-fav')) {
            $btn.closest('li').fadeOut(300, function () {
              $(this).remove();
            });
          }
        }
      },
      complete: function () {
        $btn.prop('disabled', false);
      }
    });
  }

  $(document).on('click', '.fav-btn', function () {
    toggleFavourite(this);
  });
})(jQuery);
