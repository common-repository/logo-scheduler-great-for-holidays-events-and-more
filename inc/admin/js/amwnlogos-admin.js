(function( $ ) {
	'use strict';

	var customUploader;
	var formfield;
	var buttonId;

	/**
	 * WP Media launcher for logo
	 *
	 */
	$(document).on('click', '.logo-upload_button', function(e){
		var attachment;
		buttonId = $(this).attr('id');

		$('html').addClass('Image');

		e.preventDefault();
		//If the uploader object has already been created, reopen the dialog
		if (customUploader) {
			customUploader.open();
			return;
		}
		//Extend the wp.media object
		customUploader = wp.media.frames.file_frame = wp.media({
			title: 'Choose Image',
			button: {
				text: 'Choose Image'
			},
			multiple: false
		});
		//When a file is selected, grab the URL and set it as the text field's value
		customUploader.on('select', function() {
			attachment = customUploader.state().get('selection').first().toJSON();
			if ($('#'+buttonId).data('type') == 'retina') {
				$('#'+buttonId).parents('.form-table').find('input[name="amwnlogos_replacement_logo_retina[]"]').val(attachment.id);
			} else {
				$('#'+buttonId).parents('.form-table').find('input[name="amwnlogos_replacement_logo[]"]').val(attachment.id);
			}
			console.log($('#'+buttonId).data('type'));
			// replace logo image source
			$('#'+buttonId).prev('.amwn-logo-image, .amwn-logo-image-retina').find('img').attr('src', attachment.url);
			$('#'+buttonId).prev('.amwn-logo-image, .amwn-logo-image-retina').show();
			$('#'+buttonId).hide();
			delete this;
		});
		//Open the uploader dialog
		customUploader.open();
	});

	/**
	 * Click handler for dynamically added logo sections
	 *
	 */
	function clickHandler() {
		var $parentTable = $(this).parents('table');
		var $row = $(this);
		if ($parentTable.hasClass('closed')) {
			$parentTable.removeClass('closed')
									.addClass('open');
			$parentTable.siblings().removeClass('open')
														 .addClass('closed');
			$parentTable.find('input[name="amwnlogos_name[]"]').focus().select();
			$(this).off('click');
			$('input[name="amwnlogos_name[]"]', $parentTable).on('focusout', function(){
				$row.on('click', clickHandler);
			})
		} else {
			$parentTable.removeClass('open')
									.addClass('closed');
		}
	}

	$(function() {
		// Move add logo above global settings
		$('#add-logo').parent().insertBefore( $('#amwnlogos-settings .form-table').last().prev('h2') );

		// Change class name for global settings
		$('#amwnlogos-settings .form-table').last()
			.attr('class', 'wp-list-table widefat fixed')
			.css('max-width', '600px');


		if ($.fn.datetimepicker) {
			$(".datepicker").datetimepicker({
	      dateFormat : "MM d, yy",
				timeFormat : "hh:mm tt"
	    });
		}

		$('.amwn-logo-settings .form-table').addClass('closed');
		$('.amwn-logo-settings .form-table tr:first-child').on('click', clickHandler);

		// Hide rows of hidden fields
		$('input[name="amwnlogos_replacement_logo[]"]').parents('tr').hide();
		$('input[name="amwnlogos_replacement_logo_retina[]"]').parents('tr').hide();

		// Append upload buttons to image
		$('button[name=logo_upload_button]').parents('tr').hide();
		$('button[name=logo_upload_button]').insertAfter( $('button[name=logo_upload_button_retina]').parents('.form-table').find('.amwn-logo-image') );
		$('button[name=logo_upload_button_retina]').parents('tr').hide();
		$('button[name=logo_upload_button_retina]').insertAfter( $('button[name=logo_upload_button_retina]').parents('.form-table').find('.amwn-logo-image-retina') );

		// Close button handler
		$('.close').on('click', function(){
			$(this).parent().hide('slow');
			$(this).parent().next('button').show();
		});

		/**
		 * Create multiple logos if defined
		 *
		 */
		if (typeof params != 'undefined' && typeof params.amwnlogos_start_date == 'object') {
			//console.log(params);

			params.amwnlogos_start_date.forEach(function(element, index) {
				if (index > 0) {
					console.log(params);
					var logoSection = $('#amwnlogos-settings .form-table').first().clone();
					logoSection.addClass('closed');
					$('input[name="amwnlogos_name[]"]', logoSection).val( params.amwnlogos_name[index] );
					$('input[name="amwnlogos_start_date[]"]', logoSection)
						.val( params.amwnlogos_start_date[index] )
						.attr( 'id', 'amwnlogos_start_date_'+index );
					$('input[name="amwnlogos_end_date[]"]', logoSection).val( params.amwnlogos_end_date[index] );
					$('select[name="amwnlogos_repeat[]"]', logoSection).val( params.amwnlogos_repeat[index] );
					$('input[name="amwnlogos_replacement_logo[]"]', logoSection).val( params.amwnlogos_replacement_logo[index] );
					$('.amwn-logo-image img', logoSection).attr( 'src', params.amwnlogos_replacement_logo_images[index] );
					$('.amwn-logo-image-retina img', logoSection).attr( 'src', params.amwnlogos_replacement_logo_images_retina[index] );
					$('input[name="amwnlogos_replacement_logo_retina[]"]', logoSection).val( params.amwnlogos_replacement_logo_retina[index] );
					$('input[name="amwnlogos_logo_holder[]"]', logoSection).val( params.amwnlogos_logo_holder[index] );
					$('button[name=logo_upload_button]', logoSection).attr('id', 'logo_upload_button'+index);
					$('button[name=logo_upload_button_retina]', logoSection).attr('id', 'logo_upload_button_retina'+index);
					// Set logo name label to actual logo name
					$('tr:first-child th', logoSection).html(params.amwnlogos_name[index]);

					// Hide upload buttons if image present
					if ( params.amwnlogos_replacement_logo_images[index] != "" ) {
						$('button[name=logo_upload_button]', logoSection).hide();
					}

					// insert at the end of all the logo boxes
					logoSection.insertAfter( $('#amwnlogos-settings .form-table').last() );

					$(".datepicker", logoSection)
						.removeClass('hasDatepicker')
						.attr('id', '');
					$(".datepicker", logoSection).datetimepicker({
						dateFormat : "MM d, yy",
						timeFormat : "hh:mm tt"
			    });

					$("tr:first", logoSection).on('click', clickHandler);

					// Close button handler
					$('.close', logoSection).on('click', function(){
						$(this).parent().hide('slow');
						$(this).parent().next('button').show();
					});
				} else {
					var logoSection = $('#amwnlogos-settings .form-table').first();

					$('#amwnlogos-settings .form-table input[name="amwnlogos_start_date[]"]').val(element);
					$('#amwnlogos-settings .form-table input[name="amwnlogos_name[]"]').val( params.amwnlogos_name[index] );
					$('#amwnlogos-settings .form-table input[name="amwnlogos_end_date[]"]').val( params.amwnlogos_end_date[index] );
					$('#amwnlogos-settings .form-table select[name="amwnlogos_repeat[]"]').val( params.amwnlogos_repeat[index] );
					$('#amwnlogos-settings .form-table input[name="amwnlogos_replacement_logo[]"]').val( params.amwnlogos_replacement_logo[index] );
					$('#amwnlogos-settings .form-table input[name="amwnlogos_replacement_logo_retina[]"]').val( params.amwnlogos_replacement_logo_retina[index] );
					$('#amwnlogos-settings .form-table input[name="amwnlogos_logo_holder[]"]').val( params.amwnlogos_logo_holder[index] );
					// Set logo name label to actual logo name
					$('#amwnlogos-settings .form-table tr:first-child th').html(params.amwnlogos_name[index]);

					// Hide upload buttons if image present
					if ( params.amwnlogos_replacement_logo_images[index] != "" ) {
						$('button[name=logo_upload_button]', logoSection).hide();
					}
				}
			});

			$('#add-logo').click(function(e) {
				e.preventDefault();
				var logoSection = $('#amwnlogos-settings .form-table').first().clone();
				logoSection.removeClass('closed').addClass('open');
				$('input[name="' + 'amwnlogos_name[]"]', logoSection).val('');
				$('input[name="' + 'amwnlogos_start_date[]"]', logoSection).val('');
				$('input[name="' + 'amwnlogos_end_date[]"]', logoSection).val('');
				$('input[name="' + 'amwnlogos_replacement_logo[]"]', logoSection).val('');
				$('.amwn-logo-image', logoSection).hide();
				$('.amwn-logo-image-retina', logoSection).hide();
				$('input[name="' + 'amwnlogos_replacement_logo_retina[]"]', logoSection).val('');
				$('input[name="' + 'amwnlogos_logo_holder[]"]', logoSection).val( params.amwnlogos_theme_logo_holder );
				$('button[name=logo_upload_button]', logoSection)
					.attr('id', 'logo_upload_button'+$('#amwnlogos-settings .form-table').length)
					.show();
				$('button[name=logo_upload_button_retina]', logoSection)
					.attr('id', 'logo_upload_button_retina'+$('#amwnlogos-settings .form-table').length)
					.show();
				$('tr:first-child th', logoSection).html('Logo');

				// insert at the end of all the logo boxes
				logoSection.insertAfter( $('#amwnlogos-settings .form-table').last() );

				$(".datepicker", logoSection)
					.removeClass('hasDatepicker')
					.attr('id', '');
				$(".datepicker", logoSection).datetimepicker({
					dateFormat : "MM d, yy",
					timeFormat : "hh:mm tt"
		    });

				$("tr:first", logoSection).on('click', clickHandler);

				// Close button handler
				$('.close', logoSection).on('click', function(){
					$(this).parent().hide('slow');
					$(this).parent().next('button').show();
				});
			});
		}

		/**
		 * Click handler for the Admin notice check box
		 */
		$('#share-notice').click(function() {
			$('input[name=amwnlogos_show_credit]').prop('checked', true);
			$('#amwnlogos-settings #submit').trigger('click');
		})
	});


	/**
	 * All of the code for your admin-facing JavaScript source
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
         * The file is enqueued from inc/admin/class-admin.php.
	 */

})( jQuery );
