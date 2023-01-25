import {Controller} from 'stimulus';
import {Modal} from 'bootstrap';
import $ from 'jquery';
import {useDispatch} from 'stimulus-use';

export default class extends Controller {
    static targets = ['modal', 'modalBody'];
    static values = {
        formUrl: String,
    }

    connect() {
        useDispatch(this);
    }

    openModal() {
        this.modal = new Modal(this.modalTarget);
        this.modal.show();
    }

    async submitForm(event) {
        event.preventDefault();
        const $form = $(this.modalBodyTarget).find('form');
        try {
            await $.ajax({
                url: $form.prop('action'),
                method: $form.prop('method'),
                data: $form.serialize(),
            }).then((response) => {
                // click on the backdrop to close the modal
                document.querySelector('.btn-close').click();
                const divToBeDeleted = document.getElementsByClassName('modal-backdrop')[0];
                if (divToBeDeleted) {
                    divToBeDeleted.remove();
                }
                this.dispatch('success');
                // clear the form after submit
                $form.trigger('reset');
            });
        } catch (error) {
            // show the error message for each field in the form
            console.log(error);
        }
    }

    hideModal() {
        this.modal.hide();
    }

    modalHidden() {
        console.log('modal hidden');
    }
}