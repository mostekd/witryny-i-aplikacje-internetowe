    document.querySelector('button').addEventListener('click', function() {
        // Get the input field value
        const messageInput = document.querySelector('input');
        const messageText = messageInput.value.trim();

        // Only proceed if the message is not empty
        if (messageText !== "") {
            // Create a new message element
            const newMessage = document.createElement('div');
            newMessage.classList.add('message', 'right');
            
            // Create the avatar image
            const avatar = document.createElement('img');
            avatar.src = 'user2.jpg'; // Adjust the path for the user's avatar
            avatar.alt = 'user avatar';
            avatar.classList.add('avatar');
            
            // Create the message text
            const text = document.createElement('div');
            text.classList.add('text');
            text.textContent = messageText;

            // Append avatar and text to the new message div
            newMessage.appendChild(avatar);
            newMessage.appendChild(text);

            // Add the new message to the chat container
            document.querySelector('.chat-container').appendChild(newMessage);

            // Clear the input field after sending the message
            messageInput.value = '';
        }
    });

    // Optionally, allow sending the message with the Enter key
    document.querySelector('input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            document.querySelector('button').click();
        }
    });