'use strict';

window.addEventListener('load', () => {
    document.querySelectorAll('.deleteSVG').forEach(el => el.classList.add('iconTransition'));
});

function openDeleteConfirmationModal(button) {
    const modal = document.getElementById('deleteConfirmationModal');
    const confirmationId = button.getAttribute('data-confirmation-id');
    const guestName = button.getAttribute('data-guest-name');

    const confirmationIdElement = document.querySelector('#confirmationInfoId > span');
    const guestNameElement = document.querySelector('#confirmationInfoGuestName > span');

    confirmationIdElement.innerHTML = confirmationId;
    guestNameElement.innerHTML = guestName;

    const guestConfirmationIdInput = document.getElementById('guestConfirmationId');
    guestConfirmationIdInput.value = confirmationId;

    modal.style.display = 'block';

    const pageElements = document.querySelectorAll('body > *:not(#deleteConfirmationModal)');
    pageElements.forEach(element => {
        element.style.filter = 'blur(5px)';
        element.style.pointerEvents = 'none';
        element.style.userSelect = 'none';
    });
}

function closeDeleteConfirmationModal() {
    const modal = document.getElementById('deleteConfirmationModal');
    modal.style.display = 'none';

    // Clear the form inputs
    document.getElementById('deleteConfirmationModal').querySelector('form').reset();

    const pageElements = document.querySelectorAll('body > *:not(#deleteConfirmationModal)');
    pageElements.forEach(element => {
        element.style.filter = 'none';
        element.style.pointerEvents = 'auto';
        element.style.userSelect = 'auto';
    });
}


function openDeleteContributionModal(button) {
    const modal = document.getElementById('deleteContributionModal');
    const contributionId = button.getAttribute('data-contribution-id');
    const guestName = button.getAttribute('data-guest-name');
    const giftName = button.getAttribute('data-gift-name');
    const contribution = button.getAttribute('data-contribution');
    const message = button.getAttribute('data-message');

    const contributionIdElement = document.querySelector('#contributionInfoId > span');
    const guestNameElement = document.querySelector('#contributionInfoGuestName > span');
    const giftNameElement = document.querySelector('#contributionInfoGiftName > span');
    const contributionElement = document.querySelector('#contributionInfoContribution > span');
    const messageElement = document.querySelector('#contributionInfoMessage > span');

    contributionIdElement.innerHTML = contributionId;
    guestNameElement.innerHTML = guestName;
    giftNameElement.innerHTML = giftName;
    contributionElement.innerHTML = contribution;
    messageElement.innerHTML = message;

    const guestContributionIdInput = document.getElementById('guestContributionId');
    guestContributionIdInput.value = contributionId;

    modal.style.display = 'block';

    const pageElements = document.querySelectorAll('body > *:not(#deleteContributionModal)');
    pageElements.forEach(element => {
        element.style.filter = 'blur(5px)';
        element.style.pointerEvents = 'none';
        element.style.userSelect = 'none';
    });
}

function closeDeleteContributionModal() {
    const modal = document.getElementById('deleteContributionModal');
    modal.style.display = 'none';

    // Clear the form inputs
    document.getElementById('deleteContributionModal').querySelector('form').reset();

    const pageElements = document.querySelectorAll('body > *:not(#deleteContributionModal)');
    pageElements.forEach(element => {
        element.style.filter = 'none';
        element.style.pointerEvents = 'auto';
        element.style.userSelect = 'auto';
    });
}