function activateDropdowns() {
    /* GENERAL DROPDOWN */
    const dropdownButtons = document.querySelectorAll(".dropdown-button");
    if(dropdownButtons != null) {
        for (const dropdownButton of dropdownButtons) {
            dropdownButton.addEventListener("click", function() {
                const content = this.nextElementSibling;
                content.style.display = content.style.display === 'block' ? 'none' : 'block';
            })
        }
    }

    /* PROFILE DROPDOWN */
    const userProfile = document.querySelector('.profile-dropdown');
    const dropdown = document.querySelector('.profile-dropdown-content');

    if(userProfile != null && dropdown != null) {
        userProfile.addEventListener('click', () => {
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });

        document.addEventListener('click', (event) => {
            const isClickInside = userProfile.contains(event.target);
            if (!isClickInside) {
                dropdown.style.display = 'none';
            }
        });
    }

    const faqId = window.location.hash.slice(1);
    if (faqId) {
        const faq = document.querySelector(`.question[data-faq-id="${faqId}"]`);
        if (faq) {
            faq.querySelector('.dropdown-button').click();
            faq.scrollIntoView();
        }
    }
}

activateDropdowns()

