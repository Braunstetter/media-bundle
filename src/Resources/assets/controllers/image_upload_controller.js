import {Controller} from "@hotwired/stimulus"

export default class extends Controller {
    static targets = []

    connect() {
        this.element.querySelector('input[type=file]').addEventListener('change', (input) => {
            this.displayUploadedImage(input);
        })
    }

    displayUploadedImage(input) {
        if (input.target.files && input.target.files[0]) {
            const reader = new FileReader();

            reader.onload = (event) => {

                const image = this.element.querySelector('.image-preview > img');

                if (image instanceof Image) {
                    image.setAttribute('src', event.target.result.toString());
                }

            };

            reader.readAsDataURL(input.target.files[0]);
        }
    };
}