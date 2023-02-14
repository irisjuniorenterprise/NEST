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
        this.dispatch(this);
    }

    openModal() {
        this.modal = new Modal(this.modalTarget);
        this.modal.show();
    }

    async submitForm(event) {
        event.preventDefault();
        let submitSpan = document.getElementById('submitButtonSpan');
        let loadingButton = document.getElementById('loadingButton');
        submitSpan.style.display = 'none';
        loadingButton.style.display = 'block';
        const $form = $(this.modalBodyTarget).find('form');
        var fileData = $(this.modalBodyTarget).find('input[type="file"]').prop('files');
        var formData = new FormData();
        console.log(fileData);
        if (fileData){
            for (var i = 0; i < fileData.length; i++) {
                formData.append('file' + i, fileData[i]);
            }
        }
        // add the form data to the formData object
        $form.serializeArray().forEach((input) => {
            formData.append(input.name, input.value);
        }
        );
        try {
            await $.ajax({
                url: $form.prop('action'),
                method: $form.prop('method'),
                data: formData,
                contentType: false,
                processData: false,
            }).then((response) => {
                // click on the backdrop to close the modal
                submitSpan.style.display = 'block';
                loadingButton.style.display = 'none';
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
    uploadFile() {
        var fileName = $(this.modalBodyTarget).find('input[type="file"]').val();
        $(this.modalBodyTarget).find('.custom-file-label').html(fileName);
        var fileData = $(this.modalBodyTarget).find('input[type="file"]').prop('files')[0];
        var formData = new FormData();
        formData.append('file', fileData);
        $.ajax({
            url: '/upload/announcement',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);
            }
        });
    }
}