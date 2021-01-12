(function ($) {

	const GeoLinks = {

		/**
		 * Start the engine.
		 *
		 * @since 2.0.0
		 */
		init: function () {

			// Document ready
			$(document).ready(GeoLinks.ready);

			// Page load
			$(window).on('load', GeoLinks.load);
		},
		/**
		 * Document ready.
		 *
		 * @since 2.0.0
		 */
		ready: function () {
			// Execute
			GeoLinks.executeUIActions();
		},
		/**
		 * Page load.
		 *
		 * @since 2.0.0
		 */
		load: function () {
			// Bind all actions.
			GeoLinks.bindUIActions();
		},

		/**
		 * Execute when the page is loaded
		 * @return mixed
		 */
		executeUIActions: function() {

			let $title_action = $('.edit-php .wrap .page-title-action:last');

			if( $title_action.length == 0 )
				$title_action = $('.edit-php .wrap .wp-heading-inline');

			$title_action.after(
				'<a href="' +
				geol_var.import_link +
				'" id ="' +
				geol_var.import_id +
				'" class="page-title-action">' +
				'<span class="dashicons dashicons-database-import" style="vertical-align:middle"></span> ' +
				geol_var.import_text +
				'</a>'
			);

			$title_action.after(
				'<a href="' +
				geol_var.export_link +
				'" id ="' +
				geol_var.export_id +
				'" class="page-title-action">' +
				'<span class="dashicons dashicons-database-export" style="vertical-align:middle"></span> ' +
				geol_var.export_text +
				'</a>'
			);
		},

		/**
		 * Element bindings.
		 *
		 * @since 2.0.0
		 */
		bindUIActions: function () {
			
			/*$( document.body ).on( 'click', '#' + geol_var.import_id, function(e) {
				e.preventDefault();
				
			} );*/
		}
	};

	GeoLinks.init();
	// Add to global scope.
	window.geolinks = GeoLinks;
})(jQuery);