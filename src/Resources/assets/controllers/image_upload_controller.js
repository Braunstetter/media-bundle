import {Controller} from "@hotwired/stimulus"

export default class extends Controller {
    static targets = []

    connect() {
        this.element.querySelector('input[type=file]')
            .addEventListener('change', input => this.displayUploadedImage(input))
    }

    displayUploadedImage(input) {
        if (input.target.files && input.target.files[0]) {
            const reader = new FileReader();

            reader.onload = (event) => {
                const imageContainer = this.element.querySelector('.image-preview');
                let image = imageContainer.querySelector('img');

                // If no img element exists, create one dynamically
                if (!image) {
                    image = document.createElement('img');
                    image.alt = 'uploaded image';
                    imageContainer.appendChild(image);
                }

                image.setAttribute('src', event.target.result.toString());

                // Ensure the image is visible (in case it was hidden)
                image.style.display = '';

                // Dispatch custom event to notify other controllers about the new image
                this.element.dispatchEvent(new CustomEvent('image:uploaded', {
                    bubbles: true,
                    detail: { image: image }
                }));
            };

            reader.readAsDataURL(input.target.files[0]);
        }
    };

    openFileSelection() {
        const fileInput = this.element.querySelector('input[type=file]');
        if (fileInput === null) {
            return;
        }

        fileInput.click();
    }

}