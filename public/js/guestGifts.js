'use strict';

function openGuestGiftModal(openButton, fullGift = false) {
    const modal = document.getElementById('guestGiftModal');
    const remainingValue = openButton.getAttribute('data-remaining-value');
    const valueInput = document.getElementById('value');
    const valueInputLabel = valueInput.labels[0];
    if( fullGift ) {
        // If offering the full gift, set the value input to the remaining value and disable it
        valueInput.value = remainingValue;
        valueInput.setAttribute('readonly', true);
    } else {
        // If offering a partial gift, clear the value input and set the max attribute
        valueInput.value = '';
        valueInput.removeAttribute('readonly');        
    }
    if( remainingValue < 10000 ) {
        valueInputLabel.innerHTML = `Valor: (${remainingValue}€ restantes)`;
    }
    valueInput.setAttribute('max', remainingValue);

    // Fill the hidden input with the gift ID from the button's data attribute
    const giftId = openButton.getAttribute('data-gift-id');
    const giftIdInput = document.getElementById('giftId');
    giftIdInput.value = giftId;

    // Fill the item name in the modal header
    const giftName = openButton.getAttribute('data-gift-name');
    const itemNameElement = document.getElementById('itemName');
    itemNameElement.textContent = giftName;

    modal.style.display = 'block';

    const pageElements = document.querySelectorAll('body > *:not(#guestGiftModal, #confirmationModal)');
    pageElements.forEach(element => {
        element.style.filter = 'blur(5px)';
        element.style.pointerEvents = 'none';
        element.style.userSelect = 'none';
    });
}

function closeGuestGiftModal() {
    const modal = document.getElementById('guestGiftModal');
    modal.style.display = 'none';

    // Clear the form inputs
    document.getElementById('guestGiftModal').querySelector('form').reset();

    const pageElements = document.querySelectorAll('body > *:not(#guestGiftModal, #confirmationModal)');
    pageElements.forEach(element => {
        element.style.filter = 'none';
        element.style.pointerEvents = 'auto';
        element.style.userSelect = 'auto';
    });
}



function openConfirmationModal() {
    const modal = document.getElementById('confirmationModal');
    const guestName = document.getElementById('guestName').value.trim();
    const giftName = document.getElementById('itemName').textContent;
    const contributionValue = document.getElementById('value').value;
    const message = document.getElementById('message').value.trim();

    document.getElementById('contributionInfoGuestName').querySelector('span').textContent = guestName || '—';
    document.getElementById('contributionInfoGiftName').querySelector('span').textContent = giftName || '—';
    document.getElementById('contributionInfoContribution').querySelector('span').textContent = contributionValue ? `${contributionValue} €` : '—';
    document.getElementById('contributionInfoMessage').querySelector('span').textContent = message || '—';

    modal.style.display = 'block';

    const guestGiftModal = document.getElementById('guestGiftModal');
    guestGiftModal.style.filter = 'blur(5px)';
    guestGiftModal.style.pointerEvents = 'none';
    guestGiftModal.style.userSelect = 'none';
}

function closeConfirmationModal() {
    const modal = document.getElementById('confirmationModal');
    modal.style.display = 'none';

    const guestGiftModal = document.getElementById('guestGiftModal');
    guestGiftModal.style.filter = 'none';
    guestGiftModal.style.pointerEvents = 'auto';
    guestGiftModal.style.userSelect = 'auto';
}

function submitGuestGiftFormFromConfirmation() {
    const guestGiftForm = document.getElementById('guestGiftForm');
    guestGiftForm.dataset.confirmed = 'true';
    guestGiftForm.submit();
}



window.addEventListener('load', () => {
    const guestGiftForm = document.getElementById('guestGiftForm');
    if( guestGiftForm ) {
        guestGiftForm.addEventListener('submit', (event) => {
            if( !guestGiftForm.dataset.confirmed ) {
                event.preventDefault();
                openConfirmationModal();
            } else {
                delete guestGiftForm.dataset.confirmed;
            }
        });
    }
});