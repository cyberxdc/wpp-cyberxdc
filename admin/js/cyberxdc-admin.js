(function( $ ) {
	'use strict';

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
	 */

})( jQuery );

jQuery(document).ready(function($) {
    // Add click event handler to navigation links
    $('.subsubsub li a').on('click', function() {
        // Remove 'current' class from all navigation links
        $('.subsubsub li a').removeClass('current');
        // Add 'current' class to the clicked navigation link
        $(this).addClass('current');
    });
});


const { useState, useEffect } = wp.element;
const { ColorPalette } = wp.blockEditor;

document.addEventListener('DOMContentLoaded', function () {
    const colorPickerContainer = document.getElementById('cyberxdc-color-picker');

    if (colorPickerContainer) {
        wp.element.render(
            wp.element.createElement(ColorPickerComponent),
            colorPickerContainer
        );
    }
});

const ColorPickerComponent = () => {
    const [backgroundColor, setBackgroundColor] = useState(colorPickerContainer.getAttribute('data-background-color'));

    useEffect(() => {
        document.getElementById('background_color').value = backgroundColor;
    }, [backgroundColor]);

    const colors = [
        { name: 'Red', color: '#f00' },
        { name: 'Green', color: '#0f0' },
        { name: 'Blue', color: '#00f' },
        // Add more colors if needed
    ];

    return (
        <ColorPalette
            colors={colors}
            value={backgroundColor}
            onChange={setBackgroundColor}
        />
    );
};


