// search card script 
function showLoader() {
    const loader = document.getElementById('loader');
    loader.style.display = 'block';
}

// Function to hide loader
function hideLoader() {
    const loader = document.getElementById('loader');
    loader.style.display = 'none';
}

// Function to display notification
function displayNotification(message, type) {
    const notificationDiv = document.createElement('div');
    notificationDiv.classList.add('alert', `alert-${type}`, 'alert-dismissible', 'fade', 'show');
    notificationDiv.innerHTML = `
        <strong>${message}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    document.getElementById('notificationContainer').appendChild(notificationDiv);
    // Remove the notification after 3 seconds
    setTimeout(() => {
        notificationDiv.classList.remove('show');
        setTimeout(() => {
            notificationDiv.remove();
        }, 1000);
    }, 3000);
}

document.addEventListener('DOMContentLoaded', function () {
    // Get the input element and add an event listener for input
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();

            
            showLoader();
            const cardsContainer = document.querySelector('.tab-pane.active');
            if (cardsContainer) {
                const cards = cardsContainer.querySelectorAll('.card');
                let foundItems = [];
                let otherItems = [];

                cards.forEach(card => {
                    
                    const cardText = card.textContent.toLowerCase();
                    if (cardText.includes(searchTerm)) {
                        foundItems.push(card);
                    } else {
                        otherItems.push(card); 
                    }
                });

                setTimeout(() => {
                    hideLoader();

                   
                    if (foundItems.length > 0) {
                        displayNotification('Items found!', 'success');
                    } else {
                        displayNotification('No items found!', 'warning');
                    }

                    cardsContainer.innerHTML = '';
                    foundItems.forEach(item => {
                        cardsContainer.appendChild(item);
                    });
                    otherItems.forEach(item => {
                        cardsContainer.appendChild(item);
                    });
                }, 2000);
            }
        });
    }
});

   

// upload img script 





// doc upload script 

function handleFileChange(event) {
    const file = event.target.files[0];
    const fileName = file.name.toLowerCase();
    

    if (fileName.endsWith('.pdf')) {
        previewPDF(file);
    } else if (fileName.endsWith('.docx')) {
        previewDOCX(file);
    } else {
        alert('Unsupported file format.');
    }
}

function previewPDF(file) {
    const filePreview = document.getElementById('file-preview');
    filePreview.innerHTML = '';

    const pdfViewer = document.createElement('iframe');
    pdfViewer.src = URL.createObjectURL(file);
    pdfViewer.width = '100%';
    pdfViewer.height = '500px';
    filePreview.appendChild(pdfViewer);

    // Add remove button
    const removeButton = document.createElement('button');
    removeButton.innerText = 'Remove';
    removeButton.style.position = 'absolute';
    removeButton.style.top = '5px';
    removeButton.style.right = '5px';
    removeButton.style.fontSize = '12px';
    removeButton.onclick = function() {
        filePreview.innerHTML = '';
        document.getElementById('upload-docs').value = '';
    };
    filePreview.appendChild(removeButton);
}

function previewDOCX(file) {
    const filePreview = document.getElementById('file-preview');
    filePreview.innerHTML = ''; 

    mammoth.convertToHtml({arrayBuffer: file})
        .then(function(result) {
            const docxHtml = result.value;
            const docxPreview = document.createElement('div');
            docxPreview.innerHTML = docxHtml;
            filePreview.appendChild(docxPreview);
            const removeButton = document.createElement('button');
            removeButton.innerText = 'Remove';
            removeButton.style.position = 'absolute';
            removeButton.style.top = '5px';
            removeButton.style.right = '5px';
            removeButton.style.fontSize = '12px';
            removeButton.onclick = function() {
                filePreview.innerHTML = '';
                document.getElementById('upload-docs').value = '';
            };
            filePreview.appendChild(removeButton);
        })
        .catch(function(err) {
            console.log('Error:', err);
        });
}