(function( $ ) {
	'use strict';

	$(function() {

		if (typeof params != 'undefined') {
			//console.log(params);
			// If Scheduled logos set switch main logo
			if (params.scheduled_logo == 1 && params.scheduled_logo_container != "") {
				var holder = params.scheduled_logo_container;
				var $holder = $('#'+holder);
				if ($('#'+holder).length > 0) {
					$holder = $('#'+holder);
					var $logoImg = $('#'+holder+ ' img').first();
				} else if ($('.'+holder).length > 0) {
					$holder = $('.'+holder);
					var $logoImg = $('.'+holder+ ' img').first();
				}

				if ($holder.length > 0) {
					var originalHeight = $($logoImg).height();
					$($logoImg).attr('src', params.scheduled_logo_url);
					$($logoImg).attr('srcset', params.scheduled_logo_url + ' 1x');
				}
			}
		}

		/**
		 * Allow addition of multiple logo schedules
		 *
		 */
	});
	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
         *
         * The file is enqueued from inc/frontend/class-frontend.php.
	 */

})( jQuery );
