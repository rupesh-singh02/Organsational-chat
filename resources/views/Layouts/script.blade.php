
<script src={{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}></script>
    <script src={{ asset('assets/libs/simplebar/simplebar.min.js')}}></script>
    <script src={{ asset('assets/libs/node-waves/waves.min.js')}}></script>
    <script src={{ asset('assets/libs/feather-icons/feather.min.js')}}></script>
    <script src={{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js')}}></script>
    <script src={{ asset('assets/js/plugins.js')}}></script>

    <!-- glightbox js -->
    <script src={{ asset('assets/libs/glightbox/js/glightbox.min.js')}}></script>

    <!-- fgEmojiPicker js -->
    <script src={{ asset('assets/libs/fg-emoji-picker/fgEmojiPicker.js')}}></script>

    <!-- chat init js -->

    <!-- App js -->
    <script src={{ asset('assets/js/app.js')}}></script>
    <script src={{ asset('assets/js/chat-script.js')}}></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>
        function showLoader() {
            document.getElementById("loader").style.display = "block";
        }

        function hideLoader() {
            document.getElementById("loader").style.display = "none";
        }

        function showSuccessMessage() {
            document.getElementById("successMessage").style.display = "block";
            setTimeout(function() {
                document.getElementById("successMessage").style.display = "none";
            }, 2000);
        }

        function showNotFoundMessage() {
            document.getElementById("notFoundMessage").style.display = "block";
            setTimeout(function() {
                document.getElementById("notFoundMessage").style.display = "none";
            }, 2000);
        }


        document.getElementById("searchInput").addEventListener("input", function() {
            var input = this.value.toLowerCase();

            var list = document.getElementById("userList");
            var items = Array.from(list.getElementsByTagName("li"));

            var found = false;


            list.innerHTML = "";
            items.forEach(function(item) {
                var text = item.textContent.toLowerCase();
                if (text.includes(input)) {
                    list.insertBefore(item, list.firstChild);
                    found = true;
                } else {
                    list.appendChild(item);
                }
            });

            if (!found) {
                showNotFoundMessage();
            }
        });
    </script>

    <script>
        function searchMessages() {
            var t, s = document.getElementById("searchMessage").value.toUpperCase();
            var conversationList = Array.from(document.getElementById("users-conversation").getElementsByTagName("li"));
            conversationList.forEach(function(e) {
                t = e.getElementsByTagName("p")[0] ? e.getElementsByTagName("p")[0] : "";
                var messageText = (t.textContent || t.innerText || "").toUpperCase();
                if (messageText.indexOf(s) > -1) {
                    e.style.display = "";
                } else {
                    e.style.display = "none";
                }
            });
        }

    </script>
