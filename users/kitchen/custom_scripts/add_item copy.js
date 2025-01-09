 // Handle file input clicks to trigger the file selector
 document.querySelectorAll('.photo-label').forEach(label => {
     label.addEventListener('click', function() {
         const input = this.previousElementSibling;
         input.click();
     });
 });

 // Toggle selection of chips for diet type, health goal, and allergens
 document.querySelectorAll('.chip').forEach(chip => {
     chip.addEventListener('click', function() {
         const checkbox = this.querySelector('input[type="checkbox"]');
         checkbox.checked = !checkbox.checked;
         this.classList.toggle('active', checkbox.checked);
     });
 });

 // Toggle radio options for pickup or delivery
 function toggleOption(option) {
     // Remove 'active' class from all options
     document.querySelectorAll('.pickup-delivery .option').forEach(opt => {
         opt.classList.remove('active');
         opt.querySelector('input[type="radio"]').checked = false;
     });

     // Add 'active' class to the selected option
     option.classList.add('active');
     option.querySelector('input[type="radio"]').checked = true;
 }

 document.addEventListener('DOMContentLoaded', function() {
     const form = document.querySelector('#addItemForm');
     const saveButton = document.querySelector('.save-btn');
     const popup = document.getElementById('custom-popup'); // Reference the popup from the DOM

     // Create spinner and prepend to button (initially hidden)
     const spinner = document.createElement('span');
     spinner.className = 'spinner-border spinner-border-sm';
     spinner.style.marginRight = '8px';
     spinner.style.display = 'none'; // Hidden initially
     saveButton.prepend(spinner);

     // Handle photo upload and preview
     const fileInputs = document.querySelectorAll('input[type="file"]');
     fileInputs.forEach(input => {
         const placeholderIndex = input.id.replace('photo', '');
         const placeholder = document.querySelector(`#photo-placeholder-${placeholderIndex}`);
         const previewDiv = document.querySelector(`#preview${placeholderIndex}`);
         const label = placeholder.querySelector('label');

         // Trigger file input when placeholder is clicked
         placeholder.addEventListener('click', () => input.click());

         // Handle file input change and image preview
         input.addEventListener('change', (event) => handleImagePreview(event, previewDiv, label, placeholder, input));
     });

     function handleImagePreview(event, previewDiv, label, placeholder, input) {
         const file = event.target.files[0];

         if (file) {
             const reader = new FileReader();
             reader.onload = function(e) {
                 label.style.display = 'none'; // Hide the camera icon
                 previewDiv.innerHTML = `
                    <img src="${e.target.result}" alt="uploaded photo" style="width: 100%; height: 100%; border-radius: 10px; object-fit: cover;" />
                    <button type="button" class="remove-image" style="position: absolute; top: 5px; right: 5px; background: rgba(255, 0, 0, 0.7); color: white; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer;">×</button>
                `;

                 // Remove button click event
                 previewDiv.querySelector('.remove-image').addEventListener('click', () => resetPlaceholder(previewDiv, input, label));
             };
             reader.readAsDataURL(file);
         }
     }

     // Reset the placeholder after removing an image
     function resetPlaceholder(previewDiv, input, label) {
         previewDiv.innerHTML = ''; // Clear the image preview
         input.value = ''; // Reset file input
         label.style.display = 'flex'; // Show the camera icon again
     }

     // Form submission
     form.addEventListener('submit', function(e) {
         e.preventDefault();

         // Show spinner and disable button
         spinner.style.display = 'inline-block';
         saveButton.disabled = true;
         saveButton.innerHTML = 'Saving...';

         // Create FormData and submit via fetch
         const formData = new FormData(form);
         submitForm(formData);
     });

     function submitForm(formData) {
         fetch('functions/add_food_item.php', {
                 method: 'POST',
                 body: formData
             })
             .then(response => response.json())
             .then(data => {
                 if (data.success) {
                     form.reset(); // Reset the form
                     document.querySelectorAll('.image-preview').forEach(preview => preview.innerHTML = ''); // Reset image previews
                     showPopup(); // Show success popup
                 } else {
                     throw new Error(data.message || 'Failed to add item');
                 }
             })
             .catch(error => {
                 console.error('Error:', error);
                 alert('Error: ' + error.message);
             })
             .finally(() => {
                 // Hide spinner and reset button
                 spinner.style.display = 'none';
                 saveButton.disabled = false;
                 saveButton.innerHTML = 'SAVE CHANGES';
             });
     }

     // Show popup after successful form submission
     function showPopup() {
         popup.style.display = 'flex'; // Show popup after form submission is successful
     }
 });