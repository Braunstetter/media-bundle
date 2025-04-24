import {Controller} from '@hotwired/stimulus'

export default class extends Controller {

    containerElement = null

    static targets = ['field', 'addButton']

    static values = {
        prototype: String,
        maxItems: Number,
        itemsCount: Number,
        focusOnAdd: Boolean,
    }

    connect() {
        this.index = this.itemsCountValue = this.fieldTargets.length
        this.containerElement = this.element.querySelector('[data-prototype]')
    }

    addItem(event) {
        event.preventDefault()
        this.doAddItem();

        if (this.focusOnAddValue) {
            this.focusInput();
        }
    }

    focusInput() {
        const newField = this.containerElement.lastElementChild;
        const fileInput = newField.querySelector('input[type="file"]');
        if (fileInput) {
            fileInput.click();
        }
    }

    doAddItem() {
        const prototype = this.containerElement.dataset.prototype
        const newField = prototype.replace(/__name__/g, this.index)
        this.containerElement.insertAdjacentHTML('beforeend', newField)
        this.index++
        this.itemsCountValue++
    }

    removeItem(event) {
        event.preventDefault()
        this.fieldTargets.forEach(element => {
            if (element.contains(event.target)) {
                element.remove()
                this.itemsCountValue--
            }
        })
    }

    removeItemAndReaddEmpty(event) {
        this.removeItem(event)
        this.doAddItem()
    }

    itemsCountValueChanged() {
        if (false === this.hasAddButtonTarget || 0 === this.maxItemsValue) {
            return
        }

        const maxItemsReached = this.itemsCountValue >= this.maxItemsValue
        this.addButtonTarget.classList.toggle('hidden', maxItemsReached)
    }

    /**
     * Convert a template string into HTML DOM nodes
     * @param  {String} str The template string
     * @return {Node}       The template HTML
     */
    stringToHTML = function (str) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(str, 'text/html');
        return doc.body;
    };

}
