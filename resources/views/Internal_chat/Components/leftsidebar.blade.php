<div class="tab-pane active" id="chats" role="tabpanel">

    <div class="chat-room-list pt-3" data-simplebar>

        <div class="d-flex align-items-center px-4 mb-2">

            <div class="flex-grow-1">
                <h6 class="mb-0">Official Chat
                    <span id="active_user_count" class="badge text-bg-secondary">
                        
                    </span>
                </h6>
            </div>

            <div class="flex-shrink-0">

                <div data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="bottom" title="add contacts">

                    <!-- Button trigger modal -->
                    {{-- <button type="button" class="btn btn-soft-primary btn-sm shadow-none">
                        <i class="ri-add-line align-bottom"></i>
                    </button> --}}

                </div>

            </div>

        </div>

        <div class="align-items-center px-4 mb-2 mt-4">

            <div class="flex-grow-1">

                <!-- Nav tabs -->
                <ul class="nav nav-pills arrow-navtabs nav-success bg-light  nav-justified mb-3" role="tablist">

                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" href="#profile1" role="tab" aria-selected="true" >
                            Active
                        </a>
                    </li>

                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#home1" role="tab" aria-selected="false" >
                            Inactive
                        </a>
                    </li>
                
                </ul>

                <!-- Tab panes -->
                <div class="tab-content text-muted">

                    <!-- active tab  -->
                    <div class="tab-pane active show" id="profile1" role="tabpanel">

                        <div class="chat-message-list">

                            <div id="activeLoader" class="d-none">
                                <div class="spinner-border text-primary avatar-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>

                            <ul class="list-unstyled chat-list chat-user-list" id="active_userList">

                                

                            </ul>

                        </div>

                    </div>

                    <!-- inatcive tab  -->
                    <div class="tab-pane" id="home1" role="tabpanel">

                        <div class="sort-contact1">

                            <div id="inactiveLoader" class="d-none">
                                <div class="spinner-border text-primary avatar-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>

                            <ul class="list-unstyled chat-list chat-user-list" id="inactive_userList">

                                

                            </ul>

                        </div>

                    </div>

                </div>

            </div>
        </div>

        <!-- End chat-message-list -->
    </div>

</div>