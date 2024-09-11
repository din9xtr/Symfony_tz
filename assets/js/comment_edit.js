function editComment(commentId, commentText, commentStatus) {

    document.getElementById('comment_id').value = commentId;

    document.querySelector('[name="comment[text]"]').value = commentText;

    let statusField = document.querySelector('[name="comment[status]"]');
    if (statusField) {
        statusField.value = commentStatus;
    }


    let submitButton = document.querySelector('button[type="submit"]');
    if (submitButton) {
        submitButton.textContent = 'Update Comment';
    }

    document.querySelector('form').scrollIntoView();
}