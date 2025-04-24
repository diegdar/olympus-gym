function hideMessage(){        
    const successMessage = document.getElementById('message');

    if (successMessage) {
        setTimeout(function() {
            successMessage.classList.add('fade-out');

            setTimeout(function() {
                successMessage.style.display = 'none';
            }, 2000); 
        }, 5000); 
    }

}

document.addEventListener('DOMContentLoaded', hideMessage);