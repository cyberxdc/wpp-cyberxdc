jQuery(document).ready(function($) {
    $('.subsubsub li a').on('click', function() {
        $('.subsubsub li a').removeClass('current');
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
        { name: 'Yellow', color: '#ff0' },
        { name: 'Cyan', color: '#0ff' },
        { name: 'Magenta', color: '#f0f' },
        { name: 'White', color: '#fff' },
        { name: 'Black', color: '#000' },];

    return (
        <ColorPalette
            colors={colors}
            value={backgroundColor}
            onChange={setBackgroundColor}
        />
    );
};


