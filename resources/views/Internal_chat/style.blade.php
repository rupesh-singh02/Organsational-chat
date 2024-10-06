<style>
    .chat-conversation {
        overflow-y: auto;
    }

    [id^="active-contact-id-"] {
        transition: background-color 0.3s ease, color 0.3s ease;
        /* Add any other properties you want to transition */
    }

    [id^="active-contact-id-"].active {
        background-color: #f0f0f0;
        /* Example background color for active state */
        color: #333;
        /* Example text color for active state */
        /* Add any other styles for the active class */
    }

    .cdoc-wrap-content {
        background-color: rgba(255, 127, 93, 0.10);
    }

    .cdoc-wrap-content .box {
        width: 100%;
        height: auto;
        background-color: rgba(255, 127, 93, 0.10);
        box-sizing: border-box;
        box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        padding: 20px;
        border-radius: 8px;
        color: #ff7f5d;
    }

    .gallery {
        display: flex;
        flex-wrap: wrap;
    }

    .gallery-item {
        width: 200px;
        cursor: pointer;
    }

    .lightbox {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        z-index: 999;
        text-align: center;
    }

    .lightbox-img {
        display: block;
        margin: auto;
        max-width: 80%;
        max-height: 80%;
    }

    .close-btn {
        color: white;
        font-size: 40px;
        position: absolute;
        top: 15px;
        right: 30px;
        cursor: pointer;
    }

    .replay-btn,
    .download-btn {
        color: white;
        font-size: 40px;
        cursor: pointer;
    }

    .replay-btn {
        position: absolute;
        top: 15px;
        right: 130px;
    }

    .download-btn {
        position: absolute;
        top: 15px;
        right: 70px;
    }

    .prev-btn,
    .next-btn {
        color: white;
        font-size: 40px;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
    }

    .prev-btn {
        left: 20px;
    }

    .next-btn {
        right: 20px;
    }

    .sent-main-box {
        background-color: #fce5de;
        padding: 15px;
    }

    .sent-main-box .box {
        height: auto;
        background-color: rgba(255, 255, 255, .5);
        border-left: 2px solid #26df84;
        box-sizing: border-box;
        /* box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1); */
        border-radius: 3px;
    }

    .received-main-box {
        background-color: #ffffff;
        padding: 15px;
    }

    .received-main-box .box {
        height: auto;
        background-color: rgba(255, 127, 93, 0.10);
        border-left: 2px solid #26df84;
        box-sizing: border-box;
        /* box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1); */
        border-radius: 3px;
    }

    .highlight-background {
        background-color: rgba(255, 127, 93, 0.10) !important;
    }

    .fade-out {
        animation: fadeOutAnimation 0.5s ease-in-out forwards;
    }

    @keyframes fadeOutAnimation {
        0% {
            opacity: 1;
        }

        100% {
            opacity: 0;
        }
    }

    .fixed-dimension {
        width: 120px;
        /* Adjust the width as needed */
        height: 120px;
        /* Adjust the height as needed */
        object-fit: cover;
        /* Ensures the image covers the area while maintaining its aspect ratio */
    }

    .chat-conversation .conversation-list .message-img {
        background-color: #ffffff !important;
    }

    .chat-conversation .conversation-list .message-img .message-img-link {
        position: absolute;
        right: 3px !important;
        left: auto;
        bottom: 3px !important;
    }

    .extra_img_overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        /* semi-transparent black */
        color: #fff;
        /* text color */
        font-size: 20px;
        /* adjust font size as needed */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chat-input-section {
        /* For WebKit browsers (Chrome, Safari) */
        width: -webkit-fill-available !important;

        /* For Firefox */
        width: -moz-available !important;

        /* For other browsers */
        width: fill-available !important;
    }

    .message-box {
        box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px !important;
    }

    textarea#chat-input {
        overflow: hidden;
        /* Hide scrollbars */
        resize: none;
        /* Disable manual resizing */
    }

    .conv-date-div.fixed {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
        background-color: inherit;
    }
</style>
