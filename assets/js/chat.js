const form = document.getElementById('chat-form');
const messageInput = document.getElementById('message');
const chatBox = document.getElementById('chat-box');

function addMessageToChat(login, message, time) {
    const messageElement = document.createElement('li');
    messageElement.innerHTML = `<strong>${login}</strong>: ${message} (${time})`;
    chatBox.appendChild(messageElement);
    chatBox.scrollTop = chatBox.scrollHeight; 
}
form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const content = messageInput.value.trim();
    if (!content) {
        return; 
    }
    const response = await fetch('/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({ content }),
    });

    if (response.ok) {
        messageInput.value = ''; 
    }
});

const eventSource = new EventSource('http://localhost:3000/.well-known/mercure?topic=chat');
eventSource.onmessage = (event) => {
    const data = JSON.parse(event.data);


    const now = new Date().toLocaleTimeString(); // в контроллере обработку времени добавить
    addMessageToChat(data.user, data.content, now);
};

window.addEventListener('load', () => {
    chatBox.scrollTop = chatBox.scrollHeight;
});