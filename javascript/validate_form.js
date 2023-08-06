function validateNewTicket(event) {
    const titleInput = document.querySelector('input[name="ticket_title"]');
    const descriptionInput = document.querySelector('textarea[name="ticket_description"]');
    const titleError = document.querySelector('input[name="ticket_title"] + .error');
    const descriptionError = document.querySelector('textarea[name="ticket_description"] + .error');

    let isValid = true;

    if (titleInput.value.trim() === '') {
        titleError.textContent = 'Please enter a title.';
        isValid = false;
    } else if (titleInput.value.trim().length < 10) {
        titleError.textContent = 'The title must be atleast 10 characters long.';
        isValid = false;
    }

    if (descriptionInput.value.trim() === '') {
        descriptionError.textContent = 'Please enter a description.';
        isValid = false;
    } else if (descriptionInput.value.trim().length < 10) {
        descriptionError.textContent = 'The description must be atleast 10 characters long.';
        isValid = false;
    }

    if (!isValid) {
        event.preventDefault();
    }
}

function validateNewFAQ(event) {
    const questionInput = document.querySelector('input[name="faq_question"]');
    const answerInput = document.querySelector('textarea[name="faq_answer"]');
    const questionError = document.querySelector('input[name="faq_question"] + .error');
    const answerError = document.querySelector('textarea[name="faq_answer"] + .error');

    let isValid = true;

    if (questionInput.value.trim() === '') {
        questionError.textContent = 'Please enter a question.';
        isValid = false;
    }

    if (answerInput.value.trim() === '') {
        answerError.textContent = 'Please enter an answer.';
        isValid = false;
    }

    if (!isValid) {
        event.preventDefault();
    }
}

function validateNewComment(event) {
    const textInput = document.querySelector('textarea[name="text"]');

    if (textInput.value.trim() === '') {
        event.preventDefault();
    }
}

const newTicketForm = document.querySelector('section.create_ticket form');
const newFaqForm = document.querySelector('section.create_faq form');
const newCommentForm = document.querySelector('section#comments form');

if(newTicketForm != null) {
    newTicketForm.addEventListener('submit', validateNewTicket);
}

if(newFaqForm != null) {
    newFaqForm.addEventListener('submit', validateNewFAQ);
}

if(newCommentForm != null) {
    newCommentForm.addEventListener('submit', validateNewComment);
}
