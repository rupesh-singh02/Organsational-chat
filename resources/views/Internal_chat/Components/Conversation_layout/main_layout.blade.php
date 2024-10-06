<div id="chat-layout">

    <div id="chat-content" class="chat-content d-flex-lg">

        <!-- start chat conversation section -->
        <div class="w-100 overflow-hidden position-relative">
            <!-- conversation user -->
            <div class="position-relative">

                <div class="position-relative" id="users-chat">

                    <div class="p-3 user-chat-topbar">

                        <div class="row align-items-center">

                            <div class="col-sm-4 col-8">

                                <div class="d-flex align-items-center">

                                    <div class="flex-shrink-0 d-block d-lg-none me-3">
                                        <a href="javascript: void(0);" class="user-chat-remove fs-18 p-1">
                                            <i class="ri-arrow-left-s-line align-bottom"></i>
                                        </a>
                                    </div>

                                    <div class="flex-grow-1 overflow-hidden">

                                        <div class="d-flex align-items-center">

                                            <input type="hidden" id="senders_id" value="">
                                            <input type="hidden" id="senders_full_id" value="">

                                            <div id="chat-user-img-div"
                                                class="flex-shrink-0 chat-user-img user-own-img align-self-center me-3 ms-0">
                                                <img id="chat-user-img" class="rounded-circle avatar-xs" src=""
                                                    alt="">
                                                <span class="user-status"></span>
                                            </div>

                                            <div class="flex-grow-1 overflow-hidden">
                                                <h5 class="text-truncate mb-0 fs-16">
                                                    <a id="receiver-name" class="text-reset username"
                                                        data-bs-toggle="offcanvas" href="#userProfileCanvasExample"
                                                        aria-controls="userProfileCanvasExample">

                                                    </a>
                                                </h5>
                                                <p class="text-truncate text-muted fs-14 mb-0">
                                                    <small id="user-status" class="userStatus">

                                                    </small>
                                                </p>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="col-sm-8 col-4">

                                <ul class="list-inline user-chat-nav text-end mb-0">

                                    <li class="list-inline-item m-0">

                                        <div class="dropdown">

                                            <button class="btn btn-ghost-secondary btn-icon" type="button"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-more-vertical icon-sm">
                                                    <circle cx="12" cy="12" r="1"></circle>
                                                    <circle cx="12" cy="5" r="1"></circle>
                                                    <circle cx="12" cy="19" r="1"></circle>
                                                </svg>
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-end">

                                                <a class="dropdown-item d-block d-lg-none user-profile-show"
                                                    href="#"><i
                                                        class="ri-user-2-fill align-bottom text-muted me-2"></i>
                                                    View Profile</a>
                                                <a class="dropdown-item" href="#"><i
                                                        class="ri-add-line align-bottom align-bottom text-muted me-2"></i>
                                                    Add to Group</a>
                                                <a class="dropdown-item" href="#"><i
                                                        class="ri-mic-off-line align-bottom text-muted me-2"></i>
                                                    Hide</a>
                                                <a class="dropdown-item" href="#"><i
                                                        class="ri-delete-bin-5-line align-bottom text-muted me-2"></i>
                                                    Delete</a>

                                            </div>

                                        </div>

                                    </li>

                                </ul>

                            </div>

                        </div>

                    </div>
                    <!-- end chat user head -->

                    <div class="chat-conversation p-3 p-lg-4 " id="chat-conversation">
                        <div id="elmLoader" class="d-none">
                            <div class="spinner-border text-primary avatar-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>

                        <ul class="list-unstyled chat-conversation-list" id="users-conversation">
                            

                        </ul>
                        <!-- end chat-conversation-list -->
                    </div>

                </div>

                <div class="container-fluid d-none px-0" id="replyCard">
                    <div class="card mb-0">
                        <div class="card-body overflow-hidden">
                            <div class="replymessage-block mb-0 d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <input type="hidden" name="" id="reply_id" value="">
                                    <h5 class="conversation-name"></h5>
                                    <div class="mb-0 reply-content">
                                        {{-- reply content will be added here --}}
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <button type="button" id="close_toggle"
                                        class="btn btn-sm btn-link mt-n2 me-n3 fs-18">
                                        <i class="bx bx-x align-middle"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- end chat-conversation -->
                <div class="chat-input-section p-3 d-flex align-items-center">

                    <form id="chatinput-form" enctype="multipart/form-data" class="mt-0 w-100">
                        @csrf
                        <div class="preview" id="preview"></div>
                        <div id="file-preview" style="position: relative; width: 200px;"></div>

                        <div class="row g-0 d-flex align-items-center justify-content-center mt-0">

                            <div class="col-auto">
                                <div class="chat-input-links me-1" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-original-title="emojis">
                                    <div class="links-list-item">
                                        <button type="button" class="btn btn-link text-decoration-none emoji-btn"
                                            id="emoji-btn" onclick="showOrHideEmojiLayout()">
                                            <i class="bx bx-smile align-middle" style="font-size: 23px;"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-auto">
                                <div class="chat-input-links  me-1 pt-3" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-original-title="Images">
                                    <input type="file" name="upload-doc[]" id="upload-media"
                                        accept=".jpg, .jpeg, .png, .svg, .gif, .webp, .bmp, .mp4, .mov, .avi"
                                        class="form-control upload-doc" style="display: none;" multiple
                                        onchange="handleMediaFileChange(event)">
                                    <label for="upload-media" class="btn btn-link text-decoration-none">
                                        <i class="las la-photo-video" style="font-size: 22px;"></i>
                                    </label>
                                </div>
                            </div>

                            <div class="col-auto" data-bs-toggle="tooltip" data-bs-placement="top"
                                data-bs-original-title="Doc">
                                <div class="links-list-item me-1 pt-3">
                                    <label for="upload-doc" class="btn btn-link text-decoration-none">
                                        <i class="lab la-squarespace" style="font-size: 22px;"></i>
                                    </label>
                                    <input type="file" name="upload-docs" id="upload-doc"
                                        accept=".doc, .docx, .pdf, .xlsx, .xls" class="form-control upload-docs"
                                        style="display: none;" onchange="handleDocumentFileChange(event)">
                                </div>
                            </div>

                            <div class="col">
                                <div class="chat-input-feedback">
                                    Please Enter a Message
                                </div>
                                <div class="input-container">
                                    <input type="text" class="form-control chat-input bg-light border-light"
                                        id="chat-input" placeholder="Type your message..." autocomplete="off">

                                    <input type="hidden" class="form-control" id="chat-input-value">
                                </div>
                            </div>

                            <div class="col-auto">
                                <div class="chat-input-links ms-2">
                                    <div class="links-list-item">
                                        <button type="submit"
                                            class="btn btn-success chat-send waves-effect waves-light">
                                            <i class="ri-send-plane-2-fill align-bottom"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </form>

                </div>

                <div class="lightbox" id="lightbox">

                    <span class="close-btn" onclick="closeLightbox()">
                        &times;
                    </span>
                    
                    <span class="replay-btn" onclick="replayImage()">
                        <i class="las la-reply fs-1" aria-hidden="true">
                        </i>
                    </span>

                    <span class="download-btn" onclick="downloadImage()">
                        <i class="las la-download fs-1" aria-hidden="true">
                        </i>
                    </span>

                    <span class="prev-btn" onclick="prevImage()">
                        &#10094;
                    </span>

                    <img src="" alt="" class="lightbox-img" id="lightbox-img">

                    <span class="next-btn" onclick="nextImage()">
                        &#10095;
                    </span>

                </div>

            </div>
        </div>

    </div>
</div>
