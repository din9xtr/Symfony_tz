const url = new URL('/.well-known/mercure?topic=admin', 'http://localhost:3000');
const eventSource = new EventSource(url);
console.log('EventSource created');
const notifications = new Set();
eventSource.onmessage = function (event) {
    const data = JSON.parse(event.data);

    const notificationKey = `${data.user}-${data.post}-${data.message}`;


    const notification = document.createElement('div');
    notification.classList.add('alert', 'alert-info');
    notification.textContent = `${data.user} ${data.message} ${data.post}`;
    document.getElementById('notifications').appendChild(notification);

    const removeNotification = () => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 500);
    };


    const timer = setTimeout(removeNotification, 10000);

    notification.addEventListener('mouseover', () => clearTimeout(timer));


    notification.addEventListener('mouseout', () => {

        setTimeout(removeNotification, 1000);
    });

};
eventSource.onerror = function (event) {
    console.error('EventSource failed:', event);
};