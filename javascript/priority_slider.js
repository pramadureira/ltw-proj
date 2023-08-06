function updateSliderId(slider){
    switch (slider.value) {
        case "0":
            slider.id = 'low_priority'
            break;
        case "1":
            slider.id = 'medium_priority'
            break;
        case "2":
            slider.id = 'high_priority'
            break;
    }
}

function updateSlider(e) {
    const slider = e.currentTarget

    updateSliderId(slider)
}

const slider = document.querySelector('section.create_ticket form #ticket_priority input')

if (slider) {
    updateSliderId(slider)
    slider.addEventListener('input', updateSlider)
}
