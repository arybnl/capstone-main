<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Messenger</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="img/t3-logo.png" href="img/t3-logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"> 
    <link rel="stylesheet" href="Messenger.css">
    <link rel="stylesheet" href="SideBar.css">

</head>

<body class="body">

    <!-- Toggle button -->
     <button class="btn btn-danger d-md-none" id="menuToggle" style="margin:10px;">
        <i class="bi bi-list"></i>
    </button>

    <!-- Side Bar -->
    <?php include 'Client_sidebar.php'; ?>

    <!------------------------------- Main Container for MESSENGERRRR -------------------------------->

    <div class="content-wrapper">
        <div class="main-container">

        <!-- Left Column -->
        <div class="left-col">
            <!-- Messages Container -->
            <div class="container-box messages-container">
                <div class="container-header">MESSAGES</div>
                <div class="container-underline"></div>
                <div class="messages-list" id="messagesList">
                    <!-- Message items injected by JS -->
                </div>
            </div>
            
            <!-- Active Status Container -->
            <div class="container-box active-status-container">
                <div class="container-header">ACTIVE STATUS</div>
                <div class="container-underline"></div>
                <div class="active-list" id="activeList">
                    <!-- Active items injected by JS -->
                </div>
            </div>
        </div>

        <!-- Chat Column -->
        <div class="chat-col" id="chatCol">
            <div class="chat-header">
                <button class="chat-back-btn" id="chatBackBtn" aria-label="Back">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16"><path d="M15 8a.5.5 0 0 1-.5.5H3.707l3.147 3.146a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 0 1 .708.708L3.707 7.5H14.5A.5.5 0 0 1 15 8z"/></svg>
                </button>
                <span id="chatHeaderName"></span>
            </div>
            <div class="chat-area" id="chatArea">

                <!-- Chat messages injected by JS -->
            </div>
            <div class="chat-input-section">
                <div class="chat-input-icons">
                    <span class="chat-input-icon" id="photoIcon" title="Send Photo">
                        <i class="bi bi-card-image"></i><path d="M4.502 1a1 1 0 0 0-.964.736l-.5 2A1 1 0 0 0 4 4h8a1 1 0 0 0 .962-1.264l-.5-2A1 1 0 0 0 11.498 1h-7z"/><path d="M1 5a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V5zm2.5 3a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm7.5 1.5a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-6a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 1 .5-.5h6z"/></svg>
                    </span>
                    <input type="file" id="photoInput" accept="image/*" style="display:none">
                    <span class="chat-input-icon" id="fileIcon" title="Send File">
                        <i class="bi bi-file-earmark"></i><path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6.5L9.5 0H4zm8 1.5V6h-4a1 1 0 0 1-1-1V1h5z"/></svg>
                    </span>
                    <input type="file" id="fileInput" style="display:none">
                </div>
                <input type="text" class="chat-input" id="chatInput" placeholder="Type here">
                <div class="chat-send" id="chatSendBtn">
                    <span class="chat-send-icon">
                        <i class="bi bi-send-fill"></i><path d="M15.854.146a.5.5 0 0 1 .11.638l-7 14a.5.5 0 0 1-.927-.002l-2.5-6a.5.5 0 0 1 .276-.658l6-2.5a.5.5 0 0 1 .658.276l2.5 6a.5.5 0 0 1-.276.658l-6 2.5a.5.5 0 0 1-.658-.276l-2.5-6a.5.5 0 0 1 .276-.658l6-2.5a.5.5 0 0 1 .658.276l2.5 6a.5.5 0 0 1-.276.658l-6 2.5a.5.5 0 0 1-.658-.276l-2.5-6a.5.5 0 0 1 .276-.658l6-2.5a.5.5 0 0 1 .658.276z"/></svg>
                    </span>
                    <span class="chat-send-text">send</span>
                </div>
            </div>
        </div>
    </div>
    </div>
    
    <script src="Sidebar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="Messenger.js"></script>
</body>
</html>
