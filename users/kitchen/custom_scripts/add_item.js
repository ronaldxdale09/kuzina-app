// Handle file input clicks to trigger the file selector
document.querySelectorAll('.photo-label').forEach(label => {
    label.addEventListener('click', function() {
        const input = this.previousElementSibling;
        input.click();
    });
});

// Handle chip selection (for both checkboxes and radio buttons)
document.querySelectorAll('.chip').forEach(chip => {
    chip.addEventListener('click', function() {
        const input = this.querySelector('input');
        if (input.type === 'checkbox') {
            input.checked = !input.checked;
            this.classList.toggle('active');
        } else if (input.type === 'radio') {
            const name = input.name;
            document.querySelectorAll(`input[name="${name}"]`).forEach(radio => {
                radio.closest('.chip').classList.remove('active');
            });
            input.checked = true;
            this.classList.add('active');
        }
    });
});

// Toggle radio options for pickup or delivery
function toggleOption(option) {
    document.querySelectorAll('.pickup-delivery .option').forEach(opt => {
        opt.classList.remove('active');
        opt.querySelector('input[type="radio"]').checked = false;
    });
    option.classList.add('active');
    option.querySelector('input[type="radio"]').checked = true;
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('#addItemForm');
    const saveButton = document.querySelector('.save-btn');
    const popup = document.getElementById('custom-popup');
    const isEditMode = form.action.includes('process_edit_menu.php');

    // Initialize chips with checked values
    document.querySelectorAll('.chip').forEach(chip => {
        const input = chip.querySelector('input');
        if (input.checked) {
            chip.classList.add('active');
        }
    });

    // Create spinner and prepend to button
    const spinner = document.createElement('span');
    spinner.className = 'spinner-border spinner-border-sm';
    spinner.style.marginRight = '8px';
    spinner.style.display = 'none';
    saveButton.prepend(spinner);

    // Handle photo upload and preview
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        const placeholderIndex = input.id.replace('photo', '');
        const placeholder = document.querySelector(`#photo-placeholder-${placeholderIndex}`);
        const previewDiv = document.querySelector(`#preview${placeholderIndex}`);
        const label = placeholder.querySelector('label');

        // Keep existing image preview if in edit mode
        if (isEditMode && label.querySelector('img')) {
            label.style.display = 'block';
        }

        placeholder.addEventListener('click', () => input.click());
        input.addEventListener('change', (event) => handleImagePreview(event, previewDiv, label, placeholder, input));
    });

    function handleImagePreview(event, previewDiv, label, placeholder, input) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                label.innerHTML = `<img src="${e.target.result}" alt="preview" style="width: 100%; height: 100%; border-radius: 10px; object-fit: cover;" />`;
                label.style.display = 'block';

                // Add remove button
                const removeBtn = document.createElement('button');
                removeBtn.className = 'remove-image';
                removeBtn.innerHTML = '×';
                removeBtn.style.cssText = 'position: absolute; top: 5px; right: 5px; background: rgba(255, 0, 0, 0.7); color: white; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer;';
                placeholder.appendChild(removeBtn);

                removeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    resetPlaceholder(previewDiv, input, label, placeholder);
                });
            };
            reader.readAsDataURL(file);
        }
    }

    function resetPlaceholder(previewDiv, input, label, placeholder) {
        input.value = '';
        label.innerHTML = '<i class="bx bx-camera"></i>';
        const removeBtn = placeholder.querySelector('.remove-image');
        if (removeBtn) removeBtn.remove();
    }

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Show spinner and disable button
        spinner.style.display = 'inline-block';
        saveButton.disabled = true;
        saveButton.innerHTML = isEditMode ? 'Updating...' : 'Saving...';

        const formData = new FormData(form);
        const submitUrl = isEditMode ? 'functions/edit_food_item.php' : 'functions/add_food_item.php';

        fetch(submitUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (!isEditMode) form.reset();
                    document.querySelectorAll('.image-preview').forEach(preview => preview.innerHTML = '');
                    showPopup();
                } else {
                    throw new Error(data.message || 'Failed to ' + (isEditMode ? 'update' : 'add') + ' item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: ' + error.message);
            })
            .finally(() => {
                spinner.style.display = 'none';
                saveButton.disabled = false;
                saveButton.innerHTML = isEditMode ? 'UPDATE ITEM' : 'SAVE CHANGES';
            });
    });

    function showPopup() {
        popup.style.display = 'flex';
    }

    // Initialize all active/selected states for edit mode
    if (isEditMode) {
        // Initialize checkbox selections
        document.querySelectorAll('input[type="checkbox"]:checked').forEach(checkbox => {
            checkbox.closest('.chip').classList.add('active');
        });

        // Initialize radio button selections
        document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
            radio.closest('.chip').classList.add('active');
        });

        // Initialize pickup/delivery selection
        const selectedDeliveryOption = document.querySelector('input[name="pickupDelivery"]:checked');
        if (selectedDeliveryOption) {
            selectedDeliveryOption.closest('.option').classList.add('active');
        }
    }
});