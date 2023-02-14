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
    async comments()
    {
        const response = await fetch(this.urlValue);
        const element = document.getElementById('comment');
        element.innerHTML = await response.text();
        const div = element.querySelector('div');
        const modal = new Modal(div);
        await modal.show();
    }
}