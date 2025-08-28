document.addEventListener('DOMContentLoaded', function() {
    const chatbotToggler = document.querySelector(".chatbot-toggler");
    const chatbotContainer = document.querySelector(".chatbot-container");
    const closeBtn = document.querySelector(".chatbot-close-btn");
    const chatMessages = document.querySelector(".chatbot-messages");
    const optionsContainer = document.querySelector(".chatbot-options");

    const toggleChatbot = () => {
        chatbotContainer.classList.toggle("show");
    }

    chatbotToggler.addEventListener("click", toggleChatbot);
    closeBtn.addEventListener("click", toggleChatbot);

    const addMessage = (message, sender) => {
        const messageDiv = document.createElement("div");
        messageDiv.classList.add("chat-message", sender);
        messageDiv.textContent = message;
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    const loadQuestions = () => {
        fetch('get_chatbot_response.php?action=get_questions')
            .then(response => response.json())
            .then(data => {
                optionsContainer.innerHTML = '';
                data.forEach(q => {
                    const button = document.createElement('button');
                    button.classList.add('option-btn');
                    button.textContent = q.question;
                    button.dataset.id = q.id;
                    button.addEventListener('click', handleOptionClick);
                    optionsContainer.appendChild(button);
                });
            });
    }

    const handleOptionClick = (e) => {
        const questionText = e.target.textContent;
        const questionId = e.target.dataset.id;
        addMessage(questionText, 'user');

        // Tampilkan "typing..."
        const typingDiv = document.createElement("div");
        typingDiv.classList.add("chat-message", "bot", "typing");
        typingDiv.textContent = "Bot is typing...";
        chatMessages.appendChild(typingDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        fetch(`get_chatbot_response.php?action=get_answer&id=${questionId}`)
            .then(response => response.json())
            .then(data => {
                // Hapus "typing..."
                chatMessages.removeChild(typingDiv);
                // Tampilkan jawaban
                addMessage(data.answer, 'bot');
            });
    }

    loadQuestions();
});