// example data
        const users = [
            {
                id: 1,
                name: "Alice",
                profile: "https://randomuser.me/api/portraits/women/1.jpg",
                online: true,
                messages: [
                    { text: "Hey, how are you?", time: "09:15", incoming: true, status: "seen" },
                    { text: "I'm good, thanks!", time: "09:16", incoming: false, status: "delivered" },
                    { text: "Are you coming today?", time: "09:17", incoming: true, status: "sent" }
                ]
            },
            {
                id: 2,
                name: "Bob",
                profile: "https://randomuser.me/api/portraits/men/2.jpg",
                online: false,
                messages: [
                    { text: "Did you finish the report?", time: "08:45", incoming: true, status: "delivered" },
                    { text: "Yes, sent it to your email.", time: "08:46", incoming: false, status: "sent" }
                ]
            },
            {
                id: 3,
                name: "Carol",
                profile: "https://randomuser.me/api/portraits/women/3.jpg",
                online: true,
                messages: [
                    { text: "Lunch at 12?", time: "07:30", incoming: true, status: "seen" }
                ]
            },
            {
                id: 4,
                name: "Dave",
                profile: "https://randomuser.me/api/portraits/men/4.jpg",
                online: false,
                messages: [
                    { text: "Happy Birthday!", time: "06:00", incoming: true, status: "delivered" }
                ]
            }
        ];

        // Active users for Active Status container
        const activeUsers = users.map(u => ({
            id: u.id,
            name: u.name,
            profile: u.profile,
            online: u.online
        }));

        let selectedUserId = users[0].id;

        // Render Messages List
        function renderMessagesList() {
            const messagesList = document.getElementById('messagesList');
            messagesList.innerHTML = '';
            users.forEach(user => {
                const lastMsg = user.messages[user.messages.length - 1];
                const item = document.createElement('div');
                item.className = 'message-item';
                item.dataset.userid = user.id;
                if (user.id === selectedUserId) item.style.background = 'rgba(255,255,255,0.08)';
                item.innerHTML = `
                    <div class="message-left">
                        <div style="position:relative;">
                            <img src="${user.profile}" class="profile-pic" alt="${user.name}">
                            <span class="status-circle ${user.online ? 'status-online' : 'status-offline'}"></span>
                        </div>
                        <div class="message-info">
                            <span class="sender-name">${user.name}</span>
                            <span class="last-message">${lastMsg.text}</span>
                        </div>
                    </div>
                    <div class="message-right">
                        <span class="last-time">${lastMsg.time}</span>
                        <span class="last-status">${lastMsg.status}</span>
                    </div>
                `;
                item.onclick = () => selectConversation(user.id);
                messagesList.appendChild(item);
            });
        }

        // Render Active Status List
        function renderActiveList() {
            const activeList = document.getElementById('activeList');
            activeList.innerHTML = '';
            activeUsers.forEach(user => {
                const item = document.createElement('div');
                item.className = 'active-item';
                item.innerHTML = `
                    <div style="position:relative;">
                        <img src="${user.profile}" class="profile-pic" alt="${user.name}">
                        <span class="status-circle ${user.online ? 'status-online' : 'status-offline'}"></span>
                    </div>
                    <span class="active-name">${user.name}</span>
                `;
                activeList.appendChild(item);
            });
        }

        // Render Chat Area
        function renderChatArea() {
            const chatArea = document.getElementById('chatArea');
            const chatHeaderName = document.getElementById('chatHeaderName');
            const user = users.find(u => u.id === selectedUserId);
            chatHeaderName.textContent = user.name;
            chatArea.innerHTML = '';
            user.messages.forEach(msg => {
                const msgDiv = document.createElement('div');
                msgDiv.className = 'chat-message ' + (msg.incoming ? 'chat-incoming' : 'chat-outgoing');
                msgDiv.innerHTML = `
                    ${msg.text}
                    ${!msg.incoming ? `<div class="chat-status">${msg.status}</div>` : ''}
                `;
                chatArea.appendChild(msgDiv);
            });
            chatArea.scrollTop = chatArea.scrollHeight;
        }

        // Select conversation
        function selectConversation(id) {
            selectedUserId = id;
            renderMessagesList();
            renderChatArea();
            // Mobile: show chat, hide messages
            if (window.innerWidth < 768) {
                document.getElementById('chatCol').classList.add('active');
                document.querySelector('.messages-container').style.display = 'none';
            }
        }

        // Back button for mobile
        document.getElementById('chatBackBtn').onclick = function() {
            document.getElementById('chatCol').classList.remove('active');
            document.querySelector('.messages-container').style.display = '';
        };

        // Send message
        function sendMessage() {
            const input = document.getElementById('chatInput');
            const text = input.value.trim();
            if (!text) return;
            const now = new Date();
            const time = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            const user = users.find(u => u.id === selectedUserId);
            user.messages.push({ text, time, incoming: false, status: "sent" });
            input.value = '';
            renderMessagesList();
            renderChatArea();
        }

        document.getElementById('chatSendBtn').onclick = sendMessage;
        document.getElementById('chatInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') sendMessage();
        });

        // File/Image icon triggers
        document.getElementById('photoIcon').onclick = function() {
            document.getElementById('photoInput').click();
        };
        document.getElementById('fileIcon').onclick = function() {
            document.getElementById('fileInput').click();
        };

        // Prevent scrollbars
        document.body.style.overflow = 'hidden';

        // Initial render
        renderMessagesList();
        renderActiveList();
        renderChatArea();

        // Responsive: handle resize for mobile/desktop switch
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                document.getElementById('chatCol').classList.remove('active');
                document.querySelector('.messages-container').style.display = '';
            }
        });