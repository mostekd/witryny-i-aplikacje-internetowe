    document.querySelector('button').addEventListener('click', function() {
        // Get the input field value
        const messageInput = document.querySelector('input');
        const messageText = messageInput.value.trim();

        if (messageText !== "") {
            const newMessage = document.createElement('div');
            newMessage.classList.add('message', 'right');
            
            // Create the avatar image
            const avatar1 = document.createElement('img1');
            avatar1.src = 'https://img.freepik.com/premium-zdjecie/mezczyzna-w-czarnej-koszuli-z-napisem-quote_1262781-47950.jpg';
            avatar1.classList.add('avatar1');

            const avatar2 = document.createElement('img2');
            avatar2.src = 'https://img.freepik.com/premium-zdjecie/mezczyzna-w-czarnej-koszuli-z-napisem-quote_1262781-47950.jpg';
            avatar2.classList.add('avatar2');
            
            const text = document.createElement('div');
            text.classList.add('text');
            text.textContent = messageText;

            newMessage.appendChild(avatar1);
            newMessage.appendChild(text);

            document.querySelector('.chat-container').appendChild(newMessage);

            messageInput.value = '';
        }
    });

    document.querySelector('input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            document.querySelector('button').click();
        }
    });