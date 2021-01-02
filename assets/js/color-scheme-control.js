/* global colorScheme, Color */
/**
 * Add a listener to the Color Scheme control to update other color controls to new values/defaults.
 * Also trigger an update of the Color Scheme CSS when a color is changed.
 */

( function( api ) {
	var cssTemplate = wp.template('raiden-color-scheme'),
		colorSchemeKeys = [
			'background_color',
			'sidebar_background_color',
			'sidebar_text_color',
			'content_background_color',
			'content_text_color',
			'link_color',
			'button_color'
		],
		colorSettings = [
			'background_color',
			'sidebar_background_color',
			'sidebar_text_color',
			'content_background_color',
			'content_text_color',
			'link_color',
			'button_color'
		];

	api.controlConstructor.select = api.Control.extend( {
		ready: function() {
			if ( 'color_scheme' === this.id ) {
				this.setting.bind( 'change', function( value ) {
					var colors = colorScheme[value].colors;

					// Update Background Color.
					var color = colors[0];
					api( 'background_color' ).set( color );
					api.control(  'background_color').container.find( '.color-picker-hex' )
						.data( 'data-default-color', color )
						.wpColorPicker( 'defaultColor', color );

					// Update Sidebar Background Color.
					color = colors[1];
					api( 'sidebar_background_color' ).set( color );
					api.control(  'sidebar_background_color').container.find( '.color-picker-hex' )
						.data( 'data-default-color', color )
						.wpColorPicker( 'defaultColor', color );

					// Update Sidebar Text Color.
					color = colors[2];
					api( 'sidebar_text_color' ).set( color );
					api.control(  'sidebar_text_color').container.find( '.color-picker-hex' )
						.data( 'data-default-color', color )
						.wpColorPicker( 'defaultColor', color );

					// Update Content Background Color.
					color = colors[3];
					api( 'content_background_color' ).set( color );
					api.control(  'content_background_color').container.find( '.color-picker-hex' )
						.data( 'data-default-color', color )
						.wpColorPicker( 'defaultColor', color );

					// Update Content Text Color.
					color = colors[4];
					api( 'content_text_color' ).set( color );
					api.control(  'content_text_color').container.find( '.color-picker-hex' )
						.data( 'data-default-color', color )
						.wpColorPicker( 'defaultColor', color );

					// Update Link Color.
					color = colors[5];
					api( 'link_color' ).set( color );
					api.control(  'link_color').container.find( '.color-picker-hex' )
						.data( 'data-default-color', color )
						.wpColorPicker( 'defaultColor', color );

					// Update Button Color.
					color = colors[6];
					api( 'button_color' ).set( color );
					api.control(  'button_color').container.find( '.color-picker-hex' )
						.data( 'data-default-color', color )
						.wpColorPicker( 'defaultColor', color );

				} );
			}
		}
	} );

	// Generate the CSS for the current Color Scheme.
	function updateCSS() {
		var scheme = api( 'color_scheme' )(),
			css,
			colors = _.object( colorSchemeKeys, colorScheme[ scheme ].colors );

		_.each( colorSettings, function( setting ) {
			colors[ setting ] = api( setting )();
		} );

		css = cssTemplate( colors );

		api.previewer.send( 'update-color-scheme-css', css );
	}

	// Update the CSS whenever a color setting is changed.
	_.each( colorSettings, function( setting ) {
		api( setting, function( setting ) {
			setting.bind( updateCSS );
		} );
	} );
} )( wp.customize );
