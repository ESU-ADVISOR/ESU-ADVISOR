document.addEventListener('DOMContentLoaded', function() {
    const ratingContainer = document.querySelector('.rating-container');
    if (!ratingContainer) return;

    const radioButtons = Array.from(ratingContainer.querySelectorAll('input[type="radio"]'));
    if (radioButtons.length === 0) return;

    const KEYCODE = {
        LEFT: 37,
        RIGHT: 39,
        UP: 38,
        DOWN: 40,
        HOME: 36,
        END: 35
    };

    radioButtons.forEach(radio => {
        radio.addEventListener('keydown', function(e) {
            if ([KEYCODE.LEFT, KEYCODE.RIGHT, KEYCODE.UP, KEYCODE.DOWN].includes(e.keyCode)) {
                e.preventDefault();
            }

            let index = radioButtons.indexOf(this);
            let nextIndex;

            switch(e.keyCode) {
                case KEYCODE.LEFT:
                case KEYCODE.DOWN:
                    // LEFT/DOWN = decrease rating = move to next radio button
                    nextIndex = Math.min(index + 1, radioButtons.length - 1);
                    break;
                case KEYCODE.RIGHT:
                case KEYCODE.UP:
                    // RIGHT/UP = increase rating = move to previous radio button
                    nextIndex = Math.max(index - 1, 0);
                    break;
                case KEYCODE.HOME:
                    // HOME = highest rating (first in DOM)
                    nextIndex = 0;
                    break;
                case KEYCODE.END:
                    // END = lowest rating (last in DOM)
                    nextIndex = radioButtons.length - 1;
                    break;
                default:
                    return;
            }

            radioButtons[nextIndex].focus();
            radioButtons[nextIndex].checked = true;
            radioButtons[nextIndex].dispatchEvent(new Event('change', { bubbles: true }));
        });
    });
});