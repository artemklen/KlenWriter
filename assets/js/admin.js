(function () {
	'use strict';

	/**
	 * Initializes the WordPress media uploader for the logo setting.
	 */
	function initLogoUploader() {
		var uploadButton = document.querySelector('.kw-upload-logo');
		var removeButton = document.querySelector('.kw-remove-logo');
		var logoInput = document.querySelector('.kw-logo-id');
		var logoPreview = document.querySelector('.kw-logo-preview');
		var frame;

		if (!uploadButton || !logoInput || !logoPreview || !window.wp || !window.wp.media) {
			return;
		}

		uploadButton.onclick = function (event) {
			event.preventDefault();

			if (frame) {
				frame.open();
				return;
			}

			frame = window.wp.media({
				title: window.klenWriterAdmin ? window.klenWriterAdmin.title : 'Select logo',
				button: {
					text: window.klenWriterAdmin ? window.klenWriterAdmin.buttonText : 'Use this logo'
				},
				library: {
					type: 'image'
				},
				multiple: false
			});

			frame.on('select', function () {
				var attachment = frame.state().get('selection').first().toJSON();
				var previewUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

				logoInput.value = attachment.id;
				logoPreview.src = previewUrl;
			});

			frame.open();
		};

		if (removeButton) {
			removeButton.onclick = function (event) {
				event.preventDefault();
				logoInput.value = '';
				logoPreview.src = window.klenWriterAdmin ? window.klenWriterAdmin.placeholder : '';
			};
		}
	}

	/**
	 * Initializes WordPress color pickers for KlenWriter color fields.
	 */
	function initColorPickers() {
		if (window.jQuery && window.jQuery.fn && window.jQuery.fn.wpColorPicker) {
			window.jQuery('.kw-color-field').wpColorPicker();
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			initLogoUploader();
			initColorPickers();
		});
	} else {
		initLogoUploader();
		initColorPickers();
	}
}());
