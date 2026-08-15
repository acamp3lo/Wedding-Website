'use strict';

function addNewGuestField() {
    const button = document.getElementById('addGuestButton');

    if( button !== null ) {
        button.addEventListener('click', function() {
            const guestInfo = document.querySelector('.guestInfo');
            const newGuestInfo = guestInfo.cloneNode(true);
            const guestCount = document.querySelectorAll('.guestInfo').length + 1;

            // Update IDs and names for the new guest fields
            const newGuestName = newGuestInfo.querySelector('.guestName');
            newGuestName.id = `guestName_${guestCount}`;
            newGuestName.value = '';
            const newGuestNameLabel = newGuestInfo.querySelector('.guestNameLabel');
            newGuestNameLabel.htmlFor = `guestName_${guestCount}`;

            const newFoodRestrictions = newGuestInfo.querySelector('.foodRestrictions');
            newFoodRestrictions.id = `foodRestrictions_${guestCount}`;
            newFoodRestrictions.value = '';
            const newFoodRestrictionsLabel = newGuestInfo.querySelector('.foodRestrictionsLabel');
            newFoodRestrictionsLabel.htmlFor = `foodRestrictions_${guestCount}`;
            const newRemoveButton = newGuestInfo.querySelector('.removeGuestButton');
            newRemoveButton.id = `removeGuestButton_${guestCount}`;

            // Show the remove button and add event listener to remove the guest info section
            newRemoveButton.style.display = 'flex';
            newRemoveButton.addEventListener('click', function() {
                newGuestInfo.remove();
            });

            // Append the new guest info section
            guestInfo.parentNode.insertBefore(newGuestInfo, button);
        });
    }
}

addNewGuestField();