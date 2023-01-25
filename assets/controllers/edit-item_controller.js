import {Controller} from "stimulus";
import {Modal} from "bootstrap";
import $ from "jquery";
import {useDispatch} from "stimulus-use";

export default class extends Controller {
    static targets = ['modal', 'modalBody'];

    static values = {
        url: String,
    };

    connect() {
        useDispatch(this);
    }

    async openEditModal() {
        const response = await fetch(this.urlValue);
        const element = document.getElementById('test');
        element.innerHTML = await response.text();
        const div = element.querySelector('div');
        const modal = new Modal(div);
        await modal.show();
        await this.addEventListeners();
    }

    addEventListeners() {
        const addItemButton = document.getElementById("addItemButtonEdit");
        console.log(addItemButton);
        const formGroupTemplate = document.getElementById("formGroupTemplateEdit");
        console.log(formGroupTemplate);
        const removeItemButton = document.getElementById("removeItemButtonEdit");
        console.log(removeItemButton);
        const deleteItemButtons = document.querySelectorAll('.delete-item-button');
        deleteItemButtons.forEach(button => {
            button.addEventListener('click', event => {
                // check if there is more than one item
                if (formGroupTemplate.parentNode.childElementCount > 1) {
                    // remove the item
                    event.target.parentNode.remove();
                }
                // Remove the parent element of the button (which should be the container element for the item)
                //event.target.parentElement.remove();
            });
        });

        addItemButton.addEventListener("click", function () {
            console.log("add item button clicked");
            // Clone the form group template and append it to the form group and clear the inputs in the process
            const formGroup = formGroupTemplate.cloneNode(true);
            formGroup.removeAttribute("id");
            formGroup.classList.remove("d-none");
            formGroup.style.display = "block";
            const inputs = formGroup.querySelectorAll("input");
            inputs.forEach(function (input) {
                input.value = "";
            }
            );
            formGroupTemplate.parentNode.appendChild(formGroup);
        });

        removeItemButton.addEventListener("click", function () {
            console.log("remove item button clicked");
            // remove per item form group
            // check if there is more than one form group
            if (formGroupTemplate.parentNode.childElementCount > 1) {
                // remove the last form group
                formGroupTemplate.parentNode.removeChild(formGroupTemplate.parentNode.lastElementChild);
            }
        });
    }
}