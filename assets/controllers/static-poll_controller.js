import { Controller } from "stimulus";
import {useDispatch} from "stimulus-use";
import {Modal} from "bootstrap";

export default class extends Controller {

    static targets = ['modal', 'modalBody'];

    static values = {
        url: String,
    };
    connect() {
        this.dispatch(this);
    }
    async statics()
    {
        const response = await fetch(this.urlValue);
        const element = document.getElementById('static');
        element.innerHTML = await response.text();
        const div = element.querySelector('div');
        console.log(div);
        const modal = new Modal(div);
        await modal.show();
    }
}