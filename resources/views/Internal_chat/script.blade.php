<script>
    // {{-- Fetching data for dashboard --}}
    document.addEventListener("DOMContentLoaded", function() {
        fetchActiveContacts("activeContact");
        fetchInactiveContacts("inactiveContact");
    });

    // {{-- Fetching all active contact list --}}
    function fetchActiveContacts(fileName) {
        fetch("{{ route('get-active-contacts') }}")
            .then(response => response.json())
            .then(data => {

                sessionStorage.setItem(fileName, JSON.stringify(data.contact_list));

                if (fileName === "activeContact") {

                    data.contact_list.forEach(userData => {

                        createContactListElement(userData, data, "");
                        initializeContactListEventListeners();

                    });

                    sortContactListElement("active_userList");

                    document.getElementById("active_user_count").innerHTML = data.active_user_count;
                } else {

                    compareAndDisplayNewContacts("activeContact", "newActiveContact", data);
                    sortContactListElement("active_userList");

                    initializeContactListEventListeners();

                    document.getElementById("active_user_count").innerHTML = data.active_user_count;

                    return;

                }

            })
            .catch(error => console.error('Error fetching active contacts:', error));
    }

    // {{-- Fetching all inactive contact list --}}
    function fetchInactiveContacts(fileName) {
        fetch("{{ route('get-inactive-contacts') }}")
            .then(response => response.json())
            .then(data => {

                sessionStorage.setItem(fileName, JSON.stringify(data.contact_list));

                if (fileName === "inactiveContact") {

                    data.contact_list.forEach(userData => {

                        createContactListElement(userData, data, "");
                        initializeContactListEventListeners();

                    });

                    sortContactListElement("inactive_userList");

                } else {

                    compareAndDisplayNewContacts("inactiveContact", "newInactiveContact", data);
                    sortContactListElement("inactive_userList");

                    initializeContactListEventListeners();
                    return;

                }
            })
            .catch(error => console.error('Error fetching inactive contacts:', error));
    }

    // {{-- create active and inactive contact list --}}
    function createContactListElement(contact, data, active_list_id) {

        // Parent element to append this list item to
        const parentElement = document.getElementById(`${data.type}_userList`);

        // Create the list item
        const listItem = document.createElement('li');
        listItem.id = `${data.type}-contact-id-${contact.id}`;
        listItem.dataset.name = 'direct-message';
        listItem.onclick = initializeContactListEventListeners;
        if (active_list_id === listItem.id) {
            listItem.className = 'active';
        }

        // Create the link
        const link = document.createElement('a');
        link.href = 'javascript: void(0);';
        link.className = 'unread-msg-user';

        // Create the outer div
        const outerDiv = document.createElement('div');
        outerDiv.className = 'd-flex align-items-center mt-1';

        // Create the chat user image div
        const chatUserImgDiv = document.createElement('div');
        chatUserImgDiv.className =
            `flex-shrink-0 chat-user-img ${contact.online_status == 1 ? 'online' : 'away'} align-self-center me-2 ms-0`;

        // Create the avatar div
        const avatarDiv = document.createElement('div');
        avatarDiv.className = 'avatar-xxs';

        // Create the image element
        const img = document.createElement('img');
        img.src = contact.image;
        img.className = 'rounded-circle img-fluid userprofile';
        img.alt = contact.name;

        // Create the user status span
        const userStatusSpan = document.createElement('span');
        userStatusSpan.className = 'user-status';

        // Append image and status span to avatar div
        avatarDiv.appendChild(img);
        avatarDiv.appendChild(userStatusSpan);

        // Append avatar div to chat user image div
        chatUserImgDiv.appendChild(avatarDiv);

        // Create the flex grow div
        const flexGrowDiv = document.createElement('div');
        flexGrowDiv.className = 'flex-grow-1 overflow-hidden';

        // Create the paragraph for the contact name
        const contactNameP = document.createElement('p');
        contactNameP.className = 'text-truncate mb-0';
        contactNameP.textContent = contact.name;

        // Append contact name to flex grow div
        flexGrowDiv.appendChild(contactNameP);

        const full_timeboxDiv = document.createElement('div');
        full_timeboxDiv.className = 'd-none full_timebox';

        // Create the timebox div
        const timeboxDiv = document.createElement('div');
        timeboxDiv.className = 'ms-auto text-muted timebox';

        // Format the created_at timestamp
        if (contact.last_chat_time !== null) {
            const createdAt = new Date(contact.last_chat_time);
            let hours = createdAt.getHours();
            const minutes = createdAt.getMinutes().toString().padStart(2, '0');
            const seconds = createdAt.getSeconds().toString().padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            const formattedHours = hours.toString().padStart(2, '0');

            full_timeboxDiv.textContent = `${formattedHours}:${minutes}:${seconds} ${ampm}`;
            timeboxDiv.textContent = `${formattedHours}:${minutes} ${ampm}`;

        } else {
            full_timeboxDiv.textContent = "";
            timeboxDiv.textContent = "";
        }


        // Append chat user image div, flex grow div, and timebox div to outer div
        outerDiv.appendChild(chatUserImgDiv);
        outerDiv.appendChild(flexGrowDiv);
        outerDiv.appendChild(full_timeboxDiv);
        outerDiv.appendChild(timeboxDiv);

        // Create the text box div
        const tboxDiv = document.createElement('div');
        tboxDiv.classList.add('tbox', 'text-muted');

        // Create the paragraph for the last message
        const lastMessageP = document.createElement('p');

        // Set message and class based on the last chat type
        switch (contact.last_chat_type) {
            case "text":
                lastMessageP.textContent = contact.last_chat_message;
                lastMessageP.classList.add("my-2");
                tboxDiv.classList.add('py-0');
                break;
            case "image":
                lastMessageP.textContent = "Sent an image";
                lastMessageP.classList.add("my-2");
                tboxDiv.classList.add('py-0');
                break;
            case "video":
                lastMessageP.textContent = "Sent a video";
                lastMessageP.classList.add("my-2");
                tboxDiv.classList.add('py-0');
                break;
            case "document":
                lastMessageP.textContent = "Sent a document";
                lastMessageP.classList.add("my-2");
                tboxDiv.classList.add('py-0');
                break;
        }

        // Append last message to tbox div
        tboxDiv.appendChild(lastMessageP);

        // Append outer div and tbox div to link
        link.appendChild(outerDiv);
        link.appendChild(tboxDiv);

        // Append link to list item
        listItem.appendChild(link);

        parentElement.appendChild(listItem);
    }

    function sortContactListElement(parentElement) {

        var userList = document.getElementById(parentElement);

        // Get all the list items inside the parent element
        var listItems = userList.querySelectorAll("li");

        // Convert NodeList to array for easier sorting
        var listArray = Array.from(listItems);

        // Sort the array based on the timebox content in descending order
        listArray.sort(function(a, b) {

            var timeA = a.querySelector(".full_timebox").textContent.trim();
            var timeB = b.querySelector(".full_timebox").textContent.trim();

            // Handle empty values
            if (timeA === "" && timeB === "") {
                return 0;
            }
            if (timeA === "") {
                return 1; // Move empty timebox to the end
            }
            if (timeB === "") {
                return -1; // Move empty timebox to the end
            }

            // Convert time to 24-hour format for comparison
            var dateA = convertTo24Hour(timeA);
            var dateB = convertTo24Hour(timeB);

            return dateB - dateA;
        });

        // Clear the list
        userList.innerHTML = '';

        // Append sorted list items to the parent element
        listArray.forEach(function(item) {
            userList.appendChild(item);
        });
    }

    function convertTo24Hour(time) {
        var [timePart, ampm] = time.split(' ');
        var [hours, minutes] = timePart.split(':');
        hours = parseInt(hours);
        minutes = parseInt(minutes);

        if (ampm === 'PM' && hours !== 12) {
            hours += 12;
        } else if (ampm === 'AM' && hours === 12) {
            hours = 0;
        }

        var date = new Date();
        date.setHours(hours, minutes, 0, 0);

        return date;
    }

    // {{-- compare old and new contact list --}}
    function compareAndDisplayNewContacts(oldContact, newContact, data) {

        // Parent element to append this list item to
        const parentElement = document.getElementById(`${data.type}_userList`);

        // Check if any <li> inside the <ul> has the class 'active'
        const hasActiveClass = parentElement.querySelector('li.active') !== null;

        var active_list_id = "";

        // Output the result
        if (hasActiveClass) {
            active_list_id = parentElement.querySelector('li.active').id;
        }

        const oldContactData = getStoredChatData(oldContact);
        const newContactData = getStoredChatData(newContact);

        // Find new or updated contacts
        const addedOrUpdatedContacts = newContactData.filter(newContact => {
            const oldContact = oldContactData.find(contact => contact.id === newContact.id);
            return !oldContact || JSON.stringify(oldContact) !== JSON.stringify(newContact);
        });

        // Find contacts removed from the old list
        const removedContacts = oldContactData.filter(oldContact => {
            return !newContactData.some(newContact => newContact.id === oldContact.id);
        });

        if (addedOrUpdatedContacts.length > 0) {
            // Display or update contacts
            addedOrUpdatedContacts.forEach(contact => {
                const existingListItem = document.getElementById(`${data.type}-contact-id-${contact.id}`);
                if (existingListItem) {
                    // Update the existing contact
                    existingListItem.remove();
                }
                // Create and append new UI elements for new or updated contacts
                createContactListElement(contact, data, active_list_id);
            });

            // Update stored data with new data
            sessionStorage.setItem(oldContact, JSON.stringify(newContactData));
        }

        if (removedContacts.length > 0) {
            // Remove UI elements for removed contacts
            removedContacts.forEach(contact => {
                const listItem = document.getElementById(`${data.type}-contact-id-${contact.id}`);
                if (listItem) {
                    listItem.remove();
                }
            });

            // Remove removed contacts from the stored data
            const updatedContactData = oldContactData.filter(oldContact =>
                !removedContacts.some(contact => contact.id === oldContact.id)
            );
            sessionStorage.setItem(oldContact, JSON.stringify(updatedContactData));
        }

        // Clear the new contact data from session storage
        sessionStorage.removeItem(newContact);
    }

    // Helper function to convert 12-hour time format to a Date object
    function initializeContactListEventListeners() {

        document.querySelectorAll("#active_userList li").forEach(function(userListItem) {

            userListItem.addEventListener("click", function() {

                sessionStorage.removeItem("chatData");

                const parentNode = userListItem.parentNode;

                if (parentNode) {

                    // Remove active class from all list items
                    document.querySelectorAll("#active_userList li").forEach(function(item) {
                        item.classList.remove("active");
                    });

                    // Remove active class from all list items
                    document.querySelectorAll("#inactive_userList li").forEach(function(item) {
                        item.classList.remove("active");
                    });

                    // Add active class to the clicked list item
                    userListItem.classList.add("active");

                    // Clone and replace the clicked list item to reset event listeners
                    const newUserListItem = userListItem.cloneNode(true);
                    parentNode.replaceChild(newUserListItem, userListItem);

                    // Get the user ID from the clicked list item
                    var clickedUserId = newUserListItem.getAttribute("id");
                    let idWithoutPrefix = clickedUserId.replace("active-contact-id-", "");

                    // Show the staff data
                    fetchConversationData(idWithoutPrefix, "chatData");

                    updateMsgSeenStatus(idWithoutPrefix);

                    // Re-initialize event listeners
                    initializeContactListEventListeners();
                }

            });
        });

        document.querySelectorAll("#inactive_userList li").forEach(function(userListItem) {

            userListItem.addEventListener("click", function() {

                sessionStorage.removeItem("chatData");

                const parentNode = userListItem.parentNode;

                if (parentNode) {

                    // Remove active class from all list items
                    document.querySelectorAll("#active_userList li").forEach(function(item) {
                        item.classList.remove("active");
                    });

                    // Remove active class from all list items
                    document.querySelectorAll("#inactive_userList li").forEach(function(item) {
                        item.classList.remove("active");
                    });

                    // Add active class to the clicked list item
                    userListItem.classList.add("active");

                    const newUserListItem = userListItem.cloneNode(true);
                    parentNode.replaceChild(newUserListItem, userListItem);

                    var clickedUserId = userListItem.closest('li').getAttribute("id");
                    let idWithoutPrefix = clickedUserId.replace("inactive-contact-id-", "");

                    fetchConversationData(idWithoutPrefix, "chatData");

                    updateMsgSeenStatus(idWithoutPrefix);

                    // Re-initialize event listeners
                    initializeContactListEventListeners();
                }

            });
        });

        document.querySelectorAll("#channelList li").forEach(function(channelListItem) {
            channelListItem.addEventListener("click", function() {
                var channelName =
                    channelListItem.querySelector(".text-truncate").innerHTML;

                var userChatTopbarUsername = document.querySelector(
                    ".user-chat-topbar .text-truncate .username"
                );
                var profileOffcanvasUsername = document.querySelector(
                    ".profile-offcanvas .username"
                );

                if (userChatTopbarUsername && profileOffcanvasUsername) {
                    userChatTopbarUsername.innerHTML = channelName;
                    profileOffcanvasUsername.innerHTML = channelName;
                }

                var defaultAvatar = "default-avatar.png";

                var userChatTopbarAvatar = document.querySelector(
                    ".user-chat-topbar .avatar-xs"
                );
                var profileOffcanvasAvatar = document.querySelector(
                    ".profile-offcanvas .avatar-lg"
                );

                if (userChatTopbarAvatar && profileOffcanvasAvatar) {
                    userChatTopbarAvatar.setAttribute("src", defaultAvatar);
                    profileOffcanvasAvatar.setAttribute("src", defaultAvatar);
                }

            });
        });
    }

    // {{-- Fetching conversation data for clicked contact list element --}}
    function fetchConversationData(staff_id, fileName) {

        let url = `/load-chat-data/${staff_id}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.status) {

                    var sent_chat = data.chat_data;

                    sessionStorage.setItem(fileName, JSON.stringify(sent_chat));

                    if (fileName === "chatData") {

                        // const messageContainer = document.getElementById('messages');

                        document.getElementById('coversation-layout').innerHTML = `
                            @include('Internal_chat.Components.Conversation_layout.main_layout')
                        `;

                        userChatTopBar(data.receiver_staff);


                        Object.keys(sent_chat).forEach(date => {

                            // Iterate over each chat within the current date
                            createDateBanner(date);

                            sent_chat[date].forEach(chat => {
                                // Render each chat or reply message card
                                if (chat.reply_id === null) {
                                    messageCard_li(chat);
                                } else {
                                    replyMessageCard(chat);
                                }
                            });
                        });

                        imageClickHandler();
                        sendchat();
                        scrollToBottom();
                    } else {

                        userChatTopBar(data.receiver_staff);
                        compareAndDisplayNewMessages();

                    }

                }
            })
            .catch(error => console.error('Error fetching contact details:', error));
    }
    
    // {{-- Fetching unread message data for clicked contact list element --}}
    function updateMsgSeenStatus(chat_user_id) {

        let url = `/update-msg-seen/${chat_user_id}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    //message status updated
                }
            })
            .catch(error => console.error('Error fetching contact details:', error));
    }

    function userChatTopBar(data) {
        const senders_id = document.getElementById('senders_id');
        const senders_full_id = document.getElementById('senders_full_id');
        const chat_user_img_div = document.getElementById('chat-user-img-div');
        const chat_user_img = document.getElementById('chat-user-img');
        const receiver_name = document.getElementById('receiver-name');
        const user_status = document.getElementById('user-status');

        senders_id.value = data.id;
        senders_full_id.value = (data.online_status == 1 ? 'active' : 'inactive') + '-contact-id-' + data.id;

        const currentClass = data.online_status == 1 ? 'online' : 'away';

        if (!chat_user_img_div.classList.contains(currentClass)) {

            if (chat_user_img_div.classList.contains('online')) {
                chat_user_img_div.classList.remove('online');
            } else if (chat_user_img_div.classList.contains('away')) {
                chat_user_img_div.classList.remove('away');
            }
            chat_user_img_div.classList.add(currentClass);
        }
        chat_user_img_div.classList.add(data.online_status == 1 ? 'online' : 'away');

        chat_user_img.src = data.image;
        receiver_name.innerHTML = data.name;
        user_status.innerHTML = data.online_status == 1 ? 'Online' : 'Offline';

    }

    function createDateBanner(date) {
        const outerDiv = document.createElement('div');
        outerDiv.className = 'd-flex justify-content-center mb-3 conv-date-div';

        const innerP = document.createElement('p');
        innerP.className = 'fw-bold px-3 py-2 m-0 rounded-pill text-white conv-date fs-6';
        innerP.style.backgroundColor = '#ff805d88';

        // Get today's date and yesterday's date
        const today = new Date();
        const yesterday = new Date();
        yesterday.setDate(today.getDate() - 1);

        // Format the dates to match the format of the `date` variable
        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = ('0' + (date.getMonth() + 1)).slice(-2);
            const day = ('0' + date.getDate()).slice(-2);
            return `${year}-${month}-${day}`;
        };

        const formattedToday = formatDate(today);
        const formattedYesterday = formatDate(yesterday);

        if (date === formattedToday) {
            innerP.textContent = 'Today';
        } else if (date === formattedYesterday) {
            innerP.textContent = 'Yesterday';
        } else {
            innerP.textContent = date;
        }

        outerDiv.appendChild(innerP);

        const parentContainer = document.getElementById('users-conversation');
        parentContainer.appendChild(outerDiv);
    }

    // {{-- creating normal message card for conversation list element when contact is clicked --}}
    function messageCard_li(data) {
        
        // Create the main list item
        const listItem = document.createElement('li');
        listItem.className = `chat-list ${data.type === 'sent' ? 'right' : 'left'}`;
        listItem.id = `message-id-${data.id}`;

        // Create the conversation list div
        const conversationList = document.createElement('div');
        conversationList.className = 'conversation-list';

        var chatAvatar;

        if (data.type != "sent") {
            // Create the chat avatar div
            chatAvatar = document.createElement('div');
            chatAvatar.className = 'chat-avatar';

            // Create the img element
            const img = document.createElement('img');
            img.src = data.sender.image;
            img.alt = '';

            // Append img to chat avatar
            chatAvatar.appendChild(img);

        }

        // Create the user chat content div
        const userChatContent = document.createElement('div');
        userChatContent.className = 'user-chat-content';

        // Create the ctext wrap div
        const ctextWrap = document.createElement('div');
        ctextWrap.className = 'ctext-wrap';

        // Create the paragraph for message content or image element for image content
        if (data.message_type === "text") {

            // Create the ctext wrap content div
            const ctextWrapContent = document.createElement('div');
            ctextWrapContent.className = 'ctext-wrap-content message-box';
            ctextWrapContent.id = data.id;

            const messageContent = document.createElement('p');
            messageContent.className = 'mb-0 ctext-content';

            // Helper function to check if a string is a valid URL
            function isValidURL(string) {
                try {
                    new URL(string);
                    return true;
                } catch (_) {
                    return false;
                }
            }

            // Check if data.content.content is a valid URL
            if (isValidURL(data.content.content)) {
                const link = document.createElement('a');
                link.href = data.content.content;
                link.textContent = data.content.content;
                link.target = '_blank'; // Open link in a new tab
                messageContent.appendChild(link);
            } else {
                if (data.content.content.includes('\n')) {
                    // Replace newline characters with <br> tags
                    const formattedContent = data.content.content.replace(/\n/g, '<br>');
                    messageContent.innerHTML = formattedContent;
                } else {
                    messageContent.textContent = data.content.content;
                }
            }

            // Append message content to ctext wrap content
            ctextWrapContent.appendChild(messageContent);

            // Create the dropdown div
            const dropdown = document.createElement('div');
            dropdown.className = 'dropdown align-self-start message-box-drop';

            // Create the dropdown toggle link
            const dropdownToggle = document.createElement('a');
            dropdownToggle.className = 'dropdown-toggle';
            dropdownToggle.href = '#';
            dropdownToggle.role = 'button';
            dropdownToggle.dataset.bsToggle = 'dropdown';
            dropdownToggle.ariaHasPopup = 'true';
            dropdownToggle.ariaExpanded = 'false';

            // Create the icon inside the dropdown toggle
            const dropdownIcon = document.createElement('i');
            dropdownIcon.className = 'ri-more-2-fill';

            // Append icon to dropdown toggle
            dropdownToggle.appendChild(dropdownIcon);

            // Create the dropdown menu div
            const dropdownMenu = document.createElement('div');
            dropdownMenu.className = 'dropdown-menu';

            // Helper function to create dropdown items
            function createDropdownItem(href, iconClass, text) {
                const dropdownItem = document.createElement('a');
                dropdownItem.className = 'dropdown-item';
                dropdownItem.href = href;

                const itemIcon = document.createElement('i');
                itemIcon.className = `${iconClass} me-2 text-muted align-bottom`;

                dropdownItem.appendChild(itemIcon);
                dropdownItem.appendChild(document.createTextNode(text));

                return dropdownItem;
            }

            // Create dropdown items
            const replyItem = createDropdownItem('#', 'ri-reply-line', 'Reply');
            replyItem.classList.add('reply-message');
            replyItem.onclick = function() {
                replyEventListener(data);
            };

            // const forwardItem = createDropdownItem('#', 'ri-share-line', 'Forward');
            const copyItem = createDropdownItem('#', 'ri-file-copy-line', 'Copy');
            copyItem.classList.add('copy-message');
            copyItem.onclick = function() {
                copyEventListener(data.content.content);
            };

            // const bookmarkItem = createDropdownItem('#', 'ri-bookmark-line', 'Bookmark');
            // const deleteItem = createDropdownItem('#', 'ri-delete-bin-5-line', 'Delete');
            // deleteItem.classList.add('delete-item');

            // Append dropdown items to dropdown menu
            // dropdownMenu.append(replyItem, forwardItem, copyItem, bookmarkItem, deleteItem);
            dropdownMenu.append(replyItem, copyItem);

            // Append dropdown toggle and dropdown menu to dropdown div
            dropdown.append(dropdownToggle, dropdownMenu);

            // Append ctext wrap content and dropdown to ctext wrap
            ctextWrap.append(ctextWrapContent, dropdown);

        } else if (data.message_type === "image") {

            // Create the message-img div
            const messageImgDiv = document.createElement('div');
            messageImgDiv.className = 'message-img mb-0 d-flex flex-column gap-2 p-2 message-box';

            const imagesPerRow = 2;
            const maxImages = 4; // Maximum number of images to display
            const totalImages = Math.min(data.image_content.content.length, maxImages);
            const numberOfRows = Math.ceil(totalImages / imagesPerRow);
            const remainingImages = data.image_content.content.length - maxImages;
            var count = 0;

            for (let j = 0; j < numberOfRows; j++) {

                const mainDiv = document.createElement('div');
                mainDiv.className = 'd-flex gap-2';

                for (let i = 0; i < imagesPerRow; i++) {

                    const imageIndex = j * imagesPerRow + i;

                    if (imageIndex >= totalImages)
                        break;

                    // Create the message-img-list div
                    const messageImgListDiv = document.createElement('div');
                    messageImgListDiv.className = 'message-img-list';

                    // Create the div for the image
                    const imageDiv = document.createElement('div');

                    // Create the anchor tag for the popup image
                    const anchor = document.createElement('a');
                    anchor.className = 'popup-img d-inline-block';
                    anchor.href = "#";

                    // Create the image element
                    const image = document.createElement('img');
                    image.src = data.image_content.content[count];
                    image.alt = '';
                    image.className = 'fixed-dimension rounded border gallery-item';

                    // Append the image to the anchor tag
                    anchor.appendChild(image);

                    // Overlay for +1
                    if (imageIndex === maxImages - 1 && remainingImages > 0) {
                        const overlay = document.createElement('div');
                        overlay.className = 'extra_img_overlay';
                        overlay.innerText = `+${remainingImages}`;
                        overlay.onclick = overlayEventListener();
                        anchor.appendChild(overlay);
                    }

                    // Append the anchor tag to the image div
                    imageDiv.appendChild(anchor);

                    // Create the message-img-link div
                    const messageImgLinkDiv = document.createElement('div');
                    messageImgLinkDiv.className = 'message-img-link';

                    // Create the ul element
                    const ul = document.createElement('ul');
                    ul.className = 'list-inline mb-0';

                    // Create the li element
                    const li = document.createElement('li');
                    li.className = 'list-inline-item dropdown me-1';

                    // Create the dropdown-toggle anchor tag
                    const dropdownToggle = document.createElement('a');
                    dropdownToggle.className = 'dropdown-toggle';
                    dropdownToggle.href = '#';
                    dropdownToggle.setAttribute('role', 'button');
                    dropdownToggle.setAttribute('data-bs-toggle', 'dropdown');
                    dropdownToggle.setAttribute('aria-haspopup', 'true');
                    dropdownToggle.setAttribute('aria-expanded', 'false');
                    dropdownToggle.innerHTML = '<i class="ri-more-fill"></i>';

                    // Append the dropdown-toggle to the li
                    li.appendChild(dropdownToggle);

                    // Create the dropdown-menu div
                    const dropdownMenu = document.createElement('div');
                    dropdownMenu.className = 'dropdown-menu';

                    // Create the dropdown items
                    const dropdownItems = [{
                            href: data.image_content.content[imageIndex],
                            iconClass: 'ri-download-2-line',
                            text: 'Download'
                        },
                        {
                            iconClass: 'ri-reply-line',
                            text: 'Reply',
                            click: function() {
                                replyEventListener(data);
                            },
                        },
                        // {
                        //     href: '#',
                        //     iconClass: 'ri-share-line',
                        //     text: 'Forward'
                        // },
                        // {
                        //     href: '#',
                        //     iconClass: 'ri-bookmark-line',
                        //     text: 'Bookmark'
                        // },
                        // {
                        //     href: '#',
                        //     iconClass: 'ri-delete-bin-5-line',
                        //     text: 'Delete'
                        // }
                    ];

                    dropdownItems.forEach(item => {
                        const dropdownItem = document.createElement('a');
                        dropdownItem.className = 'dropdown-item';

                        const itemIcon = document.createElement('i');
                        itemIcon.className = `${item.iconClass} me-2 text-muted align-bottom`;

                        if (item.click) {
                            dropdownItem.onclick = item.click;
                            dropdownItem.href = "#";
                        }

                        if (item.href) {
                            dropdownItem.href = item.href;
                        }

                        dropdownItem.appendChild(itemIcon);
                        dropdownItem.appendChild(document.createTextNode(item.text));

                        dropdownMenu.appendChild(dropdownItem);
                    });

                    // Append the dropdown-menu to the li
                    li.appendChild(dropdownMenu);

                    // Append the li to the ul
                    ul.appendChild(li);

                    // Append the ul to the messageImgLinkDiv
                    messageImgLinkDiv.appendChild(ul);

                    // Append the imageDiv and messageImgLinkDiv to the messageImgListDiv
                    messageImgListDiv.append(imageDiv, messageImgLinkDiv);

                    // Append the messageImgListDiv to the mainDiv
                    mainDiv.appendChild(messageImgListDiv);

                    count++;
                }

                // Append the mainDiv to the messageImgDiv
                messageImgDiv.appendChild(mainDiv);
            }
            ctextWrap.appendChild(messageImgDiv);

        } else if (data.message_type === "document") {

            // Create the ctext wrap content div
            const ctextWrapContent = document.createElement('div');
            ctextWrapContent.className = 'ctext-wrap-content p-2 message-box';
            ctextWrapContent.id = data.id;

            // Add mouseenter event listener to change cursor to pointer
            ctextWrapContent.addEventListener("mouseenter", function() {
                ctextWrapContent.style.cursor = "pointer";
            });

            // Add mouseleave event listener to revert cursor to default
            ctextWrapContent.addEventListener("mouseleave", function() {
                ctextWrapContent.style.cursor = "default";
            });

            const messageContent = document.createElement('div');
            messageContent.className = 'cdoc-wrap-content py-3 ps-2 pe-5 mb-1 d-flex';
            messageContent.onclick = function() {
                window.open(data.document_content.content, '_blank');
            }

            var contentIcon = document.createElement('i');
            contentIcon.className = `ri-file-${data.document_content.doc_type}-fill fs-3`;

            messageContent.appendChild(contentIcon);

            var contentSpan = document.createElement('span');
            contentSpan.className = 'd-flex justify-content-center align-items-center ps-2';
            const fullPath = data.document_content.content;
            const filename = fullPath.split('/').pop();
            contentSpan.textContent = filename;

            messageContent.appendChild(contentSpan);

            // Append message content to ctext wrap content
            ctextWrapContent.appendChild(messageContent);

            const contentDetails = document.createElement('div');
            contentDetails.className = 'text-start px-1';

            const spanType = document.createElement('span');
            spanType.textContent = data.document_content.type;

            contentDetails.appendChild(spanType);

            const spanDot = document.createElement('span');
            spanDot.className = 'mx-2',
                spanDot.textContent = '-';

            contentDetails.appendChild(spanDot);

            const spanSize = document.createElement('span');
            spanSize.textContent = data.document_content.size;

            contentDetails.appendChild(spanSize);

            // Append message content to ctext wrap content
            ctextWrapContent.appendChild(contentDetails);

            // Create the dropdown div
            const dropdown = document.createElement('div');
            dropdown.className = 'dropdown align-self-start message-box-drop';

            // Create the dropdown toggle link
            const dropdownToggle = document.createElement('a');
            dropdownToggle.className = 'dropdown-toggle';
            dropdownToggle.href = '#';
            dropdownToggle.role = 'button';
            dropdownToggle.dataset.bsToggle = 'dropdown';
            dropdownToggle.ariaHasPopup = 'true';
            dropdownToggle.ariaExpanded = 'false';

            // Create the icon inside the dropdown toggle
            const dropdownIcon = document.createElement('i');
            dropdownIcon.className = 'ri-more-2-fill';

            // Append icon to dropdown toggle
            dropdownToggle.appendChild(dropdownIcon);

            // Create the dropdown menu div
            const dropdownMenu = document.createElement('div');
            dropdownMenu.className = 'dropdown-menu';

            // Helper function to create dropdown items
            function createDropdownItem(href, iconClass, text) {
                const dropdownItem = document.createElement('a');
                dropdownItem.className = 'dropdown-item';
                dropdownItem.href = href;

                const itemIcon = document.createElement('i');
                itemIcon.className = `${iconClass} me-2 text-muted align-bottom`;

                dropdownItem.appendChild(itemIcon);
                dropdownItem.appendChild(document.createTextNode(text));

                return dropdownItem;
            }

            // Create dropdown items
            const replyItem = createDropdownItem('#', 'ri-reply-line', 'Reply');
            replyItem.classList.add('reply-message');
            replyItem.onclick = function() {
                replyEventListener(data);
            };

            // const forwardItem = createDropdownItem('#', 'ri-share-line', 'Forward');
            const copyItem = createDropdownItem('#', 'ri-file-copy-line', 'Copy');
            copyItem.classList.add('copy-message');

            // const bookmarkItem = createDropdownItem('#', 'ri-bookmark-line', 'Bookmark');
            // const deleteItem = createDropdownItem('#', 'ri-delete-bin-5-line', 'Delete');
            // deleteItem.classList.add('delete-item');

            // Append dropdown items to dropdown menu
            // dropdownMenu.append(replyItem, forwardItem, copyItem, bookmarkItem, deleteItem);s

            dropdownMenu.append(replyItem);


            // Append dropdown toggle and dropdown menu to dropdown div
            dropdown.append(dropdownToggle, dropdownMenu);

            // Append ctext wrap content and dropdown to ctext wrap
            ctextWrap.append(ctextWrapContent, dropdown);

        } else if (data.message_type === "video") {

            // Create the ctext wrap content div
            const ctextWrapContent = document.createElement('div');
            ctextWrapContent.className = 'ctext-wrap-content p-2 message-box';
            ctextWrapContent.id = data.id;

            // Add mouseenter event listener to change cursor to pointer
            ctextWrapContent.addEventListener("mouseenter", function() {
                ctextWrapContent.style.cursor = "pointer";
            });

            // Add mouseleave event listener to revert cursor to default
            ctextWrapContent.addEventListener("mouseleave", function() {
                ctextWrapContent.style.cursor = "default";
            });

            const messageContent = document.createElement('div');
            messageContent.className = 'cdoc-wrap-content py-3 ps-2 pe-5 mb-1 d-flex';
            messageContent.onclick = function() {
                window.open(data.video_content.content, '_blank');
            }

            var contentIcon = document.createElement('i');
            contentIcon.className = `ri-video-fill fs-3`;

            messageContent.appendChild(contentIcon);

            var contentSpan = document.createElement('span');
            contentSpan.className = 'd-flex justify-content-center align-items-center ps-2';
            const fullPath = data.video_content.content[0];
            const filename = fullPath.split('/').pop();
            contentSpan.textContent = filename;

            messageContent.appendChild(contentSpan);

            // Append message content to ctext wrap content
            ctextWrapContent.appendChild(messageContent);

            const contentDetails = document.createElement('div');
            contentDetails.className = 'text-start px-1';

            const spanType = document.createElement('span');
            spanType.textContent = data.video_content.type[0];

            contentDetails.appendChild(spanType);

            const spanDot = document.createElement('span');
            spanDot.className = 'mx-2',
                spanDot.textContent = '-';

            contentDetails.appendChild(spanDot);

            const spanSize = document.createElement('span');
            spanSize.textContent = data.video_content.size[0];

            contentDetails.appendChild(spanSize);

            // Append message content to ctext wrap content
            ctextWrapContent.appendChild(contentDetails);

            // Create the dropdown div
            const dropdown = document.createElement('div');
            dropdown.className = 'dropdown align-self-start message-box-drop';

            // Create the dropdown toggle link
            const dropdownToggle = document.createElement('a');
            dropdownToggle.className = 'dropdown-toggle';
            dropdownToggle.href = '#';
            dropdownToggle.role = 'button';
            dropdownToggle.dataset.bsToggle = 'dropdown';
            dropdownToggle.ariaHasPopup = 'true';
            dropdownToggle.ariaExpanded = 'false';

            // Create the icon inside the dropdown toggle
            const dropdownIcon = document.createElement('i');
            dropdownIcon.className = 'ri-more-2-fill';

            // Append icon to dropdown toggle
            dropdownToggle.appendChild(dropdownIcon);

            // Create the dropdown menu div
            const dropdownMenu = document.createElement('div');
            dropdownMenu.className = 'dropdown-menu';

            // Helper function to create dropdown items
            function createDropdownItem(href, iconClass, text) {
                const dropdownItem = document.createElement('a');
                dropdownItem.className = 'dropdown-item';
                dropdownItem.href = href;

                const itemIcon = document.createElement('i');
                itemIcon.className = `${iconClass} me-2 text-muted align-bottom`;

                dropdownItem.appendChild(itemIcon);
                dropdownItem.appendChild(document.createTextNode(text));

                return dropdownItem;
            }

            // Create dropdown items
            const replyItem = createDropdownItem('#', 'ri-reply-line', 'Reply');
            replyItem.classList.add('reply-message');
            replyItem.onclick = function() {
                replyEventListener(data);
            };

            // const forwardItem = createDropdownItem('#', 'ri-share-line', 'Forward');
            const copyItem = createDropdownItem('#', 'ri-file-copy-line', 'Copy');
            copyItem.classList.add('copy-message');

            // const bookmarkItem = createDropdownItem('#', 'ri-bookmark-line', 'Bookmark');
            // const deleteItem = createDropdownItem('#', 'ri-delete-bin-5-line', 'Delete');
            // deleteItem.classList.add('delete-item');

            // Append dropdown items to dropdown menu
            // dropdownMenu.append(replyItem, forwardItem, copyItem, bookmarkItem, deleteItem);s

            dropdownMenu.append(replyItem);


            // Append dropdown toggle and dropdown menu to dropdown div
            dropdown.append(dropdownToggle, dropdownMenu);

            // Append ctext wrap content and dropdown to ctext wrap
            ctextWrap.append(ctextWrapContent, dropdown);

        }

        // Create the conversation name div
        const conversationName = document.createElement('div');
        conversationName.className = 'conversation-name';

        // Format the created_at timestamp to only show time in HH:MM
        var createdAt;

        if (data.message_type === "text") {
            createdAt = new Date(data.content.created_at);
        } else if (data.message_type === "image") {
            createdAt = new Date(data.image_content.created_at);
        } else if (data.message_type === "video") {
            createdAt = new Date(data.video_content.created_at);
        } else if (data.message_type === "document") {
            createdAt = new Date(data.document_content.created_at);
        }

        let hours = createdAt.getHours();
        const minutes = createdAt.getMinutes().toString().padStart(2, '0');
        const seconds = createdAt.getSeconds().toString().padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        const formattedHours = hours.toString().padStart(2, '0');
        const formattedTime = `${formattedHours}:${minutes} ${ampm}`;
        const full_formattedTime = `${formattedHours}:${minutes}:${seconds} ${ampm}`;

        // Create the time element
        const timeElement = document.createElement('small');
        timeElement.className = 'text-muted time';
        timeElement.textContent = formattedTime; // Replace with formatted time

        // Create hidden full time element
        const full_time = document.createElement('small');
        full_time.className = 'd-none text-muted time';
        full_time.textContent = full_formattedTime; // Replace with full_formatted time

        // Create the check message icon span
        const checkMessageIcon = document.createElement('span');
        checkMessageIcon.className = 'text-success check-message-icon';

        const checkIcon = document.createElement('i');
        checkIcon.id = `message-status-id-${data.id}`;
        if (data.view_status === 0) {
            checkIcon.className = 'bx bx-check';
        } else {
            checkIcon.className = 'bx bx-check-double';
        }

        checkMessageIcon.appendChild(checkIcon);

        // Append name, time, and check message icon to conversation name div
        conversationName.append(full_time, timeElement, checkMessageIcon);

        // Append ctext wrap and conversation name to user chat content
        userChatContent.append(ctextWrap, conversationName);

        if (data.type != "sent") {
            // Append chat avatar and user chat content to conversation list
            conversationList.append(chatAvatar, userChatContent);

        } else {

            // Append chat avatar and user chat content to conversation list
            conversationList.append(userChatContent);
        }

        // Append conversation list to list item
        listItem.appendChild(conversationList);

        // Assuming you have a parent element to append this list item to
        const parentElement = document.getElementById('users-conversation');
        parentElement.appendChild(listItem);

        setTimeout(scrollToBottom, 100);
    }

    function overlayEventListener() {

        //implementation left

        const galleryItems = document.querySelectorAll('.gallery-item');

        imageClickHandler();
    }

    // {{-- creating reply message card for conversation list element when contact is clicked --}}
    function replyMessageCard(data) {

        // Create the main list item
        const listItem = document.createElement('li');
        listItem.className = `chat-list ${data.type === 'sent' ? 'right' : 'left'}`;
        listItem.id = `message-id-${data.id}`;

        // Create the conversation list div
        const conversationList = document.createElement('div');
        conversationList.className = 'conversation-list';

        var chatAvatar;

        if (data.type != "sent") {
            // Create the chat avatar div
            chatAvatar = document.createElement('div');
            chatAvatar.className = 'chat-avatar';

            // Create the img element
            const img = document.createElement('img');
            img.src = data.sender.image;
            img.alt = '';

            // Append img to chat avatar
            chatAvatar.appendChild(img);

        }

        // Create the user chat content div
        const userChatContent = document.createElement('div');
        userChatContent.className = 'user-chat-content';

        // Create the ctext wrap div
        const ctextWrap = document.createElement('div');
        ctextWrap.className = 'ctext-wrap';

        // Create the paragraph for message content or image element for image content
        if (data.reply_details.message_type === "text") {
            // Create the ctext wrap content div
            const ctextWrapContent = document.createElement('div');
            ctextWrapContent.className = 'ctext-wrap-content p-0 message-box';
            ctextWrapContent.id = data.id;

            const containerDiv = document.createElement('div');
            containerDiv.classList.add('container');

            // Create the row div
            const rowDiv = document.createElement('div');
            rowDiv.classList.add('row');

            // Create the col-md-12 div
            const colDiv = document.createElement('div');
            colDiv.classList.add('col-md-12', 'p-0');

            // Create the sent-main-box div
            const mainBoxDiv = document.createElement('div');
            if (data.type === "sent") {
                mainBoxDiv.classList.add('sent-main-box');
            } else {
                mainBoxDiv.classList.add('received-main-box');
            }

            // Create the box div
            const boxDiv = document.createElement('div');
            boxDiv.onclick = function() {
                replyScrollEvent(`message-id-${data.reply_details.id}`);
            }

            // Add mouseenter event listener to change cursor to pointer
            boxDiv.addEventListener("mouseenter", function() {
                boxDiv.style.cursor = "pointer";
            });

            // Add mouseleave event listener to revert cursor to default
            boxDiv.addEventListener("mouseleave", function() {
                boxDiv.style.cursor = "default";
            });

            if (data.type === "sent") {
                boxDiv.classList.add('box', 'text-sm-end', 'ps-5', 'pe-3', 'py-2');
            } else {
                boxDiv.classList.add('box', 'text-sm-end', 'ps-3', 'pe-5', 'py-2');
            }

            // Create the h5 element for the name
            const nameH5 = document.createElement('h5');
            nameH5.style.color = '#3cd188';
            nameH5.style.fontSize = '14px';

            if (data.reply_details.from_staff_id === data.current_user) {
                nameH5.classList.add('conversation-name', 'text-sm-end', 'mb-0');
                nameH5.textContent = 'You';
            } else {
                nameH5.classList.add('conversation-name', 'text-sm-start', 'mb-0');
                nameH5.textContent = data.receiver.name;
            }

            // Create the p element for the message
            const messageP = document.createElement('p');
            messageP.style.color = '#000000';
            messageP.textContent = data.reply_details.content.content;
            if (data.type === "sent") {
                messageP.classList.add('text-sm-end', 'pt-2', 'mb-0');
            } else {
                messageP.classList.add('text-sm-start', 'pt-2', 'mb-0');
            }

            // Append the h5 and p to the box div
            boxDiv.appendChild(nameH5);
            boxDiv.appendChild(messageP);

            // Append the box div to the sent-main-box div
            mainBoxDiv.appendChild(boxDiv);

            // Create the second h5 element for the message
            const messageH5 = document.createElement('h5');
            messageH5.classList.add('conversation-name', 'text-sm-end', 'pt-3', 'mb-0', 'pe-1');
            messageH5.style.fontSize = '14px';
            messageH5.textContent = data.content.content;
            if (data.type === "sent") {
                messageH5.style.color = '#ff7f5d';
            } else {
                messageH5.style.color = '#000000';
            }

            // Append the second h5 to the sent-main-box div
            mainBoxDiv.appendChild(messageH5);

            // Append the sent-main-box div to the col-md-12 div
            colDiv.appendChild(mainBoxDiv);

            // Append the col-md-12 div to the row div
            rowDiv.appendChild(colDiv);

            // Append the row div to the container div
            containerDiv.appendChild(rowDiv)

            // Append message content to ctext wrap content
            ctextWrapContent.appendChild(containerDiv);

            // Create the dropdown div
            const dropdown = document.createElement('div');
            dropdown.className = 'dropdown align-self-start message-box-drop';

            // Create the dropdown toggle link
            const dropdownToggle = document.createElement('a');
            dropdownToggle.className = 'dropdown-toggle';
            dropdownToggle.href = '#';
            dropdownToggle.role = 'button';
            dropdownToggle.dataset.bsToggle = 'dropdown';
            dropdownToggle.ariaHasPopup = 'true';
            dropdownToggle.ariaExpanded = 'false';

            // Create the icon inside the dropdown toggle
            const dropdownIcon = document.createElement('i');
            dropdownIcon.className = 'ri-more-2-fill';

            // Append icon to dropdown toggle
            dropdownToggle.appendChild(dropdownIcon);

            // Create the dropdown menu div
            const dropdownMenu = document.createElement('div');
            dropdownMenu.className = 'dropdown-menu';

            // Helper function to create dropdown items
            function createDropdownItem(href, iconClass, text) {
                const dropdownItem = document.createElement('a');
                dropdownItem.className = 'dropdown-item';
                dropdownItem.href = href;

                const itemIcon = document.createElement('i');
                itemIcon.className = `${iconClass} me-2 text-muted align-bottom`;

                dropdownItem.appendChild(itemIcon);
                dropdownItem.appendChild(document.createTextNode(text));

                return dropdownItem;
            }

            // Create dropdown items
            const replyItem = createDropdownItem('#', 'ri-reply-line', 'Reply');
            replyItem.classList.add('reply-message');
            replyItem.onclick = function() {
                replyEventListener(data);
            };

            // const forwardItem = createDropdownItem('#', 'ri-share-line', 'Forward');
            const copyItem = createDropdownItem('#', 'ri-file-copy-line', 'Copy');
            copyItem.classList.add('copy-message');
            copyItem.onclick = function() {
                copyEventListener(data.content.content);
            };

            // const bookmarkItem = createDropdownItem('#', 'ri-bookmark-line', 'Bookmark');
            // const deleteItem = createDropdownItem('#', 'ri-delete-bin-5-line', 'Delete');
            // deleteItem.classList.add('delete-item');

            // Append dropdown items to dropdown menu
            // dropdownMenu.append(replyItem, forwardItem, copyItem, bookmarkItem, deleteItem);
            dropdownMenu.append(replyItem, copyItem);

            // Append dropdown toggle and dropdown menu to dropdown div
            dropdown.append(dropdownToggle, dropdownMenu);

            // Append ctext wrap content and dropdown to ctext wrap
            ctextWrap.append(ctextWrapContent, dropdown);

        } else if (data.reply_details.message_type === "image") {

            const ctextWrapContent = document.createElement('div');
            ctextWrapContent.className = 'ctext-wrap-content p-0 message-box';
            ctextWrapContent.id = data.id;

            const containerDiv = document.createElement('div');
            containerDiv.classList.add('container');

            // Create the row div
            const rowDiv = document.createElement('div');
            rowDiv.classList.add('row');

            // Create the col-md-12 div
            const colDiv = document.createElement('div');
            colDiv.classList.add('col-md-12', 'p-0');

            // Create the sent-main-box div
            const mainBoxDiv = document.createElement('div');
            if (data.type === "sent") {
                mainBoxDiv.classList.add('sent-main-box');
            } else {
                mainBoxDiv.classList.add('received-main-box');
            }

            // Create the box div
            const boxDiv = document.createElement('div');
            boxDiv.onclick = function() {
                replyScrollEvent(`message-id-${data.reply_details.id}`);
            }

            // Add mouseenter event listener to change cursor to pointer
            boxDiv.addEventListener("mouseenter", function() {
                boxDiv.style.cursor = "pointer";
            });

            // Add mouseleave event listener to revert cursor to default
            boxDiv.addEventListener("mouseleave", function() {
                boxDiv.style.cursor = "default";
            });

            if (data.type === "sent") {
                boxDiv.classList.add('box', 'text-sm-end', 'ps-5', 'pe-3', 'py-2');
            } else {
                boxDiv.classList.add('box', 'text-sm-end', 'ps-3', 'pe-5', 'py-2');
            }

            // Create the h5 element for the name
            const nameH5 = document.createElement('h5');
            nameH5.style.color = '#3cd188';
            nameH5.style.fontSize = '14px';

            if (data.reply_details.from_staff_id === data.current_user) {
                nameH5.classList.add('conversation-name', 'text-sm-end', 'mb-0');
                nameH5.textContent = 'You';
            } else {
                nameH5.classList.add('conversation-name', 'text-sm-start', 'mb-0');
                nameH5.textContent = data.receiver.name;
            }

            // Create the p element for the message
            var imgElement = document.createElement("img");
            imgElement.src = data.reply_details.image_content.content;
            imgElement.alt = "";
            imgElement.width = "100";
            if (data.type === "sent") {
                imgElement.classList.add("text-sm-end", "py-2", "ps-4");
            } else {
                imgElement.classList.add("text-sm-end", "py-2", "pe-4");
            }

            // Append the h5 and p to the box div
            boxDiv.appendChild(nameH5);
            boxDiv.appendChild(imgElement);

            // Append the box div to the sent-main-box div
            mainBoxDiv.appendChild(boxDiv);

            // Create the second h5 element for the message
            const messageH5 = document.createElement('h5');
            messageH5.classList.add('conversation-name', 'text-sm-end', 'pt-3', 'mb-0', 'pe-1');
            messageH5.style.fontSize = '14px';
            messageH5.textContent = data.content.content;
            if (data.type === "sent") {
                messageH5.style.color = '#ff7f5d';
            } else {
                messageH5.style.color = '#000000';
            }

            // Append the second h5 to the sent-main-box div
            mainBoxDiv.appendChild(messageH5);

            // Append the sent-main-box div to the col-md-12 div
            colDiv.appendChild(mainBoxDiv);

            // Append the col-md-12 div to the row div
            rowDiv.appendChild(colDiv);

            // Append the row div to the container div
            containerDiv.appendChild(rowDiv)

            // Append message content to ctext wrap content
            ctextWrapContent.appendChild(containerDiv);

            // Create the dropdown div
            const dropdown = document.createElement('div');
            dropdown.className = 'dropdown align-self-start message-box-drop';

            // Create the dropdown toggle link
            const dropdownToggle = document.createElement('a');
            dropdownToggle.className = 'dropdown-toggle';
            dropdownToggle.href = '#';
            dropdownToggle.role = 'button';
            dropdownToggle.dataset.bsToggle = 'dropdown';
            dropdownToggle.ariaHasPopup = 'true';
            dropdownToggle.ariaExpanded = 'false';

            // Create the icon inside the dropdown toggle
            const dropdownIcon = document.createElement('i');
            dropdownIcon.className = 'ri-more-2-fill';

            // Append icon to dropdown toggle
            dropdownToggle.appendChild(dropdownIcon);

            // Create the dropdown menu div
            const dropdownMenu = document.createElement('div');
            dropdownMenu.className = 'dropdown-menu';

            // Helper function to create dropdown items
            function createDropdownItem(href, iconClass, text) {
                const dropdownItem = document.createElement('a');
                dropdownItem.className = 'dropdown-item';
                dropdownItem.href = href;

                const itemIcon = document.createElement('i');
                itemIcon.className = `${iconClass} me-2 text-muted align-bottom`;

                dropdownItem.appendChild(itemIcon);
                dropdownItem.appendChild(document.createTextNode(text));

                return dropdownItem;
            }

            // Create dropdown items
            const replyItem = createDropdownItem('#', 'ri-reply-line', 'Reply');
            replyItem.classList.add('reply-message');
            replyItem.onclick = function() {
                replyEventListener(data);
            };

            // const forwardItem = createDropdownItem('#', 'ri-share-line', 'Forward');
            const copyItem = createDropdownItem('#', 'ri-file-copy-line', 'Copy');
            copyItem.classList.add('copy-message');
            copyItem.onclick = function() {
                copyEventListener(data.content.content);
            };

            // const bookmarkItem = createDropdownItem('#', 'ri-bookmark-line', 'Bookmark');
            // const deleteItem = createDropdownItem('#', 'ri-delete-bin-5-line', 'Delete');
            // deleteItem.classList.add('delete-item');

            // Append dropdown items to dropdown menu
            // dropdownMenu.append(replyItem, forwardItem, copyItem, bookmarkItem, deleteItem);
            dropdownMenu.append(replyItem, copyItem);

            // Append dropdown toggle and dropdown menu to dropdown div
            dropdown.append(dropdownToggle, dropdownMenu);

            // Append ctext wrap content and dropdown to ctext wrap
            ctextWrap.append(ctextWrapContent, dropdown);

        } else if (data.reply_details.message_type === "video") {

            const ctextWrapContent = document.createElement('div');
            ctextWrapContent.className = 'ctext-wrap-content p-0 message-box';
            ctextWrapContent.id = data.id;

            const containerDiv = document.createElement('div');
            containerDiv.classList.add('container');

            // Create the row div
            const rowDiv = document.createElement('div');
            rowDiv.classList.add('row');

            // Create the col-md-12 div
            const colDiv = document.createElement('div');
            colDiv.classList.add('col-md-12', 'p-0');

            // Create the sent-main-box div
            const mainBoxDiv = document.createElement('div');
            if (data.type === "sent") {
                mainBoxDiv.classList.add('sent-main-box');
            } else {
                mainBoxDiv.classList.add('received-main-box');
            }

            // Create the box div
            const boxDiv = document.createElement('div');
            boxDiv.onclick = function() {
                replyScrollEvent(`message-id-${data.reply_details.id}`);
            }

            // Add mouseenter event listener to change cursor to pointer
            boxDiv.addEventListener("mouseenter", function() {
                boxDiv.style.cursor = "pointer";
            });

            // Add mouseleave event listener to revert cursor to default
            boxDiv.addEventListener("mouseleave", function() {
                boxDiv.style.cursor = "default";
            });

            if (data.type === "sent") {
                boxDiv.classList.add('box', 'text-sm-end', 'ps-3', 'pe-3', 'py-2');
            } else {
                boxDiv.classList.add('box', 'text-sm-end', 'ps-3', 'pe-5', 'py-2');
            }

            // Create the h5 element for the name
            const nameH5 = document.createElement('h5');
            nameH5.style.color = '#3cd188';
            nameH5.style.fontSize = '14px';

            if (data.reply_details.from_staff_id === data.current_user) {
                nameH5.classList.add('conversation-name', 'text-sm-end', 'mb-0');
                nameH5.textContent = 'You';
            } else {
                nameH5.classList.add('conversation-name', 'text-sm-start', 'mb-0');
                nameH5.textContent = data.receiver.name;
            }

            // Create div element
            var divElement = document.createElement("div");
            divElement.classList.add("py-2", "mb-1", "d-flex", "justify-content-end", "text-black");

            // Create icon element
            var iconElement = document.createElement("i");
            iconElement.classList.add("ri-video-fill", "fs-3");

            // Create span element
            var spanElement = document.createElement("span");
            spanElement.classList.add("d-flex", "justify-content-center", "align-items-center", "ps-2");
            const fullPath = data.reply_details.video_content.content[0];
            const filename = fullPath.split('/').pop();
            spanElement.textContent = filename;

            // Append icon and span elements to the div
            divElement.appendChild(iconElement);
            divElement.appendChild(spanElement);

            // Append the h5 and p to the box div
            boxDiv.appendChild(nameH5);
            boxDiv.appendChild(divElement);

            // Append the box div to the sent-main-box div
            mainBoxDiv.appendChild(boxDiv);

            // Create the second h5 element for the message
            const messageH5 = document.createElement('h5');
            messageH5.classList.add('conversation-name', 'text-sm-end', 'pt-3', 'mb-0', 'pe-1');
            messageH5.style.fontSize = '14px';
            messageH5.textContent = data.content.content;
            if (data.type === "sent") {
                messageH5.style.color = '#ff7f5d';
            } else {
                messageH5.style.color = '#000000';
            }

            // Append the second h5 to the sent-main-box div
            mainBoxDiv.appendChild(messageH5);

            // Append the sent-main-box div to the col-md-12 div
            colDiv.appendChild(mainBoxDiv);

            // Append the col-md-12 div to the row div
            rowDiv.appendChild(colDiv);

            // Append the row div to the container div
            containerDiv.appendChild(rowDiv)

            // Append message content to ctext wrap content
            ctextWrapContent.appendChild(containerDiv);

            // Create the dropdown div
            const dropdown = document.createElement('div');
            dropdown.className = 'dropdown align-self-start message-box-drop';

            // Create the dropdown toggle link
            const dropdownToggle = document.createElement('a');
            dropdownToggle.className = 'dropdown-toggle';
            dropdownToggle.href = '#';
            dropdownToggle.role = 'button';
            dropdownToggle.dataset.bsToggle = 'dropdown';
            dropdownToggle.ariaHasPopup = 'true';
            dropdownToggle.ariaExpanded = 'false';

            // Create the icon inside the dropdown toggle
            const dropdownIcon = document.createElement('i');
            dropdownIcon.className = 'ri-more-2-fill';

            // Append icon to dropdown toggle
            dropdownToggle.appendChild(dropdownIcon);

            // Create the dropdown menu div
            const dropdownMenu = document.createElement('div');
            dropdownMenu.className = 'dropdown-menu';

            // Helper function to create dropdown items
            function createDropdownItem(href, iconClass, text) {
                const dropdownItem = document.createElement('a');
                dropdownItem.className = 'dropdown-item';
                dropdownItem.href = href;

                const itemIcon = document.createElement('i');
                itemIcon.className = `${iconClass} me-2 text-muted align-bottom`;

                dropdownItem.appendChild(itemIcon);
                dropdownItem.appendChild(document.createTextNode(text));

                return dropdownItem;
            }

            // Create dropdown items
            const replyItem = createDropdownItem('#', 'ri-reply-line', 'Reply');
            replyItem.classList.add('reply-message');
            replyItem.onclick = function() {
                replyEventListener(data);
            };

            // const forwardItem = createDropdownItem('#', 'ri-share-line', 'Forward');
            const copyItem = createDropdownItem('#', 'ri-file-copy-line', 'Copy');
            copyItem.classList.add('copy-message');
            copyItem.onclick = function() {
                copyEventListener(data.content.content);
            };

            // const bookmarkItem = createDropdownItem('#', 'ri-bookmark-line', 'Bookmark');
            // const deleteItem = createDropdownItem('#', 'ri-delete-bin-5-line', 'Delete');
            // deleteItem.classList.add('delete-item');

            // Append dropdown items to dropdown menu
            // dropdownMenu.append(replyItem, forwardItem, copyItem, bookmarkItem, deleteItem);
            dropdownMenu.append(replyItem, copyItem);

            // Append dropdown toggle and dropdown menu to dropdown div
            dropdown.append(dropdownToggle, dropdownMenu);

            // Append ctext wrap content and dropdown to ctext wrap
            ctextWrap.append(ctextWrapContent, dropdown);

        } else if (data.reply_details.message_type === "document") {

            const ctextWrapContent = document.createElement('div');
            ctextWrapContent.className = 'ctext-wrap-content p-0 message-box';
            ctextWrapContent.id = data.id;

            const containerDiv = document.createElement('div');
            containerDiv.classList.add('container');

            // Create the row div
            const rowDiv = document.createElement('div');
            rowDiv.classList.add('row');

            // Create the col-md-12 div
            const colDiv = document.createElement('div');
            colDiv.classList.add('col-md-12', 'p-0');

            // Create the sent-main-box div
            const mainBoxDiv = document.createElement('div');
            if (data.type === "sent") {
                mainBoxDiv.classList.add('sent-main-box');
            } else {
                mainBoxDiv.classList.add('received-main-box');
            }

            // Create the box div
            const boxDiv = document.createElement('div');
            boxDiv.onclick = function() {
                replyScrollEvent(`message-id-${data.reply_details.id}`);
            }

            // Add mouseenter event listener to change cursor to pointer
            boxDiv.addEventListener("mouseenter", function() {
                boxDiv.style.cursor = "pointer";
            });

            // Add mouseleave event listener to revert cursor to default
            boxDiv.addEventListener("mouseleave", function() {
                boxDiv.style.cursor = "default";
            });

            if (data.type === "sent") {
                boxDiv.classList.add('box', 'text-sm-end', 'ps-3', 'pe-3', 'py-2');
            } else {
                boxDiv.classList.add('box', 'text-sm-end', 'ps-3', 'pe-5', 'py-2');
            }

            // Create the h5 element for the name
            const nameH5 = document.createElement('h5');
            nameH5.style.color = '#3cd188';
            nameH5.style.fontSize = '14px';

            if (data.reply_details.from_staff_id === data.current_user) {
                nameH5.classList.add('conversation-name', 'text-sm-end', 'mb-0');
                nameH5.textContent = 'You';
            } else {
                nameH5.classList.add('conversation-name', 'text-sm-start', 'mb-0');
                nameH5.textContent = data.receiver.name;
            }

            // Create div element
            var divElement = document.createElement("div");
            divElement.classList.add("py-2", "mb-1", "d-flex", "justify-content-end", "text-black");

            // Create icon element
            var iconElement = document.createElement("i");
            iconElement.classList.add("ri-file-word-fill", "fs-3");

            // Create span element
            var spanElement = document.createElement("span");
            spanElement.classList.add("d-flex", "justify-content-center", "align-items-center", "ps-2");
            const fullPath = data.reply_details.document_content.content;
            const filename = fullPath.split('/').pop();
            spanElement.textContent = filename;

            // Append icon and span elements to the div
            divElement.appendChild(iconElement);
            divElement.appendChild(spanElement);

            // Append the h5 and p to the box div
            boxDiv.appendChild(nameH5);
            boxDiv.appendChild(divElement);

            // Append the box div to the sent-main-box div
            mainBoxDiv.appendChild(boxDiv);

            // Create the second h5 element for the message
            const messageH5 = document.createElement('h5');
            messageH5.classList.add('conversation-name', 'text-sm-end', 'pt-3', 'mb-0', 'pe-1');
            messageH5.style.fontSize = '14px';
            messageH5.textContent = data.content.content;
            if (data.type === "sent") {
                messageH5.style.color = '#ff7f5d';
            } else {
                messageH5.style.color = '#000000';
            }

            // Append the second h5 to the sent-main-box div
            mainBoxDiv.appendChild(messageH5);

            // Append the sent-main-box div to the col-md-12 div
            colDiv.appendChild(mainBoxDiv);

            // Append the col-md-12 div to the row div
            rowDiv.appendChild(colDiv);

            // Append the row div to the container div
            containerDiv.appendChild(rowDiv)

            // Append message content to ctext wrap content
            ctextWrapContent.appendChild(containerDiv);

            // Create the dropdown div
            const dropdown = document.createElement('div');
            dropdown.className = 'dropdown align-self-start message-box-drop';

            // Create the dropdown toggle link
            const dropdownToggle = document.createElement('a');
            dropdownToggle.className = 'dropdown-toggle';
            dropdownToggle.href = '#';
            dropdownToggle.role = 'button';
            dropdownToggle.dataset.bsToggle = 'dropdown';
            dropdownToggle.ariaHasPopup = 'true';
            dropdownToggle.ariaExpanded = 'false';

            // Create the icon inside the dropdown toggle
            const dropdownIcon = document.createElement('i');
            dropdownIcon.className = 'ri-more-2-fill';

            // Append icon to dropdown toggle
            dropdownToggle.appendChild(dropdownIcon);

            // Create the dropdown menu div
            const dropdownMenu = document.createElement('div');
            dropdownMenu.className = 'dropdown-menu';

            // Helper function to create dropdown items
            function createDropdownItem(href, iconClass, text) {
                const dropdownItem = document.createElement('a');
                dropdownItem.className = 'dropdown-item';
                dropdownItem.href = href;

                const itemIcon = document.createElement('i');
                itemIcon.className = `${iconClass} me-2 text-muted align-bottom`;

                dropdownItem.appendChild(itemIcon);
                dropdownItem.appendChild(document.createTextNode(text));

                return dropdownItem;
            }

            // Create dropdown items
            const replyItem = createDropdownItem('#', 'ri-reply-line', 'Reply');
            replyItem.classList.add('reply-message');
            replyItem.onclick = function() {
                replyEventListener(data);
            };

            // const forwardItem = createDropdownItem('#', 'ri-share-line', 'Forward');
            const copyItem = createDropdownItem('#', 'ri-file-copy-line', 'Copy');
            copyItem.classList.add('copy-message');
            copyItem.onclick = function() {
                copyEventListener(data.content.content);
            };

            // const bookmarkItem = createDropdownItem('#', 'ri-bookmark-line', 'Bookmark');
            // const deleteItem = createDropdownItem('#', 'ri-delete-bin-5-line', 'Delete');
            // deleteItem.classList.add('delete-item');

            // Append dropdown items to dropdown menu
            // dropdownMenu.append(replyItem, forwardItem, copyItem, bookmarkItem, deleteItem);
            dropdownMenu.append(replyItem, copyItem);

            // Append dropdown toggle and dropdown menu to dropdown div
            dropdown.append(dropdownToggle, dropdownMenu);

            // Append ctext wrap content and dropdown to ctext wrap
            ctextWrap.append(ctextWrapContent, dropdown);

        }

        // Create the conversation name div
        const conversationName = document.createElement('div');
        conversationName.className = 'conversation-name';

        // Format the created_at timestamp to only show time in HH:MM
        var createdAt;

        if (data.message_type === "text") {
            createdAt = new Date(data.content.created_at);
        } else if (data.message_type === "image") {
            createdAt = new Date(data.image_content.created_at);
        } else if (data.message_type === "document") {
            createdAt = new Date(data.document_content.created_at);
        }

        let hours = createdAt.getHours();
        const minutes = createdAt.getMinutes().toString().padStart(2, '0');
        const seconds = createdAt.getSeconds().toString().padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        const formattedHours = hours.toString().padStart(2, '0');
        const formattedTime = `${formattedHours}:${minutes} ${ampm}`;
        const full_formattedTime = `${formattedHours}:${minutes}:${seconds} ${ampm}`;

        // Create the time element
        const timeElement = document.createElement('small');
        timeElement.className = 'text-muted time';
        timeElement.textContent = formattedTime; // Replace with formatted time

        // Create hidden full time element
        const full_time = document.createElement('small');
        full_time.className = 'd-none text-muted time';
        full_time.textContent = full_formattedTime;

        // Create the check message icon span
        const checkMessageIcon = document.createElement('span');
        checkMessageIcon.className = 'text-success check-message-icon';

        const checkIcon = document.createElement('i');
        checkIcon.id = `message-status-id-${data.id}`;
        if (data.view_status === 0) {
            checkIcon.className = 'bx bx-check';
        } else {
            checkIcon.className = 'bx bx-check-double';
        }

        checkMessageIcon.appendChild(checkIcon);

        // Append name, time, and check message icon to conversation name div
        conversationName.append(full_time, timeElement, checkMessageIcon);

        // Append ctext wrap and conversation name to user chat content
        userChatContent.append(ctextWrap, conversationName);

        if (data.type != "sent") {
            // Append chat avatar and user chat content to conversation list
            conversationList.append(chatAvatar, userChatContent);

        } else {

            // Append chat avatar and user chat content to conversation list
            conversationList.append(userChatContent);
        }

        // Append conversation list to list item
        listItem.appendChild(conversationList);

        // Assuming you have a parent element to append this list item to
        const parentElement = document.getElementById('users-conversation');
        parentElement.appendChild(listItem);

        setTimeout(scrollToBottom, 100);

    }

    function replyEventListener(data) {

        var replyCard = document.querySelector("#replyCard");
        var conversationName = replyCard.querySelector(".conversation-name");
        var replyContent = replyCard.querySelector(".reply-content");
        var replyId = replyCard.querySelector("#reply_id");
        var inputField = document.querySelector("#chat-input");

        replyId.value = data.id;
        inputField.focus();

        closeReplyContainer(replyCard);

        conversationName.innerHTML = "";
        replyContent.innerHTML = "";

        if (replyCard.classList.contains("d-none")) {

            if (data.content) {
                const pTag = document.createElement('p');
                pTag.className = 'mb-0';
                pTag.innerHTML = data.content.content;
                replyContent.appendChild(pTag);
            } else if (data.image_content) {
                const imgTag = document.createElement('img');
                imgTag.src = data.image_content.content;
                imgTag.alt = "";
                imgTag.style.maxHeight = '50px';
                replyContent.appendChild(imgTag);
            } else if (data.video_content) {

                const messageContent = document.createElement('div');
                messageContent.className = 'ps-1 pe-5 mb-1 d-flex';

                var contentIcon = document.createElement('i');
                contentIcon.className = `ri-video-fill fs-3`;

                messageContent.appendChild(contentIcon);

                var contentSpan = document.createElement('span');
                contentSpan.className = 'd-flex justify-content-center align-items-center ps-2';
                const fullPath = data.video_content.content[0];
                const filename = fullPath.split('/').pop();
                contentSpan.textContent = filename;


                messageContent.appendChild(contentSpan);

                replyContent.appendChild(messageContent);

            } else if (data.document_content) {
                const messageContent = document.createElement('div');
                messageContent.className = 'ps-1 pe-5 mb-1 d-flex';

                var contentIcon = document.createElement('i');
                contentIcon.className = `ri-file-${data.document_content.doc_type}-fill fs-3`;

                messageContent.appendChild(contentIcon);

                var contentSpan = document.createElement('span');
                contentSpan.className = 'd-flex justify-content-center align-items-center ps-2';
                const fullPath = data.document_content.content;
                const filename = fullPath.split('/').pop();
                contentSpan.textContent = filename;

                messageContent.appendChild(contentSpan);

                replyContent.appendChild(messageContent);

            }

            replyCard.classList.remove("d-none");

            if (data.from_staff_id === data.current_user) {
                conversationName.innerHTML = "You";
            } else {
                conversationName.innerHTML = data.sender.name;
            }
        } else {

            if (data.content) {
                const pTag = document.createElement('p');
                pTag.className = 'mb-0';
                pTag.innerHTML = data.content.content;
                replyContent.appendChild(pTag);
            } else if (data.image_content) {
                const imgTag = document.createElement('img');
                imgTag.src = data.image_content.content;
                imgTag.alt = "";
                imgTag.style.maxHeight = '50px';
                replyContent.appendChild(imgTag);
            } else if (data.video_content) {

                const messageContent = document.createElement('div');
                messageContent.className = 'ps-1 pe-5 mb-1 d-flex';

                var contentIcon = document.createElement('i');
                contentIcon.className = `ri-video-fill fs-3`;

                messageContent.appendChild(contentIcon);

                var contentSpan = document.createElement('span');
                contentSpan.className = 'd-flex justify-content-center align-items-center ps-2';
                const fullPath = data.video_content.content;
                const filename = fullPath.split('/').pop();
                contentSpan.textContent = filename;

                messageContent.appendChild(contentSpan);

                replyContent.appendChild(messageContent);

            } else if (data.document_content) {
                const messageContent = document.createElement('div');
                messageContent.className = 'ps-1 pe-5 mb-1 d-flex';

                var contentIcon = document.createElement('i');
                contentIcon.className = `ri-file-${data.document_content.doc_type}-fill fs-3`;

                messageContent.appendChild(contentIcon);

                var contentSpan = document.createElement('span');
                contentSpan.className = 'd-flex justify-content-center align-items-center ps-2';
                const fullPath = data.document_content.content;
                const filename = fullPath.split('/').pop();
                contentSpan.textContent = filename;

                messageContent.appendChild(contentSpan);

                replyContent.appendChild(messageContent);

            }

            if (data.from_staff_id === data.current_user) {
                conversationName.innerHTML = "You";
            } else {
                conversationName.innerHTML = data.sender.name;
            }

        }

    }

    //scroll to original message from reply div
    function replyScrollEvent(listId) {
        var listItem = document.getElementById(listId);

        listItem.scrollIntoView({
            behavior: 'smooth',
            block: 'center',
            inline: 'center'
        });

        listItem.classList.toggle("highlight-background");

        setTimeout(function() {
            listItem.classList.toggle("highlight-background");
        }, 500);

        setTimeout(function() {
            listItem.style.transition = 'background-color 1s ease-in-out';
            listItem.style.backgroundColor = 'transparent';
        }, 500);

        setTimeout(function() {
            listItem.style.transition = '';
            listItem.style.backgroundColor = '';
        }, 1000);
    }

    function closeReplyContainer(container) {

        var closeToggle = container.querySelector("#close_toggle");

        if (closeToggle) {
            closeToggle.addEventListener("click", function() {
                container.classList.add("d-none");
                container.querySelector("#reply_id").value = "";
            });
        } else {
            console.error("Element with ID 'close_toggle' not found within the container");
        }
    }

    function copyEventListener(data) {

        var tempTextarea = document.createElement('textarea');

        tempTextarea.value = data;
        document.body.appendChild(tempTextarea);
        tempTextarea.select();
        document.execCommand('copy');

        document.body.removeChild(tempTextarea);

    }

    // {{-- fetch json file stored in session storage --}}
    function getStoredChatData(fileName) {
        const chatDataObject = sessionStorage.getItem(fileName);
        return chatDataObject ? JSON.parse(chatDataObject) : [];
    }

    function compareAndDisplayNewMessages() {
        try {
            const oldChatData = JSON.parse(sessionStorage.getItem("chatData")) || {};
            const newChatData = JSON.parse(sessionStorage.getItem("newChatData")) || {};

            if (typeof oldChatData !== 'object' || typeof newChatData !== 'object') {
                console.error("Invalid chat data. Expected an object.");
                return;
            }

            const changedStatusMessages = [];
            const parentContainer = document.getElementById('users-conversation');

            // Compare old and new messages
            Object.keys(newChatData).forEach(date => {
                const oldMessages = oldChatData[date] || [];
                const newMessages = newChatData[date];

                if (!oldChatData[date]) {
                    createDateBanner(date)
                }
                
                newMessages.forEach(newMessage => {
                    const oldMessageIndex = oldMessages.findIndex(oldMessage => oldMessage.id === newMessage.id);

                    if (oldMessageIndex !== -1) {
                        // Message exists, update status if changed
                        if (oldMessages[oldMessageIndex].view_status !== newMessage.view_status) {
                            changedStatusMessages.push({
                                id: newMessage.id,
                                view_status: newMessage.view_status
                            });
                        }
                        // Replace the old message with the new one
                        oldMessages[oldMessageIndex] = newMessage;
                    } else {
                        // New message, add to old messages
                        oldMessages.push(newMessage);
                        // Create message card for new message
                        if (newMessage.reply_id === null) {
                            messageCard_li(newMessage);
                        } else {
                            replyMessageCard(newMessage);
                        }
                        imageClickHandler();
                    }
                });

                // Update oldChatData with merged messages
                oldChatData[date] = oldMessages;
            });

            // Store updated chatData
            sessionStorage.setItem("chatData", JSON.stringify(oldChatData));

            // Remove temporary new chat data
            sessionStorage.removeItem("newChatData");

            // Update status of changed messages
            if (changedStatusMessages.length > 0) {
                changedStatusMessages.forEach(message => {
                    const element = document.getElementById('message-status-id-' + message.id);
                    if (element) {
                        element.classList.remove('bx-check');
                        element.classList.add('bx-check-double');
                    }
                });
            }
        } catch (error) {
            console.error("Error in compareAndDisplayNewMessages:", error);
        }
    }

    setInterval(function() {

        fetchActiveContacts("newActiveContact");
        fetchInactiveContacts("newInactiveContact");

        if (document.getElementById('senders_id')) {

            var staff_id = document.getElementById('senders_id').value;
            fetchConversationData(staff_id, "newChatData");
        }

    }, 5000);

    setInterval(function() {

        if (document.getElementById('senders_id')) {

            var staff_id = document.getElementById('senders_id').value;
            updateMsgSeenStatus(staff_id);
        }

    }, 1000);

    // {{-- Send message to contact --}}
    function sendchat() {
        const form = document.getElementById('chatinput-form');

        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission behavior

            const textInput = form.querySelector('#chat-input');
            const imageInput = document.getElementById('upload-media');
            const docInput = document.getElementById('upload-doc');

            let textMessage = textInput.value.trim();
            let imgMessages = {};
            let docMessage = "";
            
            if (imageInput.files.length > 0) {

                for (let i = 0; i < imageInput.files.length; i++) {
                    const file = imageInput.files[i];
                    const reader = new FileReader();

                    reader.onloadend = function() {
                        // Store base64 value of each image with file name as key in imgMessages object
                        imgMessages['media_' + (i + 1)] = reader.result;

                        // Check if all images have been processed
                        if (Object.keys(imgMessages).length === imageInput.files.length) {
                            // Send all image messages
                            sendMessage(imgMessages);
                        }
                    };

                    reader.readAsDataURL(file);
                }

            } else if (docInput.files.length > 0) {
                const file = docInput.files[0];
                const reader = new FileReader();

                reader.onloadend = function() {
                    docMessage = reader.result; // Base64 value of the document
                    sendMessage(docMessage);
                };

                reader.readAsDataURL(file);

            } else {
                sendMessage(textMessage);
            }

            textInput.disabled = false;

        });
    }

    function sendMessage(message_content) {

        const toStaffId = parseInt(document.getElementById("senders_id").value);
        const chatInput = document.getElementById('chat-input');
        const imageInput = document.getElementById('upload-media');
        const docInput = document.getElementById('upload-doc');
        const preview = document.getElementById("preview");

        const replyId = parseInt(document.getElementById("reply_id").value);

        // Clear the input fields
        chatInput.value = "";
        imageInput.value = "";
        docInput.value = "";
        preview.innerHTML = "";

        // Create an object with the message data
        const data = {
            message: message_content,
            to_staff_id: toStaffId
        };

        if (replyId) {
            data.reply_id = replyId;
        }

        // Send a POST request to the backend
        fetch('/send-chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data),
            })
            .then(response => response.json())
            .then(data => {

                if (data.errors) {

                    // Extract error messages
                    const errorMessages = Object.values(data.errors).flat();
                    // Join error messages into a single string
                    const errorMessageString = errorMessages.join('\n');
                    // Display error messages in an alert box
                    alert(errorMessageString);

                } else {

                    const staffId = document.getElementById('senders_id').value;

                    fetchActiveContacts("newActiveContact");
                    fetchInactiveContacts("newInactiveContact");
                    fetchConversationData(staffId, "newChatData");

                    document.getElementById("reply_id").value = "";
                    var replyCard = document.querySelector("#replyCard");

                    if (document.getElementById("reply_id").value === "") {
                        replyCard.className = 'd-none';
                    }

                }

            })
            .catch(error => {
                // Log any errors with the request
                console.error('Error:', error);
            });

    }

    function handleMediaFileChange(event) {

        const textInput = document.getElementById("chat-input");
        textInput.disabled = true;

        const files = event.target.files;
        const preview = document.getElementById("preview");

        preview.innerHTML = "";

        let containsImage = false;
        let containsVideo = false;

        let imageCount = 0;
        let videoCount = 0;

        // Allowed file extensions for images and videos
        const allowedImageExtensions = ['jpg', 'jpeg', 'png', 'svg', 'webp', 'bmp', 'gif'];
        const allowedVideoExtensions = ['mp4', 'mov', 'avi'];

        // Check if the selected files contain images, videos, or both
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const fileExtension = file.name.split('.').pop().toLowerCase();

            if (allowedImageExtensions.includes(fileExtension)) {
                imageCount++;
                containsImage = true;
            }

            if (allowedVideoExtensions.includes(fileExtension)) {
                videoCount++;
                containsVideo = true;
            }

            // If file extension is neither image nor video, show an alert and reset the file input
            if (!allowedImageExtensions.includes(fileExtension) && !allowedVideoExtensions.includes(fileExtension)) {
                alert("Only images (jpg, jpeg, png, svg, webp, bmp, gif) and videos (mp4, mov, avi) are allowed.");
                event.target.value = null;
                return;
            }

            // If both images and videos are selected, show an alert and reset the file input
            if (containsImage && containsVideo) {
                alert("Please select either images or videos, not both.");
                event.target.value = null;
                return;
            }

            if (imageCount > 5) {
                alert("You can select up to 5 images only.");
                event.target.value = null;
                return;
            }

            if (videoCount > 1) {
                alert("You can select only 1 video file.");
                event.target.value = null;
                return;
            }
        }

        // Iterate over each file to display previews
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const fileExtension = file.name.split('.').pop().toLowerCase();

            // Create a new FileReader object for each file
            const reader = new FileReader();

            // Handle the file onload event
            reader.onload = function(e) {
                // Create preview elements based on the file type
                if (containsImage) {
                    
                    const imgPreviewContainer = document.createElement("div");
                    imgPreviewContainer.className = "img-preview-container";

                    const img = document.createElement("img");
                    img.src = e.target.result;
                    img.className = "img-thumbnail img-preview";

                    const imageName = document.createElement("p");
                    imageName.textContent = file.name;

                    const removeBtn = document.createElement("button");
                    removeBtn.innerHTML = "&times;";
                    removeBtn.className = "remove-btn";
                    removeBtn.addEventListener("click", function() {
                        textInput.disabled = false;
                        imgPreviewContainer.remove();
                        event.target.value = null;
                    });

                    imgPreviewContainer.appendChild(img);
                    imgPreviewContainer.appendChild(imageName);
                    imgPreviewContainer.appendChild(removeBtn);

                    preview.appendChild(imgPreviewContainer);
                }

                if (containsVideo) {

                    const videoPreviewContainer = document.createElement("div");
                    videoPreviewContainer.className = "doc-preview-container p-3";

                    const videoPreviewContainerInner = document.createElement("div");
                    videoPreviewContainerInner.className = "d-flex";

                    const icon = document.createElement("i");
                    icon.className = 'ri-video-fill fs-2 pe-1';

                    const docName = document.createElement("p");
                    docName.className = "fs-6 d-flex mb-0 align-items-center";
                    docName.textContent = file.name;

                    const removeBtn = document.createElement("button");
                    removeBtn.innerHTML = "&times;";
                    removeBtn.className = "remove-btn";
                    removeBtn.addEventListener("click", function() {
                        textInput.disabled = false;
                        videoPreviewContainer.remove();
                        event.target.value = null;
                    });

                    videoPreviewContainerInner.appendChild(icon);
                    videoPreviewContainerInner.appendChild(docName);
                    videoPreviewContainer.appendChild(videoPreviewContainerInner);
                    videoPreviewContainer.appendChild(removeBtn);

                    preview.appendChild(videoPreviewContainer);

                }
            };

            // Read the file as a data URL
            reader.readAsDataURL(file);
        }
    }

    function handleDocumentFileChange(event) {

        const textInput = document.getElementById("chat-input");
        textInput.disabled = true;

        const files = event.target.files;
        const preview = document.getElementById("preview");

        preview.innerHTML = "";

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();

            reader.onload = function(e) {
                const docPreviewContainer = document.createElement("div");
                docPreviewContainer.className = "doc-preview-container p-3";

                const docPreviewContainerInner = document.createElement("div");
                docPreviewContainerInner.className = "d-flex";

                const fileExtension = file.name.split('.').pop();
                const extIcon = {
                    'pdf': 'pdf',
                    'xls': 'excel',
                    'xlsx': 'excel',
                    'doc': 'word',
                    'docx': 'word',
                    'ppt': 'ppt',
                    'pptx': 'ppt',
                    'ppsx': 'ppt',
                };
                const iconClass = extIcon[fileExtension] ? `ri-file-${extIcon[fileExtension]}-fill fs-2 pe-1` :
                    'default-icon-class';
                const icon = document.createElement("i");
                icon.className = iconClass;

                const docName = document.createElement("p");
                docName.className = "fs-6 d-flex mb-0 align-items-center";
                docName.textContent = file.name;

                const removeBtn = document.createElement("button");
                removeBtn.innerHTML = "&times;";
                removeBtn.className = "remove-btn";
                removeBtn.addEventListener("click", function() {
                    textInput.disabled = false;
                    docPreviewContainer.remove();
                    event.target.value = null;
                });

                docPreviewContainerInner.appendChild(icon);
                docPreviewContainerInner.appendChild(docName);
                docPreviewContainer.appendChild(docPreviewContainerInner);
                docPreviewContainer.appendChild(removeBtn);

                preview.appendChild(docPreviewContainer);
            };

            reader.readAsDataURL(file);
        }
    }

    function showOrHideEmojiLayout() {
        // Initialize FgEmojiPicker
        new FgEmojiPicker({
            // Trigger element for emoji picker
            trigger: [".emoji-btn"],
            // Don't remove emoji picker on selection
            removeOnSelection: false,
            // Show close button
            closeButton: true,
            // Position of the emoji picker
            position: ["top", "right"],
            // Pre-fetch emoji data
            preFetch: true,
            // Directory containing emoji data
            dir: "assets/js/pages/plugins/json",
            // Element to insert emoji picker into
            insertInto: document.querySelector(".chat-input")
        });

        // Add click event listener to emoji button
        document.getElementById("emoji-btn").addEventListener("click", function() {
            // Delay execution to ensure the emoji picker is fully rendered
            setTimeout(function() {
                // Get the emoji picker element
                var emojiPicker = document.getElementsByClassName("fg-emoji-picker")[0];
                // If emoji picker element exists
                if (emojiPicker) {
                    // Get the computed left style property
                    var leftValue = window.getComputedStyle(emojiPicker).getPropertyValue("left");
                    // If left style property exists
                    if (leftValue) {
                        // Remove "px" and subtract 40 from the left value
                        leftValue = leftValue.replace("px", "");
                        emojiPicker.style.left = (parseInt(leftValue) - 40) + "px";
                    }
                }
            }, 0);
        });
    }

    function scrollToBottom() {
        var chatConversation = document.getElementById("chat-conversation");
        chatConversation.scrollTop = chatConversation.scrollHeight;
    }

    //----------On click image show image preview start-------------//
    function imageClickHandler() {

        const galleryItems = document.querySelectorAll('.gallery-item');
        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.getElementById('lightbox-img');
        const closeButton = document.querySelector('.close-btn');
        const nextButton = document.querySelector('.next-btn');
        const prevButton = document.querySelector('.prev-btn');
        let currentIndex = 0;

        galleryItems.forEach((item, index) => {
            item.addEventListener('click', () => {
                currentIndex = index;
                openLightbox();
                showImage();
            });
        });

        function openLightbox() {
            lightbox.style.display = 'flex';
        }

        function closeLightbox() {
            lightbox.style.display = 'none';
        }

        function showImage() {
            lightboxImg.src = galleryItems[currentIndex].src;
        }

        function nextImage() {
            currentIndex = (currentIndex + 1) % galleryItems.length;
            showImage();
        }

        function prevImage() {
            currentIndex = (currentIndex - 1 + galleryItems.length) % galleryItems.length;
            showImage();
        }

        // Attach event listeners to the buttons
        closeButton.addEventListener('click', closeLightbox);
        nextButton.addEventListener('click', nextImage);
        prevButton.addEventListener('click', prevImage);
    }
    //----------On click image show image preview end-------------//
</script>
