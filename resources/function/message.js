$(document).ready(function () {

    const urlParams = new URLSearchParams(window.location.search);
    const username = urlParams.get('username') || '';

    if (username) {
        $('#inlineFormInputGroup').val(decodeURIComponent(username));
    }

    // Function to load chat list
    function loadChatList(searchQuery = '') {
        $.ajax({
            url: '/message/create',
            method: 'GET',
            success: function (response) {
                // Clear existing chat list items
                $('#Open .chat-list').empty();
                $('#Closed .chat-list').empty();

                // Check if response contains chatlists
                if (response.chatlists && response.chatlists.length > 0) {
                    // Loop through the chat list and append each item
                    $.each(response.chatlists, function (index, chat) {
                        // Check if chat matches the search query
                        if (chat.username.toLowerCase().includes(searchQuery.toLowerCase()) ||
                            (chat.latest_message_text && chat.latest_message_text.toLowerCase().includes(searchQuery.toLowerCase()))) {

                            var chatItem = `
                                <a href="#" class="d-flex align-items-center" data-chat='${JSON.stringify(chat)}'>
                                    <div class="flex-shrink-0 position-relative">
                                        <img class="img-fluid" src="${chat.profile || 'assets/back/images/avatar/noprofile.webp'}" alt="user img" width="35">
                                        ${chat.isLogin ? `<span class="online-indicator"></span>` : ''}
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h3>${chat.username}</h3>
                                        <p>${chat.latest_message_text || 'No messages yet'}</p>
                                    </div>
                                </a>
                            `;

                            if (chat.isLogin == 1) {
                                $('#Open .chat-list').append(chatItem);
                            } else {
                                $('#Closed .chat-list').append(chatItem);
                            }
                        }
                    });

                    // Re-bind the click events after loading new items
                    bindChatListEvents();

                    if (username) {
                        findAndSelectChat(username);
                    } else {

                        if (!searchQuery) {
                            var firstChatItem = $('.chat-list a').first();
                            if (firstChatItem.length > 0) {
                                firstChatItem.trigger('click'); // Trigger click event on the first chat item
                            }
                        }

                    }


                } else {
                    console.log('No chatlists found in the response.');
                }
            },
            error: function (xhr) {
                console.log('Error:', xhr);
            }
        });
    }

    $('#inlineFormInputGroup').on('input', function () {
        var searchQuery = $(this).val();
        loadChatList(searchQuery);
    });

    loadChatList();

    function findAndSelectChat(username) {
        const searchQuery = username.toLowerCase();
        let chatFound = false;

        // Look for the chat in the Open tab first
        $('#Open .chat-list a').each(function () {
            const chatData = $(this).data('chat');
            if (chatData.username.toLowerCase() === searchQuery) {
                $('#Open-tab').click(); // Open the "Open" tab
                $(this).trigger('click'); // Trigger the click on the chat item
                chatFound = true;
                return false; // Break the loop
            }
        });

        // If not found in Open, check the Closed tab
        if (!chatFound) {
            $('#Closed .chat-list a').each(function () {
                const chatData = $(this).data('chat');
                if (chatData.username.toLowerCase() === searchQuery) {
                    $('#Closed-tab').click(); // Open the "Closed" tab
                    $(this).trigger('click'); // Trigger the click on the chat item
                    return false; // Break the loop
                }
            });
        }
    }

    function bindChatListEvents() {
        $(".chat-list a").off('click').on('click', function () {
            var chat = $(this).data('chat');

            if (chat) {
                updateChatbox(chat);
                $(".chatbox").addClass('showbox');
            }

            return false;
        });

        $(".chat-icon").off('click').on('click', function () {
            $(".chatbox").removeClass('showbox');
        });
    }

    function updateChatbox(chat) {
        $('.chatbox .msg-head .flex-shrink-0 img').attr('src', chat.profile || 'assets/back/images/avatar/noprofile.webp');
        $('#msg-head-name').text(chat.username);
        $('#msg-head-role').text(chat.role || 'Role not defined');

        loadMessages(chat.id);

        $('.chatbox .send-box form').off('submit').on('submit', function (e) {
            e.preventDefault();

            var text = $('#msg-text').val();
            var recipientId = chat.id;

            $.ajax({
                url: '/message',
                method: 'POST',
                data: {
                    recipient_id: recipientId,
                    text: text,
                },
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                },
                success: function (response) {
                    if (response.success) {
                        var newMessage = response.message;
                        var displayTime = formatMessageTime(newMessage.created_at);

                        var messageItem = `
                            <li class="sender">
                                <p>${newMessage.text}</p>
                                <span class="time">${displayTime}</span>
                            </li>
                        `;

                        $('.chatbox .msg-body ul').append(messageItem);
                        $('#msg-text').val('');

                        loadMessages(chat.id);

                        setTimeout(scrollToEnd, 100)

                    } else {
                        console.log('Failed to send message.');
                    }
                },
                error: function (xhr) {
                    console.log('Error:', xhr);
                }
            });

        });
    }

    function loadMessages(chatId) {
        $.ajax({
            url: `/message/${chatId}`, // show route
            method: 'GET',
            success: function (response) {
                if (response.messages) {
                    $('.chatbox .msg-body ul').empty();

                    if (response.messages && response.messages.length > 0) {

                        $.each(response.messages, function (index, message) {
                            var messageClass = message.sender_id === chatId ? 'reply' : 'sender';
                            var displayTime = formatMessageTime(message.created_at);

                            var deleteIcon = message.sender_id == currentUserId
                                ? `<i class="bi bi-trash fs-5 delete-message" data-message-id="${message.id}" data-chat-id="${chatId}"  style="position:relative;top:-2px;"></i>`
                                : '';

                            var messageItem = `
                                                <li class="${messageClass}">
                                                    <p>${message.text || 'No message text'}</p>
                                                    <span class="time">${displayTime} | ${deleteIcon}</span>
                                                       
                                                           
                                                  
                                                </li>
                                            `;
                            $('.chatbox .msg-body ul').append(messageItem);
                        });

                        setTimeout(scrollToEnd, 100);

                    } else {

                        var noMessageItem = `
                        <li class="no-message">
                            <p>No message yet</p>
                        </li>
                    `;
                        $('.chatbox .msg-body ul').append(noMessageItem);

                    }



                } else {
                    console.log('No messages found in the response.');
                }
            },
            error: function (xhr) {
                console.log('Error:', xhr);
            }
        });

        $(document).on('click', '.delete-message', function () {
            let id = $(this).data('message-id');
            let chatId = $(this).data('chat-id');

            $.ajax({
                type: 'DELETE',
                url: `/message/${id}`,
                dataType: 'json',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                }
            }).done(function (data) {
                loadMessages(chatId)
            }).fail(function (data) {
                console.log(data)
            });
        });


    }

    function formatMessageTime(createdAt) {
        var messageTime = new Date(createdAt);
        var currentTime = new Date();
        var timeDifference = currentTime - messageTime;

        if (timeDifference < 60000) { // less than a minute
            return "Just now";
        } else {
            var hours = messageTime.getHours();
            var minutes = messageTime.getMinutes();
            var ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0' + minutes : minutes;
            return hours + ':' + minutes + ' ' + ampm;
        }
    }

    function scrollToEnd() {
        const msgBody = document.getElementById('msg-body-list');
        msgBody.scrollTop = msgBody.scrollHeight;
    }

});
